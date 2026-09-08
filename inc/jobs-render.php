<?php
/** Frontend renderers, shortcodes and Gutenberg blocks for the integrated Jobs engine. */

defined( 'ABSPATH' ) || exit;

function wpbb_jobs_term_name( $post_id, $taxonomy ) {
    $terms = wp_get_post_terms( $post_id, $taxonomy );
    return ( $terms && ! is_wp_error( $terms ) ) ? ( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $terms[0]->name ) : $terms[0]->name ) : '';
}

function wpbb_jobs_term_id( $post_id, $taxonomy ) {
    $terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
    return ( $terms && ! is_wp_error( $terms ) ) ? (int) $terms[0] : 0;
}

function wpbb_jobs_company_badge( $company ) {
    if ( ! $company instanceof WP_Post ) return '';
    $thumb = get_the_post_thumbnail( $company->ID, 'thumbnail', array( 'class' => 'wpbb-jobs-company-logo', 'loading' => 'lazy' ) );
    if ( $thumb ) return $thumb;
    $initials = '';
    foreach ( preg_split( '/\s+/', trim( $company->post_title ) ) as $part ) {
        if ( '' !== $part ) $initials .= strtoupper( substr( $part, 0, 1 ) );
        if ( strlen( $initials ) >= 2 ) break;
    }
    return '<span class="wpbb-jobs-company-initials" aria-hidden="true">' . esc_html( $initials ?: 'HR' ) . '</span>';
}

