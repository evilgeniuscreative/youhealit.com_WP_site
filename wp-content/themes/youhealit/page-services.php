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

<div class="services-container" style="padding:40px;">
    <h1>Our Services</h1>

    <?php if (!empty($services)): ?>
        <ul class="services-toc" style="margin:0 0 24px;padding-left:18px;">
            <?php foreach($services as $svc): $id = remove_spaces($svc['name']); ?>
                <li><a href="#<?php echo esc_attr($id); ?>"><?php echo esc_html($svc['name']); ?></a></li>
            <?php endforeach; ?>
        </ul>
        <hr style="margin:24px 0;" />
        <?php foreach($services as $svc): $id = remove_spaces($svc['name']); ?>
            <section id="<?php echo esc_attr($id); ?>" class="service-block" style="padding-top:80px;margin-bottom:40px;border-bottom:1px solid #ddd;">
                <h2><?php echo esc_html($svc['name']); ?></h2>
                <p><?php echo esc_html($svc['description']); ?></p>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No services found.</p>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
