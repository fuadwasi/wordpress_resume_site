<?php
/**
 * Admin UI Enhancements — Section 8: Admin Panel Capabilities
 *
 * Makes the WordPress admin panel fully usable for managing all
 * portfolio content without touching any code.
 *
 * Features:
 *   1. Custom list-table columns for all four CPTs
 *      (Experience, Project, Skill, Achievement).
 *   2. Sortable columns where meaningful (dates, order, proficiency).
 *   3. "Portfolio Status" dashboard widget with CPT entry counts,
 *      CV download total, and quick-action links to each admin screen.
 *   4. Lightweight inline admin CSS for the widget and custom columns.
 *
 * All meta values are read with get_post_meta() so this file has
 * zero runtime dependency on ACF.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
   SECTION 1 — ADMIN COLUMNS: EXPERIENCE CPT
==================================================================== */

/**
 * Replace the default "Date" column with richer Experience columns.
 *
 * Columns shown: Title · Company · Job Title · Start Date · Current · Order
 *
 * @param  array<string,string> $columns  Default columns.
 * @return array<string,string>
 */
add_filter( 'manage_experience_posts_columns', 'fhp_experience_columns' );

function fhp_experience_columns( array $columns ): array {
    unset( $columns['date'] );

    return array_merge( $columns, [
        'fhp_company'    => __( 'Company',    'fuadhasan-portfolio' ),
        'fhp_job_title'  => __( 'Job Title',  'fuadhasan-portfolio' ),
        'fhp_start_date' => __( 'Start',      'fuadhasan-portfolio' ),
        'fhp_current'    => __( 'Current',    'fuadhasan-portfolio' ),
        'fhp_exp_order'  => __( 'Order',      'fuadhasan-portfolio' ),
    ] );
}

/**
 * Render custom column content for Experience CPT.
 *
 * @param string $column   Column slug.
 * @param int    $post_id  Current post ID.
 */
add_action( 'manage_experience_posts_custom_column', 'fhp_experience_column_content', 10, 2 );

function fhp_experience_column_content( string $column, int $post_id ): void {
    switch ( $column ) {
        case 'fhp_company':
            echo esc_html( get_post_meta( $post_id, 'company_name', true ) ?: '—' );
            break;

        case 'fhp_job_title':
            echo esc_html( get_post_meta( $post_id, 'job_title', true ) ?: '—' );
            break;

        case 'fhp_start_date':
            $raw = get_post_meta( $post_id, 'start_date', true );
            echo esc_html( $raw ? date_i18n( 'M Y', strtotime( $raw ) ) : '—' );
            break;

        case 'fhp_current':
            $is_current = get_post_meta( $post_id, 'is_current', true );
            echo $is_current
                ? '<span class="fhp-badge fhp-badge--yes" aria-label="' . esc_attr__( 'Current position', 'fuadhasan-portfolio' ) . '">&#10003;</span>'
                : '<span class="fhp-badge fhp-badge--no" aria-label="' . esc_attr__( 'Past position', 'fuadhasan-portfolio' ) . '">—</span>';
            break;

        case 'fhp_exp_order':
            echo esc_html( get_post_meta( $post_id, 'order', true ) ?: '0' );
            break;
    }
}

/**
 * Register sortable columns for Experience CPT.
 *
 * @param  array<string,string> $sortable
 * @return array<string,string>
 */
add_filter( 'manage_edit-experience_sortable_columns', 'fhp_experience_sortable_columns' );

function fhp_experience_sortable_columns( array $sortable ): array {
    $sortable['fhp_company']    = 'fhp_company';
    $sortable['fhp_start_date'] = 'fhp_start_date';
    $sortable['fhp_exp_order']  = 'fhp_exp_order';
    return $sortable;
}

/* ====================================================================
   SECTION 2 — ADMIN COLUMNS: PROJECT CPT
==================================================================== */

/**
 * Replace the default "Date" column with richer Project columns.
 *
 * Columns shown: Title · Featured · Type · Client · Tech Stack · Order
 *
 * @param  array<string,string> $columns
 * @return array<string,string>
 */
