<?php
/**
 * My Account – Orders list (accordion view)
 * @version 9.5.0-custom
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );
?>

<div class="account-section account-section_orders">
	<h2 class="account-section__title"><?php esc_html_e( 'Мои заказы', 'your-textdomain' ); ?></h2>

	<?php if ( $has_orders ) : ?>
		<table class="woocommerce-orders-table shop_table shop_table_responsive my_account_orders">
			<thead>
				<tr>
					<?php foreach ( wc_get_account_orders_columns() as $col_id => $col_name ) : ?>
						<th class="woocommerce-orders-table__header woocommerce-orders-table__header-<?php echo esc_attr( $col_id ); ?>">
							<span class="nobr"><?php echo esc_html( $col_name ); ?></span>
						</th>
					<?php endforeach; ?>
					<th class="woocommerce-orders-table__header woocommerce-orders-table__header-more">
						<span class="nobr"><?php esc_html_e( 'Действие', 'your-textdomain' ); ?></span>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ( $customer_orders->orders as $customer_order ) :
					$order        = wc_get_order( $customer_order );
					$order_id     = $order->get_id();
					$row_id       = 'order-details-' . $order_id;
				?>
					<tr class="woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?>">
						<?php foreach ( wc_get_account_orders_columns() as $column_id => $column_name ) : ?>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>"
							    data-title="<?php echo esc_attr( $column_name ); ?>">
								<?php
								switch ( $column_id ) {
									case 'order-number':
										printf( '<a href="#%1$s" class="order-toggle" data-target="%1$s">#%2$s</a>', esc_attr( $row_id ), esc_html( $order->get_order_number() ) );
										break;

									case 'order-date':
										echo esc_html( wc_format_datetime( $order->get_date_created() ) );
										break;

									case 'order-status':
										echo esc_html( wc_get_order_status_name( $order->get_status() ) );
										break;

									case 'order-total':
										echo wp_kses_post(
											sprintf(
												_n( '%1$s for %2$s item', '%1$s for %2$s items', $order->get_item_count(), 'woocommerce' ),
												$order->get_formatted_order_total(),
												$order->get_item_count()
											)
										);
										break;
								}
								?>
							</td>
						<?php endforeach; ?>

						<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-more">
							<button class="button btn btn--primary btn--small order-toggle"
							        data-target="<?php echo esc_attr( $row_id ); ?>">
								<?php esc_html_e( 'Подробнее', 'your-textdomain' ); ?>
							</button>
						</td>
					</tr>

					<!-- Скрытая строка с деталями -->
					<tr id="<?php echo esc_attr( $row_id ); ?>" class="order-details-row" style="display:none;">
						<td colspan="<?php echo esc_attr( count( wc_get_account_orders_columns() ) + 1 ); ?>">
							<?php
							/** Вставляем содержимое шаблона view-order.php **/
							wc_get_template(
								'myaccount/view-order.php',
								[ 'order_id' => $order_id ],
								'',                  // default path
								get_template_directory() . '/woocommerce/' // смотрит override-версии
							);
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php else : ?>
		<div class="woocommerce-message woocommerce-message--info">
			<?php esc_html_e( 'У вас пока нет заказов.', 'your-textdomain' ); ?>
		</div>
	<?php endif; ?>
</div>

<?php
do_action( 'woocommerce_after_account_orders', $has_orders );
