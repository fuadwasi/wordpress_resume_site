<?php
/**
 * Recommended Plugins Admin Notice  —  Section 6
 *
 * Displays a dismissible admin notice listing all recommended and
 * required plugins from the development plan's Section 6.
 *
 * Each plugin shows:
 *  – Status  (Active ✓ / Installed (inactive) / Not installed)
 *  – A direct "Install & Activate" link for uninstalled plugins
 *  – A direct "Activate" link for installed-but-inactive plugins
 *
 * The notice can be dismissed globally via the "Dismiss" link.
 * Dismissal is stored in a user meta key so it is per-user.
 *
 * @package FuadHasanPortfolio
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------
   Plugin registry
   Each entry: slug (for WordPress.org), file (plugin_dir/file.php),
   name, purpose, configuration notes.
------------------------------------------------------------------ */

/**
 * Return the full list of recommended plugins from Section 6.
 *
 * @return array<int, array{slug:string, file:string, name:string, purpose:string, config:string, required:bool}>
 */
function fhp_get_recommended_plugins(): array {
    return [
        [
            'slug'     => 'advanced-custom-fields',
            'file'     => 'advanced-custom-fields/acf.php',
            'name'     => 'Advanced Custom Fields (ACF)',
            'purpose'  => 'Custom fields for all CPTs + Options Page',
            'config'   => 'Activate, then visit Site Settings to configure hero, contact, CV options.',
            'required' => true,
        ],
        [
            'slug'     => 'wpforms-lite',
            'file'     => 'wpforms-lite/wpforms.php',
            'name'     => 'WPForms Lite',
            'purpose'  => 'Contact form on /contact page',
            'config'   => 'Create a "Contact Me" form → copy its shortcode → paste into the Contact page body.',
            'required' => true,
        ],
        [
            'slug'     => 'wordpress-seo',
            'file'     => 'wordpress-seo/wp-seo.php',
            'name'     => 'Yoast SEO',
            'purpose'  => 'On-page SEO, meta tags, XML sitemaps',
            'config'   => 'Enable schema type Person. Set breadcrumbs. Configure social profiles.',
            'required' => true,
        ],
        [
            'slug'     => 'wp-super-cache',
            'file'     => 'wp-super-cache/wp-cache.php',
            'name'     => 'WP Super Cache',
            'purpose'  => 'Page caching for <2s load time',
            'config'   => 'Enable caching. Add /download-cv to "Rejected URIs" list.',
            'required' => false,
        ],
        [
            'slug'     => 'wordfence',
            'file'     => 'wordfence/wordfence.php',
            'name'     => 'Wordfence Security',
            'purpose'  => 'Brute-force protection, firewall',
            'config'   => 'Enable firewall in "Learning Mode" for 1 week, then switch to "Enabled". Turn on 2FA for admin.',
            'required' => false,
        ],
        [
            'slug'     => 'updraftplus',
            'file'     => 'updraftplus/updraftplus.php',
            'name'     => 'UpdraftPlus Backups',
            'purpose'  => 'Automated database + file backups',
            'config'   => 'Schedule daily backups. Connect to Google Drive or Dropbox remote storage.',
            'required' => false,
        ],
        [
            'slug'     => 'smush',
            'file'     => 'wp-smushit/wp-smush.php',
            'name'     => 'Smush Image Compression',
            'purpose'  => 'Image compression & WebP conversion',
            'config'   => 'Enable "Auto-smush on upload". Turn on WebP conversion and lazy loading.',
            'required' => false,
        ],
        [
            'slug'     => 'redirection',
            'file'     => 'redirection/redirection.php',
            'name'     => 'Redirection',
            'purpose'  => 'URL management, 301 redirects',
            'config'   => 'Run Setup Wizard. The theme registers legacy HTML paths automatically on first load.',
            'required' => false,
        ],
    ];
}

/* ------------------------------------------------------------------
   Dismissal handler
------------------------------------------------------------------ */

add_action( 'admin_init', 'fhp_handle_plugin_notice_dismissal' );

function fhp_handle_plugin_notice_dismissal(): void {
    if (
        isset( $_GET['fhp_dismiss_plugins'] )
        && current_user_can( 'manage_options' )
        && check_admin_referer( 'fhp_dismiss_plugins_nonce', 'fhp_nonce' )
    ) {
        update_user_meta( get_current_user_id(), 'fhp_plugins_notice_dismissed', true );
        wp_safe_redirect( remove_query_arg( [ 'fhp_dismiss_plugins', 'fhp_nonce' ] ) );
        exit;
    }
}

/* ------------------------------------------------------------------
   Admin notice output
------------------------------------------------------------------ */

add_action( 'admin_notices', 'fhp_recommended_plugins_notice' );

