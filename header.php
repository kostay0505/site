<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- AutoAnimate (FormKit) -->
<script src="https://unpkg.com/@formkit/auto-animate"></script>

  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header class="site-header">
  <div class="header-inner container">

    <!-- Бургер-кнопка (ведёт на каталог Оборудование) -->
    <button class="burger-products" onclick="location.href='<?php echo esc_url( home_url('/products/') ); ?>';">
      <span class="line"></span>
      <span class="line"></span>
      <span class="line"></span>
      <span class="btn-text">Оборудование</span>
    </button>

    <!-- Логотип по центру -->
    <div class="logo">
      <a href="<?php echo esc_url( home_url() ); ?>">
        <img 
          src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" 
          alt="<?php echo esc_attr( get_bloginfo('name') ); ?>"
        >
      </a>
    </div>

    <!-- Главное меню, выровнено справа от логотипа -->
    <nav class="main-nav">
      <a href="<?php echo esc_url( home_url('/installations/') ); ?>">Инсталляции</a>
      <a href="<?php echo esc_url( home_url('/news/') ); ?>">Новости</a>
      <a href="<?php echo esc_url( home_url('/faq/') ); ?>">Вопросы</a>
      <a href="<?php echo esc_url( home_url('/contacts/') ); ?>">Контакты</a>
      <a href="<?php echo esc_url( home_url('/about/') ); ?>">О нас</a>
    </nav>

    <!-- Иконки корзины и аккаунта -->
    <div class="header-icons">
      <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link" title="Корзина">
        <img 
          src="<?php echo get_template_directory_uri(); ?>/assets/images/cart.png" 
          alt="Cart"
        >
      </a>

      <?php if ( is_user_logged_in() ) : ?>
        <a href="<?php echo esc_url( home_url('/account/') ); ?>" class="account-link" title="Мой аккаунт">
          <img 
            src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png" 
            alt="Account"
          >
        </a>
      <?php else : ?>
        <a href="<?php echo esc_url( home_url('/user_login/') ); ?>" class="account-link" title="Войти">
          <img 
            src="<?php echo get_template_directory_uri(); ?>/assets/images/account.png" 
            alt="Login"
          >
        </a>
      <?php endif; ?>
    </div>

  </div>
</header>
