<?php
/** Jobs 3.8.10.97 final hero/mobile navigation layer. */
defined( 'ABSPATH' ) || exit;
if ( ! function_exists( 'wpbb_jobs_v97_enqueue' ) ) {
    function wpbb_jobs_v97_enqueue() {
        $ver = wp_get_theme()->get( 'Version' );
        $css = get_stylesheet_directory() . '/assets/jobs-v97.css';
        $js  = get_stylesheet_directory() . '/assets/jobs-v97.js';
        if ( is_readable( $css ) ) wp_enqueue_style( 'wpbb-jobs-v97', get_stylesheet_directory_uri() . '/assets/jobs-v97.css', array(), $ver );
        if ( is_readable( $js ) ) wp_enqueue_script( 'wpbb-jobs-v97', get_stylesheet_directory_uri() . '/assets/jobs-v97.js', array(), $ver, true );
    }
    add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v97_enqueue', 420 );
}
