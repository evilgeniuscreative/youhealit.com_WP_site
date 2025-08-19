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
<header class="site-header helloworld <?php echo is_front_page() ? 'homepage-header' : ''; ?>">
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
            <div class="phone-number"><?php echo YHI_PHONE; ?></div>
            <div class="header-buttons">
                <a href="/contact" class="btn-red">Request an Appointment Today!</a>
                <a href="/shop" class="btn-shop">Shop Now</a>
            </div>
        </div>
    </div>
</header>

<!-- 

Original site header

<header id="site-header" class="main-header regular_header" role="banner" itemscope="" itemtype="http://schema.org/WPHeader">
			<div class="header-spacer" style="height: 156px;"></div>

			<div class="main-head-wrap">
			<div class="top-header">
				<p>Concussion research project here, <a href="https://youhealit.com/concussion-treatment/">check it out!</a></p>
			</div>					
								    <div id="regular-header">
				    	<div class="container-wide dm-flex">
				    							    		<div class="dm-third phone-header">
					    			<p><i class="fa fa-phone-alt"></i><a href="tel:(919) 241-5032">(919) 241-5032</a></p>
					    		</div>
					    		<div class="dm-third address-header">
					    			<p><i class="fa fa-map-marker-alt"></i><a href="https://maps.app.goo.gl/ap8WekMqJJTcSsjL8" target="_blank" rel="noreferrer noopener"> Our <strong>  Hillsborough, NC  </strong> Office </a> </p>
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
												<div id="header">
								    <div class="container-wide">
					    						<div id="secondary-navigation" class="secondary-navigation" role="navigation" itemscope="" itemtype="http://schema.org/SiteNavigationElement">
							<div class="logo-wrap">
																												<h1 id="logo" class="image-logo" itemprop="headline">
												<a href="https://youhealit.com"><img src="/wp-content/uploads/2024/09/logo-Photoroom-2.png" alt="Health Center of Hillsborough"></a>
											</h1>
																								</div>
							<div class="right-side-header">
								<div>
																												<p>Call Us: <a href="tel:(919) 241-5032">(919) 241-5032</a></p>
										<a href="/appointments/" class="btn"> Request An Appointment Today! </a>
										<a href="https://youhealit.standardprocess.com/" target="_blank" class="btn">Shop Now</a>
									  
								</div>
								
									<nav class="navigation clearfix mobile-menu-wrapper">
																					<ul id="menu-main-menu" class="menu clearfix toggle-menu"><li id="menu-item-43" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-home current-menu-item page_item page-item-42 current_page_item menu-item-43"><a href="https://youhealit.com/">Home</a></li>
<li id="menu-item-36" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-36 toggle-menu-item-parent"><a href="https://youhealit.com/about/">About</a>
<ul class="sub-menu toggle-submenu">
	<li id="menu-item-79" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-79"><a href="https://youhealit.com/events-special-offers/">Events, Special Offers</a></li>
	<li id="menu-item-83" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-83 toggle-menu-item-parent"><a href="https://youhealit.com/products/">Products</a>
	<ul class="sub-menu toggle-submenu">
		<li id="menu-item-250" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-250"><a href="https://youhealit.com/supplements/">Supplements</a></li>
	</ul>
<span class="toggle-caret"><i class="fa fa-plus"></i></span></li>
	<li id="menu-item-96" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-96"><a href="https://youhealit.com/videos/">Videos</a></li>
</ul>
<span class="toggle-caret"><i class="fa fa-plus"></i></span></li>
<li id="menu-item-74" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-74"><a href="https://youhealit.com/meet-our-team/">Meet Our Team</a></li>
<li id="menu-item-35" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-35 toggle-menu-item-parent"><a href="https://youhealit.com/new-patients/">New Patients</a>
<ul class="sub-menu toggle-submenu">
	<li id="menu-item-32" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-32"><a href="https://youhealit.com/appointments/">Appointments</a></li>
	<li id="menu-item-165" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-165"><a href="https://youhealit.com/new-patient-forms/">New Patient Forms</a></li>
	<li id="menu-item-173" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-173"><a href="https://youhealit.com/prices/">Prices</a></li>
</ul>
<span class="toggle-caret"><i class="fa fa-plus"></i></span></li>
<li id="menu-item-34" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-34 toggle-menu-item-parent"><a href="https://youhealit.com/services/">Services</a>
<ul class="sub-menu toggle-submenu wda-long-menu">
	<li id="menu-item-110" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-110"><a href="https://youhealit.com/acupuncture/">Acupuncture</a></li>
	<li id="menu-item-117" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-117"><a href="https://youhealit.com/nutritional-counseling/">Nutritional Counseling</a></li>
	<li id="menu-item-116" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-116"><a href="https://youhealit.com/yoga-and-qigong/">Yoga and QiGong</a></li>
	<li id="menu-item-121" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-121"><a href="https://youhealit.com/chiropractic-care/">Chiropractic Care</a></li>
	<li id="menu-item-197" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-197"><a href="https://youhealit.com/cranium/">Cranium</a></li>
	<li id="menu-item-126" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-126"><a href="https://youhealit.com/foot-care/">Foot Care</a></li>
	<li id="menu-item-131" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-131"><a href="https://youhealit.com/hypnotherapy/">Hypnotherapy</a></li>
	<li id="menu-item-135" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-135"><a href="https://youhealit.com/cold-laser/">Cold Laser</a></li>
	<li id="menu-item-139" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-139"><a href="https://youhealit.com/soul-integration/">Soul Integration</a></li>
	<li id="menu-item-144" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-144"><a href="https://youhealit.com/massage-therapy/">Massage Therapy</a></li>
	<li id="menu-item-149" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-149"><a href="https://youhealit.com/microbiome-test-kit/">Microbiome Test Kit</a></li>
	<li id="menu-item-230" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-230"><a href="https://youhealit.com/concussion-treatment/">Concussion Treatment</a></li>
</ul>
<span class="toggle-caret"><i class="fa fa-plus"></i></span></li>
<li id="menu-item-172" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children menu-item-172 toggle-menu-item-parent"><a href="https://youhealit.com/testimonials/">Testimonials</a>
<ul class="sub-menu toggle-submenu moveLeft">
	<li id="menu-item-175" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-175"><a href="https://youhealit.com/video-testimonials/">Video Testimonials</a></li>
</ul>
<span class="toggle-caret"><i class="fa fa-plus"></i></span></li>
<li id="menu-item-33" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-33"><a href="https://youhealit.com/contact/">Contact</a></li>
</ul>																			</nav>
																<a href="#" id="pull" class="toggle-mobile-menu"></a>
							</div>
						<div id="mobile-menu-overlay"></div></div>         
					</div><!--.container-->
				</div>
			</div>
		</header>
 -->
