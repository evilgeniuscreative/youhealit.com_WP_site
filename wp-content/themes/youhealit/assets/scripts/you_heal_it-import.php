<?php
// Replace your youhealit_import_csv_data_fixed() function with this batch version

function youhealit_import_csv_data_batch() {
    $csv_file = get_template_directory() . '/assets/big.csv';
    
    if (!file_exists($csv_file)) {
        echo '<div class="notice notice-error"><p>CSV file not found.</p></div>';
        return;
    }

    // Get batch parameters
    $batch_size = isset($_POST['batch_size']) ? (int)$_POST['batch_size'] : 50;
    $start_row = isset($_POST['start_row']) ? (int)$_POST['start_row'] : 0;
    $total_imported = isset($_POST['total_imported']) ? (int)$_POST['total_imported'] : 0;
    
    $handle = fopen($csv_file, 'r');
    $header = fgetcsv($handle);
    
    // Count total rows if this is the first batch
    if ($start_row === 0) {
        $total_rows = 0;
        while (fgetcsv($handle) !== FALSE) {
            $total_rows++;
        }
        rewind($handle);
        fgetcsv($handle); // Skip header again
        
        echo '<div class="notice notice-info">';
        echo '<h3>🚀 Starting Batch Import</h3>';
        echo '<p><strong>Total rows to process:</strong> ' . $total_rows . '</p>';
        echo '<p><strong>Batch size:</strong> ' . $batch_size . ' rows per batch</p>';
        echo '<p><strong>Estimated batches:</strong> ' . ceil($total_rows / $batch_size) . '</p>';
        echo '</div>';
    } else {
        // Skip to the starting row
        for ($i = 0; $i < $start_row; $i++) {
            if (fgetcsv($handle) === FALSE) break;
        }
    }
    
    $imported = 0;
    $skipped = 0;
    $errors = 0;
    $processed_in_batch = 0;
    
    echo '<div class="notice notice-info">';
    echo '<h3>📦 Processing Batch ' . (floor($start_row / $batch_size) + 1) . '</h3>';
    echo '<div id="batch-progress">';
    
    // Process one batch
    while (($data = fgetcsv($handle)) !== FALSE && $processed_in_batch < $batch_size) {
        $processed_in_batch++;
        $row = array_combine($header, $data);
        
        // Skip if essential data is missing
        if (empty($row['city_name'])) {
            $skipped++;
            continue;
        }
        
        // Clean and prepare data
        $city_name = trim($row['city_name'] ?? '');
        $city_section = trim($row['city_section_name'] ?? '');
        $city_zip = trim($row['city_zip'] ?? '');
        $city_text = $row['city_text'] ?? '';
        $city_headline = $row['city_headline'] ?? '';
        $city_subhead = $row['city_subhead'] ?? '';
        $youhealit_page = $row['youhealit_page'] ?? '';
        $lat = $row['lat'] ?? '';
        $lon = $row['lon'] ?? '';
        $wikimedia_page = $row['wikimedia_page'] ?? '';
        
        // Create proper post title and slug
        if (!empty($city_section)) {
            $post_title = $city_section . ', ' . $city_name;
            $post_slug = sanitize_title($city_name . '-' . $city_zip . '-' . $city_section);
        } else {
            $post_title = $city_name . ($city_zip ? ' ' . $city_zip : '');
            $post_slug = sanitize_title($city_name . '-' . $city_zip);
        }
        
        // Check if already exists
        $existing_post = get_page_by_title($post_title, OBJECT, 'city');
        if ($existing_post) {
            $skipped++;
            continue;
        }
        
        // Create post
        $city_post = array(
            'post_title' => $post_title,
            'post_content' => $city_text,
            'post_status' => 'publish',
            'post_type' => 'city',
            'post_name' => $post_slug,
            'post_excerpt' => !empty($city_headline) ? $city_headline : substr(strip_tags($city_text), 0, 150)
        );
        
        $post_id = wp_insert_post($city_post, true);
        
        if ($post_id && !is_wp_error($post_id)) {
            // Add metadata with verification
            $meta_fields = array(
                'city_name' => $city_name,
                'city_zip' => $city_zip,
                'city_section_name' => $city_section,
                'city_headline' => $city_headline,
                'city_subhead' => $city_subhead,
                'youhealit_page' => $youhealit_page,
                'lat' => $lat,
                'lon' => $lon,
                'wikimedia_page' => $wikimedia_page
            );
            
            $metadata_success = true;
            foreach ($meta_fields as $meta_key => $meta_value) {
                if (!empty($meta_value)) {
                    $result = add_post_meta($post_id, $meta_key, $meta_value, true);
                    if (!$result) {
                        $metadata_success = false;
                    }
                }
            }
            
            if ($metadata_success) {
                $imported++;
            } else {
                $errors++;
            }
        } else {
            $errors++;
        }
        
        // Show progress every 10 records
        if ($processed_in_batch % 10 == 0) {
            echo '<p>Processed ' . $processed_in_batch . '/' . $batch_size . ' - Current: ' . esc_html($post_title) . '</p>';
            flush();
        }
    }
    
    $new_start_row = $start_row + $processed_in_batch;
    $new_total_imported = $total_imported + $imported;
    $current_db_count = wp_count_posts('city')->publish;
    
    echo '</div>';
    echo '<h4>✅ Batch Complete!</h4>';
    echo '<ul>';
    echo '<li><strong>This Batch:</strong> ' . $imported . ' imported, ' . $skipped . ' skipped, ' . $errors . ' errors</li>';
    echo '<li><strong>Total Imported So Far:</strong> ' . $new_total_imported . '</li>';
    echo '<li><strong>Database Count:</strong> ' . $current_db_count . '</li>';
    echo '<li><strong>Next Starting Row:</strong> ' . $new_start_row . '</li>';
    echo '</ul>';
    
    // Check if there are more rows to process
    $more_data = (fgetcsv($handle) !== FALSE);
    fclose($handle);
    
    if ($more_data) {
        // More batches to process
        echo '<h4>🔄 Continue to Next Batch?</h4>';
        echo '<form method="post" style="margin: 10px 0;">';
        echo '<input type="hidden" name="action" value="batch_import">';
        echo '<input type="hidden" name="start_row" value="' . $new_start_row . '">';
        echo '<input type="hidden" name="total_imported" value="' . $new_total_imported . '">';
        echo '<input type="hidden" name="batch_size" value="' . $batch_size . '">';
        echo '<button type="submit" class="button button-primary">Continue Import (Next ' . $batch_size . ' records)</button>';
        echo '</form>';
        
        echo '<p><em>You can safely stop here and continue later, or keep going!</em></p>';
    } else {
        // Import complete
        echo '<h3>🎉 Import Complete!</h3>';
        echo '<p><strong>Total Records Imported:</strong> ' . $new_total_imported . '</p>';
        echo '<p><strong>Final Database Count:</strong> ' . $current_db_count . '</p>';
        
        // Final verification
        $test_posts = get_posts(array('post_type' => 'city', 'posts_per_page' => 3, 'orderby' => 'rand'));
        if ($test_posts) {
            echo '<h4>🔍 Final Verification (Random Sample):</h4>';
            echo '<ul>';
            foreach ($test_posts as $test_post) {
                $city_name = get_post_meta($test_post->ID, 'city_name', true);
                $city_zip = get_post_meta($test_post->ID, 'city_zip', true);
                $status = (!empty($city_name)) ? '✅' : '❌';
                echo '<li>' . $status . ' <strong>' . $test_post->post_title . ':</strong> City=' . $city_name . ', ZIP=' . $city_zip . '</li>';
            }
            echo '</ul>';
        }
        
        flush_rewrite_rules();
    }
    
    echo '</div>';
}

