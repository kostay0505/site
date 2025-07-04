<?php
/**
 * dashboard-functions.php
 * Набор вспомогательных функций для личного кабинета
 * @package my-custom-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Подключаем WP-API для работы с медиа (если ещё не загружено)
if ( ! function_exists( 'media_handle_upload' ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
}

/**
 * Обработка сохранения и редактирования объявления
 */
add_action( 'template_redirect', 'mytheme_handle_save_ad' );
function mytheme_handle_save_ad() {
    // 1) Проверка nonce и авторизации
    if ( ! isset( $_POST['mytheme_ad_nonce'] ) ||
         ! wp_verify_nonce( $_POST['mytheme_ad_nonce'], 'mytheme_save_ad' ) ||
         ! is_user_logged_in()
    ) {
        return;
    }

    // 2) Получаем ID (0 = новое объявление)
    $post_id = ! empty( $_POST['ad_id'] ) ? absint( $_POST['ad_id'] ) : 0;

    // 3) Основные данные поста
    $args = [
        'post_title'   => sanitize_text_field( $_POST['ad_title']   ?? '' ),
        'post_content' => wp_kses_post(        $_POST['ad_desc']    ?? '' ),
        'post_status'  => 'publish',
        'post_type'    => 'product',
    ];
    if ( $post_id ) {
        $args['ID'] = $post_id;
        wp_update_post( $args );
    } else {
        $post_id = wp_insert_post( $args );
    }

    // 4) Обновляем метаполя
    update_post_meta( $post_id, '_price',     floatval(    $_POST['ad_price']    ?? 0 ) );
    update_post_meta( $post_id, '_currency',  sanitize_text_field( $_POST['ad_currency'] ?? '' ) );
    update_post_meta( $post_id, '_condition', sanitize_key(        $_POST['ad_cond']     ?? 'new' ) );
    update_post_meta( $post_id, '_quantity',  intval(      $_POST['ad_quantity'] ?? 0 ) );
    update_post_meta( $post_id, '_unit',      sanitize_text_field( $_POST['ad_unit']     ?? '' ) );
    update_post_meta( $post_id, '_country',   sanitize_text_field( $_POST['ad_country']  ?? '' ) );
    update_post_meta( $post_id, '_city',      sanitize_text_field( $_POST['ad_city']     ?? '' ) );

    // 5) Категория
    if ( ! empty( $_POST['ad_cat'] ) ) {
        wp_set_post_terms( $post_id, [ absint( $_POST['ad_cat'] ) ], 'product_cat' );
    }

    // 6) Собираем существующую галерею
    $existing = array_filter( array_map( 'absint',
        explode( ',', get_post_meta( $post_id, '_product_image_gallery', true ) )
    ) );
    $thumb_id = get_post_thumbnail_id( $post_id );
    if ( $thumb_id && ! in_array( $thumb_id, $existing, true ) ) {
        array_unshift( $existing, $thumb_id );
    }

    // 7) Обрабатываем помеченные на удаление
    if ( ! empty( $_POST['remove_images'] ) && is_array( $_POST['remove_images'] ) ) {
        $to_remove = array_map( 'absint', $_POST['remove_images'] );
        $existing  = array_diff( $existing, $to_remove );
    }

    // 8) Загрузка новых фото
    $uploaded = [];
    if ( ! empty( $_FILES['ad_images']['name'][0] ) ) {
        foreach ( $_FILES['ad_images']['name'] as $i => $name ) {
            if ( 0 === $_FILES['ad_images']['error'][ $i ] ) {
                $_FILES['upload_file'] = [
                    'name'     => $_FILES['ad_images']['name'][ $i ],
                    'type'     => $_FILES['ad_images']['type'][ $i ],
                    'tmp_name' => $_FILES['ad_images']['tmp_name'][ $i ],
                    'error'    => $_FILES['ad_images']['error'][ $i ],
                    'size'     => $_FILES['ad_images']['size'][ $i ],
                ];
                $aid = media_handle_upload( 'upload_file', $post_id );
                if ( ! is_wp_error( $aid ) ) {
                    $existing[] = $aid;
                }
                unset( $_FILES['upload_file'] );
            }
        }
    }

    // 9) Сохраняем галерею
    update_post_meta( $post_id, '_product_image_gallery', implode( ',', $existing ) );

    // 10) Обновляем featured image
    if ( ! empty( $existing ) ) {
        set_post_thumbnail( $post_id, absint( $existing[0] ) );
    } else {
        delete_post_thumbnail( $post_id );
    }

    // 11) Редирект обратно в ЛК
    $redirect = add_query_arg(
        [
            'section'  => 'ads',
            'mode'     => 'edit',
            'ad_id'    => $post_id,
            'ad_saved' => '1',
        ],
        get_query_var( 'account_page_url' )
    );
    wp_safe_redirect( $redirect );
    exit;
}

/**
 * Обработка удаления объявления из ЛК
 */
add_action( 'template_redirect', 'mytheme_handle_delete_ad' );
function mytheme_handle_delete_ad() {
    if (
        empty( $_GET['section'] ) || 'ads' !== sanitize_key( $_GET['section'] ) ||
        empty( $_GET['mode'] )    || 'delete' !== sanitize_key( $_GET['mode'] )
    ) {
        return;
    }
    $ad_id = absint( $_GET['ad_id'] ?? 0 );
    if ( ! wp_verify_nonce( $_GET['_wpnonce'] ?? '', 'mytheme_delete_ad_' . $ad_id ) ) {
        wp_die( __( 'Ошибка безопасности (nonce).', 'my-custom-theme' ) );
    }
    if ( ! is_user_logged_in() || get_current_user_id() !== (int) get_post_field( 'post_author', $ad_id ) ) {
        wp_die( __( 'У вас нет прав для удаления этого объявления.', 'my-custom-theme' ) );
    }
    wp_trash_post( $ad_id );
    wp_safe_redirect( remove_query_arg( [ 'mode', 'ad_id', '_wpnonce' ], get_permalink( get_queried_object_id() ) ) );
    exit;
}

/**
 * Возвращает статистику пользователя.
 */
function te_get_user_stats( $user_id ) {
    $listings = (int) wp_count_posts( 'listing' )->publish;
    $favs     = (int) get_user_meta( $user_id, 'te_favourites_count', true );
    $orders   = function_exists( 'wc_get_customer_order_count' )
        ? wc_get_customer_order_count( $user_id )
        : 0;

    return [
        'listings'   => $listings,
        'favourites' => $favs,
        'orders'     => $orders,
    ];
}

/**
 * Рендерит сетку объявлений пользователя.
 */
function te_render_user_listings( $user_id ) {
    $query = new WP_Query( [
        'post_type'      => 'listing',
        'author'         => $user_id,
        'posts_per_page' => 8,
    ] );

    if ( $query->have_posts() ) {
        echo '<ul class="listing-grid">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<li>';
            if ( has_post_thumbnail() ) {
                the_post_thumbnail( 'thumbnail' );
            }
            echo '<p>' . esc_html( get_the_title() ) . '</p>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>' . esc_html__( 'У вас пока нет объявлений.', 'my-custom-theme' ) . '</p>';
    }
}
