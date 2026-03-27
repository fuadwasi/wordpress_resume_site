<?php
/**
 * Custom Post Types: Experience, Project, Skill, Achievement
 *
 * @package fuadhasan-portfolio
 */

defined( 'ABSPATH' ) || exit;

function fhp_register_post_types() {

	// Experience
	register_post_type( 'fhp_experience', [
		'labels'      => [
			'name'          => __( 'Experience', 'fuadhasan-portfolio' ),
			'singular_name' => __( 'Experience', 'fuadhasan-portfolio' ),
			'add_new_item'  => __( 'Add New Experience', 'fuadhasan-portfolio' ),
		],
		'public'      => true,
		'has_archive' => false,
		'menu_icon'   => 'dashicons-businessman',
		'supports'    => [ 'title', 'editor', 'thumbnail' ],
		'show_in_rest' => true,
	] );

	// Project
	register_post_type( 'fhp_project', [
		'labels'      => [
			'name'          => __( 'Projects', 'fuadhasan-portfolio' ),
			'singular_name' => __( 'Project', 'fuadhasan-portfolio' ),
			'add_new_item'  => __( 'Add New Project', 'fuadhasan-portfolio' ),
		],
		'public'      => true,
		'has_archive' => false,
		'menu_icon'   => 'dashicons-portfolio',
		'supports'    => [ 'title', 'editor', 'thumbnail' ],
		'show_in_rest' => true,
	] );

	// Skill
	register_post_type( 'fhp_skill', [
		'labels'      => [
			'name'          => __( 'Skills', 'fuadhasan-portfolio' ),
			'singular_name' => __( 'Skill', 'fuadhasan-portfolio' ),
			'add_new_item'  => __( 'Add New Skill', 'fuadhasan-portfolio' ),
		],
		'public'      => false,
		'show_ui'     => true,
		'menu_icon'   => 'dashicons-awards',
		'supports'    => [ 'title' ],
		'show_in_rest' => true,
	] );

	// Achievement
	register_post_type( 'fhp_achievement', [
		'labels'      => [
			'name'          => __( 'Achievements', 'fuadhasan-portfolio' ),
			'singular_name' => __( 'Achievement', 'fuadhasan-portfolio' ),
			'add_new_item'  => __( 'Add New Achievement', 'fuadhasan-portfolio' ),
		],
		'public'      => false,
		'show_ui'     => true,
		'menu_icon'   => 'dashicons-star-filled',
		'supports'    => [ 'title', 'editor' ],
		'show_in_rest' => true,
	] );
}
add_action( 'init', 'fhp_register_post_types' );
