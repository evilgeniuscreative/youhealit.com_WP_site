<?php
/**
 * Template Name: All Cities Sitemap (Cities → Neighborhoods)
 * Description: Single-render, anchor-only A–Z sitemap. One long cached page with
 *              City → Neighborhoods. A–Z bar (sticky) links to in-page anchors.
 *              Letters with no matches are dimmed (30%) and not clickable.
 *              If `?letter=X` is present, we auto-scroll/highlight the section.
 *              Minimal inline styles to avoid FOUC. Aggressively cached.
 *
 * Assumptions (adjust slugs if yours differ):
 *  - City CPT slug:         'city'
 *  - Neighborhood taxonomy: 'city-section'
 */

if (!defined('ABSPATH')) exit;

get_header();

$CITY_TYPE = 'city';          // ← change if your CPT differs
$NEIGH_TAX = 'city-section';  // ← change if your neighborhoods taxonomy differs

// --- We no longer server-filter by letter; we render ALL once. ---
$letter_param = isset($_GET['letter']) ? strtoupper(substr(sanitize_text_field($_GET['letter']), 0, 1)) : '';
$letter_param = preg_match('/^[A-Z]$/', $letter_param) ? $letter_param : ($letter_param === '#' ? '#' : '');

// --- Aggressive cache: 30 days by default (filterable) ---
$cache_key = 'yhi_all_cities_onepage_v6_all_' . md5($CITY_TYPE . '|' . $NEIGH_TAX);

// Any param (other than benign tracking and 'letter') acts as a cache-buster
$param_keys     = array_keys($_GET);
$known_bypass   = ['letter','flush','flush_locations_cache','utm_source','utm_medium','utm_campaign'];
$unknown_params = array_diff($param_keys, $known_bypass);
$bust_cache     = isset($_GET['flush']) || isset($_GET['flush_locations_cache']) || !empty($unknown_params);

if ($bust_cache) { delete_transient($cache_key); }

if (!$bust_cache) {
    if ($cached = get_transient($cache_key)) { echo $cached; get_footer(); return; }
}

ob_start();
?>

