<?php
// File: /page.php
// Purpose: Default page template with animated split layout and featured image
get_header(); ?>

<main class="main-content">
  <section class="split-section animated">
    <div class="content-left">
      <h1><?php the_title(); ?></h1>
      <div class="page-content">
        <?php
        while (have_posts()) : the_post();
          the_content();
        endwhile;
        ?>
      </div>
    </div>
    <div class="content-right">
      <?php if (has_post_thumbnail()) : ?>
        <div class="featured-image">
          <?php the_post_thumbnail('large'); ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php get_footer(); ?>
