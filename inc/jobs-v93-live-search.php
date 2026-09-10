<?php
/**
 * Jobs 3.8.10.93 live UX corrections.
 *
 * - Adds AJAX-first homepage vacancy search and AJAX popular-filter chips.
 * - Seeds a larger repeat-role demo catalogue for Home 2 / high-volume hiring.
 * - Refreshes managed homepage content so new imagery and search markup appear.
 */
defined( 'ABSPATH' ) || exit;

function wpbb_jobs_v93_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-v93',
        get_stylesheet_directory_uri() . '/assets/jobs-v93.css',
        array( 'wpbb-jobs-v92' ),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-v93',
        get_stylesheet_directory_uri() . '/assets/jobs-v93.js',
        array(),
        $version,
        true
    );
    wp_localize_script( 'wpbb-jobs-v93', 'wpbbJobsV93', array(
        'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'wpbb_jobs_live_search' ),
        'jobsUrl'  => wpbb_jobs_page_url( 'jobs' ),
        'labels'   => array(
            'loading' => __( 'Finding matching jobs…', 'wp-bbtheme-child' ),
            'empty'   => __( 'No matching jobs yet. Try another role, location or filter.', 'wp-bbtheme-child' ),
            'error'   => __( 'Live search is temporarily unavailable. Use the full jobs page to continue.', 'wp-bbtheme-child' ),
            'viewAll' => __( 'View all matching jobs', 'wp-bbtheme-child' ),
            'results' => __( 'Matching jobs', 'wp-bbtheme-child' ),
        ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v93_enqueue_assets', 350 );

/** Build a safe query for the AJAX homepage finder. */
function wpbb_jobs_v93_live_query_args( $filters = array() ) {
    $filters = wp_parse_args( $filters, array(
        'job_keyword'  => '',
        'job_location' => 0,
        'job_type'     => 0,
        'job_category' => 0,
        'job_remote'   => 0,
    ) );
    $args = array(
        'post_type'      => 'wpbb_job',
        'post_status'    => 'publish',
        'posts_per_page' => 6,
        'orderby'        => array( 'date' => 'DESC' ),
        'meta_query'     => array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array( 'key' => '_wpbb_job_status', 'compare' => 'NOT EXISTS' ),
                array( 'key' => '_wpbb_job_status', 'value' => 'closed', 'compare' => '!=' ),
            ),
            array(
                'relation' => 'OR',
                array( 'key' => '_wpbb_job_deadline', 'compare' => 'NOT EXISTS' ),
                array( 'key' => '_wpbb_job_deadline', 'value' => '', 'compare' => '=' ),
                array( 'key' => '_wpbb_job_deadline', 'value' => wp_date( 'Y-m-d' ), 'compare' => '>=', 'type' => 'DATE' ),
            ),
        ),
    );
    $keyword = sanitize_text_field( (string) $filters['job_keyword'] );
    if ( $keyword ) $args['s'] = $keyword;
    if ( ! empty( $filters['job_remote'] ) ) $args['meta_query'][] = array( 'key' => '_wpbb_job_remote', 'value' => 1, 'compare' => '=' );

    $tax_query = array();
    foreach ( array(
        'wpbb_job_location' => absint( $filters['job_location'] ),
        'wpbb_job_type'     => absint( $filters['job_type'] ),
        'wpbb_job_category' => absint( $filters['job_category'] ),
    ) as $taxonomy => $term_id ) {
        if ( $term_id ) $tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => array( $term_id ) );
    }
    if ( $tax_query ) {
        if ( count( $tax_query ) > 1 ) $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }
    return $args;
}

