<?php

require_once get_stylesheet_directory() . '/includes/services-loader.php';

// Theme Setup
function youhealit_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('menus');
    add_theme_support('widgets');
    
    register_nav_menus([
        'primary' => __('Primary Menu', 'youhealit'),
        'footer' => __('Footer Menu', 'youhealit'),
    ]);
}
add_action('after_setup_theme', 'youhealit_theme_setup');


define('YHI_PHONE', '(919) 241-5032');
define('YHI_EMAIL', 'info@youhealit.com');
define('YHI_BUSINESS_NAME', 'YouHealIt - Wellness Center of the Triangle');
define('YHI_TAGLINE', 'Professional health and wellness services throughout North Carolina');
define('YHI_ADDRESS', 'North Carolina');

/*
Fallback menu function that displays basic navigation when no WordPress menu is assigned.
Creates hardcoded navigation links for primary site pages.
*/
function youhealit_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li><a href="' . home_url() . '">Home</a></li>';
    echo '<li><a href="' . home_url('/services/') . '">Services</a></li>';
    echo '<li><a href="' . home_url('/about/') . '">About</a></li>';
    echo '<li><a href="' . home_url('/contact/') . '">Contact</a></li>';
    echo '<li><a href="' . home_url('/meet-our-team/') . '">Meet Our Team</a></li>';
    echo '<li><a href="' . home_url('/service-areas/') . '">Service Areas</a></li>';
    echo '<li><a href="' . home_url('/testimonials/') . '">Testimonials</a></li>';
    echo '</ul>';
}

function remove_spaces($string) {
    return strtolower(str_replace(' ', '', $string));
}


function youhealit_get_services(): array {
    static $services = null;
    if ($services !== null) return $services;

    $paths = [
        get_stylesheet_directory() . '/includes/services-data.php',
        get_stylesheet_directory() . '/services-data.php',
        get_template_directory() . '/includes/services-data.php',
        get_template_directory() . '/services-data.php',
    ];

    foreach ($paths as $p) {
        if (file_exists($p)) {
            $loaded = require $p;
            if (is_array($loaded)) {
                $services = $loaded;
                break;
            }
        }
    }

    if (!is_array($services)) $services = [];

    global $available_services;
    $available_services = $services;
    return $services;
}



// Optional global for legacy compatibility
global $available_services;
$available_services = youhealit_get_services();


// Enhanced CSS enqueue with proper file existence check
function youhealit_enqueue_scripts() {
    // Check if style.css exists before enqueueing
    $style_path = get_stylesheet_directory() . '/style.css';
    if (file_exists($style_path)) {
        wp_enqueue_style('youhealit-style', get_stylesheet_uri(), [], filemtime($style_path));
    }
    
    // Add inline styles for city navigation
    $inline_css = '
    .city-navigation {
        background: #f8f9fa;
        padding: 20px;
        margin: 30px 0;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .city-navigation a {
        text-decoration: none;
        color: #0073aa;
    }
    .city-navigation .btn {
        background: #0073aa;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
    }
    ';
    wp_add_inline_style('youhealit-style', $inline_css);
    
    // Enqueue JavaScript with proper check
    $js_path = get_template_directory() . '/js/main.js';
    if (file_exists($js_path)) {
        wp_enqueue_script('youhealit-script', get_template_directory_uri() . '/js/main.js', ['jquery'], filemtime($js_path), true);
    }
}
add_action('wp_enqueue_scripts', 'youhealit_enqueue_scripts');

/*
theme: youhealit
youhealit/functions.php
Add this below youhealit_fallback_menu() function, above youhealit_register_post_types() function, approx line 45
Creates individual WordPress pages for each service with proper titles and slugs
*/
function youhealit_create_service_pages() {
    $services = youhealit_get_services();
    
    if (empty($services)) {
        return;
    }
    
    $created_count = 0;
    
    foreach ($services as $service) {
        $service_title = ucwords(str_replace('-', ' ', $service['name']));
        $service_slug = sanitize_title($service_title);
        
        // Check if page already exists
        $existing_page = get_page_by_path($service_slug);
        
        if (!$existing_page) {
            // Create the page
            $page_data = [
                'post_title' => $service_title,
                'post_name' => $service_slug,
                'post_content' => '<h2>' . $service_title . '</h2>' . "\n\n" . 
                                '<p>' . $service['description'] . '</p>' . "\n\n" .
                                '<p>Contact us today to learn more about our ' . strtolower($service_title) . ' services and schedule your consultation.</p>' . "\n\n" .
                                '<div style="text-align: center; margin: 30px 0;">' . "\n" .
                                '<a href="/contact" class="btn btn-red">Schedule Consultation</a>' . "\n" .
                                '</div>',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'meta_input' => [
                    'service_name' => $service['name'],
                    'service_description' => $service['description']
                ]
            ];
            
            $page_id = wp_insert_post($page_data);
            
            if ($page_id && !is_wp_error($page_id)) {
                $created_count++;
            }
        }
    }
    
    return $created_count;
}


/*
youhealit/functions.php
REPLACE the youhealit_ajax_search_cities() function around line 95
Fixed AJAX handler with proper error handling and POST data validation
*/
function youhealit_ajax_search_cities() {
    // Check if query exists
    if (!isset($_POST['query'])) {
        wp_send_json_error('No query provided');
        return;
    }
    
    $query = sanitize_text_field($_POST['query']);
    
    if (strlen($query) < 2) {
        wp_send_json_success([]);
        return;
    }
    
    // Simplified query - just search post titles first
    $cities = get_posts([
        'post_type' => 'city',
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'city_name',
                'value' => $query,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'city_section_name', 
                'value' => $query,
                'compare' => 'LIKE'
            ]
        ]
    ]);
    
    $results = [];
    foreach ($cities as $city) {
        $city_name = get_post_meta($city->ID, 'city_name', true);
        $city_section = get_post_meta($city->ID, 'city_section_name', true);
        $city_zip = get_post_meta($city->ID, 'city_zip', true);
        
        if (empty($city_name)) {
            $city_name = $city->post_title;
        }
        
        $results[] = [
            'city_name' => $city_name,
            'city_section_name' => $city_section,
            'city_zip' => $city_zip,
            'url' => get_permalink($city->ID)
        ];
    }
    
    wp_send_json_success($results);
}
add_action('wp_ajax_search_cities', 'youhealit_ajax_search_cities');
add_action('wp_ajax_nopriv_search_cities', 'youhealit_ajax_search_cities');

// Function to manually trigger service page creation (run once)
function youhealit_admin_create_service_pages() {
    if (current_user_can('manage_options') && isset($_GET['create_service_pages'])) {
        $created = youhealit_create_service_pages();
        
        if ($created > 0) {
            add_action('admin_notices', function() use ($created) {
                echo '<div class="notice notice-success"><p>Successfully created ' . $created . ' service pages!</p></div>';
            });
        } else {
            add_action('admin_notices', function() {
                echo '<div class="notice notice-info"><p>No new service pages created - they may already exist.</p></div>';
            });
        }
    }
}
add_action('admin_init', 'youhealit_admin_create_service_pages');


