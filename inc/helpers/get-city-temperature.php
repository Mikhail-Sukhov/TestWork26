<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Получает текущую температуру города по географическим координатам
 * 
 * Использует API OpenWeatherMap с кэшированием результатов. 
 * Поддерживает обработку ошибок и валидацию входных данных.
 * 
 * @since 1.0.0
 * @param float $latitude  Широта (от -90 до 90)
 * @param float $longitude Долгота (от -180 до 180)
 * @return string Форматированная температура (°C) или сообщение об ошибке
 * @uses get_option() Для получения API-ключа
 * @uses wp_remote_get() Для выполнения HTTP-запросов
 * @uses set_transient() Для кэширования результатов
 */
if ( ! function_exists( 'get_city_temperature' ) ) {
    function get_city_temperature($latitude, $longitude) {
        // Валидация координат по стандартам географических данных
        if (
            !is_numeric($latitude) || 
            !is_numeric($longitude) ||
            abs($latitude) > 90 ||
            abs($longitude) > 180
        ) {
            return __('No data', 'storefront-child');
        }

        // Получение API-ключа из настроек WordPress
        $api_key = get_option('openweather_api_key');
        if (empty($api_key)) {
            return __('API key not configured', 'storefront-child');
        }

        // Генерация уникального ключа кэша по алгоритму MD5
        $transient_key = 'weather_' . md5(sprintf('%f%f', $latitude, $longitude));
        
        // Проверка наличия актуальных данных в кэше
        $temperature = get_transient($transient_key);
        if (false !== $temperature) {
            return $temperature;
        }

        // Формирование URL-запроса с метрической системой единиц
        $api_url = add_query_arg([
            'lat'   => $latitude,
            'lon'   => $longitude,
            'units' => 'metric',
            'appid' => $api_key,
        ], 'https://api.openweathermap.org/data/2.5/weather');

        // Выполнение запроса через WordPress HTTP API
        $response = wp_remote_get($api_url);
        
        // Обработка сетевых ошибок и логирование
        if (is_wp_error($response)) {
            error_log(sprintf(
                'Weather API error: %s (%d)',
                $response->get_error_message(),
                $response->get_error_code()
            ));
            return __('API connection error', 'storefront-child');
        }

        // Парсинг JSON-ответа
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        // Извлечение температуры из структуры ответа
        if (isset($body['main']['temp'])) {
            $temperature = sprintf(
                '%d°C',
                round(floatval($body['main']['temp']))
            );
            
            // Кэширование на 15 минут для снижения нагрузки на API
            set_transient($transient_key, $temperature, 15 * MINUTE_IN_SECONDS);
        } else {
            $temperature = __('Weather data unavailable', 'storefront-child');
        }

        return $temperature;
    }
}