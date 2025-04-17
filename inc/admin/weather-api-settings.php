<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Регистрация настроек API погоды
 * 
 * Регистрация настроек и секции
 * Использует Settings API для интеграции с админ панелью
 * 
 */
if ( ! function_exists( 'register_weather_settings' ) ) {
    function register_weather_settings() {
        // Регистрация секции настроек
        add_settings_section(
            'weather_api_section',
            __('Weather API Settings', 'storefront-child'),
            '__return_empty_string',
            'general'
        );

        // Регистрация настройки с параметрами
        register_setting(
            'general',
            'openweather_api_key',
            [
                'type'              => 'string',
                'description'       => __('API key for OpenWeatherMap service', 'storefront-child'),
                'sanitize_callback' => 'sanitize_text_field',
                'show_in_rest'      => true,
                'default'           => '',
            ]
        );

        // Добавление поля ввода
        add_settings_field(
            'openweather_api_key',
            __('OpenWeatherMap API Key', 'storefront-child'),
            'render_api_key_field',
            'general',
            'weather_api_section'
        );
    }
}
add_action('admin_init', 'register_weather_settings');

/**
 * Вывод поля ввода API-ключа
 * 
 */
if ( ! function_exists( 'render_api_key_field' ) ) {
    function render_api_key_field() {
        $value = get_option('openweather_api_key', '');
        ?>
        <input 
            type="text" 
            name="openweather_api_key" 
            value="<?php echo esc_attr($value); ?>" 
            class="regular-text code"
            placeholder="<?php esc_attr_e('Enter API key', 'storefront-child'); ?>"
        >
        <p class="description">
            <?php 
            printf(
                /* translators: %s: ссылка на регистрацию API */
                __('Get your API key at <a href="%s" target="_blank">OpenWeatherMap</a>.', 'storefront-child'),
                'https://openweathermap.org/appid'
            ); 
            ?>
        </p>
        <?php
    }
}