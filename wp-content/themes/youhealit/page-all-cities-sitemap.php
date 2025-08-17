<?php
/**
 * Template Name: All Cities Sitemap (Cities → Neighborhoods)
 * Description: One-page, DB-backed list of every City linking to its page,
 *              with that City's Neighborhoods listed underneath.
 *              Adds an A–Z bar; hides letters with no cities OR neighborhoods; friendly empty state.
 *              Minimal inline styles + a click spinner to hide perceived latency.
 *
 * Assumptions (adjust slugs if yours differ):
 *  - City CPT slug:         'city'
 *  - Neighborhood taxonomy: 'city-section'
 */

if (!defined('ABSPATH')) exit;

get_header();

$CITY_TYPE = 'city';          // ← change if your CPT differs
$NEIGH_TAX = 'city-section';  // ← change if your neighborhoods taxonomy differs

// --- URL filter for first letter (A–Z) ---
$letter = isset($_GET['letter']) ? strtoupper(substr(sanitize_text_field($_GET['letter']), 0, 1)) : '';
$letter = preg_match('/^[A-Z]$/', $letter) ? $letter : ($letter === '#' ? '#' : '');

// --- Aggressive cache: 30 days by default (filterable) ---
$cache_suffix = $letter ?: 'all';
$cache_key    = 'yhi_all_cities_onepage_v4_' . md5($CITY_TYPE . '|' . $NEIGH_TAX . '|' . $cache_suffix);

