<?php
/**
 * Регистрация кастомного типа записи "Города"
 * 
 */

/**
 * Регистрация Custom Post Type 'city'
 * 
 * Создает новый тип записи для хранения информации о городах
 * 
 */
function register_cities_cpt() {
    $labels = [
        'name'          => esc_html__('Cities', 'storefront-child'),
        'singular_name' => esc_html__('City', 'storefront-child'),
        'menu_name'     => esc_html__('Cities', 'storefront-child'),
        'all_items'     => esc_html__('All Cities', 'storefront-child'),
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => ['title', 'editor'],
        'show_in_rest'       => true,
        'publicly_queryable' => true,
        'query_var'          => true,
    ];

    register_post_type('city', $args);
}
add_action('init', 'register_cities_cpt');

/**
 * Регистрация таксономии "Страны" для CPT "Города"
 * 
 * Создает иерархическую таксономию для группировки городов по странам
 * 
 */
function register_countries_taxonomy() {
    $labels = [
        'name'          => esc_html__('Countries', 'storefront-child'),
        'singular_name' => esc_html__('Country', 'storefront-child'),
    ];

    $args = [
        'labels'            => $labels,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
    ];

    register_taxonomy('country', ['city'], $args);
}
add_action('init', 'register_countries_taxonomy');