<style>
  /* Minimal, inlined so the top never flashes unstyled */
  html { scroll-behavior:smooth }
  .yhi-sitemap-hero {max-width:1100px;margin:32px auto 10px;padding:0 20px}
  .yhi-sitemap-hero h1 {margin:0 0 6px;font-size:2rem;line-height:1.2}
  .yhi-sitemap-hero p  {margin:0 0 12px;font-size:1rem;opacity:.85}
  .yhi-msg {max-width:1100px;margin:8px auto 0;padding:0 20px;color:#6b7280}

  .yhi-az-wrap {position:sticky; top:0; z-index:50; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,.06)}
  .yhi-az {max-width:1100px;margin:0 auto; padding:10px 20px; display:flex; gap:6px; flex-wrap:wrap; align-items:center}
  .yhi-az a,.yhi-az span{display:inline-block;padding:6px 8px;border-radius:8px;text-decoration:none}
  .yhi-az a{background:#f3f4f6}
  .yhi-az .on{background:#111827;color:#fff}
  .yhi-az .off{opacity:.3; cursor:not-allowed; background:#f3f4f6}

  .yhi-sitemap-body {max-width:1100px;margin:8px auto 60px;padding:0 20px}
  .yhi-letter h2 {margin:18px 0 10px;font-size:1.1rem}
  .yhi-city {margin:10px 0 14px;padding:12px 14px;border-radius:10px;background:#fff;box-shadow:0 1px 8px rgba(0,0,0,.06)}
  .yhi-city h3 {margin:0 0 8px;font-size:1.05rem}
  .yhi-neigh-list {margin:0;padding-left:18px;columns:2;column-gap:28px}
  .yhi-neigh-list li {margin:4px 0}
  @media (max-width:900px){ .yhi-neigh-list { columns:1 } }
  .yhi-muted{color:#6b7280}
  .yhi-top {margin:8px 0 0}
  .yhi-top a{font-size:.9rem; opacity:.8; text-decoration:none}

  /* Section highlight after instant-jump */
  .yhi-hit{animation:yhi-flash 1s ease-out}
  @keyframes yhi-flash{0%{background:rgba(255,244,163,.9)} 100%{background:transparent}}
</style>

<section class="yhi-sitemap-hero" id="yhi-top">
  <h1>All Cities & Neighborhoods We Serve</h1>
  <p>Browse every city we cover. Each city links to its page; neighborhoods are listed right beneath. This page is deliberately simple so it loads fast and stays crawlable.</p>
</section>
<div class="yhi-msg" id="yhi-msg" aria-live="polite"></div>

<?php
  // Helper to get first-letter bucket (A–Z or '#')
  $first_bucket = function($str) {
      $str = ltrim((string)$str);
      $L = mb_strtoupper(mb_substr($str, 0, 1));
      return preg_match('/^[A-Z]$/u', $L) ? $L : '#';
  };

  // Pull all city IDs once
  $q_all = new WP_Query([
    'post_type'      => $CITY_TYPE,
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'fields'         => 'ids',
    'no_found_rows'  => true,
  ]);
  $all_ids = $q_all->posts;

  // Preload neighborhood terms map and compute available letters from BOTH cities and neighborhoods
  $has_letter = array_fill_keys(range('A','Z'), false);
  $has_other  = false; // any non A–Z first char across cities OR neighborhoods
  $city_terms_map = [];

  foreach ($all_ids as $cid) {
    // City title bucket
    $bucket = $first_bucket(get_the_title($cid));
    if ($bucket === '#') $has_other = true; else $has_letter[$bucket] = true;

    // Neighborhoods for this city
    $terms = taxonomy_exists($NEIGH_TAX) ? get_the_terms($cid, $NEIGH_TAX) : [];
    if (!is_array($terms) || is_wp_error($terms)) $terms = [];
    $city_terms_map[$cid] = $terms;

    foreach ($terms as $term) {
      $tb = $first_bucket($term->name);
      if ($tb === '#') $has_other = true; else $has_letter[$tb] = true;
    }
  }

  // Build groups by CITY first letter for rendering
  $groups = [];
  foreach ($all_ids as $cid) {
    $key = $first_bucket(get_the_title($cid));
    $groups[$key][] = $cid;
  }
  ksort($groups);
?>

<div class="yhi-az-wrap">
  <nav class="yhi-az" aria-label="Alphabet index">
    <?php
      // Always show All (current view)
      echo '<span class="on">All</span>';

      // A–Z tiles: show all letters, dim (30%) if no matches; link to in-page anchors if present
      foreach (range('A','Z') as $L) {
        if (!empty($has_letter[$L])) {
          echo '<a href="#az-' . esc_attr($L) . '" data-letter="' . esc_attr($L) . '">' . esc_html($L) . '</a>';
        } else {
          echo '<span class="off">' . esc_html($L) . '</span>';
        }
      }
      if ($has_other) {
        echo '<a href="#az-other" data-letter="#">#</a>';
      }
    ?>
  </nav>
</div>

<section class="yhi-sitemap-body" aria-label="Cities and neighborhoods">
<?php
  foreach ($groups as $L => $ids) {
    $anchor = ($L === '#') ? 'other' : $L;
    echo '<div class="yhi-letter">';
    echo '<h2 id="az-' . esc_attr($anchor) . '">' . esc_html($L) . '</h2>';
    foreach ($ids as $cid) {
      $title = get_the_title($cid);
      $link  = get_permalink($cid);
      echo '<article class="yhi-city">';
      echo '<h3><a href="' . esc_url($link) . '">' . esc_html($title ?: '(no title)') . '</a></h3>';
      $terms = $city_terms_map[$cid] ?? [];
      if ($terms) {
        usort($terms, function($a,$b){ return strcasecmp($a->name, $b->name); });
        echo '<ul class="yhi-neigh-list">';
        foreach ($terms as $t) {
          $t_link = get_term_link($t);
          if (!is_wp_error($t_link)) {
            echo '<li><a href="' . esc_url($t_link) . '">' . esc_html($t->name) . '</a></li>';
          }
        }
        echo '</ul>';
      } else {
        echo '<p style="margin:0;opacity:.7">No neighborhoods listed.</p>';
      }
      echo '</article>';
    }
    echo '<div class="yhi-top"><a href="#yhi-top">Back to top ↑</a></div>';
    echo '</div>';
  }
?>
</section>

<script>
  (function(){
    // If a ?letter=X param is present, auto-scroll to that section and show a friendly message if it doesn't exist
    var params = new URLSearchParams(location.search);
    var L = params.get('letter');
    if (L) {
      var id = 'az-' + (L === '#' ? 'other' : L.toUpperCase());
      var target = document.getElementById(id);
      if (target) {
        target.classList.add('yhi-hit');
        target.scrollIntoView({behavior:'smooth', block:'start'});
        setTimeout(function(){ target.classList.remove('yhi-hit'); }, 1200);
      } else {
        var msg = document.getElementById('yhi-msg');
        if (msg) msg.textContent = 'Sorry, we have no listings starting with ' + L.toUpperCase() + '.';
      }
    }

    // Also highlight when clicking an A–Z tile
    document.addEventListener('click', function(e){
      var a = e.target.closest('.yhi-az a[data-letter]');
      if (!a) return;
      var L = a.getAttribute('data-letter');
      var id = 'az-' + (L === '#' ? 'other' : L);
      var target = document.getElementById(id);
      if (target) {
        target.classList.add('yhi-hit');
        setTimeout(function(){ target.classList.remove('yhi-hit'); }, 1200);
      }
    }, {passive:true});
  })();
</script>

<?php
$html = ob_get_clean();

// 30 days, filterable (minimum 6 hours safety)
$ttl = (int) apply_filters('yhi_all_cities_cache_ttl', 30 * DAY_IN_SECONDS);
set_transient($cache_key, $html, max(6 * HOUR_IN_SECONDS, $ttl));

echo $html;

wp_reset_postdata();
get_footer();
