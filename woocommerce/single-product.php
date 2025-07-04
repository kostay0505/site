<?php
/**
 * Single Product template – my-custom-theme  (версия 2025‑05‑22)
 * ----------------------------------------------------------------
 * Это доработанный вариант вашего исходного `single-product.php`.
 * Главное отличие:
 *   — вместо вызова `wc_get_template_part( 'content', 'single-product' );`
 *     мы сразу выводим галерею + summary внутри этого же шаблона.
 *   — сохранены все родные хуки WooCommerce, так что zoom / slider /
 *     lightbox и сторонние плагины работают «из коробки».
 *   — разметка построена на flex‑секции `.product__wrap`, чтобы легко
 *     управлять расстояниями между колонками.
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

do_action( 'woocommerce_before_main_content' );

while ( have_posts() ) :
    the_post();

    do_action( 'woocommerce_before_single_product' );

    if ( post_password_required() ) {
        echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        return;
    }
    ?>

    <section id="product-<?php the_ID(); ?>" <?php wc_product_class( 'product__wrap container', $product ); ?>>

        <?php
        /* ================== ГАЛЕРЕЯ Woo ================== */
        do_action( 'woocommerce_before_single_product_summary' );
        ?>

        <div class="summary entry-summary" data-sticky-container>
            <div class="summary__inner" data-sticky="top:100; bottom:50;">

                <?php
                /* ======= Заголовок, цена, кнопка «Купить» и пр. ======= */
                do_action( 'woocommerce_single_product_summary' );
                ?>

            </div><!-- /.summary__inner -->
        </div><!-- /.summary -->

        <?php
        /* =================== Табы, похожие товары =================== */
        do_action( 'woocommerce_after_single_product_summary' );
        ?>

        <?php if ( $brand_logo = get_field( 'brand_logo' ) ) : ?>
            <figure class="brand-logo"><?php echo wp_get_attachment_image( $brand_logo, 'full' ); ?></figure>
        <?php endif; ?>

    </section><!-- /.product__wrap -->

    <?php
    do_action( 'woocommerce_after_single_product' );

endwhile; // End of the loop.

do_action( 'woocommerce_after_main_content' );

get_footer();
