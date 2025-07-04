<?php
/**
 * Template Name: Account Page
 * Личный кабинет пользователя
 * @package my-custom-theme
 */

defined( 'ABSPATH' ) || exit;
get_header();
mytheme_breadcrumbs();

/* ───────── базовые переменные ───────── */
$account_page_id  = get_queried_object_id();
$account_page_url = get_permalink( $account_page_id );
$section          = isset( $_GET['section'] )
  ? sanitize_key( wp_unslash( $_GET['section'] ) )
  : 'dashboard';
$mode             = isset( $_GET['mode'] )
  ? sanitize_key( wp_unslash( $_GET['mode'] ) )
  : ''; // для ads: '', 'new', 'edit'

/* ───────── меню ───────── */
$menu = [
  'profile'   => [ 'profile.svg',   __( 'Личная информация', 'my-custom-theme' ) ],
  'payment'   => [ 'payment.svg',   __( 'Платёжные реквизиты', 'my-custom-theme' ) ],
  'cart'      => [ 'cart.svg',      __( 'Корзина', 'my-custom-theme' ) ],
  'orders'    => [ 'orders.svg',    __( 'Заказы',  'my-custom-theme' ) ],
  'ads'       => [ 'ads.svg',       __( 'Объявления', 'my-custom-theme' ) ],
  'favorites' => [ 'heart.svg',     __( 'Избранное', 'my-custom-theme' ) ],
];
if ( current_user_can( 'administrator' ) ) {
  $menu['pending'] = [ 'pending.svg', __( 'На утверждении', 'my-custom-theme' ) ];
  $menu['users']   = [ 'users.svg',   __( 'Пользователи', 'my-custom-theme' ) ];
}
$menu['logout'] = [ 'logout.svg', __( 'Выйти', 'my-custom-theme' ) ];
?>

<div class="page-wrapper account-wrapper">

  <!-- Sidebar -->
  <aside class="account-sidebar">
      <?php
$current_user = wp_get_current_user();
$display_name = trim($current_user->first_name . ' ' . $current_user->last_name);
if (!$display_name) $display_name = $current_user->user_login;
$email = $current_user->user_email;
$avatar = get_avatar($current_user->ID, 60);
?>



      <?php
$current_user   = wp_get_current_user();

// Имя + фамилия (если не заполнены — логин)
$display_name   = trim( $current_user->first_name . ' ' . $current_user->last_name );
if ( '' === $display_name ) {
	$display_name = $current_user->user_login;
}

// E-mail пользователя
$email          = $current_user->user_email;

// Аватар (Gravatar или тот, что вы сохраняете в user-meta)
// 60 px — как в макете Фигмы
$avatar_img_tag = get_avatar( $current_user->ID, 60, '', esc_attr( $display_name ), [
	'class' => 'account-user__avatar',
] );
?>

    <div class="account-user">
	<?= $avatar_img_tag; // аватар ?>

	<div class="account-user__meta">
		<strong class="account-user__name"><?= esc_html( $display_name ); ?></strong>
		<span  class="account-user__email"><?= esc_html( $email ); ?></span>
	</div>
</div>

    <nav class="account-menu"><ul>
      <?php foreach ( $menu as $slug => $item ) :
        $count_html = '';

        /* ── счётчики ── */
        if ( 'orders' === $slug && is_user_logged_in() ) {
          $cnt        = wc_get_customer_order_count( get_current_user_id() );
          $count_html = $cnt ? '<span class="menu-count">' . intval( $cnt ) . '</span>' : '';
        }

        if ( 'ads' === $slug && is_user_logged_in() ) {                   // ← ДОБАВЛЕНО
          $ads_q      = new WP_Query( [
            'post_type'      => 'product',
            'author'         => get_current_user_id(),
            'post_status'    => [ 'publish', 'pending', 'draft' ],
            'posts_per_page' => 1,
            'fields'         => 'ids',
          ] );
          $count_html = '<span class="menu-count">' . intval( $ads_q->found_posts ) . '</span>';
          wp_reset_postdata();
        }

        /* ── ссылка ── */
        $url = ( 'logout' === $slug )
          ? wp_logout_url( $account_page_url )
          : add_query_arg( 'section', $slug, $account_page_url );

        printf(
          '<li class="%1$s"><a href="%2$s"><img src="%3$s/assets/icons/%4$s" alt="">%5$s%6$s</a></li>',
          esc_attr( $slug === $section ? 'is-active' : '' ),
          esc_url( $url ),
          esc_url( get_template_directory_uri() ),
          esc_attr( $item[0] ),
          esc_html( $item[1] ),
          $count_html
        );
      endforeach; ?>
    </ul></nav>
  </aside>

  <!-- Content -->
  <main class="account-content">

  <?php switch ( $section ) :

    /* ────────────────────────── Личная информация ───────────────────────── */
    case 'profile':
      get_template_part( 'inc/account/personal-info' );
      break;

    /* ────────────────────────── Платёжные реквизиты ─────────────────────── */
    case 'payment':
      get_template_part( 'inc/dashboard/payment-methods' );
      break;

    /* ────────────────────────── Корзина ─────────────────────────────────── */
    case 'cart':
      echo '<h2>' . esc_html__( 'Ваша корзина', 'my-custom-theme' ) . '</h2>';
      echo do_shortcode( '[woocommerce_cart]' );
      break;

    /* ────────────────────────── Мои заказы ──────────────────────────────── */
    case 'orders':
      get_template_part( 'inc/account/orders-table' ); // ваш уже настроенный файл
      break;

