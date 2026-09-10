<?php
/**
 * Jobs v3.8.10.89 final marketplace finish.
 *
 * - Refreshes the managed homepage so the hero uses BBuilder's fluid-container
 *   background image attributes (WP BBuilder 5.6.9+).
 * - Protects CV/profile and application uploads with the configured BBuilder
 *   hCaptcha challenge.
 * - Cleans old managed demo revisions and visual/PWA leftovers.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v89_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v89',
        get_stylesheet_directory_uri() . '/assets/jobs-v89.css',
        array( 'wpbb-jobs-v88' ),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-v89',
        get_stylesheet_directory_uri() . '/assets/jobs-v89.js',
        array( 'wpbb-jobs-v88' ),
        $version,
        true
    );
    wp_localize_script( 'wpbb-jobs-v89', 'wpbbJobsV89', array(
        'icon32'  => get_stylesheet_directory_uri() . '/assets/brand/favicon-32.png',
        'icon192' => get_stylesheet_directory_uri() . '/assets/brand/favicon-192.png',
    ) );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v89_enqueue_assets', 220 );

/** Keep BBuilder's default control tokens in the Jobs palette on this child. */
function wpbb_jobs_v89_bbuilder_default_palette( $value, $key = '' ) {
    $map = array(
        'default_button_bg'       => '#078f6a',
        'default_button_text'     => '#ffffff',
        'default_label_color'     => '#17324d',
        'default_input_border_color' => '#d9e6e3',
        'cookie_button_bg'        => '#078f6a',
    );
    return isset( $map[ $key ] ) ? $map[ $key ] : $value;
}
add_filter( 'wpbb_option', 'wpbb_jobs_v89_bbuilder_default_palette', 40, 2 );

/**
 * Refresh only theme-managed Jobs homepage/translations once after upgrade.
 * User-created/unmanaged pages are never overwritten.
 */
function wpbb_jobs_v89_refresh_managed_home() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    $key = 'wpbb_jobs_v89_home_background_refresh';
    if ( '3.8.10.89' === (string) get_option( $key ) ) return;

    $front = absint( get_option( 'page_on_front' ) );
    $profile = function_exists( 'wp_theme_get_demo_profile' ) ? (array) wp_theme_get_demo_profile() : array( 'id' => 'jobs' );
    if ( $front && 'jobs' === ( $profile['id'] ?? '' ) && get_post_meta( $front, '_wp_theme_demo_managed', true ) && function_exists( 'wpbb_jobs_home_content_v76' ) ) {
        wp_update_post( array( 'ID' => $front, 'post_content' => wpbb_jobs_home_content_v76( $profile ) ) );
        update_post_meta( $front, '_wpbb_child_bbuilder_version', '3.8.10.89' );
        clean_post_cache( $front );
    }

    // Rebuild managed Jobs portal pages and translated copies with the same
    // canonical block structure. Existing unmanaged same-slug pages are skipped.
    if ( function_exists( 'wpbb_jobs_create_portal_pages' ) ) wpbb_jobs_create_portal_pages( $profile );
    if ( function_exists( 'wpbb_jobs_v89_sync_translations' ) ) wpbb_jobs_v89_sync_translations( $profile, $front );

    // Remove stale revisions only for theme-managed demo pages. This clears old
    // Lorem-ipsum/placeholder demo snapshots without touching user content.
    $managed = get_posts( array(
        'post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids',
        'meta_key' => '_wp_theme_demo_managed', 'meta_value' => '1',
    ) );
    foreach ( $managed as $page_id ) {
        foreach ( wp_get_post_revisions( $page_id, array( 'posts_per_page' => -1 ) ) as $revision ) {
            wp_delete_post_revision( $revision->ID );
        }
    }

    update_option( $key, '3.8.10.89', false );
}
add_action( 'admin_init', 'wpbb_jobs_v89_refresh_managed_home', 210 );

function wpbb_jobs_v89_sync_translations( $profile, $front ) {
    if ( ! $front || ! function_exists( 'pll_get_post' ) || ! function_exists( 'wp_theme_demo_polylang_languages' ) ) return;
    foreach ( array_keys( (array) wp_theme_demo_polylang_languages() ) as $lang ) {
        $lang = sanitize_key( $lang );
        if ( ! $lang || 'en' === $lang ) continue;
        $target = absint( pll_get_post( $front, $lang ) );
        if ( ! $target || ! get_post_meta( $target, '_wp_theme_demo_managed', true ) ) continue;
        $previous = function_exists( 'wpbb_jobs_set_language_override' ) ? wpbb_jobs_set_language_override( $lang ) : '';
        wp_update_post( array( 'ID' => $target, 'post_content' => wpbb_jobs_home_content_v76( $profile ) ) );
        update_post_meta( $target, '_wpbb_child_bbuilder_version', '3.8.10.89' );
        clean_post_cache( $target );
        if ( function_exists( 'wpbb_jobs_restore_language_override' ) ) wpbb_jobs_restore_language_override( $previous );
    }
}
