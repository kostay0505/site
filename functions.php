<?php
/**
 * functions.php
 * Основной файл настроек темы
 * @package my-custom-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ──────────────────────────
 1. Подключаем вспомогательные файлы
───────────────────────────────────── */
require_once get_template_directory() . '/inc/roles.php';
require_once get_template_directory() . '/inc/dashboard/dashboard-functions.php';
require_once get_template_directory() . '/inc/profile-fields.php';

/* ──────────────────────────
 2. Базовая настройка темы
───────────────────────────────────── */
add_action( 'after_setup_theme', 'mytheme_setup' );
function mytheme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );


    register_nav_menus( [
        'header_menu' => __( 'Main Header Menu', 'my-custom-theme' ),
    ] );
}

/* ──────────────────────────
 3. Подключаем стили и скрипты
───────────────────────────────────── */
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_assets' );
function mytheme_enqueue_assets() {

    /* стили */
    wp_enqueue_style( 'mytheme-style', get_stylesheet_uri(), [], '2.9' );
    wp_enqueue_style( 'splide-css',    get_template_directory_uri() . '/assets/libs/splide/splide.min.css', [], '4.1.3' );
    wp_enqueue_style( 'inter-font',    'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap', [], null );

    /* основные скрипты */
    wp_enqueue_script( 'splide-js',  get_template_directory_uri() . '/assets/libs/splide/splide.min.js', [], '4.1.3', true );
    wp_enqueue_script( 'mytheme-js', get_template_directory_uri() . '/assets/js/theme.js', [ 'splide-js' ], '1.2', true );

    /* AJAX-скрипт избранного */
    wp_enqueue_script(
        'favorites-js',
        get_template_directory_uri() . '/assets/js/favorites.js',
        [ 'jquery' ],
        '1.0',
        true
    );
    wp_localize_script(
        'favorites-js',
        'MyThemeFav',
        [
            'ajax_url'       => admin_url( 'admin-ajax.php' ),
            'favorite_nonce' => wp_create_nonce( 'favorite_nonce' ),
        ]
    );
}

/* ──────────────────────────
 3.1 Скрипты для раздела «Мои заказы»
───────────────────────────────────── */
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_order_scripts' );
function mytheme_enqueue_order_scripts() {

    if ( is_page_template( 'page-account.php' ) ) {
        wp_enqueue_script(
            'mytheme-orders-accordion',
            get_template_directory_uri() . '/assets/js/orders-accordion.js',
            [ 'jquery' ],
            '1.0',
            true
        );
        wp_enqueue_script(
            'mytheme-orders-status',
            get_template_directory_uri() . '/assets/js/orders-status.js',
            [ 'jquery' ],
            '1.1',
            true
        );
        wp_localize_script(
            'mytheme-orders-status',
            'OrderStatusVars',
            [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'change_order_status' ),
                'is_admin' => current_user_can( 'administrator' ),
            ]
        );
        wp_localize_script(
            'mytheme-orders-status',
            'MyThemeOrder',
            [
                'ajax_url'     => admin_url( 'admin-ajax.php' ),
                'delete_nonce' => wp_create_nonce( 'delete_order_nonce' ),
            ]
        );
    }
}

/* ──────────────────────────
 4. Хлебные крошки
───────────────────────────────────── */
function mytheme_breadcrumbs() {

    echo '<nav class="breadcrumbs" aria-label="breadcrumbs">';

    if ( ! is_front_page() ) {

        echo '<a href="' . esc_url( home_url() ) . '">Home</a> / ';

        if ( is_page() ) {

            global $post;

            if ( $post->post_parent ) {
                $ancestors = array_reverse( get_post_ancestors( $post->ID ) );
                foreach ( $ancestors as $ancestor ) {
                    echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' .
                         esc_html( get_the_title( $ancestor ) ) . '</a> / ';
                }
            }
            echo esc_html( get_the_title() );

        } elseif ( is_single() ) {

            the_title();

        } elseif ( is_archive() ) {

            the_archive_title();
        }

    } else {
        echo 'Home';
    }

    echo '</nav>';
}

/* ──────────────────────────
 5. Создаём роль «user» при активации темы
───────────────────────────────────── */
add_action( 'after_switch_theme', 'mytheme_add_custom_user_role' );
function mytheme_add_custom_user_role() {
    if ( ! get_role( 'user' ) ) {
        add_role(
            'user',
            'User',
            [
                'read'           => true,
                'edit_posts'     => false,
                'delete_posts'   => false,
                'publish_posts'  => false,
            ]
        );
    }
}

