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
            <h3>Our Services</h3>
            <ul>
                <?php 
                $services = youhealit_get_services();
                if (!empty($services)) {
                    foreach (array_slice($services, 0, 8) as $service) {
                        echo '<li><a href="/services#' . sanitize_title($service['name']) . '">' . esc_html($service['name']) . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
        
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        <p>Professional health and wellness services throughout North Carolina.</p>
        
        <div class="footer-contact">
            <p><strong>Phone:</strong> (919) 241-5092</p>
            <p><strong>Email:</strong> info@youhealit.com</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>