function wpbb_jobs_v93_ajax_live_search() {
    check_ajax_referer( 'wpbb_jobs_live_search', 'nonce' );
    $filters = array(
        'job_keyword'  => sanitize_text_field( wp_unslash( $_POST['job_keyword'] ?? '' ) ),
        'job_location' => absint( $_POST['job_location'] ?? 0 ),
        'job_type'     => absint( $_POST['job_type'] ?? 0 ),
        'job_category' => absint( $_POST['job_category'] ?? 0 ),
        'job_remote'   => ! empty( $_POST['job_remote'] ) ? 1 : 0,
    );
    $query = new WP_Query( wpbb_jobs_v93_live_query_args( $filters ) );
    $results = array();
    foreach ( $query->posts as $job ) {
        $company = wpbb_jobs_company_for_job( $job->ID );
        $results[] = array(
            'title'    => function_exists( 'wpbb_jobs_localized_post_title' ) ? wpbb_jobs_localized_post_title( $job ) : get_the_title( $job ),
            'company'  => $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' ),
            'location' => wpbb_jobs_term_name( $job->ID, 'wpbb_job_location' ),
            'type'     => wpbb_jobs_term_name( $job->ID, 'wpbb_job_type' ),
            'category' => wpbb_jobs_term_name( $job->ID, 'wpbb_job_category' ),
            'salary'   => wpbb_jobs_salary_label( $job->ID ),
            'remote'   => (bool) get_post_meta( $job->ID, '_wpbb_job_remote', true ),
            'url'      => get_permalink( $job ),
        );
    }
    $query_args = array_filter( array(
        'job_keyword'  => $filters['job_keyword'],
        'job_location' => $filters['job_location'],
        'job_type'     => $filters['job_type'],
        'job_category' => $filters['job_category'],
        'job_remote'   => $filters['job_remote'],
    ), static function( $value ) { return '' !== $value && 0 !== $value; } );
    wp_send_json_success( array(
        'items'   => $results,
        'total'   => (int) $query->found_posts,
        'viewAll' => add_query_arg( $query_args, wpbb_jobs_page_url( 'jobs' ) ),
    ) );
}
add_action( 'wp_ajax_wpbb_jobs_live_search', 'wpbb_jobs_v93_ajax_live_search' );
add_action( 'wp_ajax_nopriv_wpbb_jobs_live_search', 'wpbb_jobs_v93_ajax_live_search' );