// Register Custom Post Types
function youhealit_register_post_types() {
    // Cities Post Type
    register_post_type('city', [
        'labels' => [
            'name' => 'Cities & Locations',
            'singular_name' => 'City',
            'add_new' => 'Add New City',
            'add_new_item' => 'Add New City',
            'edit_item' => 'Edit City',
            'new_item' => 'New City',
            'all_items' => 'All Cities',
            'view_item' => 'View City',
            'search_items' => 'Search Cities',
            'not_found' => 'No cities found',
            'not_found_in_trash' => 'No cities found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 20,
        'menu_icon' => 'dashicons-location-alt',
        'supports' => ['title', 'editor', 'custom-fields'],
        'rewrite' => ['slug' => 'locations'],
        'capability_type' => 'post',
    ]);

    // Services Post Type
    register_post_type('service', [
        'labels' => [
            'name' => 'Services',
            'singular_name' => 'Service',
            'add_new' => 'Add New Service',
            'add_new_item' => 'Add New Service',
            'edit_item' => 'Edit Service',
            'new_item' => 'New Service',
            'all_items' => 'All Services',
            'view_item' => 'View Service',
            'search_items' => 'Search Services',
            'not_found' => 'No services found',
            'not_found_in_trash' => 'No services found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'services'],
    ]);

    // Team Members Post Type
    register_post_type('team', [
        'labels' => [
            'name' => 'Team Members',
            'singular_name' => 'Team Member',
            'add_new' => 'Add New Team Member',
            'add_new_item' => 'Add New Team Member',
            'edit_item' => 'Edit Team Member',
            'new_item' => 'New Team Member',
            'all_items' => 'All Team Members',
            'view_item' => 'View Team Member',
            'search_items' => 'Search Team Members',
            'not_found' => 'No team members found',
            'not_found_in_trash' => 'No team members found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'team'],
    ]);

    // Testimonials Post Type
    register_post_type('testimonial', [
        'labels' => [
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
            'add_new' => 'Add New Testimonial',
            'add_new_item' => 'Add New Testimonial',
            'edit_item' => 'Edit Testimonial',
            'new_item' => 'New Testimonial',
            'all_items' => 'All Testimonials',
            'view_item' => 'View Testimonial',
            'search_items' => 'Search Testimonials',
            'not_found' => 'No testimonials found',
            'not_found_in_trash' => 'No testimonials found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'rewrite' => ['slug' => 'testimonials'],
    ]);

    // Events Post Type
    register_post_type('event', [
        'labels' => [
            'name' => 'Events',
            'singular_name' => 'Event',
            'add_new' => 'Add New Event',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'new_item' => 'New Event',
            'all_items' => 'All Events',
            'view_item' => 'View Event',
            'search_items' => 'Search Events',
            'not_found' => 'No events found',
            'not_found_in_trash' => 'No events found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'events'],
    ]);

    // Special Offers Post Type
    register_post_type('special_offer', [
        'labels' => [
            'name' => 'Special Offers',
            'singular_name' => 'Special Offer',
            'add_new' => 'Add New Special Offer',
            'add_new_item' => 'Add New Special Offer',
            'edit_item' => 'Edit Special Offer',
            'new_item' => 'New Special Offer',
            'all_items' => 'All Special Offers',
            'view_item' => 'View Special Offer',
            'search_items' => 'Search Special Offers',
            'not_found' => 'No special offers found',
            'not_found_in_trash' => 'No special offers found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'special-offers'],
    ]);

    // Videos Post Type
    register_post_type('video', [
        'labels' => [
            'name' => 'Videos',
            'singular_name' => 'Video',
            'add_new' => 'Add New Video',
            'add_new_item' => 'Add New Video',
            'edit_item' => 'Edit Video',
            'new_item' => 'New Video',
            'all_items' => 'All Videos',
            'view_item' => 'View Video',
            'search_items' => 'Search Videos',
            'not_found' => 'No videos found',
            'not_found_in_trash' => 'No videos found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'videos'],
    ]);

    // Resources Post Type
    register_post_type('resource', [
        'labels' => [
            'name' => 'Resources',
            'singular_name' => 'Resource',
            'add_new' => 'Add New Resource',
            'add_new_item' => 'Add New Resource',
            'edit_item' => 'Edit Resource',
            'new_item' => 'New Resource',
            'all_items' => 'All Resources',
            'view_item' => 'View Resource',
            'search_items' => 'Search Resources',
            'not_found' => 'No resources found',
            'not_found_in_trash' => 'No resources found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
        'rewrite' => ['slug' => 'resources'],
    ]);

    // FAQs Post Type
    register_post_type('faq', [
        'labels' => [
            'name' => 'FAQs',
            'singular_name' => 'FAQ',
            'add_new' => 'Add New FAQ',
            'add_new_item' => 'Add New FAQ',
            'edit_item' => 'Edit FAQ',
            'new_item' => 'New FAQ',
            'all_items' => 'All FAQs',
            'view_item' => 'View FAQ',
            'search_items' => 'Search FAQs',
            'not_found' => 'No FAQs found',
            'not_found_in_trash' => 'No FAQs found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'rewrite' => ['slug' => 'faqs'],
    ]);

    // Reviews Post Type
    register_post_type('review', [
        'labels' => [
            'name' => 'Reviews',
            'singular_name' => 'Review',
            'add_new' => 'Add New Review',
            'add_new_item' => 'Add New Review',
            'edit_item' => 'Edit Review',
            'new_item' => 'New Review',
            'all_items' => 'All Reviews',
            'view_item' => 'View Review',
            'search_items' => 'Search Reviews',
            'not_found' => 'No reviews found',
            'not_found_in_trash' => 'No reviews found in trash'
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor', 'custom-fields'],
        'rewrite' => ['slug' => 'reviews'],
    ]);
}
add_action('init', 'youhealit_register_post_types');

// Register Taxonomies
function youhealit_register_taxonomies() {
    // Service Categories
    register_taxonomy('service-category', 'service', [
        'labels' => [
            'name' => 'Service Categories',
            'singular_name' => 'Service Category',
            'add_new_item' => 'Add New Service Category',
            'edit_item' => 'Edit Service Category',
            'new_item_name' => 'New Service Category Name',
            'menu_name' => 'Service Categories'
        ],
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'service-category'],
    ]);
}
add_action('init', 'youhealit_register_taxonomies');

// Register location taxonomies
function youhealit_register_location_taxonomies() {
    // State taxonomy
    register_taxonomy('state', 'city', [
        'labels' => [
            'name' => 'States',
            'singular_name' => 'State',
            'add_new_item' => 'Add New State',
            'edit_item' => 'Edit State',
            'menu_name' => 'States'
        ],
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => '', 'with_front' => false],
    ]);

    // City Section taxonomy
    register_taxonomy('city-section', 'city', [
        'labels' => [
            'name' => 'City Sections',
            'singular_name' => 'City Section',
            'add_new_item' => 'Add New City Section',
            'edit_item' => 'Edit City Section',
            'menu_name' => 'City Sections'
        ],
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'section'],
    ]);

    // Services taxonomy
    register_taxonomy('service-type', 'city', [
        'labels' => [
            'name' => 'Service Types',
            'singular_name' => 'Service Type',
            'add_new_item' => 'Add New Service Type',
            'edit_item' => 'Edit Service Type',
            'menu_name' => 'Service Types'
        ],
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => ['slug' => 'services'],
    ]);
}
add_action('init', 'youhealit_register_location_taxonomies');

// Add CSV Import Menu
function youhealit_add_csv_import_menu() {
    add_submenu_page(
        'edit.php?post_type=city',
        'CSV Import V2',
        'CSV Import V2',
        'manage_options',
        'youhealit-csv-import-v2',
        'youhealit_csv_import_page_v2'
    );
    
    add_submenu_page(
        'edit.php?post_type=city',
        'Statistics',
        'Statistics',
        'manage_options',
        'youhealit-statistics',
        'youhealit_statistics_page'
    );
}
add_action('admin_menu', 'youhealit_add_csv_import_menu');

// Fix Cities per page display
function youhealit_fix_cities_per_page($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('city') || is_home()) {
            $query->set('posts_per_page', 20);
        }
    }
}
add_action('pre_get_posts', 'youhealit_fix_cities_per_page');

// Fix admin cities list
function youhealit_admin_cities_per_page($query) {
    global $pagenow;
    if (is_admin() && $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'city') {
        $query->set('posts_per_page', 50);
    }
}
add_action('pre_get_posts', 'youhealit_admin_cities_per_page');

// Utility functions with proper sanitization
function youhealit_clean_zip_code($zip_code) {
    $zip_code = sanitize_text_field(trim($zip_code));
    
    if (strpos($zip_code, '.') !== false) {
        $zip_code = explode('.', $zip_code)[0];
    }
    
    if (is_numeric($zip_code) && strlen($zip_code) == 5) {
        return $zip_code;
    }
    
    if (preg_match('/^\d{5}-\d{4}$/', $zip_code)) {
        return $zip_code;
    }
    
    if (is_numeric($zip_code)) {
        return str_pad($zip_code, 5, '0', STR_PAD_LEFT);
    }
    
    return $zip_code;
}

function youhealit_title_case($string) {
    // Handle special cases first
    $special_cases = [
        'tbi' => 'TBI',
        'qigong' => 'QiGong'
    ];
    
    $lower = strtolower($string);
    if (isset($special_cases[$lower])) {
        return $special_cases[$lower];
    }
    
    // Standard title case
    return ucwords(strtolower($string));
}

function youhealit_randomize_services() {
    global $available_services;
    $service_names = get_service_names();
    shuffle($service_names);
    return implode('-', $service_names);
}

function youhealit_randomize_services_in_url($url) {
    $parts = explode('/', trim($url, '/'));
    
    if (count($parts) >= 4) {
        $parts[3] = youhealit_randomize_services();
        return '/' . implode('/', $parts);
    }
    
    return $url;
}

function youhealit_parse_page_url_nc($url) {
    $url = sanitize_text_field($url);
    $parts = explode('/', trim($url, '/'));
    
    $city_zip = isset($parts[1]) ? sanitize_text_field($parts[1]) : '';
    if ($city_zip) {
        $city_zip = preg_replace('/(\d+)\.0/', '$1', $city_zip);
    }
    
    return [
        'state' => 'north-carolina',
        'city_zip' => $city_zip,
        'city_section' => isset($parts[2]) ? sanitize_text_field($parts[2]) : '',
        'services' => isset($parts[3]) ? sanitize_text_field($parts[3]) : ''
    ];
}

function youhealit_assign_taxonomies_nc($post_id, $url_parts, $city_section_name) {
    $post_id = absint($post_id);
    $city_section_name = sanitize_text_field($city_section_name);
    
    // Always assign North Carolina state
    $state_term = wp_insert_term(
        'North Carolina',
        'state',
        ['slug' => 'north-carolina']
    );
    
    if (!is_wp_error($state_term)) {
        wp_set_post_terms($post_id, [$state_term['term_id']], 'state');
    }
    
    // Create/assign city section taxonomy
    if (!empty($city_section_name)) {
        $section_term = wp_insert_term(
            $city_section_name,
            'city-section',
            ['slug' => sanitize_title($url_parts['city_section'])]
        );
        
        if (!is_wp_error($section_term)) {
            wp_set_post_terms($post_id, [$section_term['term_id']], 'city-section');
        }
    }
    
    // Create/assign service types
    if (!empty($url_parts['services'])) {
        $services = explode('-', str_replace(['holistic-health', 'nutrition-supplements'], ['holistic_health', 'nutrition_supplements'], $url_parts['services']));
        $service_ids = [];
        
        foreach ($services as $service_slug) {
            $service_slug = sanitize_title($service_slug);
            if (empty($service_slug)) continue;
            
            $service_name = ucwords(str_replace('_', ' ', $service_slug));
            $service_term = wp_insert_term(
                $service_name,
                'service-type',
                ['slug' => $service_slug]
            );
            
            if (!is_wp_error($service_term)) {
                $service_ids[] = $service_term['term_id'];
            }
        }
        
        if (!empty($service_ids)) {
            wp_set_post_terms($post_id, $service_ids, 'service-type');
        }
    }
}

// Count CSV records with proper error handling
function youhealit_count_csv_records($file_path) {
    $file_path = sanitize_text_field($file_path);
    
    if (!file_exists($file_path)) {
        return 0;
    }
    
    $handle = @fopen($file_path, 'r');
    if (!$handle) {
        return 0;
    }
    
    $count = 0;
    
    // Skip header if it exists
    $first_line = fgetcsv($handle);
    if ($first_line === false) {
        fclose($handle);
        return 0;
    }
    
    // Count remaining lines
    while (fgetcsv($handle) !== false) {
        $count++;
    }
    
    fclose($handle);
    return $count;
}

// Clear all cities function
function youhealit_clear_all_cities() {
    $cities = get_posts([
        'post_type' => 'city',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ]);
    
    foreach ($cities as $city) {
        wp_delete_post($city->ID, true);
    }
    
    return count($cities);
}

// Enhanced CSV import function with security improvements
function youhealit_process_csv_batch_v2($batch_number, $batch_size = 25) {
    // Validate batch number
    $batch_number = absint($batch_number);
    if ($batch_number < 1) {
        $batch_number = 1;
    }
    
    $batch_size = absint($batch_size);
    if ($batch_size < 1 || $batch_size > 100) {
        $batch_size = 25;
    }
    
    $big_csv_path = get_template_directory() . '/assets/big.csv';
    
    if (!file_exists($big_csv_path)) {
        return [
            'success' => false,
            'message' => "big.csv not found in assets directory",
            'imported' => 0,
            'updated' => 0,
            'created' => 0,
            'checked' => 0,
            'skipped' => 0,
            'errors' => 1
        ];
    }
    
    $handle = @fopen($big_csv_path, 'r');
    if (!$handle) {
        return [
            'success' => false,
            'message' => "Could not open big.csv file",
            'imported' => 0,
            'updated' => 0,
            'created' => 0,
            'checked' => 0,
            'skipped' => 0,
            'errors' => 1
        ];
    }
    
    // Get header and verify required columns
    $header = fgetcsv($handle);
    if (!$header) {
        fclose($handle);
        return [
            'success' => false,
            'message' => "Could not read CSV header",
            'imported' => 0,
            'updated' => 0,
            'created' => 0,
            'checked' => 0,
            'skipped' => 0,
            'errors' => 1
        ];
    }
    
    // Find column indexes
    $required_columns = ['city_name', 'city_section_name', 'city_zip'];
    $column_indexes = [];
    
    foreach ($required_columns as $col) {
        $index = array_search($col, $header);
        if ($index === false) {
            fclose($handle);
            return [
                'success' => false,
                'message' => "Required column '{$col}' not found in CSV",
                'imported' => 0,
                'updated' => 0,
                'created' => 0,
                'checked' => 0,
                'skipped' => 0,
                'errors' => 1
            ];
        }
        $column_indexes[$col] = $index;
    }
    
    // Add optional columns
    $optional_columns = ['lat', 'lon', 'city_headline', 'city_subhead', 'city_text', 'city_image', 
                        'user_address', 'default_distance', 'wiki_page', 'city_page', 
                        'wikimedia_page', 'youhealit_page', 'batch_number'];
    
    foreach ($optional_columns as $col) {
        $index = array_search($col, $header);
        if ($index !== false) {
            $column_indexes[$col] = $index;
        }
    }
    
    // Count total rows first
    $total_rows = 0;
    while (fgetcsv($handle) !== false) {
        $total_rows++;
    }
    
    // Reset file pointer
    rewind($handle);
    fgetcsv($handle); // Skip header again
    
    // Calculate batch position
    $start_row = ($batch_number - 1) * $batch_size;
    $end_row = $start_row + $batch_size;
    
    // Skip to batch start
    for ($i = 0; $i < $start_row; $i++) {
        if (fgetcsv($handle) === false) {
            fclose($handle);
            return [
                'success' => true,
                'message' => "Batch {$batch_number}: End of file reached",
                'imported' => 0,
                'updated' => 0,
                'created' => 0,
                'checked' => 0,
                'skipped' => 0,
                'errors' => 0,
                'total_rows' => $total_rows,
                'is_complete' => true
            ];
        }
    }
    
    $imported = 0;
    $updated = 0;
    $created = 0;
    $checked = 0;
    $skipped = 0;
    $errors = 0;
    $current_row = $start_row;
    $detailed_log = [];
    
    while (($data = fgetcsv($handle)) !== false && $current_row < $end_row && $current_row < $total_rows) {
        if (empty($data) || count($data) < 3) {
            $errors++;
            $current_row++;
            continue;
        }
        
        // Extract and sanitize data using column indexes
        $city_name = isset($column_indexes['city_name']) && isset($data[$column_indexes['city_name']]) 
                    ? sanitize_text_field(trim($data[$column_indexes['city_name']])) : '';
        $city_section_name = isset($column_indexes['city_section_name']) && isset($data[$column_indexes['city_section_name']]) 
                           ? sanitize_text_field(trim($data[$column_indexes['city_section_name']])) : '';
        $city_zip = isset($column_indexes['city_zip']) && isset($data[$column_indexes['city_zip']]) 
                   ? youhealit_clean_zip_code(trim($data[$column_indexes['city_zip']])) : '';
        
        if (empty($city_name) || empty($city_section_name) || empty($city_zip)) {
            $skipped++;
            $current_row++;
            continue;
        }
        
        // Check if city exists
        $existing = get_posts([
            'post_type' => 'city',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'city_name',
                    'value' => $city_name,
                    'compare' => '='
                ],
                [
                    'key' => 'city_section_name',
                    'value' => $city_section_name,
                    'compare' => '='
                ],
                [
                    'key' => 'city_zip',
                    'value' => $city_zip,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => 1
        ]);
        
        if (!empty($existing)) {
            // Update existing city
            $post = $existing[0];
            $checked++;
            $needs_update = false;
            
            // Check if content is missing
            $city_text = isset($column_indexes['city_text']) && isset($data[$column_indexes['city_text']]) 
                        ? wp_kses_post(trim($data[$column_indexes['city_text']])) : '';
            
            if (empty($post->post_content) && !empty($city_text)) {
                wp_update_post([
                    'ID' => $post->ID,
                    'post_content' => $city_text
                ]);
                $needs_update = true;
            }
            
            // Update meta fields if missing
            foreach ($column_indexes as $field => $index) {
                if ($field === 'city_name' || $field === 'city_section_name' || $field === 'city_zip') {
                    continue; // Skip key fields
                }
                
                $value = isset($data[$index]) ? sanitize_text_field(trim($data[$index])) : '';
                if (!empty($value)) {
                    $existing_meta = get_post_meta($post->ID, $field, true);
                    if (empty($existing_meta)) {
                        update_post_meta($post->ID, $field, $value);
                        $needs_update = true;
                    }
                }
            }
            
            if ($needs_update) {
                $updated++;
                $detailed_log[] = "Updated: {$city_name}, NC - {$city_section_name}";
            }
            
        } else {
            // Create new city
            $city_text = isset($column_indexes['city_text']) && isset($data[$column_indexes['city_text']]) 
                        ? wp_kses_post(trim($data[$column_indexes['city_text']])) : '';
            
            $post_content = !empty($city_text) ? $city_text : "Welcome to {$city_name}, North Carolina in the {$city_section_name} area.";
            
            $post_id = wp_insert_post([
                'post_title' => sanitize_text_field("{$city_name}, North Carolina - {$city_section_name}"),
                'post_content' => $post_content,
                'post_status' => 'publish',
                'post_type' => 'city'
            ]);
            
            if ($post_id && !is_wp_error($post_id)) {
                // Save basic meta fields
                update_post_meta($post_id, 'city_name', $city_name);
                update_post_meta($post_id, 'state', 'North Carolina');
                update_post_meta($post_id, 'city_section_name', $city_section_name);
                update_post_meta($post_id, 'city_zip', $city_zip);
                
                // Save optional meta fields
                foreach ($column_indexes as $field => $index) {
                    if (in_array($field, ['city_name', 'city_section_name', 'city_zip'])) {
                        continue;
                    }
                    
                    $value = isset($data[$index]) ? sanitize_text_field(trim($data[$index])) : '';
                    if (!empty($value)) {
                        update_post_meta($post_id, $field, $value);
                    }
                }
                
                // Generate youhealit_page if missing
                $youhealit_page = isset($column_indexes['youhealit_page']) && isset($data[$column_indexes['youhealit_page']]) 
                                 ? sanitize_text_field(trim($data[$column_indexes['youhealit_page']])) : '';
                
                if (empty($youhealit_page)) {
                    $city_slug = strtolower(str_replace(' ', '-', $city_name)) . '-' . $city_zip;
                    $section_slug = strtolower(str_replace([' ', '/'], ['-', '-'], $city_section_name));
                    $randomized_services = youhealit_randomize_services();
                    $youhealit_page = "/north-carolina/{$city_slug}/{$section_slug}/{$randomized_services}";
                    update_post_meta($post_id, 'youhealit_page', $youhealit_page);
                }
                
                // Assign taxonomies
                $url_parts = youhealit_parse_page_url_nc($youhealit_page);
                youhealit_assign_taxonomies_nc($post_id, $url_parts, $city_section_name);
                
                $created++;
                $imported++;
                $detailed_log[] = "Created: {$city_name}, NC - {$city_section_name}";
            } else {
                $errors++;
            }
        }
        
        $current_row++;
    }
    
    fclose($handle);
    
    $total_batches = ceil($total_rows / $batch_size);
    $is_complete = $batch_number >= $total_batches;
    $progress = $total_batches > 0 ? round(($batch_number / $total_batches) * 100, 1) : 100;
    
    return [
        'success' => true,
        'message' => "Batch {$batch_number}/{$total_batches}: {$checked} checked, {$updated} updated, {$created} created, {$skipped} skipped, {$errors} errors",
        'imported' => $imported,
        'updated' => $updated,
        'created' => $created,
        'checked' => $checked,
        'skipped' => $skipped,
        'errors' => $errors,
        'batch_number' => $batch_number,
        'total_batches' => $total_batches,
        'total_rows' => $total_rows,
        'progress' => $progress,
        'is_complete' => $is_complete,
        'detailed_log' => $detailed_log
    ];
}

// AJAX handler with proper validation
function youhealit_ajax_process_csv_batch_v2() {
    // Verify nonce and permissions
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'youhealit_csv_import_v2')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }
    
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Access denied']);
        return;
    }
    
    // Validate batch number
    $batch_number = isset($_POST['batch_number']) ? absint($_POST['batch_number']) : 1;
    if ($batch_number < 1) {
        wp_send_json_error(['message' => 'Invalid batch number']);
        return;
    }
    
    $result = youhealit_process_csv_batch_v2($batch_number);
    
    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result);
    }
}
add_action('wp_ajax_youhealit_process_csv_batch_v2', 'youhealit_ajax_process_csv_batch_v2');

