<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Виджет отображения погоды для городов
 *
 */
class Weather_Cities_Widget extends WP_Widget {

    /**
     * Регистрация виджета
     *
     */
    public function __construct() {
        parent::__construct(
            'weather_cities_widget', // Base ID
            __('Weather City Widget', 'storefront-child'), // Name
            [
                'description' => __('Displays weather for selected city', 'storefront-child'),
                'classname'   => 'widget-weather-cities',
            ],
            []
        );
    }

    /**
     * Вывод виджета на фронтенде
     *
     * @param array $args     Аргументы области виджетов
     * @param array $instance Настройки экземпляра виджета
     */
    public function widget($args, $instance) {
        $city_id = isset($instance['city_id']) ? absint($instance['city_id']) : 0;
        if (!$city_id) {
            return;
        }

        $city = get_post($city_id);
        if (!$city) {
            return;
        }

        $latitude = get_post_meta($city_id, '_latitude', true);
        $longitude = get_post_meta($city_id, '_longitude', true);
        $temperature = get_city_temperature($latitude, $longitude);

        echo $args['before_widget'];
        ?>
        <div class="weather-widget">
            <h3 class="widget-title"><?php echo esc_html($city->post_title); ?></h3>
            <p><?php 
                /* translators: %s: температура */
                echo esc_html(sprintf(
                    __('Temperature: %s', 'storefront-child'),
                    $temperature
                )); 
            ?></p>
        </div>
        <?php
        echo $args['after_widget'];
    }

    /**
     * Форма настроек виджета в админке
     *
     * @param array $instance Текущие настройки
     */
    public function form($instance) {
        $city_id = isset($instance['city_id']) ? absint($instance['city_id']) : 0;
        
        $cities = get_posts([
            'post_type'      => 'city',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('city_id')); ?>">
                <?php esc_html_e('Select City:', 'storefront-child'); ?>
            </label>
            <select 
                id="<?php echo esc_attr($this->get_field_id('city_id')); ?>"
                name="<?php echo esc_attr($this->get_field_name('city_id')); ?>"
                class="widefat"
            >
                <?php foreach ($cities as $city) : ?>
                    <option value="<?php echo esc_attr($city->ID); ?>" 
                        <?php selected($city_id, $city->ID); ?>>
                        <?php echo esc_html($city->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    /**
     * Обновление настроек виджета
     *
     * @param array $new_instance Новые настройки
     * @param array $old_instance Старые настройки
     * @return array Обновленные настройки
     */
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['city_id'] = absint($new_instance['city_id']);
        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('Weather_Cities_Widget');
});