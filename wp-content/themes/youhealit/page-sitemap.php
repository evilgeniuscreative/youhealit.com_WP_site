<?php
/**
 * Template Name: HTML Sitemap
 * Description: Human-friendly sitemap that lists ALL published pages by hierarchy
 *              and groups content by selected taxonomies (Sections).
 *              Minimal styles to blend with theme.
 */

if (!defined('ABSPATH')) { exit; }

get_header();

// ---------- Config ----------
$taxonomies_to_show = [ 'category', 'service-type', 'service-category' ]; // add/remove as you like
$max_posts_per_term = 1000; // large so we effectively show all

// Build a set of public, non-noisy post types to list under taxonomies
$public_types = get_post_types([ 'public' => true ], 'names');
unset($public_types['attachment']); // attachments rarely belong in human sitemaps
$public_types = array_values($public_types);

?>

<style>
/***** Lightweight, theme-friendly styling *****/
.sitemap-wrap { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
.sitemap-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
.sitemap-card { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.06); padding: 22px; }
.sitemap-card h2 { margin: 0 0 16px; font-size: 1.25rem; }
.sitemap-card h3 { margin: 18px 0 10px; font-size: 1.05rem; }
.sitemap-card ul { margin: 0; padding-left: 18px; }
.sitemap-card li { margin: 6px 0; }
.sitemap-muted { color: #6b7280; font-size: .9rem; margin: 8px 0 0; }
@media (max-width: 900px) { .sitemap-grid { grid-template-columns: 1fr; } }
</style>

<main class="sitemap-wrap" aria-labelledby="sitemap-title">
  <h1 id="sitemap-title"><?php echo esc_html( get_the_title() ?: 'Sitemap' ); ?></h1>
  <p class="sitemap-muted">This is a human-readable map of the site. For search engines, the XML index is at <code>/sitemap_index.xml</code>.</p>

  <div class="sitemap-grid">

    <!-- PAGES by hierarchy -->
    <section class="sitemap-card" aria-labelledby="sitemap-pages">
      <h2 id="sitemap-pages">Pages (by hierarchy)</h2>
      <?php
      $pages = get_pages([
        'post_status' => 'publish',
        'sort_column' => 'menu_order,post_title',
        'hierarchical' => true,
      ]);
      $by_parent = [];
      foreach ($pages as $p) { $by_parent[$p->post_parent][] = $p; }
      $render = function($parent_id) use (&$render, $by_parent) {
        if (empty($by_parent[$parent_id])) return;
        echo '<ul>';
        foreach ($by_parent[$parent_id] as $page) {
          printf('<li><a href="%s">%s</a>', esc_url(get_permalink($page)), esc_html($page->post_title ?: '(no title)'));
          $render($page->ID);
          echo '</li>';
        }
        echo '</ul>';
      };
      $render(0);
      ?>
    </section>

    <!-- SECTIONS (Taxonomies) -->
    <section class="sitemap-card" aria-labelledby="sitemap-sections">
      <h2 id="sitemap-sections">Sections</h2>
      <?php
      foreach ($taxonomies_to_show as $tx) {
        $t_obj = get_taxonomy($tx);
        if (!$t_obj || empty($t_obj->public)) continue;
        $terms = get_terms([ 'taxonomy' => $tx, 'hide_empty' => true ]);
        if (is_wp_error($terms) || empty($terms)) continue;

        printf('<h3>%s</h3>', esc_html($t_obj->labels->name));
        echo '<ul class="sitemap-tax sitemap-tax-' . esc_attr($tx) . '">';
        foreach ($terms as $term) {
          echo '<li><strong>' . esc_html($term->name) . '</strong>';

          // Pull posts across public types for this term
          $posts = get_posts([
            'post_type'      => $public_types,
            'post_status'    => 'publish',
            'posts_per_page' => $max_posts_per_term,
            'tax_query'      => [[
              'taxonomy' => $tx,
              'field'    => 'term_id',
              'terms'    => $term->term_id,
            ]],
            'orderby' => 'title',
            'order'   => 'ASC',
          ]);

          if ($posts) {
            echo '<ul>';
            foreach ($posts as $post) {
              printf('<li><a href="%s">%s</a></li>', esc_url(get_permalink($post)), esc_html(get_the_title($post) ?: '(no title)'));
            }
            echo '</ul>';
          }
          echo '</li>';
        }
        echo '</ul>';
      }
      ?>
    </section>

  </div>
</main>

<?php get_footer();
