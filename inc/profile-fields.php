<?php
// Показываем поля в разделе "Основная информация" профиля
add_action( 'show_user_profile', 'mt_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'mt_show_extra_profile_fields' );

function mt_show_extra_profile_fields( $user ) {
    ?>
    <h2>Основная информация</h2>
    <table class="form-table">
      <!-- Название компании -->
      <tr>
        <th><label for="company_name">Название компании</label></th>
        <td>
          <input type="text" name="company_name" id="company_name"
            value="<?php echo esc_attr( get_user_meta( $user->ID, 'company_name', true ) ); ?>"
            class="regular-text" />
        </td>
      </tr>
      <!-- Логотип -->
      <tr>
        <th><label for="company_logo">Логотип</label></th>
        <td>
          <?php
            $logo = get_user_meta( $user->ID, 'company_logo', true );
            if ( $logo ) {
              echo wp_get_attachment_image( $logo, [100,100] ) . '<br>';
            }
          ?>
          <input type="file" name="company_logo" id="company_logo" />
        </td>
      </tr>
      <!-- Адрес -->
      <tr>
        <th><label for="company_address">Адрес</label></th>
        <td>
          <input type="text" name="company_address" id="company_address"
            value="<?php echo esc_attr( get_user_meta( $user->ID, 'company_address', true ) ); ?>"
            class="regular-text" />
        </td>
      </tr>
      <!-- Телефон -->
      <tr>
        <th><label for="company_phone">Телефон</label></th>
        <td>
          <input type="text" name="company_phone" id="company_phone"
            value="<?php echo esc_attr( get_user_meta( $user->ID, 'company_phone', true ) ); ?>"
            class="regular-text" />
        </td>
      </tr>
      <!-- …добавьте остальные поля по аналогии… -->
    </table>
    <?php
}

// Сохраняем эти поля
add_action( 'personal_options_update', 'mt_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'mt_save_extra_profile_fields' );

function mt_save_extra_profile_fields( $user_id ) {
  if ( ! current_user_can( 'edit_user', $user_id ) ) {
    return false;
  }

  // company_name, company_address, company_phone
  update_user_meta( $user_id, 'company_name', sanitize_text_field( $_POST['company_name'] ) );
  update_user_meta( $user_id, 'company_address', sanitize_text_field( $_POST['company_address'] ) );
  update_user_meta( $user_id, 'company_phone', sanitize_text_field( $_POST['company_phone'] ) );

  // company_logo — обработка загрузки
  if ( ! empty( $_FILES['company_logo']['name'] ) ) {
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $upload = wp_handle_upload( $_FILES['company_logo'], [ 'test_form' => false ] );
    if ( ! empty( $upload['file'] ) ) {
      $file     = $upload['file'];
      $filetype = wp_check_filetype( $file );
      $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_file_name( basename( $file ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
      ];
      $attach_id = wp_insert_attachment( $attachment, $file );
      $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
      wp_update_attachment_metadata( $attach_id, $attach_data );
      update_user_meta( $user_id, 'company_logo', $attach_id );
    }
  }
}
