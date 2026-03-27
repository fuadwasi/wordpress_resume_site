<?php
/**
 * Custom Post Type & Taxonomy Registration
 *
 * Registers the four CPTs used by the portfolio:
 *   - experience  (Work Experience timeline)
 *   - project     (Portfolio projects grid)
 *   - skill       (Categorised skill list)
 *   - achievement (Certifications & awards)
 *
 * Also registers the shared `tech_stack` taxonomy used by both
 * experience and project CPTs.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------
   Bootstrap hook
------------------------------------------------------------------ */
add_action( 'init', 'fhp_register_post_types', 0 );
add_action( 'init', 'fhp_register_taxonomies', 0 );

/* ------------------------------------------------------------------
   Register Custom Post Types
------------------------------------------------------------------ */

/**
 * Register all theme CPTs.
 */
function fhp_register_post_types() {
    fhp_register_cpt_experience();
    fhp_register_cpt_project();
    fhp_register_cpt_skill();
    fhp_register_cpt_achievement();
}

/* --- 1. EXPERIENCE ------------------------------------------------ */

function fhp_register_cpt_experience() {
    $labels = [
        'name'                  => __( 'Work Experience',          'fuadhasan-portfolio' ),
        'singular_name'         => __( 'Experience Entry',         'fuadhasan-portfolio' ),
        'add_new'               => __( 'Add Experience',           'fuadhasan-portfolio' ),
        'add_new_item'          => __( 'Add New Experience Entry', 'fuadhasan-portfolio' ),
        'edit_item'             => __( 'Edit Experience Entry',    'fuadhasan-portfolio' ),
        'new_item'              => __( 'New Experience Entry',     'fuadhasan-portfolio' ),
        'view_item'             => __( 'View Experience',          'fuadhasan-portfolio' ),
        'view_items'            => __( 'View All Experience',      'fuadhasan-portfolio' ),
        'search_items'          => __( 'Search Experience',        'fuadhasan-portfolio' ),
        'not_found'             => __( 'No experience entries found.',  'fuadhasan-portfolio' ),
        'not_found_in_trash'    => __( 'No experience entries in trash.', 'fuadhasan-portfolio' ),
        'all_items'             => __( 'All Experience',           'fuadhasan-portfolio' ),
        'menu_name'             => __( 'Experience',               'fuadhasan-portfolio' ),
        'name_admin_bar'        => __( 'Experience Entry',         'fuadhasan-portfolio' ),
    ];

    $args = [
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,   // Gutenberg / REST API support
        'query_var'           => true,
        'rewrite'             => [ 'slug' => 'experience', 'with_front' => false ],
        'capability_type'     => 'post',
        'has_archive'         => 'experience',
        'hierarchical'        => false,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-businessman',
        'supports'            => [ 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes' ],
    ];

    register_post_type( 'experience', $args );
}

/* --- 2. PROJECT --------------------------------------------------- */

function fhp_register_cpt_project() {
    $labels = [
        'name'                  => __( 'Projects',            'fuadhasan-portfolio' ),
        'singular_name'         => __( 'Project',             'fuadhasan-portfolio' ),
        'add_new'               => __( 'Add Project',         'fuadhasan-portfolio' ),
        'add_new_item'          => __( 'Add New Project',     'fuadhasan-portfolio' ),
        'edit_item'             => __( 'Edit Project',        'fuadhasan-portfolio' ),
        'new_item'              => __( 'New Project',         'fuadhasan-portfolio' ),
        'view_item'             => __( 'View Project',        'fuadhasan-portfolio' ),
        'view_items'            => __( 'View All Projects',   'fuadhasan-portfolio' ),
        'search_items'          => __( 'Search Projects',     'fuadhasan-portfolio' ),
        'not_found'             => __( 'No projects found.',  'fuadhasan-portfolio' ),
        'not_found_in_trash'    => __( 'No projects in trash.', 'fuadhasan-portfolio' ),
        'all_items'             => __( 'All Projects',        'fuadhasan-portfolio' ),
        'menu_name'             => __( 'Projects',            'fuadhasan-portfolio' ),
        'name_admin_bar'        => __( 'Project',             'fuadhasan-portfolio' ),
    ];

    $args = [
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => [ 'slug' => 'projects', 'with_front' => false ],
        'capability_type'     => 'post',
        'has_archive'         => 'projects',
        'hierarchical'        => false,
        'menu_position'       => 6,
        'menu_icon'           => 'dashicons-portfolio',
        'supports'            => [ 'title', 'editor', 'thumbnail', 'revisions', 'page-attributes' ],
    ];

    register_post_type( 'project', $args );
}

/* --- 3. SKILL ----------------------------------------------------- */

function fhp_register_cpt_skill() {
    $labels = [
        'name'                  => __( 'Skills',          'fuadhasan-portfolio' ),
        'singular_name'         => __( 'Skill',           'fuadhasan-portfolio' ),
        'add_new'               => __( 'Add Skill',       'fuadhasan-portfolio' ),
        'add_new_item'          => __( 'Add New Skill',   'fuadhasan-portfolio' ),
        'edit_item'             => __( 'Edit Skill',      'fuadhasan-portfolio' ),
        'new_item'              => __( 'New Skill',       'fuadhasan-portfolio' ),
        'view_item'             => __( 'View Skill',      'fuadhasan-portfolio' ),
        'search_items'          => __( 'Search Skills',   'fuadhasan-portfolio' ),
        'not_found'             => __( 'No skills found.', 'fuadhasan-portfolio' ),
        'not_found_in_trash'    => __( 'No skills in trash.', 'fuadhasan-portfolio' ),
        'all_items'             => __( 'All Skills',      'fuadhasan-portfolio' ),
        'menu_name'             => __( 'Skills',          'fuadhasan-portfolio' ),
        'name_admin_bar'        => __( 'Skill',           'fuadhasan-portfolio' ),
    ];

    $args = [
        'labels'              => $labels,
        'public'              => false,       // Skills are not individually navigable
        'publicly_queryable'  => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'query_var'           => false,
        'rewrite'             => false,
        'capability_type'     => 'post',
        'has_archive'         => false,
        'hierarchical'        => false,
        'menu_position'       => 7,
        'menu_icon'           => 'dashicons-admin-tools',
        'supports'            => [ 'title', 'page-attributes' ],
    ];

    register_post_type( 'skill', $args );
}

/* --- 4. ACHIEVEMENT ---------------------------------------------- */

function fhp_register_cpt_achievement() {
    $labels = [
        'name'                  => __( 'Achievements',          'fuadhasan-portfolio' ),
        'singular_name'         => __( 'Achievement',           'fuadhasan-portfolio' ),
        'add_new'               => __( 'Add Achievement',       'fuadhasan-portfolio' ),
        'add_new_item'          => __( 'Add New Achievement',   'fuadhasan-portfolio' ),
        'edit_item'             => __( 'Edit Achievement',      'fuadhasan-portfolio' ),
        'new_item'              => __( 'New Achievement',       'fuadhasan-portfolio' ),
        'view_item'             => __( 'View Achievement',      'fuadhasan-portfolio' ),
        'search_items'          => __( 'Search Achievements',   'fuadhasan-portfolio' ),
        'not_found'             => __( 'No achievements found.', 'fuadhasan-portfolio' ),
        'not_found_in_trash'    => __( 'No achievements in trash.', 'fuadhasan-portfolio' ),
        'all_items'             => __( 'All Achievements',      'fuadhasan-portfolio' ),
        'menu_name'             => __( 'Achievements',          'fuadhasan-portfolio' ),
        'name_admin_bar'        => __( 'Achievement',           'fuadhasan-portfolio' ),
    ];

    $args = [
        'labels'              => $labels,
        'public'              => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => true,
        'query_var'           => true,
        'rewrite'             => [ 'slug' => 'achievements', 'with_front' => false ],
        'capability_type'     => 'post',
        'has_archive'         => 'achievements',
        'hierarchical'        => false,
        'menu_position'       => 8,
        'menu_icon'           => 'dashicons-awards',
        'supports'            => [ 'title', 'thumbnail', 'page-attributes' ],
    ];

    register_post_type( 'achievement', $args );
}

/* ------------------------------------------------------------------
   Register Taxonomies
------------------------------------------------------------------ */

/**
 * Register all theme taxonomies.
 */
function fhp_register_taxonomies() {
    fhp_register_taxonomy_tech_stack();
    fhp_register_taxonomy_skill_category();
    fhp_register_taxonomy_achievement_type();
}

/* --- Tech Stack (shared by experience + project) ------------------ */

function fhp_register_taxonomy_tech_stack() {
    $labels = [
        'name'              => __( 'Tech Stack',        'fuadhasan-portfolio' ),
        'singular_name'     => __( 'Technology',        'fuadhasan-portfolio' ),
        'search_items'      => __( 'Search Tech Stack', 'fuadhasan-portfolio' ),
        'all_items'         => __( 'All Technologies',  'fuadhasan-portfolio' ),
        'edit_item'         => __( 'Edit Technology',   'fuadhasan-portfolio' ),
        'update_item'       => __( 'Update Technology', 'fuadhasan-portfolio' ),
        'add_new_item'      => __( 'Add Technology',    'fuadhasan-portfolio' ),
        'new_item_name'     => __( 'New Technology',    'fuadhasan-portfolio' ),
        'menu_name'         => __( 'Tech Stack',        'fuadhasan-portfolio' ),
    ];

    register_taxonomy( 'tech_stack', [ 'experience', 'project' ], [
        'labels'            => $labels,
        'hierarchical'      => false,
        'public'            => true,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => [ 'slug' => 'tech', 'with_front' => false ],
    ] );
}

/* --- Skill Category (for skill CPT) ------------------------------ */

function fhp_register_taxonomy_skill_category() {
    $labels = [
        'name'              => __( 'Skill Categories',   'fuadhasan-portfolio' ),
        'singular_name'     => __( 'Skill Category',     'fuadhasan-portfolio' ),
        'search_items'      => __( 'Search Categories',  'fuadhasan-portfolio' ),
        'all_items'         => __( 'All Categories',     'fuadhasan-portfolio' ),
        'edit_item'         => __( 'Edit Category',      'fuadhasan-portfolio' ),
        'update_item'       => __( 'Update Category',    'fuadhasan-portfolio' ),
        'add_new_item'      => __( 'Add Category',       'fuadhasan-portfolio' ),
        'new_item_name'     => __( 'New Category',       'fuadhasan-portfolio' ),
        'menu_name'         => __( 'Categories',         'fuadhasan-portfolio' ),
    ];

    // Predefined categories from the plan
    register_taxonomy( 'skill_category', [ 'skill' ], [
        'labels'            => $labels,
        'hierarchical'      => true,   // Hierarchical so categories can have order
        'public'            => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => false,
    ] );
}

/* --- Achievement Type --------------------------------------------- */

function fhp_register_taxonomy_achievement_type() {
    $labels = [
        'name'          => __( 'Achievement Types', 'fuadhasan-portfolio' ),
        'singular_name' => __( 'Achievement Type',  'fuadhasan-portfolio' ),
        'all_items'     => __( 'All Types',         'fuadhasan-portfolio' ),
        'edit_item'     => __( 'Edit Type',         'fuadhasan-portfolio' ),
        'update_item'   => __( 'Update Type',       'fuadhasan-portfolio' ),
        'add_new_item'  => __( 'Add Type',          'fuadhasan-portfolio' ),
        'new_item_name' => __( 'New Type',          'fuadhasan-portfolio' ),
        'menu_name'     => __( 'Types',             'fuadhasan-portfolio' ),
    ];

    register_taxonomy( 'achievement_type', [ 'achievement' ], [
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => false,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'rewrite'           => false,
    ] );
}

/* ------------------------------------------------------------------
   Seed default taxonomy terms on theme activation (runs once)
------------------------------------------------------------------ */
add_action( 'after_switch_theme', 'fhp_seed_taxonomy_terms' );

function fhp_seed_taxonomy_terms() {
    // Skill categories
    $skill_categories = [
        'Backend',
        'Frontend',
        'Database',
        'DevOps & Cloud',
        'Integrations',
        'Soft Skills',
        'Competitive Programming',
    ];

    foreach ( $skill_categories as $cat ) {
        if ( ! term_exists( $cat, 'skill_category' ) ) {
            wp_insert_term( $cat, 'skill_category' );
        }
    }

    // Achievement types
    $achievement_types = [ 'Certification', 'Award', 'Contest', 'Volunteer' ];
    foreach ( $achievement_types as $type ) {
        if ( ! term_exists( $type, 'achievement_type' ) ) {
            wp_insert_term( $type, 'achievement_type' );
        }
    }

    // Common tech stack terms
    $tech_terms = [
        'ASP.NET Core', 'C#', 'NopCommerce', 'gRPC', 'RabbitMQ', 'MongoDB',
        'Angular', 'Next.js', 'JavaScript', 'jQuery', 'Firebase',
        'MSSQL', 'MySQL', 'Azure DevOps', 'REST APIs', 'Microservices',
        'SAP ERP', 'ZohoCRM', 'TaxJar', 'PHP', 'WordPress',
    ];

    foreach ( $tech_terms as $tech ) {
        if ( ! term_exists( $tech, 'tech_stack' ) ) {
            wp_insert_term( $tech, 'tech_stack' );
        }
    }
}
