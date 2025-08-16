<?php
// Template Name: About Page
get_header(); ?>

<main class="main-content">
  <!-- Hero Section with Green Background -->
  <section class="page-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 20px; font-weight: 300;">ABOUT</h1>
    </div>
  </section>

  <!-- Split Content Section -->
  <section class="split-content-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div class="split-section animated">
      <div class="content-left">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
          <div class="page-content">
            <?php the_content(); ?>
          </div>
        <?php endwhile; endif; ?>
      </div>
      <div class="content-right">
        <?php if (has_post_thumbnail()) : ?>
          <div class="featured-image">
            <?php the_post_thumbnail('large'); ?>
          </div>
        <?php else: ?>
          <img src="<?php echo get_template_directory_uri(); ?>/images/about-image.jpg" alt="About Health Center" style="width: 100%; height: auto; border-radius: 8px;">
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Gray Information Section -->
  <section class="info-section" style="background: #f5f5f5; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <div class="split-section">
        <div class="content-left">
          <h2 style="color: var(--primary-green); font-size: 2.5rem; margin-bottom: 30px; font-weight: 300;">Your Natural, Conservative Options</h2>
        </div>
        <div class="content-right">
          <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 20px;">There is a time and a place for interventions such as surgery and medications. We do believe, however, that you owe it to yourself to look into safer, more conservative means of resolving your health concerns before resorting to them. As the North Carolina Chiropractic Association states, "Chiropractic before opioids." Let us work naturally to let your true health flourish!</p>
          
          <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px;">We welcome everyone at our practice, including babies, children, athletes, car accident victims and senior citizens. Whether you want to perform at a higher level in your life or get out of pain, we can help you reach your goals.</p>
          
          <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 30px;">
            <p style="font-style: italic; font-size: 1rem; line-height: 1.6; margin-bottom: 20px;">In today's medical world, people are often feeling ignored or that their concerns are overlooked by their doctors. We respect your self-knowledge and opinions. At your first visit, we want to know everything that's happened and how you've been affected. No detail is too small for us to know, and we want them all from you! It's like putting together a puzzle. We need every piece to see the full picture. You'll feel listened to and respected by all of our practitioners.</p>
            
            <a href="#" class="btn btn-red">Meet Our Team</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Philosophy Section -->
  <section class="philosophy-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div class="split-section">
      <div class="content-left">
        <h2 style="color: var(--primary-green); font-size: 2.5rem; margin-bottom: 30px; font-weight: 300;">Our Chiropractic Philosophy</h2>
      </div>
      <div class="content-right">
        <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 20px;">Our practice is dedicated to bringing the knowledge of our practitioners to our community and every individual in it. Using our expertise, we want to bring you to the best of health. We'll do so by starting with a comprehensive consultation and examination, collaborating with you. Together, we'll determine the direction we need to move to accomplish your goals. No matter what your age or level of health, you still have an opportunity to achieve greater well-being.</p>
        
        <p style="font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px;">As chiropractors, we'll remove the obstacles to your healing so that you can experience a restoration of your wellness.</p>
        
        <h3 style="color: var(--primary-green); font-size: 1.8rem; margin-bottom: 20px;">Learn More Today</h3>
        <p style="font-size: 1rem; line-height: 1.6; margin-bottom: 30px;">Don't wait to get the attention your well-being needs and deserves. <a href="/contact" style="color: var(--primary-green);">Contact our team</a> today to book a same-day appointment!</p>
      </div>
    </div>
  </section>
</main>

<?php get_footer(); ?>