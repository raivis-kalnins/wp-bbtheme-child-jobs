<?php
/** Jobs 3.8.11.03 mobile/legal/contact/single-item correction; colour and imagery remain unchanged. */
defined( 'ABSPATH' ) || exit;
if ( ! function_exists( 'wpbb_jobs_v101_body_classes' ) ) {
    function wpbb_jobs_v101_body_classes( $classes ) {
        if ( is_singular( 'page' ) ) {
            $id = get_queried_object_id();
            $slug = (string) get_post_field( 'post_name', $id );
            $title = (string) get_the_title( $id );
            if ( preg_match( '/privacy|terms|condition|cookie|cookies|legal/i', $slug . ' ' . $title ) ) $classes[] = 'wpbb-legal-page';
            if ( false !== stripos( $slug . ' ' . $title, 'contact' ) ) $classes[] = 'wpbb-contact-page';
        }
        if ( is_singular( 'event' ) ) $classes[] = 'wpbb-v101-single-event';
        return array_values( array_unique( $classes ) );
    }
    add_filter( 'body_class', 'wpbb_jobs_v101_body_classes', 999 );
}
if ( ! function_exists( 'wpbb_jobs_v101_enqueue_assets' ) ) {
    function wpbb_jobs_v101_enqueue_assets() {
        $v = wp_get_theme()->get( 'Version' );
        wp_enqueue_style( 'wpbb-jobs-v101', get_stylesheet_directory_uri() . '/assets/jobs-v101.css', array( 'wpbb-jobs-v100' ), $v );
        wp_enqueue_script( 'wpbb-jobs-v101', get_stylesheet_directory_uri() . '/assets/jobs-v101.js', array(), $v, true );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v101_enqueue_assets', 2500 );
}
