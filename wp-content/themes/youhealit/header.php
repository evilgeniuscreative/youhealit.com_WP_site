<?php
/*
theme: youhealit
youhealit/header.php
WordPress header template that loads on every page. Includes DOCTYPE, head section, 
top alert bar, site header with logo, main navigation menu, and header contact info.
Functions used: language_attributes(), bloginfo(), wp_head(), body_class(), 
home_url(), has_custom_logo(), the_custom_logo(), wp_nav_menu()
*/
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- Top Alert Bar -->
<?php 
$alert_message = 'Concussion research project here, <span class="highlight">check it out!</span>';
if ($alert_message): ?>
  <div class="top-alert">
    <?php echo $alert_message; ?>
  </div>
<?php endif; ?>

<!-- Header -->
<header class="site-header">
    <div class="header-inner">
        <!-- Logo -->
        <a href="<?php echo home_url(); ?>" class="logo-link">
            <?php if (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <div class="text-logo">YouHealIt</div>
            <?php endif; ?>
        </a>

        <!-- Main Navigation -->
        <nav class="main-nav">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class' => 'nav-menu',
                'container' => false,
                'fallback_cb' => 'youhealit_fallback_menu'
            ]); ?>
        </nav>

        <!-- Header Info & Buttons -->
        <div class="header-info">
            <div class="phone-number">(919) 241-5092</div>
            <div class="header-buttons">
                <a href="/contact" class="btn-red">Request an Appointment Today!</a>
                <a href="/shop" class="btn-shop">Shop Now</a>
            </div>
        </div>
    </div>
</header>