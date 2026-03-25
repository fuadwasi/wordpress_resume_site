<?php
/**
 * Projects Archive Template — /projects
 *
 * Renders all projects as a filterable card grid.
 * JavaScript in main.js handles the client-side filtering by
 * project type using data-type attributes on each card.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$projects_query = fhp_get_all_projects();
?>

<section class="section section--dark projects-archive">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Portfolio',   'fuadhasan-portfolio' ),
            'heading' => __( 'Projects',    'fuadhasan-portfolio' ),
            'subheading' => __( 'B2B eCommerce, microservices, POS systems, NopCommerce plugins, and more.', 'fuadhasan-portfolio' ),
        ] ); ?>

        <!-- Project type filter buttons -->
        <div class="projects-filter" role="group" aria-label="<?php esc_attr_e( 'Filter projects by type', 'fuadhasan-portfolio' ); ?>">
            <button class="btn btn--outline btn--sm projects-filter__btn is-active" data-filter="all">
                <?php esc_html_e( 'All', 'fuadhasan-portfolio' ); ?>
            </button>
            <?php
            /*
             * Build the filter list using fhp_field() so the returned value
             * matches the ACF return_format (label), which is also what
             * project-card.php stores in data-type via sanitize_title().
             * Using get_post_meta() directly would return the raw key
             * (e.g. "b2c_ecommerce") while project-card.php serialises the
             * display label (e.g. "b2c-ecommerce") — causing filter mismatches.
             */
            $types = [];
            if ( $projects_query->have_posts() ) {
                foreach ( $projects_query->posts as $p ) {
                    $t = fhp_field( 'project_type', $p->ID );
                    if ( $t && ! in_array( $t, $types, true ) ) {
                        $types[] = $t;
                    }
                }
            }
            foreach ( $types as $type ) :
            ?>
            <button class="btn btn--outline btn--sm projects-filter__btn"
                    data-filter="<?php echo esc_attr( sanitize_title( $type ) ); ?>">
                <?php echo esc_html( $type ); ?>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Projects grid -->
        <?php if ( $projects_query->have_posts() ) : ?>
            <div class="projects-grid grid-3" id="projects-grid">
                <?php while ( $projects_query->have_posts() ) : $projects_query->the_post(); ?>
                    <?php get_template_part( 'template-parts/project/project-card' ); ?>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php else : ?>
            <p class="text-muted"><?php esc_html_e( 'No projects found. Add them from the WordPress admin.', 'fuadhasan-portfolio' ); ?></p>
        <?php endif; ?>

    </div>
</section>

<?php get_footer();
