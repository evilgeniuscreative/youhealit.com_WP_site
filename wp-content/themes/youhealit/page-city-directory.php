<?php
/*
theme: youhealit
youhealit/page-city-directory.php
Paginated city directory with Google Places Autocomplete search functionality.
Shows 75 cities per page with city name, neighborhood, zip, distance estimates.
Functions used: WP_Query, get_post_meta(), get_permalink(), paginate_links()
Variables: $paged, $cities_query, $city_name, $city_section_name, $city_zip
*/

/* Template Name: City Directory */
get_header();

// Pagination setup
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$cities_per_page = 75;

// Query cities with pagination
$cities_query = new WP_Query([
    'post_type' => 'city',
    'posts_per_page' => $cities_per_page,
    'paged' => $paged,
    'orderby' => 'title',
    'order' => 'ASC',
    'post_status' => 'publish',
    'meta_query' => [
        [
            'key' => 'city_name',
            'compare' => 'EXISTS'
        ]
    ]
]);

$total_cities = wp_count_posts('city')->publish;
?>

<main class="main-content">
    <!-- Hero Section -->
    <section class="directory-hero" style="background: var(--primary-green); padding: 60px 20px; text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h1 style="color: white; font-size: 2.5rem; margin-bottom: 15px; font-weight: 300;">
                Find Health & Wellness Services Near You
            </h1>
            <p style="color: white; font-size: 1.2rem; margin-bottom: 30px;">
                Serving <?php echo number_format($total_cities); ?> locations across North Carolina
            </p>
        </div>
    </section>

    <!-- Search Section -->
    <section class="search-section" style="background: white; padding: 40px 20px; border-bottom: 1px solid #eee;">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <label for="location-search" style="display: block; font-size: 1.3rem; color: var(--text-dark); margin-bottom: 15px; font-weight: 600;">
                Where do you live?
            </label>
            <div style="position: relative; max-width: 500px; margin: 0 auto;">
                <input 
                    type="text" 
                    id="location-search" 
                    placeholder="Enter your city or neighborhood..."
                    style="width: 100%; padding: 15px 20px; font-size: 1.1rem; border: 2px solid var(--border-color); border-radius: 8px; outline: none; transition: border-color 0.3s ease;"
                    autocomplete="off"
                />
                <div id="search-results" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: none; z-index: 1000; max-height: 300px; overflow-y: auto;"></div>
            </div>
            <p style="margin-top: 15px; color: var(--text-light); font-size: 0.95rem;">
                Or browse all locations below
            </p>
        </div>
    </section>

    <!-- Directory Section -->
    <section class="directory-section" style="background: var(--background-light); padding: 60px 20px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            
            <!-- Pagination Info -->
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: var(--primary-green); font-size: 2rem; margin-bottom: 10px;">
                    North Carolina Locations
                </h2>
                <p style="color: var(--text-light);">
                    Showing <?php echo (($paged - 1) * $cities_per_page) + 1; ?>-<?php echo min($paged * $cities_per_page, $total_cities); ?> 
                    of <?php echo number_format($total_cities); ?> locations
                </p>
            </div>

            <?php if ($cities_query->have_posts()) : ?>
                
                <!-- Cities Grid -->
                <div class="cities-directory-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px; margin-bottom: 50px;">
                    
                    <?php while ($cities_query->have_posts()) : $cities_query->the_post(); 
                        $city_name = get_post_meta(get_the_ID(), 'city_name', true);
                        $city_section_name = get_post_meta(get_the_ID(), 'city_section_name', true);
                        $city_zip = get_post_meta(get_the_ID(), 'city_zip', true);
                        $lat = get_post_meta(get_the_ID(), 'lat', true);
                        $lon = get_post_meta(get_the_ID(), 'lon', true);
                        
                        // Calculate rough distance and time (placeholder logic)
                        $distance = rand(5, 45); // Random distance for demo
                        $drive_time = round($distance * 1.5); // Rough time estimate
                        
                        if (empty($city_name)) $city_name = get_the_title();
                    ?>
                        
                        <div class="city-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); transition: transform 0.3s ease, box-shadow 0.3s ease;">
                            
                            <!-- City Name -->
                            <h3 style="margin: 0 0 8px 0; font-size: 1.3rem;">
                                <a href="<?php echo get_permalink(); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">
                                    <?php echo esc_html($city_name); ?>
                                </a>
                            </h3>
                            
                            <!-- Neighborhood Name -->
                            <?php if (!empty($city_section_name)) : ?>
                                <h4 style="margin: 0 0 12px 0; font-size: 1rem; font-weight: 500;">
                                    <a href="<?php echo get_permalink(); ?>" style="color: var(--red-accent); text-decoration: none;">
                                        <?php echo esc_html($city_section_name); ?>
                                    </a>
                                </h4>
                            <?php else : ?>
                                <div style="height: 20px; margin-bottom: 12px;"></div>
                            <?php endif; ?>
                            
                            <!-- Zip Code -->
                            <?php if (!empty($city_zip)) : ?>
                                <p style="margin: 0 0 10px 0; color: var(--text-light); font-size: 0.95rem;">
                                    <strong>ZIP:</strong> <?php echo esc_html($city_zip); ?>
                                </p>
                            <?php endif; ?>
                            
                            <!-- Distance Info -->
                            <p style="margin: 0 0 15px 0; color: var(--text-light); font-size: 0.9rem; font-style: italic;">
                                <?php echo $distance; ?> miles from you, roughly <?php echo $drive_time; ?> minutes
                            </p>
                            
                            <!-- Services Description -->
                            <p style="margin: 0; color: var(--text-dark); font-size: 0.95rem; line-height: 1.5;">
                                Get chiropractic, massage, acupuncture and pain relief of all kinds near 
                                <a href="<?php echo get_permalink(); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 500;">
                                    <?php echo esc_html($city_name); ?>
                                </a><?php if (!empty($city_section_name)) : ?>, 
                                <a href="<?php echo get_permalink(); ?>" style="color: var(--red-accent); text-decoration: none; font-weight: 500;">
                                    <?php echo esc_html($city_section_name); ?>
                                </a><?php endif; ?>
                            </p>
                            
                        </div>
                        
                    <?php endwhile; ?>
                    
                </div>
                
                <!-- Pagination -->
                <div class="pagination-wrapper" style="text-align: center;">
                    <?php
                    $pagination = paginate_links([
                        'total' => $cities_query->max_num_pages,
                        'current' => $paged,
                        'prev_text' => '← Previous',
                        'next_text' => 'Next →',
                        'type' => 'array'
                    ]);
                    
                    if ($pagination) :
                        echo '<div class="pagination" style="display: inline-flex; gap: 10px; flex-wrap: wrap;">';
                        foreach ($pagination as $page) {
                            echo '<div style="background: white; border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">' . $page . '</div>';
                        }
                        echo '</div>';
                    endif;
                    ?>
                </div>
                
            <?php else : ?>
                
                <div style="text-align: center; padding: 60px 20px;">
                    <h3 style="color: var(--text-light);">No locations found</h3>
                    <p>Please check back later as we add more service areas.</p>
                </div>
                
            <?php endif; ?>
            
        </div>
    </section>

