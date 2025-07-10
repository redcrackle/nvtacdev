<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Twenty_Sixteen
 * @since Twenty Sixteen 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-43541138-7"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-43541138-7');
</script>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Archivo:600&display=swap" rel="stylesheet">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php if ( is_singular() && pings_open( get_queried_object() ) ) : ?>
        <link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>">
        <?php endif; ?>
        <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
        <div class="site-inner">
                <a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'twentysixteen' ); ?></a>

                <header id="masthead" class="site-header" role="banner">
                        <div class="site-header-main">
                                <div class="site-branding">
                                        <?php twentysixteen_the_custom_logo(); ?>

                                        <?php if ( is_front_page() && is_home() ) : ?>
                                                <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
                                        <?php else : ?>
                                                <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
                                                <?php
                                        endif;

                                        $description = get_bloginfo( 'description', 'display' );
                                        if ( $description || is_customize_preview() ) :
                                                ?>
                                                <p class="site-description"><?php echo $description; ?></p>
                                        <?php endif; ?>
                                </div><!-- .site-branding -->

                                <?php if ( has_nav_menu( 'primary' ) || has_nav_menu( 'social' ) ) : ?>
                                        <button id="menu-toggle" class="menu-toggle"><?php _e( 'Menu', 'twentysixteen' ); ?></button>

                                        <div id="site-header-menu" class="site-header-menu">
                                                <?php if ( has_nav_menu( 'primary' ) ) : ?>
                                                        <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'twentysixteen' ); ?>">
                                                                <?php
                                                                        wp_nav_menu(
                                                                                array(
                                                                                        'theme_location' => 'primary',
                                                                                        'menu_class' => 'primary-menu',
                                                                                )
                                                                        );
 ?>
                                                        </nav><!-- .main-navigation -->
                                                <?php endif; ?>

                                                <?php if ( has_nav_menu( 'social' ) ) : ?>
                                                        <nav id="social-navigation" class="social-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Social Links Menu', 'twentysixteen' ); ?>">
                                                                <?php
                                                                        wp_nav_menu(
                                                                                array(
                                                                                        'theme_location' => 'social',
                                                                                        'menu_class'  => 'social-links-menu',
                                                                                        'depth'       => 1,
                                                                                        'link_before' => '<span class="screen-reader-text">',
                                                                                        'link_after'  => '</span>',
                                                                                )
                                                                        );
                                                                ?>
                                                        </nav><!-- .social-navigation -->
                                                <?php endif; ?>
                                        </div><!-- .site-header-menu -->
                                <?php endif; ?>
                        </div><!-- .site-header-main -->



<a href="<?php echo site_url(); ?>" class="noExitNotifier"><img class="nvtaclogo" src="https://nvtac.org/wp-content/themes/twentysixteensubtheme2/images/nvtac-logo.svg"  alt="National Veterans' Technical Assistance Center (NVTAC) Logo" title="National Veterans' Technical Assistance Center (NVTAC) Logo" width="300px"></a>
<div class="confeed"><a href="/contact-us">Contact</a> | <a href="/feedback">Feedback</a> | <a href="/join-the-mailing-list">Mailing List</a></div>
<?php

if ( is_active_sidebar( 'custom-header-widget' ) ) : ?>
    <div id="header-widget-area" class="chw-widget-area widget-area" role="complementary">
    <?php dynamic_sidebar( 'custom-header-widget' ); ?>
    </div>

<?php endif; ?>

<?php nivo_slider( "employers-landing" ); ?>

<div id="content" class="site-content">
