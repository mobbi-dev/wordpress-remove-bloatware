<?php
/**
 * Plugin Name: WordPress Remove Bloatware
 * Description: Removes unnecessary WordPress bloat and optimizes head output.
 * Version: 1.0.0
 * Author: mobbi
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Defines which bloat removal features are enabled
 */
function wordpress_bloat_remover_settings() {
    return array(
        'remove_emojis'         => true,
        'hide_rest_api_links'   => true,
        'remove_jquery_migrate' => true,
        'remove_embed_script'   => true,
        'disable_pingbacks'     => true,
        'disable_xmlrpc'        => true,
    );
}

/**
 * Remove WordPress version number from the <head>
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * Disable comments support
 */
add_action( 'admin_init', function() {
    // Disable support for comments and trackbacks in post types
    foreach ( get_post_types() as $post_type ) {
        if ( post_type_supports( $post_type, 'comments' ) ) {
            remove_post_type_support( $post_type, 'comments' );
            remove_post_type_support( $post_type, 'trackbacks' );
        }
    }
});

// Close comments on the front-end
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

// Hide existing comments
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

/**
 * Clean up the WordPress admin dashboard by removing default widgets
 */
add_action( 'wp_dashboard_setup', function() {
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    remove_meta_box( 'dashboard_secondary', 'dashboard', 'side' );
});

/**
 * Removes default WordPress emojis.
 */
function remove_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
}

/**
 * Removes REST API and oEmbed links from the head.
 */
function remove_rest_api_links() {
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
}

/**
 * Removes various default links from wp_head.
 */
function remove_misc_head_links() {
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'wp_generator');
}

/**
 * Removes jQuery Migrate script.
 */
function remove_jquery_migrate( $scripts ) {
    if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
        $script = $scripts->registered['jquery'];
        if ( $script->deps ) {
            $script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
        }
    }
}

/**
 * Deregisters the wp-embed script
 */
function remove_embed_script() {
    wp_deregister_script( 'wp-embed' );
}

/**
 * Disables pingback links and pingback functionality
 */
function disable_pingbacks() {
    // Remove <link rel="pingback"> from the head
    remove_action('wp_head', 'rsd_link');
    add_filter('xmlrpc_methods', function( $methods ) {
        unset( $methods['pingback.ping'] );
        return $methods;
    });
    // Remove X-Pingback HTTP header
    add_filter('wp_headers', function( $headers ) {
        unset( $headers['X-Pingback'] );
        return $headers;
    });
}

/**
 * Disables XML-RPC functionality entirely
 */
function disable_xmlrpc() {
    add_filter('xmlrpc_enabled', '__return_false');
}

/**
 * Executes enabled optimizations from settings
 */
function execute_wordpress_bloat_remover_settings() {
    $settings = wordpress_bloat_remover_settings();

    if ( $settings['remove_emojis'] ) {
        add_action( 'init', 'remove_emojis' );
    }

    if ( $settings['hide_rest_api_links'] ) {
        add_action( 'init', 'remove_rest_api_links' );
        add_action( 'init', 'remove_misc_head_links' );
    }

    if ( $settings['remove_jquery_migrate'] ) {
        add_action( 'wp_default_scripts', 'remove_jquery_migrate' );
    }

    if ( $settings['remove_embed_script'] ) {
        add_action( 'wp_footer', 'remove_embed_script' );
    }

    if ( $settings['disable_pingbacks'] ) {
        add_action( 'init', 'disable_pingbacks' );
    }

    if ( $settings['disable_xmlrpc'] ) {
        add_action( 'init', 'disable_xmlrpc' );
    }
}
add_action( 'init', 'execute_wordpress_bloat_remover_settings' );
