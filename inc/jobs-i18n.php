<?php
/** Lightweight frontend/demo translations for the integrated Jobs sector. */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_current_language() {
    global $wpbb_jobs_language_override;
    if ( ! empty( $wpbb_jobs_language_override ) ) return sanitize_key( (string) $wpbb_jobs_language_override );
    $lang = '';
    if ( function_exists( 'pll_current_language' ) ) {
        $lang = sanitize_key( (string) pll_current_language( 'slug' ) );
    }
    if ( ! $lang ) {
        $locale = strtolower( (string) determine_locale() );
        $lang = sanitize_key( substr( $locale, 0, 2 ) );
    }
    if ( 'no' === $lang ) $lang = 'nb';
    return $lang ?: 'en';
}

function wpbb_jobs_translation_dictionary( $lang = '' ) {
    static $all = null;
    if ( null === $all ) {
        $path = __DIR__ . '/demo-translations.json';
        $json = is_readable( $path ) ? file_get_contents( $path ) : '';
        $all = $json ? json_decode( $json, true ) : array();
        if ( ! is_array( $all ) ) $all = array();
    }
    $lang = sanitize_key( $lang ?: wpbb_jobs_current_language() );
    return isset( $all[ $lang ] ) && is_array( $all[ $lang ] ) ? $all[ $lang ] : array();
}

function wpbb_jobs_translate_text( $text, $lang = '' ) {
    $text = (string) $text;
    $lang = sanitize_key( $lang ?: wpbb_jobs_current_language() );
    if ( ! $text || 'en' === $lang ) return $text;
    $dictionary = wpbb_jobs_translation_dictionary( $lang );
    if ( isset( $dictionary[ $text ] ) ) return (string) $dictionary[ $text ];

    // Reuse the parent Starter Setup translator as a safe fallback. The parent
    // dictionary automatically merges this child theme's sector dictionary, so
    // longer managed demo strings can still localise from known phrases instead
    // of falling back to a fully English UI.
    if ( function_exists( 'wp_theme_demo_translate_starter_string' ) ) {
        $fallback = (string) wp_theme_demo_translate_starter_string( $text, $lang );
        if ( $fallback !== $text ) return $fallback;
    }
    return $text;
}

add_filter( 'gettext', static function( $translation, $text, $domain ) {
    if ( 'wp-bbtheme-child' !== $domain ) return $translation;
    $translated = wpbb_jobs_translate_text( $text );
    return $translated !== $text ? $translated : $translation;
}, 20, 3 );

add_filter( 'ngettext', static function( $translation, $single, $plural, $number, $domain ) {
    if ( 'wp-bbtheme-child' !== $domain ) return $translation;
    $source = 1 === (int) $number ? $single : $plural;
    $translated = wpbb_jobs_translate_text( $source );
    return $translated !== $source ? $translated : $translation;
}, 20, 5 );



/** Temporarily render managed demo content in a specific Polylang language. */
function wpbb_jobs_set_language_override( $lang = '' ) {
    global $wpbb_jobs_language_override;
    $previous = isset( $wpbb_jobs_language_override ) ? (string) $wpbb_jobs_language_override : '';
    $wpbb_jobs_language_override = sanitize_key( (string) $lang );
    return $previous;
}

function wpbb_jobs_restore_language_override( $previous = '' ) {
    global $wpbb_jobs_language_override;
    $wpbb_jobs_language_override = sanitize_key( (string) $previous );
}

/** Translate demo-owned titles/excerpts at render time without duplicating CPT data. */
function wpbb_jobs_localized_demo_text( $text, $post_id = 0 ) {
    $text = (string) $text;
    if ( '' === $text ) return $text;
    if ( $post_id && ! get_post_meta( $post_id, '_wpbb_jobs_sample', true ) && 'jobs' !== get_post_meta( $post_id, '_wp_theme_demo_profile', true ) ) return $text;
    return wpbb_jobs_translate_text( $text );
}


function wpbb_jobs_localized_demo_html( $html, $post_id = 0 ) {
    $html = (string) $html;
    if ( '' === $html || 'en' === wpbb_jobs_current_language() ) return $html;
    if ( $post_id && ! get_post_meta( $post_id, '_wpbb_jobs_sample', true ) && 'jobs' !== get_post_meta( $post_id, '_wp_theme_demo_profile', true ) ) return $html;
    $dictionary = wpbb_jobs_translation_dictionary();
    if ( ! $dictionary ) return $html;
    uksort( $dictionary, static function( $a, $b ) { return strlen( $b ) <=> strlen( $a ); } );
    return strtr( $html, $dictionary );
}

