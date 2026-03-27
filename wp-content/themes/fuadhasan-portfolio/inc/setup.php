<?php
/**
 * Theme Activation Setup — Section 11, Phase 1: Environment Configuration
 *
 * Automates the WordPress-level tasks from Phase 1 that must run
 * inside WordPress:
 *
 *  1. Permalink structure  → /%postname%/   (required for all clean URLs)
 *  2. Required pages       → About, Skills, Achievements, Contact,
 *                            Download CV  (with correct page templates)
 *  3. Admin notice         → Summary of what was configured + manual
 *                            steps still needed
 *
 * All tasks run on after_switch_theme and are idempotent — safe to
 * re-run after migrations or re-activations without creating
 * duplicate pages or overwriting existing settings.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
   1. TRIGGER ON THEME ACTIVATION
==================================================================== */

add_action( 'after_switch_theme', 'fhp_run_activation_setup', 20 );

/**
 * Run all one-time activation tasks.
 *
 * Priority 20 ensures CPT registration (priority 10 in functions.php)
 * has already completed before we flush rewrite rules.
 */
function fhp_run_activation_setup(): void {
    fhp_configure_permalink_structure();
    fhp_create_required_pages();
    fhp_set_site_defaults();

    // Store the version that ran setup so we know it completed
    update_option( 'fhp_setup_version', FHP_VERSION );

    // Schedule the admin notice to appear on the next admin page load
    set_transient( 'fhp_show_setup_notice', 1, 5 * MINUTE_IN_SECONDS );
}

/* ====================================================================
   2. PERMALINK STRUCTURE
==================================================================== */

/**
 * Set permalink structure to /%postname%/ as required by the plan.
 *
 * Called both on theme activation and can be called standalone.
 * Uses the global $wp_rewrite object and flushes rules so that
 * CPT archives and custom slugs are immediately available.
 */
function fhp_configure_permalink_structure(): void {
    $desired = '/%postname%/';

    if ( get_option( 'permalink_structure' ) === $desired ) {
        return; // already set — nothing to do
    }

    update_option( 'permalink_structure', $desired );

    global $wp_rewrite;
    if ( $wp_rewrite instanceof WP_Rewrite ) {
        $wp_rewrite->set_permalink_structure( $desired );
        $wp_rewrite->flush_rules();
    }
}

/* ====================================================================
   3. REQUIRED PAGES
==================================================================== */

/**
 * Return the list of pages that must exist for the theme to function.
 *
 * Each entry maps the page slug to [ title, template_file, menu_order ].
 * The slugs must match the URLs used throughout the theme's navigation.
 *
 * @return array<string, array{0: string, 1: string, 2: int}>
 */
function fhp_get_required_pages(): array {
    return [
        'about'        => [ 'About',        'page-about.php',        10 ],
        'skills'       => [ 'Skills',        'page-skills.php',       30 ],
        'achievements' => [ 'Achievements',  'page-achievements.php', 40 ],
        'contact'      => [ 'Contact',       'page-contact.php',      50 ],
        'download-cv'  => [ 'Download CV',   'page-download-cv.php',  60 ],
    ];
}

/**
 * Create all required pages if they do not already exist.
 *
 * Uses the page slug as the unique identifier; existing pages are
 * left untouched except to update a missing page template assignment.
 *
 * Note: The Experience (/experience) and Projects (/projects) URLs
 * are served by CPT archives — no page post is required for them.
 */
function fhp_create_required_pages(): void {
    foreach ( fhp_get_required_pages() as $slug => [ $title, $template, $order ] ) {

        $existing = get_page_by_path( $slug, OBJECT, 'page' );

        if ( $existing instanceof WP_Post ) {
            // Page exists — ensure the correct template is assigned
            $current_template = get_post_meta( $existing->ID, '_wp_page_template', true );
            if ( $current_template !== $template ) {
                update_post_meta( $existing->ID, '_wp_page_template', $template );
            }
            continue;
        }

        // Page does not exist — create it
        $post_id = wp_insert_post( [
            'post_title'     => $title,
            'post_name'      => $slug,
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'menu_order'     => $order,
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ] );

        if ( is_wp_error( $post_id ) || ! $post_id ) {
            continue;
        }

        update_post_meta( $post_id, '_wp_page_template', $template );
    }
}

/* ====================================================================
   4. SITE DEFAULTS
==================================================================== */

/**
 * Configure sensible WordPress defaults for a portfolio site.
 *
 * - Discourages search engines until the site is ready
 * - Sets default timezone to Asia/Dhaka (Fuad's location)
 * - Removes sample "Hello World" post and page on first activation
 */
function fhp_set_site_defaults(): void {
    // Discourage search engines until the site is ready to launch
    if ( ! get_option( 'fhp_defaults_applied' ) ) {
        update_option( 'blog_public', 0 );          // discourage indexing
        update_option( 'timezone_string', 'Asia/Dhaka' );
        update_option( 'date_format', 'F j, Y' );
        update_option( 'time_format', 'g:i a' );
        update_option( 'start_of_week', 0 );        // Sunday

        // Remove the default "Hello World" post (ID 1) if it hasn't been edited
        $hello_post = get_post( 1 );
        if (
            $hello_post &&
            $hello_post->post_name === 'hello-world' &&
            $hello_post->post_author === '1' &&
            (int) $hello_post->comment_count === 1
        ) {
            wp_delete_post( 1, true );
        }

        // Remove the default "Sample Page" (ID 2) if untouched
        $sample_page = get_post( 2 );
        if (
            $sample_page &&
            $sample_page->post_type === 'page' &&
            $sample_page->post_name === 'sample-page'
        ) {
            wp_delete_post( 2, true );
        }

        update_option( 'fhp_defaults_applied', true );
    }
}

