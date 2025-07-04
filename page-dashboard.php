<?php
/**
 * Template Name: Личный кабинет
 * Description: Шаблон страницы личного кабинета, где пользователь может управлять своими записями.
 */

get_header();

// Если пользователь не авторизован, перенаправляем на страницу входа
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

// Получаем данные текущего пользователя
$current_user = wp_get_current_user();

// Вывод хлебных крошек (если функция определена в functions.php)
if ( function_exists( 'mytheme_breadcrumbs' ) ) {
    mytheme_breadcrumbs();
}
?>

<div class="page-wrapper">
    <main class="site-main">
        <section class="dashboard-info">
            <h1>Добро пожаловать, <?php echo esc_html( $current_user->display_name ); ?>!</h1>
            <p>Ваш email: <?php echo esc_html( $current_user->user_email ); ?></p>
        </section>

        <section class="dashboard-posts">
            <h2>Мои объявления</h2>
            <?php
            // Запрос для получения записей (например, кастомного пост-типа "listings")
            $args = array(
                'post_type'      => 'post', // замените на 'listings' если используете кастомный пост-тип
                'author'         => $current_user->ID,
                'posts_per_page' => -1
            );
            $my_posts = new WP_Query( $args );

            if ( $my_posts->have_posts() ) {
                echo '<ul class="dashboard-posts-list">';
                while ( $my_posts->have_posts() ) {
                    $my_posts->the_post();
                    echo '<li>';
                    echo '<a href="' . esc_url( get_edit_post_link() ) . '">' . get_the_title() . '</a> ';
                    // Выводим статус поста
                    echo '(' . get_post_status() . ')';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>Пока что у вас нет объявлений.</p>';
            }
            wp_reset_postdata();
            ?>
        </section>

        <section class="dashboard-add">
            <h2>Добавить объявление</h2>
            <p>Нажмите ниже, чтобы добавить новое объявление для каталога.</p>
            <a class="btn-add" href="<?php echo esc_url( home_url( '/add-new-listing/' ) ); ?>">Добавить объявление</a>
        </section>
    </main>
</div>

<?php get_footer(); ?>
