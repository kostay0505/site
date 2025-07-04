<?php
/**
 * roles.php
 *
 * Добавляет кастомную роль "Пользователь" (user), 
 * которая может читать сайт, создавать и редактировать свои записи,
 * загружать файлы, но не публиковать их сразу.
 *
 * Подключается из functions.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Защита от прямого доступа
}

if ( ! function_exists( 'mytheme_add_custom_user_role' ) ) {

    /**
     * Регистрирует кастомную роль "user" при активации темы.
     */
    function mytheme_add_custom_user_role() {
        // Проверяем, что такой роли еще нет
        if ( ! get_role( 'user' ) ) {
            add_role(
                'user',            // Системное имя роли
                'Пользователь',    // Читаемое имя
                array(
                    'read'           => true,  // Может читать
                    'edit_posts'     => true,  // Может создавать и редактировать свои записи
                    'delete_posts'   => false, // Не может удалять записи
                    'publish_posts'  => false, // Не может сразу публиковать
                    'upload_files'   => true,  // Может загружать файлы
                )
            );
        }
    }

    // Хук: при активации/переключении темы — создаем роль
    add_action( 'after_switch_theme', 'mytheme_add_custom_user_role' );
}
