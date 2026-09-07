<?php
/**
 * Integrated recruitment engine for WP BBTheme Child Jobs.
 *
 * Stores recruitment data using normal WordPress users, posts, taxonomies and
 * post meta so the Jobs child theme does not require a separate job-board
 * plugin. The frontend remains Gutenberg/BBuilder friendly and admin data is
 * available through native WordPress screens.
 */

defined( 'ABSPATH' ) || exit;

const WPBB_JOBS_VERSION = '3.8.10.68';

function wpbb_jobs_settings() {
    $defaults = array(
        'job_approval'     => 1,
        'company_approval' => 0,
        'guest_apply'      => 1,
        'currency'         => 'GBP',
        'admin_email'      => get_option( 'admin_email' ),
    );
    $saved = get_option( 'wpbb_jobs_settings', array() );
    return wp_parse_args( is_array( $saved ) ? $saved : array(), $defaults );
}

function wpbb_jobs_setting( $key, $default = null ) {
    $settings = wpbb_jobs_settings();
    return array_key_exists( $key, $settings ) ? $settings[ $key ] : $default;
}

function wpbb_jobs_register_roles() {
    add_role(
        'wpbb_job_candidate',
        __( 'Job Candidate', 'wp-bbtheme-child' ),
        array( 'read' => true, 'upload_files' => true )
    );
    add_role(
        'wpbb_job_employer',
        __( 'Job Employer', 'wp-bbtheme-child' ),
        array( 'read' => true, 'upload_files' => true )
    );
}

function wpbb_jobs_register_admin_capabilities() {
    $role = get_role( 'administrator' );
    if ( ! $role ) return;
    $sets = array(
        array( 'wpbb_job', 'wpbb_jobs' ),
        array( 'wpbb_company', 'wpbb_companies' ),
        array( 'wpbb_resume', 'wpbb_resumes' ),
        array( 'wpbb_application', 'wpbb_applications' ),
    );
    foreach ( $sets as $set ) {
        list( $singular, $plural ) = $set;
        foreach ( array(
            'edit_' . $singular, 'read_' . $singular, 'delete_' . $singular,
            'edit_' . $plural, 'edit_others_' . $plural, 'publish_' . $plural,
            'read_private_' . $plural, 'delete_' . $plural, 'delete_private_' . $plural,
            'delete_published_' . $plural, 'delete_others_' . $plural,
            'edit_private_' . $plural, 'edit_published_' . $plural,
        ) as $cap ) { if ( ! $role->has_cap( $cap ) ) $role->add_cap( $cap ); }
    }
}
add_action( 'init', 'wpbb_jobs_register_admin_capabilities', 4 );

function wpbb_jobs_activate_theme() {
    wpbb_jobs_register_roles();
    wpbb_jobs_register_admin_capabilities();
    wpbb_jobs_register_content_types();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'wpbb_jobs_activate_theme' );
add_action( 'init', 'wpbb_jobs_register_roles', 4 );

