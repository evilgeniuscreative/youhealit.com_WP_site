<?php
/**
 * Archive template for cities - North Carolina locations listing
 */

get_header(); ?>

<div class="container">
    <div class="cities-archive">
        
        <!-- Page Header -->
        <header class="archive-header">
            <h1 class="archive-title">North Carolina Health & Wellness Locations</h1>
            <p class="archive-description">
                Discover comprehensive health and wellness services across North Carolina. 
                From chiropractic care to holistic health, massage therapy to nutrition counseling, 
                find your perfect healing destination in cities and towns throughout the state.
            </p>
        </header>

        <!-- Filters and Search -->
        <div class="cities-filters">
            <div class="filter-row">
                <div class="search-cities">
                    <input type="text" id="city-search" placeholder="Search cities..." />
                </div>
                
                <div class="filter-by-section">
                    <select id="section-filter">
                        <option value="">All City Sections</option>
                        <?php
                        $city_sections = get_terms([
                            'taxonomy' => 'city-section',
                            'hide_empty' => true,
                        ]);
                        foreach ($city_sections as $section) {
                            echo '<option value="' . esc_attr($section->slug) . '">' . esc_html($section->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-by-service">
                    <select id="service-filter">
                        <option value="">All Services</option>
                        <?php
                        $service_types = get_terms([
                            'taxonomy' => 'service-type',
                            'hide_empty' => true,
                        ]);
                        foreach ($service_types as $service) {
                            echo '<option value="' . esc_attr($service->slug) . '">' . esc_html($service->name) . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Cities Stats -->
        <div class="cities-stats">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number"><?php echo wp_count_posts('city')->publish; ?></span>
                    <span class="stat-label">NC Locations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo wp_count_terms('city-section'); ?></span>
                    <span class="stat-label">City Sections</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number"><?php echo wp_count_terms('service-type'); ?></span>
                    <span class="stat-label">Service Types</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">North Carolina</span>
                </div>
            </div>
        </div>

        <!-- Cities Grid -->
        <div class="cities-grid" id="cities-container">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); 
                    // Get city meta data
                    $city_name = get_post_meta(get_the_ID(), 'city_name', true);
                    $city_section_name = get_post_meta(get_the_ID(), 'city_section_name', true);
                    $city_zip = get_post_meta(get_the_ID(), 'city_zip', true);
                    $city_headline = get_post_meta(get_the_ID(), 'city_headline', true);
                    $city_subhead = get_post_meta(get_the_ID(), 'city_subhead', true);
                    $city_image = get_post_meta(get_the_ID(), 'city_image', true);
                    $lat = get_post_meta(get_the_ID(), 'lat', true);
                    $lon = get_post_meta(get_the_ID(), 'lon', true);
                    
                    // Get taxonomies for filtering
                    $city_section_terms = get_the_terms(get_the_ID(), 'city-section');
                    $service_terms = get_the_terms(get_the_ID(), 'service-type');
                    
                    // Build data attributes for filtering
                    $section_slugs = $city_section_terms ? implode(' ', wp_list_pluck($city_section_terms, 'slug')) : '';
                    $service_slugs = $service_terms ? implode(' ', wp_list_pluck($service_terms, 'slug')) : '';
                ?>
                    <article class="city-card" 
                             data-city="<?php echo esc_attr(strtolower($city_name)); ?>"
                             data-section="<?php echo esc_attr($section_slugs); ?>"
                             data-services="<?php echo esc_attr($service_slugs); ?>">
                        
                        <!-- City Card Header -->
                        <div class="city-card-header">
                            <?php if ($city_image): ?>
                                <div class="city-card-image">
                                    <img src="<?php echo esc_url($city_image); ?>" alt="<?php echo esc_attr($city_name . ' - ' . $city_section_name); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <h3>
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo esc_html($city_name); ?>, NC
                                    <?php if ($city_section_name): ?>
                                        <span class="city-section"> - <?php echo esc_html($city_section_name); ?></span>
                                    <?php endif; ?>
                                </a>
                            </h3>
                            
                            <?php if ($city_headline): ?>
                                <p class="city-card-headline"><?php echo esc_html($city_headline); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- City Card Body -->
                        <div class="city-card-body">
                            <?php if ($city_subhead): ?>
                                <p class="city-card-subhead"><?php echo esc_html($city_subhead); ?></p>
                            <?php endif; ?>
                            
                            <div class="city-card-meta">
                                <?php if ($city_zip): ?>
                                    <span class="city-zip">ZIP: <?php echo esc_html($city_zip); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($lat && $lon): ?>
                                    <span class="city-coordinates">📍 <?php echo esc_html($lat); ?>, <?php echo esc_html($lon); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($service_terms && !is_wp_error($service_terms)): ?>
                                <div class="city-services">
                                    <strong>Services:</strong>
                                    <div class="services-tags">
                                        <?php foreach (array_slice($service_terms, 0, 4) as $service): ?>
                                            <span class="service-tag"><?php echo esc_html($service->name); ?></span>
                                        <?php endforeach; ?>
                                        <?php if (count($service_terms) > 4): ?>
                                            <span class="service-tag more">+<?php echo (count($service_terms) - 4); ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="city-card-excerpt">
                                <?php if (has_excerpt()): ?>
                                    <?php the_excerpt(); ?>
                                <?php else: ?>
                                    <p>Comprehensive health and wellness services in <?php echo esc_html($city_name); ?>, North Carolina. Experience personalized care in our <?php echo esc_html($city_section_name); ?> location.</p>
                                <?php endif; ?>
                            </div>
                            
                            <div class="city-card-actions">
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary">Learn More</a>
                                <a href="tel:+1234567890" class="btn btn-outline">Call Now</a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <div class="no-cities">
                    <h3>No locations found</h3>
                    <p>We're expanding our network of health and wellness centers across North Carolina. Check back soon for new locations!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Load More / Pagination -->
        <div class="cities-pagination">
            <?php
            the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => '← Previous',
                'next_text' => 'Next →',
                'class' => 'pagination-links'
            ]);
            ?>
        </div>

        <!-- Call to Action Section -->
        <div class="archive-cta">
            <h2>Ready to Start Your Healing Journey?</h2>
            <p>Find the perfect North Carolina location for your health and wellness needs. Our experienced practitioners are here to help you achieve your wellness goals.</p>
            <div class="cta-buttons">
                <a href="#contact" class="btn btn-primary">Schedule Consultation</a>
                <a href="tel:+1234567890" class="btn btn-secondary">Call Now</a>
            </div>
        </div>
    </div>
