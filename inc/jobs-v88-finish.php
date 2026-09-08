<?php
/**
 * Jobs v3.8.10.88 live design correction layer.
 *
 * - Widens the public canvas and integrates the hero visual.
 * - Adds a dedicated Post a job header CTA + account action.
 * - Cleans newsletter/public-form notices after parent form enhancement.
 * - Hardens the newsletter hCaptcha production-host detection for proxies.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v88_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v88',
        get_stylesheet_directory_uri() . '/assets/jobs-v88.css',
        array( 'wpbb-jobs-v87' ),
        $version
    );

    wp_enqueue_script(
        'wpbb-jobs-v88',
        get_stylesheet_directory_uri() . '/assets/jobs-v88.js',
        array( 'wpbb-jobs-v87' ),
        $version,
        true
    );

    $account_url = wpbb_jobs_page_url( 'login-register' );
    if ( is_user_logged_in() ) {
        if ( function_exists( 'wpbb_jobs_is_employer' ) && wpbb_jobs_is_employer() ) {
            $account_url = wpbb_jobs_page_url( 'employer-dashboard' );
        } elseif ( function_exists( 'wpbb_jobs_is_candidate' ) && wpbb_jobs_is_candidate() ) {
            $account_url = wpbb_jobs_page_url( 'candidate-dashboard' );
        } else {
            $account_url = admin_url( 'profile.php' );
        }
    }

    $captcha = function_exists( 'wpbb_jobs_v86_hcaptcha_config' ) ? wpbb_jobs_v86_hcaptcha_config() : array();
    wp_localize_script( 'wpbb-jobs-v88', 'wpbbJobsV88', array(
        'postUrl'         => wpbb_jobs_page_url( 'post-a-job' ),
        'postLabel'       => __( 'Post a job', 'wp-bbtheme-child' ),
        'accountUrl'      => $account_url,
        'accountLabel'    => __( 'Account', 'wp-bbtheme-child' ),
        'hcaptchaSiteKey' => ! empty( $captcha['enabled'] ) ? (string) $captcha['site_key'] : '',
    ) );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v88_enqueue_assets', 205 );

/**
 * Production requests can reach PHP through a reverse proxy whose HTTP_HOST is
 * still the local upstream name. Honour forwarded/original host headers before
 * allowing the Newsletter Campaigns plugin to switch to hCaptcha test keys.
 */
function wpbb_jobs_v88_newsletter_hcaptcha_test_mode( $is_local, $host = '' ) {
    $config = function_exists( 'wpbb_jobs_v86_hcaptcha_config' ) ? wpbb_jobs_v86_hcaptcha_config() : array();
    if ( empty( $config['enabled'] ) ) return $is_local;

    $candidates = array();
    foreach ( array( 'HTTP_X_FORWARDED_HOST', 'HTTP_X_ORIGINAL_HOST', 'HTTP_HOST', 'SERVER_NAME' ) as $key ) {
        if ( empty( $_SERVER[ $key ] ) ) continue;
        $raw = sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
        foreach ( explode( ',', $raw ) as $candidate ) {
            $candidate = strtolower( trim( $candidate ) );
            $candidate = preg_replace( '/:\d+$/', '', trim( $candidate, '[] ' ) );
            if ( '' !== $candidate ) $candidates[] = $candidate;
        }
    }

    foreach ( array_unique( $candidates ) as $candidate ) {
        $local = 'localhost' === $candidate
            || ( strlen( $candidate ) > 10 && '.localhost' === substr( $candidate, -10 ) )
            || '::1' === $candidate
            || 0 === strpos( $candidate, '127.' );
        if ( ! $local ) return false;
    }

    return $is_local;
}
add_filter( 'wp_newslatter_campaigns_hcaptcha_local_test_mode', 'wpbb_jobs_v88_newsletter_hcaptcha_test_mode', 100, 2 );
