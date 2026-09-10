<?php
defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/frontend-password-protection.php';
require_once __DIR__ . '/inc/jobs-i18n.php';
require_once __DIR__ . '/inc/bbuilder-system-v62.php';
require_once __DIR__ . '/inc/jobs-core.php';
require_once __DIR__ . '/inc/jobs-render.php';
require_once __DIR__ . '/inc/jobs-portal-integration.php';
require_once __DIR__ . '/inc/jobs-admin.php';
require_once __DIR__ . '/inc/jobs-v84-polish.php';
require_once __DIR__ . '/inc/jobs-v85-upgrade.php';
require_once __DIR__ . '/inc/jobs-v86-finish.php';
require_once __DIR__ . '/inc/jobs-v87-design.php';
require_once __DIR__ . '/inc/jobs-v88-finish.php';
require_once __DIR__ . '/inc/jobs-v89-finish.php';
require_once __DIR__ . '/inc/jobs-v92-live-fix.php';
// 3.8.10.94 hotfix: the 3.8.10.93 archive shipped the module as
// jobs-v93-live-search.php while functions.php referenced jobs-v93-live-fix.php.
// Load the real module defensively so a missing optional release module can never
// take the entire WordPress site down during an interrupted/partial update.
$wpbb_jobs_v93_module = __DIR__ . '/inc/jobs-v93-live-search.php';
if ( is_readable( $wpbb_jobs_v93_module ) ) {
    require_once $wpbb_jobs_v93_module;
}
unset( $wpbb_jobs_v93_module );

function wpbb_jobs_project_mode( $mode ) {
    return 'jobs';
}
add_filter( 'wp_theme_project_mode', 'wpbb_jobs_project_mode' );


function wpbb_jobs_theme_color( $color, $stylesheet ) {
    return get_stylesheet() === $stylesheet ? '#078f6a' : $color;
}
add_filter( 'wp_theme_project_theme_color', 'wpbb_jobs_theme_color', 20, 2 );

function wpbb_jobs_enqueue_sector_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-sector-base',
        get_stylesheet_directory_uri() . '/assets/jobs-sector-base.css',
        array(),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-sector-base',
        get_stylesheet_directory_uri() . '/assets/jobs-sector-base.js',
        array(),
        $version,
        true
    );
    if ( function_exists( 'wp_theme_sector_customizer_css' ) ) {
        wp_add_inline_style( 'wpbb-jobs-sector-base', wp_theme_sector_customizer_css( '#078f6a', '18px', '--sector-primary', '--sector-radius' ) );
    }
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_enqueue_sector_assets', 30 );