function wpbb_jobs_register_content_types() {
    register_post_type( 'wpbb_job', array(
        'labels' => array(
            'name'               => __( 'Jobs', 'wp-bbtheme-child' ),
            'singular_name'      => __( 'Job', 'wp-bbtheme-child' ),
            'add_new_item'       => __( 'Add Job', 'wp-bbtheme-child' ),
            'edit_item'          => __( 'Edit Job', 'wp-bbtheme-child' ),
            'new_item'           => __( 'New Job', 'wp-bbtheme-child' ),
            'view_item'          => __( 'View Job', 'wp-bbtheme-child' ),
            'search_items'       => __( 'Search Jobs', 'wp-bbtheme-child' ),
            'not_found'          => __( 'No jobs found.', 'wp-bbtheme-child' ),
            'all_items'          => __( 'All Jobs', 'wp-bbtheme-child' ),
        ),
        'public'              => true,
        'show_in_rest'        => true,
        'show_in_menu'        => 'wpbb-jobs',
        'menu_icon'           => 'dashicons-businessperson',
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'capability_type'     => array( 'wpbb_job', 'wpbb_jobs' ),
        'has_archive'         => false,
        'rewrite'             => array( 'slug' => 'job', 'with_front' => false ),
        'exclude_from_search' => false,
        'map_meta_cap'        => true,
    ) );

    register_post_type( 'wpbb_company', array(
        'labels' => array(
            'name'          => __( 'Companies', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Company', 'wp-bbtheme-child' ),
            'add_new_item'  => __( 'Add Company', 'wp-bbtheme-child' ),
            'edit_item'     => __( 'Edit Company', 'wp-bbtheme-child' ),
            'all_items'     => __( 'All Companies', 'wp-bbtheme-child' ),
        ),
        'public'       => true,
        'show_in_rest' => true,
        'show_in_menu' => 'wpbb-jobs',
        'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'capability_type' => array( 'wpbb_company', 'wpbb_companies' ),
        'has_archive'  => false,
        'rewrite'      => array( 'slug' => 'employer', 'with_front' => false ),
        'map_meta_cap' => true,
    ) );

    register_post_type( 'wpbb_resume', array(
        'labels' => array(
            'name'          => __( 'Candidate Profiles', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Candidate Profile', 'wp-bbtheme-child' ),
            'add_new_item'  => __( 'Add Candidate Profile', 'wp-bbtheme-child' ),
            'edit_item'     => __( 'Edit Candidate Profile', 'wp-bbtheme-child' ),
            'all_items'     => __( 'Candidate Profiles', 'wp-bbtheme-child' ),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_rest'        => true,
        'show_in_menu'        => 'wpbb-jobs',
        'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions' ),
        'capability_type'     => array( 'wpbb_resume', 'wpbb_resumes' ),
        'exclude_from_search' => true,
        'map_meta_cap'        => true,
    ) );

    register_post_type( 'wpbb_application', array(
        'labels' => array(
            'name'          => __( 'Applications', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Application', 'wp-bbtheme-child' ),
            'edit_item'     => __( 'Review Application', 'wp-bbtheme-child' ),
            'all_items'     => __( 'Applications', 'wp-bbtheme-child' ),
        ),
        'public'              => false,
        'show_ui'             => true,
        'show_in_rest'        => false,
        'show_in_menu'        => 'wpbb-jobs',
        'supports'            => array( 'title', 'author' ),
        'capability_type'     => array( 'wpbb_application', 'wpbb_applications' ),
        'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
        'exclude_from_search' => true,
        'map_meta_cap'        => true,
    ) );

    register_taxonomy( 'wpbb_job_category', array( 'wpbb_job', 'wpbb_resume' ), array(
        'labels' => array(
            'name'          => __( 'Job Categories', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Job Category', 'wp-bbtheme-child' ),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'capabilities'      => array( 'manage_terms' => 'edit_wpbb_jobs', 'edit_terms' => 'edit_wpbb_jobs', 'delete_terms' => 'edit_wpbb_jobs', 'assign_terms' => 'edit_wpbb_jobs' ),
        'rewrite'           => false,
    ) );

    register_taxonomy( 'wpbb_job_type', array( 'wpbb_job' ), array(
        'labels' => array(
            'name'          => __( 'Job Types', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Job Type', 'wp-bbtheme-child' ),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'capabilities'      => array( 'manage_terms' => 'edit_wpbb_jobs', 'edit_terms' => 'edit_wpbb_jobs', 'delete_terms' => 'edit_wpbb_jobs', 'assign_terms' => 'edit_wpbb_jobs' ),
        'rewrite'           => false,
    ) );

    register_taxonomy( 'wpbb_job_location', array( 'wpbb_job', 'wpbb_company', 'wpbb_resume' ), array(
        'labels' => array(
            'name'          => __( 'Locations', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Location', 'wp-bbtheme-child' ),
        ),
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'capabilities'      => array( 'manage_terms' => 'edit_wpbb_jobs', 'edit_terms' => 'edit_wpbb_jobs', 'delete_terms' => 'edit_wpbb_jobs', 'assign_terms' => 'edit_wpbb_jobs' ),
        'rewrite'           => false,
    ) );

    register_taxonomy( 'wpbb_job_skill', array( 'wpbb_job', 'wpbb_resume' ), array(
        'labels' => array(
            'name'          => __( 'Skills', 'wp-bbtheme-child' ),
            'singular_name' => __( 'Skill', 'wp-bbtheme-child' ),
        ),
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => false,
        'capabilities'      => array( 'manage_terms' => 'edit_wpbb_jobs', 'edit_terms' => 'edit_wpbb_jobs', 'delete_terms' => 'edit_wpbb_jobs', 'assign_terms' => 'edit_wpbb_jobs' ),
        'rewrite'           => false,
    ) );

    $meta = array(
        'wpbb_job' => array(
            '_wpbb_job_company_id' => 'integer',
            '_wpbb_job_salary_min' => 'number',
            '_wpbb_job_salary_max' => 'number',
            '_wpbb_job_currency'   => 'string',
            '_wpbb_job_remote'     => 'boolean',
            '_wpbb_job_deadline'   => 'string',
            '_wpbb_job_featured'   => 'boolean',
            '_wpbb_job_status'     => 'string',
        ),
        'wpbb_company' => array(
            '_wpbb_company_website' => 'string',
            '_wpbb_company_email'   => 'string',
            '_wpbb_company_phone'   => 'string',
        ),
        'wpbb_resume' => array(
            '_wpbb_resume_headline'    => 'string',
            '_wpbb_resume_phone'       => 'string',
            '_wpbb_resume_availability'=> 'string',
            '_wpbb_resume_file_id'     => 'integer',
            '_wpbb_resume_visibility'  => 'string',
        ),
        'wpbb_application' => array(
            '_wpbb_application_job_id'       => 'integer',
            '_wpbb_application_resume_id'    => 'integer',
            '_wpbb_application_candidate_id' => 'integer',
            '_wpbb_application_company_id'   => 'integer',
            '_wpbb_application_name'         => 'string',
            '_wpbb_application_email'        => 'string',
            '_wpbb_application_phone'        => 'string',
            '_wpbb_application_message'      => 'string',
            '_wpbb_application_cv_id'        => 'integer',
            '_wpbb_application_status'       => 'string',
        ),
    );

    foreach ( $meta as $post_type => $fields ) {
        foreach ( $fields as $key => $type ) {
            register_post_meta( $post_type, $key, array(
                'type'              => $type,
                'single'            => true,
                'show_in_rest'      => false,
                'sanitize_callback' => 'wpbb_jobs_sanitize_meta_value',
                'auth_callback'     => static function() { return current_user_can( 'edit_wpbb_jobs' ); },
            ) );
        }
    }
}
add_action( 'init', 'wpbb_jobs_register_content_types', 5 );

function wpbb_jobs_sanitize_meta_value( $value, $meta_key = '', $object_type = '' ) {
    unset( $object_type );
    if ( false !== strpos( (string) $meta_key, '_id' ) ) return absint( $value );
    if ( false !== strpos( (string) $meta_key, 'salary_' ) ) return is_numeric( $value ) ? (float) $value : 0;
    if ( in_array( $meta_key, array( '_wpbb_job_remote', '_wpbb_job_featured' ), true ) ) return (bool) $value;
    if ( in_array( $meta_key, array( '_wpbb_company_email', '_wpbb_application_email' ), true ) ) return sanitize_email( $value );
    if ( '_wpbb_company_website' === $meta_key ) return esc_url_raw( $value );
    return sanitize_text_field( $value );
}

function wpbb_jobs_user_has_role( $role, $user_id = 0 ) {
    $user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
    if ( ! $user instanceof WP_User ) return false;
    return in_array( $role, (array) $user->roles, true );
}

function wpbb_jobs_is_admin_user( $user_id = 0 ) {
    if ( $user_id ) return user_can( $user_id, 'edit_wpbb_jobs' );
    return current_user_can( 'edit_wpbb_jobs' );
}

function wpbb_jobs_is_employer( $user_id = 0 ) {
    return wpbb_jobs_is_admin_user( $user_id ) || wpbb_jobs_user_has_role( 'wpbb_job_employer', $user_id );
}

function wpbb_jobs_is_candidate( $user_id = 0 ) {
    return wpbb_jobs_is_admin_user( $user_id ) || wpbb_jobs_user_has_role( 'wpbb_job_candidate', $user_id );
}

function wpbb_jobs_engine_active() {
    return post_type_exists( 'wpbb_job' );
}

function wpbb_jobs_page_url( $slug ) {
    $page = get_page_by_path( sanitize_title( $slug ), OBJECT, 'page' );
    if ( $page instanceof WP_Post ) return get_permalink( $page );
    if ( function_exists( 'wp_theme_demo_page_url' ) ) {
        $url = wp_theme_demo_page_url( $slug );
        if ( $url ) return $url;
    }
    return home_url( '/' . trim( $slug, '/' ) . '/' );
}

function wpbb_jobs_get_user_company_ids( $user_id = 0 ) {
    $user_id = $user_id ?: get_current_user_id();
    if ( ! $user_id ) return array();
    return get_posts( array(
        'post_type'      => 'wpbb_company',
        'post_status'    => array( 'publish', 'pending', 'draft' ),
        'author'         => $user_id,
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );
}

function wpbb_jobs_get_user_resume( $user_id = 0 ) {
    $user_id = $user_id ?: get_current_user_id();
    if ( ! $user_id ) return null;
    $ids = get_posts( array(
        'post_type'      => 'wpbb_resume',
        'post_status'    => array( 'publish', 'pending', 'draft', 'private' ),
        'author'         => $user_id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'orderby'        => 'modified',
        'order'          => 'DESC',
    ) );
    return $ids ? get_post( $ids[0] ) : null;
}

function wpbb_jobs_can_edit_owned_post( $post_id, $post_type ) {
    $post = get_post( $post_id );
    if ( ! $post instanceof WP_Post || $post_type !== $post->post_type ) return false;
    return wpbb_jobs_is_admin_user() || ( get_current_user_id() && (int) $post->post_author === get_current_user_id() );
}

function wpbb_jobs_get_terms_options( $taxonomy ) {
    $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
    return is_wp_error( $terms ) ? array() : $terms;
}

function wpbb_jobs_money( $amount, $currency = '' ) {
    if ( '' === $amount || null === $amount || ! is_numeric( $amount ) ) return '';
    $currency = $currency ?: wpbb_jobs_setting( 'currency', 'GBP' );
    $symbols = array( 'GBP' => '£', 'USD' => '$', 'EUR' => '€', 'CAD' => 'C$', 'AUD' => 'A$' );
    $prefix = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : $currency . ' ';
    return $prefix . number_format_i18n( (float) $amount, ( (float) $amount === floor( (float) $amount ) ) ? 0 : 2 );
}

function wpbb_jobs_salary_label( $job_id ) {
    $min = get_post_meta( $job_id, '_wpbb_job_salary_min', true );
    $max = get_post_meta( $job_id, '_wpbb_job_salary_max', true );
    $currency = get_post_meta( $job_id, '_wpbb_job_currency', true ) ?: wpbb_jobs_setting( 'currency', 'GBP' );
    if ( $min && $max ) return sprintf( '%1$s – %2$s', wpbb_jobs_money( $min, $currency ), wpbb_jobs_money( $max, $currency ) );
    if ( $min ) return sprintf( __( 'From %s', 'wp-bbtheme-child' ), wpbb_jobs_money( $min, $currency ) );
    if ( $max ) return sprintf( __( 'Up to %s', 'wp-bbtheme-child' ), wpbb_jobs_money( $max, $currency ) );
    return '';
}

function wpbb_jobs_company_for_job( $job_id ) {
    $company_id = absint( get_post_meta( $job_id, '_wpbb_job_company_id', true ) );
    return $company_id ? get_post( $company_id ) : null;
}

function wpbb_jobs_job_is_open( $job_id ) {
    if ( 'publish' !== get_post_status( $job_id ) ) return false;
    if ( 'closed' === get_post_meta( $job_id, '_wpbb_job_status', true ) ) return false;
    $deadline = get_post_meta( $job_id, '_wpbb_job_deadline', true );
    return ! $deadline || $deadline >= wp_date( 'Y-m-d' );
}

function wpbb_jobs_application_statuses() {
    return array(
        'new'         => __( 'New', 'wp-bbtheme-child' ),
        'reviewing'   => __( 'Reviewing', 'wp-bbtheme-child' ),
        'shortlisted' => __( 'Shortlisted', 'wp-bbtheme-child' ),
        'interview'   => __( 'Interview', 'wp-bbtheme-child' ),
        'offered'     => __( 'Offer made', 'wp-bbtheme-child' ),
        'hired'       => __( 'Hired', 'wp-bbtheme-child' ),
        'rejected'    => __( 'Not selected', 'wp-bbtheme-child' ),
        'withdrawn'   => __( 'Withdrawn', 'wp-bbtheme-child' ),
    );
}

function wpbb_jobs_redirect_with_notice( $url, $type, $message ) {
    $key = 'wpbb_jobs_notice_' . wp_generate_password( 12, false, false );
    set_transient( $key, array( 'type' => sanitize_key( $type ), 'message' => sanitize_text_field( $message ) ), 5 * MINUTE_IN_SECONDS );
    wp_safe_redirect( add_query_arg( 'jobs_notice', rawurlencode( $key ), $url ) );
    exit;
}

function wpbb_jobs_front_notice_html() {
    if ( empty( $_GET['jobs_notice'] ) ) return '';
    $key = sanitize_key( wp_unslash( $_GET['jobs_notice'] ) );
    $notice = get_transient( $key );
    if ( ! is_array( $notice ) ) return '';
    delete_transient( $key );
    $type = in_array( $notice['type'] ?? '', array( 'success', 'error', 'info' ), true ) ? $notice['type'] : 'info';
    return '<div class="wpbb-jobs-notice is-' . esc_attr( $type ) . '" role="status">' . esc_html( $notice['message'] ?? '' ) . '</div>';
}

function wpbb_jobs_handle_registration() {
    if ( is_user_logged_in() ) wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'info', __( 'You are already signed in.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_register', 'wpbb_jobs_nonce' );

    $account_type = sanitize_key( wp_unslash( $_POST['account_type'] ?? '' ) );
    $role = 'employer' === $account_type ? 'wpbb_job_employer' : 'wpbb_job_candidate';
    $email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $name  = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
    $password = (string) ( $_POST['password'] ?? '' );

    if ( ! is_email( $email ) || strlen( $password ) < 8 || '' === $name ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'Enter your name, a valid email address and a password of at least 8 characters.', 'wp-bbtheme-child' ) );
    }
    if ( email_exists( $email ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'An account already exists for that email address.', 'wp-bbtheme-child' ) );
    }

    $base = sanitize_user( strstr( $email, '@', true ), true );
    $base = $base ?: 'user';
    $username = $base;
    $counter = 2;
    while ( username_exists( $username ) ) {
        $username = $base . $counter;
        $counter++;
    }

    $user_id = wp_insert_user( array(
        'user_login'   => $username,
        'user_email'   => $email,
        'display_name' => $name,
        'user_pass'    => $password,
        'role'         => $role,
    ) );
    if ( is_wp_error( $user_id ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', $user_id->get_error_message() );
    }

    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    $target = 'wpbb_job_employer' === $role ? wpbb_jobs_page_url( 'employer-dashboard' ) : wpbb_jobs_page_url( 'candidate-dashboard' );
    wpbb_jobs_redirect_with_notice( $target, 'success', __( 'Your account is ready.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_nopriv_wpbb_jobs_register', 'wpbb_jobs_handle_registration' );

function wpbb_jobs_handle_company_save() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) wp_die( esc_html__( 'Employer access required.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_company_save', 'wpbb_jobs_nonce' );

    $company_id = absint( $_POST['company_id'] ?? 0 );
    if ( $company_id && ! wpbb_jobs_can_edit_owned_post( $company_id, 'wpbb_company' ) ) wp_die( esc_html__( 'You cannot edit this company.', 'wp-bbtheme-child' ) );

    $name = sanitize_text_field( wp_unslash( $_POST['company_name'] ?? '' ) );
    if ( '' === $name ) wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'employer-dashboard' ), 'error', __( 'Company name is required.', 'wp-bbtheme-child' ) );

    $status = wpbb_jobs_setting( 'company_approval', 0 ) && ! wpbb_jobs_is_admin_user() ? 'pending' : 'publish';
    $args = array(
        'post_type'    => 'wpbb_company',
        'post_status'  => $status,
        'post_title'   => $name,
        'post_content' => wp_kses_post( wp_unslash( $_POST['company_description'] ?? '' ) ),
        'post_excerpt' => sanitize_textarea_field( wp_unslash( $_POST['company_excerpt'] ?? '' ) ),
        'post_author'  => get_current_user_id(),
    );
    if ( $company_id ) $args['ID'] = $company_id;
    $saved = $company_id ? wp_update_post( wp_slash( $args ), true ) : wp_insert_post( wp_slash( $args ), true );
    if ( is_wp_error( $saved ) ) wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'employer-dashboard' ), 'error', $saved->get_error_message() );

    update_post_meta( $saved, '_wpbb_company_website', esc_url_raw( wp_unslash( $_POST['company_website'] ?? '' ) ) );
    update_post_meta( $saved, '_wpbb_company_email', sanitize_email( wp_unslash( $_POST['company_email'] ?? '' ) ) );
    update_post_meta( $saved, '_wpbb_company_phone', sanitize_text_field( wp_unslash( $_POST['company_phone'] ?? '' ) ) );
    $location = absint( $_POST['company_location'] ?? 0 );
    wp_set_object_terms( $saved, $location ? array( $location ) : array(), 'wpbb_job_location', false );

    wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'employer-dashboard' ), 'success', __( 'Company profile saved.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_wpbb_jobs_company_save', 'wpbb_jobs_handle_company_save' );

function wpbb_jobs_handle_job_save() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) wp_die( esc_html__( 'Employer access required.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_job_save', 'wpbb_jobs_nonce' );

    $job_id = absint( $_POST['job_id'] ?? 0 );
    if ( $job_id && ! wpbb_jobs_can_edit_owned_post( $job_id, 'wpbb_job' ) ) wp_die( esc_html__( 'You cannot edit this job.', 'wp-bbtheme-child' ) );

    $title = sanitize_text_field( wp_unslash( $_POST['job_title'] ?? '' ) );
    $description = wp_kses_post( wp_unslash( $_POST['job_description'] ?? '' ) );
    $company_id = absint( $_POST['company_id'] ?? 0 );
    $owned_companies = wpbb_jobs_get_user_company_ids();
    if ( ! wpbb_jobs_is_admin_user() && $company_id && ! in_array( $company_id, array_map( 'intval', $owned_companies ), true ) ) $company_id = 0;

    if ( '' === $title || '' === trim( wp_strip_all_tags( $description ) ) || ! $company_id ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'post-a-job' ), 'error', __( 'Job title, company and job description are required.', 'wp-bbtheme-child' ) );
    }

    $status = wpbb_jobs_setting( 'job_approval', 1 ) && ! wpbb_jobs_is_admin_user() ? 'pending' : 'publish';
    $args = array(
        'post_type'    => 'wpbb_job',
        'post_status'  => $status,
        'post_title'   => $title,
        'post_content' => $description,
        'post_excerpt' => sanitize_textarea_field( wp_unslash( $_POST['job_excerpt'] ?? '' ) ),
        'post_author'  => get_current_user_id(),
    );
    if ( $job_id ) $args['ID'] = $job_id;
    $saved = $job_id ? wp_update_post( wp_slash( $args ), true ) : wp_insert_post( wp_slash( $args ), true );
    if ( is_wp_error( $saved ) ) wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'post-a-job' ), 'error', $saved->get_error_message() );

    update_post_meta( $saved, '_wpbb_job_company_id', $company_id );
    update_post_meta( $saved, '_wpbb_job_salary_min', (float) ( $_POST['salary_min'] ?? 0 ) );
    update_post_meta( $saved, '_wpbb_job_salary_max', (float) ( $_POST['salary_max'] ?? 0 ) );
    update_post_meta( $saved, '_wpbb_job_currency', sanitize_text_field( wp_unslash( $_POST['currency'] ?? wpbb_jobs_setting( 'currency', 'GBP' ) ) ) );
    update_post_meta( $saved, '_wpbb_job_remote', ! empty( $_POST['remote'] ) ? 1 : 0 );
    update_post_meta( $saved, '_wpbb_job_deadline', sanitize_text_field( wp_unslash( $_POST['deadline'] ?? '' ) ) );
    $job_status = sanitize_key( wp_unslash( $_POST['job_status'] ?? 'open' ) );
    update_post_meta( $saved, '_wpbb_job_status', in_array( $job_status, array( 'open', 'closed' ), true ) ? $job_status : 'open' );

    foreach ( array( 'wpbb_job_category' => 'job_category', 'wpbb_job_type' => 'job_type', 'wpbb_job_location' => 'job_location' ) as $taxonomy => $field ) {
        $term = absint( $_POST[ $field ] ?? 0 );
        wp_set_object_terms( $saved, $term ? array( $term ) : array(), $taxonomy, false );
    }

    $msg = 'pending' === $status
        ? __( 'Job submitted for review.', 'wp-bbtheme-child' )
        : __( 'Job published.', 'wp-bbtheme-child' );
    wpbb_jobs_notify_admin_new_job( $saved, $status );
    wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'employer-dashboard' ), 'success', $msg );
}
add_action( 'admin_post_wpbb_jobs_job_save', 'wpbb_jobs_handle_job_save' );

