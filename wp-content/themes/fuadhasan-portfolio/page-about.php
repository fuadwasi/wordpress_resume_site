<?php
/**
 * About Page Template — /about
 *
 * Template Name: About
 *
 * Displays the full professional summary, career focus statement,
 * coding profiles, and education history pulled from ACF options
 * and static content.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$name    = fhp_option( 'hero_name',    'Fuad Hasan' );
$title   = fhp_option( 'hero_title',   'Senior Software Engineer II' );
$photo   = fhp_option( 'profile_photo' );
$summary = fhp_option( 'about_summary' );
?>

<!-- ABOUT HERO -->
<section class="section section--dark about-hero">
    <div class="container">
        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'    => __( 'About Me',           'fuadhasan-portfolio' ),
            'heading'  => $name,
            'subheading' => $title,
        ] ); ?>

        <div class="about-hero__grid">

            <?php if ( $photo ) : ?>
            <div class="about-hero__photo">
                <img src="<?php echo esc_url( $photo['url'] ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>"
                     width="<?php echo esc_attr( $photo['width'] ?? 400 ); ?>"
                     height="<?php echo esc_attr( $photo['height'] ?? 400 ); ?>"
                     loading="eager">
            </div>
            <?php endif; ?>

            <div class="about-hero__content">
                <?php if ( $summary ) : ?>
                    <div class="about-hero__summary entry-content">
                        <?php echo wp_kses_post( $summary ); ?>
                    </div>
                <?php else : ?>
                    <p><?php esc_html_e( 'Certified NopCommerce Developer and Senior Software Engineer with 3+ years of experience designing and scaling B2B & B2C eCommerce platforms, ERP integrations, and enterprise-grade plugins.', 'fuadhasan-portfolio' ); ?></p>
                <?php endif; ?>

                <?php get_template_part( 'template-parts/shared/cta-buttons', null, [
                    'show_contact' => true,
                    'show_cv'      => true,
                ] ); ?>
            </div>

        </div><!-- .about-hero__grid -->
    </div>
</section>

<!-- CODING PROFILES -->
<section class="section section--light about-profiles">
    <div class="container">
        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Online',       'fuadhasan-portfolio' ),
            'heading' => __( 'Coding Profiles', 'fuadhasan-portfolio' ),
        ] ); ?>

        <div class="grid-3">
            <?php
            $profiles = [
                [
                    'label' => 'GitHub',
                    'url'   => fhp_option( 'github_url', 'https://github.com/fuadwasi' ),
                    'note'  => 'fuadwasi',
                    'icon'  => 'github',
                ],
                [
                    'label' => 'LinkedIn',
                    'url'   => fhp_option( 'linkedin_url', 'https://www.linkedin.com/in/fuadwasi/' ),
                    'note'  => 'fuadwasi',
                    'icon'  => 'linkedin',
                ],
                [
                    'label' => 'Codeforces',
                    'url'   => fhp_option( 'codeforces_url', 'https://codeforces.com/profile/fhwasi' ),
                    'note'  => 'fhwasi',
                    'icon'  => 'code',
                ],
            ];
            foreach ( $profiles as $p ) :
            ?>
            <a href="<?php echo esc_url( $p['url'] ); ?>" target="_blank" rel="noopener noreferrer" class="card card--elevated about-profile-card">
                <?php fhp_icon( $p['icon'], 'about-profile-card__icon' ); ?>
                <strong><?php echo esc_html( $p['label'] ); ?></strong>
                <span class="text-muted text-mono"><?php echo esc_html( $p['note'] ); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php get_footer();
