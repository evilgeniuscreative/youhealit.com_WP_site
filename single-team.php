<?php
// File: /single-team.php
// Purpose: Template for displaying single Team Member CPT entries
get_header(); ?>

<main class="main-content">
  <?php while (have_posts()) : the_post(); 
    $photo = get_field('photo');
    $title = get_field('title');
  ?>
  
  <!-- Team Member Hero Section -->
  <section class="team-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 10px; font-weight: 300;"><?php the_title(); ?></h1>
      <?php if ($title) : ?>
        <p style="color: white; font-size: 1.3rem; font-weight: 300;"><?php echo $title; ?></p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Team Member Profile Section -->
  <section class="team-profile-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div class="split-section animated">
      <div class="content-left">
        <?php if ($photo) : ?>
          <div class="profile-image" style="text-align: center; margin-bottom: 30px;">
            <img src="<?php echo esc_url($photo['url']); ?>" 
                 alt="<?php echo esc_attr(get_the_title()); ?>" 
                 style="width: 100%; max-width: 400px; height: auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          </div>
        <?php endif; ?>
        
        <!-- Contact Info if available -->
        <div class="team-contact" style="background: #f5f5f5; padding: 30px; border-radius: 8px; margin-top: 30px;">
          <h3 style="color: var(--red-accent); font-size: 1.3rem; margin-bottom: 20px;">Contact <?php echo get_the_title(); ?></h3>
          <p style="margin-bottom: 15px;">
            <strong>Phone:</strong> <a href="tel:${YHI_PHONE}" style="color: var(--red-accent);">${YHI_PHONE}</a>
          </p>
          <p style="margin-bottom: 20px;">
            <strong>Email:</strong> <a href="mailto:${YHI_EMAIL}" style="color: var(--red-accent);">${YHI_EMAIL}</a>
          </p>
          <a href="/contact" class="btn btn-red">Schedule Appointment</a>
        </div>
      </div>
      
      <div class="content-right">
        <div class="team-bio">
          <?php if (has_excerpt()) : ?>
            <div class="team-excerpt" style="font-size: 1.2rem; color: var(--text-light); font-style: italic; margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-left: 4px solid var(--primary-green); border-radius: 4px;">
              <?php the_excerpt(); ?>
            </div>
          <?php endif; ?>
          
          <div class="team-content" style="font-size: 1.1rem; line-height: 1.7; color: var(--text-dark);">
            <?php the_content(); ?>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Services/Specialties Section -->
  <section class="team-specialties" style="background: #f5f5f5; padding: 80px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h2 style="color: var(--primary-green); font-size: 2.5rem; text-align: center; margin-bottom: 50px; font-weight: 300;">
        Services & Specialties
      </h2>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
        <!-- These could be populated from ACF fields or custom taxonomy -->
        <div class="specialty-card" style="background: white; padding: 25px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
          <div style="color: var(--red-accent); font-size: 2rem; margin-bottom: 15px;">🩺</div>
          <h3 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 10px;">Primary Care</h3>
          <p style="color: var(--text-light); font-size: 0.95rem;">Comprehensive health assessments and treatment plans</p>
        </div>
        
        <div class="specialty-card" style="background: white; padding: 25px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
          <div style="color: var(--red-accent); font-size: 2rem; margin-bottom: 15px;">🌿</div>
          <h3 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 10px;">Natural Healing</h3>
          <p style="color: var(--text-light); font-size: 0.95rem;">Holistic approaches to health and wellness</p>
        </div>
        
        <div class="specialty-card" style="background: white; padding: 25px; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
          <div style="color: var(--red-accent); font-size: 2rem; margin-bottom: 15px;">💪</div>
          <h3 style="color: var(--text-dark); font-size: 1.2rem; margin-bottom: 10px;">Rehabilitation</h3>
          <p style="color: var(--text-light); font-size: 0.95rem;">Recovery and strengthening programs</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Navigation to Other Team Members -->
  <section class="team-navigation" style="max-width: 1200px; margin: 60px auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
      <a href="/meet-our-team" class="btn" style="background: var(--primary-green); color: white; padding: 12px 25px;">
        ← Back to Team
      </a>
      
      <div style="display: flex; gap: 15px;">
        <?php
        // Get previous and next team members
        $prev_post = get_previous_post(false, '', 'team');
        $next_post = get_next_post(false, '', 'team');
        
        if ($prev_post) : ?>
          <a href="<?php echo get_permalink($prev_post->ID); ?>" class="btn btn-red" style="padding: 12px 20px;">
            ← <?php echo get_the_title($prev_post->ID); ?>
          </a>
        <?php endif;
        
        if ($next_post) : ?>
          <a href="<?php echo get_permalink($next_post->ID); ?>" class="btn btn-red" style="padding: 12px 20px;">
            <?php echo get_the_title($next_post->ID); ?> →
          </a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="team-cta" style="background: var(--red-accent); color: white; padding: 80px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 300;">Ready to Work with <?php echo get_the_title(); ?>?</h2>
      <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6;">
        Schedule your consultation today and take the first step toward better health and wellness.
      </p>
      <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
        <a href="tel:${YHI_PHONE}" class="btn" style="background: white; color: var(--red-accent); padding: 15px 30px; font-size: 1.1rem;">
          Call (919) -5092
        </a>
        <a href="/contact" class="btn" style="background: transparent; color: white; border: 2px solid white; padding: 15px 30px; font-size: 1.1rem;">
          Schedule Online
        </a>
      </div>
    </div>
  </section>

  <?php endwhile; ?>
</main>

<style>
.specialty-card:hover {
  transform: translateY(-3px);
  transition: transform 0.3s ease;
}

@media (max-width: 768px) {
  .team-navigation > div {
    flex-direction: column;
    align-items: stretch;
  }
  
  .team-navigation .btn {
    text-align: center;
  }
  
  .split-section {
    flex-direction: column;
  }
  
  .content-left {
    order: 2;
  }
  
  .content-right {
    order: 1;
  }
}
</style>

<?php get_footer(); ?>