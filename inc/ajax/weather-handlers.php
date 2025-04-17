<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Обработчик AJAX-запроса для поиска городов
 *
 * Выполняет поиск городов по названию с использованием SQL-запроса.
 * Возвращает данные в формате JSON, включая температуру (при наличии координат).
 *
 * @uses $wpdb Для выполнения SQL-запросов
 * @uses check_ajax_referer() Для проверки безопасности запроса
 * @uses get_city_temperature() Для получения температурных данных
 * @return void Отправляет JSON-ответ через wp_send_json_success()/wp_send_json_error()
 */
if ( ! function_exists( 'handle_city_search' ) ) {
    function handle_city_search() {
        // Проверка подлинности AJAX-запроса через nonce
        if (!check_ajax_referer('city_search_nonce', 'nonce', false)) {
            wp_send_json_error(__('Invalid security token', 'storefront-child'), 403);
        }

        // Очистка входных данных от XSS-атак
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        global $wpdb;

        /**
         * SQL-запрос с JOIN таблиц таксономий для получения связанных стран
         *
         * Структура запроса:
         * 1. Основная таблица: wp_posts (города)
         * 2. Связь с терминами через term_relationships и term_taxonomy
         * 3. Подзапросы для получения координат из postmeta
         * 4. Группировка для объединения стран через GROUP_CONCAT
         */
        $sql = "
            SELECT 
                p.ID,
                p.post_title,
                (SELECT meta_value FROM {$wpdb->postmeta} 
                WHERE post_id = p.ID AND meta_key = '_latitude' LIMIT 1) AS latitude,
                (SELECT meta_value FROM {$wpdb->postmeta} 
                WHERE post_id = p.ID AND meta_key = '_longitude' LIMIT 1) AS longitude,
                GROUP_CONCAT(DISTINCT t.name) AS countries
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE 
                p.post_type = 'city' AND
                p.post_status = 'publish'
        ";

        // Динамическое добавление условия поиска с экранированием
        if (!empty($search)) {
            $like = '%' . $wpdb->esc_like($search) . '%';
            $sql .= $wpdb->prepare(" AND p.post_title LIKE %s", $like);
        }

        $sql .= " GROUP BY p.ID LIMIT 10";

        // Выполнение запроса с использованием WordPress Database API
        $results = $wpdb->get_results($sql);

        // Обработка результатов с валидацией геоданных
        $output = [];
        foreach ($results as $city) {
            // Значение по умолчанию при отсутствии координат
            $temperature = __('No data', 'storefront-child');
            
            // Получение температуры только при наличии валидных координат
            if (!empty($city->latitude) && !empty($city->longitude)) {
                $temperature = get_city_temperature(
                    $city->latitude, 
                    $city->longitude
                );
            }

            // Формирование структуры ответа с экранированием данных
            $output[] = [
                'id'         => (int) $city->ID,
                'title'      => esc_html($city->post_title),
                'countries'  => esc_html($city->countries),
                'temperature' => $temperature,
            ];
        }

        // Логирование SQL-ошибок для отладки
        if ($wpdb->last_error) {
            error_log('Weather search query error: ' . $wpdb->last_error);
        }

        // Отправка успешного ответа в формате JSON
        wp_send_json_success($output);
    }
}

// Регистрация обработчика для авторизованных и неавторизованных пользователей
add_action('wp_ajax_city_search', 'handle_city_search');
add_action('wp_ajax_nopriv_city_search', 'handle_city_search');