</div>

<style>
/* Archive-specific styles */
.cities-archive {
    padding: 40px 0;
}

.archive-header {
    text-align: center;
    margin-bottom: 50px;
    padding: 40px 20px;
    background: linear-gradient(135deg, var(--background-light) 0%, #e8f4f8 100%);
    border-radius: 15px;
}

.archive-title {
    font-size: 2.5rem;
    margin-bottom: 20px;
    color: var(--text-dark);
    font-weight: 700;
}

.archive-description {
    font-size: 1.1rem;
    color: var(--text-light);
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Filters */
.cities-filters {
    margin-bottom: 40px;
}

.filter-row {
    display: flex;
    gap: 20px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.search-cities input,
.filter-by-section select,
.filter-by-service select {
    padding: 12px 20px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    min-width: 200px;
    transition: border-color 0.3s ease;
}

.search-cities input:focus,
.filter-by-section select:focus,
.filter-by-service select:focus {
    outline: none;
    border-color: var(--primary-blue);
}

/* Stats */
.cities-stats {
    margin-bottom: 40px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
}

.stat-item {
    text-align: center;
    padding: 20px;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary-blue);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 0.9rem;
    color: var(--text-light);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* City Cards */
.city-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    border-top: 4px solid var(--primary-blue);
}

.city-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
}

.city-card-header {
    padding: 25px 25px 15px;
    background: linear-gradient(135deg, var(--background-light) 0%, #e8f4f8 100%);
}

.city-card-image {
    margin-bottom: 15px;
    border-radius: 10px;
    overflow: hidden;
}

.city-card-image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.city-card h3 {
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.city-card h3 a {
    text-decoration: none;
    color: var(--text-dark);
}

.city-card h3 a:hover {
    color: var(--primary-blue);
}

.city-card .city-section {
    color: var(--primary-blue);
    font-weight: 600;
}

.city-card-headline {
    font-size: 1rem;
    color: var(--text-light);
    font-style: italic;
    margin-bottom: 0;
}

.city-card-body {
    padding: 25px;
}

.city-card-subhead {
    font-size: 0.95rem;
    color: var(--text-light);
    margin-bottom: 15px;
    line-height: 1.5;
}

.city-card-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.city-zip,
.city-coordinates {
    background: var(--primary-blue);
    color: white;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.city-coordinates {
    background: var(--secondary-blue);
}

.city-services {
    margin-bottom: 20px;
}

.city-services strong {
    display: block;
    margin-bottom: 10px;
    color: var(--text-dark);
    font-size: 0.9rem;
}

.services-tags {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.service-tag {
    background: var(--background-light);
    color: var(--text-dark);
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid var(--border-color);
}

.service-tag.more {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

.city-card-excerpt {
    margin-bottom: 20px;
    color: var(--text-light);
    line-height: 1.6;
}

.city-card-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.city-card-actions .btn {
    flex: 1;
    text-align: center;
    min-width: 120px;
}

/* No cities message */
.no-cities {
    grid-column: 1 / -1;
    text-align: center;
    padding: 60px 20px;
    background: var(--background-light);
    border-radius: 15px;
}

.no-cities h3 {
    color: var(--text-dark);
    margin-bottom: 15px;
}

.no-cities p {
    color: var(--text-light);
    font-size: 1.1rem;
}

/* Pagination */
.cities-pagination {
    margin: 50px 0;
    text-align: center;
}

.pagination-links {
    display: inline-flex;
    gap: 10px;
    align-items: center;
}

.pagination-links a,
.pagination-links .current {
    padding: 10px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-dark);
    transition: all 0.3s ease;
}

.pagination-links a:hover,
.pagination-links .current {
    background: var(--primary-blue);
    color: white;
    border-color: var(--primary-blue);
}

/* Archive CTA */
.archive-cta {
    text-align: center;
    padding: 60px 40px;
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
    color: white;
    border-radius: 20px;
    margin-top: 60px;
}

.archive-cta h2 {
    color: white;
    margin-bottom: 15px;
    font-size: 2.2rem;
}

.archive-cta p {
    color: rgba(255,255,255,0.9);
    font-size: 1.1rem;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

/* Responsive */
@media (max-width: 768px) {
    .archive-title {
        font-size: 2rem;
    }
    
    .filter-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .search-cities input,
    .filter-by-section select,
    .filter-by-service select {
        min-width: 100%;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .cities-grid {
        grid-template-columns: 1fr;
    }
    
    .city-card-actions {
        flex-direction: column;
    }
    
    .city-card-actions .btn {
        flex: none;
    }
    
    .archive-cta {
        padding: 40px 20px;
    }
    
    .archive-cta h2 {
        font-size: 1.8rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('city-search');
    const sectionFilter = document.getElementById('section-filter');
    const serviceFilter = document.getElementById('service-filter');
    const cityCards = document.querySelectorAll('.city-card');
    
    function filterCities() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedSection = sectionFilter.value;
        const selectedService = serviceFilter.value;
        
        cityCards.forEach(card => {
            const cityName = card.dataset.city;
            const citySections = card.dataset.section;
            const cityServices = card.dataset.services;
            
            const matchesSearch = cityName.includes(searchTerm);
            const matchesSection = !selectedSection || citySections.includes(selectedSection);
            const matchesService = !selectedService || cityServices.includes(selectedService);
            
            if (matchesSearch && matchesSection && matchesService) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    
    searchInput.addEventListener('input', filterCities);
    sectionFilter.addEventListener('change', filterCities);
    serviceFilter.addEventListener('change', filterCities);
});
</script>

<?php get_footer(); ?>