/* ====================================================================
   5. ADMIN NOTICE — Setup Summary
==================================================================== */

add_action( 'admin_notices', 'fhp_setup_admin_notice' );

/**
 * Display a one-time admin notice after theme activation summarising
 * what was configured automatically and what still needs manual action.
 *
 * The notice is shown once per activation, controlled by a transient
 * set in fhp_run_activation_setup().
 */
function fhp_setup_admin_notice(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    if ( ! get_transient( 'fhp_show_setup_notice' ) ) {
        return;
    }

    delete_transient( 'fhp_show_setup_notice' );

    // Gather status data for the notice
    $permalink      = get_option( 'permalink_structure', '(not set)' );
    $pages_created  = [];
    $pages_missing  = [];

    foreach ( fhp_get_required_pages() as $slug => [ $title, , ] ) {
        if ( get_page_by_path( $slug, OBJECT, 'page' ) ) {
            $pages_created[] = $title;
        } else {
            $pages_missing[] = $title;
        }
    }

    $admin_url    = admin_url( 'admin.php' );
    $settings_url = add_query_arg( 'page', 'fhp-hero-about', $admin_url );
    $cv_url       = add_query_arg( 'page', 'fhp-cv-settings', $admin_url );
    $plugins_url  = admin_url( 'plugin-install.php' );
    ?>
    <div class="notice notice-success is-dismissible fhp-setup-notice">
        <h3 style="margin-top:.8em;font-size:1.1em">
            🚀 <?php esc_html_e( 'Fuad Hasan Portfolio — Theme Activated', 'fuadhasan-portfolio' ); ?>
        </h3>

        <p><strong><?php esc_html_e( 'Automatically configured:', 'fuadhasan-portfolio' ); ?></strong></p>
        <ul style="list-style:disc;padding-left:1.8em;margin-bottom:.5em">
            <li>
                <?php
                printf(
                    /* translators: %s: permalink structure e.g. /%postname%/ */
                    esc_html__( 'Permalink structure: %s', 'fuadhasan-portfolio' ),
                    '<code>' . esc_html( $permalink ) . '</code>'
                );
                ?>
            </li>
            <?php if ( $pages_created ) : ?>
            <li>
                <?php
                printf(
                    /* translators: %s: comma-separated page titles */
                    esc_html__( 'Pages created: %s', 'fuadhasan-portfolio' ),
                    esc_html( implode( ', ', $pages_created ) )
                );
                ?>
            </li>
            <?php endif; ?>
            <li><?php esc_html_e( 'Site defaults set (timezone, date format, discourage indexing)', 'fuadhasan-portfolio' ); ?></li>
        </ul>

        <?php if ( $pages_missing ) : ?>
        <p style="color:#d63638"><strong><?php esc_html_e( 'Pages could not be created:', 'fuadhasan-portfolio' ); ?></strong>
            <?php echo esc_html( implode( ', ', $pages_missing ) ); ?>
        </p>
        <?php endif; ?>

        <p><strong><?php esc_html_e( 'Manual steps still required:', 'fuadhasan-portfolio' ); ?></strong></p>
        <ol style="padding-left:1.8em;margin-bottom:.8em">
            <li>
                <?php
                printf(
                    /* translators: %s: link to plugin install page */
                    wp_kses(
                        __( 'Install required plugins — <a href="%s">ACF Pro</a> (licence needed), Yoast SEO, WPForms Lite, WP Super Cache', 'fuadhasan-portfolio' ),
                        [ 'a' => [ 'href' => [] ] ]
                    ),
                    esc_url( $plugins_url )
                );
                ?>
            </li>
            <li>
                <?php
                printf(
                    /* translators: %s: link to Site Settings → Hero page */
                    wp_kses(
                        __( 'Fill in <a href="%s">Site Settings → Hero &amp; About</a> (name, title, bio, profile photo)', 'fuadhasan-portfolio' ),
                        [ 'a' => [ 'href' => [] ] ]
                    ),
                    esc_url( $settings_url )
                );
                ?>
            </li>
            <li>
                <?php
                printf(
                    /* translators: %s: link to Site Settings → CV page */
                    wp_kses(
                        __( 'Upload your CV PDF via <a href="%s">Site Settings → CV / Resume</a>', 'fuadhasan-portfolio' ),
                        [ 'a' => [ 'href' => [] ] ]
                    ),
                    esc_url( $cv_url )
                );
                ?>
            </li>
            <li><?php esc_html_e( 'Re-enable search engine indexing (Settings → Reading) when ready to launch', 'fuadhasan-portfolio' ); ?></li>
        </ol>
    </div>
    <?php
}
