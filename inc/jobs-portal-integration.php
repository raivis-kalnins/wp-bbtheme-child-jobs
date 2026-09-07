<?php
/**
 * BBuilder/Gutenberg integration for the theme-integrated HR Jobs engine.
 */

defined( 'ABSPATH' ) || exit;

function wpbb_jobs_block( $name, $attrs = array() ) {
    return wpbb_child_v62_block( 'wpbb-jobs/' . $name, $attrs, '', true );
}

function wpbb_jobs_shortcode_block( $shortcode ) {
    return '<!-- wp:shortcode -->' . $shortcode . '<!-- /wp:shortcode -->';
}

function wpbb_jobs_section( $inner, $class = '' ) {
    if ( function_exists( 'wpbb_child_v67_section' ) ) {
        return wpbb_child_v67_section( trim( 'wpbb-jobs-section ' . $class ), $inner, 'wpbb-jobs-section-shell' );
    }
    return wpbb_child_v62_block(
        'wpbb/bootstrap-div',
        array( 'containerClass' => 'container-fluid', 'utilityClasses' => trim( 'wpbb-jobs-section ' . $class ) ),
        wpbb_child_v62_block( 'wpbb/bootstrap-div', array( 'containerClass' => 'container' ), $inner )
    );
}

function wpbb_jobs_home_search( $content, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return $content;
    $inner  = wpbb_child_v62_paragraph( __( 'Find your next move', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $inner .= wpbb_child_v62_heading( __( 'Search by role, discipline and location.', 'wp-bbtheme-child' ), 2, 'wpbb-jobs-search-title' );
    $inner .= wpbb_jobs_block( 'search', array( 'compact' => true ) );
    return $content . wpbb_jobs_section( $inner, 'wpbb-jobs-search-band' );
}
add_filter( 'wp_theme_demo_after_hero_sections', 'wpbb_jobs_home_search', 20, 2 );

function wpbb_jobs_home_extra_sections( $content, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return $content;

    $latest  = wpbb_child_v62_paragraph( __( 'Current opportunities', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $latest .= wpbb_child_v62_heading( __( 'Fresh roles worth a closer look.', 'wp-bbtheme-child' ), 2 );
    $latest .= wpbb_jobs_block( 'list', array( 'perPage' => 8, 'columns' => 2, 'featured' => false ) );
    $latest .= wpbb_child_v62_paragraph( '<a class="wpbb-jobs-text-link" href="' . esc_url( wpbb_jobs_page_url( 'jobs' ) ) . '">' . esc_html__( 'View all jobs', 'wp-bbtheme-child' ) . ' →</a>' );

    $companies  = wpbb_child_v62_paragraph( __( 'Employers', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $companies .= wpbb_child_v62_heading( __( 'Meet companies that are actively hiring.', 'wp-bbtheme-child' ), 2 );
    $companies .= wpbb_jobs_block( 'companies', array( 'perPage' => 6, 'columns' => 3 ) );

    $candidate_card = wpbb_child_v62_icon_card(
        __( 'I am looking for work', 'wp-bbtheme-child' ),
        __( 'Search roles, build your candidate profile and keep applications organised from your dashboard.', 'wp-bbtheme-child' ),
        'wpbb-jobs-path-card',
        __( 'Candidate dashboard', 'wp-bbtheme-child' ),
        wpbb_jobs_page_url( 'candidate-dashboard' )
    );
    $employer_card = wpbb_child_v62_icon_card(
        __( 'I am hiring', 'wp-bbtheme-child' ),
        __( 'Create an employer profile, publish opportunities and manage candidate applications in one recruitment workspace.', 'wp-bbtheme-child' ),
        'wpbb-jobs-path-card',
        __( 'Employer dashboard', 'wp-bbtheme-child' ),
        wpbb_jobs_page_url( 'employer-dashboard' )
    );
    $paths_intro  = wpbb_child_v62_paragraph( __( 'Choose your route', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $paths_intro .= wpbb_child_v62_heading( __( 'Start with the tools that match your role.', 'wp-bbtheme-child' ), 2, 'wpbb-jobs-paths-title' );
    $paths = $paths_intro . wpbb_child_v62_block( 'wpbb/row', array( 'gutterX' => 'gx-4', 'gutterY' => 'gy-4', 'customClasses' => 'wpbb-jobs-paths-row' ),
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 6, 'customClasses' => 'd-flex' ), $candidate_card ) .
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 6, 'customClasses' => 'd-flex' ), $employer_card )
    );

    return $content
        . wpbb_jobs_section( $latest, 'wpbb-jobs-latest-section' )
        . wpbb_jobs_section( $companies, 'wpbb-jobs-companies-section bg-light' )
        . wpbb_jobs_section( $paths, 'wpbb-jobs-paths-section' );
}
add_filter( 'wp_theme_demo_extra_home_sections', 'wpbb_jobs_home_extra_sections', 20, 2 );


/**
 * Final Jobs homepage safety pass.
 *
 * Older imported pages can contain two self-closing Icon Cards with no attrs in
 * the candidate/employer pathway section. Populate those placeholders so both
 * frontend and editor render a real recruitment choice instead of default cards.
 */
function wpbb_jobs_repair_canonical_home_content( $content, $profile = array() ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' || ! is_string( $content ) ) return $content;

    $cards = array(
        wpbb_child_v62_icon_card(
            __( 'I am looking for work', 'wp-bbtheme-child' ),
            __( 'Search roles, build your candidate profile and keep applications organised from your dashboard.', 'wp-bbtheme-child' ),
            'wpbb-jobs-path-card',
            __( 'Candidate dashboard', 'wp-bbtheme-child' ),
            wpbb_jobs_page_url( 'candidate-dashboard' )
        ),
        wpbb_child_v62_icon_card(
            __( 'I am hiring', 'wp-bbtheme-child' ),
            __( 'Create an employer profile, publish opportunities and manage candidate applications in one recruitment workspace.', 'wp-bbtheme-child' ),
            'wpbb-jobs-path-card',
            __( 'Employer dashboard', 'wp-bbtheme-child' ),
            wpbb_jobs_page_url( 'employer-dashboard' )
        ),
    );

    $index = 0;
    $content = preg_replace_callback(
        '~<!--\\s*wp:wpbb/icon-card\\s*/-->~',
        static function() use ( &$index, $cards ) {
            if ( $index >= count( $cards ) ) return '<!-- wp:wpbb/icon-card /-->';
            return $cards[ $index++ ];
        },
        $content,
        2
    );

    return $content;
}

function wpbb_jobs_portal_page_content( $slug ) {
    $pages = array(
        'jobs' => array(
            __( 'Jobs', 'wp-bbtheme-child' ),
            __( 'Search current opportunities and narrow the list by the details that matter to you.', 'wp-bbtheme-child' ),
            wpbb_jobs_block( 'search', array( 'compact' => false ) ) . wpbb_jobs_block( 'list', array( 'perPage' => 12, 'columns' => 2, 'featured' => false ) ),
        ),
        'hiring-companies' => array(
            __( 'Hiring Companies', 'wp-bbtheme-child' ),
            __( 'Explore employer profiles and discover the teams behind current opportunities.', 'wp-bbtheme-child' ),
            wpbb_jobs_block( 'companies', array( 'perPage' => 12, 'columns' => 3 ) ),
        ),
        'find-candidates' => array(
            __( 'Find Candidates', 'wp-bbtheme-child' ),
            __( 'A privacy-conscious employer view of candidate profiles and uploaded CVs.', 'wp-bbtheme-child' ),
            wpbb_jobs_block( 'resumes', array( 'perPage' => 12, 'columns' => 2 ) ),
        ),
        'candidate-dashboard' => array(
            __( 'Candidate Dashboard', 'wp-bbtheme-child' ),
            __( 'Manage your profile and track the applications you have submitted.', 'wp-bbtheme-child' ),
            wpbb_jobs_shortcode_block( '[wpbb_candidate_dashboard]' ),
        ),
        'employer-dashboard' => array(
            __( 'Employer Dashboard', 'wp-bbtheme-child' ),
            __( 'Manage company information, vacancies, applications and recruitment activity.', 'wp-bbtheme-child' ),
            wpbb_jobs_shortcode_block( '[wpbb_employer_dashboard]' ),
        ),
        'post-a-job' => array(
            __( 'Post a Job', 'wp-bbtheme-child' ),
            __( 'Create a clear vacancy with the information candidates need to make an informed application.', 'wp-bbtheme-child' ),
            wpbb_jobs_shortcode_block( '[wpbb_post_job]' ),
        ),
        'create-resume' => array(
            __( 'Create or Update Your Candidate Profile', 'wp-bbtheme-child' ),
            __( 'Build a reusable profile and attach a CV for applications and employer discovery.', 'wp-bbtheme-child' ),
            wpbb_jobs_shortcode_block( '[wpbb_resume_form]' ),
        ),
        'login-register' => array(
            __( 'Login & Register', 'wp-bbtheme-child' ),
            __( 'Choose a candidate or employer account and keep each recruitment journey focused on the tools it needs.', 'wp-bbtheme-child' ),
            wpbb_jobs_shortcode_block( '[wpbb_jobs_login_register]' ),
        ),
    );

    if ( empty( $pages[ $slug ] ) ) return '';
    list( $title, $intro, $body ) = $pages[ $slug ];
    $header  = wpbb_child_v62_paragraph( __( 'TalentBridge Jobs', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $header .= wpbb_child_v62_heading( $title, 1, 'wpbb-jobs-portal-page-title' );
    $header .= wpbb_child_v62_paragraph( $intro, 'wpbb-jobs-portal-page-intro' );
    return wpbb_jobs_section( $header, 'wpbb-jobs-portal-page-header bg-light' ) . wpbb_jobs_section( $body, 'wpbb-jobs-portal-page-body' );
}

function wpbb_jobs_create_portal_pages( $profile = null ) {
    $profile = is_array( $profile ) ? $profile : ( function_exists( 'wp_theme_get_demo_profile' ) ? wp_theme_get_demo_profile() : array( 'id' => 'jobs' ) );
    if ( ( $profile['id'] ?? 'jobs' ) !== 'jobs' ) return array( 'created' => 0, 'updated' => 0, 'skipped' => 0 );

    $specs = array(
        'jobs'                => __( 'Jobs', 'wp-bbtheme-child' ),
        'hiring-companies'    => __( 'Hiring Companies', 'wp-bbtheme-child' ),
        'find-candidates'     => __( 'Find Candidates', 'wp-bbtheme-child' ),
        'candidate-dashboard' => __( 'Candidate Dashboard', 'wp-bbtheme-child' ),
        'employer-dashboard'  => __( 'Employer Dashboard', 'wp-bbtheme-child' ),
        'post-a-job'          => __( 'Post a Job', 'wp-bbtheme-child' ),
        'create-resume'       => __( 'Candidate Profile', 'wp-bbtheme-child' ),
        'login-register'      => __( 'Login & Register', 'wp-bbtheme-child' ),
    );
    $result = array( 'created' => 0, 'updated' => 0, 'skipped' => 0 );

    foreach ( $specs as $slug => $title ) {
        $existing = get_page_by_path( $slug, OBJECT, 'page' );
        if ( $existing instanceof WP_Post && ! get_post_meta( $existing->ID, '_wpbb_jobs_portal_page', true ) ) {
            $result['skipped']++;
            continue;
        }
        $args = array(
            'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug,
            'post_content' => wpbb_jobs_portal_page_content( $slug ),
        );
        if ( $existing instanceof WP_Post ) $args['ID'] = $existing->ID;
        $id = $existing instanceof WP_Post ? wp_update_post( wp_slash( $args ), true ) : wp_insert_post( wp_slash( $args ), true );
        if ( is_wp_error( $id ) || ! $id ) { $result['skipped']++; continue; }
        update_post_meta( $id, '_wpbb_jobs_portal_page', 1 );
        update_post_meta( $id, '_wp_theme_demo_profile', 'jobs' );
        $existing instanceof WP_Post ? $result['updated']++ : $result['created']++;
    }
    return $result;
}
add_action( 'wp_theme_seed_sector_pages', 'wpbb_jobs_create_portal_pages', 20 );

function wpbb_jobs_demo_import_message( $message, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return $message;
    return __( 'TalentBridge Jobs starter refreshed: BBuilder/Gutenberg sector pages, integrated Jobs pages, navigation and rich recruitment demo data are assigned. Employers, vacancies, candidate profiles and example application workflow states are repaired automatically; existing unmanaged pages remain untouched.', 'wp-bbtheme-child' );
}
add_filter( 'wp_theme_demo_import_message', 'wpbb_jobs_demo_import_message', 20, 2 );


/** Automatically populate the integrated recruitment marketplace during Starter Setup. */
function wpbb_jobs_seed_demo_after_starter_import( $page_id, $profile ) {
    unset( $page_id );
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return;
    $result = wpbb_jobs_seed_rich_demo_data();
    update_option( 'wpbb_jobs_demo_seed_summary', $result, false );
    update_option( 'wpbb_jobs_demo_seeded_at', current_time( 'mysql' ), false );
}
add_action( 'wp_theme_after_demo_import', 'wpbb_jobs_seed_demo_after_starter_import', 60, 2 );
