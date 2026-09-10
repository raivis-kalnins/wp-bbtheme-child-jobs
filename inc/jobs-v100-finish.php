<?php
/** Jobs 3.8.11.01 final overlap/cookie/mobile fix. */
defined( 'ABSPATH' ) || exit;
function wpbb_jobs_v100_enqueue_assets(){
    $v = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'wpbb-jobs-v100', get_stylesheet_directory_uri() . '/assets/jobs-v100.css', array( 'wpbb-jobs-v99' ), $v );
    wp_enqueue_script( 'wpbb-jobs-v100', get_stylesheet_directory_uri() . '/assets/jobs-v100.js', array(), $v, true );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v100_enqueue_assets', 1900 );
add_filter( 'wp_theme_cookie_consent_enabled', '__return_false', PHP_INT_MAX );
add_filter( 'wp_theme_cookie_banner_enabled', '__return_false', PHP_INT_MAX );
add_filter( 'wp_theme_cookie_settings_enabled', '__return_false', PHP_INT_MAX );
