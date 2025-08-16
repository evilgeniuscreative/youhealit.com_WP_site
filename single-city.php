<?php
/**
 * Template for displaying single city pages - North Carolina Structure
 */

get_header(); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php while (have_posts()) : the_post(); 
                // Get all city meta data from your CSV structure
                $city_name = get_post_meta(get_the_ID(), 'city_name', true);
                $city_section_name = get_post_meta(get_the_ID(), 'city_section_name', true);
                $city_zip = get_post_meta(get_the_ID(), 'city_zip', true);
                $lat = get_post_meta(get_the_ID(), 'lat', true);
                $lon = get_post_meta(get_the_ID(), 'lon', true);
                $city_headline = get_post_meta(get_the_ID(), 'city_headline', true);
                $city_subhead = get_post_meta(get_the_ID(), 'city_subhead', true);
                $city_text = get_post_meta(get_the_ID(), 'city_text', true);
                $city_image = get_post_meta(get_the_ID(), 'city_image', true);
                $user_address = get_post_meta(get_the_ID(), 'user_address', true);
                $default_distance = get_post_meta(get_the_ID(), 'default_distance', true);
                $wiki_page = get_post_meta(get_the_ID(), 'wiki_page', true);
                $city_page = get_post_meta(get_the_ID(), 'city_page', true);
                $wikimedia_page = get_post_meta(get_the_ID(), 'wikimedia_page', true);
                $youhealit_page = get_post_meta(get_the_ID(), 'youhealit_page', true);
                $batch_number = get_post_meta(get_the_ID(), 'batch_number', true);
                
                // Get taxonomies
                $city_section_terms = get_the_terms(get_the_ID(), 'city-section');
                $service_terms = get_the_terms(get_the_ID(), 'service-type');
            ?>
                
                <article id="post-<?php the_ID(); ?>" <?php post_class('city-post'); ?>>
                    
                    <!-- Breadcrumbs -->
                    <nav class="breadcrumbs" aria-label="Breadcrumb">
                        <ol class="breadcrumb-list">
                            <li><a href="<?php echo home_url(); ?>">Home</a></li>
                            <li><a href="<?php echo get_post_type_archive_link('city'); ?>">North Carolina Locations</a></li>
                            <?php if ($city_section_terms && !is_wp_error($city_section_terms)): ?>
                                <li><a href="<?php echo get_term_link($city_section_terms[0]); ?>"><?php echo $city_section_terms[0]->name; ?></a></li>
                            <?php endif; ?>
                            <li class="current"><?php echo $city_name; ?><?php if ($city_section_name): ?> - <?php echo $city_section_name; ?><?php endif; ?></li>
                        </ol>
                    </nav>

                    <!-- City Header -->
                    <header class="city-header">
                        <?php if ($city_headline): ?>
                            <h1 class="city-headline"><?php echo esc_html($city_headline); ?></h1>
                        <?php else: ?>
                            <h1 class="city-title">
                                <?php echo $city_name; ?>, North Carolina
                                <?php if ($city_section_name): ?>
                                    <span class="city-section"> - <?php echo $city_section_name; ?></span>
                                <?php endif; ?>
                            </h1>
                        <?php endif; ?>
                        
                        <?php if ($city_subhead): ?>
                            <p class="city-subhead"><?php echo esc_html($city_subhead); ?></p>
                        <?php endif; ?>
                        
                        <div class="city-meta">
                            <?php if ($city_zip): ?>
                                <span class="zip-code">ZIP: <?php echo $city_zip; ?></span>
                            <?php endif; ?>
                            
                            <?php if ($lat && $lon): ?>
                                <span class="coordinates">📍 <?php echo $lat; ?>, <?php echo $lon; ?></span>
                            <?php endif; ?>
                            
                            <?php if ($service_terms && !is_wp_error($service_terms)): ?>
                                <div class="services-offered">
                                    <strong>Services Available:</strong>
                                    <ul class="services-list">
                                        <?php foreach ($service_terms as $service): ?>
                                            <li class="service-item">
                                                <a href="<?php echo get_term_link($service); ?>"><?php echo $service->name; ?></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </header>

                    <!-- City Content -->
                    <div class="city-content">
                        <?php if ($city_image): ?>
                            <div class="city-featured-image">
                                <img src="<?php echo esc_url($city_image); ?>" alt="<?php echo esc_attr($city_name . ' - ' . $city_section_name); ?>" class="img-responsive">
                            </div>
                        <?php elseif (has_post_thumbnail()): ?>
                            <div class="city-featured-image">
                                <?php the_post_thumbnail('large', ['class' => 'img-responsive']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="city-description">
                            <?php 
                            // Use city_text from CSV if available, otherwise use post content
                            if (!empty($city_text)) {
                                echo wpautop(esc_html($city_text));
                            } elseif (!empty(get_the_content())) {
                                the_content();
                            } else {
                                // Fallback content
                                echo '<p>Welcome to ' . $city_name . ', North Carolina! ';
                                if ($city_section_name) {
                                    echo 'Located in the ' . $city_section_name . ' area, ';
                                }
                                echo 'we offer comprehensive health and wellness services to help you achieve your best life.</p>';
                                
                                if ($service_terms && !is_wp_error($service_terms)) {
                                    echo '<p>Our services include: ';
                                    $service_names = array_map(function($term) { return $term->name; }, $service_terms);
                                    echo implode(', ', $service_names) . '.</p>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div class="location-details">
                        <h2>Location Information</h2>
                        <div class="location-info-grid">
                            <div class="location-card">
                                <h3>Address & Contact</h3>
                                <p><strong>City:</strong> <?php echo $city_name; ?></p>
                                <p><strong>State:</strong> North Carolina</p>
                                <?php if ($city_zip): ?>
                                    <p><strong>ZIP Code:</strong> <?php echo $city_zip; ?></p>
                                <?php endif; ?>
                                <?php if ($city_section_name): ?>
                                    <p><strong>Area:</strong> <?php echo $city_section_name; ?></p>
                                <?php endif; ?>
                                <?php if ($user_address): ?>
                                    <p><strong>Address:</strong> <?php echo esc_html($user_address); ?></p>
                                <?php endif; ?>
                                <?php if ($default_distance): ?>
                                    <p><strong>Service Range:</strong> <?php echo esc_html($default_distance); ?> miles</p>
                                <?php endif; ?>
                            </div>

                            <?php if ($lat && $lon): ?>
                                <div class="map-card">
                                    <h3>Location Map</h3>
                                    <div class="map-container">
                                        <iframe 
                                            src="https://maps.google.com/maps?q=<?php echo $lat; ?>,<?php echo $lon; ?>&hl=en&z=14&output=embed"
                                            width="100%" 
                                            height="250" 
                                            style="border:0;" 
                                            allowfullscreen="" 
                                            loading="lazy">
                                        </iframe>
                                    </div>
                                    <p><strong>Coordinates:</strong> <?php echo $lat; ?>, <?php echo $lon; ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if ($service_terms && !is_wp_error($service_terms)): ?>
                                <div class="services-card">
                                    <h3>Available Services</h3>
                                    <div class="services-grid">
                                        <?php foreach ($service_terms as $service): ?>
                                            <div class="service-badge">
                                                <a href="<?php echo get_term_link($service); ?>">
                                                    <?php echo $service->name; ?>
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- External Links -->
                    <?php if ($wiki_page || $city_page || $wikimedia_page): ?>
                        <div class="external-links">
                            <h2>Additional Information</h2>
                            <div class="links-grid">
                                <?php if ($wiki_page): ?>
                                    <a href="<?php echo esc_url($wiki_page); ?>" target="_blank" class="external-link wiki-link">
                                        📖 Wikipedia Page
                                    </a>
                                <?php endif; ?>
                                <?php if ($wikimedia_page): ?>
                                    <a href="<?php echo esc_url($wikimedia_page); ?>" target="_blank" class="external-link wikimedia-link">
                                        🖼️ Wikimedia Commons
                                    </a>
                                <?php endif; ?>
                                <?php if ($city_page): ?>
                                    <a href="<?php echo esc_url($city_page); ?>" target="_blank" class="external-link city-link">
                                        🏛️ Official City Page
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Call to Action -->
                    <div class="cta-section">
                        <h2>Ready to Start Your Healing Journey?</h2>
                        <p>Contact us today to schedule your consultation and begin your path to wellness in <?php echo $city_name; ?>, North Carolina.</p>
                        <div class="cta-buttons">
                            <a href="#contact" class="btn btn-primary">Schedule Consultation</a>
                            <a href="tel:+1234567890" class="btn btn-secondary">Call Now</a>
                        </div>
                    </div>

                    <!-- Related Locations -->
                    <?php
                    // Get other cities in the same city section
                    $related_cities = get_posts([
                        'post_type' => 'city',
                        'posts_per_page' => 6,
                        'post__not_in' => [get_the_ID()],
                        'meta_query' => [
                            [
                                'key' => 'city_section_name',
                                'value' => $city_section_name,
                                'compare' => '='
                            ]
                        ],
                        'orderby' => 'rand'
                    ]);
                    
                    if ($related_cities): ?>
                        <div class="related-locations">
                            <h2>Other Locations in <?php echo $city_section_name; ?></h2>
                            <div class="related-cities-grid">
                                <?php foreach ($related_cities as $related_city): 
                                    $related_city_name = get_post_meta($related_city->ID, 'city_name', true);
                                    $related_city_section = get_post_meta($related_city->ID, 'city_section_name', true);
                                    $related_city_headline = get_post_meta($related_city->ID, 'city_headline', true);
                                ?>
                                    <div class="related-city-card">
                                        <h4><a href="<?php echo get_permalink($related_city->ID); ?>">
                                            <?php echo $related_city_name; ?>
                                            <?php if ($related_city_section): ?>
                                                <span class="city-section"> - <?php echo $related_city_section; ?></span>
                                            <?php endif; ?>
                                        </a></h4>
                                        <?php if ($related_city_headline): ?>
                                            <p class="related-headline"><?php echo esc_html($related_city_headline); ?></p>
                                        <?php endif; ?>
                                        <a href="<?php echo get_permalink($related_city->ID); ?>" class="btn btn-outline">View Location</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Schema.org Structured Data -->
                    <script type="application/ld+json">
                    {
                        "@context": "https://schema.org",
                        "@type": "LocalBusiness",
                        "name": "YouHealIt - <?php echo $city_name; ?><?php if ($city_section_name): ?> <?php echo $city_section_name; ?><?php endif; ?>",
                        "description": "<?php echo wp_strip_all_tags($city_headline ?: $city_name . ' health and wellness services'); ?>",
                        "address": {
                            "@type": "PostalAddress",
                            "addressLocality": "<?php echo $city_name; ?>",
                            "addressRegion": "North Carolina"
                            <?php if ($city_zip): ?>,"postalCode": "<?php echo $city_zip; ?>"<?php endif; ?>
                        },
                        "url": "<?php echo get_permalink(); ?>"
                        <?php if ($lat && $lon): ?>
                        ,"geo": {
                            "@type": "GeoCoordinates",
                            "latitude": "<?php echo $lat; ?>",
                            "longitude": "<?php echo $lon; ?>"
                        }
                        <?php endif; ?>
                        <?php if ($service_terms && !is_wp_error($service_terms)): ?>
                        ,"hasOfferCatalog": {
                            "@type": "OfferCatalog",
                            "name": "Health & Wellness Services",
                            "itemListElement": [
                                <?php 
                                $service_items = [];
                                foreach ($service_terms as $service) {
                                    $service_items[] = '{
                                        "@type": "Offer",
                                        "itemOffered": {
                                            "@type": "Service",
                                            "name": "' . $service->name . '"
                                        }
                                    }';
                                }
                                echo implode(',', $service_items);
                                ?>
                            ]
                        }
                        <?php endif; ?>
                    }
                    </script>

                    <?php if ($batch_number): ?>
                        <!-- Debug info (remove in production) -->
                        <div class="debug-info" style="margin-top: 50px; padding: 10px; background: #f0f0f0; font-size: 12px; color: #666;">
                            <strong>Import Info:</strong> Batch #<?php echo $batch_number; ?>
                        </div>
                    <?php endif; ?>

                </article>

            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
/* City Page Specific Styles for NC Structure */
.city-post {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.breadcrumbs {
    margin-bottom: 30px;
}

.breadcrumb-list {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    gap: 10px;
}

.breadcrumb-list li::after {
    content: " → ";
    margin-left: 10px;
    color: #666;
}

.breadcrumb-list li:last-child::after {
    display: none;
}

.breadcrumb-list .current {
    font-weight: bold;
    color: #333;
}

.city-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 10px;
}

.city-headline,
.city-title {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #2c3e50;
}

.city-section {
    color: #27ae60;
    font-weight: 500;
}

.city-subhead {
    font-size: 1.2rem;
    color: #555;
    margin-bottom: 20px;
    font-style: italic;
}

.city-meta {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.zip-code,
.coordinates {
    background: #3498db;
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-weight: 500;
}

.coordinates {
    background: #e74c3c;
}

.services-offered {
    text-align: left;
}

.services-list {
    display: flex;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 5px 0 0 0;
    flex-wrap: wrap;
}

.service-item {
    background: #27ae60;
    border-radius: 15px;
    overflow: hidden;
}

.service-item a {
    display: block;
    padding: 5px 12px;
    color: white;
    text-decoration: none;
    font-size: 0.9rem;
}

.service-item a:hover {
    background: #219a52;
}

.city-content {
    margin-bottom: 40px;
}

.city-featured-image {
    text-align: center;
    margin-bottom: 30px;
}

.city-featured-image img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.city-description {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #444;
}

.location-details {
    margin-bottom: 40px;
}

.location-details h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #2c3e50;
}

.location-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

.location-card,
.services-card,
.map-card {
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    border-left: 4px solid #3498db;
}

.map-card {
    border-left-color: #e74c3c;
}

.location-card h3,
.services-card h3,
.map-card h3 {
    margin-top: 0;
    color: #2c3e50;
}

.map-container {
    margin: 15px 0;
    border-radius: 8px;
    overflow: hidden;
}

.services-grid {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.service-badge {
    background: #ecf0f1;
    border-radius: 20px;
    overflow: hidden;
}

.service-badge a {
    display: block;
    padding: 8px 15px;
    text-decoration: none;
    color: #2c3e50;
    font-weight: 500;
}

.service-badge a:hover {
    background: #3498db;
    color: white;
}

.external-links {
    margin-bottom: 40px;
}

.external-links h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #2c3e50;
}

.links-grid {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.external-link {
    display: inline-block;
    padding: 12px 20px;
    background: #f8f9fa;
    color: #2c3e50;
    text-decoration: none;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.external-link:hover {
    background: #3498db;
    color: white;
    border-color: #3498db;
}

.cta-section {
    text-align: center;
    padding: 40px;
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    border-radius: 15px;
    margin-bottom: 40px;
}

.cta-section h2 {
    margin-bottom: 15px;
}

.cta-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 25px;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 12px 25px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-primary {
    background: #27ae60;
    color: white;
}

.btn-primary:hover {
    background: #219a52;
    transform: translateY(-2px);
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid white;
}

.btn-secondary:hover {
    background: white;
    color: #3498db;
}

.btn-outline {
    background: transparent;
    color: #3498db;
    border: 2px solid #3498db;
    padding: 8px 15px;
    font-size: 0.9rem;
}

.btn-outline:hover {
    background: #3498db;
    color: white;
}

.related-locations {
    margin-top: 50px;
}

.related-locations h2 {
    text-align: center;
    margin-bottom: 30px;
    color: #2c3e50;
}

.related-cities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.related-city-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease;
}

.related-city-card:hover {
    transform: translateY(-5px);
}

.related-city-card h4 {
    margin-bottom: 10px;
}

.related-city-card h4 a {
    text-decoration: none;
    color: #2c3e50;
}

.related-city-card .city-section {
    color: #27ae60;
    font-weight: normal;
}

.related-headline {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .city-headline,
    .city-title {
        font-size: 2rem;
    }
    
    .city-meta {
        flex-direction: column;
        gap: 15px;
    }
    
    .services-list {
        justify-content: center;
    }
    
    .location-info-grid {
        grid-template-columns: 1fr;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .breadcrumb-list {
        flex-wrap: wrap;
    }
    
    .links-grid {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<?php get_footer(); ?>