function wpbb_jobs_handle_resume_save() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_candidate() ) wp_die( esc_html__( 'Candidate access required.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_resume_save', 'wpbb_jobs_nonce' );

    $resume = wpbb_jobs_get_user_resume();
    $resume_id = $resume instanceof WP_Post ? $resume->ID : 0;
    $name = sanitize_text_field( wp_unslash( $_POST['candidate_name'] ?? '' ) );
    $headline = sanitize_text_field( wp_unslash( $_POST['headline'] ?? '' ) );
    if ( '' === $name || '' === $headline ) wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'create-resume' ), 'error', __( 'Name and professional headline are required.', 'wp-bbtheme-child' ) );

    $args = array(
        'post_type'    => 'wpbb_resume',
        'post_status'  => 'publish',
        'post_title'   => $name,
        'post_content' => wp_kses_post( wp_unslash( $_POST['resume_summary'] ?? '' ) ),
        'post_excerpt' => $headline,
        'post_author'  => get_current_user_id(),
    );
    if ( $resume_id ) $args['ID'] = $resume_id;
    $saved = $resume_id ? wp_update_post( wp_slash( $args ), true ) : wp_insert_post( wp_slash( $args ), true );
    if ( is_wp_error( $saved ) ) wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'create-resume' ), 'error', $saved->get_error_message() );

    update_post_meta( $saved, '_wpbb_resume_headline', $headline );
    update_post_meta( $saved, '_wpbb_resume_phone', sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) ) );
    update_post_meta( $saved, '_wpbb_resume_availability', sanitize_text_field( wp_unslash( $_POST['availability'] ?? '' ) ) );
    update_post_meta( $saved, '_wpbb_resume_visibility', 'employers' );

    foreach ( array( 'wpbb_job_category' => 'resume_category', 'wpbb_job_location' => 'resume_location' ) as $taxonomy => $field ) {
        $term = absint( $_POST[ $field ] ?? 0 );
        wp_set_object_terms( $saved, $term ? array( $term ) : array(), $taxonomy, false );
    }
    $skills = sanitize_text_field( wp_unslash( $_POST['skills'] ?? '' ) );
    if ( '' !== $skills ) {
        $skill_terms = array_filter( array_map( 'trim', explode( ',', $skills ) ) );
        wp_set_object_terms( $saved, $skill_terms, 'wpbb_job_skill', false );
    }

    if ( ! empty( $_FILES['resume_file']['name'] ) ) {
        $attachment = wpbb_jobs_handle_document_upload( 'resume_file', $saved );
        if ( is_wp_error( $attachment ) ) {
            wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'create-resume' ), 'error', sprintf( __( 'Profile saved, but the CV could not be uploaded: %s', 'wp-bbtheme-child' ), $attachment->get_error_message() ) );
        }
        update_post_meta( $saved, '_wpbb_resume_file_id', $attachment );
    }

    wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'candidate-dashboard' ), 'success', __( 'Candidate profile saved.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_wpbb_jobs_resume_save', 'wpbb_jobs_handle_resume_save' );

