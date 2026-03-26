<?php
/**
 * Content Seeder — Pre-populated CPT Data & Site Options
 *
 * Creates all pre-defined content entries for the four Custom Post Types
 * and seeds the ACF Options Page values when the theme is first activated.
 * The seeder is **idempotent**:
 *   - A single option flag (`fhp_seeded`) prevents re-running.
 *   - Each CPT insert is also guarded by a title+post-type existence check.
 *
 * Data seeded (mirrors the plan, Sections 4 & 9):
 *   9.0  Site Options — personal info, professional summary, stats
 *   4.1  7 Work Experience entries — Brain Station 23 PLC & BSSIT
 *   4.2  7 Project entries
 *   4.3  ~40 Skill entries across 7 skill_category terms
 *   4.4  6 Achievement entries
 *
 * Taxonomy terms (skill_category, achievement_type, tech_stack) are
 * seeded by fhp_seed_taxonomy_terms() in custom-post-types.php before
 * this function runs.
 *
 * Hook: after_switch_theme  (priority 20 — runs after CPT + taxonomy
 *       registration at priority 0 and taxonomy seeding at default 10)
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_switch_theme', 'fhp_seed_content', 20 );

/**
 * Entry point: seed all CPT content once.
 */
function fhp_seed_content(): void {
    if ( get_option( 'fhp_seeded' ) ) {
        return; // Already seeded — nothing to do.
    }

    fhp_seed_site_options();
    fhp_seed_experience();
    fhp_seed_projects();
    fhp_seed_skills();
    fhp_seed_achievements();

    update_option( 'fhp_seeded', true );
}

/* ====================================================================
   HELPERS
==================================================================== */

/**
 * Insert a post only if no published/draft post with the same title
 * and post type already exists.  Returns the post ID on success or 0.
 *
 * @param string $post_type
 * @param string $title
 * @param array  $extra_args  Any extra wp_insert_post() args (status, etc.)
 * @return int
 */
function fhp_maybe_insert_post( string $post_type, string $title, array $extra_args = [] ): int {
    $existing = get_posts( [
        'post_type'   => $post_type,
        'title'       => $title,
        'post_status' => [ 'publish', 'draft' ],
        'numberposts' => 1,
        'fields'      => 'ids',
    ] );

    if ( ! empty( $existing ) ) {
        return (int) $existing[0];
    }

    $defaults = [
        'post_title'  => $title,
        'post_type'   => $post_type,
        'post_status' => 'publish',
    ];

    return (int) wp_insert_post( array_merge( $defaults, $extra_args ) );
}

/**
 * Update ACF field if ACF is active, otherwise fall back to update_post_meta().
 *
 * @param string $field_name
 * @param mixed  $value
 * @param int    $post_id
 */
function fhp_set_field( string $field_name, $value, int $post_id ): void {
    if ( function_exists( 'update_field' ) ) {
        update_field( $field_name, $value, $post_id );
    } else {
        update_post_meta( $post_id, $field_name, $value );
    }
}

/**
 * Write a value to an ACF Options Page field.
 *
 * When ACF Pro is active, uses update_field() with 'option' as the
 * object ID, which stores the value as `options_{field_name}` in
 * wp_options and writes the field-key reference needed for ACF to
 * recognise the value.
 *
 * When ACF is absent (e.g. plain WordPress), falls back to
 * update_option( "options_{field_name}", $value ) which matches the
 * exact key ACF would use, allowing seamless ACF activation later.
 *
 * @param string $field_name  The ACF field name (slug), e.g. 'hero_name'.
 * @param mixed  $value       The value to store.
 */
function fhp_set_option_field( string $field_name, $value ): void {
    if ( function_exists( 'update_field' ) ) {
        update_field( $field_name, $value, 'option' );
    } else {
        update_option( 'options_' . $field_name, $value );
    }
}

/**
 * Assign tech_stack taxonomy terms to a post.
 *
 * @param int      $post_id
 * @param string[] $tech_names
 */
function fhp_assign_tech_stack( int $post_id, array $tech_names ): void {
    $term_ids = [];
    foreach ( $tech_names as $name ) {
        $term = get_term_by( 'name', $name, 'tech_stack' );
        if ( ! $term ) {
            $result = wp_insert_term( $name, 'tech_stack' );
            if ( ! is_wp_error( $result ) ) {
                $term_ids[] = $result['term_id'];
            }
        } else {
            $term_ids[] = $term->term_id;
        }
    }
    if ( $term_ids ) {
        wp_set_object_terms( $post_id, $term_ids, 'tech_stack', true );
    }
}