/** Add realistic repeated-role vacancies for the high-volume recruitment demo. */
function wpbb_jobs_v93_seed_repeat_roles() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return;
    $key = 'wpbb_jobs_v93_repeat_roles';
    if ( '3.8.10.93' === (string) get_option( $key ) ) return;

    wpbb_jobs_seed_default_terms();
    foreach ( array( 'Healthcare' ) as $name ) if ( ! term_exists( $name, 'wpbb_job_category' ) ) wp_insert_term( $name, 'wpbb_job_category' );
    foreach ( array( 'Leeds', 'Cardiff', 'Glasgow', 'Cambridge' ) as $name ) if ( ! term_exists( $name, 'wpbb_job_location' ) ) wp_insert_term( $name, 'wpbb_job_location' );

    $admin = get_users( array( 'role__in' => array( 'administrator' ), 'number' => 1, 'fields' => 'ID' ) );
    $author = $admin ? (int) $admin[0] : get_current_user_id();
    $companies = array(
        array( 'City Response', 'Public-safety and emergency-response teams recruiting operational roles across multiple locations.', 'London' ),
        array( 'CareFirst Services', 'Community care provider recruiting dependable frontline care teams throughout the year.', 'Manchester' ),
        array( 'Rapid Fulfilment', 'High-volume warehousing and delivery operation with regular shift-based recruitment.', 'Birmingham' ),
        array( 'Metro Retail', 'Multi-site retail employer with recurring store, service and seasonal vacancies.', 'Leeds' ),
    );
    $company_ids = array();
    foreach ( $companies as $company ) {
        $ids = get_posts( array( 'post_type' => 'wpbb_company', 'post_status' => 'any', 'title' => $company[0], 'posts_per_page' => 1, 'fields' => 'ids' ) );
        $id = $ids ? (int) $ids[0] : wp_insert_post( array(
            'post_type' => 'wpbb_company', 'post_status' => 'publish', 'post_title' => $company[0], 'post_author' => $author,
            'post_excerpt' => $company[1], 'post_content' => '<p>' . esc_html( $company[1] ) . '</p>',
        ) );
        if ( ! $id || is_wp_error( $id ) ) continue;
        update_post_meta( $id, '_wpbb_jobs_sample', 1 );
        update_post_meta( $id, '_wp_theme_demo_profile', 'jobs' );
        $term = get_term_by( 'name', $company[2], 'wpbb_job_location' );
        if ( $term ) wp_set_object_terms( $id, array( $term->term_id ), 'wpbb_job_location', false );
        $company_ids[ $company[0] ] = $id;
    }

    $jobs = array(
        array( 'Firefighter', 'City Response', 'Operations', 'Full time', 'London', 31000, 39000, false ),
        array( 'On-call Firefighter', 'City Response', 'Operations', 'Part time', 'Bristol', 26000, 34000, false ),
        array( 'Warehouse Operative', 'Rapid Fulfilment', 'Operations', 'Full time', 'Birmingham', 25500, 29500, false ),
        array( 'Warehouse Operative – Evening Shift', 'Rapid Fulfilment', 'Operations', 'Part time', 'Birmingham', 27000, 31500, false ),
        array( 'Delivery Driver', 'Rapid Fulfilment', 'Operations', 'Full time', 'Manchester', 28500, 34000, false ),
        array( 'Care Assistant', 'CareFirst Services', 'Healthcare', 'Full time', 'Manchester', 24500, 29000, false ),
        array( 'Support Worker', 'CareFirst Services', 'Healthcare', 'Part time', 'Leeds', 23500, 28000, false ),
        array( 'Customer Service Advisor', 'Metro Retail', 'Customer Success', 'Full time', 'Leeds', 25000, 30000, true ),
        array( 'Retail Assistant', 'Metro Retail', 'Sales', 'Part time', 'London', 23500, 27000, false ),
        array( 'Store Team Member', 'Metro Retail', 'Sales', 'Full time', 'Manchester', 24500, 28500, false ),
        array( 'Security Officer', 'City Response', 'Operations', 'Full time', 'Edinburgh', 27000, 32000, false ),
        array( 'Event Steward', 'City Response', 'Operations', 'Temporary', 'London', 23000, 27000, false ),
    );
    foreach ( $jobs as $index => $job ) {
        $ids = get_posts( array( 'post_type' => 'wpbb_job', 'post_status' => 'any', 'title' => $job[0], 'posts_per_page' => 1, 'fields' => 'ids' ) );
        $id = $ids ? (int) $ids[0] : wp_insert_post( array(
            'post_type' => 'wpbb_job', 'post_status' => 'publish', 'post_title' => $job[0], 'post_author' => $author,
            'post_date' => wp_date( 'Y-m-d H:i:s', strtotime( '-' . ( $index + 1 ) . ' hours' ) ),
            'post_excerpt' => sprintf( __( 'A clear, repeatable %1$s vacancy for %2$s with shift, location and application details upfront.', 'wp-bbtheme-child' ), strtolower( $job[2] ), $job[1] ),
            'post_content' => '<h2>' . esc_html__( 'The opportunity', 'wp-bbtheme-child' ) . '</h2><p>' . esc_html__( 'This demonstration role is designed for repeat recruitment campaigns where the same vacancy may reopen by location, shift or intake date.', 'wp-bbtheme-child' ) . '</p><h2>' . esc_html__( 'What you will do', 'wp-bbtheme-child' ) . '</h2><ul><li>' . esc_html__( 'Work within a clear shift or team structure.', 'wp-bbtheme-child' ) . '</li><li>' . esc_html__( 'Complete a short qualifying step before the full application.', 'wp-bbtheme-child' ) . '</li><li>' . esc_html__( 'Progress through one consistent recruitment pipeline.', 'wp-bbtheme-child' ) . '</li></ul>',
        ) );
        if ( ! $id || is_wp_error( $id ) ) continue;
        update_post_meta( $id, '_wpbb_jobs_sample', 1 );
        update_post_meta( $id, '_wp_theme_demo_profile', 'jobs' );
        update_post_meta( $id, '_wpbb_job_company_id', (int) ( $company_ids[ $job[1] ] ?? 0 ) );
        update_post_meta( $id, '_wpbb_job_salary_min', $job[5] );
        update_post_meta( $id, '_wpbb_job_salary_max', $job[6] );
        update_post_meta( $id, '_wpbb_job_currency', 'GBP' );
        update_post_meta( $id, '_wpbb_job_remote', $job[7] ? 1 : 0 );
        update_post_meta( $id, '_wpbb_job_status', 'open' );
        update_post_meta( $id, '_wpbb_job_deadline', wp_date( 'Y-m-d', strtotime( '+60 days' ) ) );
        foreach ( array( 'wpbb_job_category' => $job[2], 'wpbb_job_type' => $job[3], 'wpbb_job_location' => $job[4] ) as $taxonomy => $name ) {
            $term = get_term_by( 'name', $name, $taxonomy );
            if ( $term ) wp_set_object_terms( $id, array( $term->term_id ), $taxonomy, false );
        }
    }
    update_option( $key, '3.8.10.93', false );
}
add_action( 'admin_init', 'wpbb_jobs_v93_seed_repeat_roles', 355 );

