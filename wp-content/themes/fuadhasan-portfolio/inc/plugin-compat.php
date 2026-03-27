<?php
/**
 * Plugin Compatibility & Integration Layer  —  Section 6
 *
 * Provides theme-side glue code for each plugin listed in the
 * development plan's Section 6.  All integrations degrade
 * gracefully when the relevant plugin is not active.
 *
 * Note: SEO schema markup, Open Graph meta tags, per-page titles/descriptions,
 * and resource hints are handled by inc/seo.php and inc/performance.php
 * (Section 10) which supersede any duplicate functionality that was here.
 *
 * Plugins covered:
 *  1. WPForms Lite / Contact Form 7  — email recipient + spam-protection hooks
 *  2. WP Super Cache / W3 Total Cache — exclude /download-cv from page cache
 *  3. Smush / ShortPixel             — image dimension hints & WebP support
 *  4. Redirection plugin             — programmatic redirect rules
 *  5. Wordfence                      — XML-RPC disable, WP version fingerprint removal
 *  6. Core Web Vitals                — preload hero font, fetchpriority on hero image
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
   1. CONTACT FORM — WPForms Lite / Contact Form 7
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
   3. IMAGE OPTIMISATION — Smush / ShortPixel / Core lazy-load
==================================================================== */

/**
 * 3a. Suggest the 'loading' attribute value per context.
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
 * 3b. Add width/height to images that lack them so that the browser
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
   4. REDIRECTION PLUGIN — programmatic redirect registration
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
   5. WORDFENCE / SECURITY — theme-level hardening
==================================================================== */

/**
 * 5a. Disable XML-RPC if it is not needed (Wordfence recommendation).
 *     Admin can override via the plugin settings if pingbacks are needed.
 *     Note: wp_generator removal is handled by fhp_cleanup_head() in
 *     inc/performance.php to avoid duplicate remove_action calls.
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * 5b. Remove version query strings from enqueued assets to reduce
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
   6. PERFORMANCE HELPERS — Core Web Vitals
==================================================================== */

/**
 * 6a. Preload the hero font (Inter 700) to improve LCP.
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
 * 6b. Add fetchpriority="high" to the hero profile photo (LCP element).
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
