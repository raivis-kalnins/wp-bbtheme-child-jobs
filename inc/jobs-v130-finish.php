<?php
/**
 * Jobs 3.8.11.30 application CTA and PWA manifest repair.
 *
 * Keeps the job-detail application action in the form, makes the guest path
 * explicit, and guarantees that the parent `?wpbb-pwa=manifest` endpoint
 * returns a valid web-app manifest instead of an HTML page.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v130_enqueue_assets() {
    $css = get_stylesheet_directory() . '/assets/jobs-v130.css';
    if ( ! is_readable( $css ) ) return;

    wp_enqueue_style(
        'wpbb-jobs-v130',
        get_stylesheet_directory_uri() . '/assets/jobs-v130.css',
        array( 'wpbb-jobs-v101' ),
        (string) filemtime( $css )
    );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v130_enqueue_assets', PHP_INT_MAX );

/**
 * The parent theme advertises `/?wpbb-pwa=manifest`. On some cloned installs
 * that request falls through to the front page, so Chromium tries to parse
 * HTML as JSON and reports "Manifest: Line: 1, column: 1, Syntax error".
 */
function wpbb_jobs_v130_manifest_response() {
    if ( ! isset( $_GET['wpbb-pwa'] ) ) return; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $request = sanitize_key( wp_unslash( $_GET['wpbb-pwa'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ( 'manifest' !== $request ) return;

    $name = wp_strip_all_tags( get_bloginfo( 'name' ) );
    if ( '' === $name ) $name = __( 'Jobs', 'wp-bbtheme-child' );

    $manifest = array(
        'id'               => home_url( '/' ),
        'name'             => $name,
        'short_name'       => function_exists( 'mb_substr' ) ? mb_substr( $name, 0, 24 ) : substr( $name, 0, 24 ),
        'description'      => wp_strip_all_tags( get_bloginfo( 'description' ) ),
        'start_url'        => home_url( '/' ),
        'scope'            => home_url( '/' ),
        'display'          => 'standalone',
        'background_color' => '#ffffff',
        'theme_color'      => '#078f6a',
        'icons'            => array(
            array(
                'src'   => get_stylesheet_directory_uri() . '/assets/brand/favicon-192.png',
                'sizes' => '192x192',
                'type'  => 'image/png',
            ),
            array(
                'src'   => get_stylesheet_directory_uri() . '/assets/brand/favicon-512.png',
                'sizes' => '512x512',
                'type'  => 'image/png',
            ),
        ),
    );

    status_header( 200 );
    header( 'Content-Type: application/manifest+json; charset=' . get_option( 'blog_charset' ) );
    header( 'Cache-Control: public, max-age=3600' );
    header( 'X-Robots-Tag: noindex, nofollow', true );
    echo wp_json_encode( $manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    exit;
}
add_action( 'template_redirect', 'wpbb_jobs_v130_manifest_response', -1000 );
