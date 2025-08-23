<?php
/*
theme: youhealit
youhealit/footer.php
WordPress footer template with services links, copyright info, contact details.
Functions used: youhealit_get_services(), sanitize_title(), esc_html(), 
date(), bloginfo(), wp_footer()
*/
?>
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-services">
            <h3 class="footer-services-title">Our Services</h3>
            <ul>
                <?php 
                $services = youhealit_get_services();
                if (!empty($services)) {
                    // Always include these two first
                    $priority_services = ['chiropractic', 'massage'];
                    
                    // Filter out priority services from random selection
                    $other_services = array_filter($services, function($service) use ($priority_services) {
                        return !in_array(strtolower($service['name']), $priority_services);
                    });
                    
                    // Get 10 random services from the remaining
                    $random_services = array_rand($other_services, min(10, count($other_services)));
                    if (!is_array($random_services)) $random_services = [$random_services];
                    
                    // Display priority services first
                    foreach ($priority_services as $priority) {
                        $service_title = ucwords(str_replace('-', ' ', $priority));
                        $service_slug = sanitize_title($priority) . '-near-me';
                        $service_name = 'service-item/'.sanitize_title($priority);

                        echo '<li><a href="' . $service_name . '">' . esc_html($service_title) . ' Near Me</a> | </li>';
                    }
                    
                    // Display random services
                    foreach ($random_services as $index) {
                        $service = $other_services[$index];
                        $service_title = ucwords(str_replace('-', ' ', $service['name']));
                        $service_name = 'service-item/' . sanitize_title($service['name']);
                        echo '<li><a href="' . $service_name . '">' . esc_html($service_title) . ' Near Me</a> | </li>';
                    }
                }
                ?>
            </ul>
        </div>
        
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        <p>Professional health and wellness services throughout North Carolina.</p>
        
        <div class="footer-contact">
            <p><strong>Phone:</strong> <?php // echo YHI_PHONE ?></p>
            <p><strong>Email:</strong> info@youhealit.com</p>
        </div>
    </div>
    <a href="<?php echo esc_url( home_url('/sitemap/') ); ?>">Sitemap</a>
</footer>

<?php wp_footer(); ?>
</body>
</html>