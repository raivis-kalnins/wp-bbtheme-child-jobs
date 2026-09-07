<?php
/** Clean WordPress admin for the integrated HR / Jobs engine. */

defined( 'ABSPATH' ) || exit;

function wpbb_jobs_admin_menu() {
    add_menu_page(
        __( 'HR Jobs', 'wp-bbtheme-child' ),
        __( 'HR Jobs', 'wp-bbtheme-child' ),
        'edit_wpbb_jobs',
        'wpbb-jobs',
        'wpbb_jobs_admin_dashboard_page',
        'dashicons-businessperson',
        25
    );
    add_submenu_page( 'wpbb-jobs', __( 'HR Jobs Dashboard', 'wp-bbtheme-child' ), __( 'Dashboard', 'wp-bbtheme-child' ), 'edit_wpbb_jobs', 'wpbb-jobs', 'wpbb_jobs_admin_dashboard_page' );
    add_submenu_page( 'wpbb-jobs', __( 'HR Jobs Setup', 'wp-bbtheme-child' ), __( 'Setup & Demo', 'wp-bbtheme-child' ), 'manage_options', 'wpbb-jobs-setup', 'wpbb_jobs_setup_admin_page' );
    add_submenu_page( 'wpbb-jobs', __( 'HR Jobs Settings', 'wp-bbtheme-child' ), __( 'Settings', 'wp-bbtheme-child' ), 'manage_options', 'wpbb-jobs-settings', 'wpbb_jobs_settings_page' );
    add_theme_page( __( 'HR Jobs Setup', 'wp-bbtheme-child' ), __( 'HR Jobs Setup', 'wp-bbtheme-child' ), 'manage_options', 'wpbb-jobs-setup-theme', 'wpbb_jobs_setup_admin_page' );
}
add_action( 'admin_menu', 'wpbb_jobs_admin_menu', 5 );