add_filter( 'manage_project_posts_columns', 'fhp_project_columns' );

function fhp_project_columns( array $columns ): array {
    unset( $columns['date'] );

    return array_merge( $columns, [
        'fhp_featured'     => __( 'Featured',   'fuadhasan-portfolio' ),
        'fhp_proj_type'    => __( 'Type',        'fuadhasan-portfolio' ),
        'fhp_client'       => __( 'Client',      'fuadhasan-portfolio' ),
        'fhp_proj_order'   => __( 'Order',       'fuadhasan-portfolio' ),
    ] );
}

/**
 * Render custom column content for Project CPT.
 *
 * @param string $column
 * @param int    $post_id
 */
add_action( 'manage_project_posts_custom_column', 'fhp_project_column_content', 10, 2 );

function fhp_project_column_content( string $column, int $post_id ): void {
    switch ( $column ) {
        case 'fhp_featured':
            $featured = get_post_meta( $post_id, 'is_featured', true );
            echo $featured
                ? '<span class="fhp-badge fhp-badge--yes fhp-star" aria-label="' . esc_attr__( 'Featured project', 'fuadhasan-portfolio' ) . '">&#9733;</span>'
                : '<span class="fhp-badge fhp-badge--no" aria-label="' . esc_attr__( 'Not featured', 'fuadhasan-portfolio' ) . '">—</span>';
            break;

        case 'fhp_proj_type':
            echo esc_html( get_post_meta( $post_id, 'project_type', true ) ?: '—' );
            break;

        case 'fhp_client':
            echo esc_html( get_post_meta( $post_id, 'client_company', true ) ?: '—' );
            break;

        case 'fhp_proj_order':
            echo esc_html( get_post_meta( $post_id, 'order', true ) ?: '0' );
            break;
    }
}

/**
 * Register sortable columns for Project CPT.
 *
 * @param  array<string,string> $sortable
 * @return array<string,string>
 */
add_filter( 'manage_edit-project_sortable_columns', 'fhp_project_sortable_columns' );

function fhp_project_sortable_columns( array $sortable ): array {
    $sortable['fhp_featured']   = 'fhp_featured';
    $sortable['fhp_proj_order'] = 'fhp_proj_order';
    return $sortable;
}

/* ====================================================================
   SECTION 3 — ADMIN COLUMNS: SKILL CPT
==================================================================== */

/**
 * Replace the default "Date" column with richer Skill columns.
 *
 * Columns shown: Title · Category · Proficiency · Order
 *
 * @param  array<string,string> $columns
 * @return array<string,string>
 */
add_filter( 'manage_skill_posts_columns', 'fhp_skill_columns' );

function fhp_skill_columns( array $columns ): array {
    unset( $columns['date'] );

    // Keep the taxonomy column already added by show_admin_column
    return array_merge( $columns, [
        'fhp_proficiency' => __( 'Proficiency', 'fuadhasan-portfolio' ),
        'fhp_skill_order' => __( 'Order',        'fuadhasan-portfolio' ),
    ] );
}

/**
 * Render custom column content for Skill CPT.
 *
 * @param string $column
 * @param int    $post_id
 */
add_action( 'manage_skill_posts_custom_column', 'fhp_skill_column_content', 10, 2 );

function fhp_skill_column_content( string $column, int $post_id ): void {
    switch ( $column ) {
        case 'fhp_proficiency':
            $level = (int) get_post_meta( $post_id, 'proficiency_level', true );
            if ( $level < 1 ) {
                echo '—';
                break;
            }
            echo '<span class="fhp-dots" aria-label="' . esc_attr( sprintf( __( 'Level %d of 5', 'fuadhasan-portfolio' ), $level ) ) . '">';
            for ( $i = 1; $i <= 5; $i++ ) {
                echo '<span class="fhp-dot' . ( $i <= $level ? ' fhp-dot--filled' : '' ) . '"></span>';
            }
            echo '</span>';
            break;

        case 'fhp_skill_order':
            echo esc_html( get_post_meta( $post_id, 'order', true ) ?: '0' );
            break;
    }
}

