<?php
/*
Name: WordPress Menu Integration (PHP Version)
File: wordpress-menu-integration.php
Path: themes/youhealit/includes/wordpress-menu-integration.php
Function: Reads services from youhealit/includes/services-data.php and creates WordPress pages/menu items 
          using native WordPress functions - no external database connections needed
Usage:
    Access via WordPress Admin → YouHealIt → Menu Integration
    Or call youhealit_create_services_menu() function

Variables and Functions:
    $services_data - array containing parsed service data from PHP file
    $menu_id - WordPress menu ID for the Services menu
    $created_count - counter for successfully created menu items
    
    youhealit_get_services_for_menu() - loads services data from services-data.php
    youhealit_find_services_menu() - finds or creates the Services menu
    youhealit_create_services_menu_items() - creates individual menu items for each service
    youhealit_create_service_pages() - creates WordPress pages for each service
    youhealit_menu_integration_admin_page() - WordPress admin interface
    youhealit_create_services_menu() - main function that orchestrates the process

Dependencies: WordPress core functions
Requirements: services-data.php in includes/ directory, WordPress admin access
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Load services data from services-data.php
 * @return array Services data
 */
function youhealit_get_services_for_menu() {
    $services_file = get_template_directory() . '/includes/services-data.php';
    
    if (!file_exists($services_file)) {
        return [];
    }
    
    $services = include $services_file;
    
    if (!is_array($services)) {
        return [];
    }
    
    return $services;
}

/**
 * Find existing Services menu or create new one
 * @return int|false Menu ID or false on failure
 */
function youhealit_find_services_menu() {
    // Get all navigation menus
    $menus = wp_get_nav_menus();
    
    // Look for existing Services menu
    foreach ($menus as $menu) {
        if (stripos($menu->name, 'services') !== false || 
            stripos($menu->name, 'service') !== false) {
            return $menu->term_id;
        }
    }
    
    // Create new Services menu if none exists
    $menu_id = wp_create_nav_menu('Services');
    
    if (is_wp_error($menu_id)) {
        return false;
    }
    
    return $menu_id;
}

/**
 * Create WordPress pages for each service
 * @param array $services Services data
 * @return array Results with created/updated counts
 */
function youhealit_create_service_pages($services) {
    $results = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
        'messages' => []
    ];
    
    foreach ($services as $service_key => $service_data) {
        $service_name = isset($service_data['name']) ? $service_data['name'] : ucwords(str_replace('-', ' ', $service_key));
        $service_description = isset($service_data['description']) ? $service_data['description'] : '';
        $service_slug = sanitize_title($service_name);
        
        // Check if page already exists
        $existing_page = get_page_by_path($service_slug);
        
        if ($existing_page) {
            // Update existing page if content is empty
            if (empty($existing_page->post_content) && !empty($service_description)) {
                $updated = wp_update_post([
                    'ID' => $existing_page->ID,
                    'post_content' => wpautop($service_description)
                ]);
                
                if ($updated && !is_wp_error($updated)) {
                    $results['updated']++;
                    $results['messages'][] = "Updated page: {$service_name}";
                } else {
                    $results['errors']++;
                    $results['messages'][] = "Error updating page: {$service_name}";
                }
            } else {
                $results['skipped']++;
                $results['messages'][] = "Skipped existing page: {$service_name}";
            }
        } else {
            // Create new page
            $page_content = !empty($service_description) ? 
                wpautop($service_description) : 
                wpautop("Learn more about our {$service_name} services. Contact us today to schedule your consultation.");
            
            $page_id = wp_insert_post([
                'post_title' => $service_name,
                'post_name' => $service_slug,
                'post_content' => $page_content,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => get_current_user_id(),
                'meta_input' => [
                    'service_key' => $service_key,
                    'service_name' => $service_name,
                    'service_description' => $service_description
                ]
            ]);
            
            if ($page_id && !is_wp_error($page_id)) {
                $results['created']++;
                $results['messages'][] = "Created page: {$service_name}";
            } else {
                $results['errors']++;
                $results['messages'][] = "Error creating page: {$service_name}";
            }
        }
    }
    
    return $results;
}

/**
 * Create menu items for services in the Services menu
 * @param int $menu_id Menu ID
 * @param array $services Services data
 * @return array Results with created/skipped counts
 */
