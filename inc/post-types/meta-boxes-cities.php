<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Метабокс для координат городов
 * 
 * Регистрация метабокса координат
 * 
 * Добавляет метабокс на страницу редактирования CPT 'city'
 * Использует WordPress API для создания метабоксов
 * 
 */
if ( ! function_exists( 'add_city_coordinates_meta_box' ) ) {
    function add_city_coordinates_meta_box() {
        add_meta_box(
            'city_coordinates', // ID
            __('City Coordinates', 'storefront-child'), // Заголовок
            'render_coordinates_meta_box', // Callback
            'city', // Тип записи
            'normal', // Контекст
            'default' // Приоритет
        );
    }
}
add_action('add_meta_boxes_city', 'add_city_coordinates_meta_box'); // Уточнение типа записи

/**
 * Вывод содержимого метабокса
 * 
 * @param WP_Post $post Текущий объект поста
 */
if ( ! function_exists( 'render_coordinates_meta_box' ) ) {
    function render_coordinates_meta_box($post) {
        // Добавление nonce-поля для безопасности 
        wp_nonce_field('save_city_coordinates', 'city_coordinates_nonce');
        
        $latitude = get_post_meta($post->ID, '_latitude', true);
        $longitude = get_post_meta($post->ID, '_longitude', true);
        ?>
        <p>
            <label for="latitude"><?php esc_html_e('Latitude', 'storefront-child'); ?></label>
            <input 
                type="text" 
                id="latitude" 
                name="latitude" 
                value="<?php echo esc_attr($latitude); ?>" 
                class="regular-text"
            >
        </p>
        <p>
            <label for="longitude"><?php esc_html_e('Longitude', 'storefront-child'); ?></label>
            <input 
                type="text" 
                id="longitude" 
                name="longitude" 
                value="<?php echo esc_attr($longitude); ?>" 
                class="regular-text"
            >
        </p>
        <?php
    }
}

/**
 * Сохранение данных метабокса
 * 
 * @param int $post_id ID поста
 */
if ( ! function_exists( 'save_city_coordinates' ) ) {
    function save_city_coordinates($post_id) {
        // Проверка nonce
        if (!isset($_POST['city_coordinates_nonce']) || 
            !wp_verify_nonce($_POST['city_coordinates_nonce'], 'save_city_coordinates')) {
            return;
        }

        // Проверка автосохранения
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Проверка прав доступа
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Сохранение данных с очисткой
        if (isset($_POST['latitude'])) {
            update_post_meta(
                $post_id,
                '_latitude',
                sanitize_text_field($_POST['latitude'])
            );
        }

        if (isset($_POST['longitude'])) {
            update_post_meta(
                $post_id,
                '_longitude',
                sanitize_text_field($_POST['longitude'])
            );
        }
    }
}
add_action('save_post_city', 'save_city_coordinates');