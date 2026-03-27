<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" role="banner">
	<div class="container nav-inner">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			$name = fhp_option( 'full_name', get_bloginfo( 'name' ) );
			$parts = explode( ' ', trim( $name ) );
			$last  = array_pop( $parts );
			echo esc_html( implode( ' ', $parts ) ) . ' <span>' . esc_html( $last ) . '</span>';
			?>
		</a>

		<nav class="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'fuadhasan-portfolio' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'fallback_cb'    => function() {
					echo '<ul>';
					$pages = [ 'About' => '#about', 'Experience' => '#experience', 'Projects' => '#projects', 'Skills' => '#skills', 'Contact' => '#contact' ];
					foreach ( $pages as $label => $href ) {
						echo '<li><a href="' . esc_attr( $href ) . '">' . esc_html( $label ) . '</a></li>';
					}
					echo '</ul>';
				},
			] );
			?>
		</nav>
	</div>
</header>
