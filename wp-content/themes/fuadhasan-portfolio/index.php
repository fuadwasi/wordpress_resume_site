<?php
/**
 * The main fallback template.
 *
 * WordPress uses this when no more-specific template is found.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container section">
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <article <?php post_class( 'card' ); ?>>
                <h1><?php the_title(); ?></h1>
                <div class="entry-content"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p><?php esc_html_e( 'Nothing found.', 'fuadhasan-portfolio' ); ?></p>
    <?php endif; ?>
</div>

<?php get_footer();