/**
 * Register sortable columns for Skill CPT.
 *
 * @param  array<string,string> $sortable
 * @return array<string,string>
 */
add_filter( 'manage_edit-skill_sortable_columns', 'fhp_skill_sortable_columns' );

function fhp_skill_sortable_columns( array $sortable ): array {
    $sortable['fhp_proficiency'] = 'fhp_proficiency';
    $sortable['fhp_skill_order'] = 'fhp_skill_order';
    return $sortable;
}

/* ====================================================================
   SECTION 4 — ADMIN COLUMNS: ACHIEVEMENT CPT
==================================================================== */

/**
 * Replace the default "Date" column with richer Achievement columns.
 *
 * Columns shown: Title · Type · Organisation · Issue Date
 *
 * @param  array<string,string> $columns
 * @return array<string,string>
 */
add_filter( 'manage_achievement_posts_columns', 'fhp_achievement_columns' );

function fhp_achievement_columns( array $columns ): array {
    unset( $columns['date'] );

    return array_merge( $columns, [
        'fhp_issuer'       => __( 'Organisation', 'fuadhasan-portfolio' ),
        'fhp_issue_date'   => __( 'Issue Date',   'fuadhasan-portfolio' ),
    ] );
}

/**
 * Render custom column content for Achievement CPT.
 *
 * @param string $column
 * @param int    $post_id
 */
add_action( 'manage_achievement_posts_custom_column', 'fhp_achievement_column_content', 10, 2 );

function fhp_achievement_column_content( string $column, int $post_id ): void {
    switch ( $column ) {
        case 'fhp_issuer':
            echo esc_html( get_post_meta( $post_id, 'issuing_organization', true ) ?: '—' );
            break;

        case 'fhp_issue_date':
            $raw = get_post_meta( $post_id, 'issue_date', true );
            echo esc_html( $raw ? date_i18n( 'M Y', strtotime( $raw ) ) : '—' );
            break;
    }
}

/**
 * Register a sortable Issue Date column for Achievement CPT.
 *
 * @param  array<string,string> $sortable
 * @return array<string,string>
 */
add_filter( 'manage_edit-achievement_sortable_columns', 'fhp_achievement_sortable_columns' );

function fhp_achievement_sortable_columns( array $sortable ): array {
    $sortable['fhp_issue_date'] = 'fhp_issue_date';
    return $sortable;
}

/* ====================================================================
   SECTION 5 — CUSTOM SORT QUERY HANDLING
   Translate our sortable column keys into WP_Query orderby args.
==================================================================== */

add_action( 'pre_get_posts', 'fhp_admin_column_sort' );

function fhp_admin_column_sort( WP_Query $query ): void {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $orderby = $query->get( 'orderby' );

    $meta_map = [
        'fhp_company'    => 'company_name',
        'fhp_start_date' => 'start_date',
        'fhp_exp_order'  => 'order',
        'fhp_featured'   => 'is_featured',
        'fhp_proj_order' => 'order',
        'fhp_proficiency' => 'proficiency_level',
        'fhp_skill_order' => 'order',
        'fhp_issue_date'  => 'issue_date',
    ];

    if ( isset( $meta_map[ $orderby ] ) ) {
        $query->set( 'meta_key', $meta_map[ $orderby ] );
        // Date fields → sort as value; everything else → alphabetical / numeric
        $date_fields = [ 'start_date', 'issue_date' ];
        $query->set(
            'orderby',
            in_array( $meta_map[ $orderby ], $date_fields, true ) ? 'meta_value' : 'meta_value_num'
        );
    }
}

/* ====================================================================
   SECTION 6 — DASHBOARD WIDGET: PORTFOLIO STATUS
==================================================================== */

add_action( 'wp_dashboard_setup', 'fhp_register_dashboard_widget' );

function fhp_register_dashboard_widget(): void {
    wp_add_dashboard_widget(
        'fhp_portfolio_status',
        __( '&#128193; Portfolio Site Status', 'fuadhasan-portfolio' ),
        'fhp_render_dashboard_widget'
    );
}