// CSV Import Page V2 with security improvements
function youhealit_csv_import_page_v2() {
    $assets_dir = get_template_directory() . '/assets/';
    $big_csv_path = $assets_dir . 'big.csv';
    $csv_exists = file_exists($big_csv_path);
    $csv_records = $csv_exists ? youhealit_count_csv_records($big_csv_path) : 0;
    $total_cities = wp_count_posts('city');
    $published_cities = isset($total_cities->publish) ? $total_cities->publish : 0;
    
    // Check CSV format
    $csv_format_check = '';
    $can_import = false;
    
    if ($csv_exists) {
        $handle = @fopen($big_csv_path, 'r');
        if ($handle) {
            $header = fgetcsv($handle);
            fclose($handle);
            
            if ($header) {
                $required_columns = ['city_name', 'city_section_name', 'city_zip'];
                $found_required = array_intersect($required_columns, $header);
                $missing_required = array_diff($required_columns, $header);
                
                if (count($found_required) == count($required_columns)) {
                    $can_import = true;
                    $csv_format_check = '<div class="notice notice-success">
                        <p><strong>✅ CSV Format Valid</strong></p>
                        <p><strong>Required columns found:</strong> ' . esc_html(implode(', ', $found_required)) . '</p>
                        <p><strong>Total columns:</strong> ' . count($header) . '</p>
                    </div>';
                } else {
                    $csv_format_check = '<div class="notice notice-error">
                        <p><strong>❌ CSV Format Invalid</strong></p>
                        <p><strong>Missing required columns:</strong> ' . esc_html(implode(', ', $missing_required)) . '</p>
                        <p><strong>Available columns:</strong> ' . esc_html(implode(', ', $header)) . '</p>
                    </div>';
                }
            } else {
                $csv_format_check = '<div class="notice notice-error">
                    <p><strong>❌ Could not read CSV header</strong></p>
                </div>';
            }
        } else {
            $csv_format_check = '<div class="notice notice-error">
                <p><strong>❌ Could not open CSV file</strong></p>
            </div>';
        }
    }
    ?>
    <div class="wrap">
        <h1>CSV Import V2 - Complete Version</h1>
        
        <div class="notice notice-info">
            <h3>📊 Current Status</h3>
            <p><strong>Cities in Database:</strong> <?php echo $published_cities; ?></p>
            <p><strong>CSV File Found:</strong> <?php echo $csv_exists ? '✅ Yes' : '❌ No'; ?></p>
            <?php if ($csv_exists): ?>
                <p><strong>CSV Records:</strong> <?php echo $csv_records; ?></p>
                <p><strong>CSV Location:</strong> <code><?php echo esc_html($big_csv_path); ?></code></p>
                <p><strong>CSV Size:</strong> <?php echo file_exists($big_csv_path) ? number_format(filesize($big_csv_path) / 1024, 1) . ' KB' : 'Unknown'; ?></p>
            <?php endif; ?>
        </div>
        
        <?php echo $csv_format_check; ?>
        
        <?php if ($csv_exists && $can_import): ?>
        
        <div id="import-progress" style="display: none;">
            <h3>🚀 Import Progress</h3>
            <div style="background: #f1f1f1; border: 1px solid #ccc; padding: 10px; margin: 10px 0; border-radius: 5px;">
                <div id="progress-bar" style="background: #0073aa; height: 20px; width: 0%; transition: width 0.3s; border-radius: 3px;"></div>
            </div>
            <div id="progress-info" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                <div>
                    <p><strong>Current Batch:</strong> <span id="current-batch">-</span></p>
                    <p><strong>Progress:</strong> <span id="progress-percent">0%</span></p>
                    <p><strong>Status:</strong> <span id="import-status">Ready</span></p>
                </div>
                <div>
                    <p><strong>Checked:</strong> <span id="records-checked">0</span></p>
                    <p><strong>Updated:</strong> <span id="records-updated">0</span></p>
                    <p><strong>Created:</strong> <span id="records-created">0</span></p>
                </div>
                <div>
                    <p><strong>Skipped:</strong> <span id="records-skipped">0</span></p>
                    <p><strong>Errors:</strong> <span id="records-errors">0</span></p>
                    <p><strong>Total Rows:</strong> <span id="total-rows"><?php echo $csv_records; ?></span></p>
                </div>
            </div>
            <div id="batch-log" style="max-height: 300px; overflow-y: auto; background: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-top: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                <h4>Processing Log:</h4>
            </div>
        </div>
        
        <h2>🚀 Process CSV Import</h2>
        <p><strong>Import/Update cities from big.csv (<?php echo $csv_records; ?> records)</strong></p>
        <button id="start-import" class="button button-primary button-large">
            Start Complete CSV Import (<?php echo $csv_records; ?> records)
        </button>
        <p><em>This will process your CSV file securely in batches, updating existing cities and creating new ones.</em></p>
        
        <?php else: ?>
        <div class="notice notice-warning">
            <p><strong>⚠️ Cannot import</strong></p>
            <?php if (!$csv_exists): ?>
                <p>big.csv file not found. Please place your CSV file at: <code><?php echo esc_html($big_csv_path); ?></code></p>
            <?php elseif (!$can_import): ?>
                <p>CSV format is invalid. Please ensure your CSV has the required columns: city_name, city_section_name, city_zip</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <h2>🧹 Database Management</h2>
        <form method="post" onsubmit="return confirm('Are you sure you want to delete ALL cities? This cannot be undone!');">
            <?php wp_nonce_field('youhealit_csv_import_v2'); ?>
            <input type="submit" name="clear_cities" class="button button-secondary" value="Clear All Cities">
            <p><em>This will permanently delete all city posts. Use with caution.</em></p>
        </form>
        
        <h2>🔗 Quick Links</h2>
        <p>
            <a href="<?php echo admin_url('edit.php?post_type=city'); ?>" class="button">View All Cities</a>
            <a href="<?php echo admin_url('options-permalink.php'); ?>" class="button">Refresh Permalinks</a>
        </p>
        
        <script>
        jQuery(document).ready(function($) {
            $('#start-import').click(function() {
                $(this).prop('disabled', true).text('Processing...');
                $('#import-progress').show();
                $('#import-status').text('Starting complete CSV import...');
                
                let currentBatch = 1;
                let totalChecked = 0, totalUpdated = 0, totalCreated = 0, totalSkipped = 0, totalErrors = 0;
                
                function processBatch() {
                    $.post(ajaxurl, {
                        action: 'youhealit_process_csv_batch_v2',
                        batch_number: currentBatch,
                        nonce: '<?php echo wp_create_nonce('youhealit_csv_import_v2'); ?>'
                    }, function(response) {
                        if (response.success) {
                            const data = response.data;
                            totalChecked += data.checked;
                            totalUpdated += data.updated;
                            totalCreated += data.created;
                            totalSkipped += data.skipped;
                            totalErrors += data.errors;
                            
                            $('#current-batch').text(data.batch_number + '/' + data.total_batches);
                            $('#progress-percent').text(data.progress + '%');
                            $('#progress-bar').css('width', data.progress + '%');
                            $('#records-checked').text(totalChecked);
                            $('#records-updated').text(totalUpdated);
                            $('#records-created').text(totalCreated);
                            $('#records-skipped').text(totalSkipped);
                            $('#records-errors').text(totalErrors);
                            $('#import-status').text('Processing batch ' + data.batch_number + '/' + data.total_batches);
                            
                            // Add log entries with XSS protection
                            $('#batch-log').append('<div>' + $('<div>').text(data.message).html() + '</div>');
                            if (data.detailed_log && data.detailed_log.length > 0) {
                                data.detailed_log.forEach(function(logEntry) {
                                    $('#batch-log').append('<div style="color: #666; font-size: 11px;">→ ' + $('<div>').text(logEntry).html() + '</div>');
                                });
                            }
                            $('#batch-log').scrollTop($('#batch-log')[0].scrollHeight);
                            
                            if (!data.is_complete) {
                                currentBatch++;
                                setTimeout(processBatch, 500);
                            } else {
                                $('#import-status').text('✅ Import completed! Total: ' + totalChecked + ' checked, ' + totalUpdated + ' updated, ' + totalCreated + ' created');
                                $('#start-import').prop('disabled', false).text('Start Complete CSV Import (<?php echo $csv_records; ?> records)');
                                $('#batch-log').append('<hr><div style="font-weight: bold; color: green;">Import completed successfully!</div>');
                            }
                        } else {
                            $('#import-status').text('❌ Import failed: ' + response.data.message);
                            $('#start-import').prop('disabled', false).text('Start Complete CSV Import (<?php echo $csv_records; ?> records)');
                            $('#batch-log').append('<div style="color: red;">Error: ' + $('<div>').text(response.data.message).html() + '</div>');
                        }
                    }).fail(function(xhr, status, error) {
                        $('#import-status').text('❌ AJAX request failed: ' + error);
                        $('#start-import').prop('disabled', false).text('Start Complete CSV Import (<?php echo $csv_records; ?> records)');
                        $('#batch-log').append('<div style="color: red;">AJAX Error: ' + $('<div>').text(error).html() + '</div>');
                    });
                }
                
                processBatch();
            });
        });
        </script>
        
        <?php
        // Handle form submissions with proper nonce check
        if (isset($_POST['clear_cities']) && wp_verify_nonce($_POST['_wpnonce'], 'youhealit_csv_import_v2')) {
            $deleted_count = youhealit_clear_all_cities();
            echo '<div class="notice notice-success"><p>' . absint($deleted_count) . ' cities have been deleted.</p></div>';
        }
        ?>
    </div>
    <?php
}