/** Refresh only managed Home/Home 2 content, including Polylang copies. */
function wpbb_jobs_v93_refresh_managed_pages() {
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) return;
    if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) return;
    $key = 'wpbb_jobs_v93_managed_home';
    if ( '3.8.10.93' === (string) get_option( $key ) ) return;

    $profile = function_exists( 'wp_theme_get_demo_profile' ) ? (array) wp_theme_get_demo_profile() : array( 'id' => 'jobs' );
    if ( 'jobs' !== sanitize_key( (string) ( $profile['id'] ?? '' ) ) ) return;
    $sources = array();
    $front = absint( get_option( 'page_on_front' ) );
    if ( $front && get_post_meta( $front, '_wp_theme_demo_managed', true ) ) {
        wp_update_post( array( 'ID' => $front, 'post_content' => wpbb_jobs_home_content_v76( $profile ) ) );
        update_post_meta( $front, '_wpbb_child_bbuilder_version', '3.8.10.93' );
        $sources['home'] = $front;
    }
    $home2 = get_page_by_path( 'home-2', OBJECT, 'page' );
    if ( $home2 instanceof WP_Post && get_post_meta( $home2->ID, '_wp_theme_demo_managed', true ) ) {
        wp_update_post( array( 'ID' => $home2->ID, 'post_content' => wpbb_jobs_portal_page_content( 'home-2' ) ) );
        update_post_meta( $home2->ID, '_wpbb_child_bbuilder_version', '3.8.10.93' );
        $sources['home-2'] = (int) $home2->ID;
    }

    if ( $sources && function_exists( 'pll_get_post' ) && function_exists( 'wp_theme_demo_polylang_languages' ) ) {
        foreach ( array_keys( (array) wp_theme_demo_polylang_languages() ) as $lang ) {
            $lang = sanitize_key( $lang );
            if ( ! $lang || 'en' === $lang ) continue;
            $previous = function_exists( 'wpbb_jobs_set_language_override' ) ? wpbb_jobs_set_language_override( $lang ) : '';
            foreach ( $sources as $slug => $source_id ) {
                $target_id = absint( pll_get_post( $source_id, $lang ) );
                if ( ! $target_id || ! get_post_meta( $target_id, '_wp_theme_demo_managed', true ) ) continue;
                $content = 'home' === $slug ? wpbb_jobs_home_content_v76( $profile ) : wpbb_jobs_portal_page_content( 'home-2' );
                wp_update_post( array( 'ID' => $target_id, 'post_content' => $content ) );
                update_post_meta( $target_id, '_wpbb_child_bbuilder_version', '3.8.10.93' );
            }
            if ( function_exists( 'wpbb_jobs_restore_language_override' ) ) wpbb_jobs_restore_language_override( $previous );
        }
    }
    update_option( $key, '3.8.10.93', false );
}
add_action( 'admin_init', 'wpbb_jobs_v93_refresh_managed_pages', 365 );
