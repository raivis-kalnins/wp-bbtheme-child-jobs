<?php
/** Jobs 3.8.10.99 final grid, captcha, contact/map and mobile finishing layer. */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v99_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    $css = get_stylesheet_directory() . '/assets/jobs-v99.css';
    $js  = get_stylesheet_directory() . '/assets/jobs-v99.js';
    if ( is_readable( $css ) ) {
        wp_enqueue_style( 'wpbb-jobs-v99', get_stylesheet_directory_uri() . '/assets/jobs-v99.css', array( 'wpbb-jobs-v98' ), $version );
    }
    if ( is_readable( $js ) ) {
        wp_enqueue_script( 'wpbb-jobs-v99', get_stylesheet_directory_uri() . '/assets/jobs-v99.js', array(), $version, true );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v99_enqueue_assets', 1400 );

/**
 * Keep contact useful on upgraded managed installs. Some previous managed page
 * revisions lost the map block; append a lightweight location map only when no
 * map is already present.
 */
function wpbb_jobs_v99_contact_map( $content ) {
    if ( is_admin() || ! is_singular( 'page' ) ) return $content;
    $id = get_queried_object_id();
    if ( ! $id ) return $content;
    $slug = (string) get_post_field( 'post_name', $id );
    if ( false === strpos( $slug, 'contact' ) ) return $content;
    if ( false !== strpos( $content, 'wpbb-jobs-v99-map' ) || false !== strpos( $content, 'output=embed' ) ) return $content;

    $title = function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( 'Recruitment team location' ) : __( 'Recruitment team location', 'wp-bbtheme-child' );
    $map = '<section class="wpbb-jobs-v99-map"><div class="wpbb-jobs-v99-map__inner">'
        . '<iframe loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="' . esc_attr( $title ) . '" src="https://www.google.com/maps?q=London%20United%20Kingdom&z=12&output=embed"></iframe>'
        . '</div></section>';
    return $content . $map;
}
add_filter( 'the_content', 'wpbb_jobs_v99_contact_map', 160 );

/** Add explicit page classes for final layout selectors without touching data. */
function wpbb_jobs_v99_body_classes( $classes ) {
    if ( is_singular( 'wpbb_job' ) ) $classes[] = 'wpbb-jobs-v99-single-job';
    if ( is_singular( 'post' ) ) $classes[] = 'wpbb-jobs-v99-single-post';
    if ( is_singular( 'page' ) ) {
        $slug = (string) get_post_field( 'post_name', get_queried_object_id() );
        if ( preg_match( '/(^|[-_])(privacy|terms|cookie|cookies)([-_]|$)/i', $slug ) ) $classes[] = 'wpbb-jobs-v99-legal';
        if ( false !== strpos( $slug, 'contact' ) ) $classes[] = 'wpbb-jobs-v99-contact';
        if ( false !== strpos( $slug, 'about' ) ) $classes[] = 'wpbb-jobs-v99-about';
        if ( false !== strpos( $slug, 'create-resume' ) || false !== strpos( $slug, 'resume' ) ) $classes[] = 'wpbb-jobs-v99-resume';
    }
    return $classes;
}
add_filter( 'body_class', 'wpbb_jobs_v99_body_classes', 120 );

/** Refresh only Starter Setup-managed Jobs content once on this release. */
function wpbb_jobs_v99_refresh_managed_demo() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    $key = 'wpbb_jobs_v99_managed_refresh';
    if ( '3.8.10.99' === (string) get_option( $key ) ) return;

    if ( function_exists( 'wpbb_child_v62_rebuild_demo_pages' ) ) {
        wpbb_child_v62_rebuild_demo_pages( true );
    }
    if ( function_exists( 'wpbb_jobs_v82_repair_managed_menus' ) ) {
        wpbb_jobs_v82_repair_managed_menus();
    }
    update_option( $key, '3.8.10.99', false );
}
add_action( 'admin_init', 'wpbb_jobs_v99_refresh_managed_demo', 900 );
add_action( 'wp_theme_after_demo_import', 'wpbb_jobs_v99_refresh_managed_demo', 900 );
