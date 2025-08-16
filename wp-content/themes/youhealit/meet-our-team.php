<?php
// Template Name: Meet Our Team
get_header(); ?>

<main class="main-content">
  <!-- Team Hero Section -->
  <section class="page-hero" style="background: var(--primary-green); padding: 80px 20px; text-align: center;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h1 style="color: white; font-size: 3rem; margin-bottom: 20px; font-weight: 300;">MEET OUR TEAM</h1>
      <p style="color: white; font-size: 1.2rem; max-width: 600px; margin: 0 auto;">Dedicated professionals committed to your health and wellness</p>
    </div>
  </section>

  <!-- Team Grid -->
  <section class="team-section" style="max-width: 1200px; margin: 80px auto; padding: 0 20px;">
    <div class="team-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
      
      <?php
      // Get all team members
      $team_members = get_posts(array(
        'post_type' => 'team',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
      ));
      
      if ($team_members) :
        foreach ($team_members as $member) : 
          $photo = get_field('photo', $member->ID);
          $title = get_field('title', $member->ID);
          ?>
          <div class="team-member animated" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease;">
            <div class="member-image" style="height: 300px; overflow: hidden;">
              <?php if ($photo) : ?>
                <img src="<?php echo esc_url($photo['url']); ?>" alt="<?php echo esc_attr($member->post_title); ?>" style="width: 100%; height: 100%; object-fit: cover;">
              <?php else : ?>
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                  <?php echo substr($member->post_title, 0, 1); ?>
                </div>
              <?php endif; ?>
            </div>
            
            <div class="member-info" style="padding: 30px 25px;">
              <h3 style="color: var(--red-accent); margin: 0 0 10px 0; font-size: 1.4rem; font-weight: 600;">
                <?php echo $member->post_title; ?>
              </h3>
              
              <?php if ($title) : ?>
                <p style="color: var(--primary-green); margin: 0 0 15px 0; font-size: 1.1rem; font-weight: 500;">
                  <?php echo $title; ?>
                </p>
              <?php endif; ?>
              
              <?php if ($member->post_excerpt) : ?>
                <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                  <?php echo $member->post_excerpt; ?>
                </p>
              <?php endif; ?>
              
              <a href="<?php echo get_permalink($member->ID); ?>" class="btn btn-red" style="font-size: 0.9rem;">
                Learn More
              </a>
            </div>
          </div>
        <?php endforeach;
      else : ?>
        <!-- Default team members if none exist -->
        <div class="team-member" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center;">
          <div class="member-image" style="height: 300px; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
            DR
          </div>
          <div class="member-info" style="padding: 30px 25px;">
            <h3 style="color: var(--red-accent); margin: 0 0 10px 0; font-size: 1.4rem;">Dr. Paul Aaron</h3>
            <p style="color: var(--primary-green); margin: 0 0 15px 0; font-size: 1.1rem;">Chiropractor</p>
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
              Experienced chiropractor dedicated to helping patients achieve optimal health through natural healing methods.
            </p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
        
        <div class="team-member" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center;">
          <div class="member-image" style="height: 300px; background: linear-gradient(135deg, var(--red-accent), var(--primary-green)); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
            LN
          </div>
          <div class="member-info" style="padding: 30px 25px;">
            <h3 style="color: var(--red-accent); margin: 0 0 10px 0; font-size: 1.4rem;">Lee Needham</h3>
            <p style="color: var(--primary-green); margin: 0 0 15px 0; font-size: 1.1rem;">Certified Hypnotherapist</p>
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
              Certified hypnotherapist specializing in stress relief and wellness through mind-body techniques.
            </p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
        
        <div class="team-member" style="background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); text-align: center;">
          <div class="member-image" style="height: 300px; background: linear-gradient(135deg, var(--primary-green), var(--red-accent)); display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
            LW
          </div>
          <div class="member-info" style="padding: 30px 25px;">
            <h3 style="color: var(--red-accent); margin: 0 0 10px 0; font-size: 1.4rem;">LaVonda Walton</h3>
            <p style="color: var(--primary-green); margin: 0 0 15px 0; font-size: 1.1rem;">Reflexologist</p>
            <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
              Licensed reflexologist providing therapeutic foot care and holistic healing treatments.
            </p>
            <a href="#" class="btn btn-red">Learn More</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="team-cta" style="background: #f5f5f5; padding: 80px 20px; text-align: center;">
    <div style="max-width: 800px; margin: 0 auto;">
      <h2 style="color: var(--primary-green); font-size: 2.5rem; margin-bottom: 20px; font-weight: 300;">Ready to Meet Our Team?</h2>
      <p style="font-size: 1.1rem; color: var(--text-light); margin-bottom: 30px; line-height: 1.6;">
        Our caring professionals are here to listen to your concerns and develop a personalized treatment plan that's right for you.
      </p>
      <a href="/contact" class="btn btn-red" style="font-size: 1.1rem; padding: 15px 30px;">Schedule Your Consultation</a>
    </div>
  </section>
</main>

<style>
.team-member:hover {
  transform: translateY(-5px);
}

@media (max-width: 768px) {
  .team-grid {
    grid-template-columns: 1fr !important;
  }
}
</style>

<?php get_footer(); ?>