/**
 * Assign a single skill_category term to a skill post.
 *
 * @param int    $post_id
 * @param string $category_name
 */
function fhp_assign_skill_category( int $post_id, string $category_name ): void {
    $term = get_term_by( 'name', $category_name, 'skill_category' );
    if ( ! $term ) {
        $result = wp_insert_term( $category_name, 'skill_category' );
        if ( is_wp_error( $result ) ) {
            return;
        }
        $term_id = $result['term_id'];
    } else {
        $term_id = $term->term_id;
    }
    wp_set_object_terms( $post_id, [ $term_id ], 'skill_category' );
}

/**
 * Assign a single achievement_type term to an achievement post.
 *
 * @param int    $post_id
 * @param string $type_name
 */
function fhp_assign_achievement_type( int $post_id, string $type_name ): void {
    $term = get_term_by( 'name', $type_name, 'achievement_type' );
    if ( ! $term ) {
        $result = wp_insert_term( $type_name, 'achievement_type' );
        if ( is_wp_error( $result ) ) {
            return;
        }
        $term_id = $result['term_id'];
    } else {
        $term_id = $term->term_id;
    }
    wp_set_object_terms( $post_id, [ $term_id ], 'achievement_type' );
}

/* ====================================================================
   9.0  SITE OPTIONS — Personal Information, Summary & Stats
==================================================================== */

/**
 * Seed all ACF Options Page values from the Content Migration Plan
 * (Section 9 of wordpress_portfolio_development_plan.md).
 *
 * Groups seeded:
 *   Hero & About     → hero_name, hero_title, hero_subtitle, about_summary
 *   Contact & Social → email_address, phone_number, location, github_url,
 *                      linkedin_url, codeforces_url, footer_tagline
 *   CV / Resume      → cv_download_label
 *   Stats & Counters → years_of_experience, total_projects,
 *                      open_source_contributions
 *
 * Image fields (profile_photo, hero_background_image, active_cv_file) are
 * intentionally skipped — they require a file upload and cannot be seeded
 * from code without a binary asset.
 */
function fhp_seed_site_options(): void {

    // ----------------------------------------------------------------
    // Hero & About
    // ----------------------------------------------------------------

    fhp_set_option_field( 'hero_name',  'Fuad Hasan' );
    fhp_set_option_field( 'hero_title', 'Senior Software Engineer II' );
    fhp_set_option_field( 'hero_subtitle',
        'Certified NopCommerce Developer · ASP.NET Core · Microservices · B2B & B2C eCommerce'
    );

    // Full professional summary with key achievements — stored as HTML
    // so the wysiwyg field renders it correctly.
    fhp_set_option_field( 'about_summary', wp_kses_post(
        '<p>Certified NopCommerce Developer and Senior Software Engineer with 3+ years of experience ' .
        'designing and scaling B2B &amp; B2C eCommerce platforms, ERP integrations, and enterprise-grade ' .
        'plugins. Skilled in ASP.NET Core, C#, and modern microservices architecture, I help businesses ' .
        'improve operations and customer experience through robust, future-ready solutions.</p>' .
        '<h3>Key Achievements</h3>' .
        '<ul>' .
        '<li>Integrated SAP ERP for Macsteel — real-time sync across 10,000+ SKUs.</li>' .
        '<li>Led POS system development now deployed in 50+ retail stores.</li>' .
        '<li>NopCommerce core contributor: Facebook auth, MFA, discount management.</li>' .
        '<li>Built Shawpno microservices platform — 30% checkout latency reduction.</li>' .
        '<li>ICPC Dhaka Regional Contestant (Rank 96 / 300).</li>' .
        '<li>DIU Take-Off Programming Contest Champion (Rank 1 / 300).</li>' .
        '</ul>'
    ) );

    // ----------------------------------------------------------------
    // Contact & Social
    // ----------------------------------------------------------------

    fhp_set_option_field( 'email_address',   'fhassanwasi@gmail.com' );
    fhp_set_option_field( 'phone_number',    '+880 01792 478 378' );
    fhp_set_option_field( 'location',        'Dhaka, Bangladesh' );
    fhp_set_option_field( 'github_url',      'https://github.com/fuadwasi' );
    fhp_set_option_field( 'linkedin_url',    'https://www.linkedin.com/in/fuadwasi/' );
    fhp_set_option_field( 'codeforces_url',  'https://codeforces.com/profile/fhwasi' );
    fhp_set_option_field( 'footer_tagline',  'Building scalable eCommerce solutions, one commit at a time.' );

    // ----------------------------------------------------------------
    // CV / Resume
    // ----------------------------------------------------------------

    fhp_set_option_field( 'cv_download_label', 'Download CV' );

    // ----------------------------------------------------------------
    // Stats & Counters
    // ----------------------------------------------------------------

    fhp_set_option_field( 'years_of_experience',       5 );
    fhp_set_option_field( 'total_projects',            20 );
    fhp_set_option_field( 'open_source_contributions', 50 );
}

