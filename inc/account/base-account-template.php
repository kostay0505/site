<?php
/**
 * Каркас внутренних страниц кабинета
 * Подключается как   require locate_template('inc/account/base-account-template.php');
 *
 * ── Доступные переменные ──────────────────────────
 * $active_slug  – slug активного пункта меню
 * $page_title   – заголовок h1 в контент‑области
 * $content_html – HTML‑строка с «начинкой» страницы
 * ──────────────────────────────────────────────────
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();
mytheme_breadcrumbs();
?>
<div class="account-wrapper">
  <!-- Левый сайдбар‑меню -->
  <aside class="account-sidebar">
    <?php
      // массив: slug => [иконка, подпись]
      $items = [
        'account'         => ['dashboard.svg',        'Основная информация'],
        'personal-info'   => ['user.svg',             'Личная информация'],
        'payment-details' => ['bank.svg',             'Платежные реквизиты'],
        'basket'          => ['basket.svg',           'Корзина'],
        'orders'          => ['box.svg',              'Заказы'],
        'favourites'      => ['heart.svg',            'Избранное'],
        'listing-add'     => ['plus-square.svg',      'Создать объявление'],
        'my-listings'     => ['list.svg',             'Мои объявления'],
        'stats'           => ['chart.svg',            'Статистика'],
        'logout'          => ['logout.svg',           'Выйти из аккаунта'],
      ];

      foreach ( $items as $slug => [$icon,$label] ) :

        // «Выйти» — реальная ссылка logout.
        $href = ( $slug === 'logout' )
                  ? wp_logout_url( home_url() )
                  : home_url( "/{$slug}/" );

        $active = ( $slug === $active_slug ) ? 'active' : '';
    ?>
      <a href="<?php echo esc_url( $href ); ?>" class="sidebar-link <?php echo $active; ?>">
        <img src="<?php echo get_template_directory_uri()."/assets/images/account/{$icon}"; ?>" alt="">
        <span><?php echo esc_html( $label ); ?></span>
      </a>
    <?php endforeach; ?>
  </aside>

  <!-- Контент‑область -->
  <section class="account-content">
    <h1><?php echo esc_html( $page_title ); ?></h1>
    <?php echo $content_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
  </section>
</div>

<?php get_footer(); ?>
