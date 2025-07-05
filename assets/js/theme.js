// assets/js/theme.js
// Главный скрипт темы

(function() {
  document.addEventListener('DOMContentLoaded', function() {

    // 1. Галерея товара — переключение главного изображения
    const mainImg = document.querySelector('.product-main-img');
    const thumbs  = document.querySelectorAll('.product-thumb-img');
    if ( mainImg && thumbs.length ) {
      thumbs.forEach(thumb => {
        thumb.addEventListener('click', function() {
          mainImg.src = this.dataset.full;
          thumbs.forEach(t => t.classList.remove('active'));
          this.classList.add('active');
        });
      });
    }

    // 2. Инициализация каруселей с помощью Splide.js
    const splides = document.querySelectorAll('.carousel-splide');
    if ( splides.length && typeof Splide !== 'undefined' ) {
      splides.forEach(el => {
        new Splide(el, {
          type       : 'loop',
          perPage    : 4,
          gap        : '1rem',
          arrows     : true,
          pagination : true,
          breakpoints: {
            1024: { perPage: 3 },
             768: { perPage: 2 },
             480: { perPage: 1 },
          },
        }).mount();
      });
    }

    // 3. Live-поиск по товарам
    const searchInput    = document.getElementById('product-search-input');
    const suggestionsBox = document.getElementById('search-suggestions');
    if ( searchInput && suggestionsBox ) {
      let timer;
      searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        const term = searchInput.value.trim();
        if ( ! term ) {
          suggestionsBox.innerHTML = '';
          return;
        }
        timer = setTimeout(() => {
          fetch(
            `${window.location.origin}/wp-json/wp/v2/product?search=${encodeURIComponent(term)}&per_page=5`
          )
          .then(res => res.json())
          .then(data => {
            suggestionsBox.innerHTML = data
              .map(item =>
                `<div class="suggestion-item" data-url="${item.link}">${item.title.rendered}</div>`
              )
              .join('');
          });
        }, 300);
      });
      suggestionsBox.addEventListener('click', e => {
        const item = e.target.closest('.suggestion-item');
        if ( item ) window.location.href = item.dataset.url;
      });
      document.addEventListener('click', e => {
        if ( ! searchInput.contains(e.target) && ! suggestionsBox.contains(e.target) ) {
          suggestionsBox.innerHTML = '';
        }
      });
    }

    // 4. Редактор объявлений: превью и удаление фото
// assets/js/theme.js
// assets/js/theme.js
(function(){
  document.addEventListener('DOMContentLoaded', function(){
    const fileInput       = document.getElementById('ad-images-input');
    const previewsWrapper = document.querySelector('.image-previews');
    const removeContainer = document.getElementById('remove-images-container');
      // —————— autoAnimate ——————
  if ( typeof autoAnimate === 'function' && previewsWrapper ) {
    autoAnimate(previewsWrapper);
  }
  // ————— стандартный код далее —————


    // Если превью-блоков нет — ни о чём не думаем
    if (!fileInput || !removeContainer) return;

    // Делегируем клик по кнопкам удаления во всём документе
    document.addEventListener('click', function(e){
      // ищем именно кнопку внутри .image-thumb
      const btn   = e.target.closest('.image-thumb button');
      const thumb = btn && btn.closest('.image-thumb');
      if (!thumb) return;

      const isNew = thumb.classList.contains('new-thumb');
      const id    = thumb.dataset.id;

      if (!isNew) {
        // отмечаем на удаление
        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = 'remove_images[]';
        hidden.value = id;
        removeContainer.appendChild(hidden);
      }
      thumb.remove();
    });

    // Рендер новых превью
    if (previewsWrapper) {
      fileInput.addEventListener('change', function(){
        Array.from(this.files).forEach(file => {
          const reader = new FileReader();
          reader.onload = e => {
            const wrapper = document.createElement('div');
            wrapper.className = 'image-thumb new-thumb';
            wrapper.dataset.id = 'new-' + Date.now() + '-' +
                                 Math.random().toString(36).substr(2,5);
            wrapper.innerHTML = `
              <img src="${e.target.result}" alt="${file.name}">
              <button type="button" title="Удалить">×</button>
            `;
            previewsWrapper.appendChild(wrapper);
          };
          reader.readAsDataURL(file);
        });
      });
    }
  });
})();
jQuery(document).ready(function($) {
$(document).on('click', '.order-delete', function(e) {
        e.preventDefault();

        var orderId = $(this).data('order-id'); // Получаем ID заказа из кнопки
        var nonce = window.MyThemeOrder.delete_nonce; // Получаем nonce для безопасности

        $.ajax({
            url: window.MyThemeOrder.ajax_url,  // URL для отправки запроса
            type: 'POST',
            data: {
                action: 'mytheme_delete_order',  // Экшен для обработчика в WordPress
                order_id: orderId,
                nonce: nonce  // Отправляем nonce для безопасности
            },
            success: function(response) {
                if (response.success) {
                    alert('Заказ был удален');
                    location.reload(); // Перезагружаем страницу для отображения изменений
                } else {
                    alert('Ошибка: ' + response.data);  // Показываем ошибку, если удаление не удалось
                }
            }
        });
    });

    // ---------- Подтверждение удаления объявления ----------
    const modal   = document.getElementById('ad-delete-modal');
    if (modal) {
        const yesBtn = modal.querySelector('.confirm-yes');
        const noBtn  = modal.querySelector('.confirm-no');
        let action = null;

        document.addEventListener('click', function(ev){
            const link = ev.target.closest('.ads-delete');
            const btn  = ev.target.closest('.ad-delete-btn');
            if (!link && !btn) return;
            ev.preventDefault();

            if (link) {
                const href = link.getAttribute('href');
                action = function(){ window.location.href = href; };
            }

            if (btn) {
                const form = btn.closest('form');
                action = function(){ form.submit(); };
            }

            modal.classList.remove('hidden');
        });

        yesBtn.addEventListener('click', function(){
            modal.classList.add('hidden');
            if (typeof action === 'function') action();
            action = null;
        });

        noBtn.addEventListener('click', function(){
            modal.classList.add('hidden');
            action = null;
        });
    }
});