/**
 * Render the Portfolio Status dashboard widget.
 *
 * Shows live CPT entry counts, CV download total, last-updated dates,
 * and quick-action buttons to each main admin screen.
 */
function fhp_render_dashboard_widget(): void {
    $exp_count     = wp_count_posts( 'experience' )->publish ?? 0;
    $proj_count    = wp_count_posts( 'project' )->publish ?? 0;
    $skill_count   = wp_count_posts( 'skill' )->publish ?? 0;
    $achiev_count  = wp_count_posts( 'achievement' )->publish ?? 0;

    // Featured project count
    $featured_count = (int) ( new WP_Query( [
        'post_type'      => 'project',
        'post_status'    => 'publish',
        'meta_key'       => 'is_featured',
        'meta_value'     => '1',
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'posts_per_page' => -1,
    ] ) )->post_count;

    // CV download count (stored by cv-protection.php)
    $cv_downloads = function_exists( 'fhp_cv_get_download_count' )
        ? fhp_cv_get_download_count()
        : (int) get_option( 'fhp_cv_download_count', 0 );

    // Active CV filename
    $cv_label = __( 'No CV uploaded yet', 'fuadhasan-portfolio' );
    if ( function_exists( 'get_field' ) ) {
        $cv_file = get_field( 'active_cv_file', 'option' );
        if ( ! empty( $cv_file['filename'] ) ) {
            $cv_label = esc_html( $cv_file['filename'] );
        }
    }

    $rows = [
        [
            'icon'    => '&#128100;',
            'label'   => __( 'Experience entries', 'fuadhasan-portfolio' ),
            'count'   => $exp_count,
            'url'     => admin_url( 'edit.php?post_type=experience' ),
            'add_url' => admin_url( 'post-new.php?post_type=experience' ),
        ],
        [
            'icon'    => '&#128193;',
            'label'   => sprintf(
                /* translators: %d: number of featured projects */
                __( 'Projects (%d featured)', 'fuadhasan-portfolio' ),
                $featured_count
            ),
            'count'   => $proj_count,
            'url'     => admin_url( 'edit.php?post_type=project' ),
            'add_url' => admin_url( 'post-new.php?post_type=project' ),
        ],
        [
            'icon'    => '&#128736;',
            'label'   => __( 'Skills', 'fuadhasan-portfolio' ),
            'count'   => $skill_count,
            'url'     => admin_url( 'edit.php?post_type=skill' ),
            'add_url' => admin_url( 'post-new.php?post_type=skill' ),
        ],
        [
            'icon'    => '&#127942;',
            'label'   => __( 'Achievements', 'fuadhasan-portfolio' ),
            'count'   => $achiev_count,
            'url'     => admin_url( 'edit.php?post_type=achievement' ),
            'add_url' => admin_url( 'post-new.php?post_type=achievement' ),
        ],
    ];
    ?>
    <div class="fhp-dash-widget">

        <table class="fhp-dash-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Section', 'fuadhasan-portfolio' ); ?></th>
                    <th class="fhp-col-count"><?php esc_html_e( 'Entries', 'fuadhasan-portfolio' ); ?></th>
                    <th class="fhp-col-actions"><?php esc_html_e( 'Actions', 'fuadhasan-portfolio' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $rows as $row ) : ?>
                <tr>
                    <td><?php echo esc_html( $row['icon'] ); ?> <?php echo esc_html( $row['label'] ); ?></td>
                    <td class="fhp-col-count">
                        <a href="<?php echo esc_url( $row['url'] ); ?>" class="fhp-count">
                            <?php echo esc_html( $row['count'] ); ?>
                        </a>
                    </td>
                    <td class="fhp-col-actions">
                        <a href="<?php echo esc_url( $row['url'] ); ?>" class="button button-small">
                            <?php esc_html_e( 'Manage', 'fuadhasan-portfolio' ); ?>
                        </a>
                        <a href="<?php echo esc_url( $row['add_url'] ); ?>" class="button button-small button-primary">
                            <?php esc_html_e( '+ Add', 'fuadhasan-portfolio' ); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="fhp-dash-cv">
            <span class="fhp-dash-cv__label">
                &#128196; <?php esc_html_e( 'Active CV:', 'fuadhasan-portfolio' ); ?>
                <strong><?php echo esc_html( $cv_label ); ?></strong>
            </span>
            <span class="fhp-dash-cv__downloads">
                &#11015; <?php
                printf(
                    /* translators: %d: total CV download count */
                    esc_html__( '%d total downloads', 'fuadhasan-portfolio' ),
                    (int) $cv_downloads
                );
                ?>
            </span>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=fhp-cv-settings' ) ); ?>" class="button button-small">
                <?php esc_html_e( 'Update CV', 'fuadhasan-portfolio' ); ?>
            </a>
        </div>

        <div class="fhp-dash-settings">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=fhp-hero-about' ) ); ?>" class="button button-secondary">
                &#9998; <?php esc_html_e( 'Hero &amp; About', 'fuadhasan-portfolio' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=fhp-contact-social' ) ); ?>" class="button button-secondary">
                &#9993; <?php esc_html_e( 'Contact &amp; Social', 'fuadhasan-portfolio' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=fhp-stats' ) ); ?>" class="button button-secondary">
                &#128202; <?php esc_html_e( 'Stats &amp; Counters', 'fuadhasan-portfolio' ); ?>
            </a>
        </div>

    </div>
    <?php
}

