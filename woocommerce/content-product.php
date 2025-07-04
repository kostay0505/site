<?php
/**
 * The template for displaying product entries in a custom subcategory list
 * @package my-custom-theme
 */
defined( 'ABSPATH' ) || exit;
global $product;
?>

<li <?php wc_product_class( 'catalog-row', $product ); ?>>

  <!-- 1. Превью изображения -->
  <div class="catalog-row__thumb">
    <a href="<?php the_permalink(); ?>">
      <?php echo wp_get_attachment_image( $product->get_image_id(), 'thumbnail' ); ?>
    </a>
  </div>

  <!-- 2. Название и описание -->
  <div class="catalog-row__text">
    <a class="catalog-row__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
    <div class="catalog-row__desc">
      <?php echo wp_trim_words( get_the_excerpt(), 25, '&hellip;' ); ?>
    </div>
  </div>

  <!-- 3. Цена, дата, автор (в столбик) -->
  <div class="catalog-row__meta">
    <div class="catalog-row__price">
      <?php echo $product->get_price_html(); ?>
    </div>
    <div class="catalog-row__date">
      <?php echo get_the_date( 'd.m.Y' ); ?>
    </div>
    <div class="catalog-row__author">
      <?php _e('Автор', 'my-custom-theme'); ?>: <?php the_author(); ?>
    </div>
  </div>

</li>
