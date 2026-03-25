<?php
/**
 * Template Part: Professional Summary (Home Page)
 *
 * Short bio + key achievement pills pulled from ACF options.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$summary = fhp_option( 'about_summary' );
if ( ! $summary ) {
    $summary = '<p>Certified NopCommerce Developer and Senior Software Engineer with 3+ years of experience designing and scaling B2B &amp; B2C eCommerce platforms, ERP integrations, and enterprise-grade plugins. Skilled in ASP.NET Core, C#, and modern microservices architecture, I help businesses improve operations and customer experience through robust, future-ready solutions.</p>';
}

$key_achievements = [
    __( 'Integrated SAP ERP for Macsteel — real-time sync across 10,000+ SKUs',    'fuadhasan-portfolio' ),
    __( 'Led NopCommerce POS system development used in 50+ retail stores',         'fuadhasan-portfolio' ),
    __( 'NopCommerce core contributor (Facebook auth, MFA, discount management)',   'fuadhasan-portfolio' ),
    __( 'Built Shawpno microservices platform — 30% checkout latency reduction',    'fuadhasan-portfolio' ),
    __( 'ICPC Dhaka Regional Onsite Contestant — Rank 96 / 300',                   'fuadhasan-portfolio' ),
    __( 'DIU Take-Off Programming Contest Champion — Rank 1 / 300',                 'fuadhasan-portfolio' ),
];
?>

<section class="section section--light summary-section" aria-label="<?php esc_attr_e( 'Professional Summary', 'fuadhasan-portfolio' ); ?>">
    <div class="container">

        <div class="summary-section__grid">

            <div class="summary-section__bio">
                <?php get_template_part( 'template-parts/shared/section-header', null, [
                    'label'   => __( 'Who I Am', 'fuadhasan-portfolio' ),
                    'heading' => __( 'Summary',  'fuadhasan-portfolio' ),
                    'align'   => 'left',
                ] ); ?>

                <div class="entry-content">
                    <?php echo wp_kses_post( $summary ); ?>
                </div>

                <a href="<?php echo esc_url( home_url( '/about' ) ); ?>" class="btn btn--outline" style="margin-top:1.5rem;">
                    <?php esc_html_e( 'Read More About Me →', 'fuadhasan-portfolio' ); ?>
                </a>
            </div>

            <div class="summary-section__achievements">
                <?php get_template_part( 'template-parts/shared/section-header', null, [
                    'label'   => __( 'Highlights', 'fuadhasan-portfolio' ),
                    'heading' => __( 'Key Achievements', 'fuadhasan-portfolio' ),
                    'align'   => 'left',
                ] ); ?>

                <ul class="summary-section__achievement-list">
                    <?php foreach ( $key_achievements as $item ) : ?>
                    <li class="summary-section__achievement-item">
                        <span class="summary-section__achievement-icon" aria-hidden="true">✓</span>
                        <?php echo esc_html( $item ); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>

    </div>
</section>