function wpbb_jobs_admin_assets( $hook ) {
    if ( false === strpos( $hook, 'wpbb-jobs' ) && ! in_array( get_post_type(), array( 'wpbb_job', 'wpbb_company', 'wpbb_resume', 'wpbb_application' ), true ) ) return;
    wp_enqueue_style( 'wpbb-jobs-admin', get_stylesheet_directory_uri() . '/assets/jobs-admin.css', array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'admin_enqueue_scripts', 'wpbb_jobs_admin_assets' );

function wpbb_jobs_admin_counts() {
    $counts = array();
    foreach ( array( 'wpbb_job' => 'jobs', 'wpbb_company' => 'companies', 'wpbb_resume' => 'resumes', 'wpbb_application' => 'applications' ) as $post_type => $key ) {
        $obj = wp_count_posts( $post_type );
        $counts[ $key ] = (int) ( $obj->publish ?? 0 ) + (int) ( $obj->pending ?? 0 ) + (int) ( $obj->draft ?? 0 ) + (int) ( $obj->private ?? 0 );
    }
    return $counts;
}

function wpbb_jobs_admin_dashboard_page() {
    if ( ! current_user_can( 'edit_wpbb_jobs' ) ) return;
    $counts = wpbb_jobs_admin_counts();
    $pending_jobs = (int) ( wp_count_posts( 'wpbb_job' )->pending ?? 0 );
    $new_apps = count( get_posts( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_wpbb_application_status', 'meta_value' => 'new' ) ) );
    ?>
    <div class="wrap wpbb-jobs-admin">
        <div class="wpbb-jobs-admin__hero"><div><p class="wpbb-jobs-admin__eyebrow"><?php esc_html_e( 'WP BBTheme · integrated recruitment', 'wp-bbtheme-child' ); ?></p><h1><?php esc_html_e( 'HR Jobs', 'wp-bbtheme-child' ); ?></h1><p><?php esc_html_e( 'Jobs, employer profiles, candidate CVs and applications are managed directly by this child theme using native WordPress content.', 'wp-bbtheme-child' ); ?></p></div><span class="wpbb-jobs-admin__version"><?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></span></div>
        <div class="wpbb-jobs-admin__stats">
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpbb_job' ) ); ?>"><strong><?php echo esc_html( $counts['jobs'] ); ?></strong><span><?php esc_html_e( 'Jobs', 'wp-bbtheme-child' ); ?></span></a>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpbb_company' ) ); ?>"><strong><?php echo esc_html( $counts['companies'] ); ?></strong><span><?php esc_html_e( 'Companies', 'wp-bbtheme-child' ); ?></span></a>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpbb_resume' ) ); ?>"><strong><?php echo esc_html( $counts['resumes'] ); ?></strong><span><?php esc_html_e( 'Candidate profiles', 'wp-bbtheme-child' ); ?></span></a>
            <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpbb_application' ) ); ?>"><strong><?php echo esc_html( $counts['applications'] ); ?></strong><span><?php esc_html_e( 'Applications', 'wp-bbtheme-child' ); ?></span></a>
        </div>
        <div class="wpbb-jobs-admin__grid">
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'Review queue', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status <?php echo $pending_jobs || $new_apps ? 'is-action' : 'is-ready'; ?>"><?php echo esc_html( $pending_jobs + $new_apps ); ?></span></div><p><?php echo esc_html( sprintf( __( '%1$d jobs await approval and %2$d applications are new.', 'wp-bbtheme-child' ), $pending_jobs, $new_apps ) ); ?></p><p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_status=pending&post_type=wpbb_job' ) ); ?>"><?php esc_html_e( 'Review jobs', 'wp-bbtheme-child' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=wpbb_application' ) ); ?>"><?php esc_html_e( 'Review applications', 'wp-bbtheme-child' ); ?></a></p></section>
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'Frontend setup', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status is-ready"><?php esc_html_e( 'Integrated', 'wp-bbtheme-child' ); ?></span></div><p><?php esc_html_e( 'Create or refresh the theme-managed Jobs pages and optional rich sample companies/jobs without installing another recruitment plugin.', 'wp-bbtheme-child' ); ?></p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=wpbb-jobs-setup' ) ); ?>"><?php esc_html_e( 'Open setup', 'wp-bbtheme-child' ); ?></a></section>
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'BBuilder & Gutenberg', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status is-ready"><?php esc_html_e( 'Native blocks', 'wp-bbtheme-child' ); ?></span></div><p><?php esc_html_e( 'Use HR Jobs Search, HR Jobs Listings, HR Hiring Companies and HR Candidate Profiles as dynamic Gutenberg blocks inside BBuilder Bootstrap sections.', 'wp-bbtheme-child' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>"><?php esc_html_e( 'Edit pages', 'wp-bbtheme-child' ); ?></a></section>
        </div>
    </div>
    <?php
}

function wpbb_jobs_setup_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $notice = get_transient( 'wpbb_jobs_setup_notice_' . get_current_user_id() );
    if ( $notice ) delete_transient( 'wpbb_jobs_setup_notice_' . get_current_user_id() );
    $pages = array( 'jobs', 'hiring-companies', 'find-candidates', 'candidate-dashboard', 'employer-dashboard', 'post-a-job', 'create-resume', 'login-register' );
    $ready = 0;
    foreach ( $pages as $slug ) if ( get_page_by_path( $slug, OBJECT, 'page' ) ) $ready++;
    ?>
    <div class="wrap wpbb-jobs-admin">
        <div class="wpbb-jobs-admin__hero"><div><p class="wpbb-jobs-admin__eyebrow"><?php esc_html_e( 'WP BBTheme · HR sector', 'wp-bbtheme-child' ); ?></p><h1><?php esc_html_e( 'HR Jobs Setup', 'wp-bbtheme-child' ); ?></h1><p><?php esc_html_e( 'The recruitment engine is built into the Jobs child theme. No WP Job Portal installation is required.', 'wp-bbtheme-child' ); ?></p></div><span class="wpbb-jobs-admin__version"><?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></span></div>
        <?php if ( is_array( $notice ) ) : ?><div class="notice notice-success is-dismissible"><p><?php echo esc_html( $notice['message'] ?? '' ); ?></p></div><?php endif; ?>
        <div class="wpbb-jobs-admin__grid">
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'Recruitment engine', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status is-ready"><?php esc_html_e( 'Built in', 'wp-bbtheme-child' ); ?></span></div><p><?php esc_html_e( 'Native WordPress jobs, companies, candidate profiles, applications, roles, search, dashboards and email notifications are active with the child theme.', 'wp-bbtheme-child' ); ?></p></section>
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'Portal pages', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status is-ready"><?php echo esc_html( $ready . '/' . count( $pages ) ); ?></span></div><p><?php esc_html_e( 'Only pages previously created by this Jobs theme are refreshed. Same-slug pages you created yourself stay untouched.', 'wp-bbtheme-child' ); ?></p><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="wpbb_jobs_setup_pages" /><?php wp_nonce_field( 'wpbb_jobs_setup_pages' ); ?><?php submit_button( __( 'Create / refresh Jobs pages', 'wp-bbtheme-child' ), 'primary', 'submit', false ); ?></form></section>
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'Demo recruitment data', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status is-ready"><?php esc_html_e( 'Optional', 'wp-bbtheme-child' ); ?></span></div><p><?php esc_html_e( 'Starter Setup seeds this automatically. Use this repair action to restore the rich HR demo: employers, jobs, candidates, skills, locations and applications across multiple workflow states. Existing matching records are updated instead of duplicated.', 'wp-bbtheme-child' ); ?></p><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="wpbb_jobs_seed_demo" /><?php wp_nonce_field( 'wpbb_jobs_seed_demo' ); ?><?php submit_button( __( 'Add / repair HR demo data', 'wp-bbtheme-child' ), 'secondary', 'submit', false ); ?></form></section>
            <section class="wpbb-jobs-admin__card"><div class="wpbb-jobs-admin__card-head"><h2><?php esc_html_e( 'Sector demo', 'wp-bbtheme-child' ); ?></h2><span class="wpbb-jobs-admin__status is-ready"><?php esc_html_e( 'BBuilder ready', 'wp-bbtheme-child' ); ?></span></div><p><?php esc_html_e( 'Use the parent Starter Setup / BBuilder Demo tools for the rich HR landing pages and navigation. The Jobs engine pages remain normal editable WordPress pages.', 'wp-bbtheme-child' ); ?></p><a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=wp-theme-starter-setup' ) ); ?>"><?php esc_html_e( 'Open Starter Setup', 'wp-bbtheme-child' ); ?></a></section>
        </div>
        <section class="wpbb-jobs-admin__next"><h2><?php esc_html_e( 'Recommended first setup', 'wp-bbtheme-child' ); ?></h2><ol><li><?php esc_html_e( 'Activate WP BBuilder and this Jobs child theme.', 'wp-bbtheme-child' ); ?></li><li><?php esc_html_e( 'Create / refresh the Jobs pages above.', 'wp-bbtheme-child' ); ?></li><li><?php esc_html_e( 'Add the optional HR demo data for a populated marketplace.', 'wp-bbtheme-child' ); ?></li><li><?php esc_html_e( 'Review HR Jobs → Settings for approvals, guest applications, currency and notification email.', 'wp-bbtheme-child' ); ?></li><li><?php esc_html_e( 'Test candidate registration/application and employer job-posting flows on staging.', 'wp-bbtheme-child' ); ?></li></ol></section>
    </div>
    <?php
}

function wpbb_jobs_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $settings = wpbb_jobs_settings();
    ?>
    <div class="wrap wpbb-jobs-admin"><div class="wpbb-jobs-admin__hero"><div><p class="wpbb-jobs-admin__eyebrow"><?php esc_html_e( 'Recruitment controls', 'wp-bbtheme-child' ); ?></p><h1><?php esc_html_e( 'HR Jobs Settings', 'wp-bbtheme-child' ); ?></h1><p><?php esc_html_e( 'Keep the defaults simple and safe, then adapt the workflow to the organisation running the careers site or job marketplace.', 'wp-bbtheme-child' ); ?></p></div></div>
        <form class="wpbb-jobs-admin__settings" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="wpbb_jobs_save_settings" /><?php wp_nonce_field( 'wpbb_jobs_save_settings' ); ?>
            <section class="wpbb-jobs-admin__card"><h2><?php esc_html_e( 'Publishing & applications', 'wp-bbtheme-child' ); ?></h2><label><input type="checkbox" name="job_approval" value="1" <?php checked( ! empty( $settings['job_approval'] ) ); ?> /> <?php esc_html_e( 'Require administrator approval before employer-submitted jobs are published', 'wp-bbtheme-child' ); ?></label><label><input type="checkbox" name="company_approval" value="1" <?php checked( ! empty( $settings['company_approval'] ) ); ?> /> <?php esc_html_e( 'Require administrator approval for new company profiles', 'wp-bbtheme-child' ); ?></label><label><input type="checkbox" name="guest_apply" value="1" <?php checked( ! empty( $settings['guest_apply'] ) ); ?> /> <?php esc_html_e( 'Allow visitors to quick-apply without creating a candidate account', 'wp-bbtheme-child' ); ?></label></section>
            <section class="wpbb-jobs-admin__card"><h2><?php esc_html_e( 'Defaults', 'wp-bbtheme-child' ); ?></h2><label><?php esc_html_e( 'Salary currency', 'wp-bbtheme-child' ); ?><input type="text" name="currency" maxlength="3" value="<?php echo esc_attr( $settings['currency'] ); ?>" /></label><label><?php esc_html_e( 'Recruitment notification email', 'wp-bbtheme-child' ); ?><input type="email" class="regular-text" name="admin_email" value="<?php echo esc_attr( $settings['admin_email'] ); ?>" /></label></section>
            <?php submit_button( __( 'Save HR Jobs settings', 'wp-bbtheme-child' ) ); ?>
        </form>
    </div>
    <?php
}