// Statistics page
function youhealit_statistics_page() {
    $total_cities = wp_count_posts('city');
    $published_cities = isset($total_cities->publish) ? $total_cities->publish : 0;
    $total_services = wp_count_posts('service');
    $published_services = isset($total_services->publish) ? $total_services->publish : 0;
    $total_team = wp_count_posts('team');
    $published_team = isset($total_team->publish) ? $total_team->publish : 0;
    $total_testimonials = wp_count_posts('testimonial');
    $published_testimonials = isset($total_testimonials->publish) ? $total_testimonials->publish : 0;
    
    // Get cities by state
    $cities_by_state = get_terms([
        'taxonomy' => 'state',
        'hide_empty' => true
    ]);
    
    // Get cities by section
    $cities_by_section = get_terms([
        'taxonomy' => 'city-section',
        'hide_empty' => true
    ]);
    
    // Get recent cities
    $recent_cities = get_posts([
        'post_type' => 'city',
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ]);
    ?>
    <div class="wrap">
        <h1>YouHealIt Statistics</h1>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3 style="margin: 0 0 10px 0;">Cities & Locations</h3>
                <p style="font-size: 24px; margin: 0; color: #0073aa;"><?php echo $published_cities; ?></p>
            </div>
            
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3 style="margin: 0 0 10px 0;">Services</h3>
                <p style="font-size: 24px; margin: 0; color: #0073aa;"><?php echo $published_services; ?></p>
            </div>
            
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3 style="margin: 0 0 10px 0;">Team Members</h3>
                <p style="font-size: 24px; margin: 0; color: #0073aa;"><?php echo $published_team; ?></p>
            </div>
            
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3 style="margin: 0 0 10px 0;">Testimonials</h3>
                <p style="font-size: 24px; margin: 0; color: #0073aa;"><?php echo $published_testimonials; ?></p>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0;">
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3>Cities by State</h3>
                <?php if (!empty($cities_by_state)): ?>
                    <ul>
                        <?php foreach ($cities_by_state as $state): ?>
                            <li><?php echo esc_html($state->name); ?>: <?php echo $state->count; ?> cities</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No states found.</p>
                <?php endif; ?>
            </div>
            
            <div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3>Cities by Section</h3>
                <?php if (!empty($cities_by_section)): ?>
                    <ul style="max-height: 300px; overflow-y: auto;">
                        <?php foreach ($cities_by_section as $section): ?>
                            <li><?php echo esc_html($section->name); ?>: <?php echo $section->count; ?> cities</li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No city sections found.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <h2>Recent Cities</h2>
        <?php if (!empty($recent_cities)): ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>City Name</th>
                        <th>Section</th>
                        <th>ZIP Code</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_cities as $city): ?>
                        <tr>
                            <td><a href="<?php echo get_edit_post_link($city->ID); ?>"><?php echo esc_html($city->post_title); ?></a></td>
                            <td><?php echo esc_html(get_post_meta($city->ID, 'city_name', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($city->ID, 'city_section_name', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($city->ID, 'city_zip', true)); ?></td>
                            <td><?php echo get_the_date('Y-m-d H:i', $city); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No cities found.</p>
        <?php endif; ?>
    </div>
    <?php
}

// Add city navigation to single city pages
function youhealit_add_city_navigation_nc($content) {
    if (is_singular('city')) {
        global $post;
        
        $city_name = get_post_meta($post->ID, 'city_name', true);
        $state = get_post_meta($post->ID, 'state', true);
        $city_section_name = get_post_meta($post->ID, 'city_section_name', true);
        
        if (!empty($city_name) && !empty($state)) {
            // Get adjacent cities in the same state and section
            $adjacent_args = [
                'post_type' => 'city',
                'posts_per_page' => 2,
                'post__not_in' => [$post->ID],
                'orderby' => 'title',
                'order' => 'ASC',
                'meta_query' => [
                    'relation' => 'AND',
                    [
                        'key' => 'state',
                        'value' => $state,
                        'compare' => '='
                    ]
                ]
            ];
            
            if (!empty($city_section_name)) {
                $adjacent_args['meta_query'][] = [
                    'key' => 'city_section_name',
                    'value' => $city_section_name,
                    'compare' => '='
                ];
            }
            
            $adjacent_cities = get_posts($adjacent_args);
            
            $navigation = '<div class="city-navigation">';
            
            if (!empty($adjacent_cities)) {
                $prev_city = isset($adjacent_cities[0]) ? $adjacent_cities[0] : null;
                $next_city = isset($adjacent_cities[1]) ? $adjacent_cities[1] : null;
                
                $navigation .= '<div class="nav-links">';
                
                if ($prev_city) {
                    $navigation .= '<a href="' . get_permalink($prev_city->ID) . '" class="btn">← ' . esc_html($prev_city->post_title) . '</a>';
                }
                
                $navigation .= '<span class="current-city">Current: ' . esc_html($post->post_title) . '</span>';
                
                if ($next_city) {
                    $navigation .= '<a href="' . get_permalink($next_city->ID) . '" class="btn">' . esc_html($next_city->post_title) . ' →</a>';
                }
                
                $navigation .= '</div>';
            }
            
            $navigation .= '</div>';
            
            $content .= $navigation;
        }
    }
    
    return $content;
}
add_filter('the_content', 'youhealit_add_city_navigation_nc');

// Add Widget Support
function youhealit_widgets_init() {
    register_sidebar([
        'name' => 'Primary Sidebar',
        'id' => 'primary-sidebar',
        'description' => 'Main sidebar for posts and pages',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);

    register_sidebar([
        'name' => 'Footer Widgets',
        'id' => 'footer-widgets',
        'description' => 'Widgets area in the footer',
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="footer-widget-title">',
        'after_title' => '</h4>',
    ]);
}
add_action('widgets_init', 'youhealit_widgets_init');

// Custom excerpts
function youhealit_custom_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'youhealit_custom_excerpt_length');

function youhealit_custom_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'youhealit_custom_excerpt_more');

