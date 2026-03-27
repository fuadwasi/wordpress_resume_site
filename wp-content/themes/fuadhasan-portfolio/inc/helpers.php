<?php
/**
 * Template Helper Functions
 *
 * Utility functions used across all theme templates to keep
 * template files clean and readable.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------
   ACF Helpers (fallback-safe when ACF is not active)
------------------------------------------------------------------ */

/**
 * Retrieve an ACF field value with a fallback for when ACF is not active.
 *
 * @param string     $field_name  ACF field name.
 * @param int|string $post_id     Post ID or 'option'.
 * @param mixed      $fallback    Value to return when ACF is absent.
 * @return mixed
 */
function fhp_field( string $field_name, $post_id = false, $fallback = '' ) {
    if ( ! function_exists( 'get_field' ) ) {
        return $fallback;
    }
    $value = get_field( $field_name, $post_id );
    return ( $value !== null && $value !== false && $value !== '' ) ? $value : $fallback;
}

/**
 * Echo an ACF field value, escaped for output.
 *
 * @param string     $field_name
 * @param int|string $post_id
 * @param string     $fallback
 */
function fhp_the_field( string $field_name, $post_id = false, string $fallback = '' ) {
    echo esc_html( fhp_field( $field_name, $post_id, $fallback ) );
}

/**
 * Retrieve an ACF option-page field.
 *
 * @param string $field_name
 * @param mixed  $fallback
 * @return mixed
 */
function fhp_option( string $field_name, $fallback = '' ) {
    return fhp_field( $field_name, 'option', $fallback );
}

/**
 * Echo an ACF option-page field, escaped.
 *
 * @param string $field_name
 * @param string $fallback
 */
function fhp_the_option( string $field_name, string $fallback = '' ) {
    echo esc_html( fhp_option( $field_name, $fallback ) );
}

/* ------------------------------------------------------------------
   Social / Contact Helpers
------------------------------------------------------------------ */

/**
 * Return the full mailto: link for the site email.
 *
 * @return string
 */
function fhp_get_email_link(): string {
    $email = fhp_option( 'email_address', 'fhassanwasi@gmail.com' );
    return 'mailto:' . sanitize_email( $email );
}

/**
 * Return the CV download URL.
 *
 * @return string
 */
function fhp_get_cv_url(): string {
    return home_url( '/download-cv' );
}

/**
 * Render an icon SVG inline from the assets/images folder.
 * Falls back to a dashicon span if the file doesn't exist.
 *
 * @param string $icon_name  Filename without extension (e.g. 'github').
 * @param string $class      Optional extra CSS classes.
 */
function fhp_icon( string $icon_name, string $class = '' ) {
    $path = FHP_DIR . '/assets/images/icons/' . sanitize_file_name( $icon_name ) . '.svg';
    if ( file_exists( $path ) ) {
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
        echo '<span class="icon ' . esc_attr( $class ) . '" aria-hidden="true">'
            . file_get_contents( $path ) // Safe: local file, not user input
            . '</span>';
    } else {
        echo '<span class="dashicons dashicons-' . esc_attr( $icon_name ) . ' ' . esc_attr( $class ) . '" aria-hidden="true"></span>';
    }
}

/* ------------------------------------------------------------------
   Post Query Helpers
------------------------------------------------------------------ */

/**
 * Get all experience entries ordered by the ACF 'order' meta field,
 * then by post date descending.
 *
 * @return WP_Query
 */
function fhp_get_experience_query(): WP_Query {
    return new WP_Query( [
        'post_type'      => 'experience',
        'posts_per_page' => -1,
        'orderby'        => [
            'meta_value_num' => 'ASC',
            'date'           => 'DESC',
        ],
        'meta_key'       => 'order',
        'no_found_rows'  => true,
    ] );
}

/**
 * Get featured project posts.
 *
 * @param int $limit  Number of projects to return.
 * @return WP_Query
 */
