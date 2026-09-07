<?php
/** Title: Candidate and employer pathways / Slug: wp-bbtheme-child/jobs-candidate-employer / Categories: wp-patterns-main, wp-theme-current / Description: Two BBuilder cards for the main integrated recruitment journeys. */
$candidate = wpbb_child_v62_icon_card(
    __( 'I am looking for work', 'wp-bbtheme-child' ),
    __( 'Search roles, build your candidate profile and keep applications organised from your dashboard.', 'wp-bbtheme-child' ),
    'wpbb-jobs-path-card', __( 'Candidate dashboard', 'wp-bbtheme-child' ), '/candidate-dashboard/'
);
$employer = wpbb_child_v62_icon_card(
    __( 'I am hiring', 'wp-bbtheme-child' ),
    __( 'Create an employer profile, publish opportunities and review applications in one recruitment workspace.', 'wp-bbtheme-child' ),
    'wpbb-jobs-path-card', __( 'Employer dashboard', 'wp-bbtheme-child' ), '/employer-dashboard/'
);
?>
<!-- wp:wpbb/bootstrap-div {"containerClass":"container-fluid","utilityClasses":"wpbb-jobs-section px-0"} -->
<!-- wp:wpbb/bootstrap-div {"containerClass":"container","utilityClasses":"wpbb-v67-section-inner"} -->
<!-- wp:wpbb/row {"gutterX":"gx-4","gutterY":"gy-4","customClasses":"wpbb-jobs-paths-row"} -->
<!-- wp:wpbb/column {"xs":12,"lg":6,"customClasses":"d-flex"} --><?php echo $candidate; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><!-- /wp:wpbb/column -->
<!-- wp:wpbb/column {"xs":12,"lg":6,"customClasses":"d-flex"} --><?php echo $employer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><!-- /wp:wpbb/column -->
<!-- /wp:wpbb/row -->
<!-- /wp:wpbb/bootstrap-div -->
<!-- /wp:wpbb/bootstrap-div -->
