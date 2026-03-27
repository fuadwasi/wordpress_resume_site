<?php
/**
 * Template Part: CTA Buttons
 *
 * Reusable call-to-action button row.
 * Used on the home page bottom CTA section and in the About page.
 *
 * Accepts $args:
 *   show_cv        (bool) — show "Download CV" button (default true)
 *   show_contact   (bool) — show "Contact Me" button (default true)
 *   show_projects  (bool) — show "View Projects" button (default false)
 *   align          (string) — 'center' (default) or 'left'
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$show_cv       = $args['show_cv']       ?? true;
$show_contact  = $args['show_contact']  ?? true;
$show_projects = $args['show_projects'] ?? false;
$align         = $args['align']         ?? 'center';

$cv_label = fhp_option( 'cv_download_label', __( 'Download CV', 'fuadhasan-portfolio' ) );
?>

<div class="cta-buttons flex flex-gap" style="<?php echo $align === 'center' ? 'justify-content:center;' : ''; ?> flex-wrap:wrap;">

    <?php if ( $show_cv ) : ?>
    <a href="<?php echo esc_url( fhp_get_cv_url() ); ?>"
       class="btn btn--primary"
       aria-label="<?php esc_attr_e( 'Download CV', 'fuadhasan-portfolio' ); ?>">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="7 10 12 15 17 10"/>
            <line x1="12" y1="15" x2="12" y2="3"/>
        </svg>
        <?php echo esc_html( $cv_label ); ?>
    </a>
    <?php endif; ?>

    <?php if ( $show_contact ) : ?>
    <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"
       class="btn btn--secondary">
        <?php esc_html_e( 'Contact Me', 'fuadhasan-portfolio' ); ?>
    </a>
    <?php endif; ?>

    <?php if ( $show_projects ) : ?>
    <a href="<?php echo esc_url( home_url( '/projects' ) ); ?>"
       class="btn btn--outline">
        <?php esc_html_e( 'View Projects', 'fuadhasan-portfolio' ); ?>
    </a>
    <?php endif; ?>

</div>
