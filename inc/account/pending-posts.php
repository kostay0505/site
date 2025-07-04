<?php
// Файл: inc/account/pending-posts.php
// Только для администратора
if ( ! current_user_can( 'administrator' ) ) {
  echo '<p>У вас нет прав на просмотр этой страницы.</p>';
  return;
}

// Запрос всех товаров со статусом pending
$pending = new WP_Query([
  'post_type'      => 'product',
  'post_status'    => 'pending',
  'posts_per_page' => -1,
]);

if ( ! $pending->have_posts() ) {
  echo '<p>Нет постов на утверждение.</p>';
  return;
}

// Один раз генерируем nonce для всех кнопок
$approve_nonce = wp_create_nonce( 'approve_post_nonce' );
?>

<h2>Посты на утверждении</h2>

<?php
while ( $pending->have_posts() ):
  $pending->the_post();
  $post_id   = get_the_ID();
  $author_id = get_post_field( 'post_author', $post_id );
?>
<article id="pending-<?php echo esc_attr( $post_id ); ?>" class="pending-item">
  <h3><?php the_title(); ?></h3>
  <p><strong>Автор:</strong> <?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></p>

  <?php if ( has_post_thumbnail( $post_id ) ) : ?>
    <div class="pending-thumbnail">
      <?php echo get_the_post_thumbnail( $post_id, 'medium' ); ?>
    </div>
  <?php endif; ?>

  <div class="pending-content">
    <?php the_content(); ?>
  </div>

  <ul class="pending-meta">
    <li><strong>Цена:</strong> <?php echo esc_html( get_post_meta( $post_id, '_price', true ) ); ?></li>
    <li><strong>SKU:</strong>   <?php echo esc_html( get_post_meta( $post_id, '_sku',   true ) ); ?></li>
    <li><strong>Категории:</strong>
      <?php echo esc_html( join( ', ', wp_get_post_terms( $post_id, 'product_cat', ['fields'=>'names'] ) ) ); ?>
    </li>
    <li><strong>Теги:</strong>
      <?php echo esc_html( join( ', ', wp_get_post_terms( $post_id, 'product_tag', ['fields'=>'names'] ) ) ); ?>
    </li>
    <?php
    // дополнительные мета-поля, если нужны
    $all_meta = get_post_meta( $post_id );
    foreach ( $all_meta as $key => $values ) {
      if ( in_array( $key, ['_price','_sku','_thumbnail_id','_edit_lock'], true ) ) {
        continue;
      }
      foreach ( $values as $value ) {
        echo '<li><strong>'. esc_html( $key ) .':</strong> '. esc_html( $value ) .'</li>';
      }
    }
    ?>
  </ul>

  <button
    class="approve-post-btn"
    data-post-id="<?php echo esc_attr( $post_id ); ?>"
    data-nonce="<?php echo esc_attr( $approve_nonce ); ?>"
  >Утвердить</button>
  <hr>
</article>

<?php
endwhile;
wp_reset_postdata();
?>

<script>
jQuery(function($){
  $('.approve-post-btn').on('click', function(){
    var btn   = $(this),
        id    = btn.data('post-id'),
        nonce = btn.data('nonce');

    if ( ! confirm('Вы уверены, что хотите утвердить этот пост?') ) {
      return;
    }

    btn.prop('disabled', true).text('Утверждаю…');

    $.post(
      '<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>',
      {
        action:      'approve_post',
        post_id:     id,
        _ajax_nonce: nonce
      },
      function(res){
        if ( res.success ) {
          $('#pending-' + id).fadeOut();
        } else {
          alert('Ошибка: ' + res.data);
          btn.prop('disabled', false).text('Утвердить');
        }
      },
      'json'
    );
  });
});
</script>
