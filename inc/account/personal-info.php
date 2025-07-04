<?php
/**
 * Личный кабинет → Личная информация
 * Отображается внутри page-account.php
 */

$current_user = wp_get_current_user();
?>

<form class="personal-form"
      method="post"
      enctype="multipart/form-data">

    <!-- для хука mytheme_handle_personal_info_save -->

    <?php wp_nonce_field( 'save_personal', '_personal_nonce' ); ?>

    <div class="columns">
        <!-- Левая колонка -->
        <div class="col col-left">

            <!-- Аватар + поле загрузки -->
            <div class="avatar" style="margin-bottom:1rem;">
                <?php echo get_avatar( $current_user->ID, 120 ); ?>
                <input type="file" name="avatar" accept="image/*">
            </div>

            <!-- Имя -->
            <label>Имя
                <input type="text" name="first_name"
                       value="<?php echo esc_attr( $current_user->first_name ); ?>">
            </label>

            <!-- Фамилия -->
            <label>Фамилия
                <input type="text" name="last_name"
                       value="<?php echo esc_attr( $current_user->last_name ); ?>">
            </label>

            <!-- E‑mail -->
            <label>E‑mail
                <input type="email" name="user_email"
                       value="<?php echo esc_attr( $current_user->user_email ); ?>">
            </label>

            <!-- Название компании -->
            <label>Название компании
                <input type="text" name="company_name"
                       value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'company_name', true ) ); ?>">
            </label>

            <!-- Логотип -->
            <label>Логотип
                <?php
                    $logo_id = get_user_meta( $current_user->ID, 'company_logo', true );
                    if ( $logo_id ) {
                        echo wp_get_attachment_image( $logo_id, [ 100, 100 ] ) . '<br>';
                    }
                ?>
                <input type="file" name="company_logo" accept="image/*">
            </label>

            <!-- Страна -->
            <label>Страна
                <input type="text" name="company_country"
                       value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'company_country', true ) ); ?>">
            </label>

            <!-- Город -->
            <label>Город
                <input type="text" name="company_city"
                       value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'company_city', true ) ); ?>">
            </label>

            <!-- Адрес -->
            <label>Адрес
                <input type="text" name="company_address"
                       value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'company_address', true ) ); ?>">
            </label>

            <!-- Телефон -->
            <label>Телефон
                <input type="tel" name="company_phone"
                       value="<?php echo esc_attr( get_user_meta( $current_user->ID, 'company_phone', true ) ); ?>">
            </label>
        </div>

        <!-- Правая колонка -->
        <div class="col col-right">
            <h3>Смена пароля</h3>

            <label>Новый пароль
                <input type="password" name="pass1">
            </label>

            <label>Подтвердите новый пароль
                <input type="password" name="pass2">
            </label>
        </div>
    </div>

    <button type="submit" class="btn full">Сохранить</button>
</form>

<?php
/* ========== Обработка формы ========== */
if (
    isset( $_POST['_personal_nonce'] )
    && wp_verify_nonce( $_POST['_personal_nonce'], 'save_personal' )
) {
    // --- Обновляем основные поля пользователя ---
    $update = [
        'ID'         => $current_user->ID,
        'first_name' => sanitize_text_field( $_POST['first_name'] ?? $current_user->first_name ),
        'last_name'  => sanitize_text_field( $_POST['last_name']  ?? $current_user->last_name ),
        'user_email' => sanitize_email(       $_POST['user_email'] ?? $current_user->user_email ),
    ];

    if ( ! empty( $_POST['pass1'] ) && $_POST['pass1'] === $_POST['pass2'] ) {
        $update['user_pass'] = $_POST['pass1'];
    }
    wp_update_user( $update );

    // --- Локальный аватар ---
    if ( ! empty( $_FILES['avatar']['name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attach_id = media_handle_upload( 'avatar', 0 );
        if ( ! is_wp_error( $attach_id ) ) {
            update_user_meta( $current_user->ID, 'avatar_attachment_id', $attach_id );
            clean_user_cache( $current_user->ID );
        }
    }

    // --- Логотип компании ---
    if ( ! empty( $_FILES['company_logo']['name'] ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    // Удаляем старый логотип, если был
    $old_logo_id = get_user_meta( $current_user->ID, 'company_logo', true );
    if ( $old_logo_id ) {
        wp_delete_attachment( $old_logo_id, true );
    }

    $attach_id = media_handle_upload( 'company_logo', 0 );
    if ( ! is_wp_error( $attach_id ) ) {
        update_user_meta( $current_user->ID, 'company_logo', $attach_id );
    }
    }

    // --- Прочие мета ---
    update_user_meta( $current_user->ID, 'company_name',    sanitize_text_field( $_POST['company_name']    ?? '' ) );
    update_user_meta( $current_user->ID, 'company_country', sanitize_text_field( $_POST['company_country'] ?? '' ) );
    update_user_meta( $current_user->ID, 'company_city',    sanitize_text_field( $_POST['company_city']    ?? '' ) );
    update_user_meta( $current_user->ID, 'company_address', sanitize_text_field( $_POST['company_address'] ?? '' ) );
    update_user_meta( $current_user->ID, 'company_phone',   sanitize_text_field( $_POST['company_phone']   ?? '' ) );

    echo '<p style="color:green;">Данные сохранены!</p>';
}
?>
