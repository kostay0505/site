<?php
/**
 * Универсальный вывод товаров любой подкатегории WooCommerce
 * Подключается из page-products.php через get_template_part
 * Получает переменные:
 *   $term    – объект терма product_cat
 *   $section – его slug
 * @package my-custom-theme
 */

if ( ! isset( $term ) || is_wp_error( $term ) ) {
    return;
}

// Параметры фильтрации
$current_orderby = sanitize_text_field( wp_unslash( $_GET['orderby']  ?? 'date' ) );
$current_per     = intval(           wp_unslash( $_GET['per_page'] ?? 10   ) );
$paged           = max( 1, get_query_var( 'paged', 1 ) );
$order           = ( 'date' === $current_orderby ) ? 'DESC' : 'ASC';

// Запрос товаров
$args = array(
    'post_type'      => 'product',
    'posts_per_page' => $current_per,
    'orderby'        => $current_orderby,
    'order'          => $order,
    'paged'          => $paged,
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
);
if ( isset( $_GET['used_only'] ) ) {
    $args['meta_query'][] = array(
        'key'     => '_condition',
        'value'   => 'used',
        'compare' => '=',
    );
}
$products = new WP_Query( $args );
?>

<h1 class="subcategory-title"><?php echo esc_html( $term->name ); ?></h1>

<!-- Форма фильтра -->
<div class="filter-box">
  <form class="subcategory-filter" method="get">
    <input type="hidden" name="section" value="<?php echo esc_attr( $section ); ?>">

    <!-- Поисковая строка -->
    <div class="filter-search">
      <label for="product-search-input"><?php esc_html_e( 'Search', 'my-custom-theme' ); ?></label>
      <input type="text" id="product-search-input" placeholder="<?php esc_attr_e( 'Search by title...', 'my-custom-theme' ); ?>">
      <div id="search-suggestions" class="search-suggestions"></div>
    </div>

    <div class="filter-grid">
      <label>
        <?php esc_html_e( 'Sort by', 'my-custom-theme' ); ?>
        <select name="orderby">
          <option value="date" <?php selected( $current_orderby, 'date' ); ?>><?php esc_html_e( 'Date Descending', 'my-custom-theme' ); ?></option>
          <option value="title" <?php selected( $current_orderby, 'title' ); ?>><?php esc_html_e( 'Title',            'my-custom-theme' ); ?></option>
        </select>
      </label>
      <label>
        <?php esc_html_e( 'Ads per Page', 'my-custom-theme' ); ?>
        <select name="per_page">
          <?php foreach ( array( 10, 20, 40, 80 ) as $n ) : ?>
            <?php if ( $n <= $products->found_posts ) : ?>
              <option value="<?php echo esc_attr( $n ); ?>" <?php selected( $current_per, $n ); ?>><?php echo esc_html( $n ); ?></option>
            <?php endif; endforeach; ?>
        </select>
      </label>
    </div>

    <div class="filter-actions">
      <label class="filter-checkbox">
        <input type="checkbox" name="used_only" <?php checked( isset( $_GET['used_only'] ) ); ?>>
        <?php esc_html_e( 'Show only used items', 'my-custom-theme' ); ?>
      </label>
      <button type="submit" class="button"><?php esc_html_e( 'Filter', 'my-custom-theme' ); ?></button>
    </div>
  </form>
</div>

<?php if ( $products->have_posts() ) : ?>
  <ul class="products-list">
    <?php while ( $products->have_posts() ) : $products->the_post(); ?>
      <?php wc_get_template_part( 'content', 'product' ); ?>
    <?php endwhile; ?>
  </ul>
  <?php wp_reset_postdata(); ?>

  <div class="products-pagination">
    <?php the_posts_pagination( array(
      'mid_size'  => 1,
      'prev_text' => '«',
      'next_text' => '»',
    ) ); ?>
  </div>

  <?php if ( $products->found_posts > 0 ) : ?>
    <div class="bottom-switcher">
      <span><?php esc_html_e( 'Show per page:', 'my-custom-theme' ); ?></span>
      <?php foreach ( array( 10, 20, 40, 80 ) as $n ) : if ( $n <= $products->found_posts ) : ?>
        <?php
        $query      = wp_parse_args( $_SERVER['QUERY_STRING'] );
        $query['per_page'] = $n;
        $query['paged']    = 1;
        $url        = esc_url( add_query_arg( $query, get_permalink() ) );
        $active     = ( $current_per === $n ) ? 'active' : '';
        ?>
        <a href="<?php echo $url; ?>" class="<?php echo esc_attr( $active ); ?>"><?php echo esc_html( $n ); ?></a>
      <?php endif; endforeach; ?>
    </div>
  <?php endif; ?>

<?php else : ?>
  <p><?php esc_html_e( 'В этой категории товаров нет.', 'my-custom-theme' ); ?></p>
<?php endif; ?>