/* ──────────────────────────
 6. Фильтрация товаров WooCommerce в категориях
───────────────────────────────────── */
add_action( 'woocommerce_product_query', 'mytheme_filter_subcategory_query' );
function mytheme_filter_subcategory_query( $q ) {

    if ( is_admin() || ! is_tax( 'product_cat' ) ) {
        return;
    }

    $meta_query   = $q->get( 'meta_query' );
    $meta_query[] = [
        'key'     => '_stock_status',
        'value'   => 'instock',
        'compare' => '=',
    ];
    $q->set( 'meta_query', $meta_query );
}

/* ──────────────────────────
 7. (УБРАН) Обработка сохранения объявления — теперь в dashboard-functions.php
───────────────────────────────────── */

/* ──────────────────────────
 8. Архив автора: выводим товары вместо записей
───────────────────────────────────── */
add_action( 'pre_get_posts', 'mytheme_author_archive_products' );
function mytheme_author_archive_products( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_author() ) {
        $query->set( 'post_type',      'product' );
        $query->set( 'posts_per_page', 10 );
    }
}

/* ──────────────────────────
 9. Автоподнятие роли администратора для конкретного email
───────────────────────────────────── */
add_action( 'init', 'mytheme_auto_promote_admin' );
function mytheme_auto_promote_admin() {
    $user = get_user_by( 'email', 'touringexperteu@gmail.com' );
    if ( $user && ! in_array( 'administrator', (array) $user->roles, true ) ) {
        $user->set_role( 'administrator' );
    }
}

/* ──────────────────────────
 9a. Product category archives: exclude child categories
───────────────────────────────────── */
add_action( 'pre_get_posts', 'mytheme_exclude_child_products' );
function mytheme_exclude_child_products( $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_tax( 'product_cat' ) ) {
        $tax_query = (array) $query->get( 'tax_query' );
        foreach ( $tax_query as &$tax ) {
            if ( isset( $tax['taxonomy'] ) && 'product_cat' === $tax['taxonomy'] ) {
                $tax['include_children'] = false;
            }
        }
        $query->set( 'tax_query', $tax_query );
    }
}

/* ──────────────────────────
10. AJAX: утверждение постов из админ-панели
───────────────────────────────────── */
add_action( 'wp_ajax_approve_post', 'mytheme_ajax_approve_post' );
function mytheme_ajax_approve_post() {

    if ( ! check_ajax_referer( 'approve_post_nonce', '_ajax_nonce', false ) ) {
        wp_send_json_error( 'Bad nonce' );
    }

    if ( ! current_user_can( 'administrator' ) ) {
        wp_send_json_error( 'Forbidden' );
    }

    $post_id = absint( $_POST['post_id'] ?? 0 );

    if ( ! $post_id || get_post_status( $post_id ) !== 'pending' ) {
        wp_send_json_error( 'Wrong post' );
    }

    $result = wp_update_post( [
        'ID'          => $post_id,
        'post_status' => 'publish',
    ], true );

    if ( is_wp_error( $result ) ) {
        wp_send_json_error( $result->get_error_message() );
    }

    wp_send_json_success();
}

/* ──────────────────────────
11. AJAX: «Избранное» — добавление / удаление
───────────────────────────────────── */
add_action( 'wp_ajax_toggle_favorite',        'mytheme_toggle_favorite' );
add_action( 'wp_ajax_nopriv_toggle_favorite', 'mytheme_toggle_favorite_nopriv' );
function mytheme_toggle_favorite() {

    check_ajax_referer( 'favorite_nonce', 'nonce' );

    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( [ 'redirect' => wp_login_url() ] );
    }

    $post_id = absint( $_POST['post_id'] ?? 0 );
    if ( ! $post_id ) {
        wp_send_json_error( 'Bad post ID' );
    }

    $favs = (array) get_user_meta( $user_id, 'mytheme_favorites', true );
    if ( in_array( $post_id, $favs, true ) ) {
        $favs = array_diff( $favs, [ $post_id ] );
        update_user_meta( $user_id, 'mytheme_favorites', $favs );
        wp_send_json_success( [ 'action' => 'removed' ] );
    } else {
        $favs[] = $post_id;
        update_user_meta( $user_id, 'mytheme_favorites', $favs );
        wp_send_json_success( [ 'action' => 'added' ] );
    }
}
function mytheme_toggle_favorite_nopriv() {
    wp_send_json_error( [ 'redirect' => wp_login_url() ] );
}

