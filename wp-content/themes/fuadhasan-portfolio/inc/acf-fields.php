<?php
/**
 * ACF Field Group registrations (fallback if ACF is active)
 *
 * @package fuadhasan-portfolio
 */

defined( 'ABSPATH' ) || exit;

// Only register fields if ACF Pro or ACF is active
if ( ! function_exists( 'acf_add_local_field_group' ) ) {
	return;
}

/* --------------------------------------------------------------------------
 * Experience Fields
 * -------------------------------------------------------------------------- */
acf_add_local_field_group( [
	'key'      => 'group_experience',
	'title'    => 'Experience Details',
	'fields'   => [
		[ 'key' => 'field_company',      'label' => 'Company Name',  'name' => 'company_name',  'type' => 'text' ],
		[ 'key' => 'field_role',         'label' => 'Role / Title',  'name' => 'role_title',    'type' => 'text' ],
		[ 'key' => 'field_start_date',   'label' => 'Start Date',    'name' => 'start_date',    'type' => 'date_picker', 'display_format' => 'M Y', 'return_format' => 'Y-m' ],
		[ 'key' => 'field_end_date',     'label' => 'End Date',      'name' => 'end_date',      'type' => 'date_picker', 'display_format' => 'M Y', 'return_format' => 'Y-m' ],
		[ 'key' => 'field_current_role', 'label' => 'Current Role?', 'name' => 'current_role',  'type' => 'true_false' ],
		[ 'key' => 'field_tech_stack',   'label' => 'Tech Stack',    'name' => 'tech_stack',    'type' => 'textarea', 'instructions' => 'Comma-separated list, e.g. PHP, WordPress, React' ],
	],
	'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'fhp_experience' ] ] ],
] );

/* --------------------------------------------------------------------------
 * Project Fields
 * -------------------------------------------------------------------------- */
acf_add_local_field_group( [
	'key'      => 'group_project',
	'title'    => 'Project Details',
	'fields'   => [
		[ 'key' => 'field_proj_tech',    'label' => 'Technologies',  'name' => 'technologies',  'type' => 'textarea', 'instructions' => 'Comma-separated list' ],
		[ 'key' => 'field_proj_github',  'label' => 'GitHub URL',    'name' => 'github_url',    'type' => 'url' ],
		[ 'key' => 'field_proj_live',    'label' => 'Live URL',      'name' => 'live_url',      'type' => 'url' ],
		[ 'key' => 'field_proj_featured','label' => 'Featured?',     'name' => 'is_featured',   'type' => 'true_false' ],
	],
	'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'fhp_project' ] ] ],
] );

/* --------------------------------------------------------------------------
 * Skill Fields
 * -------------------------------------------------------------------------- */
acf_add_local_field_group( [
	'key'      => 'group_skill',
	'title'    => 'Skill Details',
	'fields'   => [
		[ 'key' => 'field_skill_cat',    'label' => 'Category',      'name' => 'skill_category', 'type' => 'select',
		  'choices' => [ 'Languages' => 'Languages', 'Frameworks' => 'Frameworks', 'DevOps' => 'DevOps', 'Databases' => 'Databases', 'Tools' => 'Tools', 'Soft Skills' => 'Soft Skills' ] ],
		[ 'key' => 'field_skill_level',  'label' => 'Proficiency %', 'name' => 'skill_level',    'type' => 'number', 'min' => 0, 'max' => 100 ],
	],
	'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'fhp_skill' ] ] ],
] );

/* --------------------------------------------------------------------------
 * Options Page (Site-wide settings)
 * -------------------------------------------------------------------------- */
if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page( [
		'page_title' => 'Portfolio Settings',
		'menu_title' => 'Portfolio Settings',
		'menu_slug'  => 'portfolio-settings',
		'capability' => 'edit_posts',
	] );

	acf_add_local_field_group( [
		'key'      => 'group_options',
		'title'    => 'Portfolio Settings',
		'fields'   => [
			[ 'key' => 'field_full_name',     'label' => 'Full Name',          'name' => 'full_name',          'type' => 'text' ],
			[ 'key' => 'field_job_title',     'label' => 'Job Title',          'name' => 'job_title',          'type' => 'text' ],
			[ 'key' => 'field_hero_summary',  'label' => 'Hero Summary',       'name' => 'hero_summary',       'type' => 'textarea' ],
			[ 'key' => 'field_email',         'label' => 'Email',              'name' => 'contact_email',      'type' => 'email' ],
			[ 'key' => 'field_phone',         'label' => 'Phone',              'name' => 'contact_phone',      'type' => 'text' ],
			[ 'key' => 'field_linkedin',      'label' => 'LinkedIn URL',       'name' => 'linkedin_url',       'type' => 'url' ],
			[ 'key' => 'field_github',        'label' => 'GitHub URL',         'name' => 'github_url',         'type' => 'url' ],
			[ 'key' => 'field_cv_file',       'label' => 'CV File (PDF)',      'name' => 'cv_file',            'type' => 'file', 'return_format' => 'url', 'mime_types' => 'pdf' ],
		],
		'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'portfolio-settings' ] ] ],
	] );
}
