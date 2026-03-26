<?php
/**
 * Template Part: Skill Group
 *
 * Renders one skill category group with proficiency bars.
 * Accepts $args['category'] (string) and $args['skills'] (WP_Post[]).
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$category = $args['category'] ?? '';
$skills   = $args['skills']   ?? [];

if ( empty( $skills ) ) {
    return;
}
?>

<div class="skill-group card">
    <h3 class="skill-group__title"><?php echo esc_html( $category ); ?></h3>

    <ul class="skill-group__list" aria-label="<?php echo esc_attr( $category ) . ' ' . esc_attr__( 'skills', 'fuadhasan-portfolio' ); ?>">
        <?php foreach ( $skills as $skill ) :
            $level = (int) get_post_meta( $skill->ID, 'proficiency_level', true );
            $level = ( $level >= 1 && $level <= 5 ) ? $level : 3;
            $pct   = ( $level / 5 ) * 100;
            $icon  = get_post_meta( $skill->ID, 'skill_icon', true ); // ACF image field with return_format='array' → returns array with 'url', 'alt', etc.; plain get_post_meta returns the attachment ID (int) when ACF is inactive
        ?>
        <li class="skill-group__item" data-level="<?php echo esc_attr( $level ); ?>">
            <div class="skill-group__item-header">
                <?php if ( is_array( $icon ) && ! empty( $icon['url'] ) ) : ?>
                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                         alt="" width="20" height="20" loading="lazy"
                         class="skill-group__icon" aria-hidden="true">
                <?php endif; ?>
                <span class="skill-group__name"><?php echo esc_html( get_the_title( $skill->ID ) ); ?></span>
                <span class="skill-group__level" aria-hidden="true">
                    <?php echo esc_html( $level ); ?>/5
                </span>
            </div>

            <!-- Animated progress bar (triggered by skills-animation.js) -->
            <div class="skill-group__bar-track" role="progressbar"
                 aria-valuenow="<?php echo esc_attr( $level ); ?>"
                 aria-valuemin="1"
                 aria-valuemax="5"
                 aria-label="<?php printf( esc_attr__( '%s proficiency %d out of 5', 'fuadhasan-portfolio' ), get_the_title( $skill->ID ), $level ); ?>">
                <div class="skill-group__bar-fill"
                     data-width="<?php echo esc_attr( $pct ); ?>"
                     style="width: 0%;"></div>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
