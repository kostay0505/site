/* assets/js/ads-dashboard.js
 * Управляет модальным подтверждением удаления объявления
 * + отправляет AJAX-запрос на admin-ajax.php?action=delete_ad
 *
 * Требования:
 *   – В functions.php подключён wp_localize_script( 'ads-dashboard', 'mytheme', { ajax, nonce } )
 *   – Кнопка «Удалить» имеет классы .delete-ad и data-post-id="123"
 *   – Карточка / строка объявления обёрнута в .ad-row (чтобы убрать её из DOM)
 */

(function () {
  /** ===== DOM-элементы ===== */
  const modal   = document.getElementById('confirmDeleteModal');
  const btnYes  = document.getElementById('confirmDeleteYes');
  const btnNo   = document.getElementById('confirmDeleteNo');

  /** Текущая строка и ID поста */
  let targetRow = null;
  let postId    = 0;

  /** Показать модалку */
  function openModal(row, id) {
    targetRow = row;
    postId    = id;
    modal.classList.remove('hidden');
  }

  /** Закрыть модалку */
  function closeModal() {
    modal.classList.add('hidden');
    targetRow = null;
    postId    = 0;
  }

  /** Отправка AJAX-запроса */
  function deleteAd() {
    const body = new URLSearchParams({
      action:  'delete_ad',
      post_id: postId,
      nonce:   mytheme.nonce,      // передаём nonce, локализованный в PHP
    });

    fetch(mytheme.ajax, {
      method:      'POST',
      credentials: 'same-origin',
      headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:        body.toString(),
    })
      .then(r => r.json())
      .then(res => {
        if (res.success) {
          // убираем объявление из DOM и закрываем модалку
          targetRow?.remove();
          closeModal();
        } else {
          alert(res.data || 'Ошибка удаления');
        }
      })
      .catch(() => alert('Ошибка сети'));
  }

  /** Делегируем клик по «Удалить» во всём документе */
  document.addEventListener('click', (e) => {
    if (e.target.matches('.delete-ad')) {
      e.preventDefault();
      const row = e.target.closest('.ad-row');
      const id  = Number(e.target.dataset.postId);
      openModal(row, id);
    }
  });

  /** Слушатели кнопок модалки */
  btnYes.addEventListener('click', deleteAd);
  btnNo .addEventListener('click', closeModal);
})();