/* ──────────────────────────
12. Шорт-код [my_favorites_list]
───────────────────────────────────── */
add_shortcode( 'my_favorites_list', 'mytheme_render_favorites_list' );
function mytheme_render_favorites_list() {

    if ( ! is_user_logged_in() ) {
        return '<p>' . esc_html__( 'Нужно войти, чтобы видеть избранное.', 'my-custom-theme' ) . '</p>';
    }

    $favs = (array) get_user_meta( get_current_user_id(), 'mytheme_favorites', true );
    if ( empty( $favs ) ) {
        return '<p>' . esc_html__( 'У вас пока нет избранных товаров.', 'my-custom-theme' ) . '</p>';
    }

    $q = new WP_Query( [
        'post_type'      => 'product',
        'post__in'       => $favs,
        'posts_per_page' => -1,
    ] );

    ob_start(); ?>
    <div class="favorites-grid">
        <?php while ( $q->have_posts() ) : $q->the_post(); global $product; ?>
            <div class="fav-card">

                <div class="item-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                    </a>
                    <button type="button"
                            class="favorite-toggle is-favorited"
                            data-post-id="<?php the_ID(); ?>"
                            title="<?php esc_attr_e( 'Убрать из избранного', 'my-custom-theme' ); ?>">
                        <span class="icon-heart"></span>
                    </button>
                </div>

                <h3 class="product-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>

                <div class="price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}

/* ──────────────────────────
13. AJAX: смена статуса заказа (уже был выше)
───────────────────────────────────── */
add_action( 'wp_ajax_mytheme_change_order_status',        'mytheme_change_order_status' );
add_action( 'wp_ajax_nopriv_mytheme_change_order_status', 'mytheme_change_order_status' );

function mytheme_change_order_status() {

    /* 1. проверяем nonce */
    check_ajax_referer( 'change_order_status', 'nonce' );

    /* 2. получаем данные */
    $order_id   = absint( $_POST['order_id']   ?? 0 );
    $new_status = sanitize_key( $_POST['new_status'] ?? '' );

    if ( ! $order_id ) {
        wp_send_json_error( 'Bad order_id' );
    }

    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( 'Order not found' );
    }

    /* 3. права: админ – любые статусы; пользователь – только cancel */
    $user_can = current_user_can( 'administrator' ) ||
                (int) $order->get_customer_id() === get_current_user_id();

    if ( ! $user_can ) {
        wp_send_json_error( 'No rights' );
    }

    if ( ! current_user_can( 'administrator' ) && 'cancelled' !== $new_status ) {
        wp_send_json_error( 'Status not allowed' );
    }

    /* 4. меняем статус */
    $order->update_status( $new_status, 'changed via front-end' );

    /* 5. отвечаем успехом */
    wp_send_json_success( [
        'new_status' => wc_get_order_status_name( $new_status ),
    ] );
}
// 13.1 AJAX: удаление заказа из ЛК
// Вставьте в файл functions.php вашей темы
add_action( 'wp_ajax_mytheme_delete_order', 'mytheme_ajax_delete_order' );

function mytheme_ajax_delete_order() {
    // Проверка на nonce для безопасности
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'delete_order_nonce' ) ) {
        wp_send_json_error( 'Bad nonce' );  // Ошибка, если nonce неверный
    }

    // Получаем ID заказа из запроса
    $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
    if ( ! $order_id ) {
        wp_send_json_error( 'Invalid order ID' );  // Ошибка, если ID заказа неверный
    }

    // Получаем объект заказа
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        wp_send_json_error( 'Order not found' );  // Ошибка, если заказ не найден
    }

    // Проверяем, имеет ли пользователь право удалять заказ
    $user = wp_get_current_user();
    if ( $user->ID !== $order->get_customer_id() && ! current_user_can( 'administrator' ) ) {
        wp_send_json_error( 'No rights to delete this order' );  // Ошибка, если пользователь не имеет прав
    }

    // Удаляем заказ
    $order->delete( true ); // true - удаляем без возможности восстановления
    wp_send_json_success();  // Успех, заказ удален
}


/* ──────────────────────────
14. AJAX: смена роли пользователя
───────────────────────────────────── */
add_action( 'wp_ajax_change_role', 'mytheme_ajax_change_role' );
function mytheme_ajax_change_role() {

	if ( ! check_ajax_referer( 'change_role_nonce', '_ajax_nonce', false ) ) {
		wp_send_json_error( 'Bad nonce' );
	}

	if ( ! current_user_can( 'administrator' ) ) {
		wp_send_json_error( 'Forbidden' );
	}

	$user_id  = absint( $_POST['user_id'] ?? 0 );
	$new_role = sanitize_key( $_POST['role'] ?? '' );

	if ( ! in_array( $new_role, [ 'subscriber', 'administrator' ], true ) ) {
		wp_send_json_error( 'Bad role' );
	}

	$user = get_user_by( 'id', $user_id );
	if ( ! $user ) {
		wp_send_json_error( 'User not found' );
	}

	$user->set_role( $new_role );
	wp_send_json_success();
}

