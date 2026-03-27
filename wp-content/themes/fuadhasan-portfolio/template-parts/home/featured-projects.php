<?php
/**
 * Template Part: Featured Projects Grid (Home Page)
 *
 * Shows projects where `is_featured = 1`, ordered by ACF `order` field.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$featured = fhp_get_featured_projects( 4 );
?>

<section class="section section--dark featured-projects" aria-label="<?php esc_attr_e( 'Featured Projects', 'fuadhasan-portfolio' ); ?>">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'What I\'ve Built', 'fuadhasan-portfolio' ),
            'heading' => __( 'Featured Projects', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( $featured->have_posts() ) : ?>
            <div class="grid-2">
                <?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
                    <?php get_template_part( 'template-parts/project/project-card', null, [ 'featured' => true ] ); ?>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <!-- Static fallback before projects are added in the admin -->
            <div class="grid-2">
                <?php
                $static_projects = [
                    [
                        'title' => 'Shawpno eCommerce Platform',
                        'desc'  => 'Microservices-based eCommerce platform (gRPC, RabbitMQ, MongoDB, Next.js) for Bangladesh\'s largest supermarket chain.',
                        'type'  => 'B2C eCommerce',
                        'tech'  => ['ASP.NET Core','gRPC','RabbitMQ','MongoDB','Angular','Firebase'],
                        'url'   => 'https://www.shwapno.com/',
                    ],
                    [
                        'title' => 'Macsteel B2B eCommerce',
                        'desc'  => 'NopCommerce B2B platform for South Africa\'s top steel distributor with SAP ERP real-time sync across 10,000+ SKUs.',
                        'type'  => 'B2B eCommerce',
                        'tech'  => ['NopCommerce','C#','SAP ERP','MSSQL'],
                        'url'   => 'https://shop.macsteel.co.za/',
                    ],
                    [
                        'title' => 'Online POS System',
                        'desc'  => 'NopCommerce-based Point of Sale deployed in 50+ retail stores with barcode scanning, SMS OTP, and kitchen management.',
                        'type'  => 'POS System',
                        'tech'  => ['NopCommerce','C#','AJAX','SMS Gateway'],
                        'url'   => 'https://www.nop-station.com/point-of-sale-pos-for-nopcommerce',
                    ],
                    [
                        'title' => 'NopCommerce Core Contributions',
                        'desc'  => 'Open-source contributions: Facebook auth, MFA, reCAPTCHA for guest checkout, Google Analytics, Sendinblue integration.',
                        'type'  => 'Open Source',
                        'tech'  => ['NopCommerce','C#','ASP.NET Core'],
                        'url'   => 'https://github.com/nopSolutions/nopCommerce/commits?author=fuadhasan28',
                    ],
                ];
                foreach ( $static_projects as $p ) : ?>
                <div class="card project-card">
                    <div class="project-card__header">
                        <span class="tag"><?php echo esc_html( $p['type'] ); ?></span>
                    </div>
                    <h3 class="project-card__title"><?php echo esc_html( $p['title'] ); ?></h3>
                    <p class="project-card__desc"><?php echo esc_html( $p['desc'] ); ?></p>
                    <div class="tag-list" style="margin-top:1rem;">
                        <?php foreach ( $p['tech'] as $t ) : ?>
                            <span class="tag"><?php echo esc_html( $t ); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?php echo esc_url( $p['url'] ); ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="btn btn--outline btn--sm"
                       style="margin-top:1.25rem;">
                        <?php esc_html_e( 'View Project ↗', 'fuadhasan-portfolio' ); ?>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center" style="margin-top:2.5rem;">
            <a href="<?php echo esc_url( home_url( '/projects' ) ); ?>" class="btn btn--secondary">
                <?php esc_html_e( 'View All Projects →', 'fuadhasan-portfolio' ); ?>
            </a>
        </div>

    </div>
</section>
