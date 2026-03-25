<?php
/**
 * Sidebar template (optional).
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
<aside class="sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'fuadhasan-portfolio' ); ?>">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
<?php endif;
