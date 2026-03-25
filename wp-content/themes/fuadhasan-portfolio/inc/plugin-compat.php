<?php
/**
 * Plugin Compatibility & Integration Layer  —  Section 6
 *
 * Provides theme-side glue code for each plugin listed in the
 * development plan's Section 6.  All integrations degrade
 * gracefully when the relevant plugin is not active.
 *
 * Plugins covered:
 *  1. Yoast SEO / Rank Math        — JSON-LD schema, title & meta helpers
 *  2. WPForms Lite / Contact Form 7 — email recipient + spam-protection hooks
 *  3. WP Super Cache / W3 Total Cache — exclude /download-cv from page cache
 *  4. Smush / ShortPixel            — image lazy-loading & WebP support hints
 *  5. ACF Pro                       — already handled by acf-fields.php / acf-options.php
 *  6. Redirection plugin            — programmatic redirect rules
 *  7. Resource hints                — preconnect / dns-prefetch for Google Fonts & CDN
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
   1. SEO — Yoast SEO / Rank Math / fallback
==================================================================== */

/**
 * 1a. Structured Data (JSON-LD)
 *
 * Outputs schema.org markup as recommended in Section 6 of the plan:
 *  – Person   (Fuad Hasan — on every page)
 *  – WebSite  (sitelinks search box hint — home page only)
 *  – WebPage  (per-page type)
 *
 * Only runs when neither Yoast nor Rank Math is active (they handle
 * schema themselves).
 */
add_action( 'wp_head', 'fhp_output_schema_markup', 5 );

function fhp_output_schema_markup(): void {
    // Let Yoast / Rank Math handle schema when they are active
    if ( function_exists( 'yoast_head' ) || function_exists( 'rank_math_head' ) ) {
        return;
    }

    $site_url  = home_url( '/' );
    $site_name = get_bloginfo( 'name' );

    // ---- Person schema (Fuad Hasan) --------------------------------
    $person = [
        '@context' => 'https://schema.org',
        '@type'    => 'Person',
        '@id'      => $site_url . '#person',
        'name'     => fhp_option( 'hero_name', 'Fuad Hasan' ),
        'jobTitle' => fhp_option( 'hero_title', 'Senior Software Engineer II' ),
        'url'      => $site_url,
        'email'    => fhp_option( 'email_address', 'fhassanwasi@gmail.com' ),
        'telephone' => fhp_option( 'phone_number', '+880 01792 478 378' ),
        'address'  => [
            '@type'           => 'PostalAddress',
            'addressLocality' => 'Dhaka',
            'addressCountry'  => 'BD',
        ],
        'sameAs'   => array_filter( [
            fhp_option( 'github_url',     'https://github.com/fuadwasi' ),
            fhp_option( 'linkedin_url',   'https://www.linkedin.com/in/fuadwasi/' ),
            fhp_option( 'codeforces_url', 'https://codeforces.com/profile/fhwasi' ),
        ] ),
        'knowsAbout' => [
            'ASP.NET Core', 'C#', 'NopCommerce', 'Microservices',
            'B2B eCommerce', 'B2C eCommerce', 'gRPC', 'RabbitMQ',
            'Angular', 'Next.js', 'PHP', 'WordPress',
        ],
    ];

    // Profile photo if set
    $photo = fhp_option( 'profile_photo' );
    if ( ! empty( $photo['url'] ) ) {
        $person['image'] = [
            '@type'  => 'ImageObject',
            'url'    => $photo['url'],
            'width'  => $photo['width']  ?? 400,
            'height' => $photo['height'] ?? 400,
        ];
    }

    fhp_print_json_ld( $person );

    // ---- WebSite schema (home page only) ---------------------------
    if ( is_front_page() ) {
        $website = [
            '@context'        => 'https://schema.org',
            '@type'           => 'WebSite',
            '@id'             => $site_url . '#website',
            'url'             => $site_url,
            'name'            => $site_name,
            'description'     => get_bloginfo( 'description' ),
            'publisher'       => [ '@id' => $site_url . '#person' ],
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => $site_url . '?s={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
        fhp_print_json_ld( $website );
    }

    // ---- WebPage / WebPageElement schema (current page) -----------
    $webpage = [
        '@context'  => 'https://schema.org',
        '@type'     => fhp_get_page_schema_type(),
        '@id'       => get_permalink() . '#webpage',
        'url'       => get_permalink() ?: $site_url,
        'name'      => fhp_page_title(),
        'isPartOf'  => [ '@id' => $site_url . '#website' ],
        'about'     => [ '@id' => $site_url . '#person' ],
        'inLanguage' => get_bloginfo( 'language' ),
    ];
    fhp_print_json_ld( $webpage );

    // ---- ItemList for projects archive ----------------------------
    if ( is_post_type_archive( 'project' ) ) {
        $projects_q = fhp_get_all_projects();
        $items      = [];
        $pos        = 1;
        while ( $projects_q->have_posts() ) {
            $projects_q->the_post();
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $pos++,
                'name'     => get_the_title(),
                'url'      => get_permalink(),
            ];
        }
        wp_reset_postdata();

        if ( $items ) {
            fhp_print_json_ld( [
                '@context'        => 'https://schema.org',
                '@type'           => 'ItemList',
                'name'            => 'Portfolio Projects',
                'itemListElement' => $items,
            ] );
        }
    }
}

