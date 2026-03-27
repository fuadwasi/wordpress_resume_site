<?php
/**
 * Shortcodes
 *
 * @package fuadhasan-portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * [fhp_projects] — renders featured projects grid.
 */
function fhp_shortcode_projects( array $atts ): string {
	$atts  = shortcode_atts( [ 'limit' => 6, 'featured' => 'no' ], $atts );
	$args  = [
		'post_type'      => 'fhp_project',
		'posts_per_page' => (int) $atts['limit'],
		'post_status'    => 'publish',
	];
	if ( 'yes' === $atts['featured'] && function_exists( 'get_field' ) ) {
		$args['meta_query'] = [ [ 'key' => 'is_featured', 'value' => '1' ] ];
	}
	$posts = get_posts( $args );
	if ( empty( $posts ) ) {
		return '<p class="no-content">' . esc_html__( 'No projects found.', 'fuadhasan-portfolio' ) . '</p>';
	}
	ob_start();
	echo '<div class="grid-2 projects-grid">';
	foreach ( $posts as $post ) {
		$github = function_exists( 'get_field' ) ? get_field( 'github_url', $post->ID ) : '';
		$live   = function_exists( 'get_field' ) ? get_field( 'live_url',   $post->ID ) : '';
		$tech   = function_exists( 'get_field' ) ? get_field( 'technologies', $post->ID ) : '';
		?>
		<article class="card project-card">
			<h3 class="project-title"><?php echo esc_html( $post->post_title ); ?></h3>
			<div class="project-excerpt"><?php echo wp_kses_post( wp_trim_words( $post->post_content, 25 ) ); ?></div>
			<?php if ( $tech ) : ?>
				<div class="project-badges"><?php echo fhp_tech_badges( $tech ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
			<?php endif; ?>
			<div class="project-links">
				<?php if ( $github ) : ?>
					<a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">GitHub</a>
				<?php endif; ?>
				<?php if ( $live ) : ?>
					<a href="<?php echo esc_url( $live ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">Live Demo</a>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}
	echo '</div>';
	return ob_get_clean();
}
add_shortcode( 'fhp_projects', 'fhp_shortcode_projects' );

/**
 * [fhp_skills] — renders skills grouped by category.
 */
function fhp_shortcode_skills( array $atts ): string {
	$posts = get_posts( [ 'post_type' => 'fhp_skill', 'posts_per_page' => -1, 'post_status' => 'publish' ] );
	if ( empty( $posts ) ) {
		return '<p class="no-content">' . esc_html__( 'No skills found.', 'fuadhasan-portfolio' ) . '</p>';
	}
	$grouped = [];
	foreach ( $posts as $post ) {
		$cat = function_exists( 'get_field' ) ? get_field( 'skill_category', $post->ID ) : 'General';
		$cat = $cat ?: 'General';
		$grouped[ $cat ][] = $post;
	}
	ob_start();
	foreach ( $grouped as $category => $skills ) : ?>
		<div class="skill-group">
			<h3 class="skill-category"><?php echo esc_html( $category ); ?></h3>
			<div class="skill-list">
				<?php foreach ( $skills as $skill ) : ?>
					<span class="badge skill-badge"><?php echo esc_html( $skill->post_title ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach;
	return ob_get_clean();
}
add_shortcode( 'fhp_skills', 'fhp_shortcode_skills' );