/* ====================================================================
   4.1  WORK EXPERIENCE  (7 entries)
==================================================================== */

function fhp_seed_experience(): void {
    $entries = [
        // Most recent first (order = 1)
        [
            'title'           => 'Senior Software Engineer II — Brain Station 23 PLC',
            'company_name'    => 'Brain Station 23 PLC',
            'company_url'     => 'https://brainstation-23.com',
            'job_title'       => 'Senior Software Engineer II',
            'employment_type' => 'full_time',
            'start_date'      => '2026-01-01',
            'end_date'        => '',
            'is_current'      => 1,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 1,
            'job_description' => '<p>Leading development of enterprise-grade eCommerce platforms and ERP integrations. Architecting scalable microservices solutions using ASP.NET Core, gRPC, and RabbitMQ for high-traffic B2B and B2C clients.</p>',
            'contributions'   => [
                'Led end-to-end architecture for Shawpno microservices eCommerce platform, reducing checkout latency by 30%.',
                'Integrated SAP ERP with Macsteel B2B NopCommerce platform, enabling real-time sync across 10,000+ SKUs.',
                'Mentored junior engineers and conducted code reviews for a distributed team of 8 engineers.',
                'Established Azure DevOps CI/CD pipelines cutting deployment time from 2 hours to 15 minutes.',
            ],
            'references' => [
                [ 'ref_name' => 'Shawpno eCommerce', 'ref_url' => 'https://www.shwapno.com/' ],
                [ 'ref_name' => 'Macsteel B2B Shop',  'ref_url' => 'https://shop.macsteel.co.za/' ],
            ],
            'tech' => [
                'ASP.NET Core', 'C#', 'gRPC', 'RabbitMQ', 'NopCommerce',
                'Angular', 'Next.js', 'MongoDB', 'MSSQL', 'Azure DevOps', 'SAP ERP',
            ],
        ],
        [
            'title'           => 'Senior Software Engineer I — Brain Station 23 PLC',
            'company_name'    => 'Brain Station 23 PLC',
            'company_url'     => 'https://brainstation-23.com',
            'job_title'       => 'Senior Software Engineer I',
            'employment_type' => 'full_time',
            'start_date'      => '2024-07-01',
            'end_date'        => '2025-12-31',
            'is_current'      => 0,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 2,
            'job_description' => '<p>Promoted to Senior Software Engineer I after demonstrating ownership of complex NopCommerce plugin development and eCommerce platform enhancements for international clients.</p>',
            'contributions'   => [
                'Built the NopCommerce-based Online POS System now deployed in 50+ retail stores across Bangladesh.',
                'Developed the Delivery Management System (DMS) plugin with multi-branch routing and real-time tracking.',
                'Delivered the NopCommerce Plugins Suite: TaxJar, ZohoCRM, WebApi, Copy&Pay, and City Bank integrations.',
                'Led integration of SMS OTP authentication and barcode scanning into the POS workflow.',
            ],
            'references' => [
                [ 'ref_name' => 'NopCommerce POS',  'ref_url' => 'https://www.nop-station.com/point-of-sale-pos-for-nopcommerce' ],
                [ 'ref_name' => 'DMS Plugin',       'ref_url' => 'https://www.nop-station.com/delivery-management-system-for-nopcommerce' ],
            ],
            'tech' => [
                'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'REST APIs',
                'AJAX', 'JavaScript', 'ZohoCRM', 'TaxJar', 'Azure DevOps',
            ],
        ],
        [
            'title'           => 'Software Engineer II — Brain Station 23 PLC',
            'company_name'    => 'Brain Station 23 PLC',
            'company_url'     => 'https://brainstation-23.com',
            'job_title'       => 'Software Engineer II',
            'employment_type' => 'full_time',
            'start_date'      => '2023-07-01',
            'end_date'        => '2024-06-30',
            'is_current'      => 0,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 3,
            'job_description' => '<p>Took ownership of full-cycle NopCommerce plugin development, contributing to both client-facing products and the NopCommerce open-source core.</p>',
            'contributions'   => [
                'Contributed to NopCommerce core: Facebook social authentication, Multi-Factor Authentication (MFA), and reCAPTCHA for guest checkout.',
                'Implemented Google Analytics enhanced eCommerce tracking and Sendinblue email marketing integration.',
                'Developed payment gateway integrations: SSLCommerz, bKash, Nagad, CyberSource, and HyperPay Copy&Pay.',
                'Delivered the TARBIYAH CMS using ASP.NET MVC with bKash and SSLCommerz payment support.',
            ],
            'references' => [
                [ 'ref_name' => 'NopCommerce Contributions', 'ref_url' => 'https://github.com/nopSolutions/nopCommerce/commits?author=fuadhasan28' ],
                [ 'ref_name' => 'TARBIYAH CMS',              'ref_url' => '#' ],
            ],
            'tech' => [
                'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'REST APIs',
                'Firebase', 'JavaScript', 'jQuery', 'Google Analytics',
            ],
        ],
        [
            'title'           => 'Software Engineer — Brain Station 23 PLC',
            'company_name'    => 'Brain Station 23 PLC',
            'company_url'     => 'https://brainstation-23.com',
            'job_title'       => 'Software Engineer',
            'employment_type' => 'full_time',
            'start_date'      => '2022-07-01', // Jul 2022 (start of role — per plan)
            'end_date'        => '2023-08-31', // Aug 2023 (end of role — per plan; end > start, range is valid)
            'is_current'      => 0,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 4,
            'job_description' => '<p>Designed and delivered complex NopCommerce plugins, REST API integrations, and third-party service connectors for enterprise clients in South Africa and the Middle East.</p>',
            'contributions'   => [
                'Built the Macsteel B2B eCommerce platform on NopCommerce with a fully custom B2B pricing engine.',
                'Implemented real-time SAP ERP inventory sync using .NET Background Services and REST webhooks.',
                'Developed WebApi plugin enabling NopCommerce headless operations for mobile and SPA frontends.',
                'Integrated ZohoCRM for customer synchronisation across the NopCommerce storefront.',
            ],
            'references' => [
                [ 'ref_name' => 'Macsteel B2B Shop', 'ref_url' => 'https://shop.macsteel.co.za/' ],
            ],
            'tech' => [
                'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'SAP ERP',
                'REST APIs', 'Entity Framework', 'ZohoCRM', 'Azure DevOps',
            ],
        ],
        [
            'title'           => 'Associate Software Engineer — Brain Station 23 PLC',
            'company_name'    => 'Brain Station 23 PLC',
            'company_url'     => 'https://brainstation-23.com',
            'job_title'       => 'Associate Software Engineer',
            'employment_type' => 'full_time',
            'start_date'      => '2021-07-01',
            'end_date'        => '2022-06-30',
            'is_current'      => 0,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 5,
            'job_description' => '<p>Promoted from trainee to Associate after excelling during the training program. Began contributing to production NopCommerce features, unit tests, and code quality improvements.</p>',
            'contributions'   => [
                'Contributed to the development and testing of NopCommerce plugin modules.',
                'Wrote unit and integration tests using NUnit, improving code coverage for critical payment flows.',
                'Participated in sprint planning, daily standups, and retrospectives using Jira and Azure DevOps.',
            ],
            'references' => [],
            'tech' => [
                'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'REST APIs',
                'Entity Framework', 'Azure DevOps',
            ],
        ],
        [
            'title'           => 'Software Engineering Trainee — Brain Station 23 PLC',
            'company_name'    => 'Brain Station 23 PLC',
            'company_url'     => 'https://brainstation-23.com',
            'job_title'       => 'Software Engineering Trainee',
            'employment_type' => 'full_time',
            'start_date'      => '2021-03-01',
            'end_date'        => '2021-06-30',
            'is_current'      => 0,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 6,
            'job_description' => '<p>Completed the Brain Station 23 Software Engineering training program, covering .NET, NopCommerce architecture, SQL, REST APIs, and Agile practices.</p>',
            'contributions'   => [
                'Trained in ASP.NET Core, C#, NopCommerce architecture, Entity Framework, and REST API design.',
                'Built a sample NopCommerce plugin as part of the training assessment.',
                'Gained exposure to Azure DevOps, Git branching strategy, and Scrum methodology.',
            ],
            'references' => [],
            'tech' => [
                'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'Entity Framework',
            ],
        ],
        [
            'title'           => 'Junior Software Engineer — BSSIT',
            'company_name'    => 'BSSIT',
            'company_url'     => '',
            'job_title'       => 'Junior Software Engineer',
            'employment_type' => 'full_time',
            'start_date'      => '2021-01-01',
            'end_date'        => '2021-02-28',
            'is_current'      => 0,
            'location'        => 'Dhaka, Bangladesh',
            'order'           => 7,
            'job_description' => '<p>Short-term role focused on web application development using PHP and MySQL before transitioning to Brain Station 23.</p>',
            'contributions'   => [
                'Developed web features using PHP, MySQL, and jQuery.',
                'Participated in requirements gathering and UI design sessions.',
            ],
            'references' => [],
            'tech' => [ 'PHP', 'MySQL', 'jQuery', 'JavaScript', 'HTML5', 'CSS3' ],
        ],
    ];

    foreach ( $entries as $data ) {
        $post_id = fhp_maybe_insert_post( 'experience', $data['title'] );
        if ( ! $post_id ) {
            continue;
        }

        fhp_set_field( 'company_name',    $data['company_name'],    $post_id );
        fhp_set_field( 'company_url',     $data['company_url'],     $post_id );
        fhp_set_field( 'job_title',       $data['job_title'],       $post_id );
        fhp_set_field( 'employment_type', $data['employment_type'], $post_id );
        fhp_set_field( 'start_date',      $data['start_date'],      $post_id );
        fhp_set_field( 'end_date',        $data['end_date'],        $post_id );
        fhp_set_field( 'is_current',      $data['is_current'],      $post_id );
        fhp_set_field( 'location',        $data['location'],        $post_id );
        fhp_set_field( 'job_description', $data['job_description'], $post_id );
        fhp_set_field( 'order',           $data['order'],           $post_id );

        if ( ! empty( $data['contributions'] ) ) {
            $rows = array_map(
                fn( $c ) => [ 'contribution_text' => $c ],
                $data['contributions']
            );
            fhp_set_field( 'key_contributions', $rows, $post_id );
        }

        if ( ! empty( $data['references'] ) ) {
            fhp_set_field( 'project_references', $data['references'], $post_id );
        }

        fhp_assign_tech_stack( $post_id, $data['tech'] );
    }
}

