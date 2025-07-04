<?php
/**
 * Template Name: Login Page
 */

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_submit']) ) {
  // Обрабатываем логин до вывода HTML
  $creds = [
    'user_login'    => sanitize_text_field( $_POST['user_email'] ),
    'user_password' => $_POST['user_pass'],
    'remember'      => ! empty( $_POST['remember'] ),
  ];

  $user = wp_signon( $creds, false );

  if ( is_wp_error( $user ) ) {
    // Запоминаем ошибку, чтобы показать ниже
    $login_error = $user->get_error_message();
  } else {
    // При успешном логине — редиректим на /account/
    wp_safe_redirect( home_url( '/account/' ) );
    exit;
  }
}

get_header();
mytheme_breadcrumbs();
?>

<div class="page-wrapper">
  <div class="auth-container">

    <div class="auth-image">
      <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/log.jpg' ); ?>" alt="Login Image">
    </div>

    <div class="auth-form">
      <h2>Вход</h2>

      <?php if ( ! empty( $login_error ) ): ?>
        <div class="login-error" style="color:red; margin-bottom:1em;">
          <?php echo esc_html( $login_error ); ?>
        </div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="form-group">
          <label for="user_email">Email</label>
          <input type="email" id="user_email" name="user_email" required>
        </div>

        <div class="form-group">
          <label for="user_pass">Пароль</label>
          <input type="password" id="user_pass" name="user_pass" required>
        </div>

        <div class="form-group" style="display:flex; justify-content:space-between; align-items:center;">
          <label><input type="checkbox" name="remember"> Запомнить меня</label>
          <a href="<?php echo wp_lostpassword_url(); ?>">Забыли пароль?</a>
        </div>

        <button type="submit" name="login_submit" class="btn-contact">Войти</button>
      </form>

      <p class="auth-switch">
        Нет аккаунта?
        <a href="<?php echo esc_url( home_url( '/register/' ) ); ?>">Зарегистрируйтесь</a>
      </p>
    </div>

  </div>
</div>

<?php get_footer(); ?>
