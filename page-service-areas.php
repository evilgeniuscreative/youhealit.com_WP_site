<?php
// Template Name: Service Areas
get_header(); ?>

<main class="main-content">
  <!-- Service Areas Hero -->
  <section class="page-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 20px; font-weight: 300;">SERVICE AREAS</h1>
      <p style="color: white; font-size: 1.2rem; max-width: 600px; margin: 0 auto;">Serving communities throughout North Carolina</p>
    </div>
  </section>

  <!-- Google Material Search -->
  <section class="search-section" style="background: #f5f5f5; padding: 60px 20px;">
    <?php echo do_shortcode('[google_search placeholder="Search cities, neighborhoods, or services..." filters="all,cities,neighborhoods,services"]'); ?>
  </section>

  <!-- Cities and Neighborhoods List -->
  <section class="locations-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <h2 style="color: var(--primary-green); font-size: 2.5rem; text-align: center; margin-bottom: 50px; font-weight: 300;">Cities & Neighborhoods We Serve</h2>
    
    <?php
    // Get all cities from the database
    $cities = get_posts(array(
      'post_type' => 'city',
      'posts_per_page' => -1,
      'orderby' => 'title',
      'order' => 'ASC'
    ));
    
    if ($cities) :
      // Group cities alphabetically
      $grouped_cities = array();
      foreach ($cities as $city) {
        $first_letter = strtoupper(substr($city->post_title, 0, 1));
        $grouped_cities[$first_letter][] = $city;
      }
      ksort($grouped_cities);
      ?>
      
      <div class="cities-alphabetical">
        <?php foreach ($grouped_cities as $letter => $letter_cities) : ?>
          <div class="letter-group" style="margin-bottom: 40px;">
            <h3 style="color: var(--red-accent); font-size: 1.8rem; margin-bottom: 20px; border-bottom: 2px solid var(--red-accent); padding-bottom: 10px;">
              <?php echo $letter; ?>
            </h3>
            
            <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
              <?php foreach ($letter_cities as $city) : 
                $city_name = get_field('city_name', $city->ID) ?: $city->post_title;
                $city_zip = get_field('city_zip', $city->ID);
                $city_headline = get_field('city_headline', $city->ID);
                ?>
                <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                  <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">
                    <a href="<?php echo get_permalink($city->ID); ?>" style="color: inherit; text-decoration: none;">
                      <?php echo $city_name; ?>
                      <?php if ($city_zip) : ?>
                        <span style="font-size: 0.9rem; color: var(--text-light);"><?php echo $city_zip; ?></span>
                      <?php endif; ?>
                    </a>
                  </h4>
                  
                  <?php if ($city_headline) : ?>
                    <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px; line-height: 1.4;">
                      <?php echo wp_trim_words($city_headline, 15); ?>
                    </p>
                  <?php endif; ?>
                  
                  <a href="<?php echo get_permalink($city->ID); ?>" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">
                    View Services
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      
    <?php else : ?>
      <!-- Default cities if none exist in database -->
      <div class="default-cities">
        <div class="letter-group" style="margin-bottom: 40px;">
          <h3 style="color: var(--red-accent); font-size: 1.8rem; margin-bottom: 20px;">A</h3>
          <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">Apex</h4>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px;">Comprehensive chiropractic and wellness services</p>
              <a href="#" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">View Services</a>
            </div>
          </div>
        </div>
        
        <div class="letter-group" style="margin-bottom: 40px;">
          <h3 style="color: var(--red-accent); font-size: 1.8rem; margin-bottom: 20px;">C</h3>
          <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">Chapel Hill</h4>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px;">Natural health and wellness solutions</p>
              <a href="#" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">View Services</a>
            </div>
            <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">Carrboro</h4>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px;">Holistic healthcare for the whole family</p>
              <a href="#" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">View Services</a>
            </div>
          </div>
        </div>
        
        <div class="letter-group" style="margin-bottom: 40px;">
          <h3 style="color: var(--red-accent); font-size: 1.8rem; margin-bottom: 20px;">D</h3>
          <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">Durham</h4>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px;">Complete integrative health services</p>
              <a href="#" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">View Services</a>
            </div>
          </div>
        </div>
        
        <div class="letter-group" style="margin-bottom: 40px;">
          <h3 style="color: var(--red-accent); font-size: 1.8rem; margin-bottom: 20px;">H</h3>
          <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">Hillsborough</h4>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px;">Our main location - comprehensive care</p>
              <a href="#" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">View Services</a>
            </div>
          </div>
        </div>
        
        <div class="letter-group" style="margin-bottom: 40px;">
          <h3 style="color: var(--red-accent); font-size: 1.8rem; margin-bottom: 20px;">R</h3>
          <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="city-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <h4 style="color: var(--primary-green); font-size: 1.3rem; margin: 0 0 10px 0;">Raleigh</h4>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 15px;">Serving the capital city with natural health</p>
              <a href="#" class="btn btn-red" style="font-size: 0.9rem; padding: 8px 16px;">View Services</a>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <!-- CTA Section -->
  <section class="service-areas-cta" style="background: var(--red-accent); color: white; padding: 80px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 300;">Don't See Your Area Listed?</h2>
      <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6;">We're always expanding our service areas. Contact us to see if we can serve your community.</p>
      <a href="/contact" class="btn" style="background: white; color: var(--red-accent); padding: 15px 30px; font-size: 1.1rem;">Contact Us</a>
    </div>
  </section>
</main>

<style>
.city-card:hover {
  transform: translateY(-3px);
}

@media (max-width: 768px) {
  .cities-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php get_footer(); ?>