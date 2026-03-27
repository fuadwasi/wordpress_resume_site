<?php get_header(); ?>

<main class="site-main container" style="padding: 4rem 1.5rem;">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h1><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
			<?php
		endwhile;
	else :
		?>
		<p><?php esc_html_e( 'No content found.', 'fuadhasan-portfolio' ); ?></p>
		<?php
	endif;
	?>
</main>

<?php get_footer(); ?>
