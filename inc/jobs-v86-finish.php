<?php
/**
 * Jobs v3.8.10.86 finishing layer.
 *
 * Final visual brand pass, Jobs logo/favicon assets, newsletter compatibility,
 * and hCaptcha protection for public Jobs forms using WP BBuilder settings.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v86_hcaptcha_config() {
    if ( ! function_exists( 'wpbb_get_option' ) ) {
        return array( 'enabled' => false, 'site_key' => '', 'secret_key' => '' );
    }

    $enabled = (bool) wpbb_get_option( 'hcaptcha_enabled', 0 );
    $site_key = $enabled ? trim( (string) wpbb_get_option( 'hcaptcha_site_key', '' ) ) : '';
    $secret_key = $enabled ? trim( (string) wpbb_get_option( 'hcaptcha_secret_key', '' ) ) : '';

    return array(
        'enabled'    => $enabled && '' !== $site_key && '' !== $secret_key,
        'site_key'   => $site_key,
        'secret_key' => $secret_key,
    );
}

function wpbb_jobs_v86_public_hcaptcha_html( $context = 'public-form' ) {
    $config = wpbb_jobs_v86_hcaptcha_config();
    if ( empty( $config['enabled'] ) ) return '';

    // 3.8.10.99: all public Jobs forms render the BBuilder hCaptcha inline.
    // This avoids blank modal hosts when another plugin loads the shared explicit API first.
    $mode = 'inline';
    return '<div class="wpbb-jobs-public-captcha" data-wpbb-jobs-captcha-shell data-context="' . esc_attr( $context ) . '" data-mode="' . esc_attr( $mode ) . '">'
        . '<div class="wpbb-jobs-public-captcha__host" data-wpbb-jobs-hcaptcha data-sitekey="' . esc_attr( $config['site_key'] ) . '" data-size="normal"></div>'
        . '<input type="hidden" name="wpbb_jobs_hcaptcha_response" value="">'
        . '</div>';
}

function wpbb_jobs_v86_verify_public_hcaptcha() {
    $config = wpbb_jobs_v86_hcaptcha_config();
    if ( empty( $config['enabled'] ) ) return true;

    $token = isset( $_POST['wpbb_jobs_hcaptcha_response'] )
        ? sanitize_text_field( wp_unslash( $_POST['wpbb_jobs_hcaptcha_response'] ) )
        : '';

    if ( '' === $token ) {
        return new WP_Error( 'wpbb_jobs_hcaptcha_missing', __( 'Please complete the hCaptcha verification.', 'wp-bbtheme-child' ) );
    }

    if ( function_exists( 'wpbb_verify_hcaptcha_token' ) ) {
        return wpbb_verify_hcaptcha_token( $token, ! empty( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' );
    }

    $body = array(
        'secret'   => $config['secret_key'],
        'response' => $token,
        'sitekey'  => $config['site_key'],
    );
    if ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $body['remoteip'] = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
    }

    $response = wp_remote_post( 'https://api.hcaptcha.com/siteverify', array(
        'timeout' => 12,
        'body'    => $body,
    ) );
    if ( is_wp_error( $response ) ) {
        return new WP_Error( 'wpbb_jobs_hcaptcha_unavailable', __( 'Verification is temporarily unavailable. Please try again.', 'wp-bbtheme-child' ) );
    }

    $decoded = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $decoded ) || empty( $decoded['success'] ) ) {
        return new WP_Error( 'wpbb_jobs_hcaptcha_failed', __( 'hCaptcha verification failed. Please try again.', 'wp-bbtheme-child' ) );
    }

    return true;
}

/**
 * The newsletter plugin can substitute official test credentials on localhost.
 * This project intentionally keeps WP BBuilder as the single captcha source,
 * so use the saved BBuilder credentials whenever a complete pair is present.
 */
function wpbb_jobs_v86_newsletter_hcaptcha_test_mode( $is_local, $host = '' ) {
    $config = wpbb_jobs_v86_hcaptcha_config();
    if ( empty( $config['enabled'] ) ) return $is_local;

    // A migrated database can still contain a localhost home/site URL while the
    // request is already arriving on the real demo domain. In that case the
    // newsletter plugin would otherwise swap in hCaptcha's public test keys.
    // Preserve its normal localhost behaviour when the browser really is local.
    $request_host = isset( $_SERVER['HTTP_HOST'] ) ? strtolower( sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ) : '';
    $request_host = preg_replace( '/:\d+$/', '', trim( $request_host, '[] ' ) );
    $request_is_local = 'localhost' === $request_host
        || ( '' !== $request_host && strlen( $request_host ) > 10 && '.localhost' === substr( $request_host, -10 ) )
        || '::1' === $request_host
        || 0 === strpos( $request_host, '127.' );

    return $request_is_local ? $is_local : false;
}
add_filter( 'wp_newslatter_campaigns_hcaptcha_local_test_mode', 'wpbb_jobs_v86_newsletter_hcaptcha_test_mode', 20, 2 );