function wpbb_jobs_handle_document_upload( $field, $parent_post_id = 0 ) {
    if ( empty( $_FILES[ $field ]['name'] ) || empty( $_FILES[ $field ]['tmp_name'] ) ) return new WP_Error( 'missing_upload', __( 'No file uploaded.', 'wp-bbtheme-child' ) );
    $allowed = array(
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    );
    $type = wp_check_filetype( sanitize_file_name( wp_unslash( $_FILES[ $field ]['name'] ) ), $allowed );
    if ( empty( $type['ext'] ) || empty( $type['type'] ) ) return new WP_Error( 'invalid_upload', __( 'Please upload a PDF, DOC or DOCX file.', 'wp-bbtheme-child' ) );
    if ( ! empty( $_FILES[ $field ]['size'] ) && (int) $_FILES[ $field ]['size'] > 10 * MB_IN_BYTES ) {
        return new WP_Error( 'upload_too_large', __( 'CV files must be 10 MB or smaller.', 'wp-bbtheme-child' ) );
    }
    $original_name = sanitize_file_name( wp_unslash( $_FILES[ $field ]['name'] ) );
    $_FILES[ $field ]['name'] = 'cv-' . strtolower( wp_generate_password( 24, false, false ) ) . '.' . $type['ext'];
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attachment_id = media_handle_upload( $field, $parent_post_id, array(), array( 'test_form' => false ) );
    if ( ! is_wp_error( $attachment_id ) ) update_post_meta( $attachment_id, '_wpbb_jobs_original_filename', $original_name );
    return $attachment_id;
}

