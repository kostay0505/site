<?php
/**
 * Template Name: Register Page
 * Description: Страница регистрации
 */

// Сразу — проверяем, пришёл ли POST, и ОБРАБАТЫВАЕМ ЕГО
if ( isset($_POST['register_submit']) ) {
  // проверяем nonce, но здесь для примера упрощённо
  $first_name  = sanitize_text_field( $_POST['first_name'] );
  $last_name   = sanitize_text_field( $_POST['last_name'] );
  $user_email  = sanitize_email( $_POST['user_email'] );
  $pass1       = $_POST['user_password'];
  $pass2       = $_POST['user_password2'];

  if ( $pass1 !== $pass2 ) {
    $error = 'Пароли не совпадают!';
  } else {
    $username = strtolower( preg_replace('/\W+/', '_', $first_name) . '_' . wp_generate_password( 6, false ) );
    $userdata = [
      'user_login'  => $username,
      'user_pass'   => $pass1,
      'user_email'  => $user_email,
      'first_name'  => $first_name,
      'last_name'   => $last_name,
      'role'        => 'user',
    ];
    $user_id = wp_insert_user( $userdata );
    if ( is_wp_error( $user_id ) ) {
      $error = 'Ошибка регистрации: ' . $user_id->get_error_message();
    } else {
      // автоматически логиним
      wp_set_current_user( $user_id );
      wp_set_auth_cookie( $user_id );
      // и редиректим в аккаунт
      wp_redirect( home_url('/account/') );
      exit;
    }
  }
}

get_header();
mytheme_breadcrumbs(); // хлебные крошки
?>

<div class="page-wrapper">
  <div class="auth-container">
    <div class="auth-image">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/reg.jpg" alt="">
    </div>
    <div class="auth-form">
      <h2>Регистрация</h2>
      <?php if ( ! empty( $error ) ) : ?>
        <p style="color:red;"><?php echo esc_html( $error ); ?></p>
      <?php endif; ?>
      <form method="post">
        <div class="form-row">
          <div class="form-group">
            <label for="first_name">Имя</label>
            <input type="text" name="first_name" id="first_name" required>
          </div>
          <div class="form-group">
            <label for="last_name">Фамилия</label>
            <input type="text" name="last_name" id="last_name" required>
          </div>
        </div>
        <div class="form-group">
          <label for="user_email">Email</label>
          <input type="email" name="user_email" id="user_email" required>
        </div>
        <div class="form-group">
          <label for="user_password">Пароль</label>
          <input type="password" name="user_password" id="user_password" required>
        </div>
        <div class="form-group">
          <label for="user_password2">Повтор пароля</label>
          <input type="password" name="user_password2" id="user_password2" required>
        </div>
        <button type="submit" name="register_submit" class="btn-contact">Зарегистрироваться</button>
      </form>
      <p class="auth-switch">
        Есть аккаунт? <a href="<?php echo home_url('/user_login/'); ?>">Войти</a>
      </p>
    </div>
  </div>
</div>

<?php get_footer(); ?>
