<?php
/**
 * Template Part: Section Header
 *
 * Reusable centred (or left-aligned) section heading component.
 *
 * Accepts $args:
 *   label      (string) — small label above the heading (e.g. "Career")
 *   heading    (string) — main <h2> text
 *   subheading (string) — optional paragraph beneath heading
 *   align      (string) — 'center' (default) or 'left'
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$label      = $args['label']      ?? '';
$heading    = $args['heading']    ?? '';
$subheading = $args['subheading'] ?? '';
$align      = $args['align']      ?? 'center';
$class      = 'section-header section-header--' . esc_attr( $align );
?>

<div class="<?php echo esc_attr( $class ); ?>">
    <?php if ( $label ) : ?>
        <span class="section-header__label text-mono text-cyan">
            <?php echo esc_html( $label ); ?>
        </span>
    <?php endif; ?>

    <?php if ( $heading ) : ?>
        <h2 class="section-header__heading">
            <?php echo esc_html( $heading ); ?>
        </h2>
    <?php endif; ?>

    <?php if ( $subheading ) : ?>
        <p class="section-header__sub text-muted">
            <?php echo esc_html( $subheading ); ?>
        </p>
    <?php endif; ?>
</div>
