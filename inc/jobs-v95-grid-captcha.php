<?php
/**
 * Jobs v3.8.10.95 finishing pass.
 *
 * - Full-width four-column Jobs results grid.
 * - Refreshes only the theme-managed Jobs portal page (and Polylang copies).
 * - Keeps all Jobs submission forms on the WP BBuilder hCaptcha configuration.
 * - Adds a resilient front-end render pass for BBuilder dynamic-form hCaptcha.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v95_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v95',
        get_stylesheet_directory_uri() . '/assets/jobs-v95.css',
        array( 'wpbb-jobs-v93' ),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-v95',
        get_stylesheet_directory_uri() . '/assets/jobs-v95.js',
        array(),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v95_enqueue_assets', 370 );

/**
 * Refresh the generated Jobs listing page so old saved block attributes do not
 * keep the former two-column layout after the theme update.
 */
function wpbb_jobs_v95_refresh_jobs_page() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return;

    $key = 'wpbb_jobs_v95_jobs_grid';
    if ( '3.8.10.95' === (string) get_option( $key ) ) return;
    if ( ! function_exists( 'wpbb_jobs_portal_page_content' ) ) return;

    $source = get_page_by_path( 'jobs', OBJECT, 'page' );
    if ( ! $source instanceof WP_Post ) {
        update_option( $key, '3.8.10.95', false );
        return;
    }
    if ( ! get_post_meta( $source->ID, '_wp_theme_demo_managed', true ) && ! get_post_meta( $source->ID, '_wpbb_jobs_portal_page', true ) ) {
        update_option( $key, '3.8.10.95', false );
        return;
    }

    wp_update_post( array(
        'ID'           => $source->ID,
        'post_content' => wpbb_jobs_portal_page_content( 'jobs' ),
    ) );
    update_post_meta( $source->ID, '_wpbb_child_bbuilder_version', '3.8.10.95' );

    if ( function_exists( 'pll_get_post' ) && function_exists( 'wp_theme_demo_polylang_languages' ) ) {
        foreach ( array_keys( (array) wp_theme_demo_polylang_languages() ) as $lang ) {
            $lang = sanitize_key( $lang );
            if ( ! $lang || 'en' === $lang ) continue;
            $translated_id = absint( pll_get_post( $source->ID, $lang ) );
            if ( ! $translated_id ) continue;
            if ( ! get_post_meta( $translated_id, '_wp_theme_demo_managed', true ) && ! get_post_meta( $translated_id, '_wpbb_jobs_portal_page', true ) ) continue;

            $previous = function_exists( 'wpbb_jobs_set_language_override' ) ? wpbb_jobs_set_language_override( $lang ) : '';
            wp_update_post( array(
                'ID'           => $translated_id,
                'post_content' => wpbb_jobs_portal_page_content( 'jobs' ),
            ) );
            update_post_meta( $translated_id, '_wpbb_child_bbuilder_version', '3.8.10.95' );
            if ( function_exists( 'wpbb_jobs_restore_language_override' ) ) wpbb_jobs_restore_language_override( $previous );
        }
    }

    update_option( $key, '3.8.10.95', false );
}
add_action( 'admin_init', 'wpbb_jobs_v95_refresh_jobs_page', 375 );

/**
 * Add a useful body class to Jobs portal pages, including translated copies.
 */
function wpbb_jobs_v95_body_classes( $classes ) {
    if ( ! is_singular( 'page' ) ) return $classes;
    $post_id = get_queried_object_id();
    if ( ! $post_id || ! get_post_meta( $post_id, '_wpbb_jobs_portal_page', true ) ) return $classes;

    $content = (string) get_post_field( 'post_content', $post_id, 'raw' );
    if ( false !== strpos( $content, 'wpbb-jobs-page-jobs-body' ) || ( false !== strpos( $content, 'wpbb-jobs/search' ) && false !== strpos( $content, 'wpbb-jobs/list' ) ) ) {
        $classes[] = 'wpbb-jobs-listing-page';
    }
    return $classes;
}
add_filter( 'body_class', 'wpbb_jobs_v95_body_classes', 40 );
