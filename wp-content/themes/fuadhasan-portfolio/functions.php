<?php
/**
 * Fuad Hasan Portfolio Theme Functions
 *
 * @package fuadhasan-portfolio
 */

defined( 'ABSPATH' ) || exit;

define( 'FHP_VERSION', '1.0.0' );
define( 'FHP_DIR',     get_template_directory() );
define( 'FHP_URI',     get_template_directory_uri() );

/* --------------------------------------------------------------------------
 * Theme Setup
 * -------------------------------------------------------------------------- */
function fhp_setup() {
	load_theme_textdomain( 'fuadhasan-portfolio', FHP_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ] );
	add_theme_support( 'custom-logo', [
		'height'      => 60,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	] );
	add_theme_support( 'customize-selective-refresh-widgets' );

	register_nav_menus( [
		'primary' => __( 'Primary Navigation', 'fuadhasan-portfolio' ),
	] );
}
add_action( 'after_setup_theme', 'fhp_setup' );

/* --------------------------------------------------------------------------
 * Enqueue Assets
 * -------------------------------------------------------------------------- */
function fhp_enqueue_assets() {
	wp_enqueue_style(
		'fhp-style',
		get_stylesheet_uri(),
		[],
		FHP_VERSION
	);

	wp_enqueue_script(
		'fhp-main',
		FHP_URI . '/assets/js/main.js',
		[],
		FHP_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'fhp_enqueue_assets' );

/* --------------------------------------------------------------------------
 * Menus / Widget Areas
 * -------------------------------------------------------------------------- */
function fhp_widgets_init() {
	register_sidebar( [
		'name'          => __( 'Footer Widgets', 'fuadhasan-portfolio' ),
		'id'            => 'footer-widgets',
		'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	] );
}
add_action( 'widgets_init', 'fhp_widgets_init' );

/* --------------------------------------------------------------------------
 * Include Feature Files
 * -------------------------------------------------------------------------- */
$fhp_includes = [
	'/inc/cpt.php',
	'/inc/acf-fields.php',
	'/inc/shortcodes.php',
	'/inc/helpers.php',
];

foreach ( $fhp_includes as $file ) {
	if ( file_exists( FHP_DIR . $file ) ) {
		require_once FHP_DIR . $file;
	}
}