// Any param other than 'letter' acts as a cache-buster (your requested "?anything=1")
$param_keys     = array_keys($_GET);
$known_bypass   = ['letter','flush','flush_locations_cache'];
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
  .yhi-sitemap-hero {max-width:1100px;margin:32px auto 10px;padding:0 20px}
  .yhi-sitemap-hero h1 {margin:0 0 6px;font-size:2rem;line-height:1.2}
  .yhi-sitemap-hero p  {margin:0 0 12px;font-size:1rem;opacity:.85}

  .yhi-az {max-width:1100px;margin:6px auto 20px;padding:0 20px;display:flex;gap:6px;flex-wrap:wrap;align-items:center}
  .yhi-az a,.yhi-az span{display:inline-block;padding:6px 8px;border-radius:8px;text-decoration:none}
  .yhi-az a{background:#f3f4f6}
  .yhi-az .on{background:#111827;color:#fff}

  .yhi-sitemap-body {max-width:1100px;margin:8px auto 60px;padding:0 20px}
  .yhi-letter h2 {margin:18px 0 10px;font-size:1.1rem}
  .yhi-city {margin:10px 0 14px;padding:12px 14px;border-radius:10px;background:#fff;box-shadow:0 1px 8px rgba(0,0,0,.06)}
  .yhi-city h3 {margin:0 0 8px;font-size:1.05rem}
  .yhi-neigh-list {margin:0;padding-left:18px;columns:2;column-gap:28px}
  .yhi-neigh-list li {margin:4px 0}
  @media (max-width:900px){ .yhi-neigh-list { columns:1 } }
  .yhi-muted{color:#6b7280}

  /* Spinner overlay shown when clicking a letter (perceived speed) */
  .yhi-spinner{position:fixed;inset:0;display:none;place-items:center;background:rgba(255,255,255,.85);z-index:9999}
  html.yhi-loading .yhi-spinner{display:grid}
  .yhi-spinner:before{content:"";width:42px;height:42px;border:4px solid rgba(17,24,39,.2);border-top-color:#111827;border-radius:50%;animation:yhi-spin .8s linear infinite}
  @keyframes yhi-spin{to{transform:rotate(1turn)}}
</style>

<div class="yhi-spinner" aria-hidden="true"></div>

<section class="yhi-sitemap-hero">
  <h1>All Cities & Neighborhoods We Serve</h1>
  <p>Browse every city we cover. Each city links to its page; neighborhoods are listed right beneath. This page is deliberately simple so it loads fast and stays crawlable.</p>
</section>

<?php
  // Helper to get first-letter bucket (A–Z or '#')
  $first_bucket = function($str) {
      $str = ltrim((string)$str);
      $L = mb_strtoupper(mb_substr($str, 0, 1));
      return preg_match('/^[A-Z]$/u', $L) ? $L : '#';
  };

  // Pull all city IDs once to compute available letters and to render
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
  $available_letters = [];
  foreach (range('A','Z') as $L) { if (!empty($has_letter[$L])) $available_letters[] = $L; }
?>

<nav class="yhi-az" aria-label="Alphabet index">
  <?php
    $base = get_permalink();
    if ($letter) {
      echo '<a href="' . esc_url(remove_query_arg('letter')) . '">All</a>';
    } else {
      echo '<span class="on">All</span>';
    }
    // Only print letters that actually have at least one City OR Neighborhood
    foreach ($available_letters as $L) {
      $url = add_query_arg(['letter' => $L], $base);
      $on  = ($letter === $L);
      echo $on ? '<span class="on">' . esc_html($L) . '</span>' : '<a href="' . esc_url($url) . '">' . esc_html($L) . '</a>';
    }
    if ($has_other) {
      $url = add_query_arg(['letter' => '#'], $base);
      echo ($letter === '#') ? '<span class="on">#</span>' : '<a href="' . esc_url($url) . '">#</a>';
    }
  ?>
</nav>

<section class="yhi-sitemap-body" aria-label="Cities and neighborhoods">
<?php
  // If a specific letter is chosen, filter to cities where either city title OR any neighborhood starts with that letter
  $city_ids = $all_ids;
  if ($letter && $city_ids) {
    $city_ids = array_values(array_filter($city_ids, function($cid) use ($letter, $first_bucket, $city_terms_map){
      $city_bucket = $first_bucket(get_the_title($cid));
      $match_city  = ($letter === '#') ? ($city_bucket === '#') : ($city_bucket === $letter);

      $match_term = false;
      if (!empty($city_terms_map[$cid])) {
        foreach ($city_terms_map[$cid] as $term) {
          $tb = $first_bucket($term->name);
          if ($letter === '#') { if ($tb === '#') { $match_term = true; break; } }
          else { if ($tb === $letter) { $match_term = true; break; } }
        }
      }
      return $match_city || $match_term;
    }));
  }

  if (!$city_ids) {
    if ($letter) {
      echo '<p class="yhi-muted">Sorry, we have no listings starting with ' . esc_html($letter) . '.</p>';
    } else {
      echo '<p class="yhi-muted">No cities found.</p>';
    }
  } else {
    // When no letter filter: group output by first letter of the CITY only
    // (This keeps grouping stable; the A–Z nav already considers neighborhoods.)
    if (!$letter) {
      $groups = [];
      foreach ($city_ids as $cid) {
        $key = $first_bucket(get_the_title($cid));
        $groups[$key][] = $cid;
      }
      ksort($groups);

      foreach ($groups as $L => $ids) {
        $anchor = ($L === '#') ? 'other' : $L;
        echo '<div class="yhi-letter">';
        echo '<h2 id="az-' . esc_attr($anchor) . '">' . esc_html($L) . '</h2>';
        foreach ($ids as $cid) {
          $title = get_the_title($cid);
          $link  = get_permalink($cid);
          echo '<article class="yhi-city">';
          echo '<h3><a href="' . esc_url($link) . '">' . esc_html($title ?: '(no title)') . '</a></h3>';
          // Neighborhood terms under this city
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
        echo '</div>';
      }

    } else {
      // Letter filtered: render the flat list for that letter (cities matched by city OR neighborhood letter)
      foreach ($city_ids as $cid) {
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
    }
  }
?>
</section>

<script>
  // Show spinner immediately on A–Z click to improve perceived latency
  document.addEventListener('click', function(e){
    var a = e.target.closest('.yhi-az a');
    if (!a) return;
    document.documentElement.classList.add('yhi-loading');
  }, {passive:true});
  // Safety: if page is restored from bfcache, remove the class
  window.addEventListener('pageshow', function(){
    document.documentElement.classList.remove('yhi-loading');
  });
</script>

<?php
$html = ob_get_clean();

// 30 days, filterable (minimum 6 hours safety)
$ttl = (int) apply_filters('yhi_all_cities_cache_ttl', 30 * DAY_IN_SECONDS);
set_transient($cache_key, $html, max(6 * HOUR_IN_SECONDS, $ttl));

echo $html;

wp_reset_postdata();
get_footer();
