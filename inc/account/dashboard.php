<?php
/**
 * Часть шаблона: Dashboard (Основная информация + Подписки)
 * Подключается из page-account.php
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
?>

<!-- --- Секция «Основное» --- -->
<section class="account-section account-dashboard">
  <h2>Основное</h2>
  <div class="dash-cards">
    <a class="dash-card" href="#">
      <span class="number">4</span>
      <span class="label">Заказы</span>
    </a>
    <a class="dash-card" href="#">
      <span class="number">3</span>
      <span class="label">Избранное</span>
    </a>
    <a class="dash-card" href="#">
      <span class="number">1</span>
      <span class="label">Объявления</span>
    </a>
    <a class="dash-card" href="#">
      <span class="number">—</span>
      <span class="label">Личные данные</span>
    </a>
    <a class="dash-card" href="#">
      <span class="number">—</span>
      <span class="label">Платёжные реквизиты</span>
    </a>
  </div>
</section>

<!-- --- Секция «Подписки» --- -->
<section class="account-section account-subscriptions">
  <h2>Подписки</h2>
  <div class="subs-cards">
    <div class="subs-card">
      <p>Подписка на новости по email</p>
      <button class="btn">Подписаться</button>
    </div>
    <div class="subs-card">
      <p>Подписка на новости в telegram</p>
      <button class="btn">Отписаться</button>
    </div>
  </div>
</section>