function youhealit_create_services_menu_items($menu_id, $services) {
    $results = [
        'created' => 0,
        'skipped' => 0,
        'errors' => 0,
        'messages' => []
    ];
    
    // Get existing menu items to avoid duplicates
    $existing_items = wp_get_nav_menu_items($menu_id);
    $existing_titles = [];
    
    if ($existing_items) {
        foreach ($existing_items as $item) {
            $existing_titles[] = $item->title;
        }
    }
    
    $menu_order = 0;
    
    foreach ($services as $service_key => $service_data) {
        $service_name = isset($service_data['name']) ? $service_data['name'] : ucwords(str_replace('-', ' ', $service_key));
        $service_url = isset($service_data['url']) ? $service_data['url'] : '/' . sanitize_title($service_name) . '/';
        
        // Skip if menu item already exists
        if (in_array($service_name, $existing_titles)) {
            $results['skipped']++;
            $results['messages'][] = "Skipped existing menu item: {$service_name}";
            continue;
        }
        
        $menu_order++;
        
        // Create menu item
        $menu_item_data = [
            'menu-item-object-id' => 0,
            'menu-item-object' => 'custom',
            'menu-item-parent-id' => 0,
            'menu-item-position' => $menu_order,
            'menu-item-type' => 'custom',
            'menu-item-title' => $service_name,
            'menu-item-url' => home_url($service_url),
            'menu-item-description' => isset($service_data['description']) ? $service_data['description'] : '',
            'menu-item-attr-title' => '',
            'menu-item-target' => '',
            'menu-item-classes' => 'service-menu-item',
            'menu-item-xfn' => '',
            'menu-item-status' => 'publish'
        ];
        
        $menu_item_id = wp_update_nav_menu_item($menu_id, 0, $menu_item_data);
        
        if ($menu_item_id && !is_wp_error($menu_item_id)) {
            $results['created']++;
            $results['messages'][] = "Created menu item: {$service_name}";
        } else {
            $results['errors']++;
            $results['messages'][] = "Error creating menu item: {$service_name}";
        }
    }
    
    return $results;
}

/**
 * Main function to create services menu and pages
 * @return array Complete results
 */
function youhealit_create_services_menu() {
    $results = [
        'success' => false,
        'services_found' => 0,
        'menu_id' => 0,
        'pages' => ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0],
        'menu_items' => ['created' => 0, 'skipped' => 0, 'errors' => 0],
        'messages' => [],
        'errors' => []
    ];
    
    // Load services data
    $services = youhealit_get_services_for_menu();
    
    if (empty($services)) {
        $results['errors'][] = 'No services found in services-data.php';
        return $results;
    }
    
    $results['services_found'] = count($services);
    $results['messages'][] = "Found {$results['services_found']} services in services-data.php";
    
    // Find or create Services menu
    $menu_id = youhealit_find_services_menu();
    
    if (!$menu_id) {
        $results['errors'][] = 'Could not find or create Services menu';
        return $results;
    }
    
    $results['menu_id'] = $menu_id;
    $results['messages'][] = "Using Services menu (ID: {$menu_id})";
    
    // Create service pages
    $page_results = youhealit_create_service_pages($services);
    $results['pages'] = $page_results;
    $results['messages'] = array_merge($results['messages'], $page_results['messages']);
    
    // Create menu items
    $menu_results = youhealit_create_services_menu_items($menu_id, $services);
    $results['menu_items'] = $menu_results;
    $results['messages'] = array_merge($results['messages'], $menu_results['messages']);
    
    $results['success'] = true;
    
    return $results;
}

/**
 * AJAX handler for menu integration
 */
function youhealit_ajax_menu_integration() {
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Access denied']);
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'youhealit_menu_integration')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }
    
    $results = youhealit_create_services_menu();
    
    if ($results['success']) {
        wp_send_json_success($results);
    } else {
        wp_send_json_error($results);
    }
}
add_action('wp_ajax_youhealit_menu_integration', 'youhealit_ajax_menu_integration');

/**
 * Add admin menu for menu integration
 */
function youhealit_add_menu_integration_admin() {
    add_submenu_page(
        'themes.php',
        'YouHealIt Menu Integration',
        'Menu Integration',
        'manage_options',
        'youhealit-menu-integration',
        'youhealit_menu_integration_admin_page'
    );
}
add_action('admin_menu', 'youhealit_add_menu_integration_admin');

