<?php
/**
 * CV Download Page Template — /download-cv
 *
 * Template Name: CV Download
 *
 * This template is a minimal UI fallback. In practice, the rewrite
 * rule registered in inc/cv-download.php intercepts requests to
 * /download-cv BEFORE WordPress loads a template, serves the PDF
 * directly, and calls exit().
 *
 * This template is only shown if:
 *   - The rewrite rule hasn't been flushed yet, OR
 *   - ACF is not active and no CV file is set.
 *
 * Displays:
 *   - CV filename and last-updated date (plan requirement)
 *   - Filesize
 *   - Total download count
 *   - Download button (or "not available" message)
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$cv_file       = fhp_option( 'active_cv_file' );
$cv_label      = fhp_option( 'cv_download_label', __( 'Download CV', 'fuadhasan-portfolio' ) );
$download_count = function_exists( 'fhp_cv_get_download_count' ) ? fhp_cv_get_download_count() : 0;

// Build file metadata for display
$cv_filename    = '';
$cv_updated     = '';
$cv_filesize    = '';

if ( ! empty( $cv_file ) ) {
    // Filename (from ACF array or attachment)
    if ( ! empty( $cv_file['filename'] ) ) {
        $cv_filename = $cv_file['filename'];
    } elseif ( ! empty( $cv_file['url'] ) ) {
        $cv_filename = basename( $cv_file['url'] );
    }

    // Last-updated date from attachment modified time
    $attachment_id = $cv_file['ID'] ?? 0;
    if ( $attachment_id ) {
        $post_modified = get_post_field( 'post_modified', $attachment_id );
        if ( $post_modified ) {
            $cv_updated = date_i18n(
                get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
                strtotime( $post_modified )
            );
        }
    }

    // Human-readable filesize
    if ( ! empty( $cv_file['filesize'] ) ) {
        $cv_filesize = size_format( (int) $cv_file['filesize'] );
    } elseif ( $attachment_id ) {
        $file_path = get_attached_file( $attachment_id );
        if ( $file_path && file_exists( $file_path ) ) {
            $cv_filesize = size_format( filesize( $file_path ) );
        }
    }
}
?>

<section class="section section--dark cv-download-page">
    <div class="container" style="max-width: 680px;">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Resume',       'fuadhasan-portfolio' ),
            'heading' => __( 'Download CV',  'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( ! empty( $cv_file ) && ! empty( $cv_file['url'] ) ) : ?>

            <!-- CV file info card -->
            <div class="card cv-download-page__card" style="margin-bottom: 2rem;">

                <!-- File icon + name row -->
                <div class="flex flex-gap" style="align-items: flex-start; margin-bottom: 1.25rem;">
                    <span style="font-size: 2.5rem; line-height: 1;" aria-hidden="true">📄</span>
                    <div>
                        <p class="text-mono" style="font-size: var(--font-size-sm); color: var(--color-text-primary); margin-bottom: 0.25rem; word-break: break-all;">
                            <?php echo esc_html( $cv_filename ?: 'Fuad_Hasan_CV.pdf' ); ?>
                        </p>
                        <?php if ( $cv_filesize ) : ?>
                            <span class="tag tag--cyan"><?php echo esc_html( $cv_filesize ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Metadata rows -->
                <dl class="cv-download-page__meta" style="display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1.5rem; font-size: var(--font-size-sm);">

                    <?php if ( $cv_updated ) : ?>
                        <dt class="text-muted"><?php esc_html_e( 'Last updated', 'fuadhasan-portfolio' ); ?></dt>
                        <dd><?php echo esc_html( $cv_updated ); ?></dd>
                    <?php endif; ?>

                    <?php if ( $download_count > 0 ) : ?>
                        <dt class="text-muted"><?php esc_html_e( 'Downloads', 'fuadhasan-portfolio' ); ?></dt>
                        <dd><?php echo esc_html( number_format_i18n( $download_count ) ); ?></dd>
                    <?php endif; ?>

                    <dt class="text-muted"><?php esc_html_e( 'Format', 'fuadhasan-portfolio' ); ?></dt>
                    <dd>PDF</dd>

                </dl>

            </div><!-- .cv-download-page__card -->

            <!-- Download CTA -->
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <p class="text-muted" style="margin-bottom: 1.25rem;">
                    <?php esc_html_e( 'Your download should start automatically. If not, click the button below.', 'fuadhasan-portfolio' ); ?>
                </p>

                <a href="<?php echo esc_url( home_url( '/download-cv' ) ); ?>"
                   class="btn btn--primary"
                   download="Fuad_Hasan_CV.pdf">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <?php echo esc_html( $cv_label ); ?>
                </a>
            </div>

            <script>
                /* Auto-trigger download on landing — fires once and navigates away */
                (function () {
                    'use strict';
                    if ( window.sessionStorage && ! sessionStorage.getItem( 'fhp_cv_triggered' ) ) {
                        sessionStorage.setItem( 'fhp_cv_triggered', '1' );
                        window.location.href = '<?php echo esc_js( home_url( '/download-cv' ) ); ?>';
                    }
                }());
            </script>

        <?php else : ?>

            <!-- CV not configured yet -->
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="font-size: 3rem; margin-bottom: 1rem;" aria-hidden="true">🚧</p>
                <h2 style="font-size: var(--font-size-xl); margin-bottom: 0.75rem;">
                    <?php esc_html_e( 'CV Not Available Yet', 'fuadhasan-portfolio' ); ?>
                </h2>
                <p class="text-muted" style="margin-bottom: 2rem; max-width: 420px; margin-inline: auto;">
                    <?php esc_html_e( 'The CV / Resume PDF has not been uploaded yet. Please check back soon or get in touch directly.', 'fuadhasan-portfolio' ); ?>
                </p>
                <div class="flex flex-gap" style="justify-content: center; flex-wrap: wrap;">
                    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--primary">
                        <?php esc_html_e( 'Contact Me', 'fuadhasan-portfolio' ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--outline">
                        <?php esc_html_e( '← Back to Home', 'fuadhasan-portfolio' ); ?>
                    </a>
                </div>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php get_footer();
