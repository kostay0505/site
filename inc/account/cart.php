<?php
/**
 * Личный кабинет → Корзина
 * Отображается внутри page-account.php
 *
 * Здесь пример со статичными товарами. Замените на вывод WooCommerce cart
 * или собственного каталога.
 */
?>

<div class="basket-head">
  <h2>Корзина</h2>
  <a href="#" class="btn">Оформить заказ</a>
</div>

<div class="basket-grid">
  <?php
  $demo = [
    ['ARCS 2','10 000 €','10 кмпл.','/assets/images/demo1.jpg'],
    ['LA‑RAK','10 000 €','5 шт.','/assets/images/demo2.jpg'],
    ['SD12‑96','10 000 €','10 кмпл.','/assets/images/demo3.jpg'],
    ['ARCS 2','10 000 €','15 шт.','/assets/images/demo1.jpg'],
  ];
  foreach ( $demo as [$title,$price,$qty,$img] ) : ?>
    <div class="basket-item">
      <img src="<?php echo get_template_directory_uri() . $img; ?>" alt="">
      <h3><?php echo esc_html( $title ); ?></h3>
      <span class="price"><?php echo $price; ?></span>
      <span class="qty"><?php echo $qty; ?></span>
    </div>
  <?php endforeach; ?>
</div>