function wpbb_jobs_handle_save_settings() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You are not allowed to change these settings.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_save_settings' );
    update_option( 'wpbb_jobs_settings', array(
        'job_approval'     => empty( $_POST['job_approval'] ) ? 0 : 1,
        'company_approval' => empty( $_POST['company_approval'] ) ? 0 : 1,
        'guest_apply'      => empty( $_POST['guest_apply'] ) ? 0 : 1,
        'currency'         => strtoupper( substr( sanitize_text_field( wp_unslash( $_POST['currency'] ?? 'GBP' ) ), 0, 3 ) ),
        'admin_email'      => sanitize_email( wp_unslash( $_POST['admin_email'] ?? get_option( 'admin_email' ) ) ),
    ) );
    wp_safe_redirect( add_query_arg( 'settings-updated', 'true', admin_url( 'admin.php?page=wpbb-jobs-settings' ) ) );
    exit;
}
add_action( 'admin_post_wpbb_jobs_save_settings', 'wpbb_jobs_handle_save_settings' );

function wpbb_jobs_handle_setup_pages() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You are not allowed to perform this action.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_setup_pages' );
    $result = wpbb_jobs_create_portal_pages();
    $message = sprintf( __( 'Jobs pages checked: %1$d created, %2$d refreshed, %3$d existing unmanaged pages left untouched.', 'wp-bbtheme-child' ), (int) $result['created'], (int) $result['updated'], (int) $result['skipped'] );
    set_transient( 'wpbb_jobs_setup_notice_' . get_current_user_id(), array( 'message' => $message ), MINUTE_IN_SECONDS );
    wp_safe_redirect( admin_url( 'admin.php?page=wpbb-jobs-setup' ) );
    exit;
}
add_action( 'admin_post_wpbb_jobs_setup_pages', 'wpbb_jobs_handle_setup_pages' );

