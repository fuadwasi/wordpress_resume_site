<?php
/**
 * Generic page template — used for all standard WordPress pages
 * that don't have a dedicated template (e.g. /blog, /privacy-policy).
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container section">
    <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class( 'page-content' ); ?>>
            <header class="page-content__header">
                <h1 class="page-content__title"><?php the_title(); ?></h1>
            </header>
            <div class="page-content__body entry-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; ?>
</div>

<?php get_footer();
