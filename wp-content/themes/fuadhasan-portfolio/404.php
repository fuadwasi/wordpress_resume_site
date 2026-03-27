<?php
/**
 * 404 Error Page Template
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>

<section class="section section--dark" aria-label="<?php esc_attr_e( 'Page Not Found', 'fuadhasan-portfolio' ); ?>">
    <div class="container flex-center" style="flex-direction:column; min-height:60vh; text-align:center; gap:2rem;">

        <div class="text-mono text-accent" style="font-size:clamp(4rem,12vw,8rem); font-weight:700; line-height:1;">
            404
        </div>

        <h1 style="font-size:var(--font-size-2xl);">
            <?php esc_html_e( 'Page Not Found', 'fuadhasan-portfolio' ); ?>
        </h1>

        <p class="text-muted" style="max-width:480px;">
            <?php esc_html_e( "The page you're looking for doesn't exist or has been moved.", 'fuadhasan-portfolio' ); ?>
        </p>

        <div class="flex flex-gap" style="flex-wrap:wrap; justify-content:center;">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn--primary">
                <?php esc_html_e( '← Back to Home', 'fuadhasan-portfolio' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>" class="btn btn--secondary">
                <?php esc_html_e( 'Contact Me', 'fuadhasan-portfolio' ); ?>
            </a>
        </div>

    </div>
</section>

<?php get_footer();
