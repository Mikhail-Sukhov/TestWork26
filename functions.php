<?php
/**
 * Подключение стилей родительской и дочерней тем.
 * Использует хук wp_enqueue_scripts для регистрации и подключения CSS-файлов.
 *
 */
if ( ! function_exists( 'enqueue_child_theme_styles' ) ) {
    function enqueue_child_theme_styles() {
        // Регистрация и подключение стиля родительской темы
        wp_enqueue_style(
            'parent-style',
            get_template_directory_uri() . '/style.css',
            array(),
            wp_get_theme()->get('Version')
        );

        // Подключение стиля дочерней темы с зависимостью от родительского стиля
        wp_enqueue_style(
            'child-style',
            get_stylesheet_directory_uri() . '/style.css',
            array('parent-style'),
            wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_child_theme_styles');

/**
 * Загрузка текстового домена для перевода дочерней темы.
 * Использует хук after_setup_theme для инициализации локализации.
 *
 */
if ( ! function_exists( 'storefront_child_load_textdomain' ) ) {
    function storefront_child_load_textdomain() {
        load_child_theme_textdomain(
            'storefront-child',
            get_stylesheet_directory() . '/languages'
        );
    }
}
add_action('after_setup_theme', 'storefront_child_load_textdomain');

/**
 * Регистрация AJAX-скрипта и локализация параметров.
 * Использует хук after_weather_table для подключения скрипта.
 *
 */
if ( ! function_exists( 'register_ajax_scripts' ) ) {
    function register_ajax_scripts() {
        wp_enqueue_script(
            'weather-table-ajax',
            get_stylesheet_directory_uri() . '/assets/js/page-weather-table.js',
            array('jquery'),
            filemtime(get_stylesheet_directory() . '/assets/js/page-weather-table.js'),
            true
        );

        // Локализация данных для AJAX-запросов
        wp_localize_script(
            'weather-table-ajax',
            'weatherTable',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('city_search_nonce'),
                'loadingText'     => __('Loading...', 'storefront-child'),
                'noResultsText'   => __('No results found', 'storefront-child'),
                'genericErrorText'=> __('An error occurred', 'storefront-child')
            )
        );
    }
}
add_action('after_weather_table', 'register_ajax_scripts');

// Подключение функциональных модулей дочерней темы
require_once get_stylesheet_directory() . '/inc/admin/weather-api-settings.php';    // Настройки API погоды
require_once get_stylesheet_directory() . '/inc/ajax/weather-handlers.php';         // Обработчики AJAX-запросов
require_once get_stylesheet_directory() . '/inc/helpers/get-city-temperature.php';  // функция получения погоды по координатам
require_once get_stylesheet_directory() . '/inc/post-types/cities.php';             // Пользовательский тип записи "Города"
require_once get_stylesheet_directory() . '/inc/post-types/meta-boxes-cities.php';  // Метабоксы для городов
require_once get_stylesheet_directory() . '/inc/widget/weather-cities.php';         // Виджет погоды