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
    <section class="sitemap-hero" style="background: var(--primary-green); padding: 60px 20px; text-align: center;">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h1 style="color: white; font-size: 2.5rem; margin-bottom: 15px; font-weight: 300;">
                All North Carolina Locations We Serve
            </h1>
            <p style="color: white; font-size: 1.2rem; margin-bottom: 20px;">
                Believe it or not, if you're in or near one of these <?php echo number_format($total_cities); ?> cities and neighborhoods, we've got your back. Literally. <em>Ok, mediocre joke, but complete truth.</em></p>
            <p style="color: white; font-size: 1rem; opacity: 0.9;">
                We are here to help YOU feel the best you can, whether you're dealing with chronic pain, an accident, aging, or just want to feel better. We are here to help you feel the best you can.
            </p>
            <p style="color: white; font-size: 1rem; opacity: 0.9;">
                Chiropractic • Massage • Acupuncture • Pain Relief • Wellness Services
            </p>
        </div>
    </section>

     <!-- SEO Content Section -->
    <section class="seo-content" style="background: var(--background-light); padding: 60px 20px;">
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <h2 style="color: var(--red-accent); margin-bottom: 20px;">Why Choose The Wellness Center of the Triangle, NC?</h2>
            <div style="text-align: left; line-height: 1.7; color: var(--text-dark);">
                <p>Dr. Paul Aaron's office and decades of experienc, along with our team of highly experienced professionals, ensures you can find professional chiropractic care, therapeutic massage, acupuncture, and comprehensive pain relief services wherever you are in the NC Triangle.</p>
                
                <p>Our office features:</p>
                <ul style="margin: 20px 0; padding-left: 30px;">
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

    <!-- Cities List Section -->
    <section class="all-cities-section" style="background: white; padding: 60px 20px;">
        <div style="max-width: 1200px; margin: 0 auto;">
            
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: var(--primary-green); font-size: 2rem; margin-bottom: 15px;">
                    Complete Service Area Directory
                </h2>
                <p style="color: var(--text-light); font-size: 1.1rem; line-height: 1.6;">
                    Professional health and wellness services available in all <?php echo number_format($total_cities); ?> locations below. 
                    Each location offers personalized chiropractic care, therapeutic massage, acupuncture, and comprehensive pain management solutions.
                </p>
            </div>

            <?php if ($all_cities_query->have_posts()) : ?>
                
                <div class="cities-sitemap-list" style="max-width: 900px; margin: 0 auto;">
                    
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
                        $line_parts[] = '<a href="' . get_permalink() . '" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">' . esc_html($city_name) . '</a>';
                        
                        // Neighborhood Name (linked if exists)
                        if (!empty($city_section_name)) {
                            $line_parts[] = '<a href="' . get_permalink() . '" style="color: var(--red-accent); text-decoration: none; font-weight: 500;">' . esc_html($city_section_name) . '</a>';
                        } else {
                            $line_parts[] = '<span style="color: var(--text-light); font-style: italic;">City Center</span>';
                        }
                        
                        // Zip Code
                        if (!empty($city_zip)) {
                            $line_parts[] = '<span style="color: var(--text-dark);">' . esc_html($city_zip) . '</span>';
                        } else {
                            $line_parts[] = '<span style="color: var(--text-light);">NC</span>';
                        }
                        
                        // Distance
                        $line_parts[] = '<span style="color: var(--text-light);">' . $distance . ' miles</span>';
                        
                        // Drive Time
                        $line_parts[] = '<span style="color: var(--text-light);">' . $drive_time . ' minutes</span>';
                        
                        // Services Available
                        $line_parts[] = '<span style="color: var(--text-dark);">Chiropractic, Massage, Acupuncture & Pain Relief near</span> <a href="' . get_permalink() . '" style="color: var(--primary-green); text-decoration: none; font-weight: 500;">' . esc_html($city_name) . '</a>';
                        
                        if (!empty($city_section_name)) {
                            $line_parts[count($line_parts) - 1] .= ', <a href="' . get_permalink() . '" style="color: var(--red-accent); text-decoration: none; font-weight: 500;">' . esc_html($city_section_name) . '</a>';
                        }
                    ?>
                        
                        <p style="margin: 8px 0; padding: 12px 15px; background: <?php echo ($counter % 2 == 0) ? '#f8f9fa' : 'white'; ?>; border-radius: 6px; line-height: 1.5; font-size: 0.95rem; border-left: 3px solid var(--primary-green);">
                            <?php echo implode(' <span style="color: var(--primary-green); font-weight: bold;">|</span> ', $line_parts); ?>
                        </p>
                        
                    <?php endwhile; ?>
                    
                </div>
                
                <!-- Summary Section -->
                <div style="text-align: center; margin-top: 50px; padding: 30px; background: var(--background-light); border-radius: 10px;">
                    <h3 style="color: var(--primary-green); margin-bottom: 15px;">Complete North Carolina Coverage</h3>
                    <p style="color: var(--text-dark); font-size: 1.1rem; line-height: 1.6; margin-bottom: 20px;">
                        <strong><?php echo number_format($total_cities); ?> locations</strong> throughout North Carolina offering professional health and wellness services. 
                        From Charlotte to Raleigh, Durham to Greensboro, and everywhere in between.
                    </p>
                    <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-top: 25px;">
                        <a href="/contact" class="btn btn-red" style="padding: 12px 25px;">Schedule Consultation</a>
                        <a href="tel:9192415092" class="btn" style="background: var(--primary-green); color: white; padding: 12px 25px;">Call (919) 241-5092</a>
                        <a href="/services" class="btn" style="background: transparent; color: var(--primary-green); border: 2px solid var(--primary-green); padding: 12px 25px;">View All Services</a>
                    </div>
                </div>
                
            <?php else : ?>
                
                <div style="text-align: center; padding: 60px 20px;">
                    <h3 style="color: var(--text-light);">No locations found</h3>
                    <p>Please check back as we continue expanding our service areas throughout North Carolina.</p>
                </div>
                
            <?php endif; ?>
            
        </div>
    </section>

   

</main>

<style>
/* Sitemap Page Specific Styles */
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