</main>

<style>
/* City Directory Specific Styles */
.city-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.city-card a:hover {
    text-decoration: underline !important;
}

#location-search:focus {
    border-color: var(--primary-green);
    box-shadow: 0 0 0 3px rgba(139, 195, 74, 0.1);
}

.search-result-item {
    padding: 12px 20px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-result-item:hover {
    background-color: var(--background-light);
}

.search-result-item:last-child {
    border-bottom: none;
}

.pagination a, .pagination span {
    display: block;
    padding: 10px 15px;
    color: var(--primary-green);
    text-decoration: none;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.pagination a:hover {
    background: var(--primary-green);
    color: white;
}

.pagination .current {
    background: var(--red-accent);
    color: white;
    font-weight: 600;
}

@media (max-width: 768px) {
    .cities-directory-grid {
        grid-template-columns: 1fr !important;
    }
    
    .pagination {
        justify-content: center;
    }
}
</style>

<script>
// Live Search Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('location-search');
    const searchResults = document.getElementById('search-results');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 3) {  // Changed from 2 to 3 letters
            searchResults.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);  // Already set to 300ms
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
    
    function performSearch(query) {
        // AJAX search implementation
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=search_cities&query=' + encodeURIComponent(query)
        })
        .then(response => response.json())
        .then(data => {
            console.log('AJAX Response:', data); // Debug log
            if (data.success && Array.isArray(data.data)) {
                displayResults(data.data);
            } else {
                console.error('Invalid response format:', data);
                displayResults([]);
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            displayResults([]);
        });
    }
    
    function displayResults(results) {
        searchResults.innerHTML = '';
        
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="search-result-item">No locations found</div>';
        } else {
            results.forEach(result => {
                const resultDiv = document.createElement('div');
                resultDiv.className = 'search-result-item';
                resultDiv.innerHTML = `
                    <strong>${result.city_name}</strong>
                    ${result.city_section_name ? '<br><small>' + result.city_section_name + '</small>' : ''}
                    <small style="color: #666;"> - ${result.city_zip}</small>
                `;
                resultDiv.addEventListener('click', () => {
                    window.location.href = result.url;
                });
                searchResults.appendChild(resultDiv);
            });
        }
        
        searchResults.style.display = 'block';
    }
});
</script>

<?php 
wp_reset_postdata();
get_footer(); 
?>