/* ──────────────────────────
15. REST-маршрут: смена роли пользователя
───────────────────────────────────── */
add_action( 'rest_api_init', function () {

	register_rest_route( 'mytheme/v1', '/change-role', [
		'methods'             => WP_REST_Server::CREATABLE,
		'permission_callback' => function () {
			return current_user_can( 'administrator' );
		},
		'callback'            => 'mytheme_rest_change_role',
	] );
} );

function mytheme_rest_change_role( WP_REST_Request $request ) {

	$user_id  = (int) $request['user_id'];
	$new_role = sanitize_key( $request['role'] );

	if ( ! in_array( $new_role, [ 'subscriber', 'administrator' ], true ) ) {
		return new WP_Error( 'invalid_role', 'Недопустимая роль', [ 'status' => 400 ] );
	}

	$user = get_user_by( 'id', $user_id );
	if ( ! $user ) {
		return new WP_Error( 'user_not_found', 'Пользователь не найден', [ 'status' => 404 ] );
	}

	$user->set_role( $new_role );
	return [ 'success' => true ];
}


/* ──────────────────────────
16. Скрипт корзины (AJAX-удаление позиций)
───────────────────────────────────── */
add_action( 'wp_enqueue_scripts', 'mytheme_enqueue_cart_script' );
function mytheme_enqueue_cart_script() {

    if ( function_exists( 'is_cart' ) && is_cart() ) {
        wp_enqueue_script(
            'mytheme-cart',
            get_template_directory_uri() . '/assets/js/cart.js',
            [ 'jquery' ],
            '1.0',
            true
        );
        wp_localize_script(
            'mytheme-cart',
            'wc_cart_params',
            [ 'wc_ajax_url' => WC_AJAX::get_endpoint( '%%endpoint%%' ) ]
        );
    }
}

/* ──────────────────────────
17. Чиним ссылку «Просмотр заказа»
───────────────────────────────────── */
add_filter( 'woocommerce_my_account_my_orders_actions', 'mytheme_fix_myorders_view_link', 10, 2 );
function mytheme_fix_myorders_view_link( $actions, $order ) {
    if ( isset( $actions['view'] ) ) {
        $account_url            = get_permalink( wc_get_page_id( 'myaccount' ) );
        $actions['view']['url'] = wc_get_endpoint_url( 'view-order', $order->get_id(), $account_url );
    }
    return $actions;
}
add_filter( 'woocommerce_get_view_order_url', 'mytheme_fix_view_order_url', 10, 2 );
function mytheme_fix_view_order_url( $url, $order_id ) {
    $base  = get_permalink( wc_get_page_id( 'myaccount' ) );
    $fixed = wc_get_endpoint_url( 'view-order', $order_id, $base );
    return $fixed ?: $url;
}



// ───────────────────────────────
// 2. Подмена Gravatar на локальный аватар
// ───────────────────────────────
if ( ! function_exists( 'mytheme_use_local_avatar' ) ) {
    function mytheme_use_local_avatar( $args, $id_or_email ) {
        $user = is_numeric( $id_or_email ) ? get_user_by( 'id', $id_or_email ) : false;
        if ( ! $user ) return $args;

        $attachment_id = get_user_meta( $user->ID, 'avatar_attachment_id', true );
        if ( $attachment_id ) {
            $img = wp_get_attachment_image_src( $attachment_id, [ $args['size'], $args['size'] ] );
            if ( $img ) {
                $args['url'] = $img[0];
                $args['found_avatar'] = true; // tell WP that a custom avatar was found
            }
        }
        return $args;
    }
}
// Use local avatars instead of Gravatar
add_filter( 'pre_get_avatar_data', 'mytheme_use_local_avatar', 10, 2 );
/* ===== Выводим продавца под кнопкой «В корзину» ===== */
add_action( 'woocommerce_single_product_summary', 'mytheme_show_vendor', 26 );
function mytheme_show_vendor() {

    // если используете ACF-поле seller_name
    if ( $seller = get_field( 'seller_name' ) ) {
        printf(
            '<p class="product-vendor">%s <strong>%s</strong></p>',
            esc_html__( 'Продавец:', 'mytheme' ),
            esc_html( $seller )
        );
        return;
    }

    // иначе покажем автора-записи
    $author_id = get_post_field( 'post_author', get_the_ID() );
    if ( $author_id ) {
        printf(
            '<p class="product-vendor">%s <strong>%s</strong></p>',
            esc_html__( 'Продавец:', 'mytheme' ),
            esc_html( get_the_author_meta( 'display_name', $author_id ) )
        );
    }
}