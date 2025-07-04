jQuery(function ($) {

  $('body').on('change', '.order-status-select', function () {

    const $sel     = $(this);
    const orderId  = $sel.data('order-id');
    const newStat  = $sel.val();

    $.post( OrderStatusVars.ajax_url, {
      action:     'mytheme_change_order_status',
      order_id:   orderId,
      new_status: newStat,
      nonce:      OrderStatusVars.nonce
    })
    .done( res => {
        if ( res.success ) {
          alert( OrderStatusVars.msg_saved || 'Сохранено' );
        } else {
          alert( res.data || 'Ошибка' );
        }
    })
    .fail(() => alert('AJAX error'));
  });

});
