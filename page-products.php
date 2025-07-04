<?php
/**
 * Template Name: Products Page
 * Обычный каталог товаров
 * @package my-custom-theme
 */

get_header();
mytheme_breadcrumbs();

// 1) Секция из GET
$section = isset( $_GET['section'] )
  ? sanitize_key( wp_unslash( $_GET['section'] ) )
  : 'dashboard';

?>
<div class="page-wrapper account-wrapper">
  <!-- sidebar -->
  <aside class="account-sidebar">
    <h3 class="catalog-title"><?php esc_html_e( 'PRODUCT', 'my-custom-theme' ); ?></h3>
    <?php
    $menu = [
      'dashboard' => ['dashboard.svg','Dashboard'],
      'audio'     => ['audio.svg','AUDIO'],
      'lighting'  => ['lighting.svg','LIGHTING'],
      'visual'    => ['visual.svg','VISUAL'],
      'rigging'   => ['rigging.svg','RIGGING – POWER DISTRIBUTION'],
      'staging'   => ['staging.svg','STAGING – TRUSSING'],
    ];
    ?>
    <nav class="account-menu" aria-label="<?php esc_attr_e( 'Catalog Menu', 'my-custom-theme' ); ?>">
      <ul>
        <?php foreach ( $menu as $slug_key => $item ) : ?>
          <li class="<?php echo $slug_key === $section ? 'is-active' : ''; ?>">
            <a href="<?php echo esc_url( add_query_arg( 'section', $slug_key, get_permalink() ) ); ?>">
              <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/icons/' . $item[0] ); ?>" alt="">
              <?php echo esc_html( $item[1] ); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  </aside>

  <!-- main content -->
  <main class="account-content">

  <?php if ( 'dashboard' === $section ) :

    // --- Dashboard: поиск + фильтр + список всех товаров ---
    $current_orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ?? 'date' ) );
    $current_per     = intval( wp_unslash( $_GET['per_page'] ?? 10 ) );
    $paged           = max( 1, get_query_var( 'paged', 1 ) );
    $order           = ( 'date' === $current_orderby ) ? 'DESC' : 'ASC';

    // Поиск
    ?>
    <h1 class="subcategory-title"><?php esc_html_e( 'All Listings', 'my-custom-theme' ); ?></h1>

    <div class="filter-box">
      <!-- 1) Строка поиска -->
      <div class="filter-search">
        <label for="product-search-input"><?php esc_html_e( 'Search', 'my-custom-theme' ); ?></label>
        <input type="text"
               id="product-search-input"
               placeholder="<?php esc_attr_e( 'Search by title...', 'my-custom-theme' ); ?>">
        <div id="search-suggestions" class="search-suggestions"></div>
      </div>

      <!-- 2) Форма фильтра -->
      <form class="subcategory-filter" method="get">
        <input type="hidden" name="section" value="dashboard">

        <div class="filter-grid">
          <label>
            <?php esc_html_e( 'Sort by', 'my-custom-theme' ); ?>
            <select name="orderby">
              <option value="date" <?php selected( $current_orderby, 'date' ); ?>><?php esc_html_e( 'Date Descending', 'my-custom-theme' ); ?></option>
              <option value="title"<?php selected( $current_orderby, 'title' ); ?>><?php esc_html_e( 'Title', 'my-custom-theme' ); ?></option>
            </select>
          </label>
          <label>
            <?php esc_html_e( 'Ads per Page', 'my-custom-theme' ); ?>
            <select name="per_page">
              <?php foreach ( [10,20,40,80] as $n ) : ?>
                <option value="<?php echo $n; ?>" <?php selected( $current_per, $n ); ?>><?php echo $n; ?></option>
              <?php endforeach; ?>
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
    </div><!-- /.filter-box -->

    <?php
    // Запрос всех товаров
    $args = [
      'post_type'      => 'product',
      'posts_per_page' => $current_per,
      'orderby'        => $current_orderby,
      'order'          => $order,
      'paged'          => $paged,
    ];
    if ( isset( $_GET['used_only'] ) ) {
      $args['meta_query'][] = [
        'key'     => '_condition',
        'value'   => 'used',
        'compare' => '=',
      ];
    }

    $products = new WP_Query( $args );
    if ( $products->have_posts() ) : ?>
      <ul class="products-list">
        <?php while ( $products->have_posts() ) : $products->the_post(); ?>
          <?php wc_get_template_part( 'content', 'product' ); ?>
        <?php endwhile; ?>
      </ul>
      <div class="products-pagination">
        <?php the_posts_pagination( [ 'mid_size'=>1, 'prev_text'=>'«', 'next_text'=>'»' ] ); ?>
      </div>
    <?php else : ?>
      <p><?php esc_html_e( 'No listings found.', 'my-custom-theme' ); ?></p>
    <?php endif;
    wp_reset_postdata();

  else :

    // --- Категории / подкатегории ---
    $term = get_term_by( 'slug', $section, 'product_cat' );
    if ( $term && ! is_wp_error( $term ) ) :
      $children = get_terms( [
        'taxonomy'   => 'product_cat',
        'parent'     => $term->term_id,
        'hide_empty' => false,
      ] );

      if ( ! empty( $children ) ) : ?>
        <h1 class="subcategory-title"><?php echo esc_html( $term->name ); ?></h1>
        <div class="catalog-grid">
          <?php foreach ( $children as $child ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'section', $child->slug, get_permalink() ) ); ?>"
               class="catalog-item">
              <?php echo esc_html( $child->name ); ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php
      else :
        // Последний уровень: подкатегорий нет → показываем товары с фильтром, как в Dashboard
        // (можно скопировать блок фильтрации Dashboard и добавить tax_query)
        echo '<p>' . esc_html__( 'В этой категории ещё нет подкатегорий.', 'my-custom-theme' ) . '</p>';
      endif;

    else :
      echo '<p>' . esc_html__( 'Секция не указана или категория не найдена.', 'my-custom-theme' ) . '</p>';
    endif;

  endif; // if dashboard / else ?>

  </main>
</div>

<?php get_footer(); ?>