function wpbb_jobs_handle_seed_demo() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( esc_html__( 'You are not allowed to seed demo data.', 'wp-bbtheme-child' ) );
    check_admin_referer( 'wpbb_jobs_seed_demo' );
    $result = wpbb_jobs_seed_rich_demo_data();
    $message = sprintf( __( 'HR demo data is ready: %1$d companies, %2$d new/repaired jobs in this run, %3$d candidate profiles and %4$d new/repaired sample applications in this run.', 'wp-bbtheme-child' ), (int) $result['companies'], (int) $result['jobs_created'], (int) $result['resumes'], (int) $result['applications_created'] );
    set_transient( 'wpbb_jobs_setup_notice_' . get_current_user_id(), array( 'message' => $message ), MINUTE_IN_SECONDS );
    wp_safe_redirect( admin_url( 'admin.php?page=wpbb-jobs-setup' ) );
    exit;
}
add_action( 'admin_post_wpbb_jobs_seed_demo', 'wpbb_jobs_handle_seed_demo' );

function wpbb_jobs_add_meta_boxes() {
    add_meta_box( 'wpbb-job-details', __( 'Job details', 'wp-bbtheme-child' ), 'wpbb_jobs_job_meta_box', 'wpbb_job', 'normal', 'high' );
    add_meta_box( 'wpbb-company-details', __( 'Company details', 'wp-bbtheme-child' ), 'wpbb_jobs_company_meta_box', 'wpbb_company', 'normal', 'default' );
    add_meta_box( 'wpbb-resume-details', __( 'Candidate details', 'wp-bbtheme-child' ), 'wpbb_jobs_resume_meta_box', 'wpbb_resume', 'normal', 'default' );
    add_meta_box( 'wpbb-application-details', __( 'Application details', 'wp-bbtheme-child' ), 'wpbb_jobs_application_meta_box', 'wpbb_application', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'wpbb_jobs_add_meta_boxes' );

function wpbb_jobs_job_meta_box( $post ) {
    wp_nonce_field( 'wpbb_jobs_admin_meta', 'wpbb_jobs_admin_nonce' );
    $companies = get_posts( array( 'post_type' => 'wpbb_company', 'post_status' => array( 'publish', 'pending', 'draft' ), 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
    $company_id = absint( get_post_meta( $post->ID, '_wpbb_job_company_id', true ) );
    ?>
    <div class="wpbb-jobs-meta-grid"><label><?php esc_html_e( 'Company', 'wp-bbtheme-child' ); ?><select name="wpbb_job_company_id"><option value=""><?php esc_html_e( 'Choose company', 'wp-bbtheme-child' ); ?></option><?php foreach ( $companies as $company ) : ?><option value="<?php echo esc_attr( $company->ID ); ?>" <?php selected( $company_id, $company->ID ); ?>><?php echo esc_html( $company->post_title ); ?></option><?php endforeach; ?></select></label><label><?php esc_html_e( 'Salary from', 'wp-bbtheme-child' ); ?><input type="number" name="wpbb_job_salary_min" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_job_salary_min', true ) ); ?>" /></label><label><?php esc_html_e( 'Salary to', 'wp-bbtheme-child' ); ?><input type="number" name="wpbb_job_salary_max" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_job_salary_max', true ) ); ?>" /></label><label><?php esc_html_e( 'Currency', 'wp-bbtheme-child' ); ?><input maxlength="3" name="wpbb_job_currency" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_job_currency', true ) ?: wpbb_jobs_setting( 'currency', 'GBP' ) ); ?>" /></label><label><?php esc_html_e( 'Application deadline', 'wp-bbtheme-child' ); ?><input type="date" name="wpbb_job_deadline" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_job_deadline', true ) ); ?>" /></label><label><?php esc_html_e( 'Job state', 'wp-bbtheme-child' ); ?><select name="wpbb_job_status"><option value="open" <?php selected( get_post_meta( $post->ID, '_wpbb_job_status', true ), 'open' ); ?>><?php esc_html_e( 'Open', 'wp-bbtheme-child' ); ?></option><option value="closed" <?php selected( get_post_meta( $post->ID, '_wpbb_job_status', true ), 'closed' ); ?>><?php esc_html_e( 'Closed', 'wp-bbtheme-child' ); ?></option></select></label></div>
    <p><label><input type="checkbox" name="wpbb_job_remote" value="1" <?php checked( get_post_meta( $post->ID, '_wpbb_job_remote', true ) ); ?> /> <?php esc_html_e( 'Remote / hybrid', 'wp-bbtheme-child' ); ?></label> &nbsp; <label><input type="checkbox" name="wpbb_job_featured" value="1" <?php checked( get_post_meta( $post->ID, '_wpbb_job_featured', true ) ); ?> /> <?php esc_html_e( 'Featured job', 'wp-bbtheme-child' ); ?></label></p>
    <?php
}

function wpbb_jobs_company_meta_box( $post ) {
    wp_nonce_field( 'wpbb_jobs_admin_meta', 'wpbb_jobs_admin_nonce' );
    ?><div class="wpbb-jobs-meta-grid"><label><?php esc_html_e( 'Website', 'wp-bbtheme-child' ); ?><input type="url" name="wpbb_company_website" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_company_website', true ) ); ?>" /></label><label><?php esc_html_e( 'Hiring email', 'wp-bbtheme-child' ); ?><input type="email" name="wpbb_company_email" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_company_email', true ) ); ?>" /></label><label><?php esc_html_e( 'Phone', 'wp-bbtheme-child' ); ?><input name="wpbb_company_phone" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_company_phone', true ) ); ?>" /></label></div><?php
}

