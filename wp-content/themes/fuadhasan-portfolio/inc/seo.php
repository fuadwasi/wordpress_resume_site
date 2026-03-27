<?php
/**
 * SEO — Section 10: Schema Markup, Meta Tags & Structured Data
 *
 * Provides a complete native SEO layer for the portfolio theme that
 * activates automatically when no third-party SEO plugin (Yoast SEO,
 * Rank Math) is present.  When a plugin IS active, this file is
 * silent so there are no conflicts or duplicated tags.
 *
 * Features:
 *   1. Custom document title via `pre_get_document_title` filter.
 *   2. Per-page meta description, canonical URL, Open Graph (og:*)
 *      and Twitter Card meta tags output on `wp_head` priority 2.
 *   3. JSON-LD structured-data schemas output on `wp_head` priority 5:
 *        • Person      — always (from ACF options page values)
 *        • WebPage     — always (title + description per page)
 *        • ItemList    — on the projects archive / projects page
 *        • BreadcrumbList — on every inner page (not the front page)
 *
 * All data is read from ACF options fields (with hardcoded fallbacks),
 * so the schemas are populated on day one even before a CV/photo is
 * uploaded.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
   GUARD — skip everything when a 3rd-party SEO plugin is active
==================================================================== */

/**
 * Returns true if Yoast SEO or Rank Math is active.
 * Both plugins handle their own title, meta, and schema output.
 *
 * @return bool
 */
function fhp_seo_plugin_active(): bool {
    return function_exists( 'yoast_head' ) || function_exists( 'rank_math_head' );
}

/* ====================================================================
   1.  DOCUMENT TITLE
   Hooks into WordPress's own title mechanism so the `<title>` tag
   rendered via `add_theme_support('title-tag')` matches the plan.
==================================================================== */

/**
 * Override the WordPress document title with per-page custom strings.
 * This filter short-circuits all WordPress title assembly logic.
 *
 * @return string
 */
add_filter( 'pre_get_document_title', 'fhp_document_title' );

function fhp_document_title(): string {
    if ( fhp_seo_plugin_active() ) {
        return ''; // Let the SEO plugin handle it.
    }
    return fhp_page_title();
}

/* ====================================================================
   2.  META TAGS — description, canonical, Open Graph, Twitter Card
==================================================================== */

add_action( 'wp_head', 'fhp_output_meta_tags', 2 );

/**
 * Output <meta> tags for description, canonical URL, Open Graph,
 * and Twitter Card.  Skipped when Yoast / Rank Math is active.
 */
function fhp_output_meta_tags(): void {
    if ( fhp_seo_plugin_active() ) {
        return;
    }

    $title       = fhp_page_title();
    $description = fhp_meta_description();
    $url         = fhp_current_url();
    $og_image    = fhp_og_image_url();
    $site_name   = get_bloginfo( 'name' );
    $og_type     = ( is_singular() && ! is_front_page() ) ? 'article' : 'website';

    echo "\n<!-- SEO: fuadhasan-portfolio/seo.php -->\n";

    // Meta description
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

    // Canonical URL
    echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";

    // Open Graph
    echo '<meta property="og:type"        content="' . esc_attr( $og_type )    . '">' . "\n";
    echo '<meta property="og:title"       content="' . esc_attr( $title )       . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
    echo '<meta property="og:url"         content="' . esc_url( $url )          . '">' . "\n";
    echo '<meta property="og:site_name"   content="' . esc_attr( $site_name )   . '">' . "\n";
    if ( $og_image ) {
        echo '<meta property="og:image"   content="' . esc_url( $og_image )     . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card"        content="summary_large_image">'           . "\n";
    echo '<meta name="twitter:title"       content="' . esc_attr( $title )       . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
    if ( $og_image ) {
        echo '<meta name="twitter:image"   content="' . esc_url( $og_image )     . '">' . "\n";
    }

    echo "\n";
}

/* ====================================================================
   3.  JSON-LD STRUCTURED DATA SCHEMAS
==================================================================== */

add_action( 'wp_head', 'fhp_output_schema_json_ld', 5 );

/**
 * Output JSON-LD <script> blocks for all applicable schema types.
 * Skipped when Yoast / Rank Math is active (they generate their own).
 */
function fhp_output_schema_json_ld(): void {
    if ( fhp_seo_plugin_active() ) {
        return;
    }

    $schemas = [];

    // Person — always present (portfolio is a personal site)
    $schemas[] = fhp_schema_person();

    // WebPage — describes the current page
    $schemas[] = fhp_schema_webpage();

    // ItemList — on the project archive or any page with slug "projects"
    if ( is_post_type_archive( 'project' ) || is_page( 'projects' ) ) {
        $item_list = fhp_schema_project_list();
        if ( $item_list ) {
            $schemas[] = $item_list;
        }
    }

    // BreadcrumbList — every inner page (not the front page)
    if ( ! is_front_page() ) {
        $schemas[] = fhp_schema_breadcrumbs();
    }

    foreach ( $schemas as $schema ) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
        echo "\n</script>\n";
    }
}

