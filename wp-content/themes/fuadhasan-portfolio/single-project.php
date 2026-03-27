<?php
/**
 * Single Project Template — /projects/{slug}
 *
 * Detailed case-study view for a single project.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();

$short_desc  = fhp_field( 'short_description' );
$full_desc   = get_the_content();
$project_type = fhp_field( 'project_type' );
$client      = fhp_field( 'client_company' );
$live_url    = fhp_field( 'live_url' );
$github_url  = fhp_field( 'github_url' );
?>

<article class="section section--dark single-project" <?php post_class(); ?>>
    <div class="container" style="max-width:960px;">

        <!-- Back link -->
        <a href="<?php echo esc_url( get_post_type_archive_link( 'project' ) ); ?>" class="btn btn--outline btn--sm" style="margin-bottom:2rem;">
            ← <?php esc_html_e( 'All Projects', 'fuadhasan-portfolio' ); ?>
        </a>

        <!-- Featured image -->
        <?php if ( has_post_thumbnail() ) : ?>
        <div class="single-project__hero-img" style="margin-bottom:2rem; border-radius:var(--border-radius); overflow:hidden;">
            <?php the_post_thumbnail( 'large', [ 'loading' => 'eager', 'style' => 'width:100%;height:auto;' ] ); ?>
        </div>
        <?php endif; ?>

        <header class="single-project__header card">
            <div class="flex flex-between" style="flex-wrap:wrap; gap:1rem;">
                <div>
                    <h1 class="single-project__title"><?php the_title(); ?></h1>
                    <?php if ( $client ) : ?>
                        <p class="text-muted" style="margin-top:0.25rem;"><?php echo esc_html( $client ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="flex flex-gap" style="flex-wrap:wrap;">
                    <?php if ( $live_url ) : ?>
                        <a href="<?php echo esc_url( $live_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--sm">
                            <?php esc_html_e( 'View Live ↗', 'fuadhasan-portfolio' ); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ( $github_url ) : ?>
                        <a href="<?php echo esc_url( $github_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--secondary btn--sm">
                            <?php esc_html_e( 'GitHub ↗', 'fuadhasan-portfolio' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex flex-gap" style="flex-wrap:wrap; margin-top:1rem;">
                <?php if ( $project_type ) : ?>
                    <span class="tag"><?php echo esc_html( $project_type ); ?></span>
                <?php endif; ?>
                <?php echo fhp_tech_tags_html( get_the_ID() ); ?>
            </div>
        </header>

        <?php if ( $short_desc ) : ?>
        <div class="single-project__summary card" style="margin-top:1.5rem;">
            <p class="text-md"><?php echo esc_html( $short_desc ); ?></p>
        </div>
        <?php endif; ?>

        <?php if ( $full_desc ) : ?>
        <div class="single-project__description card entry-content" style="margin-top:1.5rem;">
            <?php echo wp_kses_post( $full_desc ); ?>
        </div>
        <?php endif; ?>

    </div>
</article>

<?php get_footer();