/* ======================================================================
 *                               О Б Ъ Я В Л Е Н И Я
 * ====================================================================== */
case 'ads':

	/* ---------- режим создания / редактирования ---------- */
	if ( in_array( $mode, [ 'new', 'edit' ], true ) ) {
		get_template_part( 'inc/dashboard/ad-editor' );
		break;
	}

	/* ---------- заголовок + кнопка "Новое" ---------- */
	echo '<div class="ads-header">';
	echo '<h2>' . esc_html__( 'Мои объявления', 'my-custom-theme' ) . '</h2>';
	echo '<a class="btn btn--primary" href="' . esc_url( add_query_arg(
		[ 'section' => 'ads', 'mode' => 'new' ], $account_page_url
	) ) . '">+ ' . esc_html__( 'Новое объявление', 'my-custom-theme' ) . '</a>';
	echo '</div>';

	/* ---------- доступ только авторизованным ---------- */
	if ( ! is_user_logged_in() ) {
		echo '<p>' . esc_html__( 'Пожалуйста, войдите, чтобы увидеть объявления.', 'my-custom-theme' ) . '</p>';
		break;
	}

	/* ---------- выборка объявлений текущего пользователя ---------- */
	$paged = max( 1, get_query_var( 'paged' ) );
	$ads   = new WP_Query( [
		'post_type'      => 'product',                 // ваш CPT, если не product
		'author'         => get_current_user_id(),
		'post_status'    => [ 'publish', 'pending', 'draft' ],
		'paged'          => $paged,
		'posts_per_page' => 12,
	] );

	if ( ! $ads->have_posts() ) {
		echo '<p>' . esc_html__( 'У вас пока нет объявлений.', 'my-custom-theme' ) . '</p>';
		break;
	}

	/* ---------- вывод сетки ---------- */
	echo '<div class="ads-grid">';
	while ( $ads->have_posts() ) : $ads->the_post(); global $product; ?>
		<div class="ads-card">

			<a class="ads-thumb" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'medium' ); ?>
			</a>

			<h3 class="ads-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

			<span class="ads-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>

			<span class="ads-status status-<?php echo esc_attr( get_post_status() ); ?>">
				<?php echo esc_html( get_post_status_object( get_post_status() )->label ); ?>
			</span>

			<div class="ads-actions">
				<!-- Редактировать -->
				<a class="btn btn--small"
				   href="<?php echo esc_url( add_query_arg(
					   [
						   'section' => 'ads',
						   'mode'    => 'edit',
						   'ad_id'   => get_the_ID(),     // передаём ID объявления
					   ],
					   $account_page_url
				   ) ); ?>">
					<?php esc_html_e( 'Редактировать', 'my-custom-theme' ); ?>
				</a>

				<!-- Удалить -->
				<a class="btn btn--small btn--danger ads-delete"
				   href="<?php echo esc_url( wp_nonce_url(
					   add_query_arg(
						   [
							   'section' => 'ads',
							   'mode'    => 'delete',      // обработчик удаления
							   'ad_id'   => get_the_ID(),
						   ],
						   $account_page_url
					   ),
					   'mytheme_delete_ad_' . get_the_ID()
				   ) ); ?>"
				   onclick="return confirm('<?php echo esc_js( __( 'Удалить объявление?', 'my-custom-theme' ) ); ?>');">
					<?php esc_html_e( 'Удалить', 'my-custom-theme' ); ?>
				</a>
			</div>

		</div>
	<?php endwhile;
	wp_reset_postdata();
	echo '</div>'; /* .ads-grid */

	/* ---------- пагинация ---------- */
	echo '<div class="ads-pagination">';
	echo paginate_links( [
		'total'   => $ads->max_num_pages,
		'current' => $paged,
		'base'    => add_query_arg(
			[
				'session' => 'ads',              // сохраняем секцию
				'paged'   => '%#%',
			],
			$account_page_url
		),
	] );
	echo '</div>';

	break;


    /* ────────────────────────── Избранное ───────────────────────────────── */
    case 'favorites':
      get_template_part( 'inc/dashboard/favorites' );
      break;

    /* ────────────────────────── На утверждении ──────────────────────────── */
    case 'pending':
      get_template_part( 'inc/account/pending-posts' );
      break;

    /* ────────────────────────── Пользователи ───────────────────────────── */
    case 'users':
      get_template_part( 'inc/account/user-management' );
      break;

    /* ────────────────────────── Выход ───────────────────────────────────── */
    case 'logout':
      // wp_logout_url() уже делает редирект
      break;

    /* ────────────────────────── Не найдено ─────────────────────────────── */
    default:
      echo '<p>' . esc_html__( 'Раздел не найден.', 'my-custom-theme' ) . '</p>';
      break;

  endswitch; ?>

  </main>
</div>

<?php get_footer(); ?>