/**
 * Return the most specific schema.org WebPage sub-type for the current URL.
 *
 * @return string
 */
function fhp_get_page_schema_type(): string {
    if ( is_front_page() )              return 'WebPage';
    if ( is_page( 'contact' ) )         return 'ContactPage';
    if ( is_page( 'about' ) )           return 'AboutPage';
    if ( is_post_type_archive( 'project' ) || is_singular( 'project' ) ) return 'CollectionPage';
    if ( is_post_type_archive( 'experience' ) || is_singular( 'experience' ) ) return 'WebPage';
    return 'WebPage';
}

/**
 * Print a JSON-LD <script> block.
 *
 * @param array<string, mixed> $data
 */
function fhp_print_json_ld( array $data ): void {
    // wp_json_encode uses JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
    echo '<script type="application/ld+json">'
        // phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
        . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT )
        . '</script>' . "\n";
}

/* ------------------------------------------------------------------
   1b. SEO Title filter (fallback when no SEO plugin is active)
------------------------------------------------------------------ */
add_filter( 'pre_get_document_title', 'fhp_seo_title_filter' );

function fhp_seo_title_filter( string $title ): string {
    // Only apply when neither Yoast nor Rank Math is active
    if ( function_exists( 'wpseo_init' ) || function_exists( 'rank_math' ) ) {
        return $title;
    }
    return fhp_page_title();
}

/* ------------------------------------------------------------------
   1c. Open Graph meta tags (fallback when no SEO plugin active)
------------------------------------------------------------------ */
add_action( 'wp_head', 'fhp_og_meta_tags', 6 );

function fhp_og_meta_tags(): void {
    // Let SEO plugins handle OG when active
    if ( function_exists( 'wpseo_init' ) || function_exists( 'rank_math' ) ) {
        return;
    }

    $site_name  = get_bloginfo( 'name' );
    $title      = fhp_page_title();
    $url        = get_permalink() ?: home_url( '/' );
    $desc       = get_bloginfo( 'description' );
    $image_url  = '';

    // Use profile photo as default OG image
    $photo = fhp_option( 'profile_photo' );
    if ( ! empty( $photo['url'] ) ) {
        $image_url = $photo['url'];
    }

    // For single posts/CPTs use featured image if available
    if ( is_singular() && has_post_thumbnail() ) {
        $thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
        if ( $thumb ) {
            $image_url = $thumb[0];
        }
    }

    echo "\n<!-- Open Graph / Twitter Card -->\n";
    printf( '<meta property="og:type"        content="%s">' . "\n", is_front_page() ? 'profile' : 'website' );
    printf( '<meta property="og:url"         content="%s">' . "\n", esc_url( $url ) );
    printf( '<meta property="og:site_name"   content="%s">' . "\n", esc_attr( $site_name ) );
    printf( '<meta property="og:title"       content="%s">' . "\n", esc_attr( $title ) );
    printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
    if ( $image_url ) {
        printf( '<meta property="og:image"       content="%s">' . "\n", esc_url( $image_url ) );
    }
    printf( '<meta name="twitter:card"        content="summary_large_image">' . "\n" );
    printf( '<meta name="twitter:title"       content="%s">' . "\n", esc_attr( $title ) );
    printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
    if ( $image_url ) {
        printf( '<meta name="twitter:image"      content="%s">' . "\n", esc_url( $image_url ) );
    }
    echo "\n";
}

