<?php
/**
 * inc/dashboard/favorites.php
 * Вывод списка избранного в ЛК
 */

// Заголовок раздела
echo '<h2>' . esc_html__( 'Избранное', 'my-custom-theme' ) . '</h2>';

// Если вы регистрировали шорткод [my_favorites_list]
echo do_shortcode( '[my_favorites_list]' );

// — либо, если хотите вызвать функцию напрямую:
