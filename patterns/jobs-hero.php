<?php
/**
 * Title: HR Jobs hero
 * Slug: wp-bbtheme-child/jobs-hero
 * Categories: wp-patterns-main, wp-theme-current
 * Description: Recruitment hero with candidate and employer calls to action.
 */
$slides = array(
    array(
        'type' => 'hero',
        'eyebrow' => __( 'Jobs for people. Talent for teams.', 'wp-bbtheme-child' ),
        'title' => __( 'Find work that fits. Hire people who move the business forward.', 'wp-bbtheme-child' ),
        'text' => __( 'A joined-up recruitment experience for candidates exploring their next move and employers building stronger teams.', 'wp-bbtheme-child' ),
        'buttonText' => __( 'Search jobs', 'wp-bbtheme-child' ),
        'buttonUrl' => '/jobs/',
        'secondaryText' => __( 'Hire talent', 'wp-bbtheme-child' ),
        'secondaryUrl' => '/services/',
        'image' => get_stylesheet_directory_uri() . '/assets/img/demo/recruitment-team.jpg',
    ),
    array(
        'type' => 'hero',
        'eyebrow' => __( 'Candidate journey', 'wp-bbtheme-child' ),
        'title' => __( 'Make the next application a better-informed one.', 'wp-bbtheme-child' ),
        'text' => __( 'Search by the details that matter, build a useful profile and keep your applications organised.', 'wp-bbtheme-child' ),
        'buttonText' => __( 'Candidate dashboard', 'wp-bbtheme-child' ),
        'buttonUrl' => '/candidate-dashboard/',
        'image' => get_stylesheet_directory_uri() . '/assets/img/demo/candidate-interview.jpg',
    ),
    array(
        'type' => 'hero',
        'eyebrow' => __( 'Employer journey', 'wp-bbtheme-child' ),
        'title' => __( 'A hiring workspace built around the vacancy, not admin noise.', 'wp-bbtheme-child' ),
        'text' => __( 'Publish roles, review talent and keep employer activity connected to the live recruitment journey.', 'wp-bbtheme-child' ),
        'buttonText' => __( 'Post a job', 'wp-bbtheme-child' ),
        'buttonUrl' => '/post-a-job/',
        'image' => get_stylesheet_directory_uri() . '/assets/img/demo/employer-workspace.jpg',
    ),
);
$attrs = array(
    'slides' => $slides,
    'slidesPerView' => 1,
    'slidesTablet' => 1,
    'slidesMobile' => 1,
    'spaceBetween' => 0,
    'speed' => 700,
    'rewind' => true,
    'autoplay' => true,
    'autoplayDelay' => 6500,
    'pauseOnHover' => true,
    'effect' => 'slide',
    'demoStyle' => 'hero',
    'showPagination' => true,
    'showNavigation' => true,
);
?>
<!-- wp:wpbb/bootstrap-div {"containerClass":"container-fluid","utilityClasses":"wp-theme-sector-hero wpbb-jobs-hero px-0"} -->
<!-- wp:wpbb/bootstrap-div {"containerClass":"container","utilityClasses":"wpbb-v67-section-inner"} -->
<!-- wp:wpbb/row --><!-- wp:wpbb/column {"xs":12} --><?php echo '<!-- wp:wpbb/swiper ' . wp_json_encode( $attrs, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . ' /-->'; ?><!-- /wp:wpbb/column --><!-- /wp:wpbb/row -->
<!-- /wp:wpbb/bootstrap-div -->
<!-- /wp:wpbb/bootstrap-div -->