function wpbb_jobs_localized_post_title( $post ) {
    $post = get_post( $post );
    if ( ! $post instanceof WP_Post ) return '';
    return wpbb_jobs_localized_demo_text( $post->post_title, $post->ID );
}

function wpbb_jobs_localized_post_excerpt( $post ) {
    $post = get_post( $post );
    if ( ! $post instanceof WP_Post ) return '';
    $translated = wpbb_jobs_localized_demo_text( $post->post_excerpt, $post->ID );
    if ( $translated !== $post->post_excerpt ) return $translated;
    if ( get_post_meta( $post->ID, '_wpbb_jobs_sample', true ) && 'wpbb_company' === $post->post_type ) {
        return __( 'Active employer with open roles and a clear recruitment profile.', 'wp-bbtheme-child' );
    }
    if ( 'post' === $post->post_type && 'jobs' === get_post_meta( $post->ID, '_wp_theme_demo_profile', true ) ) {
        return __( 'Practical guidance for candidates and hiring teams.', 'wp-bbtheme-child' );
    }
    if ( get_post_meta( $post->ID, '_wpbb_jobs_sample', true ) && 'wpbb_job' === $post->post_type ) {
        $company = function_exists( 'wpbb_jobs_company_for_job' ) ? wpbb_jobs_company_for_job( $post->ID ) : null;
        $category = function_exists( 'wpbb_jobs_term_name' ) ? wpbb_jobs_term_name( $post->ID, 'wpbb_job_category' ) : __( 'role', 'wp-bbtheme-child' );
        $company_name = $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' );
        return sprintf( __( 'Join %1$s in a practical %2$s role with clear responsibilities and room to grow.', 'wp-bbtheme-child' ), $company_name, function_exists( 'mb_strtolower' ) ? mb_strtolower( $category ) : strtolower( $category ) );
    }
    return $translated;
}

/** Translate copied menu labels so legacy Polylang menus do not keep English text. */
add_filter( 'nav_menu_item_title', static function( $title, $item = null ) {
    if ( is_admin() || 'en' === wpbb_jobs_current_language() ) return $title;
    $translated = wpbb_jobs_translate_text( wp_strip_all_tags( (string) $title ) );
    return $translated !== $title ? $translated : $title;
}, 30, 2 );

/**
 * v3.8.10.80: rebuild Starter-Setup managed translations directly in the
 * target language. This avoids the old parent-copy path losing theme-asset
 * image URLs and guarantees identical BBuilder structure in all languages.
 */
function wpbb_jobs_v80_sync_managed_translations() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'wp_theme_demo_polylang_languages' ) ) return;
    $done_key = 'wpbb_jobs_v80_translation_parity_' . sanitize_key( get_stylesheet() );
    if ( '3.8.10.80' === (string) get_option( $done_key ) ) return;

    $profile = function_exists( 'wpbb_child_v71_demo_profile' ) ? wpbb_child_v71_demo_profile() : array( 'id' => 'jobs' );
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return;
    $front = absint( get_option( 'page_on_front' ) );
    $sources = array();
    if ( $front ) $sources['home'] = $front;
    foreach ( array( 'jobs','hiring-companies','find-candidates','candidate-dashboard','employer-dashboard','post-a-job','create-resume','login-register','salary-guide' ) as $slug ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $page instanceof WP_Post ) $sources[ $slug ] = (int) $page->ID;
    }

    // Rebuild only the managed Jobs menus before cloning language menus.
    // This removes stale/duplicated items left by older Jobs demo imports while
    // leaving user-created menus untouched.
    if ( $front && get_post_meta( $front, '_wp_theme_demo_managed', true ) && function_exists( 'wp_theme_create_demo_menus' ) ) {
        wp_theme_create_demo_menus( $front, $profile );
    }

    foreach ( array_keys( (array) wp_theme_demo_polylang_languages() ) as $lang ) {
        $lang = sanitize_key( $lang );
        if ( ! $lang || 'en' === $lang ) continue;
        $previous = wpbb_jobs_set_language_override( $lang );
        foreach ( $sources as $slug => $source_id ) {
            $target_id = absint( pll_get_post( $source_id, $lang ) );
            if ( ! $target_id ) continue;
            $managed_source = absint( get_post_meta( $target_id, '_wp_theme_demo_translation_source', true ) );
            if ( $managed_source && $managed_source !== (int) $source_id ) continue;
            if ( ! $managed_source && ! get_post_meta( $target_id, '_wp_theme_demo_managed', true ) ) continue;

            if ( 'home' === $slug && function_exists( 'wpbb_jobs_home_content_v76' ) ) {
                $content = wpbb_jobs_home_content_v76( $profile );
            } elseif ( function_exists( 'wpbb_jobs_portal_page_content' ) ) {
                $content = wpbb_jobs_portal_page_content( $slug );
            } else {
                $content = '';
            }
            if ( '' === trim( (string) $content ) ) continue;
            $source_title = (string) get_post_field( 'post_title', $source_id, 'raw' );
            $localized_title = $source_title ? wpbb_jobs_translate_text( $source_title, $lang ) : '';
            wp_update_post( array( 'ID' => $target_id, 'post_content' => $content, 'post_title' => $localized_title ?: get_the_title( $target_id ) ) );
            update_post_meta( $target_id, '_wp_theme_demo_translation_source', $source_id );
            update_post_meta( $target_id, '_wp_theme_demo_managed', '1' );
            update_post_meta( $target_id, '_wpbb_child_bbuilder_version', '3.8.10.80' );
            clean_post_cache( $target_id );
        }
        wpbb_jobs_restore_language_override( $previous );
    }
    if ( function_exists( 'wpbb_jobs_localize_managed_translation_links' ) ) wpbb_jobs_localize_managed_translation_links( array_values( $sources ) );
    if ( function_exists( 'wp_theme_create_demo_polylang_menus' ) ) wp_theme_create_demo_polylang_menus( $profile );
    update_option( $done_key, '3.8.10.80', false );
}
add_action( 'admin_init', 'wpbb_jobs_v80_sync_managed_translations', 95 );

