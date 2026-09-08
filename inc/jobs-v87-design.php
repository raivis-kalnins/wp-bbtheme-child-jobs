<?php
/**
 * Jobs v3.8.10.87 visual/system polish.
 *
 * - Adds the High-volume Hiring Demo to the employer submenu/mega menu.
 * - Loads the final preview-aligned light/dark design layer.
 * - Keeps the managed Homepage 2 page discoverable without adding another
 *   crowded top-level navigation item.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v87_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v87',
        get_stylesheet_directory_uri() . '/assets/jobs-v87.css',
        array( 'wpbb-jobs-v86' ),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-v87',
        get_stylesheet_directory_uri() . '/assets/jobs-v87.js',
        array( 'wpbb-jobs-v86' ),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v87_enqueue_assets', 190 );

/** Add Homepage 2 as a real submenu destination under For Employers. */
function wpbb_jobs_v87_navigation_items( $items, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return $items;

    foreach ( $items as $item ) {
        if ( 'home-2' === sanitize_key( (string) ( $item['key'] ?? '' ) ) ) {
            return $items;
        }
    }

    $items[] = array(
        'key'        => 'home-2',
        'parent_key' => 'services',
        'title'      => __( 'High-volume Hiring Demo', 'wp-bbtheme-child' ),
        'slug'       => 'home-2',
        'locations'  => array( 'header', 'footer' ),
    );
    return $items;
}
add_filter( 'wp_theme_demo_navigation_items', 'wpbb_jobs_v87_navigation_items', 50, 2 );

/** Also expose the page inside the dynamically rendered employer mega menu. */
function wpbb_jobs_v87_mega_menu( $definitions, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' || empty( $definitions['services']['columns'] ) ) return $definitions;

    $high_volume_link = array(
        __( 'High-volume Hiring Demo', 'wp-bbtheme-child' ),
        __( 'A focused landing page for repeated roles, fast qualification and a clearer applicant pipeline.', 'wp-bbtheme-child' ),
        wpbb_jobs_page_url( 'home-2' ),
    );

    foreach ( $definitions['services']['columns'] as &$column ) {
        if ( ( $column['title'] ?? '' ) === __( 'For employers', 'wp-bbtheme-child' ) ) {
            $exists = false;
            foreach ( (array) ( $column['links'] ?? array() ) as $link ) {
                if ( ! empty( $link[2] ) && untrailingslashit( $link[2] ) === untrailingslashit( wpbb_jobs_page_url( 'home-2' ) ) ) {
                    $exists = true;
                    break;
                }
            }
            if ( ! $exists ) $column['links'][] = $high_volume_link;
            break;
        }
    }
    unset( $column );

    return $definitions;
}
add_filter( 'wp_theme_demo_mega_menu_definitions', 'wpbb_jobs_v87_mega_menu', 50, 2 );

/**
 * Mark the homepage so the last design layer can make exact homepage-only
 * choices without changing employer/candidate portal pages.
 */
function wpbb_jobs_v87_body_class( $classes ) {
    if ( is_front_page() ) $classes[] = 'wpbb-jobs-marketplace-home';
    if ( is_page( 'home-2' ) ) $classes[] = 'wpbb-jobs-high-volume-page';
    return $classes;
}
add_filter( 'body_class', 'wpbb_jobs_v87_body_class', 40 );

/**
 * Run the managed Jobs page refresh once after this release is installed.
 * This makes Homepage 2 appear even when 3.8.10.85/86 was installed without
 * re-running the Setup & Demo action. The existing creator only overwrites
 * Jobs-managed pages and skips same-slug pages owned by the site editor.
 */
function wpbb_jobs_v87_ensure_managed_pages() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( '3.8.10.87' === get_option( 'wpbb_jobs_managed_pages_version' ) ) return;
    if ( function_exists( 'wpbb_jobs_create_portal_pages' ) ) {
        wpbb_jobs_create_portal_pages();
        update_option( 'wpbb_jobs_managed_pages_version', '3.8.10.87', false );
    }
}
add_action( 'admin_init', 'wpbb_jobs_v87_ensure_managed_pages', 35 );