/* ====================================================================
   4.2  PROJECTS  (7 entries)
==================================================================== */

function fhp_seed_projects(): void {
    $projects = [
        [
            'title'             => 'Shawpno eCommerce Platform',
            'short_description' => 'Microservices-based B2C eCommerce platform for Bangladesh\'s largest supermarket chain, handling thousands of SKUs and real-time inventory across branches.',
            'content'           => '<p>Designed and led the development of a modern microservices eCommerce platform for Shawpno, the leading supermarket chain in Bangladesh. The platform replaced a legacy monolith, cutting checkout latency by 30% and enabling independent scaling of checkout, inventory, and notification services.</p><h3>Technical Highlights</h3><ul><li>Service-to-service communication via gRPC for low-latency order processing.</li><li>Asynchronous event streaming via RabbitMQ for inventory updates and notifications.</li><li>Multi-tenant MongoDB for product catalogue with MSSQL for transactional orders.</li><li>Angular customer storefront and Next.js landing pages for SEO.</li><li>Firebase push notifications for order status updates.</li><li>Azure DevOps pipelines for automated build, test, and deployment.</li></ul>',
            'project_type'      => 'b2c_ecommerce',
            'client_company'    => 'Shawpno / Brain Station 23 PLC',
            'live_url'          => 'https://www.shwapno.com/',
            'github_url'        => '',
            'is_featured'       => 1,
            'order'             => 1,
            'tech'              => [ 'ASP.NET Core', 'C#', 'gRPC', 'RabbitMQ', 'MongoDB', 'MSSQL', 'Angular', 'Next.js', 'Firebase', 'Azure DevOps', 'REST APIs', 'Microservices' ],
        ],
        [
            'title'             => 'Macsteel B2B eCommerce Platform',
            'short_description' => 'NopCommerce B2B platform for South Africa\'s leading steel distributor, featuring a custom B2B pricing engine and live SAP ERP inventory sync across 10,000+ SKUs.',
            'content'           => '<p>Delivered a full B2B eCommerce platform for Macsteel, the largest steel distributor in South Africa, built on NopCommerce. The project included a bespoke B2B pricing engine, real-time SAP ERP integration, and a trade account portal for enterprise buyers.</p><h3>Technical Highlights</h3><ul><li>Real-time SAP ERP bidirectional sync via .NET Background Services and REST webhooks.</li><li>Custom B2B pricing tiers, volume discounts, and credit account support.</li><li>Bulk ordering and CSV import for enterprise procurement workflows.</li><li>Multi-warehouse stock visibility and branch-based availability checks.</li><li>Secure customer-specific product catalogues and pricing portals.</li></ul>',
            'project_type'      => 'b2b_ecommerce',
            'client_company'    => 'Macsteel / Brain Station 23 PLC',
            'live_url'          => 'https://shop.macsteel.co.za/',
            'github_url'        => '',
            'is_featured'       => 1,
            'order'             => 2,
            'tech'              => [ 'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'SAP ERP', 'REST APIs', 'Entity Framework', 'Azure DevOps' ],
        ],
        [
            'title'             => 'Online POS System',
            'short_description' => 'NopCommerce-based Point of Sale system deployed across 50+ retail stores in Bangladesh, featuring barcode scanning, SMS OTP authentication, and kitchen management.',
            'content'           => '<p>Designed and led development of a NopCommerce-powered POS system tailored for retail chains in Bangladesh. The system seamlessly integrates with the existing NopCommerce catalogue, enabling in-store sales, kitchen order management, and end-of-day reporting.</p><h3>Technical Highlights</h3><ul><li>Barcode scanning integration for fast checkout.</li><li>SMS OTP authentication for secure customer registration at point of sale.</li><li>Real-time kitchen order management display for F&amp;B outlets.</li><li>Offline-first mode with sync queue for connectivity resilience.</li><li>Deployed in 50+ retail and restaurant outlets across Bangladesh.</li></ul>',
            'project_type'      => 'pos',
            'client_company'    => 'NopStation / Brain Station 23 PLC',
            'live_url'          => 'https://www.nop-station.com/point-of-sale-pos-for-nopcommerce',
            'github_url'        => '',
            'is_featured'       => 1,
            'order'             => 3,
            'tech'              => [ 'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'REST APIs', 'AJAX', 'JavaScript', 'jQuery' ],
        ],
        [
            'title'             => 'NopCommerce Core Contributions',
            'short_description' => 'Open-source contributions to the NopCommerce framework: Facebook social auth, MFA, reCAPTCHA for guest checkout, Google Analytics, and Sendinblue email marketing.',
            'content'           => '<p>Active contributor to the NopCommerce open-source project on GitHub, contributing production-grade features that are now part of the NopCommerce plugin marketplace and core framework.</p><h3>Contributions</h3><ul><li><strong>Facebook Social Authentication</strong> — OAuth 2.0 login integration.</li><li><strong>Multi-Factor Authentication (MFA)</strong> — TOTP-based 2FA for admin and customers.</li><li><strong>reCAPTCHA for Guest Checkout</strong> — Spam and bot prevention.</li><li><strong>Google Analytics Integration</strong> — Enhanced eCommerce tracking events.</li><li><strong>Sendinblue Email Marketing</strong> — Transactional and campaign email integration.</li></ul>',
            'project_type'      => 'open_source',
            'client_company'    => 'NopCommerce Open Source',
            'live_url'          => 'https://www.nopcommerce.com/',
            'github_url'        => 'https://github.com/nopSolutions/nopCommerce/commits?author=fuadhasan28',
            'is_featured'       => 1,
            'order'             => 4,
            'tech'              => [ 'ASP.NET Core', 'C#', 'NopCommerce', 'REST APIs', 'Entity Framework' ],
        ],
        [
            'title'             => 'Delivery Management System (DMS) Plugin',
            'short_description' => 'NopCommerce plugin for multi-branch delivery routing, real-time order tracking, and courier partner integration for eCommerce logistics management.',
            'content'           => '<p>Built a fully featured Delivery Management System plugin for NopCommerce, enabling eCommerce operators to manage multi-branch deliveries, assign couriers, and track orders in real time.</p><h3>Technical Highlights</h3><ul><li>Branch-based delivery zone configuration with geo-fencing support.</li><li>Courier assignment workflow with automatic reassignment on failure.</li><li>Real-time order status tracking pushed via SMS Gateway.</li><li>Admin dashboard for delivery performance analytics and SLA monitoring.</li><li>REST API endpoints for third-party courier system integration.</li></ul>',
            'project_type'      => 'plugin',
            'client_company'    => 'NopStation / Brain Station 23 PLC',
            'live_url'          => 'https://www.nop-station.com/delivery-management-system-for-nopcommerce',
            'github_url'        => '',
            'is_featured'       => 0,
            'order'             => 5,
            'tech'              => [ 'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'REST APIs', 'Entity Framework' ],
        ],
        [
            'title'             => 'NopCommerce Plugins Suite',
            'short_description' => 'A collection of enterprise-grade NopCommerce plugins: WebApi, TaxJar, ZohoCRM, HyperPay Copy&Pay, City Bank, and payment gateway integrations for global clients.',
            'content'           => '<p>Developed a suite of NopCommerce plugins that extend the platform\'s capabilities for enterprise global clients.</p><h3>Plugins Included</h3><ul><li><strong>WebApi Plugin</strong> — Headless eCommerce REST API for mobile and SPA frontends.</li><li><strong>TaxJar Integration</strong> — Automated US sales tax calculation and filing.</li><li><strong>ZohoCRM Sync</strong> — Bidirectional customer and order data synchronisation.</li><li><strong>HyperPay Copy&amp;Pay</strong> — Payment gateway for Middle East markets.</li><li><strong>City Bank Payment</strong> — Local Bangladesh payment gateway integration.</li><li><strong>bKash &amp; Nagad</strong> — Mobile money wallet integrations.</li><li><strong>SSLCommerz &amp; CyberSource</strong> — International card payment gateways.</li></ul>',
            'project_type'      => 'plugin',
            'client_company'    => 'NopStation / Brain Station 23 PLC',
            'live_url'          => 'https://www.nop-station.com/',
            'github_url'        => '',
            'is_featured'       => 0,
            'order'             => 6,
            'tech'              => [ 'ASP.NET Core', 'C#', 'NopCommerce', 'MSSQL', 'REST APIs', 'ZohoCRM', 'TaxJar', 'Entity Framework' ],
        ],
        [
            'title'             => 'TARBIYAH CMS',
            'short_description' => 'ASP.NET MVC content management system for Islamic education content, supporting bKash and SSLCommerz payment gateways for course enrolment.',
            'content'           => '<p>Designed and built TARBIYAH, a custom content management and e-learning platform for Islamic education. The platform enables educators to publish structured courses and learners to enrol and pay using local Bangladesh payment methods.</p><h3>Technical Highlights</h3><ul><li>Custom role-based CMS with admin, instructor, and student portals.</li><li>Course and content module management with video embedding.</li><li>bKash and SSLCommerz payment gateway integration for course fees.</li><li>Email notification system for enrolment confirmations and progress reminders.</li><li>MSSQL database with a normalised schema for content and user management.</li></ul>',
            'project_type'      => 'cms',
            'client_company'    => 'Private Client',
            'live_url'          => '',
            'github_url'        => '',
            'is_featured'       => 0,
            'order'             => 7,
            'tech'              => [ 'ASP.NET Core', 'C#', 'MSSQL', 'JavaScript', 'jQuery', 'HTML5', 'CSS3' ],
        ],
    ];

    foreach ( $projects as $data ) {
        $post_id = fhp_maybe_insert_post( 'project', $data['title'], [
            'post_content' => $data['content'],
        ] );
        if ( ! $post_id ) {
            continue;
        }

        fhp_set_field( 'short_description', $data['short_description'], $post_id );
        fhp_set_field( 'project_type',      $data['project_type'],      $post_id );
        fhp_set_field( 'client_company',    $data['client_company'],    $post_id );
        fhp_set_field( 'live_url',          $data['live_url'],          $post_id );
        fhp_set_field( 'github_url',        $data['github_url'],        $post_id );
        fhp_set_field( 'is_featured',       $data['is_featured'],       $post_id );
        fhp_set_field( 'order',             $data['order'],             $post_id );

        fhp_assign_tech_stack( $post_id, $data['tech'] );
    }
}

/* ====================================================================
   4.3  SKILLS  (~40 entries across 7 categories)
==================================================================== */

function fhp_seed_skills(): void {
    // [ name, proficiency (1–5), order ]
    $skills_by_category = [
        'Backend' => [
            [ 'C#',                        5, 1 ],
            [ 'ASP.NET Core',              5, 2 ],
            [ 'NopCommerce',               5, 3 ],
            [ 'REST APIs',                 5, 4 ],
            [ 'Microservices Architecture', 4, 5 ],
            [ 'gRPC',                      4, 6 ],
            [ 'RabbitMQ',                  4, 7 ],
            [ 'Entity Framework',          4, 8 ],
            [ 'AJAX',                      3, 9 ],
        ],
        'Frontend' => [
            [ 'Angular',    4, 1 ],
            [ 'Next.js',    3, 2 ],
            [ 'JavaScript', 4, 3 ],
            [ 'jQuery',     4, 4 ],
            [ 'HTML5',      4, 5 ],
            [ 'CSS3',       4, 6 ],
        ],
        'Database' => [
            [ 'MSSQL',   5, 1 ],
            [ 'MongoDB', 4, 2 ],
            [ 'MySQL',   3, 3 ],
        ],
        'DevOps & Cloud' => [
            [ 'Azure DevOps', 4, 1 ],
            [ 'Azure CI/CD',  4, 2 ],
            [ 'Linux',        3, 3 ],
            [ 'Firebase',     3, 4 ],
        ],
        'Integrations' => [
            [ 'SAP ERP',            4, 1 ],
            [ 'ZohoCRM',            3, 2 ],
            [ 'TaxJar',             3, 3 ],
            [ 'Payment Gateways',   4, 4 ],
            [ 'SMS Gateway',        3, 5 ],
            [ 'Push Notifications', 3, 6 ],
            [ 'Barcode Integration', 3, 7 ],
            [ 'Google Analytics',   3, 8 ],
            [ 'Sendinblue',         3, 9 ],
        ],
        'Soft Skills' => [
            [ 'Team Leadership',                 4, 1 ],
            [ 'Project Management',              4, 2 ],
            [ 'Client-facing Communication',     5, 3 ],
            [ 'International Team Collaboration', 4, 4 ],
            [ 'Scrum/Agile',                     4, 5 ],
        ],
        'Competitive Programming' => [
            [ 'C++',              4, 1 ],
            [ 'Algorithms',       4, 2 ],
            [ 'Data Structures',  4, 3 ],
        ],
    ];

    foreach ( $skills_by_category as $category => $skills ) {
        foreach ( $skills as [$name, $level, $order] ) {
            $post_id = fhp_maybe_insert_post( 'skill', $name );
            if ( ! $post_id ) {
                continue;
            }

            fhp_set_field( 'proficiency_level', $level, $post_id );
            fhp_set_field( 'order',             $order, $post_id );
            fhp_assign_skill_category( $post_id, $category );
        }
    }
}

/* ====================================================================
   4.4  ACHIEVEMENTS  (6 entries)
==================================================================== */

function fhp_seed_achievements(): void {
    $achievements = [
        [
            'title'        => 'NopCommerce Certified Developer',
            'type'         => 'Certification',
            'org'          => 'NopCommerce',
            'date'         => '2022-01-01',
            'credential'   => 'https://www.nopcommerce.com/en/fuad-hasan',
            'description'  => 'Official NopCommerce developer certification, validating expertise in plugin development, theme customisation, and enterprise-grade deployments.',
        ],
        [
            'title'        => 'Prompt Engineering Foundations',
            'type'         => 'Certification',
            'org'          => 'Brain Station 23 PLC',
            'date'         => '2023-06-01',
            'credential'   => '',
            'description'  => 'Completed internal certification on LLM prompt engineering fundamentals, covering few-shot prompting, chain-of-thought reasoning, and AI-assisted development workflows.',
        ],
        [
            'title'        => 'ICPC Dhaka Regional Onsite Contestant — Rank 96/300',
            'type'         => 'Contest',
            'org'          => 'ICPC Global',
            'date'         => '2019-12-01',
            'credential'   => '',
            'description'  => 'Competed in the ICPC Dhaka Regional onsite contest, finishing in the top third of 300 teams and qualifying for the regional heat.',
        ],
        [
            'title'        => 'DIU Take-Off Programming Contest Champion — Rank 1/300',
            'type'         => 'Award',
            'org'          => 'Daffodil International University (DIU)',
            'date'         => '2017-09-01',
            'credential'   => '',
            'description'  => 'Won first place out of 300 participants in the DIU Take-Off Programming Contest, Summer 2017, demonstrating strong algorithmic problem-solving skills.',
        ],
        [
            'title'        => 'Programming for Everybody (Python)',
            'type'         => 'Certification',
            'org'          => 'Coursera / University of Michigan',
            'date'         => '2020-04-01',
            'credential'   => '',
            'description'  => 'Completed the "Programming for Everybody (Getting Started with Python)" specialisation on Coursera by the University of Michigan.',
        ],
        [
            'title'        => 'Digital Assets Security Awareness',
            'type'         => 'Certification',
            'org'          => 'Brain Station 23 PLC',
            'date'         => '2023-01-01',
            'credential'   => '',
            'description'  => 'Completed internal cybersecurity certification on digital assets security, covering secure coding practices, threat modelling, and data protection compliance.',
        ],
    ];

    foreach ( $achievements as $data ) {
        $post_id = fhp_maybe_insert_post( 'achievement', $data['title'] );
        if ( ! $post_id ) {
            continue;
        }

        fhp_set_field( 'issuing_organization',    $data['org'],         $post_id );
        fhp_set_field( 'issue_date',              $data['date'],        $post_id );
        fhp_set_field( 'credential_url',          $data['credential'],  $post_id );
        fhp_set_field( 'achievement_description', $data['description'], $post_id );

        fhp_assign_achievement_type( $post_id, $data['type'] );
    }
}
