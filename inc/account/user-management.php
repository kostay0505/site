<?php
// inc/account/user-management.php

// Доступ только для администратора
if ( ! current_user_can( 'administrator' ) ) {
	echo '<p>' . esc_html__( 'У вас нет прав на просмотр этой страницы.', 'my-custom-theme' ) . '</p>';
	return;
}

$all_users = get_users();
if ( empty( $all_users ) ) {
	echo '<p>' . esc_html__( 'Нет зарегистрированных пользователей.', 'my-custom-theme' ) . '</p>';
	return;
}

$rest_url      = esc_url_raw( rest_url( 'mytheme/v1/change-role' ) );
$wp_rest_nonce = wp_create_nonce( 'wp_rest' );
?>

<h2><?php esc_html_e( 'Управление пользователями', 'my-custom-theme' ); ?></h2>

<table class="users-table">
	<thead>
		<tr>
			<th>ID</th>
			<th><?php esc_html_e( 'Логин',  'my-custom-theme' ); ?></th>
			<th>E-mail</th>
			<th><?php esc_html_e( 'Роль',   'my-custom-theme' ); ?></th>
			<th><?php esc_html_e( 'Действие', 'my-custom-theme' ); ?></th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ( $all_users as $u ) : ?>
		<?php
		/** @var WP_User $u */
		$roles = (array) $u->roles;
		/** @var WP_User $u */
$roles = (array) $u->roles;

/* берём первую роль независимо от ключей; если массив пустой — $role=false */
$role  = reset( $roles );
if ( false === $role ) {
	$role = 'none';
}

		?>
		<tr id="user-<?php echo esc_attr( $u->ID ); ?>">
			<td><?php echo esc_html( $u->ID ); ?></td>
			<td><?php echo esc_html( $u->user_login ); ?></td>
			<td><?php echo esc_html( $u->user_email ); ?></td>

			<td class="role-label"><?php echo esc_html( $role ); ?></td>

			<td>
				<select class="role-select">
					<option value="subscriber"    <?php selected( $role, 'subscriber' );    ?>>User</option>
					<option value="administrator" <?php selected( $role, 'administrator' ); ?>>Admin</option>
				</select>

				<button type="button"
				        class="change-role-btn btn btn--primary btn--small"
				        data-user-id="<?php echo esc_attr( $u->ID ); ?>">
					<?php esc_html_e( 'Сохранить', 'my-custom-theme' ); ?>
				</button>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<style>
.users-table{width:100%;border-collapse:collapse;margin-top:1rem}
.users-table th,.users-table td{padding:.5rem 1rem;border-bottom:1px solid #e6e6e6;font-size:.95rem;text-align:left}
.users-table th{font-weight:500}
.users-table select{min-width:120px}
.users-table button{margin-left:.5rem}
</style>

<script>
jQuery(function($){

	const restUrl = '<?php echo $rest_url; ?>';
	const wpNonce = '<?php echo $wp_rest_nonce; ?>';

	$('body').on('click', '.change-role-btn', function(){

		const $btn    = $(this);
		const $row    = $btn.closest('tr');
		const userId  = $btn.data('user-id');
		const newRole = $row.find('.role-select').val();

		$btn.prop('disabled', true).text('<?php echo esc_js( __( 'Сохраняю…', 'my-custom-theme' ) ); ?>');

		$.ajax({
			url:  restUrl,
			type: 'POST',
			headers: { 'X-WP-Nonce': wpNonce },
			data: { user_id: userId, role: newRole },
			dataType: 'json'
		})
		.done(function(resp){
			if ( resp && resp.success ) {
				$row.find('.role-label').text(newRole);
				$btn.text('<?php echo esc_js( __( 'Готово', 'my-custom-theme' ) ); ?>');
				setTimeout(function(){
					$btn.prop('disabled', false).text('<?php echo esc_js( __( 'Сохранить', 'my-custom-theme' ) ); ?>');
				}, 1500);
			} else {
				alert(resp.data || 'Error');
				$btn.prop('disabled', false).text('<?php echo esc_js( __( 'Сохранить', 'my-custom-theme' ) ); ?>');
			}
		})
		.fail(function(jqXHR){
			alert('HTTP ' + jqXHR.status + ' – ' + (jqXHR.responseJSON?.message || 'Server error'));
			$btn.prop('disabled', false).text('<?php echo esc_js( __( 'Сохранить', 'my-custom-theme' ) ); ?>');
		});
	});
});
</script>
