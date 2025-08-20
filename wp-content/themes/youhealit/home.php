<?php
// File: /home.php
// Purpose: Blog posts index page
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
    <a href="<?php echo defined('YHI_APPT_URL') ? YHI_APPT_URL : 'https://youhealit.com/contact/'; ?>" class="btn btn-red">Request an Appointment Today!</a>
    <a href="#" class="btn btn-shop">Shop Now</a>
  </div>
</header>

<div class="container">
    <main class="blog-main">
        <h1>Latest News & Updates</h1>
        
        <?php if (have_posts()) : ?>
            <div class="posts-grid">
                <?php while (have_posts()) : the_post(); ?>
                    <article class="post-item">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="post-content">
                            <h2 class="post-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="post-meta">
                                <span class="post-date"><?php echo get_the_date(); ?></span>
                                <span class="post-author">by <?php the_author(); ?></span>
                            </div>
                            
                            <div class="post-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="read-more">Read More</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php
                the_posts_pagination(array(
                    'prev_text' => '&laquo; Previous',
                    'next_text' => 'Next &raquo;',
                ));
                ?>
            </div>
            
        <?php else : ?>
            <p>No posts found.</p>
        <?php endif; ?>
    </main>
</div>

<?php get_footer(); ?>
