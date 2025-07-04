<?php
/**
 * Template Name: Account – Basket
 */
if ( ! is_user_logged_in() ) { wp_redirect( home_url('/user_login/') ); exit; }

ob_start();
?>
<div class="basket-head">
  <h2>Корзина</h2>
  <a href="#" class="btn">Оформить заказ</a>
</div>

<div class="basket-grid">
  <?php
  // для примера выводим статические карточки
  $demo = [
    ['ARCS2','10 000 €','10 кмпл.','/assets/images/demo1.jpg'],
    ['LA‑RAK','10 000 €','5 шт.','/assets/images/demo2.jpg'],
    ['SD12‑96','10 000 €','10 кмпл.','/assets/images/demo3.jpg'],
    ['ARCS2','10 000 €','15 шт.','/assets/images/demo1.jpg'],
  ];
  foreach ( $demo as [$title,$price,$qty,$img] ) : ?>
    <div class="basket-item">
      <img src="<?php echo get_template_directory_uri().$img; ?>" alt="">
      <h3><?php echo esc_html( $title ); ?></h3>
      <span class="price"><?php echo $price; ?></span>
      <span class="qty"><?php echo $qty; ?></span>
    </div>
  <?php endforeach; ?>
</div>
<?php
$content_html = ob_get_clean();

$active_slug  = 'basket';
$page_title   = 'Корзина';

require locate_template( 'inc/account/base-account-template.php' );