/* ====================================================================
   SECTION 7 — ADMIN CSS
   Minimal inline styles for the dashboard widget and list-table columns.
==================================================================== */

add_action( 'admin_head', 'fhp_admin_inline_css' );

function fhp_admin_inline_css(): void {
    // Only load on screens that benefit from our styles.
    $screen = get_current_screen();
    if ( ! $screen ) {
        return;
    }

    $relevant_screens = [
        'dashboard',
        'edit-experience',
        'edit-project',
        'edit-skill',
        'edit-achievement',
    ];

    if ( ! in_array( $screen->id, $relevant_screens, true ) ) {
        return;
    }
    ?>
    <style id="fhp-admin-ui">
        /* ---- List table: narrow columns ---- */
        .column-fhp_current,
        .column-fhp_featured,
        .column-fhp_exp_order,
        .column-fhp_proj_order,
        .column-fhp_skill_order { width: 60px; text-align: center; }
        .column-fhp_proficiency { width: 110px; }
        .column-fhp_start_date,
        .column-fhp_issue_date  { width: 90px; }

        /* ---- Badges (current/featured indicators) ---- */
        .fhp-badge { display: inline-block; font-weight: 600; }
        .fhp-badge--yes { color: #00a32a; }
        .fhp-badge--no  { color: #a0a0a0; }
        .fhp-star       { font-size: 16px; }

        /* ---- Proficiency dots ---- */
        .fhp-dots { display: inline-flex; gap: 3px; }
        .fhp-dot  {
            display: inline-block;
            width: 10px; height: 10px;
            border-radius: 50%;
            background: #ddd;
        }
        .fhp-dot--filled { background: #2271b1; }

        /* ---- Dashboard widget ---- */
        .fhp-dash-widget { font-size: 13px; }
        .fhp-dash-table  { margin-bottom: 12px; }
        .fhp-col-count   { width: 60px; text-align: right; padding-right: 12px; }
        .fhp-col-actions { width: 130px; }
        .fhp-col-actions .button { margin-right: 4px; }

        a.fhp-count {
            font-size: 22px;
            font-weight: 700;
            text-decoration: none;
            color: #2271b1;
        }
        a.fhp-count:hover { color: #135e96; }

        .fhp-dash-cv {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            background: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 10px;
            font-size: 12px;
        }
        .fhp-dash-cv__label   { flex: 1 1 auto; }
        .fhp-dash-cv__downloads { color: #555; }

        .fhp-dash-settings {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
    </style>
    <?php
}
