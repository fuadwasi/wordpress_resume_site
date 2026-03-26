<?php
/**
 * Performance Optimizations — Section 10: SEO & Performance
 *
 * Implements the complete performance checklist from the plan:
 *
 *   1. Resource hints — <link rel="preconnect"> and dns-prefetch for
 *      Google Fonts, reducing connection latency for web fonts.
 *
 *   2. Script defer — adds the `defer` attribute to all theme JS
 *      handles so they do not block HTML parsing.
 *
 *   3. Cache-Control headers — sets `Cache-Control: public, max-age=3600`
 *      on public front-end pages so browsers and proxies can cache HTML.
 *
 *   4. Native lazy-loading — ensures WordPress core's `loading="lazy"`
 *      attribute is applied to all attachment images (WP 5.5+).
 *
 *   5. <head> cleanup — removes unnecessary WordPress default head
 *      elements (WLW manifest, RSD, REST discovery link, oEmbed, version
 *      generator, emoji scripts, X-Pingback header).
 *
 *   6. Query string removal — strips `?ver=` from theme asset URLs so
 *      CDNs and proxy caches can serve cached copies.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ====================================================================
   1.  RESOURCE HINTS
   Emitted early in <head> (priority 1) so the browser opens the TLS
   connection to Google Fonts servers before the @font-face request.
==================================================================== */

add_action( 'wp_head', 'fhp_resource_hints', 1 );

/**
 * Output preconnect and dns-prefetch hints for Google Fonts CDN origins.
 * Placed at priority 1 to ensure they appear before any CSS <link> tags.
 */
function fhp_resource_hints(): void {
    echo "\n<!-- Resource hints: fuadhasan-portfolio/performance.php -->\n";
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
}

/* ====================================================================
   2.  DEFER NON-CRITICAL SCRIPTS
   All three theme JS handles are loaded in the footer already (in_footer
   = true in enqueue.php), but adding `defer` provides an additional hint
   to browsers that these scripts do not need to block rendering.
==================================================================== */

add_filter( 'script_loader_tag', 'fhp_defer_scripts', 10, 3 );

/**
 * Add the `defer` attribute to theme JavaScript handles.
 *
 * @param string $tag     The full <script> tag HTML.
 * @param string $handle  The script handle.
 * @param string $src     The script source URL.
 * @return string
 */
function fhp_defer_scripts( string $tag, string $handle, string $src ): string {
    $defer_handles = [ 'fhp-smooth-scroll', 'fhp-skills-animation', 'fhp-main' ];

    if ( in_array( $handle, $defer_handles, true ) && strpos( $tag, 'defer' ) === false ) {
        // Insert `defer` into the opening <script> tag.
        $tag = str_replace( '<script ', '<script defer ', $tag );
    }

    return $tag;
}

/* ====================================================================
   3.  CACHE-CONTROL HEADERS
   Sets Cache-Control: public, max-age=3600 for all public non-admin
   pages.  stale-while-revalidate=60 allows the cached page to be
   served for an extra 60 seconds while the cache is being refreshed
   in the background.
==================================================================== */

add_action( 'send_headers', 'fhp_cache_control_headers' );

/**
 * Emit Cache-Control and Vary headers for public front-end pages.
 * Admin, logged-in, search, 404, and feed responses are excluded.
 */
function fhp_cache_control_headers(): void {
    // Never cache admin, logged-in users, or dynamic content.
    if ( is_admin() || is_user_logged_in() ) {
        return;
    }

    // Skip search results, 404 pages, and feeds — content is dynamic.
    if ( is_search() || is_404() || is_feed() ) {
        return;
    }

    if ( ! headers_sent() ) {
        header( 'Cache-Control: public, max-age=3600, stale-while-revalidate=60' );
        header( 'Vary: Accept-Encoding' );
    }
}

/* ====================================================================
   4.  LAZY-LOAD IMAGES
   WordPress 5.5+ applies loading="lazy" to images output by core
   template functions.  We explicitly ensure it is not accidentally
   disabled and also add it to ACF attachment-image output.
==================================================================== */

// Guarantee lazy loading is always enabled for core images.
add_filter( 'wp_lazy_loading_enabled', '__return_true' );

add_filter( 'wp_get_attachment_image_attributes', 'fhp_lazy_attachment_images', 10, 2 );

/**
 * Ensure loading="lazy" is present on all images produced by
 * wp_get_attachment_image() (used by ACF image fields).
 *
 * @param array<string, string> $attrs       Existing image attributes.
 * @param WP_Post               $attachment  The attachment post object.
 * @return array<string, string>
 */
function fhp_lazy_attachment_images( array $attrs, WP_Post $attachment ): array {
    if ( ! isset( $attrs['loading'] ) ) {
        $attrs['loading'] = 'lazy';
    }
    return $attrs;
}

/* ====================================================================
   5.  <head> CLEANUP
   Removes unnecessary default WordPress head elements that add weight
   and can expose information about the underlying CMS version.
==================================================================== */

add_action( 'init', 'fhp_cleanup_head' );

/**
 * Remove default WordPress head output that a portfolio site does not need.
 *
 * Removed items:
 *  - Windows Live Writer manifest link (wlwmanifest_link)
 *  - Really Simple Discovery (RSD) link
 *  - REST API discovery link
 *  - oEmbed discovery links
 *  - WordPress version generator meta tag
 *  - Emoji detection script and stylesheet (saves ~10 KB per request)
 *  - X-Pingback response header
 */
function fhp_cleanup_head(): void {
    // --- Desktop publishing / discovery links ---
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'rsd_link' );

    // --- REST API & oEmbed discovery ---
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

    // --- WordPress version (security: avoid advertising WP version) ---
    remove_action( 'wp_head', 'wp_generator' );

    // --- Emoji (not needed for a developer portfolio) ---
    remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles',     'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles',  'print_emoji_styles' );
    remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
    remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );

    // --- X-Pingback header ---
    add_filter( 'wp_headers', 'fhp_remove_x_pingback_header' );
}

/**
 * Remove the X-Pingback header from all WordPress responses.
 *
 * @param array<string, string> $headers
 * @return array<string, string>
 */
function fhp_remove_x_pingback_header( array $headers ): array {
    unset( $headers['X-Pingback'] );
    return $headers;
}

/* ====================================================================
   6.  REMOVE QUERY STRINGS FROM THEME ASSET URLS
   Query strings (?ver=x.x.x) on CSS/JS URLs prevent some CDNs and
   reverse proxies from caching those assets.  We strip the `ver`
   parameter only from our own theme assets (identified by FHP_ASSETS
   prefix) so that WordPress core and plugin assets are unaffected.
==================================================================== */

add_filter( 'style_loader_src',  'fhp_remove_query_strings_from_theme_assets', 10 );
add_filter( 'script_loader_src', 'fhp_remove_query_strings_from_theme_assets', 10 );

/**
 * Strip the `ver` query string from theme CSS and JS asset URLs.
 *
 * Only theme-owned assets (whose URL starts with FHP_ASSETS) are
 * modified.  Core WordPress and plugin assets are left untouched.
 *
 * @param string $src  The asset URL.
 * @return string
 */
function fhp_remove_query_strings_from_theme_assets( string $src ): string {
    if ( is_admin() ) {
        return $src;
    }

    if ( strpos( $src, FHP_ASSETS ) !== false ) {
        return remove_query_arg( 'ver', $src );
    }

    return $src;
}
