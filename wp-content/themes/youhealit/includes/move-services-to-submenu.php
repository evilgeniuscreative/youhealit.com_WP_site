<?php
/*
Name: Move Services Menu Items to Submenu
File: move-services-to-submenu.php
Function: Moves all items from Services menu (3680) to be submenu items under Services in main menu
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Find the Services parent menu item in the main navigation menu
 * @return array|false Menu info or false on failure
 */
function youhealit_find_main_services_parent() {
    // Get all menus to find the main navigation menu
    $menus = wp_get_nav_menus();
    
    foreach ($menus as $menu) {
        // Skip the Services menu (3680) - we want the main menu
        if ($menu->term_id == 3680) {
            continue;
        }
        
        // Get menu items in this menu
        $menu_items = wp_get_nav_menu_items($menu->term_id);
        
        if ($menu_items) {
            foreach ($menu_items as $item) {
                if (stripos($item->title, 'services') !== false || 
                    stripos($item->title, 'service') !== false) {
                    return [
                        'main_menu_id' => $menu->term_id,
                        'main_menu_name' => $menu->name,
                        'parent_item_id' => $item->ID,
                        'parent_title' => $item->title
                    ];
                }
            }
        }
    }
    
    return false;
}

/**
 * Move services menu items from Services menu (3680) to main menu as submenu items
 * @return array Results
 */
function youhealit_move_services_to_submenu() {
    $results = [
        'success' => false,
        'moved' => 0,
        'skipped' => 0,
        'errors' => 0,
        'messages' => [],
        'errors_list' => []
    ];
    
    // Find the Services parent in main menu
    $main_services = youhealit_find_main_services_parent();
    
    if (!$main_services) {
        $results['errors_list'][] = 'Could not find Services menu item in main navigation menu';
        return $results;
    }
    
    $results['messages'][] = "Found Services parent: '{$main_services['parent_title']}' in menu '{$main_services['main_menu_name']}'";
    
    // Get all items from Services menu (3680)
    $services_menu_items = wp_get_nav_menu_items(3680);
    
    if (!$services_menu_items) {
        $results['errors_list'][] = 'No items found in Services menu (ID: 3680)';
        return $results;
    }
    
    $results['messages'][] = "Found " . count($services_menu_items) . " items in Services menu to move";
    
    // Get existing submenu items in main menu to avoid duplicates
    $main_menu_items = wp_get_nav_menu_items($main_services['main_menu_id']);
    $existing_submenu_titles = [];
    
    if ($main_menu_items) {
        foreach ($main_menu_items as $item) {
            if ($item->menu_item_parent == $main_services['parent_item_id']) {
                $existing_submenu_titles[] = $item->title;
            }
        }
    }
    
    $menu_order = count($existing_submenu_titles); // Start after existing submenu items
    
    // Create array to track processed titles (case-insensitive)
    $processed_titles = [];
    
    foreach ($services_menu_items as $service_item) {
        // Convert to proper Title Case
        $title_case = ucwords(strtolower($service_item->title));
        $title_lower = strtolower($title_case);
        
        // Check for case-insensitive duplicates in existing submenu
        $duplicate_found = false;
        foreach ($existing_submenu_titles as $existing_title) {
            if (strtolower($existing_title) === $title_lower) {
                $duplicate_found = true;
                break;
            }
        }
        
        // Check for case-insensitive duplicates in items we're processing
        if (in_array($title_lower, $processed_titles)) {
            $duplicate_found = true;
        }
        
        if ($duplicate_found) {
            $results['skipped']++;
            $results['messages'][] = "Skipped duplicate: {$title_case} (case-insensitive check)";
            continue;
        }
        
        // Add to processed list
        $processed_titles[] = $title_lower;
        $menu_order++;
        
        // Create new menu item in main menu as submenu of Services
        $menu_item_data = [
            'menu-item-object-id' => $service_item->object_id,
            'menu-item-object' => $service_item->object,
            'menu-item-parent-id' => $main_services['parent_item_id'], // Make it a submenu item
            'menu-item-position' => $menu_order,
            'menu-item-type' => $service_item->type,
            'menu-item-title' => $title_case, // Use Title Case version
            'menu-item-url' => $service_item->url,
            'menu-item-description' => $service_item->description,
            'menu-item-attr-title' => $service_item->attr_title,
            'menu-item-target' => $service_item->target,
            'menu-item-classes' => $service_item->classes,
            'menu-item-xfn' => $service_item->xfn,
            'menu-item-status' => 'publish'
        ];
        
        $new_menu_item_id = wp_update_nav_menu_item($main_services['main_menu_id'], 0, $menu_item_data);
        
        if ($new_menu_item_id && !is_wp_error($new_menu_item_id)) {
            $results['moved']++;
            $results['messages'][] = "Moved to submenu: {$title_case}";
        } else {
            $results['errors']++;
            $results['errors_list'][] = "Error moving: {$title_case}";
        }
    }
    
    $results['success'] = true;
    
    return $results;
}

/**
 * AJAX handler for moving services
 */
function youhealit_ajax_move_services() {
    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Access denied']);
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'youhealit_move_services')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
        return;
    }
    
    $results = youhealit_move_services_to_submenu();
    
    if ($results['success']) {
        wp_send_json_success($results);
    } else {
        wp_send_json_error($results);
    }
}
add_action('wp_ajax_youhealit_move_services', 'youhealit_ajax_move_services');

