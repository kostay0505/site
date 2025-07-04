<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 * @package WooCommerce\Templates
 * @version 8.6.0
 */
defined( 'ABSPATH' ) || exit;
get_header( 'shop' );
?>

<!-- Фильтры и поиск: полностью под твои стили -->
<div class="filter-box">
  <!-- Строка поиска -->
  <div class="filter-search">
    <label for="product-search-input">Поиск</label>
    <input type="text" id="product-search-input" placeholder="Поиск по названию..." name="search">
    <div id="search-suggestions" class="search-suggestions"></div>
  </div>
  <!-- Сетка фильтров -->
  <form class="catalog-filter" method="get">
    <div class="filter-grid">
      <!-- Категория -->
      <select name="category" class="filter-item">
        <option value="">Категория</option>
        <?php 
          $categories = get_terms( array(
            'taxonomy' => 'product_cat',
            'orderby'  => 'name',
            'order'    => 'ASC',
            'hide_empty' => false,
          ) );
          foreach ( $categories as $category ) {
            echo '<option value="' . esc_attr( $category->term_id ) . '">' . esc_html( $category->name ) . '</option>';
          }
        ?>
      </select>
      <!-- Бренд -->
      <select name="brand" class="filter-item">
        <option value="">Бренд</option>
        <?php 
          $brands = get_terms( array(
            'taxonomy' => 'pa_brand',
            'orderby'  => 'name',
            'order'    => 'ASC',
            'hide_empty' => false,
          ) );
          foreach ( $brands as $brand ) {
            echo '<option value="' . esc_attr( $brand->term_id ) . '">' . esc_html( $brand->name ) . '</option>';
          }
        ?>
      </select>
      <!-- Модель -->
      <select name="model" class="filter-item">
        <option value="">Модель</option>
        <!-- Подгрузить модели по ситуации -->
      </select>
      <!-- Цена -->
      <div class="price-range">
        <input type="number" name="min-price" class="filter-item" placeholder="Цена от">
        <input type="number" name="max-price" class="filter-item" placeholder="Цена до">
      </div>
      <!-- Сортировка -->
      <select name="orderby" class="filter-item">
        <option value="date">По дате</option>
        <option value="title">По названию</option>
        <option value="price">По цене</option>
      </select>
    </div>
    <div class="filter-actions">
      <label class="filter-checkbox">
        <input type="checkbox" name="used_only">
        Только б/у
      </label>
      <button type="submit" class="button">Применить</button>
    </div>
  </form>
</div>

<?php
// Основной контент WooCommerce
do_action( 'woocommerce_before_main_content' );

// Вывод подкатегорий (поддержка "кирпичиков")
$cat_id = is_product_category() ? get_queried_object_id() : 0;
$subcategories = get_terms( array(
    'taxonomy' => 'product_cat',
    'parent' => $cat_id,
    'hide_empty' => false,
) );
if ( ! empty( $subcategories ) ) : ?>
    <div class="subcategories-grid">
      <?php foreach ( $subcategories as $subcategory ) : ?>
        <a class="catalog-item" href="<?php echo esc_url( get_term_link( $subcategory ) ); ?>">
            <?php echo esc_html( $subcategory->name ); ?>
        </a>
      <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php
if ( woocommerce_product_loop() ) {

    do_action( 'woocommerce_before_shop_loop' );

    // UL-обёртка для товаров (для стилизации)
    echo '<ul class="products-list" style="list-style:none;padding:0;margin:0;">';

    if ( wc_get_loop_prop( 'total' ) ) {
        while ( have_posts() ) {
            the_post();
            do_action( 'woocommerce_shop_loop' );
            ?>
            <li class="product-item">
              <!-- 1. Превью изображения -->
              <div class="product-preview">
                <a href="<?php the_permalink(); ?>">
                  <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>" alt="<?php the_title_attribute(); ?>">
                </a>
              </div>

              <!-- 2. Название и описание -->
              <div class="product-details">
                <h2 class="product-title">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p class="product-description">
                  <?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
                </p>
              </div>

              <!-- 3. Цена, дата, автор — в столбик -->
              <div class="product-meta">
                <div class="product-price">
                  <?php
                  if (function_exists('wc_get_product')) {
                    $product = wc_get_product(get_the_ID());
                    if ($product) {
                      echo $product->get_price_html();
                    }
                  }
                  ?>
                </div>
                <div class="product-date">
                  <?php echo get_the_date( 'd.m.Y' ); ?>
                </div>
                <div class="product-author">
                  Автор: <?php the_author(); ?>
                </div>
              </div>
            </li>
            <?php
        }
    }

    echo '</ul>';

    do_action( 'woocommerce_after_shop_loop' );
} else {
    do_action( 'woocommerce_no_products_found' );
}

do_action( 'woocommerce_after_main_content' );
do_action( 'woocommerce_sidebar' );
get_footer( 'shop' );
?>