function wpbb_jobs_handle_application() {
    $job_id = absint( $_POST['job_id'] ?? 0 );
    $return = $job_id ? get_permalink( $job_id ) : wpbb_jobs_page_url( 'jobs' );
    check_admin_referer( 'wpbb_jobs_apply_' . $job_id, 'wpbb_jobs_nonce' );
    if ( ! $job_id || 'wpbb_job' !== get_post_type( $job_id ) || ! wpbb_jobs_job_is_open( $job_id ) ) {
        wpbb_jobs_redirect_with_notice( $return, 'error', __( 'This job is not accepting applications.', 'wp-bbtheme-child' ) );
    }

    $user_id = get_current_user_id();
    $resume = $user_id ? wpbb_jobs_get_user_resume( $user_id ) : null;
    $name  = $user_id ? wp_get_current_user()->display_name : sanitize_text_field( wp_unslash( $_POST['applicant_name'] ?? '' ) );
    $email = $user_id ? wp_get_current_user()->user_email : sanitize_email( wp_unslash( $_POST['applicant_email'] ?? '' ) );
    $phone = sanitize_text_field( wp_unslash( $_POST['applicant_phone'] ?? '' ) );
    $message = sanitize_textarea_field( wp_unslash( $_POST['application_message'] ?? '' ) );

    if ( ! $user_id && ! wpbb_jobs_setting( 'guest_apply', 1 ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'login-register' ), 'error', __( 'Please sign in or create a candidate account before applying.', 'wp-bbtheme-child' ) );
    }
    if ( $user_id && ! wpbb_jobs_is_candidate() ) {
        wpbb_jobs_redirect_with_notice( $return, 'error', __( 'Use a candidate account to apply for jobs.', 'wp-bbtheme-child' ) );
    }
    if ( '' === $name || ! is_email( $email ) ) {
        wpbb_jobs_redirect_with_notice( $return, 'error', __( 'Your name and a valid email address are required.', 'wp-bbtheme-child' ) );
    }
    if ( ! $user_id && empty( $_FILES['cv_file']['name'] ) ) {
        wpbb_jobs_redirect_with_notice( $return, 'error', __( 'Please attach a CV to a guest application.', 'wp-bbtheme-child' ) );
    }

    $duplicate_args = array(
        'post_type'      => 'wpbb_application',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array( 'key' => '_wpbb_application_job_id', 'value' => $job_id, 'compare' => '=' ),
            $user_id
                ? array( 'key' => '_wpbb_application_candidate_id', 'value' => $user_id, 'compare' => '=' )
                : array( 'key' => '_wpbb_application_email', 'value' => $email, 'compare' => '=' ),
        ),
    );
    if ( get_posts( $duplicate_args ) ) {
        wpbb_jobs_redirect_with_notice( $return, 'info', __( 'An application for this job has already been submitted with this account or email address.', 'wp-bbtheme-child' ) );
    }

    $company = wpbb_jobs_company_for_job( $job_id );
    $application_id = wp_insert_post( array(
        'post_type'   => 'wpbb_application',
        'post_status' => 'publish',
        'post_title'  => sprintf( '%1$s — %2$s', $name, get_the_title( $job_id ) ),
        'post_author' => $user_id ?: 0,
    ), true );
    if ( is_wp_error( $application_id ) ) wpbb_jobs_redirect_with_notice( $return, 'error', $application_id->get_error_message() );

    $meta = array(
        '_wpbb_application_job_id'       => $job_id,
        '_wpbb_application_resume_id'    => $resume instanceof WP_Post ? $resume->ID : 0,
        '_wpbb_application_candidate_id' => $user_id,
        '_wpbb_application_company_id'   => $company instanceof WP_Post ? $company->ID : 0,
        '_wpbb_application_name'         => $name,
        '_wpbb_application_email'        => $email,
        '_wpbb_application_phone'        => $phone,
        '_wpbb_application_message'      => $message,
        '_wpbb_application_status'       => 'new',
    );
    foreach ( $meta as $key => $value ) update_post_meta( $application_id, $key, $value );

    if ( ! empty( $_FILES['cv_file']['name'] ) ) {
        $attachment = wpbb_jobs_handle_document_upload( 'cv_file', $application_id );
        if ( is_wp_error( $attachment ) ) {
            wp_delete_post( $application_id, true );
            wpbb_jobs_redirect_with_notice( $return, 'error', sprintf( __( 'The application was not submitted because the CV upload failed: %s', 'wp-bbtheme-child' ), $attachment->get_error_message() ) );
        }
        update_post_meta( $application_id, '_wpbb_application_cv_id', $attachment );
    } elseif ( $resume instanceof WP_Post ) {
        $resume_file = absint( get_post_meta( $resume->ID, '_wpbb_resume_file_id', true ) );
        if ( $resume_file ) update_post_meta( $application_id, '_wpbb_application_cv_id', $resume_file );
    }

    wpbb_jobs_send_application_emails( $application_id );
    wpbb_jobs_redirect_with_notice( $return, 'success', __( 'Application sent. You can track it from your candidate dashboard.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_wpbb_jobs_apply', 'wpbb_jobs_handle_application' );
add_action( 'admin_post_nopriv_wpbb_jobs_apply', 'wpbb_jobs_handle_application' );

function wpbb_jobs_handle_application_status() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) wp_die( esc_html__( 'Employer access required.', 'wp-bbtheme-child' ) );
    $application_id = absint( $_POST['application_id'] ?? 0 );
    check_admin_referer( 'wpbb_jobs_application_status_' . $application_id, 'wpbb_jobs_nonce' );
    $job_id = absint( get_post_meta( $application_id, '_wpbb_application_job_id', true ) );
    $job = get_post( $job_id );
    if ( ! $job instanceof WP_Post || ( ! wpbb_jobs_is_admin_user() && (int) $job->post_author !== get_current_user_id() ) ) wp_die( esc_html__( 'You cannot update this application.', 'wp-bbtheme-child' ) );
    $status = sanitize_key( wp_unslash( $_POST['application_status'] ?? '' ) );
    $statuses = wpbb_jobs_application_statuses();
    if ( ! isset( $statuses[ $status ] ) ) $status = 'reviewing';
    update_post_meta( $application_id, '_wpbb_application_status', $status );
    wpbb_jobs_send_status_email( $application_id, $status );
    wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'employer-dashboard' ), 'success', __( 'Application status updated.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_wpbb_jobs_application_status', 'wpbb_jobs_handle_application_status' );

function wpbb_jobs_notify_admin_new_job( $job_id, $status ) {
    if ( 'pending' !== $status ) return;
    $to = sanitize_email( wpbb_jobs_setting( 'admin_email', get_option( 'admin_email' ) ) );
    if ( ! $to ) return;
    wp_mail(
        $to,
        sprintf( __( 'Job awaiting review: %s', 'wp-bbtheme-child' ), get_the_title( $job_id ) ),
        sprintf( __( "A new job has been submitted and is awaiting review.\n\n%s", 'wp-bbtheme-child' ), admin_url( 'post.php?post=' . $job_id . '&action=edit' ) )
    );
}

function wpbb_jobs_send_application_emails( $application_id ) {
    $job_id = absint( get_post_meta( $application_id, '_wpbb_application_job_id', true ) );
    $job = get_post( $job_id );
    if ( ! $job instanceof WP_Post ) return;
    $candidate_email = sanitize_email( get_post_meta( $application_id, '_wpbb_application_email', true ) );
    $employer = get_userdata( $job->post_author );
    $admin_email = sanitize_email( wpbb_jobs_setting( 'admin_email', get_option( 'admin_email' ) ) );
    $recipients = array_filter( array_unique( array( $employer ? $employer->user_email : '', $admin_email ) ) );
    $subject = sprintf( __( 'New application: %s', 'wp-bbtheme-child' ), get_the_title( $job_id ) );
    $body = sprintf(
        __( "A new application has been received for %1$s.\n\nCandidate: %2$s\nEmail: %3$s\n\nReview: %4$s", 'wp-bbtheme-child' ),
        get_the_title( $job_id ),
        get_post_meta( $application_id, '_wpbb_application_name', true ),
        $candidate_email,
        admin_url( 'post.php?post=' . $application_id . '&action=edit' )
    );
    foreach ( $recipients as $recipient ) wp_mail( $recipient, $subject, $body );
    if ( $candidate_email ) {
        wp_mail(
            $candidate_email,
            sprintf( __( 'Application received: %s', 'wp-bbtheme-child' ), get_the_title( $job_id ) ),
            sprintf( __( 'Your application for %s has been received. You can track updates from your candidate dashboard.', 'wp-bbtheme-child' ), get_the_title( $job_id ) )
        );
    }
}

function wpbb_jobs_send_status_email( $application_id, $status ) {
    $email = sanitize_email( get_post_meta( $application_id, '_wpbb_application_email', true ) );
    $job_id = absint( get_post_meta( $application_id, '_wpbb_application_job_id', true ) );
    $statuses = wpbb_jobs_application_statuses();
    if ( ! $email || ! isset( $statuses[ $status ] ) ) return;
    wp_mail(
        $email,
        sprintf( __( 'Application update: %s', 'wp-bbtheme-child' ), get_the_title( $job_id ) ),
        sprintf( __( 'Your application status for %1$s is now: %2$s.', 'wp-bbtheme-child' ), get_the_title( $job_id ), $statuses[ $status ] )
    );
}

function wpbb_jobs_seed_default_terms() {
    $terms = array(
        'wpbb_job_type' => array( 'Full time', 'Part time', 'Contract', 'Temporary', 'Internship', 'Graduate' ),
        'wpbb_job_category' => array( 'Technology', 'Marketing', 'Sales', 'Operations', 'Finance', 'People & HR', 'Customer Success', 'Design' ),
        'wpbb_job_location' => array( 'London', 'Manchester', 'Birmingham', 'Edinburgh', 'Bristol', 'Remote' ),
    );
    foreach ( $terms as $taxonomy => $names ) {
        foreach ( $names as $name ) {
            if ( ! term_exists( $name, $taxonomy ) ) wp_insert_term( $name, $taxonomy );
        }
    }
}

