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
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$cv_file  = fhp_option( 'active_cv_file' );
$cv_label = fhp_option( 'cv_download_label', __( 'Download CV', 'fuadhasan-portfolio' ) );
?>

<section class="section section--dark">
    <div class="container flex-center" style="flex-direction:column; min-height:60vh; text-align:center; gap:2rem;">

        <h1><?php esc_html_e( 'Download CV', 'fuadhasan-portfolio' ); ?></h1>

        <?php if ( ! empty( $cv_file ) && ! empty( $cv_file['url'] ) ) : ?>
            <p class="text-muted"><?php esc_html_e( 'Your download should start automatically. If not, click the button below.', 'fuadhasan-portfolio' ); ?></p>

            <a href="<?php echo esc_url( home_url( '/download-cv' ) ); ?>"
               class="btn btn--primary"
               download="Fuad_Hasan_CV.pdf">
                <?php echo esc_html( $cv_label ); ?>
            </a>

            <script>
                /* Auto-trigger download if redirected to this template */
                window.location.href = '<?php echo esc_js( home_url( '/download-cv' ) ); ?>';
            </script>
        <?php else : ?>
            <p class="text-muted">
                <?php esc_html_e( 'CV is not available yet. Please check back soon.', 'fuadhasan-portfolio' ); ?>
            </p>
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--secondary">
                <?php esc_html_e( 'Contact Me Instead', 'fuadhasan-portfolio' ); ?>
            </a>
        <?php endif; ?>

    </div>
</section>

<?php get_footer();
