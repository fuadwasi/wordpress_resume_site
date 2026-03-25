<?php
/**
 * Home Page Template  — /
 *
 * Sections (in order):
 *   1. Hero              (template-parts/home/hero.php)
 *   2. Summary           (template-parts/home/summary.php)
 *   3. Skills Overview   (template-parts/home/skills-overview.php)
 *   4. Achievements Bar  (template-parts/home/achievements-bar.php)
 *   5. Featured Projects (template-parts/home/featured-projects.php)
 *   6. Home CTA banner   (inline section)
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>

<?php get_template_part( 'template-parts/home/hero' ); ?>
<?php get_template_part( 'template-parts/home/summary' ); ?>
<?php get_template_part( 'template-parts/home/skills-overview' ); ?>
<?php get_template_part( 'template-parts/home/achievements-bar' ); ?>
<?php get_template_part( 'template-parts/home/featured-projects' ); ?>

<!-- Home-page CTA banner -->
<section class="section section--dark home-cta" aria-label="<?php esc_attr_e( 'Get In Touch', 'fuadhasan-portfolio' ); ?>">
    <div class="container text-center">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'      => __( 'What\'s Next?', 'fuadhasan-portfolio' ),
            'heading'    => __( 'Let\'s Work Together', 'fuadhasan-portfolio' ),
            'subheading' => __( 'Open to new opportunities, collaborations, and interesting conversations. Let\'s build something great.', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php get_template_part( 'template-parts/shared/cta-buttons', null, [
            'show_cv'      => true,
            'show_contact' => true,
            'align'        => 'center',
        ] ); ?>

    </div>
</section>

<?php get_footer();