function wpbb_jobs_seed_sample_data() {
    wpbb_jobs_seed_default_terms();
    $admin = get_users( array( 'role__in' => array( 'administrator' ), 'number' => 1, 'fields' => 'ID' ) );
    $author = $admin ? (int) $admin[0] : get_current_user_id();
    $companies = array(
        array( 'Northstar Digital', 'Product and engineering teams building useful digital services.', 'London', 'https://example.com/northstar' ),
        array( 'Greenline Energy', 'Clean-energy infrastructure, operations and customer technology.', 'Bristol', 'https://example.com/greenline' ),
        array( 'Harbour Health', 'Healthcare services with multidisciplinary clinical and operations teams.', 'Manchester', 'https://example.com/harbour-health' ),
        array( 'Studio Forty Two', 'Independent design and brand studio working across digital products.', 'Remote', 'https://example.com/studio42' ),
        array( 'Atlas Logistics', 'National logistics and fulfilment business with technology-led operations.', 'Birmingham', 'https://example.com/atlas' ),
        array( 'CivicWorks', 'Public-service consultancy focused on transformation, data and delivery.', 'Edinburgh', 'https://example.com/civicworks' ),
    );
    $company_ids = array();
    foreach ( $companies as $item ) {
        $existing = get_posts( array( 'post_type' => 'wpbb_company', 'post_status' => 'any', 'title' => $item[0], 'posts_per_page' => 1, 'fields' => 'ids' ) );
        if ( $existing ) {
            $company_id = (int) $existing[0];
        } else {
            $company_id = wp_insert_post( array(
                'post_type' => 'wpbb_company', 'post_status' => 'publish', 'post_title' => $item[0],
                'post_excerpt' => $item[1], 'post_content' => '<p>' . esc_html( $item[1] ) . '</p>', 'post_author' => $author,
            ) );
            update_post_meta( $company_id, '_wpbb_jobs_sample', 1 );
            update_post_meta( $company_id, '_wpbb_company_website', $item[3] );
            $term = get_term_by( 'name', $item[2], 'wpbb_job_location' );
            if ( $term ) wp_set_object_terms( $company_id, array( $term->term_id ), 'wpbb_job_location', false );
        }
        $company_ids[ $item[0] ] = $company_id;
    }

    $jobs = array(
        array( 'Senior Product Designer', 'Northstar Digital', 'Design', 'Full time', 'London', 65000, 78000, false ),
        array( 'Frontend Engineer', 'Northstar Digital', 'Technology', 'Full time', 'Remote', 58000, 72000, true ),
        array( 'Renewables Project Coordinator', 'Greenline Energy', 'Operations', 'Full time', 'Bristol', 38000, 46000, false ),
        array( 'Customer Operations Lead', 'Greenline Energy', 'Customer Success', 'Full time', 'Remote', 45000, 54000, true ),
        array( 'People Partner', 'Harbour Health', 'People & HR', 'Full time', 'Manchester', 48000, 56000, false ),
        array( 'Service Designer', 'CivicWorks', 'Design', 'Contract', 'Edinburgh', 52000, 65000, true ),
        array( 'Account Director', 'Studio Forty Two', 'Sales', 'Full time', 'Remote', 50000, 62000, true ),
        array( 'Junior Brand Designer', 'Studio Forty Two', 'Design', 'Graduate', 'London', 28000, 33000, false ),
        array( 'Transport Planning Analyst', 'Atlas Logistics', 'Operations', 'Full time', 'Birmingham', 36000, 44000, false ),
        array( 'Data Analyst', 'CivicWorks', 'Technology', 'Full time', 'Remote', 42000, 52000, true ),
        array( 'Finance Business Partner', 'Atlas Logistics', 'Finance', 'Full time', 'Birmingham', 55000, 68000, false ),
        array( 'Content Marketing Manager', 'Harbour Health', 'Marketing', 'Part time', 'Manchester', 38000, 45000, true ),
    );
    $created = 0;
    foreach ( $jobs as $item ) {
        $existing = get_posts( array( 'post_type' => 'wpbb_job', 'post_status' => 'any', 'title' => $item[0], 'posts_per_page' => 1, 'fields' => 'ids' ) );
        if ( $existing ) continue;
        $job_id = wp_insert_post( array(
            'post_type' => 'wpbb_job', 'post_status' => 'publish', 'post_title' => $item[0], 'post_author' => $author,
            'post_excerpt' => sprintf( __( 'Join %1$s in a practical %2$s role with clear responsibilities and room to grow.', 'wp-bbtheme-child' ), $item[1], strtolower( $item[2] ) ),
            'post_content' => '<h2>' . esc_html__( 'The role', 'wp-bbtheme-child' ) . '</h2><p>' . esc_html__( 'Work with a collaborative team on useful, well-scoped work. This demo vacancy includes realistic structure for responsibilities, experience, benefits and application information.', 'wp-bbtheme-child' ) . '</p><h2>' . esc_html__( 'What you will bring', 'wp-bbtheme-child' ) . '</h2><ul><li>' . esc_html__( 'Relevant practical experience and clear communication.', 'wp-bbtheme-child' ) . '</li><li>' . esc_html__( 'Comfort working with colleagues across different disciplines.', 'wp-bbtheme-child' ) . '</li><li>' . esc_html__( 'A thoughtful approach to priorities, quality and delivery.', 'wp-bbtheme-child' ) . '</li></ul>',
        ) );
        if ( ! $job_id || is_wp_error( $job_id ) ) continue;
        update_post_meta( $job_id, '_wpbb_jobs_sample', 1 );
        update_post_meta( $job_id, '_wpbb_job_company_id', $company_ids[ $item[1] ] ?? 0 );
        update_post_meta( $job_id, '_wpbb_job_salary_min', $item[5] );
        update_post_meta( $job_id, '_wpbb_job_salary_max', $item[6] );
        update_post_meta( $job_id, '_wpbb_job_currency', 'GBP' );
        update_post_meta( $job_id, '_wpbb_job_remote', $item[7] ? 1 : 0 );
        update_post_meta( $job_id, '_wpbb_job_status', 'open' );
        update_post_meta( $job_id, '_wpbb_job_deadline', wp_date( 'Y-m-d', strtotime( '+45 days' ) ) );
        foreach ( array( 'wpbb_job_category' => $item[2], 'wpbb_job_type' => $item[3], 'wpbb_job_location' => $item[4] ) as $taxonomy => $name ) {
            $term = get_term_by( 'name', $name, $taxonomy );
            if ( $term ) wp_set_object_terms( $job_id, array( $term->term_id ), $taxonomy, false );
        }
        $created++;
    }

    $resumes = array(
        array( 'Amelia Carter', 'Senior Product Designer', 'Design', 'London', 'Product design, UX research, Design systems', 'Product designer with experience simplifying complex services, facilitating research and building design systems with multidisciplinary teams.' ),
        array( 'Noah Williams', 'Frontend Engineer', 'Technology', 'Remote', 'JavaScript, React, Accessibility, CSS', 'Frontend engineer focused on accessible interfaces, maintainable component systems and pragmatic collaboration with design and backend teams.' ),
        array( 'Priya Shah', 'People & Culture Partner', 'People & HR', 'Manchester', 'Employee relations, Coaching, Talent development', 'People partner supporting growing teams with practical employee relations, manager coaching and organisation development.' ),
        array( 'Daniel Morgan', 'Operations & Logistics Analyst', 'Operations', 'Birmingham', 'Operations, Excel, Data analysis, Planning', 'Operations analyst who turns delivery and capacity data into clear decisions for busy logistics and service teams.' ),
        array( 'Sofia Rossi', 'Content Marketing Manager', 'Marketing', 'Remote', 'Content strategy, SEO, Campaigns, Analytics', 'Content marketer combining editorial planning, search insight and campaign measurement across B2B and service organisations.' ),
        array( 'Ethan Clarke', 'Finance Business Partner', 'Finance', 'Edinburgh', 'Forecasting, Commercial finance, Reporting', 'Commercial finance professional experienced in forecasting, stakeholder support and turning management information into useful action.' ),
    );
    $resume_ids = array();
    foreach ( $resumes as $item ) {
        $existing = get_posts( array( 'post_type' => 'wpbb_resume', 'post_status' => 'any', 'title' => $item[0], 'posts_per_page' => 1, 'fields' => 'ids' ) );
        if ( $existing ) { $resume_ids[] = (int) $existing[0]; continue; }
        $resume_id = wp_insert_post( array(
            'post_type' => 'wpbb_resume', 'post_status' => 'publish', 'post_title' => $item[0], 'post_author' => $author,
            'post_excerpt' => $item[1], 'post_content' => '<p>' . esc_html( $item[5] ) . '</p>',
        ) );
        if ( ! $resume_id || is_wp_error( $resume_id ) ) continue;
        update_post_meta( $resume_id, '_wpbb_jobs_sample', 1 );
        update_post_meta( $resume_id, '_wpbb_resume_headline', $item[1] );
        update_post_meta( $resume_id, '_wpbb_resume_availability', __( 'Available for the right opportunity', 'wp-bbtheme-child' ) );
        update_post_meta( $resume_id, '_wpbb_resume_visibility', 'employers' );
        foreach ( array( 'wpbb_job_category' => $item[2], 'wpbb_job_location' => $item[3] ) as $taxonomy => $name ) {
            $term = get_term_by( 'name', $name, $taxonomy );
            if ( $term ) wp_set_object_terms( $resume_id, array( $term->term_id ), $taxonomy, false );
        }
        wp_set_object_terms( $resume_id, array_map( 'trim', explode( ',', $item[4] ) ), 'wpbb_job_skill', false );
        $resume_ids[] = $resume_id;
    }

    $sample_job_ids = get_posts( array( 'post_type' => 'wpbb_job', 'post_status' => 'publish', 'posts_per_page' => 4, 'fields' => 'ids', 'meta_key' => '_wpbb_jobs_sample', 'meta_value' => 1, 'orderby' => 'date', 'order' => 'DESC' ) );
    $application_created = 0;
    $application_statuses = array( 'new', 'reviewing', 'shortlisted', 'interview' );
    foreach ( array_slice( $resume_ids, 0, min( 4, count( $sample_job_ids ) ) ) as $index => $resume_id ) {
        $job_id = (int) ( $sample_job_ids[ $index ] ?? 0 );
        if ( ! $job_id ) continue;
        $existing = get_posts( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_query' => array( array( 'key' => '_wpbb_application_job_id', 'value' => $job_id ), array( 'key' => '_wpbb_application_resume_id', 'value' => $resume_id ) ) ) );
        if ( $existing ) continue;
        $resume_post = get_post( $resume_id );
        $company = wpbb_jobs_company_for_job( $job_id );
        $application_id = wp_insert_post( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'post_title' => $resume_post->post_title . ' — ' . get_the_title( $job_id ), 'post_author' => $author ) );
        if ( ! $application_id || is_wp_error( $application_id ) ) continue;
        update_post_meta( $application_id, '_wpbb_jobs_sample', 1 );
        update_post_meta( $application_id, '_wpbb_application_job_id', $job_id );
        update_post_meta( $application_id, '_wpbb_application_resume_id', $resume_id );
        update_post_meta( $application_id, '_wpbb_application_candidate_id', 0 );
        update_post_meta( $application_id, '_wpbb_application_company_id', $company instanceof WP_Post ? $company->ID : 0 );
        update_post_meta( $application_id, '_wpbb_application_name', $resume_post->post_title );
        update_post_meta( $application_id, '_wpbb_application_email', 'candidate' . ( $index + 1 ) . '@example.com' );
        update_post_meta( $application_id, '_wpbb_application_message', __( 'Sample application for demonstrating the employer review workflow.', 'wp-bbtheme-child' ) );
        update_post_meta( $application_id, '_wpbb_application_status', $application_statuses[ $index ] ?? 'new' );
        $application_created++;
    }

    return array( 'companies' => count( $company_ids ), 'jobs_created' => $created, 'resumes' => count( $resume_ids ), 'applications_created' => $application_created );
}