/* ====================================================================
   SCHEMA BUILDERS
==================================================================== */

/**
 * Build the Person schema for Fuad Hasan.
 * Values are read from ACF options fields (seeded in Section 9).
 *
 * @return array<string, mixed>
 */
function fhp_schema_person(): array {
    $name     = fhp_option( 'hero_name',      'Fuad Hasan' );
    $job      = fhp_option( 'hero_title',     'Senior Software Engineer II' );
    $email    = fhp_option( 'email_address',  'fhassanwasi@gmail.com' );
    $phone    = fhp_option( 'phone_number',   '+880 01792 478 378' );
    $github   = fhp_option( 'github_url',     'https://github.com/fuadwasi' );
    $linkedin = fhp_option( 'linkedin_url',   'https://www.linkedin.com/in/fuadwasi/' );
    $cf       = fhp_option( 'codeforces_url', 'https://codeforces.com/profile/fhwasi' );

    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'Person',
        'name'     => $name,
        'jobTitle' => $job,
        'email'    => 'mailto:' . $email,
        'telephone' => $phone,
        'url'      => home_url( '/' ),
        'sameAs'   => array_values( array_filter( [ $github, $linkedin, $cf ] ) ),
        'address'  => [
            '@type'          => 'PostalAddress',
            'addressLocality' => 'Dhaka',
            'addressCountry' => 'BD',
        ],
        'worksFor' => [
            '@type' => 'Organization',
            'name'  => 'Brain Station 23 PLC',
        ],
    ];

    $photo = fhp_og_image_url();
    if ( $photo ) {
        $schema['image'] = $photo;
    }

    return $schema;
}

/**
 * Build the WebPage schema for the current page.
 *
 * @return array<string, mixed>
 */
function fhp_schema_webpage(): array {
    return [
        '@context'    => 'https://schema.org',
        '@type'       => 'WebPage',
        'name'        => fhp_page_title(),
        'description' => fhp_meta_description(),
        'url'         => fhp_current_url(),
        'author'      => [
            '@type' => 'Person',
            'name'  => fhp_option( 'hero_name', 'Fuad Hasan' ),
        ],
    ];
}

/**
 * Build the ItemList schema from all published projects.
 * Returns null when no projects are found.
 *
 * @return array<string, mixed>|null
 */
function fhp_schema_project_list(): ?array {
    $projects = get_posts( [
        'post_type'      => 'project',
        'posts_per_page' => -1,
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'order',
        'order'          => 'ASC',
        'post_status'    => 'publish',
    ] );

    if ( empty( $projects ) ) {
        return null;
    }

    $items = [];
    foreach ( $projects as $i => $project ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => get_the_title( $project ),
            'url'      => get_permalink( $project ),
        ];
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => __( 'Projects Portfolio', 'fuadhasan-portfolio' ),
        'itemListElement' => $items,
    ];
}

/**
 * Build the BreadcrumbList schema for the current inner page.
 * Always starts with Home as position 1.
 *
 * @return array<string, mixed>
 */
function fhp_schema_breadcrumbs(): array {
    $items = [
        [
            '@type'    => 'ListItem',
            'position' => 1,
            'name'     => __( 'Home', 'fuadhasan-portfolio' ),
            'item'     => home_url( '/' ),
        ],
    ];

    if ( is_post_type_archive( 'experience' ) || is_page( 'experience' ) ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => __( 'Experience', 'fuadhasan-portfolio' ),
            'item'     => fhp_current_url(),
        ];
    } elseif ( is_post_type_archive( 'project' ) || is_page( 'projects' ) ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => __( 'Projects', 'fuadhasan-portfolio' ),
            'item'     => fhp_current_url(),
        ];
    } elseif ( is_single() ) {
        // Single post: archive link → post title
        $post_type   = get_post_type();
        $archive_url = $post_type ? get_post_type_archive_link( $post_type ) : false;
        if ( $archive_url ) {
            $pt_obj = get_post_type_object( $post_type );
            $items[] = [
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => $pt_obj->labels->name ?? $post_type,
                'item'     => $archive_url,
            ];
            $items[] = [
                '@type'    => 'ListItem',
                'position' => 3,
                'name'     => get_the_title(),
                'item'     => fhp_current_url(),
            ];
        } else {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => 2,
                'name'     => get_the_title(),
                'item'     => fhp_current_url(),
            ];
        }
    } elseif ( is_page() ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => 2,
            'name'     => get_the_title(),
            'item'     => fhp_current_url(),
        ];
    }

    return [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ];
}

