<?php
/**
 * Template Part: Hero Section (Home Page)
 *
 * Displays the full-width hero with:
 *   - Name + animated title
 *   - Subtitle / tagline
 *   - [Download CV] + [Contact Me] CTA buttons
 *   - Stats bar (years experience, projects, contributions)
 *   - Optional profile photo
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

$name        = fhp_option( 'hero_name',    'Fuad Hasan' );
$title       = fhp_option( 'hero_title',   'Senior Software Engineer II' );
$subtitle    = fhp_option( 'hero_subtitle', 'Certified NopCommerce Developer · ASP.NET Core · Microservices · B2B & B2C eCommerce' );
$photo       = fhp_option( 'profile_photo' );
$hero_bg     = fhp_option( 'hero_background_image' );
$cv_label    = fhp_option( 'cv_download_label', __( 'Download CV', 'fuadhasan-portfolio' ) );
$years_exp   = fhp_option( 'years_of_experience', 5 );
$total_proj  = fhp_option( 'total_projects', 20 );
$open_source = fhp_option( 'open_source_contributions', 50 );

$hero_style = '';
if ( $hero_bg && ! empty( $hero_bg['url'] ) ) {
    $hero_style = ' style="background-image: url(' . esc_url( $hero_bg['url'] ) . ');"';
}
?>

<section class="hero"<?php echo $hero_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> aria-label="<?php esc_attr_e( 'Introduction', 'fuadhasan-portfolio' ); ?>">

    <!-- Decorative gradient orbs (CSS-driven) -->
    <div class="hero__orb hero__orb--1" aria-hidden="true"></div>
    <div class="hero__orb hero__orb--2" aria-hidden="true"></div>

    <div class="container hero__inner">

        <!-- Content column -->
        <div class="hero__content animate-fade-in-up stagger">

            <!-- Greeting badge -->
            <div class="hero__badge">
                <span class="tag tag--cyan text-mono">
                    &lt;?php echo "Hello, World!"; ?&gt;
                </span>
            </div>

            <!-- Name -->
            <h1 class="hero__name">
                <?php echo esc_html( $name ); ?>
            </h1>

            <!-- Animated title (JS cycles through multiple titles) -->
            <h2 class="hero__title" id="hero-title" aria-live="polite">
                <span class="hero__title-static"><?php esc_html_e( 'I\'m a ', 'fuadhasan-portfolio' ); ?></span>
                <span class="hero__title-animated text-accent" data-titles='<?php
                    echo esc_attr( json_encode( [
                        $title,
                        'NopCommerce Expert',
                        'eCommerce Architect',
                        'ASP.NET Core Developer',
                        'Open Source Contributor',
                    ] ) );
                ?>'><?php echo esc_html( $title ); ?></span>
            </h2>

            <!-- Subtitle / tagline -->
            <?php if ( $subtitle ) : ?>
            <p class="hero__subtitle">
                <?php echo esc_html( $subtitle ); ?>
            </p>
            <?php endif; ?>

            <!-- CTA buttons -->
            <div class="hero__cta flex flex-gap" style="flex-wrap:wrap;">
                <a href="<?php echo esc_url( fhp_get_cv_url() ); ?>"
                   class="btn btn--primary"
                   aria-label="<?php esc_attr_e( 'Download CV', 'fuadhasan-portfolio' ); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <?php echo esc_html( $cv_label ); ?>
                </a>

                <a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"
                   class="btn btn--secondary"
                   aria-label="<?php esc_attr_e( 'Contact Me', 'fuadhasan-portfolio' ); ?>">
                    <?php esc_html_e( 'Contact Me', 'fuadhasan-portfolio' ); ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path d="m5 12 14 0"/><path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Quick stats -->
            <div class="hero__stats" aria-label="<?php esc_attr_e( 'Career stats', 'fuadhasan-portfolio' ); ?>">
                <div class="hero__stat">
                    <strong class="hero__stat-number" data-count="<?php echo esc_attr( $years_exp ); ?>">
                        <?php echo esc_html( $years_exp ); ?>+
                    </strong>
                    <span class="hero__stat-label"><?php esc_html_e( 'Years Experience', 'fuadhasan-portfolio' ); ?></span>
                </div>
                <div class="hero__stat-divider" aria-hidden="true"></div>
                <div class="hero__stat">
                    <strong class="hero__stat-number" data-count="<?php echo esc_attr( $total_proj ); ?>">
                        <?php echo esc_html( $total_proj ); ?>+
                    </strong>
                    <span class="hero__stat-label"><?php esc_html_e( 'Projects Delivered', 'fuadhasan-portfolio' ); ?></span>
                </div>
                <div class="hero__stat-divider" aria-hidden="true"></div>
                <div class="hero__stat">
                    <strong class="hero__stat-number" data-count="<?php echo esc_attr( $open_source ); ?>">
                        <?php echo esc_html( $open_source ); ?>+
                    </strong>
                    <span class="hero__stat-label"><?php esc_html_e( 'Open Source Contributions', 'fuadhasan-portfolio' ); ?></span>
                </div>
            </div>

        </div><!-- .hero__content -->

        <!-- Photo column -->
        <?php if ( $photo && ! empty( $photo['url'] ) ) : ?>
        <div class="hero__photo animate-fade-in" style="animation-delay:200ms;" aria-hidden="true">
            <div class="hero__photo-frame">
                <img src="<?php echo esc_url( $photo['url'] ); ?>"
                     alt="<?php echo esc_attr( $name ); ?>"
                     width="<?php echo esc_attr( $photo['width'] ?? 480 ); ?>"
                     height="<?php echo esc_attr( $photo['height'] ?? 480 ); ?>"
                     loading="eager">
            </div>
        </div>
        <?php endif; ?>

    </div><!-- .hero__inner -->

    <!-- Scroll indicator -->
    <div class="hero__scroll-indicator" aria-hidden="true">
        <span></span>
    </div>

</section><!-- .hero -->