function wpbb_jobs_job_schema() {
    if ( ! is_singular( 'wpbb_job' ) ) return;
    $job_id = get_queried_object_id();
    if ( ! $job_id ) return;
    $company = wpbb_jobs_company_for_job( $job_id );
    $location_terms = wp_get_post_terms( $job_id, 'wpbb_job_location' );
    $types = wp_get_post_terms( $job_id, 'wpbb_job_type', array( 'fields' => 'names' ) );
    $min = get_post_meta( $job_id, '_wpbb_job_salary_min', true );
    $max = get_post_meta( $job_id, '_wpbb_job_salary_max', true );
    $currency = get_post_meta( $job_id, '_wpbb_job_currency', true ) ?: wpbb_jobs_setting( 'currency', 'GBP' );
    $remote = (bool) get_post_meta( $job_id, '_wpbb_job_remote', true );
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'JobPosting',
        'title' => get_the_title( $job_id ),
        'description' => wp_strip_all_tags( get_post_field( 'post_content', $job_id ) ),
        'datePosted' => get_the_date( 'c', $job_id ),
        'employmentType' => $types ? strtoupper( preg_replace( '/[^A-Za-z0-9]+/', '_', $types[0] ) ) : 'FULL_TIME',
        'hiringOrganization' => array(
            '@type' => 'Organization',
            'name' => $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' ),
        ),
    );
    $deadline = get_post_meta( $job_id, '_wpbb_job_deadline', true );
    if ( $deadline ) $schema['validThrough'] = $deadline . 'T23:59:59+00:00';
    if ( $remote ) {
        $schema['jobLocationType'] = 'TELECOMMUTE';
    } elseif ( $location_terms && ! is_wp_error( $location_terms ) ) {
        $schema['jobLocation'] = array( '@type' => 'Place', 'address' => array( '@type' => 'PostalAddress', 'addressLocality' => $location_terms[0]->name ) );
    }
    if ( $min || $max ) {
        $schema['baseSalary'] = array(
            '@type' => 'MonetaryAmount',
            'currency' => $currency,
            'value' => array( '@type' => 'QuantitativeValue', 'minValue' => $min ? (float) $min : (float) $max, 'maxValue' => $max ? (float) $max : (float) $min, 'unitText' => 'YEAR' ),
        );
    }
    echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'wpbb_jobs_job_schema', 30 );


function wpbb_jobs_job_publish_notification( $new_status, $old_status, $post ) {
    if ( ! $post instanceof WP_Post || 'wpbb_job' !== $post->post_type || 'publish' !== $new_status || 'publish' === $old_status ) return;
    $author = get_userdata( $post->post_author );
    if ( ! $author || ! is_email( $author->user_email ) ) return;
    wp_mail(
        $author->user_email,
        sprintf( __( 'Job published: %s', 'wp-bbtheme-child' ), $post->post_title ),
        sprintf( __( 'Your job is now published: %s', 'wp-bbtheme-child' ), get_permalink( $post ) )
    );
}
add_action( 'transition_post_status', 'wpbb_jobs_job_publish_notification', 20, 3 );

function wpbb_jobs_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
    unset( $requested_redirect_to );
    if ( ! $user instanceof WP_User || is_wp_error( $user ) ) return $redirect_to;
    if ( in_array( 'wpbb_job_employer', (array) $user->roles, true ) ) return wpbb_jobs_page_url( 'employer-dashboard' );
    if ( in_array( 'wpbb_job_candidate', (array) $user->roles, true ) ) return wpbb_jobs_page_url( 'candidate-dashboard' );
    return $redirect_to;
}
add_filter( 'login_redirect', 'wpbb_jobs_login_redirect', 20, 3 );

function wpbb_jobs_cv_download_url( $attachment_id ) {
    $attachment_id = absint( $attachment_id );
    if ( ! $attachment_id || ! is_user_logged_in() ) return '';
    return wp_nonce_url(
        add_query_arg( array( 'action' => 'wpbb_jobs_download_cv', 'attachment_id' => $attachment_id ), admin_url( 'admin-post.php' ) ),
        'wpbb_jobs_download_cv_' . $attachment_id,
        'wpbb_jobs_cv_nonce'
    );
}

function wpbb_jobs_user_can_download_cv( $attachment_id ) {
    if ( ! is_user_logged_in() ) return false;
    if ( wpbb_jobs_is_admin_user() ) return true;
    $user_id = get_current_user_id();
    $attachment = get_post( $attachment_id );
    if ( ! $attachment instanceof WP_Post || 'attachment' !== $attachment->post_type ) return false;
    if ( wpbb_jobs_is_candidate() && (int) $attachment->post_author === $user_id ) return true;
    if ( wpbb_jobs_is_employer() ) {
        $resume = get_posts( array(
            'post_type' => 'wpbb_resume', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids',
            'meta_key' => '_wpbb_resume_file_id', 'meta_value' => $attachment_id,
        ) );
        if ( $resume ) return true;
        $applications = get_posts( array(
            'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => 20, 'fields' => 'ids',
            'meta_key' => '_wpbb_application_cv_id', 'meta_value' => $attachment_id,
        ) );
        foreach ( $applications as $application_id ) {
            $job_id = absint( get_post_meta( $application_id, '_wpbb_application_job_id', true ) );
            $job = get_post( $job_id );
            if ( $job instanceof WP_Post && (int) $job->post_author === $user_id ) return true;
        }
    }
    return false;
}

function wpbb_jobs_handle_cv_download() {
    $attachment_id = absint( $_GET['attachment_id'] ?? 0 );
    if ( ! $attachment_id || ! isset( $_GET['wpbb_jobs_cv_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wpbb_jobs_cv_nonce'] ) ), 'wpbb_jobs_download_cv_' . $attachment_id ) || ! wpbb_jobs_user_can_download_cv( $attachment_id ) ) {
        wp_die( esc_html__( 'You are not allowed to download this CV.', 'wp-bbtheme-child' ), '', array( 'response' => 403 ) );
    }
    $file = get_attached_file( $attachment_id );
    if ( ! $file || ! is_readable( $file ) ) wp_die( esc_html__( 'The CV file could not be found.', 'wp-bbtheme-child' ), '', array( 'response' => 404 ) );
    $mime = get_post_mime_type( $attachment_id ) ?: 'application/octet-stream';
    nocache_headers();
    header( 'Content-Type: ' . $mime );
    header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( basename( $file ) ) . '"' );
    header( 'Content-Length: ' . filesize( $file ) );
    header( 'X-Content-Type-Options: nosniff' );
    readfile( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
    exit;
}
add_action( 'admin_post_wpbb_jobs_download_cv', 'wpbb_jobs_handle_cv_download' );