/** Require the same BBuilder hCaptcha on the public Jobs password login form. */
function wpbb_jobs_v86_verify_public_login_hcaptcha( $user, $username = '', $password = '' ) {
    if ( is_user_logged_in() || empty( $_POST['wpbb_jobs_public_login'] ) ) return $user;
    if ( '1' !== sanitize_text_field( wp_unslash( $_POST['wpbb_jobs_public_login'] ) ) ) return $user;

    $captcha = wpbb_jobs_v86_verify_public_hcaptcha();
    if ( is_wp_error( $captcha ) ) return $captcha;
    return $user;
}
add_filter( 'authenticate', 'wpbb_jobs_v86_verify_public_login_hcaptcha', 5, 3 );

/** Localise the newsletter plugin's small public-facing form/captcha strings
 * through the Jobs language dictionary so the footer remains multilingual. */
function wpbb_jobs_v86_newsletter_gettext( $translation, $text, $domain ) {
    if ( 'wp-newslatter-campaigns' !== $domain || ! function_exists( 'wpbb_jobs_translate_text' ) ) return $translation;
    $frontend = array(
        'Verify you are human',
        'Complete the hCaptcha check to subscribe.',
        'Close verification',
        'I agree to receive email updates and can unsubscribe at any time.',
        'Subscribe',
        'Enter your email',
        'Please complete the captcha check.',
    );
    if ( ! in_array( $text, $frontend, true ) ) return $translation;
    $translated = wpbb_jobs_translate_text( $text );
    return $translated !== $text ? $translated : $translation;
}
add_filter( 'gettext', 'wpbb_jobs_v86_newsletter_gettext', 30, 3 );


function wpbb_jobs_v86_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v86',
        get_stylesheet_directory_uri() . '/assets/jobs-v86.css',
        array( 'wpbb-jobs-v85' ),
        $version
    );

    $config = wpbb_jobs_v86_hcaptcha_config();
    $deps = array();
    if ( ! empty( $config['enabled'] ) ) {
        if ( ! wp_script_is( 'hcaptcha-api', 'registered' ) ) {
            wp_register_script(
                'hcaptcha-api',
                'https://js.hcaptcha.com/1/api.js?render=explicit&recaptchacompat=off',
                array(),
                null,
                true
            );
        }
        wp_enqueue_script( 'hcaptcha-api' );
        $deps[] = 'hcaptcha-api';
    }

    wp_enqueue_script(
        'wpbb-jobs-v86',
        get_stylesheet_directory_uri() . '/assets/jobs-v86.js',
        $deps,
        $version,
        true
    );
    wp_localize_script( 'wpbb-jobs-v86', 'wpbbJobsV86', array(
        'brandLogo'   => get_stylesheet_directory_uri() . '/assets/brand/jobs-logo.svg',
        'captcha'     => ! empty( $config['enabled'] ),
        'captchaText' => __( 'Complete the hCaptcha check to continue.', 'wp-bbtheme-child' ),
        'captchaTitle'=> __( 'Verify you are human', 'wp-bbtheme-child' ),
        'captchaEyebrow' => __( 'Security check', 'wp-bbtheme-child' ),
        'captchaClose'=> __( 'Close verification', 'wp-bbtheme-child' ),
        'captchaError'=> __( 'Please complete the hCaptcha verification.', 'wp-bbtheme-child' ),
        'captchaWait' => __( 'Loading verification…', 'wp-bbtheme-child' ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v86_enqueue_assets', 175 );

function wpbb_jobs_v86_favicon_meta() {
    $base = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/brand/';
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $base . 'favicon-32.png' ) . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url( $base . 'favicon-192.png' ) . '">' . "\n";
    echo '<link rel="apple-touch-icon" sizes="180x180" href="' . esc_url( $base . 'apple-touch-icon.png' ) . '">' . "\n";
    echo '<meta name="theme-color" content="#078f6a">' . "\n";
}
add_action( 'wp_head', 'wpbb_jobs_v86_favicon_meta', 99 );
