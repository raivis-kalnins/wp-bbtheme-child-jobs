<?php
defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/bbuilder-system-v62.php';
require_once __DIR__ . '/inc/jobs-core.php';
require_once __DIR__ . '/inc/jobs-render.php';
require_once __DIR__ . '/inc/jobs-portal-integration.php';
require_once __DIR__ . '/inc/jobs-admin.php';

function wpbb_jobs_project_mode( $mode ) {
    return 'jobs';
}
add_filter( 'wp_theme_project_mode', 'wpbb_jobs_project_mode' );


function wpbb_jobs_theme_color( $color, $stylesheet ) {
    return get_stylesheet() === $stylesheet ? '#3155d9' : $color;
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
        wp_add_inline_style( 'wpbb-jobs-sector-base', wp_theme_sector_customizer_css( '#3155d9', '18px', '--sector-primary', '--sector-radius' ) );
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
    $profile['primary_label'] = __( 'Search jobs', 'wp-bbtheme-child' );
    $profile['primary_url']   = wpbb_jobs_page_url( 'jobs' );
    $profile['secondary_label'] = __( 'Hire talent', 'wp-bbtheme-child' );
    $profile['secondary_url']   = wpbb_jobs_page_url( 'services' );

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
        'theme_brand_color'       => '#3155d9',
        'theme_accent_color'      => '#19a7ce',
        'theme_text_color'        => '#23304d',
        'theme_heading_color'     => '#0b1739',
        'theme_background_color'  => '#f6f8fc',
        'theme_surface_color'     => '#ffffff',
        'theme_surface_alt_color' => '#eef3fb',
        'theme_border_color'      => '#dce4f0',
        'theme_grey_dark_color'   => '#5d6a82',
        'theme_grey_light_color'  => '#eef3fb',
        'theme_success_color'     => '#167c5a',
        'theme_link_color'        => '#3155d9',
        'theme_link_hover_color'  => '#233fa9',
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

    $jobs = array( 'key' => 'jobs', 'title' => __( 'Jobs', 'wp-bbtheme-child' ), 'slug' => 'jobs', 'locations' => array( 'header', 'footer' ) );
    $companies = array( 'key' => 'companies', 'title' => __( 'Companies', 'wp-bbtheme-child' ), 'slug' => 'hiring-companies', 'locations' => array( 'header', 'footer' ) );
    array_splice( $items, 1, 0, array( $jobs, $companies ) );
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
                array( __( 'Contact', 'wp-bbtheme-child' ), __( 'Get help with the recruitment platform.', 'wp-bbtheme-child' ), wpbb_jobs_page_url( 'contact' ) ),
            ),
        ),
    );
    return $definitions;
}
add_filter( 'wp_theme_demo_mega_menu_definitions', 'wpbb_jobs_mega_menu_definitions', 20, 2 );
