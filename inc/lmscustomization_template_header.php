<?php

/**
 * Declaring the template header function
 */

function lmscustomization_template_header()
{


    ob_start();
?>

    <?php
    /**
     * Header Template
     *
     * Here we setup all logic and XHTML that is required for the header section of all screens.
     *
     * @package WooFramework
     * @subpackage Template
     */

    global $woo_options, $woocommerce;
    unku_before_header_generate();
    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <!-- Facebook Comment Moderation App -->
        <meta property="fb:app_id" content="417151605080123" />

        <title><?php woo_title(''); ?></title>
        <?php woo_meta(); ?>
        <link rel="pingback" href="<?php echo esc_url(get_bloginfo('pingback_url')); ?>" />
        <?php
        wp_head();
        woo_head();
        ?>

        <!--[if IE 7]>
<link href="<?php echo dirname(get_bloginfo('stylesheet_url')); ?>/css/ie7.css" />
<![endif]-->
    </head>

    <body <?php body_class(); ?>>
        <?php woo_top(); ?>

        <div id="wrapper">

            <?php woo_header_before(); ?>

            <div id="header-container">
                <div id="header" class="header">
                    <div class="logo"><?php woo_header_inside(); ?></div>

                    <?php
                    $act_url = get_bloginfo('url') . '/my-account/';
                    $act_link_txt = 'My Account';
                    $headlink_classes = '';

                    if (is_user_logged_in()) {
                        $headlink_classes = ' logged-in';
                    }
                    ?>
                    <div class="head-link <?php print $headlink_classes; ?>">
                        <a class="account" href="<?php print $act_url; ?>"><?php print $act_link_txt; ?></a>
                        <?php if (is_user_logged_in()) : ?> <a class="logout" href="<?php print wp_logout_url(); ?>">Log out</a> <?php endif; ?>
                    </div>

                    <hgroup>
                        <span class="nav-toggle"><a href="#navigation">&#9776; <span><?php _e('Navigation', 'woothemes'); ?></span></a></span>
                        <?php /* 
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
*/ ?>
                    </hgroup>

                    <?php /*if ( is_woocommerce_activated() && isset( $woo_options['woocommerce_header_cart_link'] ) && 'true' == $woo_options['woocommerce_header_cart_link'] ) { ?>
        	<ul class="nav cart fr">
        		<li><a class="cart-contents" href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'woothemes' ); ?>"><?php echo $woocommerce->cart->get_cart_total(); ?><span class="items"><?php echo sprintf( _n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'woothemes' ), $woocommerce->cart->cart_contents_count );?></span></a></li>
       		</ul>
        <?php }*/ ?>

                    <?php woo_nav_before(); ?>

                    <nav id="navigation" class="col-full" role="navigation">

                        <?php
                        if (function_exists('has_nav_menu') && has_nav_menu('primary-menu')) {
                            wp_nav_menu(array('depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'link_before' => '<span>', 'link_after' => '</span>', 'theme_location' => 'primary-menu'));
                        } else {
                        ?>
                            <ul id="main-nav" class="nav fl">
                                <?php if (is_page()) $highlight = 'page_item';
                                else $highlight = 'page_item current_page_item'; ?>
                                <li class="<?php echo $highlight; ?>"><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'woothemes'); ?></a></li>
                                <?php wp_list_pages('sort_column=menu_order&depth=6&title_li=&exclude='); ?>
                            </ul>
                        <?php } ?>

                    </nav>

                    <?php woo_nav_after(); ?>

                </div><!-- /#header -->
            </div><!--  /#header-container -->
            <?php woo_content_before(); ?>
        </div>
    <?php
    return ob_get_clean();
}
