<?php
// File: /taxonomy-service_category.php
// Purpose: Template for service category pages (e.g., /north-carolina/services/chiropractic-care/)
get_header(); 

$term = get_queried_object();
$service_name = $term->name;
$service_description = $term->description;
?>

<main class="main-content">
  <!-- Service Hero Section -->
  <section class="service-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 15px; font-weight: 300;">
        <?php echo $service_name; ?> in North Carolina
      </h1>
      <p style="color: white; font-size: 1.3rem; font-weight: 300; max-width: 800px; margin: 0 auto;">
        <?php echo $service_description ?: "Professional $service_name services throughout North Carolina"; ?>
      </p>
    </div>
  </section>

  <!-- Service Content Section -->
  <section class="service-content-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div class="split-section animated">
      <div class="content-left">
        <div class="service-description" style="font-size: 1.1rem; line-height: 1.7; color: var(--text-dark);">
          <?php if ($service_description) : ?>
            <?php echo wpautop($service_description); ?>
          <?php else : ?>
            <?php
            // Generate service-specific content based on the service type
            $service_slug = $term->slug;
            switch ($service_slug) {
              case 'chiropractic-care':
              case 'chiropractic':
                echo '<p>Our <strong>' . $service_name . '</strong> services focus on diagnosing and treating neuromuscular disorders, with a special emphasis on treating these disorders through manual adjustment and manipulation of the spine.</p>';
                echo '<p>Whether you\'re dealing with back pain, neck pain, headaches, or other musculoskeletal issues, our experienced chiropractors use gentle, effective techniques to restore proper alignment and function to your spine and nervous system.</p>';
                break;
              case 'acupuncture':
                echo '<p><strong>' . $service_name . '</strong> is an ancient Chinese healing practice that involves inserting thin needles into specific points on the body to promote natural healing and improve functioning.</p>';
                echo '<p>Our licensed acupuncturists treat a wide range of conditions including chronic pain, stress, anxiety, digestive issues, and more. Each treatment is customized to your individual needs and health goals.</p>';
                break;
              case 'massage-therapy':
                echo '<p>Our therapeutic <strong>' . $service_name . '</strong> services combine relaxation with targeted treatment to address muscle tension, improve circulation, and promote overall wellness.</p>';
                echo '<p>From Swedish massage for relaxation to deep tissue work for chronic pain, our licensed massage therapists tailor each session to your specific needs and comfort level.</p>';
                break;
              case 'nutritional-counseling':
              case 'nutrition':
                echo '<p><strong>' . $service_name . '</strong> plays a crucial role in your overall health and healing process. Our certified nutritionists work with you to develop personalized nutrition plans that support your health goals.</p>';
                echo '<p>We address everything from weight management and digestive health to nutritional support for chronic conditions, using whole food approaches and evidence-based recommendations.</p>';
                break;
              case 'concussion-treatment':
                echo '<p>Our specialized <strong>' . $service_name . '</strong> program combines cutting-edge assessment techniques with proven rehabilitation methods to help patients recover from traumatic brain injuries.</p>';
                echo '<p>We understand that each concussion is unique, which is why we develop individualized treatment plans that may include cognitive rehabilitation, balance training, and gradual return-to-activity protocols.</p>';
                break;
              default:
                echo '<p>Our <strong>' . $service_name . '</strong> services are designed to address your specific health needs using natural, evidence-based approaches that support your body\'s innate healing abilities.</p>';
                echo '<p>We take a comprehensive approach to care, considering not just your symptoms but your overall health and wellness goals to develop the most effective treatment plan for you.</p>';
            }
            ?>
          <?php endif; ?>
          
          <div class="service-benefits" style="background: #f9f9f9; padding: 30px; border-radius: 8px; margin: 30px 0; border-left: 4px solid var(--primary-green);">
            <h3 style="color: var(--red-accent); font-size: 1.4rem; margin-bottom: 20px;">Benefits of <?php echo $service_name; ?>:</h3>
            <ul style="columns: 2; column-gap: 30px; list-style-type: none; padding: 0;">
              <?php
              // Generate benefits based on service type
              $benefits = array();
              switch ($service_slug) {
                case 'chiropractic-care':
                case 'chiropractic':
                  $benefits = ['Pain relief without medication', 'Improved posture and alignment', 'Enhanced nervous system function', 'Better sleep quality', 'Increased range of motion', 'Reduced inflammation'];
                  break;
                case 'acupuncture':
                  $benefits = ['Natural pain management', 'Stress and anxiety reduction', 'Improved sleep patterns', 'Enhanced immune function', 'Better digestion', 'Hormonal balance'];
                  break;
                case 'massage-therapy':
                  $benefits = ['Muscle tension relief', 'Improved circulation', 'Stress reduction', 'Better flexibility', 'Enhanced recovery', 'Deep relaxation'];
                  break;
                case 'nutritional-counseling':
                  $benefits = ['Optimal nutrient absorption', 'Sustainable weight management', 'Improved energy levels', 'Better digestive health', 'Enhanced immune function', 'Disease prevention'];
                  break;
                case 'concussion-treatment':
                  $benefits = ['Faster recovery times', 'Reduced symptoms', 'Improved cognitive function', 'Better balance and coordination', 'Safe return to activities', 'Long-term brain health'];
                  break;
                default:
                  $benefits = ['Natural healing approach', 'Personalized treatment plans', 'Improved quality of life', 'Long-lasting results', 'Holistic wellness', 'Expert care'];
              }
              
              foreach ($benefits as $benefit) {
                echo '<li style="margin-bottom: 10px; padding-left: 20px; position: relative;">';
                echo '<span style="position: absolute; left: 0; color: var(--primary-green);">✓</span> ' . $benefit;
                echo '</li>';
              }
              ?>
            </ul>
          </div>
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
                switch ($service_slug) {
                  case 'chiropractic-care':
                  case 'chiropractic':
                    echo '🦴';
                    break;
                  case 'acupuncture':
                    echo '🌿';
                    break;
                  case 'massage-therapy':
                    echo '💆';
                    break;
                  case 'nutritional-counseling':
                    echo '🥗';
                    break;
                  case 'concussion-treatment':
                    echo '🧠';
                    break;
                  default:
                    echo '🩺';
                }
                ?>
              </div>
              <?php echo $service_name; ?>
            </div>
          </div>
        </div>
        
        <!-- Quick Contact Card -->
        <div class="quick-contact" style="background: var(--red-accent); color: white; padding: 30px; border-radius: 8px; text-align: center;">
          <h3 style="margin: 0 0 20px 0; font-size: 1.4rem;">Schedule Your <?php echo $service_name; ?> Appointment</h3>
          <p style="margin-bottom: 25px; font-size: 1.1rem;">Ready to experience the benefits of professional <?php echo strtolower($service_name); ?>?</p>
          <div style="display: flex; flex-direction: column; gap: 15px;">
            <a href="tel:${YHI_PHONE}" class="btn" style="background: white; color: var(--red-accent); padding: 15px 25px;">Call ${YHI_PHONE}</a>
            <a href="/contact" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 15px 25px;">Schedule Online</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Cities Offering This Service -->
  <section class="service-cities" style="background: #f5f5f5; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h2 style="color: var(--primary-green); font-size: 2.5rem; text-align: center; margin-bottom: 50px; font-weight: 300;">
        <?php echo $service_name; ?> Available in These Areas
      </h2>
      
      <?php
      // Get all cities that offer this service
      $cities = get_posts(array(
        'post_type' => 'city',
        'posts_per_page' => 20,
        'orderby' => 'title',
        'order' => 'ASC'
      ));
      
      if ($cities) : ?>
        <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
          <?php foreach ($cities as $city) : 
            $city_name = get_field('city_name', $city->ID) ?: $city->post_title;
            $city_zip = get_field('city_zip', $city->ID);
            ?>
            <div class="city-service-card" style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease;">
              <h3 style="color: var(--red-accent); font-size: 1.2rem; margin-bottom: 15px;">
                <?php echo $city_name; ?>
                <?php if ($city_zip) : ?>
                  <span style="font-size: 0.9rem; color: var(--text-light); display: block;"><?php echo $city_zip; ?></span>
                <?php endif; ?>
              </h3>
              <p style="color: var(--text-light); font-size: 0.95rem; margin-bottom: 20px;">
                Professional <?php echo strtolower($service_name); ?> services
              </p>
              <a href="<?php echo get_permalink($city->ID); ?>" class="btn btn-red" style="font-size: 0.9rem; padding: 10px 20px;">
                Learn More
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <div style="text-align: center; margin-top: 40px;">
        <a href="/service-areas" class="btn btn-red" style="font-size: 1.1rem; padding: 15px 30px;">View All Service Areas</a>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="service-cta" style="background: var(--red-accent); color: white; padding: 80px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 300;">Ready to Experience <?php echo $service_name; ?>?</h2>
      <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6;">
        Take the first step toward better health with our professional <?php echo strtolower($service_name); ?> services. 
        Contact us today to schedule your consultation and discover how we can help you achieve your wellness goals.
      </p>
      <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="tel:${YHI_PHONE}" class="btn" style="background: white; color: var(--red-accent); padding: 15px 30px; font-size: 1.1rem;">
          Call ${YHI_PHONE}
        </a>
        <a href="/contact" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 15px 30px; font-size: 1.1rem;">
          Schedule Consultation
        </a>
      </div>
    </div>
  </section>
</main>

<style>
.city-service-card:hover {
  transform: translateY(-3px);
}

@media (max-width: 768px) {
  .split-section {
    flex-direction: column;
  }
  
  .cities-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php get_footer(); ?>