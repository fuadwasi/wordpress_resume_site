<?php
/**
 * Skills Page Template — /skills
 *
 * Template Name: Skills
 *
 * Renders skills grouped by taxonomy category, each with a
 * proficiency indicator bar.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$skills_by_cat = fhp_get_skills_by_category();
?>

<section class="section section--dark skills-page">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Expertise',      'fuadhasan-portfolio' ),
            'heading' => __( 'Skills & Technologies', 'fuadhasan-portfolio' ),
            'subheading' => __( 'Backend, Frontend, Databases, DevOps, Integrations & more.', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( ! empty( $skills_by_cat ) ) : ?>
            <div class="skills-page__grid">
                <?php foreach ( $skills_by_cat as $category => $skills ) : ?>
                    <?php
                    get_template_part( 'template-parts/skill/skill-group', null, [
                        'category' => $category,
                        'skills'   => $skills,
                    ] );
                    ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="text-muted"><?php esc_html_e( 'No skills found. Add them from the WordPress admin.', 'fuadhasan-portfolio' ); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php get_footer();