/**
 * Add admin menu for moving services
 */
function youhealit_add_move_services_admin() {
    add_submenu_page(
        'themes.php',
        'Move Services to Submenu',
        'Move Services',
        'manage_options',
        'youhealit-move-services',
        'youhealit_move_services_admin_page'
    );
}
add_action('admin_menu', 'youhealit_add_move_services_admin');

/**
 * Admin page for moving services
 */
function youhealit_move_services_admin_page() {
    // Handle form submission
    if (isset($_POST['move_services']) && wp_verify_nonce($_POST['_wpnonce'], 'youhealit_move_services')) {
        $results = youhealit_move_services_to_submenu();
        
        if ($results['success']) {
            echo '<div class="notice notice-success"><p><strong>Services Move Completed!</strong></p>';
            echo '<p>Items moved: ' . $results['moved'] . '</p>';
            echo '<p>Items skipped: ' . $results['skipped'] . '</p>';
            echo '<p>Errors: ' . $results['errors'] . '</p>';
            if (!empty($results['messages'])) {
                echo '<details><summary>View Details</summary><ul>';
                foreach ($results['messages'] as $message) {
                    echo '<li>' . esc_html($message) . '</li>';
                }
                echo '</ul></details>';
            }
            echo '</div>';
        } else {
            echo '<div class="notice notice-error"><p><strong>Move Failed!</strong></p>';
            foreach ($results['errors_list'] as $error) {
                echo '<p>Error: ' . esc_html($error) . '</p>';
            }
            echo '</div>';
        }
    }
    
    // Get current status
    $main_services = youhealit_find_main_services_parent();
    $services_menu_items = wp_get_nav_menu_items(3680);
    $services_count = $services_menu_items ? count($services_menu_items) : 0;
    
    // Count existing submenu items
    $existing_submenu_count = 0;
    if ($main_services) {
        $main_menu_items = wp_get_nav_menu_items($main_services['main_menu_id']);
        if ($main_menu_items) {
            foreach ($main_menu_items as $item) {
                if ($item->menu_item_parent == $main_services['parent_item_id']) {
                    $existing_submenu_count++;
                }
            }
        }
    }
    
    ?>
    <div class="wrap">
        <h1>Move Services to Submenu</h1>
        
        <div class="notice notice-info">
            <h3>📊 Current Status</h3>
            <p><strong>Services menu (ID: 3680) items:</strong> <?php echo $services_count; ?></p>
            <?php if ($main_services): ?>
                <p><strong>Main menu:</strong> "<?php echo esc_html($main_services['main_menu_name']); ?>" (ID: <?php echo $main_services['main_menu_id']; ?>)</p>
                <p><strong>Services parent item:</strong> "<?php echo esc_html($main_services['parent_title']); ?>" (ID: <?php echo $main_services['parent_item_id']; ?>)</p>
                <p><strong>Current submenu items:</strong> <?php echo $existing_submenu_count; ?></p>
            <?php else: ?>
                <p><strong>Services parent item:</strong> ❌ Not found in main menu</p>
            <?php endif; ?>
        </div>
        
        <?php if ($services_count > 0 && $main_services): ?>
        <div class="card">
            <h2>🚀 Move Services Menu Items</h2>
            <p>This will:</p>
            <ul>
                <li>Move all <?php echo $services_count; ?> items from Services menu (ID: 3680)</li>
                <li>Add them as submenu items under "<?php echo esc_html($main_services['parent_title']); ?>" in main menu</li>
                <li>Skip any items that already exist as submenu items</li>
                <li>Keep the original items in Services menu (they won't be deleted)</li>
            </ul>
            
            <form method="post">
                <?php wp_nonce_field('youhealit_move_services'); ?>
                <input type="submit" name="move_services" class="button button-primary button-large" 
                       value="Move <?php echo $services_count; ?> Services to Submenu">
            </form>
        </div>
        <?php else: ?>
        <div class="notice notice-error">
            <?php if ($services_count == 0): ?>
                <p><strong>❌ No items found in Services menu (ID: 3680)</strong></p>
            <?php endif; ?>
            <?php if (!$main_services): ?>
                <p><strong>❌ Services parent item not found in main menu</strong></p>
                <p>Please ensure you have a "Services" menu item in your main navigation menu.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($services_menu_items): ?>
        <div class="card">
            <h3>📋 Items in Services Menu (ID: 3680)</h3>
            <div style="max-height: 300px; overflow-y: auto;">
                <ol>
                    <?php foreach ($services_menu_items as $item): ?>
                        <li>
                            <strong><?php echo esc_html($item->title); ?></strong>
                            <br><small>URL: <?php echo esc_html($item->url); ?></small>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>🔗 Quick Links</h3>
            <p>
                <a href="<?php echo admin_url('nav-menus.php?action=edit&menu=3680'); ?>" class="button">Edit Services Menu (ID: 3680)</a>
                <?php if ($main_services): ?>
                <a href="<?php echo admin_url('nav-menus.php?action=edit&menu=' . $main_services['main_menu_id']); ?>" class="button">Edit Main Menu</a>
                <?php endif; ?>
                <a href="<?php echo admin_url('nav-menus.php'); ?>" class="button">Manage All Menus</a>
            </p>
        </div>
    </div>
    <?php
}

?>