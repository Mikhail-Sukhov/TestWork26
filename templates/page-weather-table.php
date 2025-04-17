<?php
/**
 * 
 * Template Name: Weather Table
 * 
 * Шаблон таблицы погоды
 * Отображает список городов с температурой. Поддерживает AJAX-поиск.
 * 
 */

get_header(); 

/**
 * Хук перед выводом таблицы погоды
 * 
 */
do_action('before_weather_table');
?>

<form id="city-search-form" method="GET">
    <input type="text" 
           name="search" 
           placeholder="<?php esc_attr_e('Search City...', 'storefront-child'); ?>"
           value="<?php echo isset($_GET['search']) ? esc_attr($_GET['search']) : ''; ?>">
    
    <input type="submit" value="<?php esc_attr_e('Search'); ?>">
</form>

<table class="weather-table widefat">
    <thead>
        <tr>
            <th scope="col"><?php esc_html_e('Country', 'storefront-child'); ?></th>
            <th scope="col"><?php esc_html_e('City', 'storefront-child'); ?></th>
            <th scope="col"><?php esc_html_e('Temperature', 'storefront-child'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        /**
         * Основной цикл вывода городов
         * 
         * Использует get_posts() для получения данных из CPT 'city'
         * 
         */
        if (!isset($_GET['search'])) :
            $cities_query = get_posts([
                'post_type'      => 'city',
                'posts_per_page' => -1,
                'post_status'    => 'publish',
                'orderby'        => 'title',
                'order'          => 'ASC'
            ]);
            
            foreach ($cities_query as $city) :
                $countries = wp_get_post_terms(
                    $city->ID,
                    'country',
                    ['fields' => 'names']
                );
                ?>
                <tr>
                    <td><?php echo !empty($countries) ? esc_html($countries[0]) : '-'; // Если страна для города не указана то '-' ?></td>
                    <td><?php echo esc_html($city->post_title); ?></td>
                    <td>
                        <?php 
                        $temperature = get_city_temperature(
                            get_post_meta($city->ID, '_latitude', true),
                            get_post_meta($city->ID, '_longitude', true)
                        );
                        echo esc_html($temperature);
                        ?>
                    </td>
                </tr>
                <?php
            endforeach;
        endif; 
        ?>
    </tbody>
</table>

<?php 
/**
 * Хук после вывода таблицы погоды
 *  
 */
do_action('after_weather_table');

get_footer();