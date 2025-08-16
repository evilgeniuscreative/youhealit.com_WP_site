<?php
/*
Template Name: Testimonials
*/
get_header();
?>

<div class="container two-column-layout" style="display: flex; gap: 2rem; flex-wrap: wrap;">
  <main style="flex: 1 1 60%;">
    <h1 class="page-title"><?php the_title(); ?></h1>
    <div class="testimonials-wrapper">
      <?php echo do_shortcode('[testimonial_tabs]'); ?>
    </div>
  </main>

  <aside style="flex: 1 1 35%;">
    <h2>Featured Video Testimonials</h2>
    <?php
    $video_testimonials = new WP_Query(array(
      'post_type' => 'videos',
      'posts_per_page' => 5
    ));
    if ($video_testimonials->have_posts()) :
      while ($video_testimonials->have_posts()) : $video_testimonials->the_post(); ?>
        <div class="sidebar-item" style="margin-bottom: 1em;">
          <a href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a>
          <div><?php the_excerpt(); ?></div>
        </div>
      <?php endwhile;
      wp_reset_postdata();
    else : ?>
      <p>No video testimonials found.</p>
    <?php endif; ?>
  </aside>
</div>

<?php get_footer(); ?>
