<?php
/**
 * inc/account/orders-table.php
 * Личный кабинет → Мои заказы
 * Строка заказа + скрытая строка деталей (для orders-accordion.js)
 */
defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$is_admin = current_user_can('administrator');

// Формируем выборку заказов: все для админа, только свои для пользователя
$args = [
    'orderby'        => 'date',
    'order'          => 'DESC',
    'paged'          => max( 1, get_query_var( 'paged' ) ),
    'posts_per_page' => 10,
];

if ( ! $is_admin ) {
    $args['customer_id'] = $current_user->ID;
}

$orders = wc_get_orders( $args );

if ( ! $orders ) {
    echo '<p>' . esc_html__( 'У вас пока нет заказов.', 'my-custom-theme' ) . '</p>';
    return;
}
?>
<!-- ВАЖНО: переменные для AJAX-скриптов -->
<script>
window.MyThemeOrder = {
  ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
  delete_nonce: '<?php echo wp_create_nonce('delete_order_nonce'); ?>'
};
</script>

<table class="account-orders-table">
  <thead>
    <tr>
      <th><?php esc_html_e( 'Номер',  'my-custom-theme' ); ?></th>
      <th><?php esc_html_e( 'Дата',   'my-custom-theme' ); ?></th>
      <th><?php esc_html_e( 'Статус', 'my-custom-theme' ); ?></th>
      <th><?php esc_html_e( 'Сумма',  'my-custom-theme' ); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ( $orders as $order ) :
    // Пропускаем возвраты
    if ( $order instanceof WC_Order_Refund ) {
        continue;
    }
    $id        = $order->get_id();
    $target_id = 'order-' . $id;
    $is_own_order = ( (int)$order->get_customer_id() === $current_user->ID );
  ?>
    <!-- строка заказа -->
    <tr class="order-row" data-target="<?php echo esc_attr( $target_id ); ?>">
      <td>#<?php echo esc_html( $id ); ?></td>
      <td><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></td>
      <td class="status-cell">
        <?php if ( $is_admin ) : ?>
          <select class="order-status-select" data-order-id="<?php echo esc_attr( $id ); ?>">
            <?php foreach ( wc_get_order_statuses() as $slug => $name ) :
              $s = str_replace( 'wc-', '', $slug );
            ?>
              <option value="<?php echo esc_attr( $s ); ?>"
                <?php selected( $s, $order->get_status() ); ?>>
                <?php echo esc_html( $name ); ?>
              </option>
            <?php endforeach; ?>
          </select>
        <?php else : ?>
          <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
        <?php endif; ?>
      </td>
      <td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
      <td>
        <button type="button" class="order-toggle"
                data-target="<?php echo esc_attr( $target_id ); ?>">
          <?php esc_html_e( 'Подробнее', 'my-custom-theme' ); ?>
        </button>
      </td>
    </tr>

    <!-- строка с деталями заказа -->
    <tr id="<?php echo esc_attr( $target_id ); ?>" class="order-details-row">
      <td colspan="5">
        <div class="details-box">
          <h4><?php esc_html_e( 'Информация о клиенте', 'my-custom-theme' ); ?></h4>
          <ul class="order-customer-info">
            <?php
              $customer_id = $order->get_customer_id();
              $user        = $customer_id ? get_user_by( 'ID', $customer_id ) : false;
            ?>
            <?php if ( $user ) : ?>
              <li><strong><?php esc_html_e( 'Логин:', 'my-custom-theme' ); ?></strong>
                  <?php echo esc_html( $user->user_login ); ?></li>
            <?php endif; ?>
            <li><strong><?php esc_html_e( 'E-mail:', 'my-custom-theme' ); ?></strong>
                <?php echo esc_html( $order->get_billing_email() ); ?></li>
            <li><strong><?php esc_html_e( 'Телефон:', 'my-custom-theme' ); ?></strong>
                <?php echo esc_html( $order->get_billing_phone() ); ?></li>
          </ul>

          <h4><?php esc_html_e( 'Товары заказа', 'my-custom-theme' ); ?></h4>
          <ul class="order-items">
            <?php foreach ( $order->get_items() as $item ) : ?>
              <li>
                <?php echo esc_html( $item->get_name() )
                    . ' × ' . esc_html( $item->get_quantity() ); ?>
              </li>
            <?php endforeach; ?>
          </ul>

          <h4><?php esc_html_e( 'Адреса', 'my-custom-theme' ); ?></h4>
          <div class="order-addresses">
            <div class="address-block">
              <strong><?php esc_html_e( 'Биллинг', 'my-custom-theme' ); ?></strong><br>
              <?php echo wp_kses_post( $order->get_formatted_billing_address() ); ?>
            </div>
            <div class="address-block">
              <strong><?php esc_html_e( 'Шиппинг', 'my-custom-theme' ); ?></strong><br>
              <?php
                $ship = $order->get_formatted_shipping_address();
                echo wp_kses_post( $ship ? $ship : '—' );
              ?>
            </div>
          </div>

          <?php if ( $is_admin || $is_own_order ) : ?>
            <button type="button" class="order-delete"
                    data-order-id="<?php echo esc_attr( $id ); ?>">
              <?php esc_html_e( 'Удалить заказ', 'my-custom-theme' ); ?>
            </button>
          <?php endif; ?>

        </div>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php
// пагинация, если нужно:
echo '<div class="ads-pagination">';
echo paginate_links( [
  'total'   => is_array($orders) ? 1 : $orders->max_num_pages, // WooCommerce возвращает массив
  'current' => max( 1, get_query_var( 'paged' ) ),
] );
echo '</div>';
?>