/* ====================================================================
   2. CONTACT FORM — WPForms Lite / Contact Form 7
==================================================================== */

/**
 * 2a. Set the notification email for Contact Form 7 to the
 *     address stored in ACF options (or the fallback default).
 *
 * Hooks into the CF7 mail component after the form is submitted
 * so that changing the email in Site Settings is reflected
 * automatically without editing the form.
 */
add_filter( 'wpcf7_mail_components', 'fhp_cf7_set_recipient', 10, 2 );

function fhp_cf7_set_recipient( array $components ): array {
    $email = fhp_option( 'email_address', 'fhassanwasi@gmail.com' );
    if ( $email && isset( $components['recipient'] ) ) {
        $components['recipient'] = sanitize_email( $email );
    }
    return $components;
}

/**
 * 2b. Set the notification email for WPForms to the address from ACF.
 *
 * WPForms uses a different filter path per form notification.
 * We attach at the global wpforms_process level to override all forms.
 */
add_filter( 'wpforms_entry_email_atts', 'fhp_wpforms_set_recipient', 10, 5 );

function fhp_wpforms_set_recipient( array $atts ): array {
    $email = fhp_option( 'email_address', 'fhassanwasi@gmail.com' );
    if ( $email && isset( $atts['to'] ) ) {
        $atts['to'] = sanitize_email( $email );
    }
    return $atts;
}

/* ====================================================================
   3. PAGE CACHING — WP Super Cache / W3 Total Cache / LiteSpeed
==================================================================== */

/**
 * 3a. Prevent page caches from storing the /download-cv response.
 *
 * WP Super Cache, W3TC, and LiteSpeed Cache all honour the
 * DONOTCACHEPAGE constant and the Cache-Control: no-store header.
 * The `nocache_headers()` call in cv-download.php already sets
 * the HTTP headers; this constant covers plugin-level bypasses.
 */
add_action( 'template_redirect', 'fhp_exclude_cv_from_cache', 1 );

function fhp_exclude_cv_from_cache(): void {
    if ( get_query_var( 'fhp_cv_download' ) ) {
        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( 'DONOTCACHEPAGE', true );
        }
        if ( ! defined( 'DONOTCACHEDB' ) ) {
            define( 'DONOTCACHEDB', true );
        }
        if ( ! defined( 'DONOTMINIFY' ) ) {
            define( 'DONOTMINIFY', true );
        }
    }
}

/**
 * 3b. Tell W3 Total Cache to exclude the download-cv path.
 *     W3TC uses this filter to build its "never cache" list.
 */
add_filter( 'w3tc_config_default', 'fhp_w3tc_exclude_cv' );

function fhp_w3tc_exclude_cv( array $config ): array {
    $config['pgcache.reject.uri'][] = '/download-cv';
    return $config;
}

/* ====================================================================
   4. IMAGE OPTIMISATION — Smush / ShortPixel / Core lazy-load
==================================================================== */

/**
 * 4a. Ensure WordPress's built-in lazy loading is on for all images
 *     below the fold (WordPress 5.5+ default, but explicit is safer).
 */
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

/**
 * 4b. Suggest the 'loading' attribute value per context.
 *
 * Images in the hero and the profile photo use loading="eager" (set
 * directly in the template).  Everything else gets lazy.
 *
 * @param string $value   The 'loading' attribute value ('lazy'|'eager').
 * @param string $tag_name HTML element tag name.
 * @param string $context  Template context identifier.
 * @return string
 */