function wpbb_jobs_enqueue_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-portal',
        get_stylesheet_directory_uri() . '/assets/jobs-portal.css',
        array( 'wpbb-jobs-sector-base' ),
        $version
    );
    wp_enqueue_style(
        'wpbb-jobs-v84',
        get_stylesheet_directory_uri() . '/assets/jobs-v84.css',
        array( 'wpbb-jobs-portal' ),
        $version
    );
    wp_enqueue_style(
        'wpbb-jobs-v85',
        get_stylesheet_directory_uri() . '/assets/jobs-v85.css',
        array( 'wpbb-jobs-v84' ),
        $version
    );
    wp_enqueue_script(
        'wpbb-jobs-salary',
        get_stylesheet_directory_uri() . '/assets/jobs-salary.js',
        array(),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_enqueue_assets', 160 );

function wpbb_jobs_dark_mode_bootstrap() {
    echo '<script>(function(){try{var m=localStorage.getItem("wpThemeMode");if(m==="dark"){document.documentElement.classList.add("is-dark-theme");document.documentElement.setAttribute("data-theme","dark");}}catch(e){}})();</script>';
}
add_action( 'wp_head', 'wpbb_jobs_dark_mode_bootstrap', 1 );

/**
 * The parent orchestrator knows its historic non-store modes but predates the
 * integrated Jobs mode. Stage the same explicit WooCommerce disable prompt
 * when this HR theme is activated; never deactivate store plugins silently.
 */
function wpbb_jobs_stage_noncommerce_transition() {
    if ( ! is_admin() || ! current_user_can( 'activate_plugins' ) ) return;
    if ( ! function_exists( 'is_plugin_active' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $woo_active = is_plugin_active( 'woocommerce/woocommerce.php' );
    $support_active = is_plugin_active( 'wp-theme-woo-support/wp-theme-woo-support.php' );
    if ( $woo_active || $support_active ) {
        set_transient( 'wp_theme_project_pending_transition', array(
            'action' => 'disable',
            'mode'   => 'jobs',
            'theme'  => get_stylesheet(),
        ), DAY_IN_SECONDS );
    }
}
add_action( 'after_switch_theme', 'wpbb_jobs_stage_noncommerce_transition', 40 );

function wpbb_jobs_enqueue_editor_assets() {
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'wpbb-jobs-editor',
        get_stylesheet_directory_uri() . '/assets/jobs-portal-editor.css',
        array(),
        $version
    );
}
add_action( 'enqueue_block_editor_assets', 'wpbb_jobs_enqueue_editor_assets', 160 );

function wpbb_jobs_body_class( $classes ) {
    $classes[] = 'wpbb-jobs-theme';
    // The suite finishing layers key their responsive/navigation rules from
    // this neutral class. Jobs predates that convention, so without it the
    // injected mobile drawer exists but its responsive CSS never becomes
    // active at tablet/mobile widths.
    $classes[] = 'wpbb-sector-premium';
    if ( wpbb_jobs_engine_active() ) $classes[] = 'wpbb-jobs-engine-active';
    return $classes;
}
add_filter( 'body_class', 'wpbb_jobs_body_class' );

function wpbb_jobs_asset_url( $filename ) {
    return trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/demo/' . ltrim( $filename, '/' );
}


function wpbb_jobs_demo_profile( $profile ) {
    $profile['id']      = 'jobs';
    $profile['name']    = __( 'TalentBridge Jobs', 'wp-bbtheme-child' );
    $profile['eyebrow'] = __( 'Jobs · recruitment · talent', 'wp-bbtheme-child' );
    $profile['commerce'] = false;

    $profile['hero_title'] = __( 'Find work that fits. Hire people who move the business forward.', 'wp-bbtheme-child' );
    $profile['hero_text']  = __( 'A joined-up recruitment experience for candidates exploring their next move and employers building stronger teams.', 'wp-bbtheme-child' );
    $profile['primary_label'] = __( 'Post a job', 'wp-bbtheme-child' );
    $profile['primary_url']   = wpbb_jobs_page_url( 'post-a-job' );
    $profile['secondary_label'] = __( 'Find a job', 'wp-bbtheme-child' );
    $profile['secondary_url']   = wpbb_jobs_page_url( 'jobs' );

    $profile['services_eyebrow'] = __( 'For employers', 'wp-bbtheme-child' );
    $profile['services_heading'] = __( 'Recruitment tools that keep the hiring team focused on good decisions.', 'wp-bbtheme-child' );
    $profile['services'] = array(
        array( __( 'Advertise roles', 'wp-bbtheme-child' ), __( 'Publish clear vacancies with the salary, location, working model and requirements candidates need.', 'wp-bbtheme-child' ) ),
        array( __( 'Find candidates', 'wp-bbtheme-child' ), __( 'Search and review candidate profiles without disconnecting the shortlist from the live vacancy.', 'wp-bbtheme-child' ) ),
        array( __( 'Manage hiring', 'wp-bbtheme-child' ), __( 'Keep applications, employer activity and next actions inside one practical recruitment workspace.', 'wp-bbtheme-child' ) ),
    );

    $profile['about_eyebrow'] = __( 'A better marketplace', 'wp-bbtheme-child' );
    $profile['about_title'] = __( 'Useful information on both sides of the hiring decision.', 'wp-bbtheme-child' );
    $profile['about_text'] = __( 'Candidates can discover relevant opportunities and manage applications, while employers get a clear route from publishing a role to reviewing talent and progressing a hire.', 'wp-bbtheme-child' );

    $profile['industries_eyebrow'] = __( 'For candidates', 'wp-bbtheme-child' );
    $profile['industries_heading'] = __( 'Different career routes, one consistent way to discover the right opportunity.', 'wp-bbtheme-child' );
    $profile['industries'] = array(
        array( __( 'Remote & hybrid', 'wp-bbtheme-child' ), __( 'Find roles with clear location and working-model expectations before applying.', 'wp-bbtheme-child' ) ),
        array( __( 'Graduate & early career', 'wp-bbtheme-child' ), __( 'Make entry routes, development opportunities and application requirements easy to understand.', 'wp-bbtheme-child' ) ),
        array( __( 'Professional & specialist', 'wp-bbtheme-child' ), __( 'Search roles where responsibilities, experience and salary context matter.', 'wp-bbtheme-child' ) ),
        array( __( 'Contract & flexible', 'wp-bbtheme-child' ), __( 'Surface duration, availability and engagement details without hiding them deep in the description.', 'wp-bbtheme-child' ) ),
    );

    $profile['stats'] = array(
        array( '01', __( 'place for jobs, candidates and employers', 'wp-bbtheme-child' ) ),
        array( '02', __( 'clear journeys: find work or hire talent', 'wp-bbtheme-child' ) ),
        array( '24/7', __( 'searchable opportunities', 'wp-bbtheme-child' ) ),
        array( '100%', __( 'editable with WordPress blocks', 'wp-bbtheme-child' ) ),
    );

    $profile['process_eyebrow'] = __( 'Candidate journey', 'wp-bbtheme-child' );
    $profile['process_heading'] = __( 'From first search to a well-prepared application.', 'wp-bbtheme-child' );
    $profile['process'] = array(
        array( '01', __( 'Discover', 'wp-bbtheme-child' ), __( 'Search by role, category, employer and location to narrow the market quickly.', 'wp-bbtheme-child' ) ),
        array( '02', __( 'Apply', 'wp-bbtheme-child' ), __( 'Use a candidate profile and resume to respond with the information the employer needs.', 'wp-bbtheme-child' ) ),
        array( '03', __( 'Track', 'wp-bbtheme-child' ), __( 'Return to the candidate dashboard to keep applications and next steps organised.', 'wp-bbtheme-child' ) ),
    );

    $profile['faq_heading'] = __( 'Recruitment questions answered before they become friction.', 'wp-bbtheme-child' );
    $profile['cta_title'] = __( 'Ready to make the next career or hiring move easier?', 'wp-bbtheme-child' );
    $profile['cta_text']  = __( 'Search current opportunities, create a candidate profile or give your hiring team a better place to publish and manage roles.', 'wp-bbtheme-child' );
    $profile['footer_text'] = __( 'TalentBridge brings job discovery, employer tools and candidate journeys into one practical WordPress recruitment platform.', 'wp-bbtheme-child' );

    $profile['page_labels'] = array(
        'about'      => __( 'About', 'wp-bbtheme-child' ),
        'services'   => __( 'For Employers', 'wp-bbtheme-child' ),
        'industries' => __( 'For Candidates', 'wp-bbtheme-child' ),
        'contact'    => __( 'Contact', 'wp-bbtheme-child' ),
        'blog'       => __( 'Career Advice', 'wp-bbtheme-child' ),
    );

    $profile['palette'] = array(
        'theme_brand_color'       => '#078f6a',
        'theme_accent_color'      => '#0caf80',
        'theme_text_color'        => '#29415f',
        'theme_heading_color'     => '#10254c',
        'theme_background_color'  => '#f7fbfa',
        'theme_surface_color'     => '#ffffff',
        'theme_surface_alt_color' => '#eef8f5',
        'theme_border_color'      => '#dce9e5',
        'theme_grey_dark_color'   => '#607487',
        'theme_grey_light_color'  => '#eef8f5',
        'theme_success_color'     => '#078f6a',
        'theme_link_color'        => '#08765c',
        'theme_link_hover_color'  => '#056b54',
        'theme_radius'            => '18px',
        'theme_font_provider'     => 'system',
        'theme_body_font'         => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
        'theme_heading_font'      => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
        'theme_ui_font'           => "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif",
    );

    $profile['hero_image']  = wpbb_jobs_asset_url( 'recruitment-team.jpg' );
    $profile['about_image'] = wpbb_jobs_asset_url( 'candidate-interview.jpg' );
    $profile['hero_slides'] = array(
        array(
            'type' => 'hero',
            'eyebrow' => __( 'Jobs for people. Talent for teams.', 'wp-bbtheme-child' ),
            'title' => $profile['hero_title'],
            'text' => $profile['hero_text'],
            'image' => wpbb_jobs_asset_url( 'recruitment-team.jpg' ),
            'buttonText' => __( 'Search jobs', 'wp-bbtheme-child' ),
            'buttonUrl' => wpbb_jobs_page_url( 'jobs' ),
            'secondaryText' => __( 'Hire talent', 'wp-bbtheme-child' ),
            'secondaryUrl' => wpbb_jobs_page_url( 'services' ),
        ),
        array(
            'type' => 'hero',
            'eyebrow' => __( 'For candidates', 'wp-bbtheme-child' ),
            'title' => __( 'Make the next application a better-informed one.', 'wp-bbtheme-child' ),
            'text' => __( 'Search relevant roles, compare the details that matter and keep your applications in one candidate workspace.', 'wp-bbtheme-child' ),
            'image' => wpbb_jobs_asset_url( 'candidate-interview.jpg' ),
            'buttonText' => __( 'Browse opportunities', 'wp-bbtheme-child' ),
            'buttonUrl' => wpbb_jobs_page_url( 'jobs' ),
        ),
        array(
            'type' => 'hero',
            'eyebrow' => __( 'For employers', 'wp-bbtheme-child' ),
            'title' => __( 'A hiring workspace built around the vacancy, not admin noise.', 'wp-bbtheme-child' ),
            'text' => __( 'Publish roles, review candidates and keep employer activity connected to the recruitment journey.', 'wp-bbtheme-child' ),
            'image' => wpbb_jobs_asset_url( 'employer-workspace.jpg' ),
            'buttonText' => __( 'Post a job', 'wp-bbtheme-child' ),
            'buttonUrl' => wpbb_jobs_page_url( 'post-a-job' ),
        ),
    );

    $profile['gallery_heading'] = __( 'People, teams and career conversations—not anonymous listings.', 'wp-bbtheme-child' );
    $profile['gallery'] = array(
        array( wpbb_jobs_asset_url( 'recruitment-team.jpg' ), __( 'Recruitment teams', 'wp-bbtheme-child' ), __( 'A structured workspace for employers and hiring teams.', 'wp-bbtheme-child' ) ),
        array( wpbb_jobs_asset_url( 'candidate-interview.jpg' ), __( 'Candidate conversations', 'wp-bbtheme-child' ), __( 'Clearer job information supports better interviews and decisions.', 'wp-bbtheme-child' ) ),
        array( wpbb_jobs_asset_url( 'hiring-workshop.jpg' ), __( 'Hiring workshops', 'wp-bbtheme-child' ), __( 'Bring roles, requirements and team priorities into the same process.', 'wp-bbtheme-child' ) ),
        array( wpbb_jobs_asset_url( 'employer-workspace.jpg' ), __( 'Employer workspace', 'wp-bbtheme-child' ), __( 'Keep opportunities and candidate activity easy to navigate.', 'wp-bbtheme-child' ) ),
    );

    $profile['cases_heading'] = __( 'Recruitment journeys with a measurable next step.', 'wp-bbtheme-child' );
    $profile['cases'] = array(
        array( __( 'Specialist team growth', 'wp-bbtheme-child' ), __( 'A clearer vacancy structure helped candidates understand the role before applying.', 'wp-bbtheme-child' ), '38%', __( 'more qualified applications', 'wp-bbtheme-child' ) ),
        array( __( 'Graduate hiring campaign', 'wp-bbtheme-child' ), __( 'One candidate journey connected role discovery, application guidance and employer context.', 'wp-bbtheme-child' ), '2.4×', __( 'more completed applications', 'wp-bbtheme-child' ) ),
        array( __( 'Multi-role recruitment', 'wp-bbtheme-child' ), __( 'Search, employer profiles and dashboards reduced the number of disconnected hiring touchpoints.', 'wp-bbtheme-child' ), '1', __( 'joined-up recruitment workspace', 'wp-bbtheme-child' ) ),
    );
    $profile['contact_heading'] = __( 'Talk to the right recruitment team.', 'wp-bbtheme-child' );
    $profile['contact_text'] = __( 'Tell us whether you are looking for work, hiring, or need help with an account so the enquiry reaches the right place.', 'wp-bbtheme-child' );
    $profile['priorities_heading'] = __( 'What makes a recruitment marketplace genuinely useful.', 'wp-bbtheme-child' );
    $profile['contact'] = array(
        'email' => 'hello@talentbridge.example',
        'phone' => '+44 20 7946 0280',
        'address' => __( 'London, United Kingdom · remote recruitment across the UK', 'wp-bbtheme-child' ),
        'hours' => __( 'Mon–Fri · 08:30–18:00', 'wp-bbtheme-child' ),
    );
    $profile['blog'] = array(
        __( 'How to write a job description that earns better applications', 'wp-bbtheme-child' ),
        __( 'What candidates look for before they click Apply', 'wp-bbtheme-child' ),
        __( 'A practical interview preparation checklist', 'wp-bbtheme-child' ),
        __( 'How hiring teams can shorten the path from vacancy to shortlist', 'wp-bbtheme-child' ),
        __( 'Remote and hybrid roles: details worth making explicit', 'wp-bbtheme-child' ),
    );
    $profile['blog_categories'] = array(
        __( 'Hiring advice', 'wp-bbtheme-child' ),
        __( 'Career advice', 'wp-bbtheme-child' ),
        __( 'Interviews', 'wp-bbtheme-child' ),
    );
    $profile['blog_paths'] = array(
        get_stylesheet_directory() . '/assets/img/blog/blog-1.jpg',
        get_stylesheet_directory() . '/assets/img/blog/blog-2.jpg',
        get_stylesheet_directory() . '/assets/img/blog/blog-3.jpg',
        get_stylesheet_directory() . '/assets/img/blog/blog-4.jpg',
        get_stylesheet_directory() . '/assets/img/blog/blog-5.jpg',
    );

    $profile['blog_eyebrow'] = __( 'Career & hiring advice', 'wp-bbtheme-child' );
    $profile['blog_archive_title'] = __( 'Useful guidance for candidates and hiring teams.', 'wp-bbtheme-child' );
    $profile['blog_archive_intro'] = __( 'Practical articles on applications, interviews, employer branding, recruitment operations and better job descriptions.', 'wp-bbtheme-child' );

    return $profile;
}
add_filter( 'wp_theme_demo_profile', 'wpbb_jobs_demo_profile', 20 );

function wpbb_jobs_navigation_items( $items, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' ) return $items;

    // Keep the desktop navigation calm and useful in longer languages. About
    // and Contact stay in the utility/footer menus, while Salary Guide sits
    // under the candidate journey instead of consuming another top-level slot.
    foreach ( $items as &$item ) {
        $key = sanitize_key( (string) ( $item['key'] ?? '' ) );
        if ( 'about' === $key || 'contact' === $key ) {
            $item['locations'] = array( 'footer', 'top' );
        } elseif ( 'services' === $key || 'industries' === $key || 'blog' === $key || 'home' === $key ) {
            $item['locations'] = array( 'header', 'footer' );
        }
    }
    unset( $item );

    $jobs = array( 'key' => 'jobs', 'title' => __( 'Jobs', 'wp-bbtheme-child' ), 'slug' => 'jobs', 'locations' => array( 'header', 'footer' ) );
    $companies = array( 'key' => 'companies', 'title' => __( 'Companies', 'wp-bbtheme-child' ), 'slug' => 'hiring-companies', 'locations' => array( 'header', 'footer' ) );
    array_splice( $items, 1, 0, array( $jobs, $companies ) );

    // Useful secondary destinations live in submenus/footer rather than the
    // desktop top line. Parent menu creation understands parent_key.
    $items[] = array( 'key' => 'salary-guide', 'parent_key' => 'industries', 'title' => __( 'Salary Guide', 'wp-bbtheme-child' ), 'slug' => 'salary-guide', 'locations' => array( 'header', 'footer' ) );
    $items[] = array( 'key' => 'post-a-job', 'title' => __( 'Post a Job', 'wp-bbtheme-child' ), 'slug' => 'post-a-job', 'locations' => array( 'footer' ) );
    $items[] = array( 'key' => 'candidate-dashboard', 'title' => __( 'Candidate Dashboard', 'wp-bbtheme-child' ), 'slug' => 'candidate-dashboard', 'locations' => array( 'footer' ) );
    $items[] = array( 'key' => 'employer-dashboard', 'title' => __( 'Employer Dashboard', 'wp-bbtheme-child' ), 'slug' => 'employer-dashboard', 'locations' => array( 'footer' ) );
    return $items;
}
add_filter( 'wp_theme_demo_navigation_items', 'wpbb_jobs_navigation_items', 20, 2 );

function wpbb_jobs_mega_menu_definitions( $definitions, $profile ) {
    if ( ( $profile['id'] ?? '' ) !== 'jobs' || empty( $definitions['services'] ) ) return $definitions;

    $definitions['services']['title'] = __( 'TalentBridge — Hiring navigation', 'wp-bbtheme-child' );
    $definitions['services']['heading'] = __( 'A clearer route from vacancy to hire.', 'wp-bbtheme-child' );
    $definitions['services']['intro'] = __( 'Employer tools, candidate discovery and practical resources in one recruitment journey.', 'wp-bbtheme-child' );
    $definitions['services']['columns'] = array(
        array(
            'title' => __( 'For employers', 'wp-bbtheme-child' ),
            'links' => array(
                array( __( 'Post a job', 'wp-bbtheme-child' ), __( 'Create and publish a new vacancy.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'post-a-job' ) ),
                array( __( 'Employer dashboard', 'wp-bbtheme-child' ), __( 'Manage roles, companies and applications.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'employer-dashboard' ) ),
                array( __( 'Find candidates', 'wp-bbtheme-child' ), __( 'Review recent candidate profiles and resumes.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'find-candidates' ) ),
            ),
        ),
        array(
            'title' => __( 'For candidates', 'wp-bbtheme-child' ),
            'links' => array(
                array( __( 'Search jobs', 'wp-bbtheme-child' ), __( 'Browse and filter current opportunities.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'jobs' ) ),
                array( __( 'Candidate dashboard', 'wp-bbtheme-child' ), __( 'Track your profile and applications.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'candidate-dashboard' ) ),
                array( __( 'Create a resume', 'wp-bbtheme-child' ), __( 'Build or update your candidate profile.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'create-resume' ) ),
            ),
        ),
        array(
            'title' => __( 'Resources', 'wp-bbtheme-child' ),
            'links' => array(
                array( __( 'Hiring companies', 'wp-bbtheme-child' ), __( 'Explore employers with active recruitment profiles.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'hiring-companies' ) ),
                array( __( 'Career advice', 'wp-bbtheme-child' ), __( 'Practical guidance for applications and interviews.', 'wp-bbtheme-child' ), get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ),
                array( __( 'Salary guide', 'wp-bbtheme-child' ), __( 'Compare live demo salary benchmarks and estimate take-home pay.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'salary-guide' ) ),
                array( __( 'Contact', 'wp-bbtheme-child' ), __( 'Get help with the recruitment platform.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'contact' ) ),
            ),
        ),
    );
    return $definitions;
}
add_filter( 'wp_theme_demo_mega_menu_definitions', 'wpbb_jobs_mega_menu_definitions', 20, 2 );
// v3.8.10.75 structural/media/Woo repair.
require_once get_stylesheet_directory() . '/inc/v75-suite.php';

// v3.8.10.82: repair duplicated managed Jobs/Polylang menus.
require_once get_stylesheet_directory() . '/inc/jobs-menu-repair-v82.php';

// v3.8.10.81: keep interactive wp-admin saves/updates fast.
require_once get_stylesheet_directory() . '/inc/admin-performance.php';

// v3.8.10.90 final visual alignment layer.
function wpbb_jobs_v90_assets() {
    $path = get_stylesheet_directory() . '/assets/jobs-v90.css';
    if ( is_readable( $path ) ) wp_enqueue_style( 'wpbb-jobs-v90', get_stylesheet_directory_uri() . '/assets/jobs-v90.css', array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v90_assets', 320 );


// v3.8.10.91 live visual correction layer.
function wpbb_jobs_v91_assets() {
    $path = get_stylesheet_directory() . '/assets/jobs-v91.css';
    if ( is_readable( $path ) ) wp_enqueue_style( 'wpbb-jobs-v91', get_stylesheet_directory_uri() . '/assets/jobs-v91.css', array( 'wpbb-jobs-v90' ), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'wpbb_jobs_v91_assets', 330 );

// v3.8.10.95 full Jobs grid + BBuilder hCaptcha finishing pass.
$wpbb_jobs_v95_module = get_stylesheet_directory() . '/inc/jobs-v95-grid-captcha.php';
if ( is_readable( $wpbb_jobs_v95_module ) ) {
    require_once $wpbb_jobs_v95_module;
}
unset( $wpbb_jobs_v95_module );

// v3.8.10.96 wide-grid, contact, CTA, captcha and mobile finish.
$wpbb_jobs_v96_module = get_stylesheet_directory() . '/inc/jobs-v96-finish.php';
if ( is_readable( $wpbb_jobs_v96_module ) ) {
    require_once $wpbb_jobs_v96_module;
}
unset( $wpbb_jobs_v96_module );
// v3.8.10.97 final hero quality and mobile navigation layer.
require_once get_stylesheet_directory() . '/inc/jobs-v97-suite.php';


/** 3.8.10.98 submission, legal/article width and mobile navigation finishing layer. */
function wpbb_jobs_v98_enqueue_assets(){
    $v = wp_get_theme()->get('Version');
    wp_enqueue_style('wpbb-jobs-v98', get_stylesheet_directory_uri() . '/assets/jobs-v98.css', array('wpbb-jobs-v97'), $v);
    wp_enqueue_script('wpbb-jobs-v98', get_stylesheet_directory_uri() . '/assets/jobs-v98.js', array('wpbb-jobs-v97'), $v, true);
}
add_action('wp_enqueue_scripts','wpbb_jobs_v98_enqueue_assets',999);

// v3.8.10.99 final grid, hCaptcha/form, contact and mobile finishing layer.
$wpbb_jobs_v99_module = get_stylesheet_directory() . '/inc/jobs-v99-finish.php';
if ( is_readable( $wpbb_jobs_v99_module ) ) {
    require_once $wpbb_jobs_v99_module;
}
unset( $wpbb_jobs_v99_module );

// v3.8.11.00 final hero overlap, cookie ownership and mobile navigation fix.
$wpbb_jobs_v100_module = get_stylesheet_directory() . '/inc/jobs-v100-finish.php';
if ( is_readable( $wpbb_jobs_v100_module ) ) require_once $wpbb_jobs_v100_module;
unset( $wpbb_jobs_v100_module );

// v3.8.11.02 mobile menu, legal/contact and single-item correction.
$wpbb_jobs_v101_module = get_stylesheet_directory() . '/inc/jobs-v101-finish.php';
if ( is_readable( $wpbb_jobs_v101_module ) ) require_once $wpbb_jobs_v101_module;
unset( $wpbb_jobs_v101_module );

// v3.8.11.04 deterministic mobile navigation and WooCommerce/alignment finish.
require_once get_stylesheet_directory() . '/inc/v104-finish.php';

// v3.8.11.05 legal/contact grid, mobile drawer and WooCommerce template finish.
require_once get_stylesheet_directory() . '/inc/v105-finish.php';

// v3.8.11.07 final search, WooCommerce, Jobs captcha/grid and responsive repair.
require_once get_stylesheet_directory() . '/inc/v107-finish.php';
