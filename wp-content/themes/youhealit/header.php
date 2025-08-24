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


<!-- Header -->
<header id="site-header" class="main-header regular_header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
    <!-- <div class="header-spacer" style="height: 156px;"></div> -->

    <div class="main-head-wrap">
        <!-- Top Header Bar -->
        <div class="top-header">
            <p>Concussion research project: <a href="<?php echo home_url('/concussion-treatment/'); ?>">take a look.</a>If you have or have had a concussion, please consider <a href="<?php echo home_url('/concussion-treatment/'); ?>">participating</a> in our research study.</p>
        </div>

        <!-- Upper Header Info Row -->
        <div id="regular-header">
            <div class="container-wide dm-flex">
                <div class="dm-third phone-header">
                    <p><i class="fa fa-phone-alt"></i><a href="tel:<?php // echo YHI_PHONE  ?>"><?php // echo YHI_PHONE  ?></a></p>
                </div>
                <div class="dm-third address-header">
                    <p><i class="fa fa-map-marker-alt"></i><a href="https://maps.app.goo.gl/ap8WekMqJJTcSsjL8" target="_blank" rel="noreferrer noopener"> Our <strong> Hillsborough, NC </strong> Office </a></p>
                </div>
                <div class="dm-third social-header">
                    <div class="social-icons">
                        <a href="https://www.facebook.com/HealthCenterHillsborough?fref=ts" class="header-facebook" target="_blank" rel="noreferrer noopener">
                            <span class="fa fa-facebook"></span>
                        </a>
                        <a href="https://maps.app.goo.gl/sb7f7htAMLSCew4KA" class="header-google" target="_blank" rel="noreferrer noopener">
                            <span class="fa fa-google"></span>
                        </a>
                        <a href="https://twitter.com/HillsboroughHC" class="header-twitter" target="_blank" rel="noreferrer noopener">
                            <span class="fa fa-twitter"></span>
                        </a>
                        <a href="https://www.youtube.com/channel/UC86mQ-N9EPrM5RM0B18xGDw" class="header-youtube" target="_blank" rel="noreferrer noopener">
                            <span class="fa fa-youtube"></span>
                        </a>
                        <a href="http://www.yelp.com/biz/health-center-of-hillsborough-hillsborough" class="header-yelp" target="_blank" rel="noreferrer noopener">
                            <span class="fa fa-yelp"></span>
                        </a>
                        <a href="http://www.pinterest.com/HillsboroHealth/" class="header-pinterest-p" target="_blank" rel="noreferrer noopener">
                            <span class="fa fa-pinterest-p"></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Header with Logo and Navigation -->
        <div id="header">
            <div class="container-wide">
                <div id="secondary-navigation" class="secondary-navigation" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
                    
                    <!-- Logo Section -->
                    <div class="logo-wrap">
                        <h1 id="logo" class="image-logo" itemprop="headline">
                            <a href="<?php echo home_url(); ?>">
                                <?php if (has_custom_logo()): ?>
                                    <?php the_custom_logo(); ?>
                                <?php else: ?>
                                    <img src="<?php echo get_template_directory_uri(); ?>/images/logo-white.png" alt="<?php echo get_bloginfo('name'); ?>">
                                <?php endif; ?>
                            </a>
                        </h1>
                    </div>

                    <!-- Right Side Header Info & Navigation -->
                    <div class="right-side-header">
                        <div>
                            <p>Call Us: <a href="tel:<?php // echo YHI_PHONE ?>"><?php // echo YHI_PHONE ?></a></p>
                            <a href="<?php echo home_url('/appointments/'); ?>" class="btn"><?php echo YHI_APPT_TXT ?></a>
                            <a href="<?php  //echo YHI_SHOP_URL ?>" target="_blank" class="btn"><?php echo YHI_SHOP_TXT ?></a>
                        </div>

                        <!-- Main Navigation Menu -->
                        <nav class="navigation clearfix mobile-menu-wrapper">
                            <?php wp_nav_menu([
                                'theme_location' => 'primary',
                                'menu_id' => 'menu-main-menu',
                                'menu_class' => 'menu clearfix toggle-menu',
                                'container' => false,
                                'fallback_cb' => 'youhealit_fallback_menu',
                                'walker' => new YouHealIt_Walker_Nav_Menu()
                            ]); ?>
                            <a href="#" id="pull" class="toggle-mobile-menu"></a>
                        </nav>
                    </div>
                    <div id="mobile-menu-overlay"></div>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
function changeHeaderBG(){
    if(window.scrollY >= 35){
        console.log('greater than 35');
        document.getElementById('header').classList.add('header-scrolled');
    } else {
        console.log('less than 35');
        document.getElementById('header').classList.remove('header-scrolled');
    }
}
document.addEventListener('scroll', changeHeaderBG);
</script>   