add_filter( 'wp_lazy_loading_enabled', function ( bool $enabled, string $tag_name, string $context ) {
    // Never lazy-load the logo or hero photo  
    if ( in_array( $context, [ 'the_custom_logo', 'get_avatar' ], true ) ) {
        return false;
    }
    return $enabled;
}, 10, 3 );

/**
 * 4c. Add width/height to images that lack them so that the browser
 *     can reserve layout space and avoid CLS (Core Web Vitals).
 *     Smush and ShortPixel respect these when generating WebP.
 */
add_filter( 'the_content', 'fhp_add_image_dimensions', 20 );

function fhp_add_image_dimensions( string $content ): string {
    // Only parse if content actually contains images
    if ( strpos( $content, '<img' ) === false ) {
        return $content;
    }

    $content = preg_replace_callback(
        '/<img([^>]+)>/i',
        static function ( array $matches ): string {
            $tag  = $matches[0];
            $attr = $matches[1];

            // Already has width + height → leave untouched
            if ( preg_match( '/\bwidth\s*=/i', $attr ) && preg_match( '/\bheight\s*=/i', $attr ) ) {
                return $tag;
            }

            // Try to resolve from WordPress attachment ID
            if ( preg_match( '/class=["\'][^"\']*wp-image-(\d+)[^"\']*["\']/', $attr, $m ) ) {
                $id   = (int) $m[1];
                $meta = wp_get_attachment_metadata( $id );
                if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
                    $attr .= sprintf( ' width="%d" height="%d"', $meta['width'], $meta['height'] );
                    return '<img' . $attr . '>';
                }
            }

            return $tag;
        },
        $content
    );

    return $content ?? '';
}

/* ====================================================================
   5. RESOURCE HINTS — preconnect / dns-prefetch
==================================================================== */

/**
 * Add <link rel="preconnect"> and <link rel="dns-prefetch"> for
 * external origins used by the theme (Google Fonts, Gravatar, etc.)
 * Improves LCP by reducing connection setup latency.
 */
add_filter( 'wp_resource_hints', 'fhp_resource_hints', 10, 2 );

function fhp_resource_hints( array $urls, string $relation_type ): array {
    $origins = [
        'https://fonts.googleapis.com',
        'https://fonts.gstatic.com',
    ];

    foreach ( $origins as $origin ) {
        if ( in_array( $relation_type, [ 'preconnect', 'dns-prefetch' ], true ) ) {
            $urls[] = [
                'href'        => $origin,
                'crossorigin' => ( $relation_type === 'preconnect' ) ? 'anonymous' : false,
            ];
        }
    }

    return $urls;
}

/* ====================================================================
   6. REDIRECTION PLUGIN — programmatic redirect registration
==================================================================== */

/**
 * Register legacy URL → canonical WordPress permalink redirects.
 *
 * These mirror the static HTML site paths at resume-to-site-magic-70.lovable.app
 * and ensure inbound links are preserved after migration.
 *
 * When the Redirection plugin is active its API is used; otherwise
 * WordPress's own rewrite layer handles the canonical URLs.
 *
 * Only runs once (guarded by the fhp_redirects_registered option).
 */
add_action( 'init', 'fhp_register_redirects', 20 );