/**
 * Point managed translated demo pages at their translated Jobs pages rather
 * than leaving absolute links to the English source pages.
 */
function wpbb_jobs_localize_managed_translation_links( $source_ids ) {
    if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'wp_theme_demo_polylang_languages' ) ) return;
    $source_ids = array_values( array_unique( array_filter( array_map( 'absint', (array) $source_ids ) ) ) );
    if ( ! $source_ids ) return;

    $link_sources = array();
    foreach ( array( 'jobs','hiring-companies','salary-guide','create-resume','post-a-job','candidate-dashboard','employer-dashboard','find-candidates','login-register','about','contact' ) as $slug ) {
        $page = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $page instanceof WP_Post ) $link_sources[] = (int) $page->ID;
    }
    foreach ( array( get_option( 'page_on_front' ), get_option( 'page_for_posts' ) ) as $id ) {
        if ( $id ) $link_sources[] = absint( $id );
    }
    $link_sources = array_values( array_unique( array_filter( $link_sources ) ) );

    foreach ( array_keys( (array) wp_theme_demo_polylang_languages() ) as $lang ) {
        $lang = sanitize_key( $lang );
        if ( ! $lang || 'en' === $lang ) continue;
        $replace = array();
        foreach ( $link_sources as $source_page_id ) {
            $translated_page_id = absint( pll_get_post( $source_page_id, $lang ) );
            if ( ! $translated_page_id ) continue;
            $source_url = get_permalink( $source_page_id );
            $target_url = get_permalink( $translated_page_id );
            if ( $source_url && $target_url && $source_url !== $target_url ) $replace[ $source_url ] = $target_url;
        }
        if ( ! $replace ) continue;

        foreach ( $source_ids as $source_id ) {
            $translated_id = absint( pll_get_post( $source_id, $lang ) );
            if ( ! $translated_id ) continue;
            if ( (int) get_post_meta( $translated_id, '_wp_theme_demo_translation_source', true ) !== (int) $source_id ) continue;
            $content = (string) get_post_field( 'post_content', $translated_id, 'raw' );
            $localized = strtr( $content, $replace );
            if ( $localized !== $content ) {
                wp_update_post( array( 'ID' => $translated_id, 'post_content' => $localized ) );
                clean_post_cache( $translated_id );
            }
        }
    }
}

/** Localise managed Jobs mega-menu bodies without modifying custom mega menus. */
add_filter( 'the_content', static function( $content ) {
    if ( is_admin() || 'en' === wpbb_jobs_current_language() ) return $content;
    $post = get_post();
    if ( ! $post instanceof WP_Post || 'megamenu' !== $post->post_type ) return $content;
    if ( 'jobs' !== get_post_meta( $post->ID, '_wp_theme_demo_profile', true ) ) return $content;
    return wpbb_jobs_localized_demo_html( $content, $post->ID );
}, 19 );
