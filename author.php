<?php
/**
 * Author archive template — теперь работаем со стандартным циклом
 * Файл: wp-content/themes/my-custom-theme/author.php
 */

get_header();

// — 1. Данные автора —
$author = get_queried_object();
$uid    = $author->ID;

$company_name    = get_user_meta( $uid, 'company_name',    true ) ?: $author->display_name;
$logo_id         = get_user_meta( $uid, 'company_logo',    true );
$country         = get_user_meta( $uid, 'company_country', true );
$city            = get_user_meta( $uid, 'company_city',    true );
$address         = get_user_meta( $uid, 'company_address', true );
$phone           = get_user_meta( $uid, 'company_phone',   true );
$email           = $author->user_email;
$registered_date = date_i18n( 'd.m.Y', strtotime( $author->user_registered ) );

// — 2. То, что лежит в $_GET, WordPress сам подхватит в запросе,
//     так что дополнительного $search и $paged нам не нужно.
?>

<!-- ===== Шапка продавца ===== -->
<div class="seller-header">
  <?php if ( $logo_id ) : ?>
    <div class="seller-logo">
      <?php echo wp_get_attachment_image( $logo_id, 'medium' ); ?>
    </div>
  <?php endif; ?>

  <div class="seller-details">
    <h1 class="seller-title"><?php echo esc_html( $company_name ); ?></h1>
    <ul class="seller-meta">
      <?php if ( $country ) : ?><li><strong>Страна:</strong> <?php echo esc_html( $country ); ?></li><?php endif; ?>
      <?php if ( $city    ) : ?><li><strong>Город:</strong>   <?php echo esc_html( $city );    ?></li><?php endif; ?>
      <?php if ( $address ) : ?><li><strong>Адрес:</strong>   <?php echo esc_html( $address ); ?></li><?php endif; ?>
      <?php if ( $phone   ) : ?><li><strong>Телефон:</strong> <?php echo esc_html( $phone );   ?></li><?php endif; ?>
      <li>
        <strong>E-mail:</strong>
        <a href="mailto:<?php echo antispambot( $email ); ?>">
          <?php echo antispambot( $email ); ?>
        </a>
      </li>
      <li><strong>Зарегистрирован:</strong> <?php echo esc_html( $registered_date ); ?></li>
    </ul>
  </div>
</div>

<hr>

<!-- ===== Форма поиска ===== -->
<form class="seller-search" method="get" action="<?php echo esc_url( get_author_posts_url( $uid ) ); ?>">
  <input
    type="text"
    name="s"
    placeholder="Поиск объявлений"
    value="<?php echo get_search_query(); ?>"
  >
  <button type="submit">Найти</button>
</form>

<!-- ===== Список товаров автора (стандартный цикл) ===== -->
<?php if ( have_posts() ) : ?>
  <div class="seller-ads">
    <?php
    while ( have_posts() ) :
      the_post();
      // WooCommerce-шаблон карточки товара:
      wc_get_template_part( 'content', 'product' );

      // Если у вас свой шаблон карточки CPT, например:
      // get_template_part( 'template-parts/content', 'listing' );
    endwhile;
    ?>
  </div>

  <!-- ===== Стандартная пагинация ===== -->
  <nav class="seller-pagination">
    <?php
    the_posts_pagination( [
      'mid_size'  => 1,
      'prev_text' => '&laquo; Назад',
      'next_text' => 'Вперёд &raquo;',
    ] );
    ?>
  </nav>

<?php else : ?>
  <p>Объявлений не найдено.</p>
<?php endif; ?>

<?php
get_footer();
