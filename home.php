<?php
// File: /home.php
// Purpose: Custom homepage template with video header, ACF repeater carousel, and animated split section
get_header(); ?>

<?php
// Optional top alert (can be extended to pull from CPT or theme option)
$alert_message = 'Concussion research project here, <span class="highlight">check it out!</span>';
if ($alert_message): ?>
  <div class="top-alert">
    <?php echo $alert_message; ?>
  </div>
<?php endif; ?>

<header class="header-main">
  <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="Site Logo" class="site-logo">
  <nav class="main-nav">
    <?php wp_nav_menu(['theme_location' => 'primary']); ?>
  </nav>
  <div class="header-buttons">
    <a href="#" class="btn btn-red">Request an Appointment Today!</a>
    <a href="#" class="btn btn-shop">Shop Now</a>
  </div>
</header>

<section class="video-header">
  <video autoplay muted loop playsinline poster="<?php echo get_template_directory_uri(); ?>/images/video-fallback.jpg">
    <source src="<?php echo get_template_directory_uri(); ?>/assets/video/placeholder.mp4" type="video/mp4">
  </video>
</section>

<section class="carousel-wrapper">
  <div class="carousel">
    <?php if (have_rows('homepage_carousel')): ?>
      <?php while (have_rows('homepage_carousel')): the_row(); ?>
        <div class="carousel-item">
          <img src="<?php the_sub_field('carousel_image'); ?>" alt="<?php the_sub_field('carousel_title'); ?>">
          <h3><?php the_sub_field('carousel_title'); ?></h3>
          <p><?php the_sub_field('carousel_text'); ?></p>
          <a href="<?php the_sub_field('carousel_cta_link'); ?>" class="btn btn-cta">
            <?php the_sub_field('carousel_cta_text'); ?>
          </a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No carousel items found.</p>
    <?php endif; ?>
  </div>
  <div class="carousel-nav">
    <button class="carousel-prev">&#10094;</button>
    <button class="carousel-next">&#10095;</button>
  </div>
</section>

<section class="split-section animated">
  <div class="content-left">
    <h2>Welcome To Health Center of Hillsborough</h2>
    <p>Empowering natural healing, performance, and prevention. Let your body do what it was made to do.</p>
  </div>
  <div class="content-right">
    <h3>Concussion Treatment & Recovery</h3>
    <p>Discover our science-backed therapies and individualized programs.</p>
    <a href="#" class="btn btn-red">Request an Appointment</a>
  </div>
</section>

<?php get_footer(); ?>
