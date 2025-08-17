<?php
/*
theme: youhealit
youhealit/page-all-cities-sitemap.php
Template Name: All Cities Sitemap
Single page listing all cities and neighborhoods in pipe-separated format for SEO sitemap purposes.
Shows: City Name | Neighborhood Name | Zip Code | Distance | Services Available
Functions used: WP_Query, get_post_meta(), get_permalink(), number_format()
Variables: $all_cities_query, $city_name, $city_section_name, $city_zip, $total_cities
*/

get_header();

// Get ALL cities at once (no pagination)
$all_cities_query = new WP_Query([
    'post_type' => 'city',
    'posts_per_page' => -1,  // Get ALL cities
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

$total_cities = $all_cities_query->found_posts;
?>

<main class="main-content">
    <!-- Hero Section -->
    <section class="sitemap-hero">
        <div class="sitemap-hero-container">
            <h1>
               We serve <strong><em>you</em></strong> wherever you are in the North Carolina Triangle
            </h1>

        </div>
    </section>

     <!-- SEO Content Section -->
    <section class="seo-content">
        <div class="seo-content-container">
            <h2>There are many options out there for your health and wellness. So why choose The Wellness Center of the Triangle, NC?</h2>
            <div class="seo-content-text">
                <p>Dr. Paul Aaron's office and 4+ decades of experience, along with our team of highly experienced professionals, ensures you can find professional chiropractic care, therapeutic massage, acupuncture, and comprehensive pain relief services wherever you are in the NC Triangle.</p>
                
                <p>Our office features:</p>
                <ul>
                    <li><strong>Licensed, experienced practitioners</strong> specializing in natural healing</li>
                    <li><strong>State-of-the-art facilities</strong> with modern equipment and comfortable environments</li>
                    <li><strong>Personalized treatment plans</strong> tailored to your specific health goals</li>
                    <li><strong>Convenient scheduling</strong> to fit your busy lifestyle</li>
                    <li><strong>Comprehensive services</strong> under one roof for complete wellness care</li>
                </ul>
                
                <p>Whether you're dealing with chronic pain, recovering from an injury, seeking preventive care, or looking to optimize your overall wellness, our North Carolina locations provide the expert care and natural solutions you need to feel your best.</p>
            </div>
        </div>
    </section>

    <div class="map-wrapper"> <?php include 'map-component.php'; echo wellness_map('800px', '500px'); ?> </div>

    <!-- Cities List Section -->
    <section class="all-cities-section">
        <div class="all-cities-container">
            
            <div class="cities-header">
                <h2>
                    Complete Service Area Directory
                </h2>
                <p class="cities-header-text">
                Believe it or not, if you're in or near one of these <?php echo number_format($total_cities); ?> cities and neighborhoods, we've got your back. Literally. <em>Ok, mediocre joke, but complete truth.</em></p>
            <p class="cities-subtext">
                We are here to help YOU feel the best you can, whether you're dealing with chronic pain, an accident, aging, or just want to feel better. We are here to help you feel the best you can.
            </p>
            <p class="cities-subtext">
                Chiropractic • Massage • Acupuncture • Pain Relief • Wellness Services • Much More  •  <a href="<?php echo get_permalink(get_page_by_path('services')) ?>">See Our Complete List of Available Services</a>
            </p>
            </div>

            
            <?php if ($all_cities_query->have_posts()) : ?>
                
                <div class="cities-sitemap-list">
                    
                    <?php 
                    $counter = 0;
                    while ($all_cities_query->have_posts()) : $all_cities_query->the_post(); 
                        $counter++;
                        
                        $city_name = get_post_meta(get_the_ID(), 'city_name', true);
                        $city_section_name = get_post_meta(get_the_ID(), 'city_section_name', true);
                        $city_zip = get_post_meta(get_the_ID(), 'city_zip', true);
                        $lat = get_post_meta(get_the_ID(), 'lat', true);
                        $lon = get_post_meta(get_the_ID(), 'lon', true);
                        
                        // Calculate rough distance and time (placeholder logic)
                        $distance = rand(5, 45);
                        $drive_time = round($distance * 1.5);
                        
                        if (empty($city_name)) $city_name = get_the_title();
                        
                        // Build the pipe-separated line
                        $line_parts = [];
                        
                        // City Name (linked)
                        $line_parts[] = '<a href="' . get_permalink() . '" class="city-link">' . esc_html($city_name) . '</a>';
                        
                        // Neighborhood Name (linked if exists)
                        if (!empty($city_section_name)) {
                            $line_parts[] = '<a href="' . get_permalink() . '" class="neighborhood-link">' . esc_html($city_section_name) . '</a>';
                        } else {
                            $line_parts[] = '<span class="city-center">City Center</span>';
                        }
                        
                        // Zip Code
                        if (!empty($city_zip)) {
                            $line_parts[] = '<span class="zip-code">' . esc_html($city_zip) . '</span>';
                        } else {
                            $line_parts[] = '<span class="location-fallback">NC</span>';
                        }
                        
                        // Distance
                        $line_parts[] = '<span class="distance-info">' . $distance . ' miles</span>';
                        
                        // Drive Time
                        $line_parts[] = '<span class="distance-info">' . $drive_time . ' minutes</span>';
                        
                        // Services Available
                        $line_parts[] = '<span class="services-text">Chiropractic, Massage, Acupuncture & Pain Relief near</span> <a href="' . get_permalink() . '" class="city-link">' . esc_html($city_name) . '</a>';
                        
                        if (!empty($city_section_name)) {
                            $line_parts[count($line_parts) - 1] .= ', <a href="' . get_permalink() . '" class="neighborhood-link">' . esc_html($city_section_name) . '</a>';
                        }
                    ?>
                        
                        <p class="city-entry <?php echo ($counter % 2 == 0) ? 'even' : 'odd'; ?>">
                            <?php echo implode(' <span class="pipe-separator">|</span> ', $line_parts); ?>
                        </p>
                        
                    <?php endwhile; ?>
                    
                </div>
                
                <!-- Summary Section -->
                <div class="summary-section">
                    <h3>Complete North Carolina Coverage</h3>
                    <p class="summary-text">
                        <strong><?php echo number_format($total_cities); ?> locations</strong> throughout North Carolina offering professional health and wellness services. 
                        From Charlotte to Raleigh, Durham to Greensboro, and everywhere in between.
                    </p>
                    <div class="summary-buttons">
                        <a href="/contact" class="btn btn-red btn-consultation">Schedule Consultation</a>
                        <a href="tel:${YHI_PHONE}" class="btn btn-phone">Call ${YHI_PHONE}</a>
                        <a href="/services" class="btn btn-services">View All Services</a>
                    </div>
                </div>
                
            <?php else : ?>
                
                <div class="no-locations">
                    <h3>No locations found</h3>
                    <p>Please check back as we continue expanding our service areas throughout North Carolina.</p>
                </div>
                
            <?php endif; ?>
            
        </div>
    </section>

   

</main>

<style>
/* Sitemap Page Specific Styles */

/* Hero Section */
.sitemap-hero {
    background: var(--primary-green);
    padding: 20px 20px;
    text-align: center;
}

.sitemap-hero-container {
    max-width: 1200px;
    margin: 0 auto;
}

.sitemap-hero h1 {
    color: white;
    font-size: 2.5rem;
    margin-bottom: 15px;
    font-weight: 300;
}

/* SEO Content Section */
.seo-content {
    background: var(--background-light);
    padding: 60px 20px;
}

.seo-content-container {
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.seo-content h2 {
    color: var(--red-accent);
    margin-bottom: 20px;
}

.seo-content-text {
    text-align: left;
    line-height: 1.7;
    color: var(--text-dark);
}

.seo-content ul {
    margin: 20px 0;
    padding-left: 30px;
}

/* Map Component Wrapper */
.map-wrapper {
    max-width: 800px;
    margin: 0 auto;
}

/* Cities List Section */
.all-cities-section {
    background: white;
    padding: 60px 20px;
}

.all-cities-container {
    max-width: 1200px;
    margin: 0 auto;
}

.cities-header {
    text-align: center;
    margin-bottom: 40px;
}

.cities-header h2 {
    color: var(--primary-green);
    font-size: 2rem;
    margin-bottom: 15px;
}

.cities-header-text {
    color: white;
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.cities-subtext {
    color: white;
    font-size: 1rem;
    opacity: 0.9;
}

.cities-sitemap-list {
    max-width: 900px;
    margin: 0 auto;
}

/* Individual City Entries */
.city-entry {
    margin: 8px 0;
    padding: 12px 15px;
    border-radius: 6px;
    line-height: 1.5;
    font-size: 0.95rem;
    border-left: 3px solid var(--primary-green);
}

.city-entry.even {
    background: #f8f9fa;
}

.city-entry.odd {
    background: white;
}

.city-link {
    color: var(--primary-green);
    text-decoration: none;
    font-weight: 600;
}

.neighborhood-link {
    color: var(--red-accent);
    text-decoration: none;
    font-weight: 500;
}

.city-center {
    color: var(--text-light);
    font-style: italic;
}

.zip-code {
    color: var(--text-dark);
}

.location-fallback {
    color: var(--text-light);
}

.distance-info {
    color: var(--text-light);
}

.services-text {
    color: var(--text-dark);
}

.pipe-separator {
    color: var(--primary-green);
    font-weight: bold;
}

/* Summary Section */
.summary-section {
    text-align: center;
    margin-top: 50px;
    padding: 30px;
    background: var(--background-light);
    border-radius: 10px;
}

.summary-section h3 {
    color: var(--primary-green);
    margin-bottom: 15px;
}

.summary-text {
    color: var(--text-dark);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 20px;
}

.summary-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 25px;
}

.btn-consultation {
    padding: 12px 25px;
}

.btn-phone {
    background: var(--primary-green);
    color: white;
    padding: 12px 25px;
}

.btn-services {
    background: transparent;
    color: var(--primary-green);
    border: 2px solid var(--primary-green);
    padding: 12px 25px;
}

/* No Locations Section */
.no-locations {
    text-align: center;
    padding: 60px 20px;
}

.no-locations h3 {
    color: var(--text-light);
}

/* Existing hover effects */
.cities-sitemap-list p:hover {
    background: #e8f5e8 !important;
    transform: translateX(5px);
    transition: all 0.2s ease;
}

.cities-sitemap-list a:hover {
    text-decoration: underline !important;
}

@media (max-width: 768px) {
    .cities-sitemap-list p {
        font-size: 0.85rem !important;
        padding: 10px 12px !important;
        line-height: 1.4 !important;
    }
    
    .sitemap-hero h1 {
        font-size: 2rem !important;
    }
}
</style>

<?php 
wp_reset_postdata();
get_footer(); 
?>