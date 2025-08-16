<?php
// File: /single.php
// Purpose: Default single post view for posts without custom templates
get_header(); ?>

<main class="main-content">
  <article class="single-post animated">
    <header class="post-header">
      <h1><?php the_title(); ?></h1>
      <div class="post-meta">
        <span class="date"><?php echo get_the_date(); ?></span>
        <span class="author">by <?php the_author(); ?></span>
      </div>
    </header>

    <div class="post-content">
      <?php
      while (have_posts()) : the_post();
        the_content();
      endwhile;
      ?>
    </div>
  </article>
</main>

<?php get_footer(); ?>
