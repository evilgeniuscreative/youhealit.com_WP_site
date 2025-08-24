<?php
// Create this file as test-acf.php in your active theme folder
// Then visit: yoursite.com/wp-content/themes/youhealit/test-acf.php

echo "<h1>ACF Test</h1>";

// Check if ACF files exist
$acf_path = ABSPATH . 'wp-content/plugins/advanced-custom-fields/acf.php';
echo "<p>ACF file exists: " . (file_exists($acf_path) ? "YES" : "NO") . "</p>";
echo "<p>ACF path: " . $acf_path . "</p>";

// Try to include ACF manually and catch errors
if (file_exists($acf_path)) {
    echo "<p>Attempting to include ACF...</p>";
    try {
        include_once($acf_path);
        echo "<p>✅ ACF included successfully!</p>";
        
        // Check if ACF functions exist
        echo "<p>have_rows function exists: " . (function_exists('have_rows') ? "YES" : "NO") . "</p>";
        echo "<p>get_field function exists: " . (function_exists('get_field') ? "YES" : "NO") . "</p>";
        
    } catch (Throwable $e) {
        echo "<p>❌ Error including ACF: " . $e->getMessage() . "</p>";
        echo "<p>Error file: " . $e->getFile() . ":" . $e->getLine() . "</p>";
    }
} else {
    echo "<p>❌ ACF file not found</p>";
}

// Check for function conflicts
$functions_to_check = ['have_rows', 'the_row', 'get_sub_field', 'get_field', 'acf'];
foreach ($functions_to_check as $func) {
    if (function_exists($func)) {
        echo "<p>⚠️  Function '{$func}' already exists!</p>";
    } else {
        echo "<p>✅ Function '{$func}' is available</p>";
    }
}

// Check PHP version and requirements
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>WordPress Version: " . get_bloginfo('version') . "</p>";
?>