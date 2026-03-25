<?php
/**
 * CV Upload Directory Protection  —  Section 7
 *
 * Handles the security requirements from the Section 7 "Security
 * Considerations" spec:
 *
 *  "Restrict direct access to wp-content/uploads/cv/ via .htaccess"
 *  "Only serve CV through the /download-cv handler"
 *  "Log download count (optional: custom post meta counter)"
 *
 * Responsibilities:
 *  1. Creates wp-content/uploads/cv/ on theme activation (and on
 *     demand) with a .htaccess that denies all direct HTTP access.
 *  2. Filters WordPress's upload directory during ACF Options Page
 *     saves so that newly-uploaded CV PDFs land in uploads/cv/.
 *  3. Maintains a site-wide download counter in wp_options.
 *  4. Exposes public helpers:
 *       fhp_cv_directory_path()       → absolute filesystem path
 *       fhp_cv_directory_url()        → public base URL
 *       fhp_cv_get_download_count()   → current total downloads
 *       fhp_cv_increment_download_count() → add 1 and save
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ==================================================================
   CONSTANTS / PATH HELPERS
================================================================== */

/**
 * Return the absolute filesystem path of the CV uploads subdirectory.
 * No trailing slash.
 *
 * @return string
 */
function fhp_cv_directory_path(): string {
    $upload = wp_upload_dir( null, false );
    return $upload['basedir'] . '/cv';
}

/**
 * Return the public URL of the CV uploads subdirectory.
 * No trailing slash.
 *
 * @return string
 */
function fhp_cv_directory_url(): string {
    $upload = wp_upload_dir( null, false );
    return $upload['baseurl'] . '/cv';
}

/* ==================================================================
   1. CREATE THE DIRECTORY + .HTACCESS
================================================================== */

/**
 * Create wp-content/uploads/cv/ and write the .htaccess deny rule.
 * Safe to call multiple times — idempotent.
 *
 * @return bool  true on success.
 */
function fhp_ensure_cv_directory(): bool {
    $cv_dir   = fhp_cv_directory_path();
    $htaccess = $cv_dir . '/.htaccess';

    // Create the directory (wp_mkdir_p also creates parent dirs)
    if ( ! is_dir( $cv_dir ) ) {
        if ( ! wp_mkdir_p( $cv_dir ) ) {
            return false;
        }
    }

    // Write (or refresh) the .htaccess protection file
    $content = fhp_cv_htaccess_content();

    // phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
    // phpcs:disable WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
    if ( ! file_exists( $htaccess ) || file_get_contents( $htaccess ) !== $content ) {
        file_put_contents( $htaccess, $content );
    }
    // phpcs:enable

    return true;
}

/**
 * Return the .htaccess content that blocks direct HTTP access to
 * the uploads/cv/ directory.
 *
 * Apache 2.4+ uses mod_authz_core; Apache 2.2 falls back to the
 * legacy Order/Deny syntax.
 *
 * Nginx users: add the equivalent `deny all;` rule to the location
 * block for /wp-content/uploads/cv/ in the server configuration.
 *
 * @return string
 */
function fhp_cv_htaccess_content(): string {
    return <<<'HTACCESS'
# Fuad Hasan Portfolio — CV Upload Directory Protection
# Blocks direct HTTP access to PDF files in this directory.
# Files are served exclusively via /download-cv (inc/cv-download.php).

<IfModule mod_authz_core.c>
    # Apache 2.4+
    Require all denied
</IfModule>

<IfModule !mod_authz_core.c>
    # Apache 2.2 fallback
    Order Allow,Deny
    Deny from all
</IfModule>
HTACCESS;
}

// Create the CV directory when the theme is activated
add_action( 'after_switch_theme', 'fhp_ensure_cv_directory', 15 );

// Also ensure it exists on every admin page load (cheap check — only
// creates files if they're missing, e.g. after server migration)
add_action( 'admin_init', 'fhp_ensure_cv_directory' );

/* ==================================================================
   2. REDIRECT CV UPLOADS INTO uploads/cv/
   
   When the admin saves the ACF Options page we temporarily redirect
   the WordPress upload directory so that any freshly uploaded PDF
   lands in uploads/cv/ (instead of uploads/YYYY/MM/).
   
   The flag $fhp_is_cv_upload is set true only while the ACF
   options-page save is in progress to avoid affecting other uploads.
================================================================== */

/** @var bool Flag that restricts the upload_dir filter to CV saves only. */
$GLOBALS['fhp_is_cv_upload'] = false;

// Priority 1 → run before ACF's own save hooks
add_action( 'acf/save_post', 'fhp_cv_upload_dir_start', 1 );

/**
 * Enable the upload directory override at the start of an ACF
 * options page save.
 *
 * @param int|string $post_id
 */
function fhp_cv_upload_dir_start( $post_id ): void {
    if ( 'options' === (string) $post_id ) {
        $GLOBALS['fhp_is_cv_upload'] = true;
        fhp_ensure_cv_directory();
        add_filter( 'upload_dir', 'fhp_cv_upload_dir_filter' );
    }
}