function fhp_get_featured_projects( int $limit = 4 ): WP_Query {
    return new WP_Query( [
        'post_type'      => 'project',
        'posts_per_page' => $limit,
        'meta_query'     => [
            [
                'key'     => 'is_featured',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'order',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );
}

/**
 * Get all projects ordered by ACF order field.
 *
 * @return WP_Query
 */
function fhp_get_all_projects(): WP_Query {
    return new WP_Query( [
        'post_type'      => 'project',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'order',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );
}

/**
 * Get skills grouped by skill_category taxonomy.
 *
 * Returns an associative array: [ 'Backend' => [WP_Post, …], … ]
 *
 * @return array<string, WP_Post[]>
 */
function fhp_get_skills_by_category(): array {
    $categories = get_terms( [
        'taxonomy'   => 'skill_category',
        'hide_empty' => true,
        'orderby'    => 'term_order',
        'order'      => 'ASC',
    ] );

    if ( is_wp_error( $categories ) || empty( $categories ) ) {
        return [];
    }

    $grouped = [];
    foreach ( $categories as $cat ) {
        $skills = get_posts( [
            'post_type'      => 'skill',
            'posts_per_page' => -1,
            'tax_query'      => [
                [
                    'taxonomy' => 'skill_category',
                    'field'    => 'term_id',
                    'terms'    => $cat->term_id,
                ],
            ],
            'orderby'        => 'meta_value_num',
            'meta_key'       => 'order',
            'order'          => 'ASC',
        ] );

        if ( ! empty( $skills ) ) {
            $grouped[ $cat->name ] = $skills;
        }
    }

    return $grouped;
}

/**
 * Get all achievements ordered by issue_date descending.
 *
 * @return WP_Query
 */
function fhp_get_achievements(): WP_Query {
    return new WP_Query( [
        'post_type'      => 'achievement',
        'posts_per_page' => -1,
        'orderby'        => [
            'meta_value' => 'DESC',
            'date'       => 'DESC',
        ],
        'meta_key'       => 'issue_date',
        'no_found_rows'  => true,
    ] );
}

/* ------------------------------------------------------------------
   Formatting Helpers
------------------------------------------------------------------ */

/**
 * Format an ACF date string (Y-m-d) into a human-readable form.
 *
 * @param string $date_string  Date in Y-m-d format.
 * @param string $format       PHP date format.
 * @return string
 */
function fhp_format_date( string $date_string, string $format = 'M Y' ): string {
    if ( empty( $date_string ) ) {
        return '';
    }
    $timestamp = strtotime( $date_string );
    return $timestamp ? date_i18n( $format, $timestamp ) : $date_string;
}

/**
 * Build a human-readable date range string, e.g. "Mar 2021 – Present".
 *
 * @param string $start_date  Y-m-d
 * @param string $end_date    Y-m-d (empty = current)
 * @param bool   $is_current  Force "Present" label.
 * @return string
 */
function fhp_date_range( string $start_date, string $end_date = '', bool $is_current = false ): string {
    $start = fhp_format_date( $start_date );
    if ( $is_current || empty( $end_date ) ) {
        $end = __( 'Present', 'fuadhasan-portfolio' );
    } else {
        $end = fhp_format_date( $end_date );
    }
    return $start . ' – ' . $end;
}

/**
 * Return a comma-separated tech-stack tag list for a post.
 *
 * @param int $post_id
 * @return string  HTML string of .tag spans.
 */
function fhp_tech_tags_html( int $post_id ): string {
    $terms = get_the_terms( $post_id, 'tech_stack' );
    if ( ! $terms || is_wp_error( $terms ) ) {
        return '';
    }

    $html = '<div class="tag-list">';
    foreach ( $terms as $term ) {
        $html .= '<span class="tag">' . esc_html( $term->name ) . '</span>';
    }
    $html .= '</div>';

    return $html;
}

/* ------------------------------------------------------------------
   Meta / SEO Helpers
------------------------------------------------------------------ */

/**
 * Return the <title> tag content for the current page.
 *
 * Titles match the plan's "Meta titles" spec (Section 10):
 *   /            → "Fuad Hasan | Senior Software Engineer | NopCommerce Expert"
 *   /about       → "About Fuad Hasan | ASP.NET Core Developer | Bangladesh"
 *   /experience  → "Work Experience | Fuad Hasan | Brain Station 23"
 *   /projects    → "Projects Portfolio | Fuad Hasan | eCommerce & ERP"
 *   /contact     → "Contact Fuad Hasan | Senior Software Engineer"
 *
 * Used as the value returned by the `pre_get_document_title` filter
 * (in seo.php) so the WordPress core `<title>` tag is always correct,
 * even without a third-party SEO plugin.
 *
 * @return string  Unescaped title string (WordPress escapes it internally).
 */
function fhp_page_title(): string {
    $name = fhp_option( 'hero_name', 'Fuad Hasan' );

    if ( is_front_page() ) {
        return $name . ' | Senior Software Engineer | NopCommerce Expert';
    }

    if ( is_page( 'about' ) ) {
        return 'About ' . $name . ' | ASP.NET Core Developer | Bangladesh';
    }

    if ( is_post_type_archive( 'experience' ) || is_page( 'experience' ) ) {
        return 'Work Experience | ' . $name . ' | Brain Station 23';
    }

    if ( is_post_type_archive( 'project' ) || is_page( 'projects' ) ) {
        return 'Projects Portfolio | ' . $name . ' | eCommerce & ERP';
    }

    if ( is_page( 'contact' ) ) {
        return 'Contact ' . $name . ' | Senior Software Engineer';
    }

    if ( is_page( 'skills' ) ) {
        return 'Skills | ' . $name . ' | NopCommerce Developer';
    }

    if ( is_page( 'achievements' ) ) {
        return 'Achievements | ' . $name;
    }

    if ( is_page() ) {
        return get_the_title() . ' | ' . $name;
    }

    if ( is_single() ) {
        return get_the_title() . ' | ' . $name;
    }

    return $name;
}
