<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0f">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php if ( ! function_exists( 'yoast_head' ) && ! function_exists( 'rank_math_head' ) ) : ?>
        <title><?php echo fhp_page_title(); ?></title>
        <meta name="description" content="<?php bloginfo( 'description' ); ?>">
    <?php endif; ?>

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Skip navigation for screen readers -->
<a class="skip-link" href="#main-content">
    <?php esc_html_e( 'Skip to content', 'fuadhasan-portfolio' ); ?>
</a>

<!-- ================================================================
     SITE HEADER
     ================================================================ -->
<header class="site-header" id="site-header" role="banner">
    <div class="container site-header__inner">

        <!-- Logo / Name -->
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__logo" rel="home" aria-label="<?php esc_attr_e( 'Home', 'fuadhasan-portfolio' ); ?>">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <span class="site-header__name">
                    <?php echo esc_html( fhp_option( 'hero_name', get_bloginfo( 'name' ) ) ); ?>
                </span>
                <span class="site-header__tagline text-mono text-cyan">&lt;dev /&gt;</span>
            <?php endif; ?>
        </a>

        <!-- Primary Navigation -->
        <nav class="site-nav" id="site-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'fuadhasan-portfolio' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location'  => 'primary',
                'menu_id'         => 'primary-menu',
                'menu_class'      => 'site-nav__list',
                'container'       => false,
                'fallback_cb'     => 'fhp_fallback_nav',
                'depth'           => 1,
            ] );
            ?>
        </nav>

        <!-- Header CTA: Download CV -->
        <a href="<?php echo esc_url( fhp_get_cv_url() ); ?>"
           class="btn btn--primary btn--sm site-header__cta"
           aria-label="<?php esc_attr_e( 'Download CV', 'fuadhasan-portfolio' ); ?>">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            <?php echo esc_html( fhp_option( 'cv_download_label', __( 'Download CV', 'fuadhasan-portfolio' ) ) ); ?>
        </a>

        <!-- Mobile hamburger toggle -->
        <button class="site-header__hamburger" id="nav-toggle" aria-controls="site-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'fuadhasan-portfolio' ); ?>">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>

    </div><!-- .site-header__inner -->
</header><!-- .site-header -->

<main id="main-content" class="site-main" role="main">
<?php
/**
 * Fallback navigation when no menu has been assigned.
 * Renders the default page map from the Site Architecture spec.
 */
function fhp_fallback_nav() {
    $pages = [
        home_url( '/' )               => __( 'Home',         'fuadhasan-portfolio' ),
        home_url( '/about' )          => __( 'About',        'fuadhasan-portfolio' ),
        home_url( '/experience' )     => __( 'Experience',   'fuadhasan-portfolio' ),
        home_url( '/projects' )       => __( 'Projects',     'fuadhasan-portfolio' ),
        home_url( '/skills' )         => __( 'Skills',       'fuadhasan-portfolio' ),
        home_url( '/achievements' )   => __( 'Achievements', 'fuadhasan-portfolio' ),
        home_url( '/contact' )        => __( 'Contact',      'fuadhasan-portfolio' ),
    ];

    echo '<ul class="site-nav__list" id="primary-menu">';
    foreach ( $pages as $url => $label ) {
        $current = ( rtrim( $_SERVER['REQUEST_URI'], '/' ) === rtrim( parse_url( $url, PHP_URL_PATH ), '/' ) )
            ? ' aria-current="page"' : '';
        echo '<li class="site-nav__item">';
        echo '<a href="' . esc_url( $url ) . '" class="site-nav__link"' . $current . '>' . esc_html( $label ) . '</a>';
        echo '</li>';
    }
    echo '</ul>';
}