/* ====================================================================
   HELPER FUNCTIONS
==================================================================== */

/**
 * Return the canonical URL for the current page.
 *
 * @return string
 */
function fhp_current_url(): string {
    if ( is_front_page() ) {
        return home_url( '/' );
    }
    if ( is_post_type_archive( 'experience' ) ) {
        return home_url( '/experience/' );
    }
    if ( is_post_type_archive( 'project' ) ) {
        return home_url( '/projects/' );
    }
    if ( is_singular() ) {
        return (string) get_permalink();
    }
    if ( is_page() ) {
        return (string) get_permalink();
    }
    // Fallback — reconstruct from $_SERVER (sanitised)
    return home_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ?? '/' ) ) );
}

/**
 * Return a per-page meta description string.
 *
 * Matches the page-URL mapping in the plan:
 *   /          → personal brand + expertise summary
 *   /about     → professional bio
 *   /experience → work history overview
 *   /projects  → project portfolio overview
 *   /contact   → contact invitation
 *   /skills    → skills summary
 *   /achievements → certifications overview
 *   singles    → short_description meta or excerpt
 *
 * @return string
 */
function fhp_meta_description(): string {
    $name    = fhp_option( 'hero_name', 'Fuad Hasan' );
    $default = fhp_option( 'hero_subtitle',
        'Certified NopCommerce Developer · ASP.NET Core · Microservices · B2B & B2C eCommerce'
    );

    if ( is_front_page() ) {
        return sprintf(
            /* translators: %s: person name */
            __( '%s is a Senior Software Engineer and Certified NopCommerce Developer with 3+ years building enterprise eCommerce, ERP integrations, and microservices solutions in Bangladesh.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_page( 'about' ) ) {
        return sprintf(
            /* translators: %s: person name */
            __( '%s — Senior Software Engineer at Brain Station 23 PLC, specialising in ASP.NET Core, NopCommerce, and scalable B2B & B2C eCommerce solutions. Based in Dhaka, Bangladesh.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_post_type_archive( 'experience' ) || is_page( 'experience' ) ) {
        return sprintf(
            /* translators: %s: person name */
            __( 'Full work history of %s — from Software Engineering Trainee to Senior Software Engineer II at Brain Station 23 PLC, with 3+ years of NopCommerce and enterprise development.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_post_type_archive( 'project' ) || is_page( 'projects' ) ) {
        return sprintf(
            /* translators: %s: person name */
            __( 'Portfolio of projects by %s: Shawpno eCommerce, Macsteel B2B Platform, NopCommerce POS, Delivery Management System, and more. Specialising in eCommerce and ERP integrations.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_page( 'contact' ) ) {
        return sprintf(
            /* translators: %s: person name */
            __( 'Get in touch with %s — Senior Software Engineer available for freelance, consulting, and full-time opportunities. Based in Dhaka, Bangladesh.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_page( 'skills' ) ) {
        return sprintf(
            /* translators: %s: person name */
            __( 'Technical skills of %s: ASP.NET Core, C#, NopCommerce, gRPC, RabbitMQ, Angular, MSSQL, MongoDB, Azure DevOps, and SAP ERP integration.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_page( 'achievements' ) ) {
        return sprintf(
            /* translators: %s: person name */
            __( 'Certifications and achievements of %s: NopCommerce Certified Developer, ICPC Dhaka Regional Contestant, and DIU Programming Contest Champion.', 'fuadhasan-portfolio' ),
            $name
        );
    }

    if ( is_singular() ) {
        // For CPT singles, prefer the short_description meta field
        $short_desc = get_post_meta( get_the_ID(), 'short_description', true );
        if ( $short_desc ) {
            return wp_strip_all_tags( (string) $short_desc );
        }
        $excerpt = get_the_excerpt();
        if ( $excerpt ) {
            return wp_strip_all_tags( $excerpt );
        }
    }

    return wp_strip_all_tags( $default );
}

/**
 * Return the URL of the profile photo for use in OG:image, Twitter:image,
 * and the Person schema's `image` property.
 *
 * Falls back to the custom logo, then returns an empty string.
 *
 * @return string
 */
function fhp_og_image_url(): string {
    if ( function_exists( 'get_field' ) ) {
        $photo = get_field( 'profile_photo', 'option' );
        if ( ! empty( $photo['url'] ) ) {
            return esc_url_raw( $photo['url'] );
        }
    }

    // Fallback: custom logo
    $logo_id = (int) get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $logo = wp_get_attachment_image_src( $logo_id, 'full' );
        if ( $logo ) {
            return esc_url_raw( $logo[0] );
        }
    }

    return '';
}
