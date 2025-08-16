<?php
/*
theme: youhealit
youhealit/page-service.php
Template for individual service pages (e.g., /service/chiropractic/, /service/massage/)
Displays service information, benefits, contact forms, and related services
Functions used: get_header(), get_footer(), the_title(), the_content(), get_post_meta()
*/

get_header(); 

// Get service meta data
$service_name = get_post_meta(get_the_ID(), 'service_name', true);
$service_description = get_post_meta(get_the_ID(), 'service_description', true);
?>

<main class="main-content">
    <?php while (have_posts()) : the_post(); ?>
        
        <!-- Service Hero Section -->
        <section class="service-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
            <div style="max-width: 1200px; margin: 0 auto;">
                <h1 style="color: white; font-size: 3rem; margin-bottom: 15px; font-weight: 300;">
                    <?php the_title(); ?> in North Carolina
                </h1>
                <p style="color: white; font-size: 1.3rem; font-weight: 300; max-width: 800px; margin: 0 auto;">
                    <?php echo $service_description ?: "Professional " . get_the_title() . " services throughout North Carolina"; ?>
                </p>
            </div>
        </section>

        <!-- Service Content Section -->
        <section class="service-content-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
            <div class="split-section animated">
                <div class="content-left">
                    <div class="service-description" style="font-size: 1.1rem; line-height: 1.7; color: var(--text-dark);">
                        <?php the_content(); ?>
                    </div>
                </div>
                
                <div class="content-right">
                    <!-- Service Image -->
                    <div class="service-image-container" style="margin-bottom: 30px;">
                        <div style="width: 100%; height: 350px; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; text-align: center;">
                            <div>
                                <div style="font-size: 4rem; margin-bottom: 15px;">
                                    <?php
                                    // Service-specific icons
                                    $service_slug = get_post_field('post_name');
                                    switch (strtolower($service_slug)) {
                                        case 'chiropractic':
                                        case 'chiropractic-care':
                                            echo '🦴';
                                            break;
                                        case 'acupuncture':
                                            echo '🌿';
                                            break;
                                        case 'massage':
                                        case 'massage-therapy':
                                            echo '💆';
                                            break;
                                        case 'nutrition':
                                        case 'nutritional-consulting':
                                            echo '🥗';
                                            break;
                                        case 'concussion':
                                        case 'concussion-treatment':
                                            echo '🧠';
                                            break;
                                        default:
                                            echo '🩺';
                                    }
                                    ?>
                                </div>
                                <?php the_title(); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Contact Card -->
                    <div class="quick-contact" style="background: var(--red-accent); color: white; padding: 30px; border-radius: 8px; text-align: center;">
                        <h3 style="margin: 0 0 20px 0; font-size: 1.4rem;">Schedule Your <?php the_title(); ?> Appointment</h3>
                        <p style="margin-bottom: 25px; font-size: 1.1rem;">Ready to experience the benefits of professional <?php echo strtolower(get_the_title()); ?>?</p>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <a href="tel:9192415092" class="btn" style="background: white; color: var(--red-accent); padding: 15px 25px;">Call (919) 241-5092</a>
                            <a href="/contact" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 15px 25px;">Schedule Online</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="service-cta" style="background: var(--red-accent); color: white; padding: 80px 20px; text-align: center;">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 300;">Ready to Experience <?php the_title(); ?>?</h2>
                <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6;">
                    Take the first step toward better health with our professional <?php echo strtolower(get_the_title()); ?> services. 
                    Contact us today to schedule your consultation and discover how we can help you achieve your wellness goals.
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="tel:9192415092" class="btn" style="background: white; color: var(--red-accent); padding: 15px 30px; font-size: 1.1rem;">
                        Call (919) 241-5092
                    </a>
                    <a href="/contact" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 15px 30px; font-size: 1.1rem;">
                        Schedule Consultation
                    </a>
                </div>
            </div>
        </section>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>