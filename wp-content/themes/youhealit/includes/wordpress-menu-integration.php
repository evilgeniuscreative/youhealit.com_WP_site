<?php
/*
Name: WordPress Menu Integration (Updated for Submenu)
File: wordpress-menu-integration.php
Path: themes/youhealit/includes/wordpress-menu-integration.php
Function: Adds services as submenu items under existing Services menu item
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
 * Find the Services parent menu item in menu ID 3680
 * @return array|false Menu info or false on failure
 */
function youhealit_find_services_menu() {
    // Use your specific Services menu (ID: 3680)
    $services_menu = wp_get_nav_menu_object(3680);
    
    if (!$services_menu) {
        return false;
    }
    
    // Get all menu items in this menu
    $menu_items = wp_get_nav_menu_items(3680);
    
    if (!$menu_items) {
        return false;
    }
    
    // Look for the main Services menu item (likely the first/only one)
    foreach ($menu_items as $item) {
        if (stripos($item->title, 'services') !== false || 
            stripos($item->title, 'service') !== false) {
            return [
                'menu_id' => 3680,
                'parent_item_id' => $item->ID,
                'parent_title' => $item->title
            ];
        }
    }
    
    // If no Services item found, we can use the menu directly
    return [
        'menu_id' => 3680,
        'parent_item_id' => 0, // Top level
        'parent_title' => 'Services Menu'
    ];
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
 * Create submenu items for services under the Services parent menu item
 * @param array $menu_info Menu information (menu_id, parent_item_id)
 * @param array $services Services data
 * @return array Results with created/skipped counts
 */
function youhealit_create_services_menu_items($menu_info, $services) {
    $results = [
        'created' => 0,
        'skipped' => 0,
        'errors' => 0,
        'messages' => []
    ];
    
    $menu_id = $menu_info['menu_id'];
    $parent_item_id = $menu_info['parent_item_id'];
    
    // Get existing submenu items under Services
    $existing_items = wp_get_nav_menu_items($menu_id);
    $existing_submenu_titles = [];
    
    if ($existing_items) {
        foreach ($existing_items as $item) {
            if ($item->menu_item_parent == $parent_item_id) {
                $existing_submenu_titles[] = $item->title;
            }
        }
    }
    
    $results['messages'][] = "Found " . count($existing_submenu_titles) . " existing submenu items under Services";
    
    $menu_order = count($existing_submenu_titles); // Start after existing items
    
    foreach ($services as $service_key => $service_data) {
        $service_name = isset($service_data['name']) ? $service_data['name'] : ucwords(str_replace('-', ' ', $service_key));
        $service_url = isset($service_data['url']) ? $service_data['url'] : '/' . sanitize_title($service_name) . '/';
        
        // Skip if submenu item already exists
        if (in_array($service_name, $existing_submenu_titles)) {
            $results['skipped']++;
            $results['messages'][] = "Skipped existing submenu item: {$service_name}";
            continue;
        }
        
        $menu_order++;
        
        // Create submenu item under Services parent
        $menu_item_data = [
            'menu-item-object-id' => 0,
            'menu-item-object' => 'custom',
            'menu-item-parent-id' => $parent_item_id,  // This makes it a submenu item
            'menu-item-position' => $menu_order,
            'menu-item-type' => 'custom',
            'menu-item-title' => $service_name,
            'menu-item-url' => home_url($service_url),
            'menu-item-description' => isset($service_data['description']) ? wp_trim_words(strip_tags($service_data['description']), 20) : '',
            'menu-item-target' => '',
            'menu-item-classes' => 'service-submenu-item',
            'menu-item-status' => 'publish'
        ];
        
        $menu_item_id = wp_update_nav_menu_item($menu_id, 0, $menu_item_data);
        
        if ($menu_item_id && !is_wp_error($menu_item_id)) {
            $results['created']++;
            $results['messages'][] = "Created submenu item: {$service_name}";
        } else {
            $results['errors']++;
            $results['messages'][] = "Error creating submenu item: {$service_name}";
        }
    }
    
    return $results;
}

/**
 * Main function to create services submenu and pages
 * @return array Complete results
 */
function youhealit_create_services_menu() {
    $results = [
        'success' => false,
        'services_found' => 0,
        'menu_id' => 3680,
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
    
    // Find Services parent menu item in menu 3680
    $menu_info = youhealit_find_services_menu();
    
    if (!$menu_info) {
        $results['errors'][] = 'Could not find Services menu item in menu ID 3680';
        return $results;
    }
    
    $results['messages'][] = "Found Services parent: '{$menu_info['parent_title']}' (ID: {$menu_info['parent_item_id']}) in menu {$menu_info['menu_id']}";
    
    // Create service pages
    $page_results = youhealit_create_service_pages($services);
    $results['pages'] = $page_results;
    $results['messages'] = array_merge($results['messages'], $page_results['messages']);
    
    // Create submenu items under Services
    $menu_results = youhealit_create_services_menu_items($menu_info, $services);
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
            echo '<p>Submenu items created: ' . $results['menu_items']['created'] . '</p>';
            echo '<p>Submenu items skipped: ' . $results['menu_items']['skipped'] . '</p>';
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
    $menu_info = youhealit_find_services_menu();
    $submenu_items_count = 0;
    if ($menu_info) {
        $all_items = wp_get_nav_menu_items($menu_info['menu_id']);
        if ($all_items) {
            foreach ($all_items as $item) {
                if ($item->menu_item_parent == $menu_info['parent_item_id']) {
                    $submenu_items_count++;
                }
            }
        }
    }
    
    ?>
    <div class="wrap">
        <h1>YouHealIt Menu Integration</h1>
        
        <div class="notice notice-info">
            <h3>📊 Current Status</h3>
            <p><strong>Services in data file:</strong> <?php echo $services_count; ?></p>
            <p><strong>Target menu:</strong> Services Menu (ID: 3680)</p>
            <?php if ($menu_info): ?>
                <p><strong>Services parent item:</strong> "<?php echo esc_html($menu_info['parent_title']); ?>" (ID: <?php echo $menu_info['parent_item_id']; ?>)</p>
                <p><strong>Current submenu items:</strong> <?php echo $submenu_items_count; ?></p>
            <?php else: ?>
                <p><strong>Services parent item:</strong> ❌ Not found in menu 3680</p>
            <?php endif; ?>
            <p><strong>Services data file:</strong> <?php echo file_exists(get_template_directory() . '/includes/services-data.php') ? '✅ Found' : '❌ Missing'; ?></p>
        </div>
        
        <?php if ($services_count > 0 && $menu_info): ?>
        <div class="card">
            <h2>🚀 Add Services to Submenu</h2>
            <p>This will:</p>
            <ul>
                <li>Create or update WordPress pages for each service</li>
                <li>Add submenu items under "<?php echo esc_html($menu_info['parent_title']); ?>" in Services Menu (ID: 3680)</li>
                <li>Skip existing pages and menu items to avoid duplicates</li>
                <li>Add <?php echo $services_count - $submenu_items_count; ?> new submenu items</li>
            </ul>
            
            <form method="post">
                <?php wp_nonce_field('youhealit_menu_integration'); ?>
                <input type="submit" name="create_menu" class="button button-primary button-large" 
                       value="Add Services to Submenu (<?php echo $services_count; ?> services)">
            </form>
        </div>
        <?php else: ?>
        <div class="notice notice-error">
            <?php if ($services_count == 0): ?>
                <p><strong>❌ No services data found!</strong></p>
                <p>Please ensure your <code>services-data.php</code> file exists and contains valid service data.</p>
            <?php endif; ?>
            <?php if (!$menu_info): ?>
                <p><strong>❌ Services menu item not found!</strong></p>
                <p>Please ensure you have a "Services" menu item in menu ID 3680.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>📋 Services Found in Data File</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <ol>
                    <?php foreach ($services as $key => $service): ?>
                        <li>
                            <strong><?php echo esc_html(isset($service['name']) ? $service['name'] : ucwords(str_replace('-', ' ', $key))); ?></strong>
                            <?php if (isset($service['description'])): ?>
                                <br><small><?php echo esc_html(wp_trim_words(strip_tags($service['description']), 15)); ?></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        
        <div class="card">
            <h3>🔗 Quick Links</h3>
            <p>
                <a href="<?php echo admin_url('nav-menus.php?action=edit&menu=' . ($menu_info ? $menu_info['menu_id'] : '3680')); ?>" class="button">Edit Services Menu</a>
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