function fhp_recommended_plugins_notice(): void {
    // Only show to administrators
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Hidden if the user dismissed it
    if ( get_user_meta( get_current_user_id(), 'fhp_plugins_notice_dismissed', true ) ) {
        return;
    }

    $plugins  = fhp_get_recommended_plugins();
    $statuses = fhp_get_plugin_statuses( $plugins );

    // Don't show the notice if all required plugins are active
    $all_required_active = true;
    foreach ( $plugins as $plugin ) {
        if ( $plugin['required'] && ( $statuses[ $plugin['file'] ] ?? 'not_installed' ) !== 'active' ) {
            $all_required_active = false;
            break;
        }
    }

    if ( $all_required_active ) {
        return;
    }

    $dismiss_url = wp_nonce_url(
        add_query_arg( 'fhp_dismiss_plugins', '1' ),
        'fhp_dismiss_plugins_nonce',
        'fhp_nonce'
    );
    ?>
    <div class="notice notice-info fhp-plugins-notice" style="padding:16px 16px 0;">

        <h3 style="margin:0 0 8px; font-size:14px; display:flex; align-items:center; gap:8px;">
            <span style="background:#6c63ff; color:#fff; padding:2px 8px; border-radius:20px; font-size:12px;">
                Fuad Hasan Portfolio
            </span>
            <?php esc_html_e( 'Recommended Plugins — Section 6 Setup', 'fuadhasan-portfolio' ); ?>
        </h3>

        <p style="color:#50575e; margin:0 0 12px;">
            <?php esc_html_e( 'Install and activate the following plugins to complete the portfolio setup. Required plugins are marked with ★.', 'fuadhasan-portfolio' ); ?>
        </p>

        <table style="border-collapse:collapse; width:100%; margin-bottom:12px; font-size:13px;">
            <thead>
                <tr style="background:#f6f7f7;">
                    <th style="padding:8px 12px; text-align:left; border:1px solid #e0e0e0; width:200px;">
                        <?php esc_html_e( 'Plugin', 'fuadhasan-portfolio' ); ?>
                    </th>
                    <th style="padding:8px 12px; text-align:left; border:1px solid #e0e0e0;">
                        <?php esc_html_e( 'Purpose', 'fuadhasan-portfolio' ); ?>
                    </th>
                    <th style="padding:8px 12px; text-align:left; border:1px solid #e0e0e0; width:320px;">
                        <?php esc_html_e( 'Configuration', 'fuadhasan-portfolio' ); ?>
                    </th>
                    <th style="padding:8px 12px; text-align:left; border:1px solid #e0e0e0; width:120px;">
                        <?php esc_html_e( 'Status', 'fuadhasan-portfolio' ); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $plugins as $plugin ) :
                    $status = $statuses[ $plugin['file'] ] ?? 'not_installed';
                    ?>
                <tr style="border:1px solid #e0e0e0; <?php echo $plugin['required'] ? 'background:#fefefe;' : ''; ?>">
                    <td style="padding:8px 12px; border:1px solid #e0e0e0; vertical-align:top;">
                        <?php if ( $plugin['required'] ) : ?>
                            <span title="<?php esc_attr_e( 'Required', 'fuadhasan-portfolio' ); ?>" style="color:#d63638;">★</span>
                        <?php endif; ?>
                        <strong><?php echo esc_html( $plugin['name'] ); ?></strong>
                    </td>
                    <td style="padding:8px 12px; border:1px solid #e0e0e0; vertical-align:top; color:#50575e;">
                        <?php echo esc_html( $plugin['purpose'] ); ?>
                    </td>
                    <td style="padding:8px 12px; border:1px solid #e0e0e0; vertical-align:top; color:#50575e; font-size:12px;">
                        <?php echo esc_html( $plugin['config'] ); ?>
                    </td>
                    <td style="padding:8px 12px; border:1px solid #e0e0e0; vertical-align:top;">
                        <?php echo fhp_plugin_status_badge( $status, $plugin['slug'], $plugin['file'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p style="margin:0 0 12px; font-size:12px; color:#50575e;">
            <a href="<?php echo esc_url( $dismiss_url ); ?>">
                <?php esc_html_e( '✕ Dismiss this notice', 'fuadhasan-portfolio' ); ?>
            </a>
        </p>

    </div>
    <?php
}

/* ------------------------------------------------------------------
   Helper: build plugin status array
------------------------------------------------------------------ */

/**
 * Return activation status for each plugin file.
 *
 * @param  array<int, array{file:string}> $plugins
 * @return array<string, 'active'|'inactive'|'not_installed'>
 */
function fhp_get_plugin_statuses( array $plugins ): array {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $statuses = [];
    foreach ( $plugins as $plugin ) {
        $file = $plugin['file'];
        if ( is_plugin_active( $file ) ) {
            $statuses[ $file ] = 'active';
        } elseif ( file_exists( WP_PLUGIN_DIR . '/' . $file ) ) {
            $statuses[ $file ] = 'inactive';
        } else {
            $statuses[ $file ] = 'not_installed';
        }
    }

    return $statuses;
}

/* ------------------------------------------------------------------
   Helper: render a coloured status badge with action link
------------------------------------------------------------------ */

/**
 * Build the HTML for a plugin's status cell.
 *
 * @param  string $status  'active' | 'inactive' | 'not_installed'
 * @param  string $slug    WordPress.org plugin slug
 * @param  string $file    plugin_dir/file.php
 * @return string  Safe HTML string (no user input, all values are
 *                 hard-coded in fhp_get_recommended_plugins()).
 */
function fhp_plugin_status_badge( string $status, string $slug, string $file ): string {
    switch ( $status ) {
        case 'active':
            return '<span style="color:#00a32a; font-weight:600;">✓ '
                . esc_html__( 'Active', 'fuadhasan-portfolio' )
                . '</span>';

        case 'inactive':
            $activate_url = wp_nonce_url(
                admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $file ) ),
                'activate-plugin_' . $file
            );
            return '<span style="color:#dba617; font-weight:600;">'
                . esc_html__( 'Inactive', 'fuadhasan-portfolio' )
                . '</span><br>'
                . '<a href="' . esc_url( $activate_url ) . '" style="font-size:12px;">'
                . esc_html__( 'Activate →', 'fuadhasan-portfolio' )
                . '</a>';

        case 'not_installed':
        default:
            $install_url = wp_nonce_url(
                admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $slug ) ),
                'install-plugin_' . $slug
            );
            return '<span style="color:#d63638; font-weight:600;">'
                . esc_html__( 'Not installed', 'fuadhasan-portfolio' )
                . '</span><br>'
                . '<a href="' . esc_url( $install_url ) . '" style="font-size:12px;">'
                . esc_html__( 'Install →', 'fuadhasan-portfolio' )
                . '</a>';
    }
}
