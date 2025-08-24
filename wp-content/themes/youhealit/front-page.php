<?php get_header(); ?>

<div id="page" class="homepage">
  <div class="container-wide homepage-top-half">
  <div class="video-header">
    <video autoplay muted loop playsinline id="header-video">
      <source src="<?php echo get_template_directory_uri(); ?>/assets/video/placeholder.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
    <div class="video-overlay" data-note="Overlays the video for the carousel">
      <!-- Carousel over the video -->
      <div id="carousel-wrapper" class="carousel-wrapper insidehomepage">
        <button class="carousel-nav carousel-prev" onclick="moveCarousel(-1)">&#10094;</button>
        <div class="carousel" id="carousel">
          <?php if (function_exists('have_rows') && have_rows('homepage_carousel')): ?>
            <?php while (have_rows('homepage_carousel')): the_row(); ?>
              <div class="carousel-item">
                <?php if (get_sub_field('carousel_image')): ?>
                  <img src="<?php the_sub_field('carousel_image'); ?>" alt="<?php the_sub_field('carousel_title'); ?>">
                <?php endif; ?>
                <div class="content">
                  <h3><?php the_sub_field('carousel_title'); ?></h3>
                  <p><?php the_sub_field('carousel_text'); ?></p>
              <?php if (get_sub_field('carousel_cta_link')): ?>
                <a href="<?php the_sub_field('carousel_cta_link'); ?>" class="btn-cta">
                  <?php the_sub_field('carousel_cta_text') ?: 'Learn More'; ?>
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <!-- Default carousel items if ACF fields aren't set up -->
        <div class="carousel-item">
          <img src="<?php echo get_template_directory_uri(); ?>/images/service1.jpg" alt="Service 1">
          <div class="content">
            <h3>Chiropractic Care</h3>
            <p>Expert spinal adjustments and therapeutic treatments.</p>
            <a href="#" class="btn-cta">Learn More</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="<?php echo get_template_directory_uri(); ?>/images/service2.jpg" alt="Service 2">
          <div class="content">
            <h3>Concussion Treatment</h3>
            <p>Specialized care for head injuries and recovery.</p>
            <a href="#" class="btn-cta">Learn More</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="<?php echo get_template_directory_uri(); ?>/images/service3.jpg" alt="Service 3">
          <div class="content">
            <h3>Nutritional Consulting</h3>
            <p>Personalized nutrition plans for optimal health.</p>
            <a href="#" class="btn-cta">Learn More</a>
          </div>
        </div>
        <div class="carousel-item">
          <img src="<?php echo get_template_directory_uri(); ?>/images/service4.jpg" alt="Service 4">
          <div class="content">
            <h3>Massage Therapy</h3>
            <p>Therapeutic massage for pain relief and relaxation.</p>
            <a href="#" class="btn-cta">Learn More</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <button class="carousel-nav carousel-next" onclick="moveCarousel(1)">&#10095;</button>
  </div>
  <!-- end carousel -->
    </div>
  </div>
</div>
<!-- end video header -->
<div class="container-wide  homepage-bottom-half">


  <main class="main-content">
  <!-- Welcome Section -->
  <section class="welcome-section">
    <div class="split-section animated">
      <div class="content-left">
        <h2>Welcome To Wellness Center of the Triangle NC</h2>
      </div>
      <div class="content-right">
        <h3>Holistic Health. Remarkable Treatment For Head, Foot or Back Pain.</h3>
        <p>At Wellness Center of the Triangle NC, we're the facilitators of your healing, but You Heal It! We'll give you the care and support you need to reach your desired outcomes. Let your body's natural abilities take over and live your best life with us!</p>
        <p>Triangle NC Chiropractor Dr. Paul Aaron and his team provide you with a myriad of solutions under one roof. When you choose <span style="color: var(--primary-green);">Wellness Center of the Triangle</span> to support your health, you'll benefit from our plans of care, and your individualized plan can involve a combination of these options depending on what is most suitable for you.</p>
        <a href="#" class="btn btn-red">Request an Appointment</a>
      </div>
    </div>
  </section>
  <!-- end welcome section -->

  <!-- Red CTA Section -->
  <section class="cta-section">
    <div class="cta-split">
      <div class="cta-image">
        <img src="<?php echo get_template_directory_uri(); ?>/images/treatment.jpg" alt="Treatment">
      </div>
      <div class="cta-content">
        <h2>We'll Work With You</h2>
        <p>We firmly believe that finances should never get in the way of healthcare. Our standard pricing is competitive. If you have a financial hardship, we'll be happy to discuss other arrangements with you. Contact us today to get started!</p>
        <a href="#" class="btn" style="background: white; color: var(--red-accent);">Request an Appointment</a>
      </div>
    </div>
  </section>
  <!-- end red cta section -->

  <!-- Services Section -->
  <section class="services-section">
    <h2>Our Collaborative Approach</h2>
    <p style="margin-bottom: 40px; color: var(--text-light);">You won't have to worry about going to multiple offices to get the attention you need. Our integrative health center has the natural solutions you want at a single location. In our soothing, tranquil environment, let our team collaborate to get you the results you've been hoping for.</p>
    
    <div class="services-grid">
      <div class="service-card">
        <img src="<?php echo get_template_directory_uri(); ?>/images/acupuncture.jpg" alt="Acupuncture">
        <div class="overlay">
          <h3>Acupuncture</h3>
        </div>
      </div>
      <div class="service-card">
        <img src="<?php echo get_template_directory_uri(); ?>/images/chiropractic.jpg" alt="Chiropractic Care">
        <div class="overlay">
          <h3>Chiropractic Care</h3>
        </div>
      </div>
      <div class="service-card">
        <img src="<?php echo get_template_directory_uri(); ?>/images/massage.jpg" alt="Massage Therapy">
        <div class="overlay">
          <h3>Massage Therapy</h3>
        </div>
      </div>
      <div class="service-card">
        <img src="<?php echo get_template_directory_uri(); ?>/images/nutrition.jpg" alt="Nutritional Consultations">
        <div class="overlay">
          <h3>Nutritional Consultations</h3>
        </div>
      </div>
    </div>
    
    <div style="text-align: center; margin-top: 40px;">
      <a href="#" class="btn btn-red">Read More</a>
    </div>
  </section>
  <!-- End Services Section -->

  <!-- Map Section -->
  <?php get_template_part('template-parts/hch-map'); ?>
<!-- end map section -->
 
  </main>
</div>

<!-- end homepage bottom half -->
</div>
<!-- end homepage -->
<script>
function moveCarousel(direction) {
  const carousel = document.getElementById('carousel');
  const scrollAmount = carousel.querySelector('.carousel-item').offsetWidth + 20;
  carousel.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
}

</script>

<?php get_footer(); ?>