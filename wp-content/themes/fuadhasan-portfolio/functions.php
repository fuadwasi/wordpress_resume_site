<?php
/**
 * Fuad Hasan Portfolio — Theme Bootstrap
 *
 * Loads all modular includes in the correct order and registers
 * core WordPress theme features.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------
   Constants
------------------------------------------------------------------ */
define( 'FHP_VERSION',   '1.0.0' );
define( 'FHP_DIR',       get_template_directory() );
define( 'FHP_URI',       get_template_directory_uri() );
define( 'FHP_ASSETS',    FHP_URI  . '/assets' );
define( 'FHP_INC',       FHP_DIR  . '/inc' );

/* ------------------------------------------------------------------
   Load modular includes
------------------------------------------------------------------ */
$fhp_includes = [
    '/helpers.php',              // Utility functions (must load first)
    '/custom-post-types.php',    // Register CPTs + taxonomies
    '/acf-options.php',          // ACF Options Page
    '/acf-fields.php',           // ACF field group definitions
    '/enqueue.php',              // Enqueue CSS & JS
    '/cv-download.php',          // /download-cv URL handler
    '/seeder.php',               // Pre-populated CPT content (runs once on activation)
    '/plugin-compat.php',        // Section 6: plugin integration layer
    '/recommended-plugins.php',  // Section 6: admin notice for recommended plugins
];

foreach ( $fhp_includes as $file ) {
    $path = FHP_INC . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

/* ------------------------------------------------------------------
   Theme Setup
------------------------------------------------------------------ */
add_action( 'after_setup_theme', function () {

    // Make theme translatable
    load_theme_textdomain( 'fuadhasan-portfolio', FHP_DIR . '/languages' );

    // WordPress core features
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', [
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );

    // Custom logo
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // Navigation menus
    register_nav_menus( [
        'primary' => __( 'Primary Navigation', 'fuadhasan-portfolio' ),
        'footer'  => __( 'Footer Navigation',  'fuadhasan-portfolio' ),
    ] );

    // Set content width
    if ( ! isset( $content_width ) ) {
        $GLOBALS['content_width'] = 1200;
    }

} );

/* ------------------------------------------------------------------
   Widget areas
------------------------------------------------------------------ */
add_action( 'widgets_init', function () {
    register_sidebar( [
        'name'          => __( 'Sidebar', 'fuadhasan-portfolio' ),
        'id'            => 'sidebar-1',
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget__title">',
        'after_title'   => '</h3>',
    ] );
} );

/* ------------------------------------------------------------------
   Flush rewrite rules on theme switch (for custom slugs)
------------------------------------------------------------------ */
add_action( 'after_switch_theme', function () {
    fhp_register_post_types(); // ensure CPTs are registered first
    flush_rewrite_rules();
} );
