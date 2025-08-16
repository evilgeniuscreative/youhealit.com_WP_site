<?php get_header(); ?>
<div class="container">
  <h1>Blog</h1>
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article class="post">
      <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <div><?php the_excerpt(); ?></div>
    </article>
  <?php endwhile; else: ?>
    <p>No posts found.</p>
  <?php endif; ?>
</div>
<?php get_footer(); ?>
