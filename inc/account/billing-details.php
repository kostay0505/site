<?php
/**
 * Личный кабинет → Платёжные реквизиты
 * Отображается внутри page-account.php
 */

$current_user = wp_get_current_user();
$payer_type   = get_user_meta( $current_user->ID, 'payer_type',   true );
$bank_name    = get_user_meta( $current_user->ID, 'bank_name',    true );
$iban         = get_user_meta( $current_user->ID, 'iban',         true );
?>

<!-- === Тип лица ======================================================= -->
<div class="payer-type">
  <?php
  $types = [
    'individual' => 'Физическое лицо',
    'company'    => 'Юридическое лицо',
    'sole'       => 'ИП',
  ];
  foreach ( $types as $slug => $label ) {
    $active = ( $slug === $payer_type ) ? 'active' : '';
    printf(
      '<button class="btn %s" name="payer_type" value="%s" form="pay-form">%s</button>',
      $active,
      esc_attr( $slug ),
      esc_html( $label )
    );
  }
  ?>
</div>

<!-- === Форма реквизитов =============================================== -->
<form id="pay-form" class="pay-form" method="post">
  <label>Имя / Компания
    <input type="text" name="bank_name" value="<?php echo esc_attr( $bank_name ); ?>">
  </label>

  <label>IBAN / Расчётный счёт
    <input type="text" name="iban" value="<?php echo esc_attr( $iban ); ?>">
  </label>

  <?php wp_nonce_field( 'save_billing', '_billing_nonce' ); ?>
  <button type="submit" class="btn full">Сохранить</button>
</form>

<?php
/* ========== Сохранение ========== */
if ( isset( $_POST['_billing_nonce'] ) && wp_verify_nonce( $_POST['_billing_nonce'], 'save_billing' ) ) {

  update_user_meta( $current_user->ID, 'payer_type', sanitize_key( $_POST['payer_type'] ?? 'individual' ) );
  update_user_meta( $current_user->ID, 'bank_name',  sanitize_text_field( $_POST['bank_name'] ?? '' ) );
  update_user_meta( $current_user->ID, 'iban',       sanitize_text_field( $_POST['iban'] ?? '' ) );

  echo '<p style="color:green">Реквизиты сохранены!</p>';
}
