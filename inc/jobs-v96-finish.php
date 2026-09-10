<?php
/**
 * Jobs v3.8.10.96 layout, contact-form and mobile/hCaptcha finishing pass.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v96_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v96',
        get_stylesheet_directory_uri() . '/assets/jobs-v96.css',
        array( 'wpbb-jobs-v95' ),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-v96',
        get_stylesheet_directory_uri() . '/assets/jobs-v96.js',
        array( 'wpbb-jobs-v86' ),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v96_enqueue_assets', 390 );

/**
 * Normalise the managed Jobs contact form at render time. This leaves the
 * authored page/map intact while repairing old saved select options and
 * ensures the form keeps using BBuilder's own hCaptcha configuration.
 */
function wpbb_jobs_v96_contact_form_block( $parsed_block ) {
    if ( empty( $parsed_block['blockName'] ) || 'wpbb/dynamic-form' !== $parsed_block['blockName'] ) return $parsed_block;
    $attrs = isset( $parsed_block['attrs'] ) && is_array( $parsed_block['attrs'] ) ? $parsed_block['attrs'] : array();
    $class = (string) ( $attrs['className'] ?? '' );
    if ( false === strpos( $class, 'wp-theme-contact-form' ) && false === strpos( $class, 'wpbb-jobs-contact-form' ) ) return $parsed_block;

    $fields = array();
    if ( ! empty( $attrs['fields'] ) && is_array( $attrs['fields'] ) ) {
        $fields = $attrs['fields'];
    } elseif ( ! empty( $attrs['fieldsJson'] ) ) {
        $decoded = json_decode( (string) $attrs['fieldsJson'], true );
        if ( is_array( $decoded ) ) $fields = $decoded;
    }
    if ( ! $fields ) return $parsed_block;

    $topics = implode( "\n", array(
        __( 'Candidate support', 'wp-bbtheme-child' ),
        __( 'Employer support', 'wp-bbtheme-child' ),
        __( 'Account support', 'wp-bbtheme-child' ),
        __( 'General enquiry', 'wp-bbtheme-child' ),
    ) );
    foreach ( $fields as &$field ) {
        if ( ! is_array( $field ) ) continue;
        if ( 'select' === sanitize_key( $field['type'] ?? '' ) && 'topic' === sanitize_key( $field['name'] ?? '' ) ) {
            $field['placeholder'] = __( 'Choose a topic', 'wp-bbtheme-child' );
            $field['options'] = $topics;
            $field['width'] = 6;
            $field['breakpoint'] = 'md';
        }
    }
    unset( $field );

    $attrs['fields'] = $fields;
    $attrs['fieldsJson'] = wp_json_encode( $fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    $attrs['className'] = trim( $class . ' wpbb-jobs-contact-form' );
    $parsed_block['attrs'] = $attrs;
    return $parsed_block;
}
add_filter( 'render_block_data', 'wpbb_jobs_v96_contact_form_block', 20 );

/** Add page-context classes that make wide layouts deterministic. */
function wpbb_jobs_v96_body_classes( $classes ) {
    if ( is_singular( 'page' ) ) {
        $classes[] = 'wpbb-jobs-wide-page';
        $post_id = get_queried_object_id();
        $slug = $post_id ? (string) get_post_field( 'post_name', $post_id ) : '';
        $title = $post_id ? (string) get_post_field( 'post_title', $post_id ) : '';
        if ( preg_match( '/privacy|terms|condition|cookie|legal/i', $slug . ' ' . $title ) ) {
            $classes[] = 'wpbb-jobs-legal-wide';
        }
        if ( false !== strpos( $slug, 'contact' ) || false !== stripos( $title, 'contact' ) ) {
            $classes[] = 'wpbb-jobs-contact-page';
        }
    }
    return $classes;
}
add_filter( 'body_class', 'wpbb_jobs_v96_body_classes', 60 );
