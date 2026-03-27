<?php
/**
 * Achievements Page Template — /achievements
 *
 * Template Name: Achievements
 *
 * Displays certifications, awards, contest results, and volunteer
 * recognition grouped by achievement_type taxonomy.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$achievements_query = fhp_get_achievements();
?>

<section class="section section--dark achievements-page">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Recognition',  'fuadhasan-portfolio' ),
            'heading' => __( 'Achievements', 'fuadhasan-portfolio' ),
            'subheading' => __( 'Certifications, awards, competitive programming results, and volunteer milestones.', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( $achievements_query->have_posts() ) : ?>
            <div class="grid-3">
                <?php while ( $achievements_query->have_posts() ) : $achievements_query->the_post();
                    $org        = fhp_field( 'issuing_organization' );
                    $date       = fhp_field( 'issue_date' );
                    $cred_url   = fhp_field( 'credential_url' );
                    $desc       = fhp_field( 'achievement_description' );
                    $icon       = fhp_field( 'achievement_icon' );
                    $types      = get_the_terms( get_the_ID(), 'achievement_type' );
                    $type_label = ( $types && ! is_wp_error( $types ) ) ? $types[0]->name : '';
                ?>
                <article class="card achievement-card" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
                    <?php if ( $icon ) : ?>
                    <img src="<?php echo esc_url( $icon['url'] ); ?>"
                         alt="<?php echo esc_attr( get_the_title() ); ?>"
                         width="60" height="60" loading="lazy"
                         class="achievement-card__icon">
                    <?php endif; ?>

                    <?php if ( $type_label ) : ?>
                        <span class="tag tag--cyan" style="margin-bottom:.75rem;"><?php echo esc_html( $type_label ); ?></span>
                    <?php endif; ?>

                    <h3 class="achievement-card__title"><?php the_title(); ?></h3>

                    <?php if ( $org ) : ?>
                        <p class="achievement-card__org text-muted"><?php echo esc_html( $org ); ?></p>
                    <?php endif; ?>

                    <?php if ( $date ) : ?>
                        <p class="achievement-card__date text-muted text-mono" style="font-size:var(--font-size-sm);">
                            <?php echo esc_html( fhp_format_date( $date, 'F Y' ) ); ?>
                        </p>
                    <?php endif; ?>

                    <?php if ( $desc ) : ?>
                        <p style="margin-top:.75rem;"><?php echo esc_html( $desc ); ?></p>
                    <?php endif; ?>

                    <?php if ( $cred_url ) : ?>
                        <a href="<?php echo esc_url( $cred_url ); ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="btn btn--outline btn--sm"
                           style="margin-top:1rem;">
                            <?php esc_html_e( 'Verify ↗', 'fuadhasan-portfolio' ); ?>
                        </a>
                    <?php endif; ?>
                </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="text-muted"><?php esc_html_e( 'No achievements found. Add them from the WordPress admin.', 'fuadhasan-portfolio' ); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php get_footer();
