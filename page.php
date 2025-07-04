<?php
/**
 * page.php
 * Стандартный шаблон для всех страниц
 */
get_header();

// Хлебные крошки
mytheme_breadcrumbs();
?>

<div class="page-wrapper"><!-- Начало обёртки, чтобы контент был сужен -->

  <div id="primary" class="content-area">
    <main id="main" class="site-main">
      <?php
      while ( have_posts() ) :
        the_post(); 
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
          <h1 class="entry-title"><?php the_title(); ?></h1>
          <div class="entry-content">
            <?php
              the_content();
              wp_link_pages([
                'before' => '<div class="page-links">',
                'after'  => '</div>',
              ]);
            ?>
          </div>
        </article>
      <?php
      endwhile;
      ?>
    </main><!-- #main -->
  </div><!-- #primary -->

</div><!-- .page-wrapper, конец обёртки -->

<?php get_footer(); ?>