function fhp_register_redirects(): void {
    if ( get_option( 'fhp_redirects_registered' ) ) {
        return;
    }

    // Map: old path → new canonical path
    $redirects = [
        '/index.html'        => '/',
        '/about.html'        => '/about',
        '/resume.html'       => '/experience',
        '/portfolio.html'    => '/projects',
        '/skills.html'       => '/skills',
        '/certificates.html' => '/achievements',
        '/contact.html'      => '/contact',
        '/cv'                => '/download-cv',
        '/resume'            => '/experience',
    ];

    if ( class_exists( 'Red_Item' ) && method_exists( 'Red_Item', 'create' ) ) {
        // Redirection plugin is available — use its API
        foreach ( $redirects as $from => $to ) {
            try {
                \Red_Item::create( [
                    'url'          => $from,
                    'action_data'  => [ 'url' => $to ],
                    'match_type'   => 'url',
                    'action_type'  => 'url',
                    'action_code'  => 301,
                    'group_id'     => 1,
                ] );
            } catch ( \Exception $e ) {
                // Silently skip duplicates / errors
            }
        }
        update_option( 'fhp_redirects_registered', true );
    } else {
        // Redirection plugin not yet active; register lightweight
        // WordPress rewrite rules for the most common legacy paths.
        foreach ( $redirects as $from => $to ) {
            $pattern = ltrim( $from, '/' );
            if ( $pattern ) {
                add_rewrite_rule(
                    '^' . preg_quote( $pattern, '#' ) . '/?$',
                    'index.php?fhp_redirect_to=' . urlencode( $to ),
                    'top'
                );
            }
        }
    }
}

// Register the query var and handle the redirect for WP-native rules
add_filter( 'query_vars', function ( array $vars ): array {
    $vars[] = 'fhp_redirect_to';
    return $vars;
} );

add_action( 'template_redirect', 'fhp_handle_legacy_redirect', 1 );

function fhp_handle_legacy_redirect(): void {
    $target = get_query_var( 'fhp_redirect_to' );
    if ( ! $target ) {
        return;
    }

    // Allow only relative paths that start with /
    $target = '/' . ltrim( sanitize_text_field( urldecode( $target ) ), '/' );
    wp_safe_redirect( home_url( $target ), 301 );
    exit;
}

/* ====================================================================
   7. WORDFENCE / SECURITY — theme-level hardening
==================================================================== */

/**
 * 7a. Remove the WordPress version string from the HTML source to
 *     reduce fingerprinting surface (Wordfence recommends this).
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * 7b. Disable XML-RPC if it is not needed (Wordfence recommendation).
 *     Admin can override via the plugin settings if pingbacks are needed.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * 7c. Remove version query strings from enqueued assets to reduce
 *     fingerprinting (theme assets use FHP_VERSION which is managed).
 */
add_filter( 'style_loader_src',  'fhp_remove_wp_version_from_src' );
add_filter( 'script_loader_src', 'fhp_remove_wp_version_from_src' );

function fhp_remove_wp_version_from_src( string $src ): string {
    // Only strip the global WP version param (ver=X.X.X matching WP core version)
    $wp_version = get_bloginfo( 'version' );
    if ( $wp_version && strpos( $src, 'ver=' . $wp_version ) !== false ) {
        return remove_query_arg( 'ver', $src );
    }
    return $src;
}

/* ====================================================================
   8. PERFORMANCE HELPERS — Core Web Vitals
==================================================================== */

/**
 * 8a. Preload the hero font (Inter 700) to improve LCP.
 *     Google Fonts are loaded via enqueue.php; this adds an early
 *     preload hint so the browser fetches the font before parsing CSS.
 */
add_action( 'wp_head', 'fhp_preload_critical_font', 1 );

function fhp_preload_critical_font(): void {
    // Only preload on front page where hero is visible above the fold
    if ( ! is_front_page() ) {
        return;
    }
    echo '<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" crossorigin="anonymous">' . "\n";
}

/**
 * 8b. Add fetchpriority="high" to the hero profile photo (LCP element).
 *     Only affects the hero image on the front page.
 */
add_filter( 'wp_get_attachment_image_attributes', 'fhp_hero_image_fetch_priority', 10, 3 );

function fhp_hero_image_fetch_priority( array $attr, \WP_Post $attachment, $size ): array {
    // Tag the profile photo with high fetch priority on the home page
    if ( is_front_page() ) {
        $profile_photo = fhp_option( 'profile_photo' );
        if ( ! empty( $profile_photo['ID'] ) && (int) $profile_photo['ID'] === $attachment->ID ) {
            $attr['fetchpriority'] = 'high';
            $attr['loading']       = 'eager';
        }
    }
    return $attr;
}
