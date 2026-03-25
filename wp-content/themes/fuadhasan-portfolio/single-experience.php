<?php
/**
 * Single Experience Template — /experience/{slug}
 *
 * Detailed view of a single work experience entry.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();
the_post();

$company       = fhp_field( 'company_name' );
$job_title     = fhp_field( 'job_title' );
$is_current    = fhp_field( 'is_current' );
$start_date    = fhp_field( 'start_date' );
$end_date      = fhp_field( 'end_date' );
$location      = fhp_field( 'location' );
$company_url   = fhp_field( 'company_url' );
$description   = fhp_field( 'job_description' );
$contributions = fhp_field( 'key_contributions' );
$references    = fhp_field( 'project_references' );
$date_range    = fhp_date_range( $start_date, $end_date, (bool) $is_current );
?>

<article class="section section--dark single-experience" <?php post_class(); ?>>
    <div class="container" style="max-width:860px;">

        <!-- Back link -->
        <a href="<?php echo esc_url( get_post_type_archive_link( 'experience' ) ); ?>" class="btn btn--outline btn--sm" style="margin-bottom:2rem;">
            ← <?php esc_html_e( 'All Experience', 'fuadhasan-portfolio' ); ?>
        </a>

        <header class="single-experience__header card">
            <?php
            $logo = fhp_field( 'company_logo' );
            if ( $logo ) :
            ?>
            <img src="<?php echo esc_url( $logo['url'] ); ?>"
                 alt="<?php echo esc_attr( $company ); ?> logo"
                 class="single-experience__logo"
                 width="120" height="60" loading="lazy">
            <?php endif; ?>

            <h1 class="single-experience__title"><?php echo esc_html( $job_title ); ?></h1>

            <?php if ( $company_url ) : ?>
                <a href="<?php echo esc_url( $company_url ); ?>" target="_blank" rel="noopener noreferrer" class="single-experience__company">
                    <?php echo esc_html( $company ); ?> ↗
                </a>
            <?php else : ?>
                <span class="single-experience__company"><?php echo esc_html( $company ); ?></span>
            <?php endif; ?>

            <div class="single-experience__meta flex flex-gap" style="flex-wrap:wrap; margin-top:1rem;">
                <span class="tag"><?php echo esc_html( $date_range ); ?></span>
                <?php if ( $location ) : ?>
                    <span class="tag tag--cyan"><?php echo esc_html( $location ); ?></span>
                <?php endif; ?>
                <?php echo fhp_tech_tags_html( get_the_ID() ); ?>
            </div>
        </header>

        <?php if ( $description ) : ?>
        <div class="single-experience__description card entry-content" style="margin-top:1.5rem;">
            <?php echo wp_kses_post( $description ); ?>
        </div>
        <?php endif; ?>

        <?php if ( $contributions ) : ?>
        <div class="single-experience__contributions card" style="margin-top:1.5rem;">
            <h2><?php esc_html_e( 'Key Contributions', 'fuadhasan-portfolio' ); ?></h2>
            <ul class="single-experience__contrib-list" style="margin-top:1rem;">
                <?php foreach ( $contributions as $item ) : ?>
                    <li><?php echo esc_html( $item['contribution_text'] ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ( $references ) : ?>
        <div class="single-experience__refs card" style="margin-top:1.5rem;">
            <h2><?php esc_html_e( 'Project References', 'fuadhasan-portfolio' ); ?></h2>
            <ul style="margin-top:1rem; display:flex; flex-wrap:wrap; gap:0.75rem; list-style:none;">
                <?php foreach ( $references as $ref ) : ?>
                    <li>
                        <a href="<?php echo esc_url( $ref['ref_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline btn--sm">
                            <?php echo esc_html( $ref['ref_name'] ); ?> ↗
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

    </div>
</article>

<?php get_footer();
