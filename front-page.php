<?php
/**
 * Template Name: Front Page
 * Description: Главная страница с hero, 3-мя каруселями, секцией вопросов, брендами и хлебными крошками
 * @package my-custom-theme
 */

get_header();

// Хлебные крошки (если не на самой главной)
if ( function_exists( 'mytheme_breadcrumbs' ) ) {
    mytheme_breadcrumbs();
}
?>

<div class="front-page-wrapper">

  <!-- ================= HERO ================= -->
  <?php
    // …ваш код блока hero…
  ?>

  <!-- ================ HOT DEALS =============== -->
  <?php
  $hot_deals = new WP_Query( [
      'post_type'      => 'product',
      'posts_per_page' => 8,
      'meta_query'     => [
          [
              'key'   => 'hot_deal',
              'value' => '1',
          ],
      ],
  ] );
  if ( $hot_deals->have_posts() ) : ?>
    <section class="carousel-section hot-deals">
      <div class="container">
        <h2><?php esc_html_e( 'Hot Deals', 'my-custom-theme' ); ?></h2>

        <div id="hot-deals-splide" class="splide carousel-splide">
          <div class="splide__track">
            <ul class="splide__list">
              <?php while ( $hot_deals->have_posts() ) : $hot_deals->the_post(); global $product; ?>
                <li class="splide__slide">
                  <div class="carousel-item">

                    <div class="item-thumbnail">
                      <a href="<?php the_permalink(); ?>">
                        <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                      </a>

                      <?php
                        // избранное
                        $is_fav = false;
                        if ( is_user_logged_in() ) {
                            $favs   = (array) get_user_meta( get_current_user_id(), 'mytheme_favorites', true );
                            $is_fav = in_array( get_the_ID(), $favs, true );
                        }
                      ?>
                      <button type="button"
                              class="favorite-toggle<?php echo $is_fav ? ' is-favorited' : ''; ?>"
                              data-post-id="<?php the_ID(); ?>"
                              title="<?php echo $is_fav
                                  ? esc_attr__( 'Убрать из избранного', 'my-custom-theme' )
                                  : esc_attr__( 'Добавить в избранное', 'my-custom-theme' ); ?>">
                        <span class="icon-heart"></span>
                      </button>
                    </div>

                    <h3 class="product-title">
                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="price"><?php echo $product->get_price_html(); ?></div>

                  </div>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>

      </div>
    </section>
  <?php endif; ?>


  <!-- ============== MOST VIEWED ============== -->
  <?php
  $most_viewed = new WP_Query( [
      'post_type'      => 'product',
      'posts_per_page' => 8,
      'orderby'        => 'meta_value_num',
      'meta_key'       => 'total_sales',
      'order'          => 'DESC',
  ] );
  if ( $most_viewed->have_posts() ) : ?>
    <section class="carousel-section most-viewed">
      <div class="container">
        <h2><?php esc_html_e( 'Most Viewed', 'my-custom-theme' ); ?></h2>

        <div id="most-viewed-splide" class="splide carousel-splide">
          <div class="splide__track">
            <ul class="splide__list">
              <?php while ( $most_viewed->have_posts() ) : $most_viewed->the_post(); global $product; ?>
                <li class="splide__slide">
                  <div class="carousel-item">

                    <div class="item-thumbnail">
                      <a href="<?php the_permalink(); ?>">
                        <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                      </a>

                      <?php
                        $is_fav = false;
                        if ( is_user_logged_in() ) {
                            $favs   = (array) get_user_meta( get_current_user_id(), 'mytheme_favorites', true );
                            $is_fav = in_array( get_the_ID(), $favs, true );
                        }
                      ?>
                      <button type="button"
                              class="favorite-toggle<?php echo $is_fav ? ' is-favorited' : ''; ?>"
                              data-post-id="<?php the_ID(); ?>">
                        <span class="icon-heart"></span>
                      </button>
                    </div>

                    <h3 class="product-title">
                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="price"><?php echo $product->get_price_html(); ?></div>

                  </div>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>

      </div>
    </section>
  <?php endif; ?>


  <!-- ================ NEW IN ================= -->
  <?php
  $new_in = new WP_Query( [
      'post_type'      => 'product',
      'posts_per_page' => 8,
      'orderby'        => 'date',
      'order'          => 'DESC',
  ] );
  if ( $new_in->have_posts() ) : ?>
    <section class="carousel-section new-in">
      <div class="container">
        <h2><?php esc_html_e( 'New In', 'my-custom-theme' ); ?></h2>

        <div id="new-in-splide" class="splide carousel-splide">
          <div class="splide__track">
            <ul class="splide__list">
              <?php while ( $new_in->have_posts() ) : $new_in->the_post(); global $product; ?>
                <li class="splide__slide">
                  <div class="carousel-item">

                    <div class="item-thumbnail">
                      <a href="<?php the_permalink(); ?>">
                        <?php echo wp_get_attachment_image( $product->get_image_id(), 'medium' ); ?>
                      </a>

                      <?php
                        $is_fav = false;
                        if ( is_user_logged_in() ) {
                            $favs   = (array) get_user_meta( get_current_user_id(), 'mytheme_favorites', true );
                            $is_fav = in_array( get_the_ID(), $favs, true );
                        }
                      ?>
                      <button type="button"
                              class="favorite-toggle<?php echo $is_fav ? ' is-favorited' : ''; ?>"
                              data-post-id="<?php the_ID(); ?>">
                        <span class="icon-heart"></span>
                      </button>
                    </div>

                    <h3 class="product-title">
                      <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="price"><?php echo $product->get_price_html(); ?></div>

                  </div>
                </li>
              <?php endwhile; wp_reset_postdata(); ?>
            </ul>
          </div>
        </div>

      </div>
    </section>
  <?php endif; ?>


  <!-- ============== QUESTIONS =============== -->
  <?php
    // …ваш код секции вопросов…
  ?>

  <!-- ================ BRANDS ================ -->
  <?php
    // …ваш код карусели брендов…
  ?>

</div><!-- /.front-page-wrapper -->

<?php
get_footer();
