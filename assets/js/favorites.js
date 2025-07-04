jQuery(function ($) {

  $('body').on('click', '.favorite-toggle', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn   = $(this);
    const postId = $btn.data('post-id');

    $.post( MyThemeFav.ajax_url, {
      action:  'toggle_favorite',              // ← важно
      post_id: postId,
      nonce:   MyThemeFav.favorite_nonce       // ← передаём именно nonce
    } ).done( res => {

  if ( res.success ) {
    const added = (res.data.action === 'added');
    $btn.toggleClass('is-favorited', added);

    if ( !added ) {
      // если удалён из избранного — прячем карточку плавно
      $btn.closest('.fav-card').fadeOut(300, function(){ $(this).remove(); });
    }

  } else if ( res.data && res.data.redirect ) {
    window.location = res.data.redirect;
  }

})

      if ( res.success ) {
        $btn.toggleClass( 'is-favorited', res.data.action === 'added' );
      } else if ( res.data && res.data.redirect ) {
        window.location = res.data.redirect;
      } else {
        console.warn( res );                   // чтобы увидеть текст ошибки
      }

    } ).fail( () => console.warn( 'AJAX error' ) );

    return false;
  });

});