// Priority 99 → run after ACF has finished saving
add_action( 'acf/save_post', 'fhp_cv_upload_dir_end', 99 );

/**
 * Disable the upload directory override after the save completes.
 *
 * @param int|string $post_id
 */
function fhp_cv_upload_dir_end( $post_id ): void {
    if ( 'options' === (string) $post_id ) {
        $GLOBALS['fhp_is_cv_upload'] = false;
        remove_filter( 'upload_dir', 'fhp_cv_upload_dir_filter' );
    }
}

/**
 * Override the WordPress upload directory to point at uploads/cv/.
 * Only active while $GLOBALS['fhp_is_cv_upload'] is true.
 *
 * @param  array<string, string> $dirs  WordPress upload dirs array.
 * @return array<string, string>
 */
function fhp_cv_upload_dir_filter( array $dirs ): array {
    if ( empty( $GLOBALS['fhp_is_cv_upload'] ) ) {
        return $dirs;
    }

    $base_dirs = wp_upload_dir( null, false );

    $dirs['path']   = $base_dirs['basedir'] . '/cv';
    $dirs['url']    = $base_dirs['baseurl'] . '/cv';
    $dirs['subdir'] = '/cv';
    $dirs['error']  = false;

    return $dirs;
}

/* ==================================================================
   3. DOWNLOAD COUNTER
================================================================== */

/** wp_options key for the CV download counter. */
define( 'FHP_CV_DOWNLOAD_COUNT_KEY', 'fhp_cv_download_count' );

/**
 * Return the total number of times the CV has been downloaded.
 *
 * @return int
 */
function fhp_cv_get_download_count(): int {
    return (int) get_option( FHP_CV_DOWNLOAD_COUNT_KEY, 0 );
}

/**
 * Increment the CV download counter by 1 and persist it.
 *
 * Uses a non-autoloaded option so it does not add overhead to
 * every page load.
 */
function fhp_cv_increment_download_count(): void {
    $current = fhp_cv_get_download_count();

    // update_option handles the case where the option doesn't exist yet
    update_option( FHP_CV_DOWNLOAD_COUNT_KEY, $current + 1, false );
}

/* ==================================================================
   4. ACF MIME-TYPE VALIDATION FOR THE CV FIELD
================================================================== */

/**
 * Validate that only PDFs are uploaded via the active_cv_file field.
 * This adds a second layer of validation on top of the ACF field's
 * mime_types restriction.
 *
 * @param  array<string, string> $errors  Existing validation errors.
 * @param  array<string, mixed>  $file    Uploaded file data.
 * @param  array<string, mixed>  $field   ACF field config.
 * @return array<string, string>
 */
add_filter( 'acf/upload_prefilter/name=active_cv_file', 'fhp_cv_validate_pdf_upload', 10, 3 );

function fhp_cv_validate_pdf_upload( array $errors, array $file, array $field ): array {
    $mime = $file['type'] ?? '';
    if ( $mime !== 'application/pdf' ) {
        $errors[] = esc_html__( 'Only PDF files are accepted for the CV / Resume field.', 'fuadhasan-portfolio' );
    }
    return $errors;
}

/* ==================================================================
   5. ADMIN NOTICE — cv directory status
   
   Shows a one-time notice on the CV / Resume options page
   confirming that the upload directory is protected.
================================================================== */

add_action( 'admin_notices', 'fhp_cv_directory_notice' );

/**
 * Display a status notice on the CV / Resume ACF options page
 * showing whether the upload directory and .htaccess are in place.
 */
function fhp_cv_directory_notice(): void {
    // Only show on the CV settings options page
    $screen = get_current_screen();
    if ( ! $screen || strpos( $screen->id, 'fhp-cv-settings' ) === false ) {
        return;
    }

    $cv_dir   = fhp_cv_directory_path();
    $htaccess = $cv_dir . '/.htaccess';

    $dir_exists  = is_dir( $cv_dir );
    $htaccess_ok = file_exists( $htaccess );

    if ( $dir_exists && $htaccess_ok ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p>
                <strong><?php esc_html_e( 'CV Upload Directory Protected ✓', 'fuadhasan-portfolio' ); ?></strong>
                &mdash;
                <?php
                printf(
                    /* translators: %s: relative path to the uploads/cv directory */
                    esc_html__( 'Uploaded PDFs saved to %s are blocked from direct browser access.', 'fuadhasan-portfolio' ),
                    '<code>/wp-content/uploads/cv/</code>'
                );
                ?>
            </p>
        </div>
        <?php
    } else {
        // Attempt to create it now
        fhp_ensure_cv_directory();
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php esc_html_e( 'CV Upload Directory Notice', 'fuadhasan-portfolio' ); ?></strong>
                &mdash;
                <?php esc_html_e( 'The theme attempted to create the uploads/cv/ directory. Please re-load this page to confirm the .htaccess protection is active.', 'fuadhasan-portfolio' ); ?>
            </p>
        </div>
        <?php
    }
}
