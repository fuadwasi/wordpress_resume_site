<?php
/**
 * Experience Archive Template — /experience
 *
 * Renders all work experience entries as a vertical timeline,
 * ordered by the ACF `order` meta field (ascending).
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$experience_query = fhp_get_experience_query();
?>

<section class="section section--dark" aria-label="<?php esc_attr_e( 'Work Experience', 'fuadhasan-portfolio' ); ?>">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Career',       'fuadhasan-portfolio' ),
            'heading' => __( 'Work Experience', 'fuadhasan-portfolio' ),
            'subheading' => __( 'My professional journey — from trainee to Senior Software Engineer II.', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( $experience_query->have_posts() ) : ?>
            <div class="experience-timeline" aria-label="<?php esc_attr_e( 'Work Experience Timeline', 'fuadhasan-portfolio' ); ?>">
                <?php while ( $experience_query->have_posts() ) : $experience_query->the_post(); ?>
                    <?php get_template_part( 'template-parts/experience/timeline-item' ); ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="text-muted"><?php esc_html_e( 'No experience entries found. Add them from the WordPress admin.', 'fuadhasan-portfolio' ); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php get_footer();
