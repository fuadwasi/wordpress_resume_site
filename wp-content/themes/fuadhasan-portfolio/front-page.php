<?php
/**
 * Front Page Template — Portfolio homepage
 *
 * @package fuadhasan-portfolio
 */

get_header();

$name        = fhp_option( 'full_name',     'Fuad Hasan' );
$job_title   = fhp_option( 'job_title',     'Senior Software Engineer' );
$summary     = fhp_option( 'hero_summary',  'Building high-performance, scalable web applications with a passion for clean code and great user experiences.' );
$email       = fhp_option( 'contact_email', '' );
$linkedin    = fhp_option( 'linkedin_url',  '' );
$github      = fhp_option( 'github_url',    '' );
$cv_file     = fhp_option( 'cv_file',       '' );
?>

<!-- ========== HERO ========== -->
<section class="hero" id="home">
	<div class="container hero-content">
		<div class="hero-tag">👋 Available for hire</div>
		<h1><?php echo esc_html( $name ); ?></h1>
		<p class="hero-title"><?php echo esc_html( $job_title ); ?></p>
		<p class="hero-description"><?php echo esc_html( $summary ); ?></p>
		<div class="hero-actions">
			<?php if ( $cv_file ) : ?>
				<a href="<?php echo esc_url( $cv_file ); ?>" class="btn btn-primary" download>
					📄 Download CV
				</a>
			<?php else : ?>
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'download-cv' ) ) ?: '#' ); ?>" class="btn btn-primary">
					📄 Download CV
				</a>
			<?php endif; ?>
			<a href="#contact" class="btn btn-outline">Get in Touch</a>
		</div>
	</div>
</section>

<!-- ========== ABOUT ========== -->
<section class="section section-alt" id="about">
	<div class="container">
		<div class="section-header">
			<span class="section-label">About Me</span>
			<h2 class="section-title">Who I Am</h2>
		</div>
		<?php
		$about_page = get_page_by_path( 'about' );
		if ( $about_page ) {
			echo '<div class="about-content">' . wp_kses_post( apply_filters( 'the_content', $about_page->post_content ) ) . '</div>';
		} else {
			echo '<p class="section-subtitle">' . esc_html( $summary ) . '</p>';
		}
		?>
	</div>
</section>

<!-- ========== EXPERIENCE ========== -->
<section class="section" id="experience">
	<div class="container">
		<div class="section-header">
			<span class="section-label">Career</span>
			<h2 class="section-title">Work Experience</h2>
			<p class="section-subtitle">My professional journey and key contributions.</p>
		</div>
		<?php
		$experiences = fhp_get_posts( 'fhp_experience' );
		if ( $experiences ) :
		?>
		<div class="timeline">
			<?php foreach ( $experiences as $exp ) :
				$company  = function_exists( 'get_field' ) ? get_field( 'company_name', $exp->ID ) : '';
				$role     = function_exists( 'get_field' ) ? get_field( 'role_title',   $exp->ID ) : '';
				$start    = function_exists( 'get_field' ) ? get_field( 'start_date',   $exp->ID ) : '';
				$end      = function_exists( 'get_field' ) ? get_field( 'end_date',     $exp->ID ) : '';
				$current  = function_exists( 'get_field' ) ? get_field( 'current_role', $exp->ID ) : false;
				$tech     = function_exists( 'get_field' ) ? get_field( 'tech_stack',   $exp->ID ) : '';
			?>
			<article class="card experience-card">
				<div class="exp-header">
					<div>
						<h3 class="exp-role"><?php echo esc_html( $role ?: $exp->post_title ); ?></h3>
						<p class="exp-company"><?php echo esc_html( $company ); ?></p>
					</div>
					<span class="exp-period">
						<?php
						echo esc_html( $start );
						echo $current ? ' – Present' : ( $end ? ' – ' . esc_html( $end ) : '' );
						?>
					</span>
				</div>
				<?php if ( $exp->post_content ) : ?>
					<div class="exp-description"><?php echo wp_kses_post( apply_filters( 'the_content', $exp->post_content ) ); ?></div>
				<?php endif; ?>
				<?php if ( $tech ) : ?>
					<div class="exp-tech"><?php echo fhp_tech_badges( $tech ); // phpcs:ignore ?></div>
				<?php endif; ?>
			</article>
			<?php endforeach; ?>
		</div>
		<?php else : ?>
			<p class="no-content">Experience entries coming soon.</p>
		<?php endif; ?>
	</div>
</section>

<!-- ========== PROJECTS ========== -->
<section class="section section-alt" id="projects">
	<div class="container">
		<div class="section-header">
			<span class="section-label">Work</span>
			<h2 class="section-title">Featured Projects</h2>
			<p class="section-subtitle">A selection of projects I've built or contributed to.</p>
		</div>
		<?php echo do_shortcode( '[fhp_projects limit="6" featured="yes"]' ); ?>
	</div>
</section>

<!-- ========== SKILLS ========== -->
<section class="section" id="skills">
	<div class="container">
		<div class="section-header">
			<span class="section-label">Expertise</span>
			<h2 class="section-title">Skills & Technologies</h2>
			<p class="section-subtitle">Technologies and tools I work with.</p>
		</div>
		<?php echo do_shortcode( '[fhp_skills]' ); ?>
	</div>
</section>

<!-- ========== CONTACT ========== -->
<section class="section section-alt" id="contact">
	<div class="container">
		<div class="section-header">
			<span class="section-label">Get In Touch</span>
			<h2 class="section-title">Contact Me</h2>
			<p class="section-subtitle">Feel free to reach out for opportunities or just a chat.</p>
		</div>
		<div class="contact-links">
			<?php if ( $email ) : ?>
				<a href="mailto:<?php echo esc_attr( $email ); ?>" class="btn btn-primary">✉ Email Me</a>
			<?php endif; ?>
			<?php if ( $linkedin ) : ?>
				<a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline">LinkedIn</a>
			<?php endif; ?>
			<?php if ( $github ) : ?>
				<a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline">GitHub</a>
			<?php endif; ?>
		</div>
		<?php
		// Render WPForms/CF7 contact form if present
		$contact_page = get_page_by_path( 'contact' );
		if ( $contact_page && has_shortcode( $contact_page->post_content, 'wpforms' ) ) {
			echo do_shortcode( $contact_page->post_content );
		}
		?>
	</div>
</section>

<footer class="site-footer" role="contentinfo">
	<div class="container">
		<p>
			&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
			<a href="<?php echo esc_url( home_url() ); ?>"><?php echo esc_html( $name ); ?></a>.
			<?php esc_html_e( 'Built with WordPress.', 'fuadhasan-portfolio' ); ?>
		</p>
	</div>
</footer>

<?php get_footer(); ?>
