/**
 * assets/js/orders-accordion.js
 * Открытие/закрытие деталей заказа и AJAX-удаление заказа в Личном кабинете
 */

jQuery(function($) {

  //
  // 1. Accordion: скрываем все .details-box при загрузке
  //
  $('.order-details-row .details-box').hide();

  //
  // 2. Клик по кнопке «Подробнее» → раскрываем/скрываем соседнюю строку с деталями
  //
  $('body').on('click', '.order-toggle', function(e) {
    e.preventDefault();

    var $btn       = $(this);
    var $orderRow  = $btn.closest('tr.order-row');
    var $detailTR  = $orderRow.next('tr.order-details-row');
    var $box       = $detailTR.find('.details-box');

    // если нет элемента деталей — выходим
    if ( !$detailTR.length || !$box.length ) {
      return;
    }

    // 2.1. Скрываем все прочие открытые детали
    $('.order-row.is-open').not($orderRow).each(function() {
      $(this).removeClass('is-open');
      $(this).next('tr.order-details-row').find('.details-box').slideUp(200);
    });

    // 2.2. Переключаем для текущей кнопки/строки
    var willOpen = !$orderRow.hasClass('is-open');
    $orderRow.toggleClass('is-open', willOpen);
    $box.stop(true, true).slideToggle(300);
  });

  //
  // 3. AJAX-удаление заказа по кнопке «Удалить заказ»
  //
  $('body').on('click', '.order-delete', function(e) {
    e.preventDefault();

    if ( ! confirm( 'Вы уверены, что хотите удалить этот заказ?' ) ) {
      return;
    }

    var $btn     = $(this);
    // находим предыдущую строку заказа
    var $row     = $btn.closest('tr.order-details-row').prev('tr.order-row');
    var target   = $row.data('target');            // строка вида "order-1234"
    var order_id = parseInt( target.replace('order-', ''), 10 );

    $.post( MyThemeOrder.ajax_url, {
      action    : 'mytheme_delete_order',
      order_id  : order_id,
      nonce     : MyThemeOrder.delete_nonce
    })
    .done(function(response) {
      if ( response.success ) {
        // удаляем обе строки: основную и подробности
        $row.next('tr.order-details-row').remove();
        $row.remove();
      } else {
        alert( 'Ошибка удаления: ' + ( response.data || 'unknown error' ) );
      }
    })
    .fail(function() {
      alert( 'Сбой AJAX-запроса при удалении заказа.' );
    });
  });

});
$('.order-delete').on('click', function() {
    if (!confirm('Удалить заказ?')) return;
    var order_id = $(this).data('order-id');
    $.post(MyThemeOrder.ajax_url, {
        action: 'mytheme_delete_order',
        order_id: order_id,
        nonce: MyThemeOrder.delete_nonce
    }, function(response) {
        if (response.success) {
            // Скрыть строку заказа
            $('tr[data-target="order-' + order_id + '"]').hide();
            $('#order-' + order_id).hide();
        } else {
            alert(response.data || 'Ошибка удаления');
        }
    });
});