/**
 * Admin page for menu integration
 */
function youhealit_menu_integration_admin_page() {
    // Handle form submission
    if (isset($_POST['create_menu']) && wp_verify_nonce($_POST['_wpnonce'], 'youhealit_menu_integration')) {
        $results = youhealit_create_services_menu();
        
        if ($results['success']) {
            echo '<div class="notice notice-success"><p><strong>Menu Integration Completed!</strong></p>';
            echo '<p>Services found: ' . $results['services_found'] . '</p>';
            echo '<p>Pages created: ' . $results['pages']['created'] . '</p>';
            echo '<p>Pages updated: ' . $results['pages']['updated'] . '</p>';
            echo '<p>Menu items created: ' . $results['menu_items']['created'] . '</p>';
            echo '<p>Menu items skipped: ' . $results['menu_items']['skipped'] . '</p>';
            echo '</div>';
        } else {
            echo '<div class="notice notice-error"><p><strong>Menu Integration Failed!</strong></p>';
            foreach ($results['errors'] as $error) {
                echo '<p>Error: ' . esc_html($error) . '</p>';
            }
            echo '</div>';
        }
    }
    
    // Get current status
    $services = youhealit_get_services_for_menu();
    $services_count = count($services);
    
    // Get existing menu info
    $menu_id = youhealit_find_services_menu();
    $menu_items_count = 0;
    if ($menu_id) {
        $menu_items = wp_get_nav_menu_items($menu_id);
        $menu_items_count = $menu_items ? count($menu_items) : 0;
    }
    
    ?>
    <div class="wrap">
        <h1>YouHealIt Menu Integration</h1>
        
        <div class="notice notice-info">
            <h3>📊 Current Status</h3>
            <p><strong>Services in data file:</strong> <?php echo $services_count; ?></p>
            <p><strong>Services menu exists:</strong> <?php echo $menu_id ? 'Yes (ID: ' . $menu_id . ')' : 'No'; ?></p>
            <p><strong>Menu items in Services menu:</strong> <?php echo $menu_items_count; ?></p>
            <p><strong>Services data file:</strong> <?php echo file_exists(get_template_directory() . '/includes/services-data.php') ? '✅ Found' : '❌ Missing'; ?></p>
        </div>
        
        <?php if ($services_count > 0): ?>
        <div class="card">
            <h2>🚀 Create Services Menu & Pages</h2>
            <p>This will:</p>
            <ul>
                <li>Create or update WordPress pages for each service</li>
                <li>Create or find the "Services" navigation menu</li>
                <li>Add menu items for each service to the Services menu</li>
                <li>Skip existing pages and menu items to avoid duplicates</li>
            </ul>
            
            <form method="post">
                <?php wp_nonce_field('youhealit_menu_integration'); ?>
                <input type="submit" name="create_menu" class="button button-primary button-large" 
                       value="Create Services Menu & Pages (<?php echo $services_count; ?> services)">
            </form>
        </div>
        
        <div class="card">
            <h3>📋 Services Found in Data File</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <ol>
                    <?php foreach ($services as $key => $service): ?>
                        <li>
                            <strong><?php echo esc_html(isset($service['name']) ? $service['name'] : ucwords(str_replace('-', ' ', $key))); ?></strong>
                            <?php if (isset($service['description'])): ?>
                                <br><small><?php echo esc_html(wp_trim_words($service['description'], 15)); ?></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        <?php else: ?>
        <div class="notice notice-error">
            <p><strong>❌ No services data found!</strong></p>
            <p>Please ensure your <code>services-data.php</code> file exists in the <code>includes/</code> directory and contains valid service data.</p>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>🔗 Quick Links</h3>
            <p>
                <a href="<?php echo admin_url('nav-menus.php'); ?>" class="button">Manage Menus</a>
                <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="button">View Pages</a>
                <a href="<?php echo admin_url('customize.php'); ?>" class="button">Assign Menus to Locations</a>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Auto-run on theme activation (optional)
 */
function youhealit_auto_create_services_menu() {
    // Only run once after theme activation
    if (!get_option('youhealit_services_menu_created')) {
        youhealit_create_services_menu();
        update_option('youhealit_services_menu_created', true);
    }
}
// Uncomment the line below to auto-run on theme activation
// add_action('after_switch_theme', 'youhealit_auto_create_services_menu');

?>