function wpbb_jobs_resume_meta_box( $post ) {
    wp_nonce_field( 'wpbb_jobs_admin_meta', 'wpbb_jobs_admin_nonce' );
    $file_id = absint( get_post_meta( $post->ID, '_wpbb_resume_file_id', true ) );
    ?><div class="wpbb-jobs-meta-grid"><label><?php esc_html_e( 'Professional headline', 'wp-bbtheme-child' ); ?><input name="wpbb_resume_headline" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_resume_headline', true ) ); ?>" /></label><label><?php esc_html_e( 'Phone', 'wp-bbtheme-child' ); ?><input name="wpbb_resume_phone" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_resume_phone', true ) ); ?>" /></label><label><?php esc_html_e( 'Availability', 'wp-bbtheme-child' ); ?><input name="wpbb_resume_availability" value="<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_resume_availability', true ) ); ?>" /></label></div><?php if ( $file_id ) : ?><p><a class="button" href="<?php echo esc_url( wpbb_jobs_cv_download_url( $file_id ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open attached CV', 'wp-bbtheme-child' ); ?></a></p><?php endif;
}

function wpbb_jobs_application_meta_box( $post ) {
    wp_nonce_field( 'wpbb_jobs_admin_meta', 'wpbb_jobs_admin_nonce' );
    $job_id = absint( get_post_meta( $post->ID, '_wpbb_application_job_id', true ) );
    $file_id = absint( get_post_meta( $post->ID, '_wpbb_application_cv_id', true ) );
    $status = get_post_meta( $post->ID, '_wpbb_application_status', true ) ?: 'new';
    ?><div class="wpbb-jobs-application-meta"><p><strong><?php esc_html_e( 'Job:', 'wp-bbtheme-child' ); ?></strong> <?php echo esc_html( get_the_title( $job_id ) ); ?></p><p><strong><?php esc_html_e( 'Candidate:', 'wp-bbtheme-child' ); ?></strong> <?php echo esc_html( get_post_meta( $post->ID, '_wpbb_application_name', true ) ); ?> · <a href="mailto:<?php echo esc_attr( get_post_meta( $post->ID, '_wpbb_application_email', true ) ); ?>"><?php echo esc_html( get_post_meta( $post->ID, '_wpbb_application_email', true ) ); ?></a></p><p><strong><?php esc_html_e( 'Phone:', 'wp-bbtheme-child' ); ?></strong> <?php echo esc_html( get_post_meta( $post->ID, '_wpbb_application_phone', true ) ); ?></p><p><strong><?php esc_html_e( 'Message:', 'wp-bbtheme-child' ); ?></strong><br><?php echo nl2br( esc_html( get_post_meta( $post->ID, '_wpbb_application_message', true ) ) ); ?></p><p><label><strong><?php esc_html_e( 'Status', 'wp-bbtheme-child' ); ?></strong><select name="wpbb_application_status"><?php foreach ( wpbb_jobs_application_statuses() as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></label></p><?php if ( $file_id ) : ?><p><a class="button button-primary" href="<?php echo esc_url( wpbb_jobs_cv_download_url( $file_id ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Open CV', 'wp-bbtheme-child' ); ?></a></p><?php endif; ?></div><?php
}

function wpbb_jobs_save_admin_meta( $post_id, $post ) {
    if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, array( 'wpbb_job', 'wpbb_company', 'wpbb_resume', 'wpbb_application' ), true ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['wpbb_jobs_admin_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wpbb_jobs_admin_nonce'] ) ), 'wpbb_jobs_admin_meta' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( 'wpbb_job' === $post->post_type ) {
        update_post_meta( $post_id, '_wpbb_job_company_id', absint( $_POST['wpbb_job_company_id'] ?? 0 ) );
        update_post_meta( $post_id, '_wpbb_job_salary_min', (float) ( $_POST['wpbb_job_salary_min'] ?? 0 ) );
        update_post_meta( $post_id, '_wpbb_job_salary_max', (float) ( $_POST['wpbb_job_salary_max'] ?? 0 ) );
        update_post_meta( $post_id, '_wpbb_job_currency', strtoupper( substr( sanitize_text_field( wp_unslash( $_POST['wpbb_job_currency'] ?? '' ) ), 0, 3 ) ) );
        update_post_meta( $post_id, '_wpbb_job_deadline', sanitize_text_field( wp_unslash( $_POST['wpbb_job_deadline'] ?? '' ) ) );
        update_post_meta( $post_id, '_wpbb_job_status', in_array( $_POST['wpbb_job_status'] ?? '', array( 'open', 'closed' ), true ) ? sanitize_key( $_POST['wpbb_job_status'] ) : 'open' );
        update_post_meta( $post_id, '_wpbb_job_remote', empty( $_POST['wpbb_job_remote'] ) ? 0 : 1 );
        update_post_meta( $post_id, '_wpbb_job_featured', empty( $_POST['wpbb_job_featured'] ) ? 0 : 1 );
    } elseif ( 'wpbb_company' === $post->post_type ) {
        update_post_meta( $post_id, '_wpbb_company_website', esc_url_raw( wp_unslash( $_POST['wpbb_company_website'] ?? '' ) ) );
        update_post_meta( $post_id, '_wpbb_company_email', sanitize_email( wp_unslash( $_POST['wpbb_company_email'] ?? '' ) ) );
        update_post_meta( $post_id, '_wpbb_company_phone', sanitize_text_field( wp_unslash( $_POST['wpbb_company_phone'] ?? '' ) ) );
    } elseif ( 'wpbb_resume' === $post->post_type ) {
        update_post_meta( $post_id, '_wpbb_resume_headline', sanitize_text_field( wp_unslash( $_POST['wpbb_resume_headline'] ?? '' ) ) );
        update_post_meta( $post_id, '_wpbb_resume_phone', sanitize_text_field( wp_unslash( $_POST['wpbb_resume_phone'] ?? '' ) ) );
        update_post_meta( $post_id, '_wpbb_resume_availability', sanitize_text_field( wp_unslash( $_POST['wpbb_resume_availability'] ?? '' ) ) );
    } elseif ( 'wpbb_application' === $post->post_type ) {
        $status = sanitize_key( wp_unslash( $_POST['wpbb_application_status'] ?? 'new' ) );
        $old_status = get_post_meta( $post_id, '_wpbb_application_status', true ) ?: 'new';
        if ( isset( wpbb_jobs_application_statuses()[ $status ] ) ) {
            update_post_meta( $post_id, '_wpbb_application_status', $status );
            if ( $status !== $old_status ) wpbb_jobs_send_status_email( $post_id, $status );
        }
    }
}
add_action( 'save_post', 'wpbb_jobs_save_admin_meta', 20, 2 );

