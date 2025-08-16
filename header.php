<?php
// File: /header.php
// Purpose: Global site header matching the target design
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php wp_title('|', true, 'right'); ?></title>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css?v=<?php echo time(); ?>" type="text/css" media="all">

  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
// Top alert bar - Red banner
$alert_message = 'Concussion research project here, <span class="highlight">check it out!</span>';
if ($alert_message): ?>
  <div class="top-alert"><?php echo $alert_message; ?></div>
<?php endif; ?>

<header class="site-header">
  <div class="header-inner">
    <!-- Logo -->
    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo-link">
      <img src="<?php echo get_template_directory_uri(); ?>/images/logo.png" alt="<?php bloginfo('name'); ?>" class="site-logo">
    </a>
    
    <!-- Navigation Menu -->
    <nav class="main-nav">
      <?php
      wp_nav_menu(array(
        'theme_location' => 'primary',
        'menu_class' => 'nav-menu',
        'fallback_cb' => function() {
          // Fallback menu if no menu is set
          echo '<ul class="nav-menu">
            <li><a href="' . home_url('/') . '">HOME</a></li>
            <li><a href="' . home_url('/about') . '">ABOUT</a></li>
            <li><a href="' . home_url('/meet-our-team') . '">MEET OUR TEAM</a></li>
            <li><a href="' . home_url('/new-patients') . '">NEW PATIENTS</a></li>
            <li><a href="' . home_url('/services') . '">SERVICES</a></li>
            <li><a href="' . home_url('/testimonials') . '">TESTIMONIALS</a></li>
            <li><a href="' . home_url('/contact') . '">CONTACT</a></li>
          </ul>';
        }
      ));
      ?>
    </nav>
    
    <!-- Header Info & Buttons -->
    <div class="header-info">
      <div class="phone-number" style="color: white; font-size: 14px; margin-bottom: 5px;">
        Call Us: <strong>(919) 241-5092</strong>
      </div>
      <div class="header-buttons">
        <a href="#" class="btn btn-red">REQUEST AN APPOINTMENT TODAY!</a>
        <a href="#" class="btn btn-shop">SHOP NOW</a>
      </div>
    </div>
  </div>
</header>