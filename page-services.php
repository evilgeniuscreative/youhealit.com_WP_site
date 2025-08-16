<?php
// Template Name: Services Page
get_header(); ?>

<main class="main-content">
  <!-- Services Hero Section -->
  <section class="page-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 20px; font-weight: 300;">SERVICES</h1>
      <p style="color: white; font-size: 1.2rem; max-width: 600px; margin: 0 auto;">Comprehensive natural health solutions under one roof</p>
    </div>
  </section>

  <!-- Services Grid -->
  <section class="services-grid-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
      
      <?php
      // Get all service categories
      $service_categories = get_terms(array(
        'taxonomy' => 'service_category',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
      ));
      
      if ($service_categories && !is_wp_error($service_categories)) :
        foreach ($service_categories as $category) : ?>
          <div class="service-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
            <div class="service-image" style="height: 200px; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); position: relative;">
              <div style="position: absolute; bottom: 20px; left: 20px; color: white;">
                <h3 style="margin: 0; font-size: 1.3rem; font-weight: 600;"><?php echo $category->name; ?></h3>
              </div>
            </div>
            <div class="service-content" style="padding: 25px;">
              <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                <?php echo $category->description ?: 'Professional ' . strtolower($category->name) . ' services tailored to your individual needs.'; ?>
              </p>
              <a href="<?php echo get_term_link($category); ?>" class="btn btn-red" style="font-size: 0.9rem;">Learn More</a>
            </div>
          </div>
        <?php endforeach;
      else : ?>
        <!-- Default services if no categories exist -->
        <div class="service-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          <div class="service-image" style="height: 200px; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); position: relative;">
            <div style="position: absolute; bottom: 20px; left: 20px; color: white;">
              <h3 style="margin: 0; font-size: 1.3rem;">Chiropractic Care</h3>
            </div>
          </div>
          <div class="service-content" style="padding: 25px;">
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">Expert spinal adjustments and therapeutic treatments for optimal health and wellness.</p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
        
        <div class="service-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          <div class="service-image" style="height: 200px; background: linear-gradient(135deg, var(--red-accent), var(--primary-green)); position: relative;">
            <div style="position: absolute; bottom: 20px; left: 20px; color: white;">
              <h3 style="margin: 0; font-size: 1.3rem;">Acupuncture</h3>
            </div>
          </div>
          <div class="service-content" style="padding: 25px;">
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">Traditional Chinese medicine techniques for pain relief and healing.</p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
        
        <div class="service-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          <div class="service-image" style="height: 200px; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); position: relative;">
            <div style="position: absolute; bottom: 20px; left: 20px; color: white;">
              <h3 style="margin: 0; font-size: 1.3rem;">Massage Therapy</h3>
            </div>
          </div>
          <div class="service-content" style="padding: 25px;">
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">Therapeutic massage for pain relief, relaxation, and improved circulation.</p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
        
        <div class="service-card" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          <div class="service-image" style="height: 200px; background: linear-gradient(135deg, var(--red-accent), var(--primary-green)); position: relative;">
            <div style="position: absolute; bottom: 20px; left: 20px; color: white;">
              <h3 style="margin: 0; font-size: 1.3rem;">Nutritional Counseling</h3>
            </div>
          </div>
          <div class="service-content" style="padding: 25px;">
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">Personalized nutrition plans and supplement recommendations for optimal health.</p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="services-cta" style="background: var(--red-accent); color: white; padding: 80px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 300;">Ready to Start Your Healing Journey?</h2>
      <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6;">Our integrative approach combines multiple natural therapies to address your unique health needs. Contact us today to schedule your consultation.</p>
      <a href="/contact" class="btn" style="background: white; color: var(--red-accent); padding: 15px 30px; font-size: 1.1rem;">Schedule Consultation</a>
    </div>
  </section>
</main>

<style>
.service-card:hover {
  transform: translateY(-5px);
}

@media (max-width: 768px) {
  .services-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php get_footer(); ?>