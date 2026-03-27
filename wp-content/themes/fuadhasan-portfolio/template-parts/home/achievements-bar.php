<?php
/**
 * Template Part: Achievements Bar (Home Page)
 *
 * A horizontal strip showing certification badges and top awards.
 * Shows at most 4 items; links to /achievements for the full list.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$ach_query = new WP_Query( [
    'post_type'      => 'achievement',
    'posts_per_page' => 4,
    'orderby'        => 'meta_value',
    'meta_key'       => 'issue_date',
    'order'          => 'DESC',
    'no_found_rows'  => true,
] );
?>

<section class="section section--light achievements-bar" aria-label="<?php esc_attr_e( 'Achievements Highlights', 'fuadhasan-portfolio' ); ?>">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Recognition', 'fuadhasan-portfolio' ),
            'heading' => __( 'Achievements & Certifications', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( $ach_query->have_posts() ) : ?>
            <div class="achievements-bar__grid grid-4">
                <?php while ( $ach_query->have_posts() ) : $ach_query->the_post();
                    $org  = fhp_field( 'issuing_organization' );
                    $date = fhp_field( 'issue_date' );
                    $icon = fhp_field( 'achievement_icon' );
                    $cred = fhp_field( 'credential_url' );
                ?>
                <div class="card achievements-bar__item">
                    <?php if ( $icon ) : ?>
                        <img src="<?php echo esc_url( $icon['url'] ); ?>"
                             alt="" width="48" height="48" loading="lazy"
                             class="achievements-bar__icon" aria-hidden="true">
                    <?php else : ?>
                        <span class="achievements-bar__icon-placeholder" aria-hidden="true">🏆</span>
                    <?php endif; ?>

                    <h4 class="achievements-bar__title"><?php the_title(); ?></h4>

                    <?php if ( $org ) : ?>
                        <p class="achievements-bar__org text-muted"><?php echo esc_html( $org ); ?></p>
                    <?php endif; ?>

                    <?php if ( $date ) : ?>
                        <p class="achievements-bar__date text-mono" style="font-size:var(--font-size-xs);">
                            <?php echo esc_html( fhp_format_date( $date, 'Y' ) ); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>

            <div class="text-center" style="margin-top:2rem;">
                <a href="<?php echo esc_url( home_url( '/achievements' ) ); ?>" class="btn btn--outline">
                    <?php esc_html_e( 'View All Achievements →', 'fuadhasan-portfolio' ); ?>
                </a>
            </div>
        <?php else : ?>
            <!-- Static fallback -->
            <div class="grid-4">
                <?php
                $static = [
                    [ 'title' => 'NopCommerce Certified Developer', 'org' => 'NopCommerce', 'year' => '2022' ],
                    [ 'title' => 'Prompt Engineering Foundations',  'org' => 'Brain Station 23', 'year' => '2023' ],
                    [ 'title' => 'ICPC Dhaka Regional Contestant',  'org' => 'ICPC Global', 'year' => '2019' ],
                    [ 'title' => 'Take-Off Programming Champion',   'org' => 'DIU', 'year' => '2017' ],
                ];
                foreach ( $static as $item ) : ?>
                <div class="card achievements-bar__item">
                    <span class="achievements-bar__icon-placeholder" aria-hidden="true">🏆</span>
                    <h4 class="achievements-bar__title"><?php echo esc_html( $item['title'] ); ?></h4>
                    <p class="text-muted"><?php echo esc_html( $item['org'] ); ?></p>
                    <p class="text-mono" style="font-size:var(--font-size-xs);"><?php echo esc_html( $item['year'] ); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
