<?php
/**
 * Jobs v3.8.10.82 managed navigation repair.
 *
 * Older Jobs demo refreshes could leave repeated menu rows in the managed
 * source menu. Polylang then cloned those repeats into each language menu.
 * Keep the repair child-owned: the parent theme is not changed.
 */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_jobs_v82_menu_object' ) ) {
    function wpbb_jobs_v82_menu_object( $args ) {
        if ( is_object( $args ) && ! empty( $args->menu ) ) {
            $menu = wp_get_nav_menu_object( $args->menu );
            if ( $menu ) return $menu;
        }
        $location = is_object( $args ) && ! empty( $args->theme_location ) ? (string) $args->theme_location : '';
        if ( $location ) {
            $locations = get_nav_menu_locations();
            if ( ! empty( $locations[ $location ] ) ) {
                $menu = wp_get_nav_menu_object( (int) $locations[ $location ] );
                if ( $menu ) return $menu;
            }
        }
        return false;
    }
}

if ( ! function_exists( 'wpbb_jobs_v82_is_managed_jobs_menu' ) ) {
    function wpbb_jobs_v82_is_managed_jobs_menu( $menu ) {
        if ( ! $menu instanceof WP_Term ) return false;
        $managed = (string) get_term_meta( $menu->term_id, '_wp_theme_demo_managed', true );
        $profile = sanitize_key( (string) get_term_meta( $menu->term_id, '_wp_theme_demo_profile', true ) );
        if ( '1' === $managed && 'jobs' === $profile ) return true;
        // Backward-compatible fallback for older managed menus whose term meta
        // was incomplete but retained the stable Jobs/TalentBridge names.
        $name = strtolower( (string) $menu->name );
        return false !== strpos( $name, 'jobs' ) || false !== strpos( $name, 'talentbridge' );
    }
}

if ( ! function_exists( 'wpbb_jobs_v82_item_signature' ) ) {
    function wpbb_jobs_v82_item_signature( $item ) {
        if ( ! is_object( $item ) ) return '';
        $type   = sanitize_key( (string) ( $item->type ?? '' ) );
        $object = sanitize_key( (string) ( $item->object ?? '' ) );
        $object_id = absint( $item->object_id ?? 0 );
        $parent = absint( $item->menu_item_parent ?? 0 );
        if ( $object_id ) return $parent . '|' . $type . '|' . $object . '|' . $object_id;
        $url = isset( $item->url ) ? untrailingslashit( strtolower( (string) $item->url ) ) : '';
        $title = sanitize_title( wp_strip_all_tags( (string) ( $item->title ?? '' ) ) );
        return $parent . '|' . $type . '|' . $url . '|' . $title;
    }
}

/** Never render repeated items from a managed Jobs menu, even before DB cleanup. */
if ( ! function_exists( 'wpbb_jobs_v82_dedupe_rendered_menu' ) ) {
    function wpbb_jobs_v82_dedupe_rendered_menu( $items, $args ) {
        $location = is_object( $args ) ? (string) ( $args->theme_location ?? '' ) : '';
        if ( ! in_array( $location, array( 'wp-header-menu', 'wp-header-top-menu', 'wp-footer-menu' ), true ) ) return $items;
        $menu = wpbb_jobs_v82_menu_object( $args );
        if ( $menu && ! wpbb_jobs_v82_is_managed_jobs_menu( $menu ) ) return $items;
        if ( 'wp-bbtheme-child-jobs' !== get_stylesheet() && 'jobs' !== sanitize_key( (string) get_option( 'wp_theme_active_demo_profile', '' ) ) ) return $items;

        $seen = array();
        $clean = array();
        foreach ( (array) $items as $item ) {
            $signature = wpbb_jobs_v82_item_signature( $item );
            if ( $signature && isset( $seen[ $signature ] ) ) continue;
            if ( $signature ) $seen[ $signature ] = true;
            $clean[] = $item;
        }
        return $clean;
    }
}
add_filter( 'wp_nav_menu_objects', 'wpbb_jobs_v82_dedupe_rendered_menu', 999, 2 );

if ( ! function_exists( 'wpbb_jobs_v82_delete_duplicate_menu_rows' ) ) {
    function wpbb_jobs_v82_delete_duplicate_menu_rows( $menu_id ) {
        $items = wp_get_nav_menu_items( (int) $menu_id, array( 'post_status' => 'any' ) );
        if ( ! $items ) return 0;
        $seen = array();
        $removed = 0;
        foreach ( $items as $item ) {
            $signature = wpbb_jobs_v82_item_signature( $item );
            if ( ! $signature ) continue;
            if ( isset( $seen[ $signature ] ) ) {
                wp_delete_post( (int) $item->ID, true );
                $removed++;
                continue;
            }
            $seen[ $signature ] = (int) $item->ID;
        }
        if ( $removed ) clean_term_cache( (int) $menu_id, 'nav_menu' );
        return $removed;
    }
}

/**
 * One-time DB repair: rebuild the clean source menu, then regenerate each
 * language-specific Polylang menu from that source and remove any residual
 * duplicate rows. Runs only on a normal lightweight admin GET request.
 */
if ( ! function_exists( 'wpbb_jobs_v82_repair_managed_menus_once' ) ) {
    function wpbb_jobs_v82_repair_managed_menus_once() {
        if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
        if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return;
        $method = strtoupper( (string) ( $_SERVER['REQUEST_METHOD'] ?? 'GET' ) );
        if ( 'GET' !== $method ) return;
        $done_key = 'wpbb_jobs_v82_managed_menu_repair';
        if ( '3.8.10.86' === (string) get_option( $done_key ) ) return;

        $profile = function_exists( 'wpbb_child_v71_demo_profile' ) ? wpbb_child_v71_demo_profile() : ( function_exists( 'wp_theme_get_demo_profile' ) ? wp_theme_get_demo_profile() : array( 'id' => 'jobs', 'name' => 'Jobs' ) );
        if ( 'jobs' !== sanitize_key( (string) ( $profile['id'] ?? '' ) ) ) return;
        $front = absint( get_option( 'page_on_front' ) );
        if ( ! $front || ! get_post_meta( $front, '_wp_theme_demo_managed', true ) ) return;

        if ( function_exists( 'wp_theme_create_demo_menus' ) ) {
            wp_theme_create_demo_menus( $front, $profile );
        }
        if ( function_exists( 'wp_theme_create_demo_polylang_menus' ) ) {
            wp_theme_create_demo_polylang_menus( $profile );
        }

        $menus = get_terms( array( 'taxonomy' => 'nav_menu', 'hide_empty' => false ) );
        if ( ! is_wp_error( $menus ) ) {
            foreach ( $menus as $menu ) {
                if ( wpbb_jobs_v82_is_managed_jobs_menu( $menu ) ) {
                    wpbb_jobs_v82_delete_duplicate_menu_rows( (int) $menu->term_id );
                }
            }
        }
        update_option( $done_key, '3.8.10.86', false );
    }
}
add_action( 'admin_init', 'wpbb_jobs_v82_repair_managed_menus_once', 35 );