function wpbb_jobs_handle_withdraw_application() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_candidate() ) wp_die( esc_html__( 'Candidate access required.', 'wp-bbtheme-child' ) );
    $application_id = absint( $_POST['application_id'] ?? 0 );
    check_admin_referer( 'wpbb_jobs_withdraw_' . $application_id, 'wpbb_jobs_nonce' );
    if ( (int) get_post_meta( $application_id, '_wpbb_application_candidate_id', true ) !== get_current_user_id() ) wp_die( esc_html__( 'You cannot withdraw this application.', 'wp-bbtheme-child' ) );
    $status = get_post_meta( $application_id, '_wpbb_application_status', true );
    if ( in_array( $status, array( 'hired', 'rejected', 'withdrawn' ), true ) ) {
        wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'candidate-dashboard' ), 'info', __( 'That application can no longer be withdrawn.', 'wp-bbtheme-child' ) );
    }
    update_post_meta( $application_id, '_wpbb_application_status', 'withdrawn' );
    wpbb_jobs_redirect_with_notice( wpbb_jobs_page_url( 'candidate-dashboard' ), 'success', __( 'Application withdrawn.', 'wp-bbtheme-child' ) );
}
add_action( 'admin_post_wpbb_jobs_withdraw_application', 'wpbb_jobs_handle_withdraw_application' );

function wpbb_jobs_privacy_policy_content() {
    if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) return;
    $content = '<p>' . esc_html__( 'The HR Jobs theme can store candidate profiles, CV files, job applications, contact details and application-status history. Employers can access candidate profiles and CVs through authenticated recruitment screens. Administrators should define retention periods that match their recruitment and privacy obligations.', 'wp-bbtheme-child' ) . '</p>';
    wp_add_privacy_policy_content( __( 'HR Jobs recruitment data', 'wp-bbtheme-child' ), wp_kses_post( wpautop( $content ) ) );
}
add_action( 'admin_init', 'wpbb_jobs_privacy_policy_content' );

function wpbb_jobs_privacy_exporters( $exporters ) {
    $exporters['wpbb-jobs'] = array(
        'exporter_friendly_name' => __( 'HR Jobs recruitment data', 'wp-bbtheme-child' ),
        'callback'               => 'wpbb_jobs_privacy_exporter',
    );
    return $exporters;
}
add_filter( 'wp_privacy_personal_data_exporters', 'wpbb_jobs_privacy_exporters' );

function wpbb_jobs_privacy_exporter( $email_address, $page = 1 ) {
    $user = get_user_by( 'email', $email_address );
    $data = array();
    if ( 1 === (int) $page && $user instanceof WP_User ) {
        $resume = wpbb_jobs_get_user_resume( $user->ID );
        if ( $resume instanceof WP_Post ) {
            $data[] = array(
                'group_id'    => 'wpbb-jobs-profile',
                'group_label' => __( 'HR Jobs candidate profile', 'wp-bbtheme-child' ),
                'item_id'     => 'resume-' . $resume->ID,
                'data'        => array(
                    array( 'name' => __( 'Name', 'wp-bbtheme-child' ), 'value' => $resume->post_title ),
                    array( 'name' => __( 'Headline', 'wp-bbtheme-child' ), 'value' => get_post_meta( $resume->ID, '_wpbb_resume_headline', true ) ),
                    array( 'name' => __( 'Profile', 'wp-bbtheme-child' ), 'value' => wp_strip_all_tags( $resume->post_content ) ),
                    array( 'name' => __( 'Phone', 'wp-bbtheme-child' ), 'value' => get_post_meta( $resume->ID, '_wpbb_resume_phone', true ) ),
                    array( 'name' => __( 'Availability', 'wp-bbtheme-child' ), 'value' => get_post_meta( $resume->ID, '_wpbb_resume_availability', true ) ),
                ),
            );
        }
    }
    $applications = get_posts( array(
        'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => 100, 'paged' => max( 1, absint( $page ) ),
        'meta_key' => '_wpbb_application_email', 'meta_value' => sanitize_email( $email_address ),
    ) );
    foreach ( $applications as $application ) {
        $job_id = absint( get_post_meta( $application->ID, '_wpbb_application_job_id', true ) );
        $data[] = array(
            'group_id'    => 'wpbb-jobs-applications',
            'group_label' => __( 'HR Jobs applications', 'wp-bbtheme-child' ),
            'item_id'     => 'application-' . $application->ID,
            'data'        => array(
                array( 'name' => __( 'Job', 'wp-bbtheme-child' ), 'value' => get_the_title( $job_id ) ),
                array( 'name' => __( 'Name', 'wp-bbtheme-child' ), 'value' => get_post_meta( $application->ID, '_wpbb_application_name', true ) ),
                array( 'name' => __( 'Email', 'wp-bbtheme-child' ), 'value' => get_post_meta( $application->ID, '_wpbb_application_email', true ) ),
                array( 'name' => __( 'Phone', 'wp-bbtheme-child' ), 'value' => get_post_meta( $application->ID, '_wpbb_application_phone', true ) ),
                array( 'name' => __( 'Message', 'wp-bbtheme-child' ), 'value' => get_post_meta( $application->ID, '_wpbb_application_message', true ) ),
                array( 'name' => __( 'Status', 'wp-bbtheme-child' ), 'value' => get_post_meta( $application->ID, '_wpbb_application_status', true ) ),
            ),
        );
    }
    return array( 'data' => $data, 'done' => count( $applications ) < 100 );
}

function wpbb_jobs_privacy_erasers( $erasers ) {
    $erasers['wpbb-jobs'] = array(
        'eraser_friendly_name' => __( 'HR Jobs recruitment data', 'wp-bbtheme-child' ),
        'callback'             => 'wpbb_jobs_privacy_eraser',
    );
    return $erasers;
}
add_filter( 'wp_privacy_personal_data_erasers', 'wpbb_jobs_privacy_erasers' );

function wpbb_jobs_privacy_eraser( $email_address, $page = 1 ) {
    unset( $page );
    $items_removed = false;
    $user = get_user_by( 'email', $email_address );
    if ( 1 === (int) $page && $user instanceof WP_User ) {
        $resume = wpbb_jobs_get_user_resume( $user->ID );
        if ( $resume instanceof WP_Post ) {
            $file_id = absint( get_post_meta( $resume->ID, '_wpbb_resume_file_id', true ) );
            if ( $file_id ) wp_delete_attachment( $file_id, true );
            wp_delete_post( $resume->ID, true );
            $items_removed = true;
        }
    }
    $applications = get_posts( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => -1, 'meta_key' => '_wpbb_application_email', 'meta_value' => sanitize_email( $email_address ) ) );
    foreach ( $applications as $application ) {
        $file_id = absint( get_post_meta( $application->ID, '_wpbb_application_cv_id', true ) );
        $resume_id = absint( get_post_meta( $application->ID, '_wpbb_application_resume_id', true ) );
        if ( $file_id && ( ! $resume_id || $file_id !== absint( get_post_meta( $resume_id, '_wpbb_resume_file_id', true ) ) ) ) wp_delete_attachment( $file_id, true );
        update_post_meta( $application->ID, '_wpbb_application_name', __( 'Erased candidate', 'wp-bbtheme-child' ) );
        update_post_meta( $application->ID, '_wpbb_application_email', '' );
        update_post_meta( $application->ID, '_wpbb_application_phone', '' );
        update_post_meta( $application->ID, '_wpbb_application_message', '' );
        update_post_meta( $application->ID, '_wpbb_application_candidate_id', 0 );
        update_post_meta( $application->ID, '_wpbb_application_resume_id', 0 );
        update_post_meta( $application->ID, '_wpbb_application_cv_id', 0 );
        $items_removed = true;
    }
    return array(
        'items_removed'  => $items_removed,
        'items_retained' => false,
        'messages'       => array(),
        'done'           => true,
    );
}