function wpbb_jobs_job_columns( $columns ) {
    $new = array();
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;
        if ( 'title' === $key ) {
            $new['company'] = __( 'Company', 'wp-bbtheme-child' );
            $new['salary'] = __( 'Salary', 'wp-bbtheme-child' );
            $new['applications'] = __( 'Applications', 'wp-bbtheme-child' );
        }
    }
    return $new;
}
add_filter( 'manage_wpbb_job_posts_columns', 'wpbb_jobs_job_columns' );

function wpbb_jobs_job_column_content( $column, $post_id ) {
    if ( 'company' === $column ) { $company = wpbb_jobs_company_for_job( $post_id ); echo esc_html( $company instanceof WP_Post ? $company->post_title : '—' ); }
    if ( 'salary' === $column ) echo esc_html( wpbb_jobs_salary_label( $post_id ) ?: '—' );
    if ( 'applications' === $column ) echo esc_html( count( get_posts( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_wpbb_application_job_id', 'meta_value' => $post_id ) ) ) );
}
add_action( 'manage_wpbb_job_posts_custom_column', 'wpbb_jobs_job_column_content', 10, 2 );

function wpbb_jobs_application_columns( $columns ) {
    return array( 'cb' => $columns['cb'], 'title' => __( 'Candidate / Job', 'wp-bbtheme-child' ), 'job' => __( 'Job', 'wp-bbtheme-child' ), 'candidate' => __( 'Candidate', 'wp-bbtheme-child' ), 'status' => __( 'Status', 'wp-bbtheme-child' ), 'date' => $columns['date'] );
}
add_filter( 'manage_wpbb_application_posts_columns', 'wpbb_jobs_application_columns' );
function wpbb_jobs_application_column_content( $column, $post_id ) {
    if ( 'job' === $column ) echo esc_html( get_the_title( absint( get_post_meta( $post_id, '_wpbb_application_job_id', true ) ) ) );
    if ( 'candidate' === $column ) echo esc_html( get_post_meta( $post_id, '_wpbb_application_name', true ) . ' · ' . get_post_meta( $post_id, '_wpbb_application_email', true ) );
    if ( 'status' === $column ) { $s = get_post_meta( $post_id, '_wpbb_application_status', true ) ?: 'new'; $statuses = wpbb_jobs_application_statuses(); echo '<span class="wpbb-jobs-admin-status is-' . esc_attr( $s ) . '">' . esc_html( $statuses[ $s ] ?? ucfirst( $s ) ) . '</span>'; }
}
add_action( 'manage_wpbb_application_posts_custom_column', 'wpbb_jobs_application_column_content', 10, 2 );
