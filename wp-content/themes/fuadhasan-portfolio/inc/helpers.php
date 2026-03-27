<?php
/**
 * Template helper functions
 *
 * @package fuadhasan-portfolio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Get ACF option or return fallback.
 */
function fhp_option( string $key, string $fallback = '' ): string {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, 'option' );
		return $value ?: $fallback;
	}
	return $fallback;
}

/**
 * Render a comma-separated tech list as badge spans.
 */
function fhp_tech_badges( string $tech_string ): string {
	if ( empty( $tech_string ) ) {
		return '';
	}
	$items  = array_map( 'trim', explode( ',', $tech_string ) );
	$output = '';
	foreach ( $items as $item ) {
		if ( $item ) {
			$output .= '<span class="badge">' . esc_html( $item ) . '</span>';
		}
	}
	return $output;
}

/**
 * Returns posts of a given CPT type.
 *
 * @param string $post_type  CPT slug.
 * @param int    $limit      -1 for all.
 * @param string $meta_key   Optional meta key to order by.
 */
function fhp_get_posts( string $post_type, int $limit = -1, string $meta_key = '' ): array {
	$args = [
		'post_type'      => $post_type,
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'orderby'        => $meta_key ? 'meta_value' : 'menu_order',
		'order'          => 'ASC',
	];
	if ( $meta_key ) {
		$args['meta_key'] = $meta_key;
	}
	return get_posts( $args );
}
