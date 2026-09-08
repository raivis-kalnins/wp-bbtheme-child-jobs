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

    return $content
        . wpbb_jobs_section( $latest, 'wpbb-jobs-latest-section' )
        . wpbb_jobs_section( $companies, 'wpbb-jobs-companies-section bg-light' );
}
add_filter( 'wp_theme_demo_extra_home_sections', 'wpbb_jobs_home_extra_sections', 20, 2 );



/**
 * v3.8.10.78 search-led Jobs homepage.
 * The Jobs sector intentionally diverges from the generic sector brochure
 * homepage: job search is the primary task, followed by category discovery,
 * employers, salary tools, live jobs and career guidance.
 */
function wpbb_jobs_home_content_v76( $profile = array() ) {
    $jobs_url = wpbb_jobs_page_url( 'jobs' );
    $companies_url = wpbb_jobs_page_url( 'hiring-companies' );
    $salary_url = wpbb_jobs_page_url( 'salary-guide' );
    $candidate_url = wpbb_jobs_page_url( 'create-resume' );
    $employer_url = wpbb_jobs_page_url( 'post-a-job' );
    $blog_id = absint( get_option( 'page_for_posts' ) );
    if ( $blog_id && function_exists( 'pll_get_post' ) && function_exists( 'wpbb_jobs_current_language' ) && 'en' !== wpbb_jobs_current_language() ) {
        $translated_blog_id = absint( pll_get_post( $blog_id, wpbb_jobs_current_language() ) );
        if ( $translated_blog_id ) $blog_id = $translated_blog_id;
    }
    $blog_url = $blog_id ? get_permalink( $blog_id ) : home_url( '/blog/' );

    $hero_left  = wpbb_child_v62_paragraph( __( 'Find work. Build teams. Make the next move clearer.', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $hero_left .= wpbb_child_v62_heading( __( 'Find a job that fits your life — and your next move.', 'wp-bbtheme-child' ), 1, 'wpbb-jobs-home-title' );
    $hero_left .= wpbb_child_v62_paragraph( __( 'Search live roles by title, location and working pattern. Compare salaries, explore employers and keep your applications in one place.', 'wp-bbtheme-child' ), 'wpbb-jobs-home-intro' );
    $hero_left .= wpbb_jobs_block( 'search', array( 'compact' => true ) );
    $popular = array( 'Remote' => 'remote', 'Data' => 'data', 'Marketing' => 'marketing', 'Design' => 'design', 'Finance' => 'finance' );
    $chips = '<div class="wpbb-jobs-popular"><span>' . esc_html__( 'Popular:', 'wp-bbtheme-child' ) . '</span>';
    foreach ( $popular as $label => $keyword ) {
        $chip_label = function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $label ) : $label;
        $chips .= '<a href="' . esc_url( add_query_arg( 'job_keyword', $keyword, $jobs_url ) ) . '">' . esc_html( $chip_label ) . '</a>';
    }
    $chips .= '</div>';
    $hero_left .= wpbb_child_v62_block( 'html', array(), $chips );

    $hero_right  = wpbb_child_v62_image( wpbb_jobs_asset_url( 'candidate-interview.jpg' ), 'wpbb-jobs-home-hero-image' );
    $hero_right .= wpbb_child_v62_block( 'html', array(), '<div class="wpbb-jobs-home-trust"><strong>' . esc_html__( 'A practical recruitment marketplace', 'wp-bbtheme-child' ) . '</strong><span>' . esc_html__( 'Jobs, employers, applications and salary tools in one editable WordPress experience.', 'wp-bbtheme-child' ) . '</span></div>' );

    $hero_row = wpbb_child_v62_block( 'wpbb/row', array( 'customClasses' => 'wpbb-jobs-home-hero-row align-items-center', 'gutterX' => 'gx-5', 'gutterY' => 'gy-4' ),
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 7 ), $hero_left ) .
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 5 ), $hero_right )
    );
    $hero = wpbb_child_v67_section( 'wpbb-jobs-home-hero', $hero_row . wpbb_jobs_block( 'home-metrics' ), 'wpbb-jobs-home-hero-shell' );

    $categories  = wpbb_child_v62_paragraph( __( 'Explore opportunities', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $categories .= wpbb_child_v62_heading( __( 'Browse jobs by career area.', 'wp-bbtheme-child' ), 2 );
    $categories .= wpbb_child_v62_paragraph( __( 'Start broad, then narrow the live vacancies by location, contract type and the details that matter to you.', 'wp-bbtheme-child' ), 'wpbb-jobs-section-intro' );
    $categories .= wpbb_jobs_block( 'categories', array( 'limit' => 8 ) );
    $categories .= wpbb_child_v62_paragraph( '<a class="wpbb-jobs-text-link" href="' . esc_url( $jobs_url ) . '">' . esc_html__( 'Browse all jobs', 'wp-bbtheme-child' ) . ' →</a>' );
    $categories = wpbb_child_v67_section( 'wpbb-jobs-home-categories', $categories );

    $companies  = wpbb_child_v62_paragraph( __( 'Hiring now', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $companies .= wpbb_child_v62_heading( __( 'Meet employers with open roles.', 'wp-bbtheme-child' ), 2 );
    $companies .= wpbb_child_v62_paragraph( __( 'See who they are, where they hire and the opportunities currently attached to each employer profile.', 'wp-bbtheme-child' ), 'wpbb-jobs-section-intro' );
    $companies .= wpbb_jobs_block( 'companies', array( 'perPage' => 6, 'columns' => 3 ) );
    $companies .= wpbb_child_v62_paragraph( '<a class="wpbb-jobs-text-link" href="' . esc_url( $companies_url ) . '">' . esc_html__( 'View all hiring companies', 'wp-bbtheme-child' ) . ' →</a>' );
    $companies = wpbb_child_v67_section( 'wpbb-jobs-home-companies bg-light', $companies );

    $salary_copy  = wpbb_child_v62_paragraph( __( 'Salary planner', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $salary_copy .= wpbb_child_v62_heading( __( 'Know the salary. Understand the take-home pay.', 'wp-bbtheme-child' ), 2 );
    $salary_copy .= wpbb_child_v62_paragraph( __( 'Compare salary signals from the vacancies on this site, then estimate what a gross salary could mean after Income Tax, employee National Insurance and an optional salary-sacrifice pension.', 'wp-bbtheme-child' ) );
    $salary_copy .= wpbb_jobs_block( 'salary-guide', array( 'limit' => 4 ) );
    $salary_copy .= wpbb_child_v62_paragraph( '<a class="wpbb-jobs-text-link" href="' . esc_url( $salary_url ) . '">' . esc_html__( 'Open the full salary guide', 'wp-bbtheme-child' ) . ' →</a>' );
    $salary_row = wpbb_child_v62_block( 'wpbb/row', array( 'customClasses' => 'wpbb-jobs-home-salary-row align-items-start', 'gutterX' => 'gx-5', 'gutterY' => 'gy-4' ),
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 5 ), $salary_copy ) .
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 7 ), wpbb_jobs_block( 'salary-calculator' ) )
    );
    $salary = wpbb_child_v67_section( 'wpbb-jobs-home-salary', $salary_row );

    $latest  = wpbb_child_v62_paragraph( __( 'Latest opportunities', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $latest .= wpbb_child_v62_heading( __( 'Fresh jobs worth a closer look.', 'wp-bbtheme-child' ), 2 );
    $latest .= wpbb_child_v62_paragraph( __( 'A small, useful selection on the homepage — the full vacancy list stays on the Jobs page where filters and pagination belong.', 'wp-bbtheme-child' ), 'wpbb-jobs-section-intro' );
    $latest .= wpbb_jobs_block( 'list', array( 'perPage' => 6, 'columns' => 2, 'featured' => false ) );
    $latest .= wpbb_child_v62_paragraph( '<a class="wpbb-jobs-text-link" href="' . esc_url( $jobs_url ) . '">' . esc_html__( 'See every open vacancy', 'wp-bbtheme-child' ) . ' →</a>' );
    $latest = wpbb_child_v67_section( 'wpbb-jobs-home-latest bg-light', $latest );

    $candidate  = wpbb_child_v62_image( wpbb_jobs_asset_url( 'candidate-interview.jpg' ), 'wpbb-jobs-route-card__image' );
    $candidate .= wpbb_child_v62_paragraph( __( 'For candidates', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $candidate .= wpbb_child_v62_heading( __( 'Be ready when the right role appears.', 'wp-bbtheme-child' ), 3 );
    $candidate .= wpbb_child_v62_paragraph( __( 'Create a reusable candidate profile, attach a CV and keep submitted applications visible in your dashboard.', 'wp-bbtheme-child' ) );
    $candidate .= wpbb_child_v62_block( 'wpbb/button', array( 'text' => __( 'Create candidate profile', 'wp-bbtheme-child' ), 'url' => $candidate_url, 'btnClass' => 'btn btn-primary' ), '', true );

    $employer  = wpbb_child_v62_image( wpbb_jobs_asset_url( 'hiring-workshop.jpg' ), 'wpbb-jobs-route-card__image' );
    $employer .= wpbb_child_v62_paragraph( __( 'For employers', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $employer .= wpbb_child_v62_heading( __( 'Turn a vacancy into a clear hiring journey.', 'wp-bbtheme-child' ), 3 );
    $employer .= wpbb_child_v62_paragraph( __( 'Publish roles with salary and working-model context, review candidates and progress applications without a separate recruitment plugin.', 'wp-bbtheme-child' ) );
    $employer .= wpbb_child_v62_block( 'wpbb/button', array( 'text' => __( 'Post a job', 'wp-bbtheme-child' ), 'url' => $employer_url, 'btnClass' => 'btn btn-primary' ), '', true );

    $routes_head  = wpbb_child_v62_paragraph( __( 'One marketplace, two clear journeys', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $routes_head .= wpbb_child_v62_heading( __( 'Useful for people looking — and teams hiring.', 'wp-bbtheme-child' ), 2 );
    $routes_row = wpbb_child_v62_block( 'wpbb/row', array( 'customClasses' => 'wpbb-jobs-home-routes-row', 'gutterX' => 'gx-4', 'gutterY' => 'gy-4' ),
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 6, 'customClasses' => 'd-flex' ), wpbb_child_v62_block( 'group', array( 'className' => 'wpbb-jobs-route-card' ), '<div class="wp-block-group wpbb-jobs-route-card">' . $candidate . '</div>' ) ) .
        wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12, 'lg' => 6, 'customClasses' => 'd-flex' ), wpbb_child_v62_block( 'group', array( 'className' => 'wpbb-jobs-route-card' ), '<div class="wp-block-group wpbb-jobs-route-card">' . $employer . '</div>' ) )
    );
    $routes = wpbb_child_v67_section( 'wpbb-jobs-home-routes', $routes_head . $routes_row );

    $advice  = wpbb_child_v62_paragraph( __( 'Career advice', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
    $advice .= wpbb_child_v62_heading( __( 'Practical guidance for a better job search.', 'wp-bbtheme-child' ), 2 );
    $advice .= wpbb_jobs_block( 'career-advice', array( 'limit' => 3 ) );
    $advice .= wpbb_child_v62_paragraph( '<a class="wpbb-jobs-text-link" href="' . esc_url( $blog_url ) . '">' . esc_html__( 'View all career advice', 'wp-bbtheme-child' ) . ' →</a>' );
    $advice = wpbb_child_v67_section( 'wpbb-jobs-home-advice bg-light', $advice );

    $cta = wpbb_child_v62_block( 'wpbb/cta-section', array(
        'title' => __( 'Ready for the next move?', 'wp-bbtheme-child' ),
        'text' => __( 'Search the current vacancies, compare salaries or create a profile so the next application starts with better information.', 'wp-bbtheme-child' ),
        'buttonText' => __( 'Search jobs', 'wp-bbtheme-child' ),
        'buttonUrl' => $jobs_url,
        'className' => 'wp-theme-home-cta wp-theme-home-cta-bbuilder wpbb-jobs-home-cta',
    ), '', true );

    return $hero . $categories . $companies . $salary . $latest . $routes . $advice . $cta;
}

/**
 * Final Jobs homepage safety pass.
 *
 * v3.8.10.73 removes the redundant candidate/employer pathway card section.
 * The homepage already exposes those routes through the navigation, employer
 * tools, candidate routes and dedicated dashboards, so the extra two-card
 * section only duplicated actions and could fall back to default Icon Card
 * placeholders on older saved demos.
 */
function wpbb_jobs_repair_canonical_home_content( $content, $profile = array() ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return $content;
    return wpbb_jobs_home_content_v76( $profile );
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
        'salary-guide' => array(
            __( 'Salary Guide & Calculator', 'wp-bbtheme-child' ),
            __( 'Compare salary signals from the live vacancies on this site and estimate take-home pay for the 2026/27 tax year.', 'wp-bbtheme-child' ),
            wpbb_jobs_block( 'salary-guide', array( 'limit' => 8 ) ) . wpbb_jobs_block( 'salary-calculator' ),
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
        'salary-guide'        => __( 'Salary Guide & Calculator', 'wp-bbtheme-child' ),
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
