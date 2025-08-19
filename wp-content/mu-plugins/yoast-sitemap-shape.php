<?php
/**
 * Plugin Name: Yoast Sitemap Shape (MU)
 * Description: Keep Yoast XML sitemaps, include every published page, trim noise, and set sane limits.
 * Author: Lenny GPT
 */

// Helpful header so we can verify with curl -I
add_action('send_headers', function () {
    if (isset($_SERVER['REQUEST_URI']) && stripos($_SERVER['REQUEST_URI'], 'sitemap') !== false) {
        header('X-Lenny-Sitemap-Provider: Yoast');
    }
}, 0);

// Only apply if Yoast SEO is active
if (defined('WPSEO_VERSION')) {

    // Bigger page size to reduce fragmentation (Yoast default is 1000 already; keep explicit here)
    add_filter('wpseo_sitemap_entries_per_page', function ($n) { return 1000; });

    /**
     * Exclude unwanted post types from Yoast sitemap.
     * We keep pages, posts, and your custom types. Exclude WP internals + attachments by default.
     */
    add_filter('wpseo_sitemap_exclude_post_type', function ($exclude, $post_type) {
        $ban = [
            'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset',
            'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation'
        ];
        if (in_array($post_type, $ban, true)) return true;
        return $exclude;
    }, 10, 2);

    /**
     * Exclude noisy taxonomies. Keep those you care about for organization (e.g., categories, service-type).
     */
    add_filter('wpseo_sitemap_exclude_taxonomy', function ($exclude, $taxonomy) {
        $ban = ['post_format']; // keep 'category', 'post_tag', 'service-type' by default
        if (in_array($taxonomy, $ban, true)) return true;
        return $exclude;
    }, 10, 2);

    /**
     * Optionally exclude specific object IDs from Yoast sitemaps.
     * Add IDs to the array if you have private/utility pages you don’t want indexed.
     */
    add_filter('wpseo_exclude_from_sitemap_by_post_ids', function ($ids) {
        // return array_merge($ids, [123, 456]);
        return $ids;
    });
}