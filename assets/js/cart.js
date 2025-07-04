/**
 * File: assets/js/cart.js
 * Description: AJAX removal of cart items via custom “Удалить позицию” button.
 */

;(function($){
    'use strict';

    $(document).ready(function(){

        // При клике на кнопку удаления позиции
        $('.remove-item-btn').on('click', function(e){
            e.preventDefault();

            var $row    = $(this).closest('tr.cart_item'),
                cartKey = $row.data('cart_item_key');

            if ( ! cartKey ) {
                return;
            }

            // Отправляем AJAX-запрос на удаление позиции
            $.post(
                wc_cart_params.wc_ajax_url.replace('%%endpoint%%', 'remove_cart_item'),
                { cart_item_key: cartKey },
                function(response) {
                    // Если вернулись фрагменты, обновляем их
                    if ( response && response.fragments ) {
                        $.each(response.fragments, function(selector, html){
                            $(selector).replaceWith(html);
                        });
                    }
                    // Убираем строку из таблицы
                    $row.fadeOut(200, function(){
                        $(this).remove();
                    });
                }
            );
        });

    });
})(jQuery);
