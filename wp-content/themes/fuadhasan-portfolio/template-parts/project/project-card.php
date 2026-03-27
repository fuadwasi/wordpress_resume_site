<?php
/**
 * Template Part: Project Card
 *
 * Used in archive-project.php, featured-projects.php, and home sections.
 * Reads ACF fields from the current post in the loop.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$short_desc   = fhp_field( 'short_description' );
$project_type = fhp_field( 'project_type' );
$client       = fhp_field( 'client_company' );
$live_url     = fhp_field( 'live_url' );
$github_url   = fhp_field( 'github_url' );

// Sanitise the type for use as a data attribute
$type_slug = sanitize_title( $project_type );
?>

<article class="card project-card"
         data-type="<?php echo esc_attr( $type_slug ); ?>"
         <?php post_class( 'project-card' ); ?>>

    <!-- Thumbnail -->
    <?php if ( has_post_thumbnail() ) : ?>
    <div class="project-card__thumb" aria-hidden="true">
        <?php the_post_thumbnail( 'medium_large', [ 'loading' => 'lazy', 'alt' => '' ] ); ?>
    </div>
    <?php endif; ?>

    <div class="project-card__body">

        <header class="project-card__header">
            <div class="flex flex-gap" style="flex-wrap:wrap; margin-bottom:.75rem;">
                <?php if ( $project_type ) : ?>
                    <span class="tag"><?php echo esc_html( $project_type ); ?></span>
                <?php endif; ?>
                <?php if ( $client ) : ?>
                    <span class="tag tag--cyan"><?php echo esc_html( $client ); ?></span>
                <?php endif; ?>
            </div>
            <h3 class="project-card__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
        </header>

        <?php if ( $short_desc ) : ?>
            <p class="project-card__desc"><?php echo esc_html( $short_desc ); ?></p>
        <?php endif; ?>

        <!-- Tech tags -->
        <?php echo fhp_tech_tags_html( get_the_ID() ); ?>

        <!-- Actions -->
        <div class="project-card__actions flex flex-gap" style="margin-top:1.25rem; flex-wrap:wrap;">
            <a href="<?php the_permalink(); ?>" class="btn btn--outline btn--sm">
                <?php esc_html_e( 'Details', 'fuadhasan-portfolio' ); ?>
            </a>
            <?php if ( $live_url ) : ?>
                <a href="<?php echo esc_url( $live_url ); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="btn btn--primary btn--sm">
                    <?php esc_html_e( 'Live ↗', 'fuadhasan-portfolio' ); ?>
                </a>
            <?php endif; ?>
            <?php if ( $github_url ) : ?>
                <a href="<?php echo esc_url( $github_url ); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="btn btn--secondary btn--sm">
                    <?php esc_html_e( 'GitHub ↗', 'fuadhasan-portfolio' ); ?>
                </a>
            <?php endif; ?>
        </div>

    </div><!-- .project-card__body -->
</article>
