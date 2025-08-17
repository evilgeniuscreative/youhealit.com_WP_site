<?php
/* Template Name: Services Page */
get_header();

$services = youhealit_get_services();
if (!empty($services)) {
    usort($services, function($a,$b){ return strcmp($a['name'], $b['name']); });
}

if (!function_exists('remove_spaces')) {
    function remove_spaces($s){ return strtolower(str_replace(' ', '', $s)); }
}
?>
<style>html{scroll-behavior:smooth}</style>

<div class="services-container services-page-container">
    <h1>Our Services</h1>

    <?php if (!empty($services)): ?>
        <ul class="services-toc">
            <?php foreach($services as $svc): $id = remove_spaces($svc['name']); ?>
                <li><a href="#<?php echo esc_attr($id); ?>"><?php echo function_exists('youhealit_title_case') ? youhealit_title_case($svc['name']) : ucwords($svc['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <hr class="services-divider" />
        <?php foreach($services as $svc): $id = remove_spaces($svc['name']); ?>
            <section id="<?php echo esc_attr($id); ?>" class="service-block">
                <h2><?php echo function_exists('youhealit_title_case') ? youhealit_title_case($svc['name']) : ucwords($svc['name']); ?></h2>
                <p><?php echo wp_kses_post($svc['description']); ?></p>
            </section>
        <?php endforeach; ?>
        <p>If what you're looking for isn't listed above, don't worry! We may offer it. Please call us at <a href="tel:9192552525">919-241-5032</a> to ask about our services.</p>  
    <?php else: ?>
        <p>No services found.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
