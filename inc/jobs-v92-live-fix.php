<?php
/**
 * Jobs 3.8.10.92 live-site corrections.
 *
 * - Puts Homepage 2 under Home instead of For Employers.
 * - Keeps the high-volume page discoverable in every managed language menu.
 * - Removes the duplicate high-volume entry from the employer mega menu.
 * - Refreshes managed menus once after upgrade without touching user menus.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v92_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v92',
        get_stylesheet_directory_uri() . '/assets/jobs-v92.css',
        array( 'wpbb-jobs-v91' ),
        $version
    );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v92_enqueue_assets', 340 );

/** Move the managed high-volume page beneath Home in source menu definitions. */
function wpbb_jobs_v92_navigation_items( $items, $profile ) {
    if ( 'jobs' !== sanitize_key( (string) ( $profile['id'] ?? '' ) ) ) return $items;

    $found = false;
    foreach ( $items as &$item ) {
        if ( 'home-2' !== sanitize_key( (string) ( $item['key'] ?? '' ) ) ) continue;
        $item['parent_key'] = 'home';
        $item['title'] = sprintf( 'Home 2 – %s', __( 'High-volume hiring', 'wp-bbtheme-child' ) );
        $item['locations'] = array( 'header', 'footer' );
        $found = true;
        break;
    }
    unset( $item );

    if ( ! $found ) {
        $items[] = array(
            'key'        => 'home-2',
            'parent_key' => 'home',
            'title'      => sprintf( 'Home 2 – %s', __( 'High-volume hiring', 'wp-bbtheme-child' ) ),
            'slug'       => 'home-2',
            'locations'  => array( 'header', 'footer' ),
        );
    }
    return $items;
}
add_filter( 'wp_theme_demo_navigation_items', 'wpbb_jobs_v92_navigation_items', 95, 2 );

/** Do not duplicate Homepage 2 inside the For Employers mega menu. */
function wpbb_jobs_v92_mega_menu( $definitions, $profile ) {
    if ( 'jobs' !== sanitize_key( (string) ( $profile['id'] ?? '' ) ) ) return $definitions;
    if ( empty( $definitions['services']['columns'] ) ) return $definitions;

    $home2 = untrailingslashit( wpbb_jobs_page_url( 'home-2' ) );
    foreach ( $definitions['services']['columns'] as &$column ) {
        if ( empty( $column['links'] ) || ! is_array( $column['links'] ) ) continue;
        $column['links'] = array_values( array_filter( $column['links'], static function( $link ) use ( $home2 ) {
            $url = isset( $link[2] ) ? untrailingslashit( (string) $link[2] ) : '';
            return ! $url || $url !== $home2;
        } ) );
    }
    unset( $column );
    return $definitions;
}
add_filter( 'wp_theme_demo_mega_menu_definitions', 'wpbb_jobs_v92_mega_menu', 95, 2 );

/**
 * Runtime safety for sites whose persisted menu has not yet been rebuilt.
 * Reparent the translated Home 2 menu item to the translated front page.
 */
function wpbb_jobs_v92_reparent_rendered_home2( $items, $args ) {
    if ( is_admin() || ! is_array( $items ) ) return $items;
    $location = is_object( $args ) ? (string) ( $args->theme_location ?? '' ) : '';
    if ( 'wp-header-menu' !== $location ) return $items;
    if ( 'wp-bbtheme-child-jobs' !== get_stylesheet() ) return $items;

    $front_source = absint( get_option( 'page_on_front' ) );
    $home2_source = get_page_by_path( 'home-2', OBJECT, 'page' );
    if ( ! $front_source || ! $home2_source instanceof WP_Post ) return $items;

    $front_target = $front_source;
    $home2_target = (int) $home2_source->ID;
    if ( function_exists( 'pll_get_post' ) && function_exists( 'wpbb_jobs_current_language' ) ) {
        $lang = wpbb_jobs_current_language();
        $translated_front = absint( pll_get_post( $front_source, $lang ) );
        $translated_home2 = absint( pll_get_post( $home2_source->ID, $lang ) );
        if ( $translated_front ) $front_target = $translated_front;
        if ( $translated_home2 ) $home2_target = $translated_home2;
    }

    $home_item = null;
    $home2_item = null;
    foreach ( $items as $item ) {
        if ( absint( $item->object_id ?? 0 ) === $front_target ) $home_item = $item;
        if ( absint( $item->object_id ?? 0 ) === $home2_target ) $home2_item = $item;
    }
    if ( ! $home_item || ! $home2_item ) return $items;

    $home2_item->menu_item_parent = (string) $home_item->ID;
    $home2_item->title = sprintf( 'Home 2 – %s', __( 'High-volume hiring', 'wp-bbtheme-child' ) );
    $home_item->classes = array_values( array_unique( array_merge( (array) $home_item->classes, array( 'menu-item-has-children' ) ) ) );
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'wpbb_jobs_v92_reparent_rendered_home2', 1100, 2 );

/** Rebuild only Starter-Setup managed Jobs menus/translations once. */
function wpbb_jobs_v92_refresh_managed_navigation() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return;
    if ( 'GET' !== strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) ) ) return;

    $key = 'wpbb_jobs_v92_home2_navigation';
    if ( '3.8.10.92' === (string) get_option( $key ) ) return;

    $profile = function_exists( 'wp_theme_get_demo_profile' ) ? (array) wp_theme_get_demo_profile() : array( 'id' => 'jobs' );
    if ( 'jobs' !== sanitize_key( (string) ( $profile['id'] ?? '' ) ) ) return;

    if ( function_exists( 'wpbb_jobs_create_portal_pages' ) ) wpbb_jobs_create_portal_pages( $profile );

    $front = absint( get_option( 'page_on_front' ) );
    if ( $front && get_post_meta( $front, '_wp_theme_demo_managed', true ) ) {
        if ( function_exists( 'wp_theme_create_demo_menus' ) ) wp_theme_create_demo_menus( $front, $profile );
        if ( function_exists( 'wp_theme_create_demo_polylang_menus' ) ) wp_theme_create_demo_polylang_menus( $profile );
    }

    update_option( $key, '3.8.10.92', false );
}
add_action( 'admin_init', 'wpbb_jobs_v92_refresh_managed_navigation', 360 );
