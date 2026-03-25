<?php
/**
 * Template Part: Experience Timeline Item
 *
 * Used in archive-experience.php within a .experience-timeline wrapper.
 * Reads ACF fields from the current post in the loop.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$company     = fhp_field( 'company_name' );
$job_title   = fhp_field( 'job_title' );
$is_current  = fhp_field( 'is_current' );
$start_date  = fhp_field( 'start_date' );
$end_date    = fhp_field( 'end_date' );
$location    = fhp_field( 'location' );
$description = fhp_field( 'job_description' );
$contributions = fhp_field( 'key_contributions' );
$references  = fhp_field( 'project_references' );
$company_url = fhp_field( 'company_url' );
$logo        = fhp_field( 'company_logo' );
$date_range  = fhp_date_range( $start_date, $end_date, (bool) $is_current );
?>

<div class="timeline-item <?php echo $is_current ? 'timeline-item--current' : ''; ?>" data-post-id="<?php echo esc_attr( get_the_ID() ); ?>">

    <!-- Timeline dot -->
    <div class="timeline-item__dot" aria-hidden="true">
        <?php if ( $is_current ) : ?>
            <span class="timeline-item__dot-pulse"></span>
        <?php endif; ?>
    </div>

    <!-- Card content -->
    <article class="card timeline-item__card">

        <header class="timeline-item__header">
            <div class="timeline-item__header-content">
                <?php if ( $logo ) : ?>
                    <img src="<?php echo esc_url( $logo['url'] ); ?>"
                         alt="<?php echo esc_attr( $company ); ?>"
                         width="48" height="48" loading="lazy"
                         class="timeline-item__logo">
                <?php endif; ?>

                <div class="timeline-item__title-group">
                    <h3 class="timeline-item__job-title"><?php echo esc_html( $job_title ?: get_the_title() ); ?></h3>

                    <?php if ( $company_url ) : ?>
                        <a href="<?php echo esc_url( $company_url ); ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="timeline-item__company">
                            <?php echo esc_html( $company ); ?> ↗
                        </a>
                    <?php else : ?>
                        <span class="timeline-item__company"><?php echo esc_html( $company ); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="timeline-item__meta">
                <span class="tag"><?php echo esc_html( $date_range ); ?></span>
                <?php if ( $location ) : ?>
                    <span class="tag tag--cyan"><?php echo esc_html( $location ); ?></span>
                <?php endif; ?>
                <?php if ( $is_current ) : ?>
                    <span class="tag" style="background:rgba(72,187,120,.15);color:#48bb78;border-color:rgba(72,187,120,.3);">
                        <?php esc_html_e( 'Current', 'fuadhasan-portfolio' ); ?>
                    </span>
                <?php endif; ?>
            </div>
        </header>

        <!-- Technologies -->
        <?php echo fhp_tech_tags_html( get_the_ID() ); ?>

        <!-- Short description / key contributions -->
        <?php if ( $description ) : ?>
            <div class="timeline-item__description entry-content">
                <?php echo wp_kses_post( $description ); ?>
            </div>
        <?php elseif ( $contributions ) : ?>
            <ul class="timeline-item__contributions">
                <?php
                $limit = 3; $count = 0;
                foreach ( $contributions as $c ) :
                    if ( $count >= $limit ) break;
                    $count++;
                ?>
                    <li><?php echo esc_html( $c['contribution_text'] ); ?></li>
                <?php endforeach; ?>
                <?php if ( count( $contributions ) > $limit ) : ?>
                    <li><a href="<?php echo esc_url( get_permalink() ); ?>">
                        +<?php echo esc_html( count( $contributions ) - $limit ); ?> <?php esc_html_e( 'more…', 'fuadhasan-portfolio' ); ?>
                    </a></li>
                <?php endif; ?>
            </ul>
        <?php endif; ?>

        <!-- Project references -->
        <?php if ( $references ) : ?>
        <div class="timeline-item__refs">
            <?php foreach ( $references as $ref ) : ?>
                <a href="<?php echo esc_url( $ref['ref_url'] ); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="btn btn--outline btn--sm">
                    <?php echo esc_html( $ref['ref_name'] ); ?> ↗
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </article>
</div><!-- .timeline-item -->