// Update the CSV import page to include batch options
function youhealit_csv_import_page_with_batch() {
    ?>
    <div class="wrap">
        <h1>CSV Import Manager - Smart Batch Processing</h1>
        
        <?php
        // Handle form submissions
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'check_file':
                    youhealit_check_csv_file();
                    break;
                case 'preview_data':
                    youhealit_preview_csv_data();
                    break;
                case 'complete_cleanup':
                    youhealit_complete_database_cleanup();
                    break;
                case 'batch_import':
                    youhealit_import_csv_data_batch();
                    break;
            }
        }
        
        // Display current status
        youhealit_display_import_status();
        ?>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;">
            
            <!-- File Check Section -->
            <div class="postbox">
                <div class="postbox-header"><h2>Step 1: Check CSV File</h2></div>
                <div class="inside">
                    <p>Upload your <code>big.csv</code> to: <br><code>/wp-content/themes/youhealit/assets/big.csv</code></p>
                    <form method="post">
                        <input type="hidden" name="action" value="check_file">
                        <button type="submit" class="button button-secondary">Check File Status</button>
                    </form>
                </div>
            </div>
            
            <!-- Preview Section -->
            <div class="postbox">
                <div class="postbox-header"><h2>Step 2: Preview Data</h2></div>
                <div class="inside">
                    <p>Preview the first 5 rows to verify data structure.</p>
                    <form method="post">
                        <input type="hidden" name="action" value="preview_data">
                        <button type="submit" class="button button-secondary">Preview CSV Data</button>
                    </form>
                </div>
            </div>
            
            <!-- Batch Import Section -->
            <div class="postbox">
                <div class="postbox-header"><h2>🚀 Smart Batch Import</h2></div>
                <div class="inside">
                    <p><strong>Recommended:</strong> Import in small, manageable batches to avoid server timeouts.</p>
                    <form method="post">
                        <input type="hidden" name="action" value="batch_import">
                        <input type="hidden" name="start_row" value="0">
                        <input type="hidden" name="total_imported" value="0">
                        <p>
                            <label>Batch Size: </label>
                            <select name="batch_size">
                                <option value="25">25 records (Safest)</option>
                                <option value="50" selected>50 records (Recommended)</option>
                                <option value="100">100 records (Faster)</option>
                                <option value="200">200 records (Aggressive)</option>
                            </select>
                        </p>
                        <button type="submit" class="button button-primary">Start Batch Import</button>
                    </form>
                    <p><em>You can stop and resume at any time!</em></p>
                </div>
            </div>
            
            <!-- Complete Reset Section -->
            <div class="postbox">
                <div class="postbox-header"><h2>🗑️ Complete Reset</h2></div>
                <div class="inside">
                    <p><strong>Clean Slate:</strong> Delete ALL cities if you need to start over.</p>
                    <form method="post" onsubmit="return confirm('Delete ALL cities and start over?');">
                        <input type="hidden" name="action" value="complete_cleanup">
                        <button type="submit" class="button" style="background: #dc3232; color: white;">Complete Reset</button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
    <?php
}
?>