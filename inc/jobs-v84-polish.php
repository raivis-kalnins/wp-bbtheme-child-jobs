<?php
/** Jobs v3.8.10.84 multilingual visual polish. */
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wpbb_jobs_v84_contact_page_content' ) ) {
    function wpbb_jobs_v84_contact_page_content( $profile ) {
        $c = (array) ( $profile['contact'] ?? array() );
        $hero_inner  = wpbb_child_v62_paragraph( (string) ( $profile['name'] ?? __( 'TalentBridge Jobs', 'wp-bbtheme-child' ) ), 'wp-theme-sector-eyebrow' );
        $hero_inner .= wpbb_child_v62_heading( __( 'Contact', 'wp-bbtheme-child' ), 1 );
        $hero_inner .= wpbb_child_v62_paragraph( __( 'Tell us whether you are looking for work, hiring, or need account support.', 'wp-bbtheme-child' ), 'wp-theme-sector-lead' );
        $hero = wpbb_child_v62_block( 'group', array( 'className' => 'wp-theme-inner-hero wpbb-jobs-contact-hero' ), '<div class="wp-block-group wp-theme-inner-hero wpbb-jobs-contact-hero">' . wpbb_child_v62_block( 'wpbb/row', array( 'containerClass' => 'container' ), wpbb_child_v62_block( 'wpbb/column', array( 'xs' => 12 ), $hero_inner ) ) . '</div>' );

        $cards = array(
            array( __( 'Email support', 'wp-bbtheme-child' ), (string) ( $c['email'] ?? 'hello@talentbridge.example' ) ),
            array( __( 'Call the team', 'wp-bbtheme-child' ), (string) ( $c['phone'] ?? '+44 20 7946 0280' ) ),
            array( __( 'Recruitment hub', 'wp-bbtheme-child' ), (string) ( $c['address'] ?? __( 'London, United Kingdom · remote recruitment across the UK', 'wp-bbtheme-child' ) ) ),
            array( __( 'Opening hours', 'wp-bbtheme-child' ), (string) ( $c['hours'] ?? __( 'Mon–Fri · 08:30–18:00', 'wp-bbtheme-child' ) ) ),
        );
        $method_inner  = wpbb_child_v62_paragraph( __( 'Contact the recruitment team', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
        $method_inner .= wpbb_child_v62_heading( __( 'Talk to the right person and get a useful response.', 'wp-bbtheme-child' ), 2 );
        $method_inner .= wpbb_child_v62_paragraph( __( 'Tell us whether you are looking for work, hiring, or need account support.', 'wp-bbtheme-child' ), 'wpbb-jobs-section-intro' );
        $method_inner .= wpbb_child_v62_cards_row( $cards, 'wpbb-jobs-contact-method', 4 );
        $methods = wpbb_child_v67_section( 'wpbb-jobs-contact-methods', $method_inner );

        $fields = array(
            array( 'type' => 'text', 'name' => 'name', 'label' => __( 'Name', 'wp-theme' ), 'required' => true, 'width' => 6, 'breakpoint' => 'md', 'placeholder' => __( 'Your name', 'wp-theme' ), 'step' => 1 ),
            array( 'type' => 'email', 'name' => 'email', 'label' => __( 'Email', 'wp-theme' ), 'required' => true, 'width' => 6, 'breakpoint' => 'md', 'placeholder' => __( 'you@example.com', 'wp-theme' ), 'step' => 1 ),
            array( 'type' => 'phone', 'name' => 'phone', 'label' => __( 'Phone', 'wp-theme' ), 'required' => false, 'width' => 6, 'breakpoint' => 'md', 'placeholder' => __( 'Optional', 'wp-theme' ), 'step' => 1 ),
            array( 'type' => 'select', 'name' => 'topic', 'label' => __( 'What can we help with?', 'wp-theme' ), 'required' => true, 'width' => 6, 'breakpoint' => 'md', 'placeholder' => __( 'Choose a topic', 'wp-theme' ), 'options' => implode( "\n", array( __( 'Candidate support', 'wp-bbtheme-child' ), __( 'Employer support', 'wp-bbtheme-child' ), __( 'Account support', 'wp-bbtheme-child' ), __( 'General enquiry', 'wp-bbtheme-child' ) ) ), 'step' => 1 ),
            array( 'type' => 'textarea', 'name' => 'message', 'label' => __( 'Message', 'wp-theme' ), 'required' => true, 'width' => 12, 'breakpoint' => 'md', 'placeholder' => __( 'Tell us a little about what you need', 'wp-theme' ), 'step' => 1 ),
        );
        $form = wpbb_child_v62_block( 'wpbb/dynamic-form', array(
            'showTitle' => true,
            'formTitle' => __( 'Send a recruitment enquiry', 'wp-bbtheme-child' ),
            'emailSubject' => __( 'TalentBridge recruitment enquiry', 'wp-bbtheme-child' ),
            'stylePreset' => 'soft',
            'labelPosition' => 'top',
            'gap' => 3,
            'fields' => $fields,
            'className' => 'wp-theme-contact-form wpbb-jobs-contact-form',
        ), '', true );
        $form_inner  = wpbb_child_v62_paragraph( __( 'Send a recruitment enquiry', 'wp-bbtheme-child' ), 'wp-theme-sector-eyebrow' );
        $form_inner .= wpbb_child_v62_heading( __( 'Tell us what you need help with.', 'wp-bbtheme-child' ), 2 );
        $form_inner .= wpbb_child_v62_paragraph( __( 'A clear message helps us route candidate, employer and account questions to the right place.', 'wp-bbtheme-child' ), 'wpbb-jobs-section-intro' );
        $form_inner .= $form;
        $form_section = wpbb_child_v67_section( 'wpbb-jobs-contact-form-section', $form_inner );

        return $hero . $methods . $form_section;
    }
}

/** Repair stale absolute theme-asset URLs left in managed Polylang pages. */
if ( ! function_exists( 'wpbb_jobs_v84_repair_theme_asset_urls' ) ) {
    function wpbb_jobs_v84_repair_theme_asset_urls( $content ) {
        if ( false === strpos( (string) $content, '/assets/img/' ) ) return $content;
        $base = trailingslashit( get_stylesheet_directory_uri() ) . 'assets/img/';
        return preg_replace_callback(
            '#https?://[^\"\'\s<>]+/wp-content/themes/[^/\"\'\s<>]+/assets/img/(demo|blog)/([^\"\'\s<>]+)#i',
            static function( $m ) use ( $base ) { return $base . $m[1] . '/' . ltrim( $m[2], '/' ); },
            (string) $content
        );
    }
}
add_filter( 'the_content', static function( $content ) {
    if ( is_admin() || ! is_singular( 'page' ) ) return $content;
    $id = get_queried_object_id();
    if ( ! $id ) return $content;
    $managed = get_post_meta( $id, '_wp_theme_demo_managed', true );
    $source  = get_post_meta( $id, '_wp_theme_demo_translation_source', true );
    if ( ! $managed && ! $source ) return $content;
    return wpbb_jobs_v84_repair_theme_asset_urls( $content );
}, 8 );
