 <!-- Cities List Section -->
  <section class="cities-list-section" style="background: var(--light-gray); padding: 60px 20px;">
    <div style="max-width: 1200px; margin: 0 auto; text-align: center;">
      <h2 style="color: var(--primary-green); font-size: 2rem; margin-bottom: 40px;">Service Areas</h2>
      
      <?php
      // Get all city posts
      $cities = get_posts(array(
        'post_type' => 'city',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC'
      ));
      
      if ($cities): ?>
        <div class="cities-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
          <?php foreach ($cities as $city): ?>
            <a href="<?php echo get_permalink($city->ID); ?>" style="color: var(--red-accent); text-decoration: none; padding: 10px; background: white; border-radius: 4px; transition: all 0.3s ease;">
              <?php echo get_the_title($city->ID); ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <a href="/service-areas" class="btn btn-red">All Service Areas</a>
    </div>
  </section>