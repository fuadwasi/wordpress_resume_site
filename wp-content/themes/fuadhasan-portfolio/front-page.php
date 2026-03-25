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
 *   6. CTA banner        (template-parts/shared/cta-buttons.php)
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
<?php get_template_part( 'template-parts/shared/cta-buttons' ); ?>

<?php get_footer();
