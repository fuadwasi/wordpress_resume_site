<?php
/**
 * ACF Options Page Registration
 *
 * Registers the "Site Settings" options page that holds all
 * site-wide data: hero copy, profile photo, contact details,
 * social links, and the active CV file.
 *
 * Requires ACF Pro (acf_add_options_page is a Pro feature).
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'fhp_register_acf_options_pages' );

function fhp_register_acf_options_pages() {
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    // ---- Parent options page ----------------------------------------
    acf_add_options_page( [
        'page_title'  => __( 'Site Settings', 'fuadhasan-portfolio' ),
        'menu_title'  => __( 'Site Settings', 'fuadhasan-portfolio' ),
        'menu_slug'   => 'fhp-site-settings',
        'capability'  => 'manage_options',
        'icon_url'    => 'dashicons-admin-settings',
        'position'    => 3,
        'redirect'    => false,
    ] );

    // ---- Sub-pages --------------------------------------------------
    acf_add_options_sub_page( [
        'page_title'  => __( 'Hero & About', 'fuadhasan-portfolio' ),
        'menu_title'  => __( 'Hero & About', 'fuadhasan-portfolio' ),
        'menu_slug'   => 'fhp-hero-about',
        'parent_slug' => 'fhp-site-settings',
        'capability'  => 'manage_options',
    ] );

    acf_add_options_sub_page( [
        'page_title'  => __( 'Contact & Social', 'fuadhasan-portfolio' ),
        'menu_title'  => __( 'Contact & Social', 'fuadhasan-portfolio' ),
        'menu_slug'   => 'fhp-contact-social',
        'parent_slug' => 'fhp-site-settings',
        'capability'  => 'manage_options',
    ] );

    acf_add_options_sub_page( [
        'page_title'  => __( 'CV / Resume', 'fuadhasan-portfolio' ),
        'menu_title'  => __( 'CV / Resume', 'fuadhasan-portfolio' ),
        'menu_slug'   => 'fhp-cv-settings',
        'parent_slug' => 'fhp-site-settings',
        'capability'  => 'manage_options',
    ] );

    acf_add_options_sub_page( [
        'page_title'  => __( 'Stats & Counters', 'fuadhasan-portfolio' ),
        'menu_title'  => __( 'Stats & Counters', 'fuadhasan-portfolio' ),
        'menu_slug'   => 'fhp-stats',
        'parent_slug' => 'fhp-site-settings',
        'capability'  => 'manage_options',
    ] );
}