// Cleanup and optimization
function youhealit_remove_wp_version() {
    return '';
}
add_filter('the_generator', 'youhealit_remove_wp_version');

// Add custom meta fields to city edit screen
function youhealit_add_city_meta_boxes() {
    add_meta_box(
        'youhealit_city_details',
        'City Details - North Carolina Structure',
        'youhealit_city_details_callback',
        'city',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'youhealit_add_city_meta_boxes');

function youhealit_city_details_callback($post) {
    wp_nonce_field('youhealit_city_details', 'youhealit_city_details_nonce');
    
    $city_name = get_post_meta($post->ID, 'city_name', true);
    $state = get_post_meta($post->ID, 'state', true);
    $city_section_name = get_post_meta($post->ID, 'city_section_name', true);
    $city_zip = get_post_meta($post->ID, 'city_zip', true);
    $lat = get_post_meta($post->ID, 'lat', true);
    $lon = get_post_meta($post->ID, 'lon', true);
    $city_headline = get_post_meta($post->ID, 'city_headline', true);
    $city_subhead = get_post_meta($post->ID, 'city_subhead', true);
    $youhealit_page = get_post_meta($post->ID, 'youhealit_page', true);
    
    echo '<table class="form-table">';
    echo '<tr><th><label for="city_name">City Name</label></th><td><input type="text" id="city_name" name="city_name" value="' . esc_attr($city_name) . '" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="state">State</label></th><td><input type="text" id="state" name="state" value="' . esc_attr($state ?: 'North Carolina') . '" class="regular-text" readonly /></td></tr>';
    echo '<tr><th><label for="city_section_name">City Section</label></th><td><input type="text" id="city_section_name" name="city_section_name" value="' . esc_attr($city_section_name) . '" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="city_zip">ZIP Code</label></th><td><input type="text" id="city_zip" name="city_zip" value="' . esc_attr($city_zip) . '" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="lat">Latitude</label></th><td><input type="text" id="lat" name="lat" value="' . esc_attr($lat) . '" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="lon">Longitude</label></th><td><input type="text" id="lon" name="lon" value="' . esc_attr($lon) . '" class="regular-text" /></td></tr>';
    echo '<tr><th><label for="city_headline">City Headline</label></th><td><input type="text" id="city_headline" name="city_headline" value="' . esc_attr($city_headline) . '" class="large-text" /></td></tr>';
    echo '<tr><th><label for="city_subhead">City Subhead</label></th><td><input type="text" id="city_subhead" name="city_subhead" value="' . esc_attr($city_subhead) . '" class="large-text" /></td></tr>';
    echo '<tr><th><label for="youhealit_page">YouHealIt Page URL</label></th><td><input type="text" id="youhealit_page" name="youhealit_page" value="' . esc_attr($youhealit_page) . '" class="large-text" /></td></tr>';
    echo '</table>';
}

function youhealit_save_city_details($post_id) {
    if (!isset($_POST['youhealit_city_details_nonce']) || !wp_verify_nonce($_POST['youhealit_city_details_nonce'], 'youhealit_city_details')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    $fields = ['city_name', 'state', 'city_section_name', 'city_zip', 'lat', 'lon', 'city_headline', 'city_subhead', 'youhealit_page'];
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
add_action('save_post', 'youhealit_save_city_details');

// Transform city page titles from "Location Service" to "Service in Location"
function youhealit_transform_city_title($title, $post_id = null) {
    // Only apply to city posts
    if (!$post_id || get_post_type($post_id) !== 'city') {
        return $title;
    }
    
    // Define service patterns to look for
    $services = [
        'Chiropractic Care',
        'Massage Therapy', 
        'Nutritional Consulting',
        'Acupuncture',
        'Physical Therapy',
        'Wellness Services',
        'Holistic Health',
        'Pain Management',
        'Sports Medicine',
        'Rehabilitation'
    ];
    
    // Create regex pattern to match any service at the end of title
    $service_pattern = '(' . implode('|', array_map('preg_quote', $services)) . ')';
    $pattern = '/^(.+?)\s+' . $service_pattern . '$/i';
    
    if (preg_match($pattern, $title, $matches)) {
        $location = trim($matches[1]);
        $service = trim($matches[2]);
        
        // Transform: "Green Level Estates Chiropractic Care" → "Chiropractic Care in Green Level Estates"
        return $service . ' in ' . $location;
    }
    
    return $title;
}

// Apply to the_title when displaying
add_filter('the_title', 'youhealit_transform_city_title', 10, 2);

// Also apply to wp_title for page titles
add_filter('wp_title', function($title) {
    if (is_singular('city')) {
        global $post;
        $transformed = youhealit_transform_city_title($post->post_title, $post->ID);
        return str_replace($post->post_title, $transformed, $title);
    }
    return $title;
});

// Transform city titles in listings/archives too
add_filter('get_the_title', 'youhealit_transform_city_title', 10, 2);



// Error handling and debugging functions
function youhealit_log_error($message, $context = []) {
    if (WP_DEBUG_LOG) {
        error_log('YouHealIt Error: ' . $message . ' Context: ' . print_r($context, true));
    }
}

// Ensure proper theme activation
function youhealit_activation_check() {
    // Flush rewrite rules on theme activation
    flush_rewrite_rules();
    
    // Log activation
    youhealit_log_error('Theme activated successfully');
}
add_action('after_switch_theme', 'youhealit_activation_check');

// Add security headers
function youhealit_add_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('send_headers', 'youhealit_add_security_headers');

// Disable file editing in admin
if (!defined('DISALLOW_FILE_EDIT')) {
    define('DISALLOW_FILE_EDIT', true);
}

// Remove unnecessary WordPress features for security
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

// Limit login attempts (basic protection)
// Limit login attempts (basic protection)
function youhealit_check_attempted_login($user, $username, $password) {
    if (get_transient('youhealit_attempted_login')) {
        $datas = get_transient('youhealit_attempted_login');
        if ($datas['tried'] >= 3) {
            $until = get_option('_transient_timeout_youhealit_attempted_login');
            $time = time() - $until;
            wp_die(sprintf('Too many failed login attempts. Please try again in %d minutes.', abs($time)));
        }
    }
    return $user;
}
add_filter('authenticate', 'youhealit_check_attempted_login', 30, 3);

function youhealit_login_failed($username) {
    if (get_transient('youhealit_attempted_login')) {
        $datas = get_transient('youhealit_attempted_login');
        $datas['tried']++;
        if ($datas['tried'] <= 3) {
            set_transient('youhealit_attempted_login', $datas, 300); // 5 minutes
        }
    } else {
        $datas = array('tried' => 1);
        set_transient('youhealit_attempted_login', $datas, 300); // 5 minutes  
    }
}
add_action('wp_login_failed', 'youhealit_login_failed', 10, 1);

// Clean up on successful login
function youhealit_login_successful() {
    if (get_transient('youhealit_attempted_login')) {
        delete_transient('youhealit_attempted_login');
    }
}
add_action('wp_login', 'youhealit_login_successful', 10, 1);
// Temporary ACF fallback functions
if (!function_exists('have_rows')) {
    function have_rows($field_name = '', $post_id = false) {
        return false;
    }
}

if (!function_exists('the_row')) {
    function the_row() {
        return false;
    }
}

if (!function_exists('get_sub_field')) {
    function get_sub_field($field_name, $format = true) {
        return '';
    }
}

if (!function_exists('get_field')) {
    function get_field($field_name, $post_id = false, $format = true) {
        return '';
    }
}


function debug_stylesheet() {
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_url = get_stylesheet_uri();
    
    echo "<!-- DEBUG: Style path: " . $style_path . " -->";
    echo "<!-- DEBUG: Style exists: " . (file_exists($style_path) ? 'YES' : 'NO') . " -->";
    echo "<!-- DEBUG: Style URL: " . $style_url . " -->";
}
add_action('wp_head', 'debug_stylesheet');

// Force Classic Editor site-wide
add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

// Optional: also disable block-based widgets (since 5.8+)
add_filter('use_widgets_block_editor', '__return_false');



// Helper function for services page - anchor IDs
function create_anchor_id($name) {
    // Convert to lowercase and replace spaces/special chars with hyphens
    $anchor = strtolower($name);
    $anchor = preg_replace('/[^a-z0-9\s\-]/', '', $anchor); // Remove special chars except spaces and hyphens
    $anchor = preg_replace('/[\s]+/', '-', $anchor); // Replace spaces with hyphens
    $anchor = preg_replace('/[-]+/', '-', $anchor); // Replace multiple hyphens with single
    $anchor = trim($anchor, '-'); // Remove leading/trailing hyphens
    return $anchor;
}

// Make available_services accessible to templates
function get_available_services() {
    global $available_services;
    return $available_services;
}

// Create virtual service posts from the array (optional - only if you want them in WordPress admin)
function youhealit_sync_services_from_array() {
    global $available_services;
    
    if (empty($available_services)) {
        return;
    }
    
    foreach ($available_services as $service) {
        // Check if service post already exists
        $existing = get_posts([
            'post_type' => 'service',
            'title' => $service['name'],
            'post_status' => 'any',
            'numberposts' => 1
        ]);
        
        if (empty($existing)) {
            // Create the service post
            wp_insert_post([
                'post_title' => $service['name'],
                'post_content' => $service['description'],
                'post_status' => 'publish',
                'post_type' => 'service'
            ]);
        }
    }
}

// Uncomment this line if you want to sync the array to WordPress posts
 add_action('init', 'youhealit_sync_services_from_array');

 // Make sure Page `/services/` wins by moving CPT archive off that slug
add_filter('register_post_type_args', function ($args, $post_type) {
    if ($post_type === 'service') {
        // Move archive and single URLs away from /services/
        $args['has_archive'] = 'service-archive'; // e.g. /service-archive/
        if (empty($args['rewrite']) || !is_array($args['rewrite'])) {
            $args['rewrite'] = [];
        }
        $args['rewrite']['slug'] = 'service-item'; // singles at /service-item/%postname%/
    }
    return $args;
}, 10, 2);

// Also move the taxonomy that was using /services/
add_filter('register_taxonomy_args', function ($args, $taxonomy) {
    if ($taxonomy === 'service-type') {
        if (empty($args['rewrite']) || !is_array($args['rewrite'])) {
            $args['rewrite'] = [];
        }
        $args['rewrite']['slug'] = 'service-type'; // e.g. /service-type/chiropractic/
    }
    return $args;
}, 10, 2);

/**
 * Enqueue theme assets the right way.
 * - Uses root style.css (WordPress-native)
 * - Skips during WP-CLI (so flush/export commands don't whine)
 */
function youhealit_enqueue_assets() {
    if (defined('WP_CLI') && WP_CLI) { return; }

    // CSS
    $style_path = get_stylesheet_directory() . '/style.css';
    $ver = file_exists($style_path) ? filemtime($style_path) : wp_get_theme()->get('Version');

    wp_enqueue_style(
        'youhealit-style',
        get_stylesheet_uri(),
        [],
        $ver
    );

    // (Optional) JS – only if you actually have this file; otherwise remove this block.
    $main_js_path = get_template_directory() . '/js/main.js';
    if (file_exists($main_js_path)) {
        wp_enqueue_script(
            'youhealit-script',
            get_template_directory_uri() . '/js/main.js',
            ['jquery'],
            filemtime($main_js_path),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'youhealit_enqueue_assets', 20);


function get_service_names() {
    $services = youhealit_get_services();
    return array_column($services, 'name');
}

// If that debug helper was added earlier, kill it.
if (function_exists('debug_stylesheet')) {
    remove_action('wp_head', 'debug_stylesheet');
}

?>