function wpbb_jobs_search_form( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'compact' => false, 'action' => wpbb_jobs_page_url( 'jobs' ) ) );
    $keyword  = sanitize_text_field( wp_unslash( $_GET['job_keyword'] ?? '' ) );
    $location = absint( $_GET['job_location'] ?? 0 );
    $type     = absint( $_GET['job_type'] ?? 0 );
    $category = absint( $_GET['job_category'] ?? 0 );
    $locations = wpbb_jobs_get_terms_options( 'wpbb_job_location' );
    $types     = wpbb_jobs_get_terms_options( 'wpbb_job_type' );
    $categories= wpbb_jobs_get_terms_options( 'wpbb_job_category' );

    ob_start();
    ?>
    <form class="wpbb-jobs-search <?php echo ! empty( $atts['compact'] ) ? 'is-compact' : ''; ?>" method="get" action="<?php echo esc_url( $atts['action'] ); ?>">
        <div class="wpbb-jobs-search__field wpbb-jobs-search__field--keyword">
            <label for="wpbb-job-keyword"><?php esc_html_e( 'Role or keyword', 'wp-bbtheme-child' ); ?></label>
            <input id="wpbb-job-keyword" type="search" name="job_keyword" value="<?php echo esc_attr( $keyword ); ?>" placeholder="<?php esc_attr_e( 'e.g. Product designer', 'wp-bbtheme-child' ); ?>" />
        </div>
        <div class="wpbb-jobs-search__field">
            <label for="wpbb-job-location"><?php esc_html_e( 'Location', 'wp-bbtheme-child' ); ?></label>
            <select id="wpbb-job-location" name="job_location">
                <option value=""><?php esc_html_e( 'Any location', 'wp-bbtheme-child' ); ?></option>
                <?php foreach ( $locations as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $location, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="wpbb-jobs-search__field">
            <label for="wpbb-job-type"><?php esc_html_e( 'Job type', 'wp-bbtheme-child' ); ?></label>
            <select id="wpbb-job-type" name="job_type">
                <option value=""><?php esc_html_e( 'Any type', 'wp-bbtheme-child' ); ?></option>
                <?php foreach ( $types as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $type, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?>
            </select>
        </div>
        <?php if ( empty( $atts['compact'] ) ) : ?>
            <div class="wpbb-jobs-search__field">
                <label for="wpbb-job-category"><?php esc_html_e( 'Category', 'wp-bbtheme-child' ); ?></label>
                <select id="wpbb-job-category" name="job_category">
                    <option value=""><?php esc_html_e( 'Any category', 'wp-bbtheme-child' ); ?></option>
                    <?php foreach ( $categories as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $category, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <div class="wpbb-jobs-search__submit"><button class="wpbb-jobs-button" type="submit"><?php esc_html_e( 'Search jobs', 'wp-bbtheme-child' ); ?></button></div>
    </form>
    <?php
    return ob_get_clean();
}

function wpbb_jobs_query_args( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'per_page' => 12, 'featured' => false, 'paged' => true ) );
    $page = ! empty( $atts['paged'] ) ? max( 1, absint( get_query_var( 'paged' ) ?: ( $_GET['job_page'] ?? 1 ) ) ) : 1;
    $args = array(
        'post_type'      => 'wpbb_job',
        'post_status'    => 'publish',
        'posts_per_page' => max( 1, min( 50, absint( $atts['per_page'] ) ) ),
        'paged'          => $page,
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
    if ( ! empty( $atts['featured'] ) ) $args['meta_query'][] = array( 'key' => '_wpbb_job_featured', 'value' => 1, 'compare' => '=' );

    $keyword = sanitize_text_field( wp_unslash( $_GET['job_keyword'] ?? '' ) );
    if ( $keyword ) $args['s'] = $keyword;
    $tax_query = array();
    foreach ( array( 'wpbb_job_location' => 'job_location', 'wpbb_job_type' => 'job_type', 'wpbb_job_category' => 'job_category' ) as $taxonomy => $param ) {
        $term_id = absint( $_GET[ $param ] ?? 0 );
        if ( $term_id ) $tax_query[] = array( 'taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => array( $term_id ) );
    }
    if ( $tax_query ) {
        if ( count( $tax_query ) > 1 ) $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }
    return $args;
}

function wpbb_jobs_render_job_card( $job ) {
    if ( ! $job instanceof WP_Post ) return '';
    $company = wpbb_jobs_company_for_job( $job->ID );
    $location = wpbb_jobs_term_name( $job->ID, 'wpbb_job_location' );
    $type = wpbb_jobs_term_name( $job->ID, 'wpbb_job_type' );
    $salary = wpbb_jobs_salary_label( $job->ID );
    $remote = (bool) get_post_meta( $job->ID, '_wpbb_job_remote', true );
    $featured = (bool) get_post_meta( $job->ID, '_wpbb_job_featured', true );
    ob_start();
    ?>
    <article class="wpbb-jobs-card<?php echo $featured ? ' is-featured' : ''; ?>">
        <div class="wpbb-jobs-card__logo"><?php echo wpbb_jobs_company_badge( $company ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <div class="wpbb-jobs-card__body">
            <div class="wpbb-jobs-card__topline">
                <?php if ( $featured ) : ?><span class="wpbb-jobs-chip is-featured"><?php esc_html_e( 'Featured', 'wp-bbtheme-child' ); ?></span><?php endif; ?>
                <?php if ( $type ) : ?><span class="wpbb-jobs-chip"><?php echo esc_html( $type ); ?></span><?php endif; ?>
                <?php if ( $remote ) : ?><span class="wpbb-jobs-chip"><?php esc_html_e( 'Remote friendly', 'wp-bbtheme-child' ); ?></span><?php endif; ?>
            </div>
            <h3 class="wpbb-jobs-card__title"><a href="<?php echo esc_url( get_permalink( $job ) ); ?>"><?php echo esc_html( function_exists( 'wpbb_jobs_localized_post_title' ) ? wpbb_jobs_localized_post_title( $job ) : get_the_title( $job ) ); ?></a></h3>
            <p class="wpbb-jobs-card__company"><?php echo esc_html( $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' ) ); ?></p>
            <div class="wpbb-jobs-card__meta">
                <?php if ( $location ) : ?><span><?php echo esc_html( $location ); ?></span><?php endif; ?>
                <?php if ( $salary ) : ?><span><?php echo esc_html( $salary ); ?></span><?php endif; ?>
                <span><?php echo esc_html( sprintf( __( '%s ago', 'wp-bbtheme-child' ), human_time_diff( get_post_time( 'U', true, $job ), current_time( 'timestamp', true ) ) ) ); ?></span>
            </div>
            <?php $localized_excerpt = function_exists( 'wpbb_jobs_localized_post_excerpt' ) ? wpbb_jobs_localized_post_excerpt( $job ) : $job->post_excerpt; if ( $localized_excerpt ) : ?><p class="wpbb-jobs-card__excerpt"><?php echo esc_html( wp_trim_words( $localized_excerpt, 24 ) ); ?></p><?php endif; ?>
            <a class="wpbb-jobs-card__link" href="<?php echo esc_url( get_permalink( $job ) ); ?>"><?php esc_html_e( 'View job', 'wp-bbtheme-child' ); ?> <span aria-hidden="true">→</span></a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

function wpbb_jobs_jobs_list( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'per_page' => 12, 'columns' => 2, 'featured' => false, 'pagination' => true ) );
    $query = new WP_Query( wpbb_jobs_query_args( $atts ) );
    $columns = max( 1, min( 3, absint( $atts['columns'] ) ) );
    ob_start();
    echo wpbb_jobs_front_notice_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    if ( ! $query->have_posts() ) {
        echo '<div class="wpbb-jobs-empty">' . esc_html__( 'No open jobs matched those filters. Try a broader search.', 'wp-bbtheme-child' ) . '</div>';
    } else {
        echo '<div class="wpbb-jobs-grid wpbb-jobs-grid--' . esc_attr( $columns ) . '">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo wpbb_jobs_render_job_card( get_post() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        }
        echo '</div>';
        if ( ! empty( $atts['pagination'] ) && $query->max_num_pages > 1 ) {
            $current = max( 1, absint( $_GET['job_page'] ?? 1 ) );
            echo '<nav class="wpbb-jobs-pagination" aria-label="' . esc_attr__( 'Jobs pagination', 'wp-bbtheme-child' ) . '">';
            echo paginate_links( array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                'base' => str_replace( '999999999', '%#%', esc_url( add_query_arg( 'job_page', 999999999 ) ) ),
                'format' => '',
                'current' => $current,
                'total' => $query->max_num_pages,
                'prev_text' => '← ' . __( 'Previous', 'wp-bbtheme-child' ),
                'next_text' => __( 'Next', 'wp-bbtheme-child' ) . ' →',
            ) );
            echo '</nav>';
        }
    }
    wp_reset_postdata();
    return ob_get_clean();
}

function wpbb_jobs_render_company_card( $company ) {
    if ( ! $company instanceof WP_Post ) return '';
    $location = wpbb_jobs_term_name( $company->ID, 'wpbb_job_location' );
    $jobs = new WP_Query( array(
        'post_type' => 'wpbb_job', 'post_status' => 'publish', 'posts_per_page' => 1,
        'meta_query' => array( array( 'key' => '_wpbb_job_company_id', 'value' => $company->ID, 'compare' => '=' ) ),
        'fields' => 'ids',
    ) );
    $count = (int) $jobs->found_posts;
    ob_start();
    ?>
    <article class="wpbb-jobs-company-card">
        <div class="wpbb-jobs-company-card__logo"><?php echo wpbb_jobs_company_badge( $company ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <div>
            <h3><a href="<?php echo esc_url( get_permalink( $company ) ); ?>"><?php echo esc_html( $company->post_title ); ?></a></h3>
            <?php if ( $location ) : ?><p class="wpbb-jobs-company-card__location"><?php echo esc_html( $location ); ?></p><?php endif; ?>
            <p><?php $company_excerpt = function_exists( 'wpbb_jobs_localized_post_excerpt' ) ? wpbb_jobs_localized_post_excerpt( $company ) : $company->post_excerpt; echo esc_html( wp_trim_words( $company_excerpt ?: wp_strip_all_tags( $company->post_content ), 22 ) ); ?></p>
            <a class="wpbb-jobs-card__link" href="<?php echo esc_url( get_permalink( $company ) ); ?>"><?php echo esc_html( sprintf( _n( '%d open job', '%d open jobs', $count, 'wp-bbtheme-child' ), $count ) ); ?> →</a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

function wpbb_jobs_companies_list( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'per_page' => 6, 'columns' => 3 ) );
    $query = new WP_Query( array(
        'post_type' => 'wpbb_company', 'post_status' => 'publish', 'posts_per_page' => max( 1, min( 24, absint( $atts['per_page'] ) ) ), 'orderby' => 'title', 'order' => 'ASC',
    ) );
    $columns = max( 1, min( 4, absint( $atts['columns'] ) ) );
    ob_start();
    if ( ! $query->have_posts() ) {
        echo '<div class="wpbb-jobs-empty">' . esc_html__( 'Employer profiles will appear here as they are published.', 'wp-bbtheme-child' ) . '</div>';
    } else {
        echo '<div class="wpbb-jobs-company-grid wpbb-jobs-company-grid--' . esc_attr( $columns ) . '">';
        while ( $query->have_posts() ) { $query->the_post(); echo wpbb_jobs_render_company_card( get_post() ); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

function wpbb_jobs_resumes_list( $atts = array() ) {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) {
        return '<div class="wpbb-jobs-gate"><h3>' . esc_html__( 'Employer access', 'wp-bbtheme-child' ) . '</h3><p>' . esc_html__( 'Candidate profiles are available to signed-in employer accounts.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( wpbb_jobs_page_url( 'login-register' ) ) . '">' . esc_html__( 'Employer login', 'wp-bbtheme-child' ) . '</a></div>';
    }
    $atts = wp_parse_args( $atts, array( 'per_page' => 12, 'columns' => 2 ) );
    $query = new WP_Query( array( 'post_type' => 'wpbb_resume', 'post_status' => 'publish', 'posts_per_page' => max( 1, min( 30, absint( $atts['per_page'] ) ) ), 'orderby' => 'modified', 'order' => 'DESC' ) );
    $columns = max( 1, min( 3, absint( $atts['columns'] ) ) );
    ob_start();
    if ( ! $query->have_posts() ) {
        echo '<div class="wpbb-jobs-empty">' . esc_html__( 'No candidate profiles are available yet.', 'wp-bbtheme-child' ) . '</div>';
    } else {
        echo '<div class="wpbb-jobs-resume-grid wpbb-jobs-resume-grid--' . esc_attr( $columns ) . '">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $resume = get_post();
            $headline = get_post_meta( $resume->ID, '_wpbb_resume_headline', true ) ?: $resume->post_excerpt;
            $location = wpbb_jobs_term_name( $resume->ID, 'wpbb_job_location' );
            $skills = wp_get_post_terms( $resume->ID, 'wpbb_job_skill', array( 'fields' => 'names' ) );
            $file_id = absint( get_post_meta( $resume->ID, '_wpbb_resume_file_id', true ) );
            echo '<article class="wpbb-jobs-resume-card">';
            echo '<div class="wpbb-jobs-resume-card__avatar">' . esc_html( strtoupper( substr( $resume->post_title, 0, 1 ) ) ) . '</div>';
            echo '<h3>' . esc_html( $resume->post_title ) . '</h3>';
            if ( $headline ) echo '<p class="wpbb-jobs-resume-card__headline">' . esc_html( $headline ) . '</p>';
            if ( $location ) echo '<p class="wpbb-jobs-resume-card__meta">' . esc_html( $location ) . '</p>';
            if ( $skills && ! is_wp_error( $skills ) ) echo '<p class="wpbb-jobs-resume-card__skills">' . esc_html( implode( ' · ', array_slice( $skills, 0, 5 ) ) ) . '</p>';
            echo '<p>' . esc_html( wp_trim_words( wp_strip_all_tags( $resume->post_content ), 30 ) ) . '</p>';
            if ( $file_id && wpbb_jobs_is_employer() ) echo '<a class="wpbb-jobs-card__link" href="' . esc_url( wpbb_jobs_cv_download_url( $file_id ) ) . '">' . esc_html__( 'Download CV', 'wp-bbtheme-child' ) . ' →</a>';
            echo '</article>';
        }
        echo '</div>';
    }
    wp_reset_postdata();
    return ob_get_clean();
}

function wpbb_jobs_registration_form() {
    if ( is_user_logged_in() ) {
        $url = wpbb_jobs_is_employer() ? wpbb_jobs_page_url( 'employer-dashboard' ) : wpbb_jobs_page_url( 'candidate-dashboard' );
        return wpbb_jobs_front_notice_html() . '<div class="wpbb-jobs-gate"><p>' . esc_html__( 'You are signed in.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( $url ) . '">' . esc_html__( 'Open dashboard', 'wp-bbtheme-child' ) . '</a></div>';
    }
    ob_start();
    echo wpbb_jobs_front_notice_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
    <div class="wpbb-jobs-auth-grid">
        <section class="wpbb-jobs-panel">
            <h2><?php esc_html_e( 'Create an account', 'wp-bbtheme-child' ); ?></h2>
            <form class="wpbb-jobs-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="wpbb_jobs_register" />
                <?php wp_nonce_field( 'wpbb_jobs_register', 'wpbb_jobs_nonce' ); ?>
                <label><?php esc_html_e( 'I am', 'wp-bbtheme-child' ); ?><select name="account_type"><option value="candidate"><?php esc_html_e( 'looking for work', 'wp-bbtheme-child' ); ?></option><option value="employer"><?php esc_html_e( 'hiring', 'wp-bbtheme-child' ); ?></option></select></label>
                <label><?php esc_html_e( 'Name', 'wp-bbtheme-child' ); ?><input name="name" required /></label>
                <label><?php esc_html_e( 'Email', 'wp-bbtheme-child' ); ?><input type="email" name="email" required /></label>
                <label><?php esc_html_e( 'Password', 'wp-bbtheme-child' ); ?><input type="password" name="password" minlength="8" required autocomplete="new-password" /></label>
                <button class="wpbb-jobs-button" type="submit"><?php esc_html_e( 'Create account', 'wp-bbtheme-child' ); ?></button>
            </form>
        </section>
        <section class="wpbb-jobs-panel">
            <h2><?php esc_html_e( 'Sign in', 'wp-bbtheme-child' ); ?></h2>
            <?php wp_login_form( array( 'redirect' => wpbb_jobs_page_url( 'candidate-dashboard' ), 'remember' => true ) ); ?>
        </section>
    </div>
    <?php
    return ob_get_clean();
}

function wpbb_jobs_company_form( $company_id = 0 ) {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) return '<div class="wpbb-jobs-gate">' . esc_html__( 'Sign in with an employer account to manage a company profile.', 'wp-bbtheme-child' ) . '</div>';
    $company = $company_id ? get_post( $company_id ) : null;
    if ( $company && ! wpbb_jobs_can_edit_owned_post( $company_id, 'wpbb_company' ) ) $company = null;
    $location_id = $company ? wpbb_jobs_term_id( $company->ID, 'wpbb_job_location' ) : 0;
    $locations = wpbb_jobs_get_terms_options( 'wpbb_job_location' );
    ob_start(); ?>
    <form class="wpbb-jobs-form wpbb-jobs-panel" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="wpbb_jobs_company_save" /><input type="hidden" name="company_id" value="<?php echo esc_attr( $company ? $company->ID : 0 ); ?>" />
        <?php wp_nonce_field( 'wpbb_jobs_company_save', 'wpbb_jobs_nonce' ); ?>
        <h2><?php echo esc_html( $company ? __( 'Edit company', 'wp-bbtheme-child' ) : __( 'Create company profile', 'wp-bbtheme-child' ) ); ?></h2>
        <label><?php esc_html_e( 'Company name', 'wp-bbtheme-child' ); ?><input name="company_name" required value="<?php echo esc_attr( $company ? $company->post_title : '' ); ?>" /></label>
        <label><?php esc_html_e( 'Short introduction', 'wp-bbtheme-child' ); ?><textarea name="company_excerpt" rows="3"><?php echo esc_textarea( $company ? $company->post_excerpt : '' ); ?></textarea></label>
        <label><?php esc_html_e( 'About the company', 'wp-bbtheme-child' ); ?><textarea name="company_description" rows="7"><?php echo esc_textarea( $company ? $company->post_content : '' ); ?></textarea></label>
        <div class="wpbb-jobs-form__grid">
            <label><?php esc_html_e( 'Website', 'wp-bbtheme-child' ); ?><input type="url" name="company_website" value="<?php echo esc_attr( $company ? get_post_meta( $company->ID, '_wpbb_company_website', true ) : '' ); ?>" /></label>
            <label><?php esc_html_e( 'Hiring email', 'wp-bbtheme-child' ); ?><input type="email" name="company_email" value="<?php echo esc_attr( $company ? get_post_meta( $company->ID, '_wpbb_company_email', true ) : wp_get_current_user()->user_email ); ?>" /></label>
            <label><?php esc_html_e( 'Phone', 'wp-bbtheme-child' ); ?><input name="company_phone" value="<?php echo esc_attr( $company ? get_post_meta( $company->ID, '_wpbb_company_phone', true ) : '' ); ?>" /></label>
            <label><?php esc_html_e( 'Location', 'wp-bbtheme-child' ); ?><select name="company_location"><option value=""><?php esc_html_e( 'Choose location', 'wp-bbtheme-child' ); ?></option><?php foreach ( $locations as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $location_id, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
        </div>
        <button class="wpbb-jobs-button" type="submit"><?php esc_html_e( 'Save company', 'wp-bbtheme-child' ); ?></button>
    </form><?php
    return ob_get_clean();
}

function wpbb_jobs_job_form( $job_id = 0 ) {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) return '<div class="wpbb-jobs-gate"><p>' . esc_html__( 'Sign in with an employer account to post jobs.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( wpbb_jobs_page_url( 'login-register' ) ) . '">' . esc_html__( 'Employer login', 'wp-bbtheme-child' ) . '</a></div>';
    $job = $job_id ? get_post( $job_id ) : null;
    if ( $job && ! wpbb_jobs_can_edit_owned_post( $job_id, 'wpbb_job' ) ) $job = null;
    $companies = wpbb_jobs_get_user_company_ids();
    if ( ! $companies && ! wpbb_jobs_is_admin_user() ) return wpbb_jobs_front_notice_html() . '<div class="wpbb-jobs-gate"><h3>' . esc_html__( 'Create your company first', 'wp-bbtheme-child' ) . '</h3><p>' . esc_html__( 'A job belongs to an employer profile, so add your company before publishing a vacancy.', 'wp-bbtheme-child' ) . '</p>' . wpbb_jobs_company_form() . '</div>';
    if ( wpbb_jobs_is_admin_user() ) $companies = get_posts( array( 'post_type' => 'wpbb_company', 'post_status' => array( 'publish', 'pending', 'draft' ), 'posts_per_page' => -1, 'fields' => 'ids', 'orderby' => 'title', 'order' => 'ASC' ) );
    $company_id = $job ? absint( get_post_meta( $job->ID, '_wpbb_job_company_id', true ) ) : 0;
    $category_id = $job ? wpbb_jobs_term_id( $job->ID, 'wpbb_job_category' ) : 0;
    $type_id = $job ? wpbb_jobs_term_id( $job->ID, 'wpbb_job_type' ) : 0;
    $location_id = $job ? wpbb_jobs_term_id( $job->ID, 'wpbb_job_location' ) : 0;
    ob_start(); echo wpbb_jobs_front_notice_html(); ?>
    <form class="wpbb-jobs-form wpbb-jobs-panel" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="wpbb_jobs_job_save" /><input type="hidden" name="job_id" value="<?php echo esc_attr( $job ? $job->ID : 0 ); ?>" />
        <?php wp_nonce_field( 'wpbb_jobs_job_save', 'wpbb_jobs_nonce' ); ?>
        <h2><?php echo esc_html( $job ? __( 'Edit job', 'wp-bbtheme-child' ) : __( 'Post a job', 'wp-bbtheme-child' ) ); ?></h2>
        <label><?php esc_html_e( 'Job title', 'wp-bbtheme-child' ); ?><input name="job_title" required value="<?php echo esc_attr( $job ? $job->post_title : '' ); ?>" /></label>
        <div class="wpbb-jobs-form__grid">
            <label><?php esc_html_e( 'Company', 'wp-bbtheme-child' ); ?><select name="company_id" required><option value=""><?php esc_html_e( 'Choose company', 'wp-bbtheme-child' ); ?></option><?php foreach ( $companies as $id ) : ?><option value="<?php echo esc_attr( $id ); ?>" <?php selected( $company_id, $id ); ?>><?php echo esc_html( get_the_title( $id ) ); ?></option><?php endforeach; ?></select></label>
            <label><?php esc_html_e( 'Category', 'wp-bbtheme-child' ); ?><select name="job_category"><option value=""><?php esc_html_e( 'Choose category', 'wp-bbtheme-child' ); ?></option><?php foreach ( wpbb_jobs_get_terms_options( 'wpbb_job_category' ) as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $category_id, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
            <label><?php esc_html_e( 'Job type', 'wp-bbtheme-child' ); ?><select name="job_type"><option value=""><?php esc_html_e( 'Choose type', 'wp-bbtheme-child' ); ?></option><?php foreach ( wpbb_jobs_get_terms_options( 'wpbb_job_type' ) as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $type_id, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
            <label><?php esc_html_e( 'Location', 'wp-bbtheme-child' ); ?><select name="job_location"><option value=""><?php esc_html_e( 'Choose location', 'wp-bbtheme-child' ); ?></option><?php foreach ( wpbb_jobs_get_terms_options( 'wpbb_job_location' ) as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $location_id, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
            <label><?php esc_html_e( 'Salary from', 'wp-bbtheme-child' ); ?><input type="number" step="1" name="salary_min" value="<?php echo esc_attr( $job ? get_post_meta( $job->ID, '_wpbb_job_salary_min', true ) : '' ); ?>" /></label>
            <label><?php esc_html_e( 'Salary to', 'wp-bbtheme-child' ); ?><input type="number" step="1" name="salary_max" value="<?php echo esc_attr( $job ? get_post_meta( $job->ID, '_wpbb_job_salary_max', true ) : '' ); ?>" /></label>
            <label><?php esc_html_e( 'Currency', 'wp-bbtheme-child' ); ?><input name="currency" maxlength="3" value="<?php echo esc_attr( $job ? ( get_post_meta( $job->ID, '_wpbb_job_currency', true ) ?: wpbb_jobs_setting( 'currency', 'GBP' ) ) : wpbb_jobs_setting( 'currency', 'GBP' ) ); ?>" /></label>
            <label><?php esc_html_e( 'Application deadline', 'wp-bbtheme-child' ); ?><input type="date" name="deadline" value="<?php echo esc_attr( $job ? get_post_meta( $job->ID, '_wpbb_job_deadline', true ) : '' ); ?>" /></label>
        </div>
        <?php if ( $job ) : ?><label><?php esc_html_e( 'Job status', 'wp-bbtheme-child' ); ?><select name="job_status"><option value="open" <?php selected( get_post_meta( $job->ID, '_wpbb_job_status', true ) ?: 'open', 'open' ); ?>><?php esc_html_e( 'Open for applications', 'wp-bbtheme-child' ); ?></option><option value="closed" <?php selected( get_post_meta( $job->ID, '_wpbb_job_status', true ), 'closed' ); ?>><?php esc_html_e( 'Closed', 'wp-bbtheme-child' ); ?></option></select></label><?php else : ?><input type="hidden" name="job_status" value="open" /><?php endif; ?>
        <label class="wpbb-jobs-check"><input type="checkbox" name="remote" value="1" <?php checked( $job ? get_post_meta( $job->ID, '_wpbb_job_remote', true ) : false ); ?> /> <?php esc_html_e( 'Remote or hybrid working is available', 'wp-bbtheme-child' ); ?></label>
        <label><?php esc_html_e( 'Short summary', 'wp-bbtheme-child' ); ?><textarea name="job_excerpt" rows="3"><?php echo esc_textarea( $job ? $job->post_excerpt : '' ); ?></textarea></label>
        <label><?php esc_html_e( 'Job description', 'wp-bbtheme-child' ); ?><textarea name="job_description" rows="12" required><?php echo esc_textarea( $job ? $job->post_content : '' ); ?></textarea></label>
        <button class="wpbb-jobs-button" type="submit"><?php echo esc_html( wpbb_jobs_setting( 'job_approval', 1 ) && ! wpbb_jobs_is_admin_user() ? __( 'Submit job for review', 'wp-bbtheme-child' ) : __( 'Publish job', 'wp-bbtheme-child' ) ); ?></button>
    </form><?php
    return ob_get_clean();
}

function wpbb_jobs_resume_form() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_candidate() ) return '<div class="wpbb-jobs-gate"><p>' . esc_html__( 'Sign in with a candidate account to create your profile.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( wpbb_jobs_page_url( 'login-register' ) ) . '">' . esc_html__( 'Candidate login', 'wp-bbtheme-child' ) . '</a></div>';
    $resume = wpbb_jobs_get_user_resume();
    $category_id = $resume ? wpbb_jobs_term_id( $resume->ID, 'wpbb_job_category' ) : 0;
    $location_id = $resume ? wpbb_jobs_term_id( $resume->ID, 'wpbb_job_location' ) : 0;
    $skills = $resume ? wp_get_post_terms( $resume->ID, 'wpbb_job_skill', array( 'fields' => 'names' ) ) : array();
    ob_start(); echo wpbb_jobs_front_notice_html(); ?>
    <form class="wpbb-jobs-form wpbb-jobs-panel" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="wpbb_jobs_resume_save" /><?php wp_nonce_field( 'wpbb_jobs_resume_save', 'wpbb_jobs_nonce' ); ?>
        <h2><?php esc_html_e( 'Candidate profile', 'wp-bbtheme-child' ); ?></h2>
        <div class="wpbb-jobs-form__grid">
            <label><?php esc_html_e( 'Name', 'wp-bbtheme-child' ); ?><input name="candidate_name" required value="<?php echo esc_attr( $resume ? $resume->post_title : wp_get_current_user()->display_name ); ?>" /></label>
            <label><?php esc_html_e( 'Professional headline', 'wp-bbtheme-child' ); ?><input name="headline" required value="<?php echo esc_attr( $resume ? get_post_meta( $resume->ID, '_wpbb_resume_headline', true ) : '' ); ?>" /></label>
            <label><?php esc_html_e( 'Phone', 'wp-bbtheme-child' ); ?><input name="phone" value="<?php echo esc_attr( $resume ? get_post_meta( $resume->ID, '_wpbb_resume_phone', true ) : '' ); ?>" /></label>
            <label><?php esc_html_e( 'Availability', 'wp-bbtheme-child' ); ?><input name="availability" value="<?php echo esc_attr( $resume ? get_post_meta( $resume->ID, '_wpbb_resume_availability', true ) : '' ); ?>" placeholder="<?php esc_attr_e( 'e.g. 1 month notice', 'wp-bbtheme-child' ); ?>" /></label>
            <label><?php esc_html_e( 'Primary discipline', 'wp-bbtheme-child' ); ?><select name="resume_category"><option value=""><?php esc_html_e( 'Choose category', 'wp-bbtheme-child' ); ?></option><?php foreach ( wpbb_jobs_get_terms_options( 'wpbb_job_category' ) as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $category_id, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
            <label><?php esc_html_e( 'Location', 'wp-bbtheme-child' ); ?><select name="resume_location"><option value=""><?php esc_html_e( 'Choose location', 'wp-bbtheme-child' ); ?></option><?php foreach ( wpbb_jobs_get_terms_options( 'wpbb_job_location' ) as $term ) : ?><option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $location_id, $term->term_id ); ?>><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></option><?php endforeach; ?></select></label>
        </div>
        <label><?php esc_html_e( 'Skills (comma separated)', 'wp-bbtheme-child' ); ?><input name="skills" value="<?php echo esc_attr( ( $skills && ! is_wp_error( $skills ) ) ? implode( ', ', $skills ) : '' ); ?>" /></label>
        <label><?php esc_html_e( 'Profile summary / experience', 'wp-bbtheme-child' ); ?><textarea name="resume_summary" rows="10"><?php echo esc_textarea( $resume ? $resume->post_content : '' ); ?></textarea></label>
        <label><?php esc_html_e( 'CV file (PDF, DOC or DOCX)', 'wp-bbtheme-child' ); ?><input type="file" name="resume_file" accept=".pdf,.doc,.docx" /></label>
        <button class="wpbb-jobs-button" type="submit"><?php esc_html_e( 'Save candidate profile', 'wp-bbtheme-child' ); ?></button>
    </form><?php
    return ob_get_clean();
}

function wpbb_jobs_apply_form( $job_id ) {
    if ( ! wpbb_jobs_job_is_open( $job_id ) ) return '<div class="wpbb-jobs-empty">' . esc_html__( 'Applications for this role are closed.', 'wp-bbtheme-child' ) . '</div>';
    $logged_in = is_user_logged_in();
    if ( $logged_in && ! wpbb_jobs_is_candidate() ) return '<div class="wpbb-jobs-gate">' . esc_html__( 'Applications must be submitted from a candidate account.', 'wp-bbtheme-child' ) . '</div>';
    if ( ! $logged_in && ! wpbb_jobs_setting( 'guest_apply', 1 ) ) return '<div class="wpbb-jobs-gate"><p>' . esc_html__( 'Create a candidate account to apply.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( wpbb_jobs_page_url( 'login-register' ) ) . '">' . esc_html__( 'Create account / sign in', 'wp-bbtheme-child' ) . '</a></div>';
    $resume = $logged_in ? wpbb_jobs_get_user_resume() : null;
    ob_start(); echo wpbb_jobs_front_notice_html(); ?>
    <form class="wpbb-jobs-form wpbb-jobs-apply wpbb-jobs-panel" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="wpbb_jobs_apply" /><input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>" />
        <?php wp_nonce_field( 'wpbb_jobs_apply_' . $job_id, 'wpbb_jobs_nonce' ); ?>
        <h2><?php esc_html_e( 'Apply for this job', 'wp-bbtheme-child' ); ?></h2>
        <?php if ( $logged_in ) : ?><p class="wpbb-jobs-form__hint"><?php echo esc_html( $resume ? __( 'Your saved candidate profile will be attached to this application.', 'wp-bbtheme-child' ) : __( 'You can apply now and create a fuller candidate profile afterwards.', 'wp-bbtheme-child' ) ); ?></p><?php endif; ?>
        <?php if ( ! $logged_in ) : ?><div class="wpbb-jobs-form__grid"><label><?php esc_html_e( 'Name', 'wp-bbtheme-child' ); ?><input name="applicant_name" required /></label><label><?php esc_html_e( 'Email', 'wp-bbtheme-child' ); ?><input type="email" name="applicant_email" required /></label></div><?php endif; ?>
        <label><?php esc_html_e( 'Phone', 'wp-bbtheme-child' ); ?><input name="applicant_phone" /></label>
        <label><?php esc_html_e( 'Message to the hiring team', 'wp-bbtheme-child' ); ?><textarea name="application_message" rows="6"></textarea></label>
        <label><?php echo esc_html( $resume ? __( 'Replace attached CV for this application (optional)', 'wp-bbtheme-child' ) : __( 'CV file (PDF, DOC or DOCX)', 'wp-bbtheme-child' ) ); ?><input type="file" name="cv_file" accept=".pdf,.doc,.docx" /></label>
        <button class="wpbb-jobs-button" type="submit"><?php esc_html_e( 'Send application', 'wp-bbtheme-child' ); ?></button>
    </form><?php
    return ob_get_clean();
}

function wpbb_jobs_candidate_dashboard() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_candidate() ) return '<div class="wpbb-jobs-gate"><p>' . esc_html__( 'Sign in with a candidate account to open your dashboard.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( wpbb_jobs_page_url( 'login-register' ) ) . '">' . esc_html__( 'Candidate login', 'wp-bbtheme-child' ) . '</a></div>';
    $user_id = get_current_user_id();
    $resume = wpbb_jobs_get_user_resume( $user_id );
    $applications = get_posts( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => 50, 'meta_key' => '_wpbb_application_candidate_id', 'meta_value' => $user_id, 'orderby' => 'date', 'order' => 'DESC' ) );
    $statuses = wpbb_jobs_application_statuses();
    ob_start(); echo wpbb_jobs_front_notice_html(); ?>
    <div class="wpbb-jobs-dashboard">
        <div class="wpbb-jobs-dashboard__stats"><div><strong><?php echo esc_html( count( $applications ) ); ?></strong><span><?php esc_html_e( 'Applications', 'wp-bbtheme-child' ); ?></span></div><div><strong><?php echo esc_html( $resume ? '✓' : '—' ); ?></strong><span><?php esc_html_e( 'Candidate profile', 'wp-bbtheme-child' ); ?></span></div><div><strong><?php echo esc_html( count( array_filter( $applications, static function( $app ) { return in_array( get_post_meta( $app->ID, '_wpbb_application_status', true ), array( 'shortlisted', 'interview', 'offered', 'hired' ), true ); } ) ) ); ?></strong><span><?php esc_html_e( 'Progressing', 'wp-bbtheme-child' ); ?></span></div></div>
        <div class="wpbb-jobs-dashboard__actions"><a class="wpbb-jobs-button" href="<?php echo esc_url( wpbb_jobs_page_url( 'jobs' ) ); ?>"><?php esc_html_e( 'Search jobs', 'wp-bbtheme-child' ); ?></a><a class="wpbb-jobs-button is-secondary" href="<?php echo esc_url( wpbb_jobs_page_url( 'create-resume' ) ); ?>"><?php echo esc_html( $resume ? __( 'Update profile', 'wp-bbtheme-child' ) : __( 'Create profile', 'wp-bbtheme-child' ) ); ?></a></div>
        <section class="wpbb-jobs-panel"><h2><?php esc_html_e( 'Your applications', 'wp-bbtheme-child' ); ?></h2>
            <?php if ( ! $applications ) : ?><p><?php esc_html_e( 'You have not applied for a job yet.', 'wp-bbtheme-child' ); ?></p><?php else : ?><div class="wpbb-jobs-table-wrap"><table class="wpbb-jobs-table"><thead><tr><th><?php esc_html_e( 'Job', 'wp-bbtheme-child' ); ?></th><th><?php esc_html_e( 'Applied', 'wp-bbtheme-child' ); ?></th><th><?php esc_html_e( 'Status', 'wp-bbtheme-child' ); ?></th><th><?php esc_html_e( 'Action', 'wp-bbtheme-child' ); ?></th></tr></thead><tbody><?php foreach ( $applications as $app ) : $job_id = absint( get_post_meta( $app->ID, '_wpbb_application_job_id', true ) ); $status = get_post_meta( $app->ID, '_wpbb_application_status', true ) ?: 'new'; ?><tr><td><a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>"><?php echo esc_html( get_the_title( $job_id ) ); ?></a></td><td><?php echo esc_html( get_the_date( '', $app ) ); ?></td><td><span class="wpbb-jobs-status is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $statuses[ $status ] ?? ucfirst( $status ) ); ?></span></td><td><?php if ( ! in_array( $status, array( 'hired', 'rejected', 'withdrawn' ), true ) ) : ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="wpbb_jobs_withdraw_application" /><input type="hidden" name="application_id" value="<?php echo esc_attr( $app->ID ); ?>" /><?php wp_nonce_field( 'wpbb_jobs_withdraw_' . $app->ID, 'wpbb_jobs_nonce' ); ?><button class="wpbb-jobs-link-button" type="submit"><?php esc_html_e( 'Withdraw', 'wp-bbtheme-child' ); ?></button></form><?php else : ?>—<?php endif; ?></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?>
        </section>
    </div><?php
    return ob_get_clean();
}

function wpbb_jobs_employer_dashboard() {
    if ( ! is_user_logged_in() || ! wpbb_jobs_is_employer() ) return '<div class="wpbb-jobs-gate"><p>' . esc_html__( 'Sign in with an employer account to open the hiring dashboard.', 'wp-bbtheme-child' ) . '</p><a class="wpbb-jobs-button" href="' . esc_url( wpbb_jobs_page_url( 'login-register' ) ) . '">' . esc_html__( 'Employer login', 'wp-bbtheme-child' ) . '</a></div>';
    $user_id = get_current_user_id();
    $company_ids = wpbb_jobs_is_admin_user() ? get_posts( array( 'post_type' => 'wpbb_company', 'post_status' => array( 'publish', 'pending' ), 'posts_per_page' => -1, 'fields' => 'ids' ) ) : wpbb_jobs_get_user_company_ids( $user_id );
    $jobs = get_posts( array( 'post_type' => 'wpbb_job', 'post_status' => array( 'publish', 'pending', 'draft' ), 'author' => wpbb_jobs_is_admin_user() ? 0 : $user_id, 'posts_per_page' => 100, 'orderby' => 'date', 'order' => 'DESC' ) );
    $job_ids = wp_list_pluck( $jobs, 'ID' );
    $applications = array();
    if ( $job_ids ) {
        $applications = get_posts( array( 'post_type' => 'wpbb_application', 'post_status' => 'publish', 'posts_per_page' => 100, 'meta_query' => array( array( 'key' => '_wpbb_application_job_id', 'value' => $job_ids, 'compare' => 'IN' ) ), 'orderby' => 'date', 'order' => 'DESC' ) );
    }
    $statuses = wpbb_jobs_application_statuses();
    ob_start(); echo wpbb_jobs_front_notice_html(); ?>
    <div class="wpbb-jobs-dashboard">
        <div class="wpbb-jobs-dashboard__stats"><div><strong><?php echo esc_html( count( $jobs ) ); ?></strong><span><?php esc_html_e( 'Jobs', 'wp-bbtheme-child' ); ?></span></div><div><strong><?php echo esc_html( count( $applications ) ); ?></strong><span><?php esc_html_e( 'Applications', 'wp-bbtheme-child' ); ?></span></div><div><strong><?php echo esc_html( count( $company_ids ) ); ?></strong><span><?php esc_html_e( 'Companies', 'wp-bbtheme-child' ); ?></span></div></div>
        <div class="wpbb-jobs-dashboard__actions"><a class="wpbb-jobs-button" href="<?php echo esc_url( wpbb_jobs_page_url( 'post-a-job' ) ); ?>"><?php esc_html_e( 'Post a job', 'wp-bbtheme-child' ); ?></a><a class="wpbb-jobs-button is-secondary" href="<?php echo esc_url( wpbb_jobs_page_url( 'find-candidates' ) ); ?>"><?php esc_html_e( 'Find candidates', 'wp-bbtheme-child' ); ?></a></div>
        <div class="wpbb-jobs-dashboard__columns"><section class="wpbb-jobs-panel"><h2><?php esc_html_e( 'Company profile', 'wp-bbtheme-child' ); ?></h2><?php echo wpbb_jobs_company_form( $company_ids ? (int) $company_ids[0] : 0 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></section><section class="wpbb-jobs-panel"><h2><?php esc_html_e( 'Your jobs', 'wp-bbtheme-child' ); ?></h2><?php if ( ! $jobs ) : ?><p><?php esc_html_e( 'No jobs posted yet.', 'wp-bbtheme-child' ); ?></p><?php else : ?><ul class="wpbb-jobs-dashboard-list"><?php foreach ( array_slice( $jobs, 0, 10 ) as $job ) : ?><li><div><a href="<?php echo esc_url( 'publish' === $job->post_status ? get_permalink( $job ) : wpbb_jobs_page_url( 'post-a-job' ) . '?edit_job=' . $job->ID ); ?>"><?php echo esc_html( $job->post_title ); ?></a><small><?php echo esc_html( ucfirst( $job->post_status ) ); ?></small></div><a href="<?php echo esc_url( add_query_arg( 'edit_job', $job->ID, wpbb_jobs_page_url( 'post-a-job' ) ) ); ?>"><?php esc_html_e( 'Edit', 'wp-bbtheme-child' ); ?></a></li><?php endforeach; ?></ul><?php endif; ?></section></div>
        <section class="wpbb-jobs-panel"><h2><?php esc_html_e( 'Recent applications', 'wp-bbtheme-child' ); ?></h2><?php if ( ! $applications ) : ?><p><?php esc_html_e( 'Applications will appear here when candidates apply.', 'wp-bbtheme-child' ); ?></p><?php else : ?><div class="wpbb-jobs-table-wrap"><table class="wpbb-jobs-table"><thead><tr><th><?php esc_html_e( 'Candidate', 'wp-bbtheme-child' ); ?></th><th><?php esc_html_e( 'Job', 'wp-bbtheme-child' ); ?></th><th><?php esc_html_e( 'Status', 'wp-bbtheme-child' ); ?></th><th><?php esc_html_e( 'Update', 'wp-bbtheme-child' ); ?></th></tr></thead><tbody><?php foreach ( array_slice( $applications, 0, 20 ) as $app ) : $job_id = absint( get_post_meta( $app->ID, '_wpbb_application_job_id', true ) ); $status = get_post_meta( $app->ID, '_wpbb_application_status', true ) ?: 'new'; ?><tr><td><?php echo esc_html( get_post_meta( $app->ID, '_wpbb_application_name', true ) ); ?><small><?php echo esc_html( get_post_meta( $app->ID, '_wpbb_application_email', true ) ); ?></small><?php $cv_id = absint( get_post_meta( $app->ID, '_wpbb_application_cv_id', true ) ); if ( $cv_id ) : ?><small><a href="<?php echo esc_url( wpbb_jobs_cv_download_url( $cv_id ) ); ?>"><?php esc_html_e( 'Download CV', 'wp-bbtheme-child' ); ?></a></small><?php endif; ?></td><td><?php echo esc_html( get_the_title( $job_id ) ); ?></td><td><span class="wpbb-jobs-status is-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $statuses[ $status ] ?? ucfirst( $status ) ); ?></span></td><td><form class="wpbb-jobs-status-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="wpbb_jobs_application_status" /><input type="hidden" name="application_id" value="<?php echo esc_attr( $app->ID ); ?>" /><?php wp_nonce_field( 'wpbb_jobs_application_status_' . $app->ID, 'wpbb_jobs_nonce' ); ?><select name="application_status"><?php foreach ( $statuses as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select><button type="submit" class="wpbb-jobs-link-button"><?php esc_html_e( 'Save', 'wp-bbtheme-child' ); ?></button></form></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
    </div><?php
    return ob_get_clean();
}

function wpbb_jobs_shortcodes_init() {
    add_shortcode( 'wpbb_jobs_search', static function( $atts ) { return wpbb_jobs_search_form( shortcode_atts( array( 'compact' => 0 ), $atts ) ); } );
    add_shortcode( 'wpbb_jobs_list', static function( $atts ) { return wpbb_jobs_jobs_list( shortcode_atts( array( 'per_page' => 12, 'columns' => 2, 'featured' => 0 ), $atts ) ); } );
    add_shortcode( 'wpbb_jobs_companies', static function( $atts ) { return wpbb_jobs_companies_list( shortcode_atts( array( 'per_page' => 6, 'columns' => 3 ), $atts ) ); } );
    add_shortcode( 'wpbb_jobs_resumes', static function( $atts ) { return wpbb_jobs_resumes_list( shortcode_atts( array( 'per_page' => 12, 'columns' => 2 ), $atts ) ); } );
    add_shortcode( 'wpbb_candidate_dashboard', static function() { return wpbb_jobs_candidate_dashboard(); } );
    add_shortcode( 'wpbb_employer_dashboard', static function() { return wpbb_jobs_employer_dashboard(); } );
    add_shortcode( 'wpbb_post_job', static function() { return wpbb_jobs_job_form( absint( $_GET['edit_job'] ?? 0 ) ); } );
    add_shortcode( 'wpbb_resume_form', static function() { return wpbb_jobs_resume_form(); } );
    add_shortcode( 'wpbb_jobs_login_register', static function() { return wpbb_jobs_registration_form(); } );
}
add_action( 'init', 'wpbb_jobs_shortcodes_init', 20 );

function wpbb_jobs_register_blocks() {
    $script_path = get_stylesheet_directory() . '/assets/jobs-blocks.js';
    wp_register_script(
        'wpbb-jobs-blocks',
        get_stylesheet_directory_uri() . '/assets/jobs-blocks.js',
        array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-i18n', 'wp-server-side-render' ),
        file_exists( $script_path ) ? (string) filemtime( $script_path ) : WPBB_JOBS_VERSION,
        true
    );
    $common = array( 'api_version' => 2, 'editor_script' => 'wpbb-jobs-blocks' );
    register_block_type( 'wpbb-jobs/search', array_merge( $common, array(
        'attributes' => array( 'compact' => array( 'type' => 'boolean', 'default' => false ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_search_form( $attrs ); },
    ) ) );
    register_block_type( 'wpbb-jobs/list', array_merge( $common, array(
        'attributes' => array( 'perPage' => array( 'type' => 'number', 'default' => 8 ), 'columns' => array( 'type' => 'number', 'default' => 2 ), 'featured' => array( 'type' => 'boolean', 'default' => false ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_jobs_list( array( 'per_page' => $attrs['perPage'] ?? 8, 'columns' => $attrs['columns'] ?? 2, 'featured' => ! empty( $attrs['featured'] ), 'pagination' => false ) ); },
    ) ) );
    register_block_type( 'wpbb-jobs/companies', array_merge( $common, array(
        'attributes' => array( 'perPage' => array( 'type' => 'number', 'default' => 6 ), 'columns' => array( 'type' => 'number', 'default' => 3 ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_companies_list( array( 'per_page' => $attrs['perPage'] ?? 6, 'columns' => $attrs['columns'] ?? 3 ) ); },
    ) ) );
    register_block_type( 'wpbb-jobs/resumes', array_merge( $common, array(
        'attributes' => array( 'perPage' => array( 'type' => 'number', 'default' => 6 ), 'columns' => array( 'type' => 'number', 'default' => 2 ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_resumes_list( array( 'per_page' => $attrs['perPage'] ?? 6, 'columns' => $attrs['columns'] ?? 2 ) ); },
    ) ) );
}
add_action( 'init', 'wpbb_jobs_register_blocks', 30 );

function wpbb_jobs_single_content( $content ) {
    if ( ! wpbb_jobs_single_content_context_ok() ) return $content;
    if ( is_singular( 'wpbb_job' ) ) {
        $job_id = get_the_ID();
        $company = wpbb_jobs_company_for_job( $job_id );
        $location = wpbb_jobs_term_name( $job_id, 'wpbb_job_location' );
        $type = wpbb_jobs_term_name( $job_id, 'wpbb_job_type' );
        $category = wpbb_jobs_term_name( $job_id, 'wpbb_job_category' );
        $salary = wpbb_jobs_salary_label( $job_id );
        $deadline = get_post_meta( $job_id, '_wpbb_job_deadline', true );
        $remote = (bool) get_post_meta( $job_id, '_wpbb_job_remote', true );
        ob_start(); ?>
        <div class="wpbb-jobs-single-meta wpbb-jobs-panel">
            <div class="wpbb-jobs-single-meta__company"><?php echo wpbb_jobs_company_badge( $company ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><div><strong><?php echo esc_html( $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' ) ); ?></strong><?php if ( $company instanceof WP_Post ) : ?><a href="<?php echo esc_url( get_permalink( $company ) ); ?>"><?php esc_html_e( 'View employer', 'wp-bbtheme-child' ); ?></a><?php endif; ?></div></div>
            <div class="wpbb-jobs-single-meta__items"><?php foreach ( array_filter( array( $type, $category, $location, $remote ? __( 'Remote / hybrid', 'wp-bbtheme-child' ) : '', $salary, $deadline ? sprintf( __( 'Apply by %s', 'wp-bbtheme-child' ), wp_date( get_option( 'date_format' ), strtotime( $deadline ) ) ) : '' ) ) as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?></div>
        </div>
        <?php $meta = ob_get_clean();
        return $meta . $content . '<div id="apply" class="wpbb-jobs-single-apply">' . wpbb_jobs_apply_form( $job_id ) . '</div>';
    }
    if ( is_singular( 'wpbb_company' ) ) {
        $company_id = get_the_ID();
        $jobs = new WP_Query( array( 'post_type' => 'wpbb_job', 'post_status' => 'publish', 'posts_per_page' => 12, 'meta_query' => array( array( 'key' => '_wpbb_job_company_id', 'value' => $company_id, 'compare' => '=' ) ) ) );
        $extra = '<section class="wpbb-jobs-company-openings"><h2>' . esc_html__( 'Open roles', 'wp-bbtheme-child' ) . '</h2><div class="wpbb-jobs-grid wpbb-jobs-grid--2">';
        while ( $jobs->have_posts() ) { $jobs->the_post(); $extra .= wpbb_jobs_render_job_card( get_post() ); }
        wp_reset_postdata();
        $extra .= '</div></section>';
        return $content . $extra;
    }
    return $content;
}
add_filter( 'the_content', 'wpbb_jobs_single_content', 25 );

/**
 * v3.8.10.71: deterministic single-job renderer for block-theme templates.
 * The generic parent index template is intentionally not used for job detail
 * pages because its nested dynamic wrapper can lose post context on some
 * WordPress/Gutenberg combinations.
 */
function wpbb_jobs_single_job_view( $job_id = 0 ) {
    $job_id = absint( $job_id ?: get_queried_object_id() );
    $job = get_post( $job_id );
    if ( ! $job instanceof WP_Post || 'wpbb_job' !== $job->post_type ) {
        return '<div class="wpbb-jobs-empty">' . esc_html__( 'This job could not be found.', 'wp-bbtheme-child' ) . '</div>';
    }

    $company  = wpbb_jobs_company_for_job( $job_id );
    $location = wpbb_jobs_term_name( $job_id, 'wpbb_job_location' );
    $type     = wpbb_jobs_term_name( $job_id, 'wpbb_job_type' );
    $category = wpbb_jobs_term_name( $job_id, 'wpbb_job_category' );
    $salary   = wpbb_jobs_salary_label( $job_id );
    $deadline = (string) get_post_meta( $job_id, '_wpbb_job_deadline', true );
    $remote   = (bool) get_post_meta( $job_id, '_wpbb_job_remote', true );
    $featured = (bool) get_post_meta( $job_id, '_wpbb_job_featured', true );
    $skills   = wp_get_post_terms( $job_id, 'wpbb_job_skill', array( 'fields' => 'names' ) );
    if ( is_wp_error( $skills ) ) $skills = array();

    $description = (string) get_post_field( 'post_content', $job_id, 'raw' );
    $description = function_exists( 'wpbb_jobs_localized_demo_html' ) ? wpbb_jobs_localized_demo_html( $description, $job_id ) : $description;
    $description = has_blocks( $description ) ? do_blocks( $description ) : wpautop( $description );

    ob_start();
    ?>
    <main id="wp-theme-main" class="wpbb-jobs-single-page">
        <section class="wp-theme-inner-hero wpbb-jobs-single-hero">
            <div class="container">
                <p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Job opportunity', 'wp-bbtheme-child' ); ?></p>
                <div class="wpbb-jobs-single-hero__grid">
                    <div>
                        <div class="wpbb-jobs-single-hero__chips">
                            <?php if ( $featured ) : ?><span class="wpbb-jobs-chip is-featured"><?php esc_html_e( 'Featured', 'wp-bbtheme-child' ); ?></span><?php endif; ?>
                            <?php if ( $type ) : ?><span class="wpbb-jobs-chip"><?php echo esc_html( $type ); ?></span><?php endif; ?>
                            <?php if ( $remote ) : ?><span class="wpbb-jobs-chip"><?php esc_html_e( 'Remote friendly', 'wp-bbtheme-child' ); ?></span><?php endif; ?>
                        </div>
                        <h1><?php echo esc_html( function_exists( 'wpbb_jobs_localized_post_title' ) ? wpbb_jobs_localized_post_title( $job ) : get_the_title( $job_id ) ); ?></h1>
                        <p class="wp-theme-sector-lead"><?php $lead = function_exists( 'wpbb_jobs_localized_post_excerpt' ) ? wpbb_jobs_localized_post_excerpt( $job ) : $job->post_excerpt; echo esc_html( $lead ?: sprintf( __( 'Explore this opportunity with %s.', 'wp-bbtheme-child' ), $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' ) ) ); ?></p>
                    </div>
                    <a class="wpbb-jobs-button wpbb-jobs-single-hero__apply" href="#apply"><?php esc_html_e( 'Apply now', 'wp-bbtheme-child' ); ?></a>
                </div>
            </div>
        </section>
        <section class="wpbb-jobs-single-body">
            <div class="container">
                <div class="wpbb-jobs-single-layout">
                    <article class="wpbb-jobs-single-main">
                        <div class="wpbb-jobs-single-meta wpbb-jobs-panel">
                            <div class="wpbb-jobs-single-meta__company">
                                <?php echo wpbb_jobs_company_badge( $company ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <div><strong><?php echo esc_html( $company instanceof WP_Post ? $company->post_title : get_bloginfo( 'name' ) ); ?></strong><?php if ( $company instanceof WP_Post ) : ?><a href="<?php echo esc_url( get_permalink( $company ) ); ?>"><?php esc_html_e( 'View employer', 'wp-bbtheme-child' ); ?></a><?php endif; ?></div>
                            </div>
                            <div class="wpbb-jobs-single-meta__items">
                                <?php foreach ( array_filter( array( $location, $category, $salary, $deadline ? sprintf( __( 'Apply by %s', 'wp-bbtheme-child' ), wp_date( get_option( 'date_format' ), strtotime( $deadline ) ) ) : '' ) ) as $item ) : ?><span><?php echo esc_html( $item ); ?></span><?php endforeach; ?>
                            </div>
                        </div>
                        <div class="wpbb-jobs-job-description wpbb-jobs-panel">
                            <?php echo wp_kses_post( $description ); ?>
                            <?php if ( $skills ) : ?><div class="wpbb-jobs-job-skills"><h2><?php esc_html_e( 'Useful skills', 'wp-bbtheme-child' ); ?></h2><div><?php foreach ( $skills as $skill ) : ?><span class="wpbb-jobs-chip"><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $skill ) : $skill ); ?></span><?php endforeach; ?></div></div><?php endif; ?>
                        </div>
                    </article>
                    <aside id="apply" class="wpbb-jobs-single-sidebar">
                        <?php echo wpbb_jobs_apply_form( $job_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </aside>
                </div>
            </div>
        </section>
    </main>
    <?php
    return (string) ob_get_clean();
}
add_shortcode( 'wpbb_job_single', static function() { return wpbb_jobs_single_job_view(); } );

/** Keep the legacy post-content enhancement usable outside the dedicated template. */
function wpbb_jobs_single_content_context_ok() {
    if ( ! is_singular( array( 'wpbb_job', 'wpbb_company' ) ) ) return false;
    $queried = absint( get_queried_object_id() );
    $current = absint( get_the_ID() );
    return ! $queried || ! $current || $queried === $current;
}

/** Dedicated employer detail renderer so company links use the same stable block-theme route. */
function wpbb_jobs_single_company_view( $company_id = 0 ) {
    $company_id = absint( $company_id ?: get_queried_object_id() );
    $company = get_post( $company_id );
    if ( ! $company instanceof WP_Post || 'wpbb_company' !== $company->post_type ) {
        return '<div class="wpbb-jobs-empty">' . esc_html__( 'This employer could not be found.', 'wp-bbtheme-child' ) . '</div>';
    }
    $website = (string) get_post_meta( $company_id, '_wpbb_company_website', true );
    $description = (string) get_post_field( 'post_content', $company_id, 'raw' );
    $description = function_exists( 'wpbb_jobs_localized_demo_html' ) ? wpbb_jobs_localized_demo_html( $description, $company_id ) : $description;
    $description = has_blocks( $description ) ? do_blocks( $description ) : wpautop( $description );
    $jobs = get_posts( array(
        'post_type' => 'wpbb_job', 'post_status' => 'publish', 'posts_per_page' => 12,
        'meta_key' => '_wpbb_job_company_id', 'meta_value' => $company_id,
        'orderby' => 'date', 'order' => 'DESC',
    ) );
    ob_start(); ?>
    <main id="wp-theme-main" class="wpbb-jobs-company-page">
        <section class="wp-theme-inner-hero"><div class="container"><p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Hiring company', 'wp-bbtheme-child' ); ?></p><div class="wpbb-jobs-company-hero"><div><?php echo wpbb_jobs_company_badge( $company ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><h1><?php echo esc_html( get_the_title( $company_id ) ); ?></h1><p class="wp-theme-sector-lead"><?php echo esc_html( $company->post_excerpt ?: __( 'Employer profile and current opportunities.', 'wp-bbtheme-child' ) ); ?></p></div><?php if ( $website ) : ?><a class="wpbb-jobs-button is-secondary" href="<?php echo esc_url( $website ); ?>" rel="noopener" target="_blank"><?php esc_html_e( 'Visit website', 'wp-bbtheme-child' ); ?></a><?php endif; ?></div></div></section>
        <section class="wpbb-jobs-single-body"><div class="container"><div class="wpbb-jobs-company-layout"><article class="wpbb-jobs-panel wpbb-jobs-job-description"><?php echo wp_kses_post( $description ); ?></article><section class="wpbb-jobs-company-openings"><div class="wp-theme-section-heading"><p class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Current opportunities', 'wp-bbtheme-child' ); ?></p><h2><?php esc_html_e( 'Open roles', 'wp-bbtheme-child' ); ?></h2></div><?php if ( $jobs ) : ?><div class="wpbb-jobs-grid wpbb-jobs-grid--2"><?php foreach ( $jobs as $job ) echo wpbb_jobs_render_job_card( $job ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php else : ?><div class="wpbb-jobs-empty"><?php esc_html_e( 'There are no open roles with this employer right now.', 'wp-bbtheme-child' ); ?></div><?php endif; ?></section></div></div></section>
    </main><?php
    return (string) ob_get_clean();
}
add_shortcode( 'wpbb_company_single', static function() { return wpbb_jobs_single_company_view(); } );

/**
 * v3.8.10.78 homepage discovery blocks and salary tools.
 */
function wpbb_jobs_count_open_jobs( $term_id = 0 ) {
    $args = array(
        'post_type' => 'wpbb_job',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => array(
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
    if ( $term_id ) {
        $args['tax_query'] = array( array( 'taxonomy' => 'wpbb_job_category', 'field' => 'term_id', 'terms' => array( absint( $term_id ) ) ) );
    }
    $query = new WP_Query( $args );
    return (int) $query->found_posts;
}

function wpbb_jobs_categories_grid( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'limit' => 8 ) );
    $terms = get_terms( array( 'taxonomy' => 'wpbb_job_category', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
    if ( is_wp_error( $terms ) || ! $terms ) return '<div class="wpbb-jobs-empty">' . esc_html__( 'Job categories will appear here when vacancies are published.', 'wp-bbtheme-child' ) . '</div>';
    $items = array();
    foreach ( $terms as $term ) {
        $count = wpbb_jobs_count_open_jobs( $term->term_id );
        if ( ! $count ) continue;
        $items[] = array( 'term' => $term, 'count' => $count );
    }
    usort( $items, static function( $a, $b ) { return $b['count'] <=> $a['count']; } );
    $items = array_slice( $items, 0, max( 1, min( 12, absint( $atts['limit'] ) ) ) );
    $icons = array( 'Technology' => '⌘', 'Design' => '✦', 'Operations' => '↗', 'Customer Success' => '◎', 'People & HR' => '♟', 'Sales' => '↗', 'Finance' => '£', 'Marketing' => '◉', 'Project Management' => '✓', 'Legal & Compliance' => '§' );
    ob_start(); ?>
    <div class="wpbb-jobs-category-grid">
        <?php foreach ( $items as $item ) : $term = $item['term']; ?>
            <a class="wpbb-jobs-category-card" href="<?php echo esc_url( add_query_arg( 'job_category', $term->term_id, wpbb_jobs_page_url( 'jobs' ) ) ); ?>">
                <span class="wpbb-jobs-category-card__icon" aria-hidden="true"><?php echo esc_html( $icons[ $term->name ] ?? '•' ); ?></span>
                <span class="wpbb-jobs-category-card__title"><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $term->name ) : $term->name ); ?></span>
                <span class="wpbb-jobs-category-card__count"><?php echo esc_html( sprintf( _n( '%d job', '%d jobs', $item['count'], 'wp-bbtheme-child' ), $item['count'] ) ); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php return ob_get_clean();
}

function wpbb_jobs_home_metrics() {
    $jobs = wpbb_jobs_count_open_jobs();
    $companies = (int) wp_count_posts( 'wpbb_company' )->publish;
    $locations = get_terms( array( 'taxonomy' => 'wpbb_job_location', 'hide_empty' => true, 'fields' => 'count' ) );
    $locations = is_wp_error( $locations ) ? 0 : (int) $locations;
    ob_start(); ?>
    <div class="wpbb-jobs-home-metrics">
        <div><strong><?php echo esc_html( number_format_i18n( $jobs ) ); ?>+</strong><span><?php esc_html_e( 'open opportunities', 'wp-bbtheme-child' ); ?></span></div>
        <div><strong><?php echo esc_html( number_format_i18n( $companies ) ); ?>+</strong><span><?php esc_html_e( 'hiring employers', 'wp-bbtheme-child' ); ?></span></div>
        <div><strong><?php echo esc_html( number_format_i18n( $locations ) ); ?>+</strong><span><?php esc_html_e( 'locations & remote', 'wp-bbtheme-child' ); ?></span></div>
        <div><strong>2026/27</strong><span><?php esc_html_e( 'salary calculator', 'wp-bbtheme-child' ); ?></span></div>
    </div>
    <?php return ob_get_clean();
}

function wpbb_jobs_salary_benchmarks( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'limit' => 6 ) );
    $terms = get_terms( array( 'taxonomy' => 'wpbb_job_category', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
    if ( is_wp_error( $terms ) || ! $terms ) return '';
    $rows = array();
    foreach ( $terms as $term ) {
        $ids = get_posts( array(
            'post_type' => 'wpbb_job', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids',
            'tax_query' => array( array( 'taxonomy' => 'wpbb_job_category', 'field' => 'term_id', 'terms' => array( $term->term_id ) ) ),
        ) );
        $midpoints = array();
        foreach ( $ids as $job_id ) {
            $min = (float) get_post_meta( $job_id, '_wpbb_job_salary_min', true );
            $max = (float) get_post_meta( $job_id, '_wpbb_job_salary_max', true );
            if ( $min || $max ) $midpoints[] = $min && $max ? ( $min + $max ) / 2 : max( $min, $max );
        }
        if ( ! $midpoints ) continue;
        $rows[] = array( 'term' => $term, 'average' => array_sum( $midpoints ) / count( $midpoints ), 'count' => count( $midpoints ) );
    }
    usort( $rows, static function( $a, $b ) { return $b['count'] <=> $a['count']; } );
    $rows = array_slice( $rows, 0, max( 1, min( 10, absint( $atts['limit'] ) ) ) );
    ob_start(); ?>
    <div class="wpbb-jobs-salary-benchmarks">
        <?php foreach ( $rows as $row ) : ?>
            <a href="<?php echo esc_url( add_query_arg( 'job_category', $row['term']->term_id, wpbb_jobs_page_url( 'jobs' ) ) ); ?>" class="wpbb-jobs-salary-benchmark">
                <span><strong><?php echo esc_html( function_exists( 'wpbb_jobs_translate_text' ) ? wpbb_jobs_translate_text( $row['term']->name ) : $row['term']->name ); ?></strong><small><?php echo esc_html( sprintf( _n( '%d live role', '%d live roles', $row['count'], 'wp-bbtheme-child' ), $row['count'] ) ); ?></small></span>
                <strong><?php echo esc_html( wpbb_jobs_money( round( $row['average'] / 500 ) * 500, 'GBP' ) ); ?></strong>
            </a>
        <?php endforeach; ?>
    </div>
    <p class="wpbb-jobs-data-note"><?php esc_html_e( 'Demo salary benchmarks are calculated from the currently published vacancies on this site, not from external market data.', 'wp-bbtheme-child' ); ?></p>
    <?php return ob_get_clean();
}

function wpbb_jobs_salary_calculator() {
    ob_start(); ?>
    <div class="wpbb-jobs-salary-calculator" data-wpbb-salary-calculator>
        <div class="wpbb-jobs-salary-calculator__form">
            <div>
                <span class="wp-theme-sector-eyebrow"><?php esc_html_e( 'Take-home pay estimator', 'wp-bbtheme-child' ); ?></span>
                <h3><?php esc_html_e( 'What could that salary mean each month?', 'wp-bbtheme-child' ); ?></h3>
                <p><?php esc_html_e( 'Enter an annual salary for an illustrative 2026/27 PAYE estimate.', 'wp-bbtheme-child' ); ?></p>
            </div>
            <label><?php esc_html_e( 'Annual gross salary', 'wp-bbtheme-child' ); ?><span class="wpbb-jobs-money-input"><span>£</span><input type="number" min="0" step="500" value="45000" data-salary-gross /></span></label>
            <label><?php esc_html_e( 'Tax region', 'wp-bbtheme-child' ); ?><select data-salary-region><option value="rUK"><?php esc_html_e( 'England, Wales or Northern Ireland', 'wp-bbtheme-child' ); ?></option><option value="scotland"><?php esc_html_e( 'Scotland', 'wp-bbtheme-child' ); ?></option></select></label>
            <label><?php esc_html_e( 'Salary-sacrifice pension', 'wp-bbtheme-child' ); ?><span class="wpbb-jobs-percent-input"><input type="number" min="0" max="30" step="1" value="5" data-salary-pension /><span>%</span></span></label>
            <button type="button" class="wpbb-jobs-button" data-salary-calculate><?php esc_html_e( 'Calculate take-home pay', 'wp-bbtheme-child' ); ?></button>
        </div>
        <div class="wpbb-jobs-salary-calculator__results" aria-live="polite">
            <div class="wpbb-jobs-salary-result is-primary"><span><?php esc_html_e( 'Estimated monthly take-home', 'wp-bbtheme-child' ); ?></span><strong data-salary-monthly>£0</strong></div>
            <div class="wpbb-jobs-salary-result"><span><?php esc_html_e( 'Estimated annual take-home', 'wp-bbtheme-child' ); ?></span><strong data-salary-annual>£0</strong></div>
            <div class="wpbb-jobs-salary-result"><span><?php esc_html_e( 'Income tax', 'wp-bbtheme-child' ); ?></span><strong data-salary-tax>£0</strong></div>
            <div class="wpbb-jobs-salary-result"><span><?php esc_html_e( 'Employee National Insurance', 'wp-bbtheme-child' ); ?></span><strong data-salary-ni>£0</strong></div>
            <div class="wpbb-jobs-salary-result"><span><?php esc_html_e( 'Salary-sacrifice pension', 'wp-bbtheme-child' ); ?></span><strong data-salary-pension-out>£0</strong></div>
        </div>
        <p class="wpbb-jobs-salary-disclaimer"><?php esc_html_e( 'Illustrative estimate only. It assumes standard employment income and does not account for individual tax codes, student loans, benefits, bonuses or other deductions. Check current HMRC guidance or professional advice for personal decisions.', 'wp-bbtheme-child' ); ?></p>
    </div>
    <?php return ob_get_clean();
}

add_action( 'init', static function() {
    add_shortcode( 'wpbb_job_categories', static function( $atts ) { return wpbb_jobs_categories_grid( shortcode_atts( array( 'limit' => 8 ), $atts ) ); } );
    add_shortcode( 'wpbb_jobs_home_metrics', static function() { return wpbb_jobs_home_metrics(); } );
    add_shortcode( 'wpbb_salary_guide', static function( $atts ) { return wpbb_jobs_salary_benchmarks( shortcode_atts( array( 'limit' => 6 ), $atts ) ); } );
    add_shortcode( 'wpbb_salary_calculator', static function() { return wpbb_jobs_salary_calculator(); } );
}, 22 );

add_action( 'init', static function() {
    if ( ! wp_script_is( 'wpbb-jobs-blocks', 'registered' ) ) return;
    $common = array( 'api_version' => 2, 'editor_script' => 'wpbb-jobs-blocks' );
    register_block_type( 'wpbb-jobs/categories', array_merge( $common, array(
        'attributes' => array( 'limit' => array( 'type' => 'number', 'default' => 8 ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_categories_grid( array( 'limit' => $attrs['limit'] ?? 8 ) ); },
    ) ) );
    register_block_type( 'wpbb-jobs/home-metrics', array_merge( $common, array(
        'attributes' => array(), 'render_callback' => static function() { return wpbb_jobs_home_metrics(); },
    ) ) );
    register_block_type( 'wpbb-jobs/salary-guide', array_merge( $common, array(
        'attributes' => array( 'limit' => array( 'type' => 'number', 'default' => 6 ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_salary_benchmarks( array( 'limit' => $attrs['limit'] ?? 6 ) ); },
    ) ) );
    register_block_type( 'wpbb-jobs/salary-calculator', array_merge( $common, array(
        'attributes' => array(), 'render_callback' => static function() { return wpbb_jobs_salary_calculator(); },
    ) ) );
}, 31 );

function wpbb_jobs_career_advice_cards( $atts = array() ) {
    $atts = wp_parse_args( $atts, array( 'limit' => 3 ) );
    $query = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => max( 1, min( 6, absint( $atts['limit'] ) ) ), 'orderby' => 'date', 'order' => 'DESC' ) );
    if ( ! $query->have_posts() ) return '';
    ob_start(); ?>
    <div class="wpbb-jobs-advice-grid">
        <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <article class="wpbb-jobs-advice-card">
                <a class="wpbb-jobs-advice-card__image" href="<?php the_permalink(); ?>">
                    <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large', array( 'loading' => 'lazy' ) ); } else { echo '<span class="wpbb-jobs-advice-card__placeholder" aria-hidden="true">↗</span>'; } ?>
                </a>
                <div class="wpbb-jobs-advice-card__body">
                    <span class="wpbb-jobs-advice-card__meta"><?php echo esc_html( get_the_date() ); ?></span>
                    <h3><a href="<?php the_permalink(); ?>"><?php echo esc_html( function_exists( 'wpbb_jobs_localized_post_title' ) ? wpbb_jobs_localized_post_title( get_post() ) : get_the_title() ); ?></a></h3>
                    <p><?php $advice_excerpt = function_exists( 'wpbb_jobs_localized_post_excerpt' ) ? wpbb_jobs_localized_post_excerpt( get_post() ) : get_the_excerpt(); echo esc_html( wp_trim_words( $advice_excerpt, 18 ) ); ?></p>
                    <a class="wpbb-jobs-card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read advice', 'wp-bbtheme-child' ); ?> →</a>
                </div>
            </article>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php return ob_get_clean();
}

add_action( 'init', static function() {
    add_shortcode( 'wpbb_career_advice', static function( $atts ) { return wpbb_jobs_career_advice_cards( shortcode_atts( array( 'limit' => 3 ), $atts ) ); } );
}, 23 );

add_action( 'init', static function() {
    if ( ! wp_script_is( 'wpbb-jobs-blocks', 'registered' ) ) return;
    $common = array( 'api_version' => 2, 'editor_script' => 'wpbb-jobs-blocks' );
    register_block_type( 'wpbb-jobs/career-advice', array_merge( $common, array(
        'attributes' => array( 'limit' => array( 'type' => 'number', 'default' => 3 ) ),
        'render_callback' => static function( $attrs ) { return wpbb_jobs_career_advice_cards( array( 'limit' => $attrs['limit'] ?? 3 ) ); },
    ) ) );
}, 32 );
