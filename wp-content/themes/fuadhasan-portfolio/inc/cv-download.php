<?php
/**
 * CV / Resume Download Handler
 *
 * Registers the /download-cv rewrite rule and serves the active
 * CV PDF file as a direct download with the canonical filename
 * "Fuad_Hasan_CV.pdf".
 *
 * How it works:
 *   1. Admin uploads a new PDF via Media Library.
 *   2. Admin selects that file in Site Settings → CV / Resume.
 *   3. Any visitor hitting /download-cv gets the file immediately.
 *
 * Security:
 *   - The upload directory for CVs should be blocked from direct
 *     listing in .htaccess (see README for snippet).
 *   - Only PDF MIME types are accepted in the ACF field (enforced
 *     at field level and double-checked here).
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------
   1. Register custom rewrite rule + query var
------------------------------------------------------------------ */
add_action( 'init', 'fhp_cv_rewrite_rule' );

function fhp_cv_rewrite_rule() {
    add_rewrite_rule( '^download-cv/?$', 'index.php?fhp_cv_download=1', 'top' );
}

add_filter( 'query_vars', function ( array $vars ): array {
    $vars[] = 'fhp_cv_download';
    return $vars;
} );

/* ------------------------------------------------------------------
   2. Handle the download request
------------------------------------------------------------------ */
add_action( 'template_redirect', 'fhp_handle_cv_download' );

function fhp_handle_cv_download() {
    if ( ! get_query_var( 'fhp_cv_download' ) ) {
        return;
    }

    // Fetch the file array from ACF options
    $cv_file = function_exists( 'get_field' ) ? get_field( 'active_cv_file', 'option' ) : null;

    if ( empty( $cv_file ) || empty( $cv_file['url'] ) ) {
        // No CV configured — redirect home with a notice
        wp_redirect( home_url( '/' ) );
        exit;
    }

    $attachment_id = $cv_file['ID'] ?? attachment_url_to_postid( $cv_file['url'] );
    $file_path     = get_attached_file( $attachment_id );

    // Double-check it is actually a PDF
    $mime = mime_content_type( $file_path );
    if ( $mime !== 'application/pdf' ) {
        wp_redirect( home_url( '/' ) );
        exit;
    }

    if ( ! $file_path || ! file_exists( $file_path ) ) {
        wp_redirect( home_url( '/' ) );
        exit;
    }

    // Serve the file
    nocache_headers();
    header( 'Content-Type: application/pdf' );
    header( 'Content-Disposition: attachment; filename="Fuad_Hasan_CV.pdf"' );
    header( 'Content-Length: ' . filesize( $file_path ) );
    header( 'X-Content-Type-Options: nosniff' );

    // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
    readfile( $file_path );
    exit;
}

/* ------------------------------------------------------------------
   3. Flush rewrite rules when theme is activated
      (also called from functions.php after_switch_theme)
------------------------------------------------------------------ */
add_action( 'after_switch_theme', function () {
    fhp_cv_rewrite_rule();
    flush_rewrite_rules();
} );
