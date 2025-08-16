<?php
// Single source of truth loader for services (uses services-data.php)
if (!function_exists('youhealit_get_services')) {
    function youhealit_get_services(): array {
        static $services = null;
        if ($services !== null) return $services;

        $candidates = [
            get_stylesheet_directory() . '/includes/services-data.php',
            get_stylesheet_directory() . '/services-data.php',
            get_template_directory()  . '/includes/services-data.php',
            get_template_directory()  . '/services-data.php',
        ];

        $available_services = [];
        foreach ($candidates as $p) {
if (is_readable($p)) { $available_services = include $p; break; }
        }
        if (!is_array($available_services)) $available_services = [];

        // Cache + legacy global for any old templates
        $GLOBALS['available_services'] = $services = $available_services;
        return $services;
    }
}
