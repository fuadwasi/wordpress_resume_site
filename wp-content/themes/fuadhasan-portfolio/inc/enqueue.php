<?php
/**
 * Asset Enqueue
 *
 * Registers and enqueues all CSS and JavaScript assets for the
 * Fuad Hasan Portfolio theme, including Google Fonts.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', 'fhp_enqueue_assets' );

function fhp_enqueue_assets() {

    $ver = FHP_VERSION;

    /* ----------------------------------------------------------
       Google Fonts — Inter + Fira Code
    ---------------------------------------------------------- */
    wp_enqueue_style(
        'fhp-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap',
        [],
        null
    );

    /* ----------------------------------------------------------
       Main theme stylesheet (style.css — design tokens + base)
    ---------------------------------------------------------- */
    wp_enqueue_style(
        'fhp-style',
        get_stylesheet_uri(),
        [ 'fhp-google-fonts' ],
        $ver
    );

    /* ----------------------------------------------------------
       Modular CSS
    ---------------------------------------------------------- */
    $css_files = [
        'fhp-main'       => '/assets/css/main.css',
        'fhp-hero'       => '/assets/css/hero.css',
        'fhp-timeline'   => '/assets/css/timeline.css',
        'fhp-responsive' => '/assets/css/responsive.css',
    ];

    $prev = 'fhp-style';
    foreach ( $css_files as $handle => $path ) {
        wp_enqueue_style( $handle, FHP_ASSETS . $path, [ $prev ], $ver );
        $prev = $handle;
    }

    /* ----------------------------------------------------------
       JavaScript — defer non-critical scripts
    ---------------------------------------------------------- */

    // Smooth scroll (no dependencies)
    wp_enqueue_script(
        'fhp-smooth-scroll',
        FHP_ASSETS . '/js/smooth-scroll.js',
        [],
        $ver,
        true // load in footer
    );

    // Skills animation (depends on IntersectionObserver — native browser API)
    wp_enqueue_script(
        'fhp-skills-animation',
        FHP_ASSETS . '/js/skills-animation.js',
        [],
        $ver,
        true
    );

    // Main JS (depends on both above)
    wp_enqueue_script(
        'fhp-main',
        FHP_ASSETS . '/js/main.js',
        [ 'fhp-smooth-scroll', 'fhp-skills-animation' ],
        $ver,
        true
    );

    /* ----------------------------------------------------------
       Localise script data (pass PHP data to JS)
    ---------------------------------------------------------- */
    wp_localize_script( 'fhp-main', 'fhpData', [
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'siteUrl'   => home_url( '/' ),
        'cvUrl'     => home_url( '/download-cv' ),
        'nonce'     => wp_create_nonce( 'fhp_nonce' ),
    ] );
}

/* ------------------------------------------------------------------
   Admin-side enqueue (for options page UX)
------------------------------------------------------------------ */
add_action( 'admin_enqueue_scripts', 'fhp_admin_enqueue' );

function fhp_admin_enqueue( $hook ) {
    // Only load on ACF options pages
    if ( strpos( $hook, 'fhp-' ) !== false ) {
        wp_enqueue_style(
            'fhp-admin',
            FHP_ASSETS . '/css/admin.css',
            [],
            FHP_VERSION
        );
    }
}
