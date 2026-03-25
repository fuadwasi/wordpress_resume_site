<?php
/**
 * ACF Field Group Definitions
 *
 * All field groups are registered programmatically via acf_add_local_field_group()
 * so they are version-controlled alongside the theme and do not depend on the
 * ACF database records.
 *
 * Field Groups:
 *   - fhp_experience_details  → Work Experience CPT
 *   - fhp_project_details     → Project CPT
 *   - fhp_skill_details       → Skill CPT
 *   - fhp_achievement_details → Achievement CPT
 *   - fhp_site_settings       → Options Page (hero, contact, CV, stats)
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/init', 'fhp_register_acf_fields' );

function fhp_register_acf_fields() {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    /* ================================================================
       1. WORK EXPERIENCE
       ================================================================ */
    acf_add_local_field_group( [
        'key'      => 'group_fhp_experience',
        'title'    => 'Experience Details',
        'fields'   => [
            [
                'key'   => 'field_company_name',
                'label' => 'Company Name',
                'name'  => 'company_name',
                'type'  => 'text',
                'required' => 1,
            ],
            [
                'key'   => 'field_company_logo',
                'label' => 'Company Logo',
                'name'  => 'company_logo',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ],
            [
                'key'   => 'field_company_url',
                'label' => 'Company Website',
                'name'  => 'company_url',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_job_title',
                'label' => 'Job Title',
                'name'  => 'job_title',
                'type'  => 'text',
                'required' => 1,
            ],
            [
                'key'           => 'field_employment_type',
                'label'         => 'Employment Type',
                'name'          => 'employment_type',
                'type'          => 'select',
                'choices'       => [
                    'full_time'  => 'Full-time',
                    'part_time'  => 'Part-time',
                    'contract'   => 'Contract',
                    'freelance'  => 'Freelance',
                ],
                'default_value' => 'full_time',
                'return_format' => 'label',
            ],
            [
                'key'           => 'field_start_date',
                'label'         => 'Start Date',
                'name'          => 'start_date',
                'type'          => 'date_picker',
                'display_format' => 'F, Y',
                'return_format'  => 'Y-m-d',
                'required' => 1,
            ],
            [
                'key'           => 'field_end_date',
                'label'         => 'End Date',
                'name'          => 'end_date',
                'type'          => 'date_picker',
                'display_format' => 'F, Y',
                'return_format'  => 'Y-m-d',
                'instructions'  => 'Leave blank if this is a current position.',
            ],
            [
                'key'   => 'field_is_current',
                'label' => 'Current Position',
                'name'  => 'is_current',
                'type'  => 'true_false',
                'message' => 'This is my current role',
                'default_value' => 0,
            ],
            [
                'key'   => 'field_location',
                'label' => 'Location',
                'name'  => 'location',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_job_description',
                'label' => 'Role Description',
                'name'  => 'job_description',
                'type'  => 'wysiwyg',
                'tabs'  => 'all',
                'toolbar' => 'basic',
                'media_upload' => 0,
            ],
            [
                'key'    => 'field_key_contributions',
                'label'  => 'Key Contributions',
                'name'   => 'key_contributions',
                'type'   => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Contribution',
                'sub_fields'   => [
                    [
                        'key'   => 'field_contribution_text',
                        'label' => 'Contribution',
                        'name'  => 'contribution_text',
                        'type'  => 'text',
                        'column_width' => '100',
                    ],
                ],
            ],
            [
                'key'    => 'field_project_references',
                'label'  => 'Project References',
                'name'   => 'project_references',
                'type'   => 'repeater',
                'layout' => 'table',
                'button_label' => 'Add Reference',
                'sub_fields'   => [
                    [
                        'key'   => 'field_ref_name',
                        'label' => 'Project Name',
                        'name'  => 'ref_name',
                        'type'  => 'text',
                    ],
                    [
                        'key'   => 'field_ref_url',
                        'label' => 'URL',
                        'name'  => 'ref_url',
                        'type'  => 'url',
                    ],
                ],
            ],
            [
                'key'   => 'field_experience_order',
                'label' => 'Display Order',
                'name'  => 'order',
                'type'  => 'number',
                'instructions' => 'Lower number = displayed first. Default 0.',
                'default_value' => 0,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'experience' ] ],
        ],
        'menu_order'     => 0,
        'position'       => 'normal',
        'style'          => 'default',
        'label_placement' => 'top',
    ] );

    /* ================================================================
       2. PROJECT
       ================================================================ */
    acf_add_local_field_group( [
        'key'    => 'group_fhp_project',
        'title'  => 'Project Details',
        'fields' => [
            [
                'key'   => 'field_short_description',
                'label' => 'Short Description',
                'name'  => 'short_description',
                'type'  => 'textarea',
                'rows'  => 3,
                'instructions' => '1–2 sentence summary shown on the projects grid card.',
            ],
            [
                'key'           => 'field_project_type',
                'label'         => 'Project Type',
                'name'          => 'project_type',
                'type'          => 'select',
                'choices'       => [
                    'b2b_ecommerce' => 'B2B eCommerce',
                    'b2c_ecommerce' => 'B2C eCommerce',
                    'plugin'        => 'Plugin',
                    'pos'           => 'POS System',
                    'cms'           => 'CMS',
                    'open_source'   => 'Open Source',
                    'other'         => 'Other',
                ],
                'return_format' => 'label',
            ],
            [
                'key'   => 'field_client_company',
                'label' => 'Client / Company',
                'name'  => 'client_company',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_live_url',
                'label' => 'Live URL',
                'name'  => 'live_url',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_github_url',
                'label' => 'GitHub URL',
                'name'  => 'github_url',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_is_featured',
                'label' => 'Featured Project',
                'name'  => 'is_featured',
                'type'  => 'true_false',
                'message' => 'Show on the Home page featured section',
                'default_value' => 0,
            ],
            [
                'key'   => 'field_project_order',
                'label' => 'Display Order',
                'name'  => 'order',
                'type'  => 'number',
                'default_value' => 0,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'project' ] ],
        ],
        'menu_order' => 0,
        'position'   => 'normal',
    ] );

    /* ================================================================
       3. SKILL
       ================================================================ */
    acf_add_local_field_group( [
        'key'    => 'group_fhp_skill',
        'title'  => 'Skill Details',
        'fields' => [
            [
                'key'   => 'field_skill_icon',
                'label' => 'Skill Icon',
                'name'  => 'skill_icon',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'instructions'  => 'Optional — upload an SVG or PNG icon for this skill.',
            ],
            [
                'key'           => 'field_proficiency_level',
                'label'         => 'Proficiency Level',
                'name'          => 'proficiency_level',
                'type'          => 'range',
                'min'           => 1,
                'max'           => 5,
                'step'          => 1,
                'default_value' => 3,
                'instructions'  => '1 = Beginner, 5 = Expert',
            ],
            [
                'key'   => 'field_skill_order',
                'label' => 'Display Order',
                'name'  => 'order',
                'type'  => 'number',
                'default_value' => 0,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'skill' ] ],
        ],
        'menu_order' => 0,
        'position'   => 'side',
    ] );

    /* ================================================================
       4. ACHIEVEMENT
       ================================================================ */
    acf_add_local_field_group( [
        'key'    => 'group_fhp_achievement',
        'title'  => 'Achievement Details',
        'fields' => [
            [
                'key'   => 'field_issuing_org',
                'label' => 'Issuing Organisation',
                'name'  => 'issuing_organization',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_issue_date',
                'label' => 'Issue Date',
                'name'  => 'issue_date',
                'type'  => 'date_picker',
                'display_format' => 'F Y',
                'return_format'  => 'Y-m-d',
            ],
            [
                'key'   => 'field_credential_url',
                'label' => 'Credential URL',
                'name'  => 'credential_url',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_achievement_description',
                'label' => 'Description',
                'name'  => 'achievement_description',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            [
                'key'   => 'field_achievement_icon',
                'label' => 'Badge / Icon',
                'name'  => 'achievement_icon',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'achievement' ] ],
        ],
        'menu_order' => 0,
        'position'   => 'normal',
    ] );

    /* ================================================================
       5. OPTIONS PAGE — Site Settings
       ================================================================ */

    // ---- 5a. Hero & About ----------------------------------------
    acf_add_local_field_group( [
        'key'    => 'group_fhp_hero',
        'title'  => 'Hero & About Settings',
        'fields' => [
            [
                'key'           => 'field_hero_name',
                'label'         => 'Full Name',
                'name'          => 'hero_name',
                'type'          => 'text',
                'default_value' => 'Fuad Hasan',
            ],
            [
                'key'           => 'field_hero_title',
                'label'         => 'Professional Title',
                'name'          => 'hero_title',
                'type'          => 'text',
                'default_value' => 'Senior Software Engineer II',
            ],
            [
                'key'           => 'field_hero_subtitle',
                'label'         => 'Tagline / Subtitle',
                'name'          => 'hero_subtitle',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => 'Certified NopCommerce Developer · ASP.NET Core · Microservices · B2B & B2C eCommerce',
            ],
            [
                'key'   => 'field_profile_photo',
                'label' => 'Profile Photo',
                'name'  => 'profile_photo',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'   => 'field_hero_background',
                'label' => 'Hero Background Image',
                'name'  => 'hero_background_image',
                'type'  => 'image',
                'return_format' => 'array',
                'instructions'  => 'Optional — dark overlay is applied automatically.',
            ],
            [
                'key'           => 'field_about_summary',
                'label'         => 'About / Full Summary',
                'name'          => 'about_summary',
                'type'          => 'wysiwyg',
                'tabs'          => 'all',
                'toolbar'       => 'full',
                'media_upload'  => 0,
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fhp-hero-about' ] ],
        ],
        'menu_order' => 0,
    ] );

    // ---- 5b. Contact & Social ------------------------------------
    acf_add_local_field_group( [
        'key'    => 'group_fhp_contact',
        'title'  => 'Contact & Social Links',
        'fields' => [
            [
                'key'           => 'field_email_address',
                'label'         => 'Email Address',
                'name'          => 'email_address',
                'type'          => 'email',
                'default_value' => 'fhassanwasi@gmail.com',
            ],
            [
                'key'           => 'field_phone_number',
                'label'         => 'Phone Number',
                'name'          => 'phone_number',
                'type'          => 'text',
                'default_value' => '+880 01792 478 378',
            ],
            [
                'key'           => 'field_location_text',
                'label'         => 'Location',
                'name'          => 'location',
                'type'          => 'text',
                'default_value' => 'Dhaka, Bangladesh',
            ],
            [
                'key'           => 'field_github_profile',
                'label'         => 'GitHub Profile URL',
                'name'          => 'github_url',
                'type'          => 'url',
                'default_value' => 'https://github.com/fuadwasi',
            ],
            [
                'key'           => 'field_linkedin_url',
                'label'         => 'LinkedIn Profile URL',
                'name'          => 'linkedin_url',
                'type'          => 'url',
                'default_value' => 'https://www.linkedin.com/in/fuadwasi/',
            ],
            [
                'key'           => 'field_codeforces_url',
                'label'         => 'Codeforces Profile URL',
                'name'          => 'codeforces_url',
                'type'          => 'url',
                'default_value' => 'https://codeforces.com/profile/fhwasi',
            ],
            [
                'key'   => 'field_footer_tagline',
                'label' => 'Footer Tagline',
                'name'  => 'footer_tagline',
                'type'  => 'text',
                'default_value' => 'Building scalable eCommerce solutions, one commit at a time.',
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fhp-contact-social' ] ],
        ],
        'menu_order' => 0,
    ] );

    // ---- 5c. CV / Resume -----------------------------------------
    acf_add_local_field_group( [
        'key'    => 'group_fhp_cv',
        'title'  => 'CV / Resume Settings',
        'fields' => [
            [
                'key'          => 'field_active_cv_file',
                'label'        => 'Active CV File (PDF)',
                'name'         => 'active_cv_file',
                'type'         => 'file',
                'return_format' => 'array',
                'library'      => 'all',
                'mime_types'   => 'pdf',
                'instructions' => 'Upload the latest CV/Resume PDF. The download link site-wide updates automatically.',
            ],
            [
                'key'           => 'field_cv_download_label',
                'label'         => 'Download Button Label',
                'name'          => 'cv_download_label',
                'type'          => 'text',
                'default_value' => 'Download CV',
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fhp-cv-settings' ] ],
        ],
        'menu_order' => 0,
    ] );

    // ---- 5d. Stats & Counters ------------------------------------
    acf_add_local_field_group( [
        'key'    => 'group_fhp_stats',
        'title'  => 'Stats & Counter Settings',
        'fields' => [
            [
                'key'           => 'field_years_experience',
                'label'         => 'Years of Experience',
                'name'          => 'years_of_experience',
                'type'          => 'number',
                'default_value' => 5,
            ],
            [
                'key'           => 'field_total_projects',
                'label'         => 'Total Projects Delivered',
                'name'          => 'total_projects',
                'type'          => 'number',
                'default_value' => 20,
            ],
            [
                'key'           => 'field_open_source_contributions',
                'label'         => 'Open Source Contributions',
                'name'          => 'open_source_contributions',
                'type'          => 'number',
                'default_value' => 50,
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fhp-stats' ] ],
        ],
        'menu_order' => 0,
    ] );
}
