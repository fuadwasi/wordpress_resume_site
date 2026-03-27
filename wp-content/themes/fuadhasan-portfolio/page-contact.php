<?php
/**
 * Contact Page Template — /contact
 *
 * Template Name: Contact
 *
 * Displays contact info from ACF options + a contact form
 * rendered via a WPForms or Contact Form 7 shortcode.
 *
 * To connect a form:
 *   1. Create a form in WPForms or Contact Form 7.
 *   2. Copy its shortcode (e.g. [wpforms id="5"]).
 *   3. Paste it in the page body via the WordPress editor.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;
get_header();

$email   = fhp_option( 'email_address', 'fhassanwasi@gmail.com' );
$phone   = fhp_option( 'phone_number',  '+880 01792 478 378' );
$location = fhp_option( 'location',     'Dhaka, Bangladesh' );
$github  = fhp_option( 'github_url',   'https://github.com/fuadwasi' );
$linkedin = fhp_option( 'linkedin_url', 'https://www.linkedin.com/in/fuadwasi/' );
?>

<section class="section section--dark contact-page" aria-label="<?php esc_attr_e( 'Contact', 'fuadhasan-portfolio' ); ?>">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'Get In Touch', 'fuadhasan-portfolio' ),
            'heading' => __( 'Contact Me',   'fuadhasan-portfolio' ),
            'subheading' => __( "I'm open to new opportunities, collaborations, and interesting conversations.", 'fuadhasan-portfolio' ),
        ] ); ?>

        <div class="contact-page__grid">

            <!-- Contact info column -->
            <div class="contact-page__info">
                <ul class="contact-info-list">
                    <?php if ( $email ) : ?>
                    <li class="contact-info-list__item">
                        <span class="contact-info-list__icon" aria-hidden="true">✉</span>
                        <a href="<?php echo esc_url( fhp_get_email_link() ); ?>">
                            <?php echo esc_html( $email ); ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ( $phone ) : ?>
                    <li class="contact-info-list__item">
                        <span class="contact-info-list__icon" aria-hidden="true">📞</span>
                        <a href="tel:<?php echo esc_attr( preg_replace( '/\s/', '', $phone ) ); ?>">
                            <?php echo esc_html( $phone ); ?>
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ( $location ) : ?>
                    <li class="contact-info-list__item">
                        <span class="contact-info-list__icon" aria-hidden="true">📍</span>
                        <span><?php echo esc_html( $location ); ?></span>
                    </li>
                    <?php endif; ?>

                    <?php if ( $github ) : ?>
                    <li class="contact-info-list__item">
                        <span class="contact-info-list__icon" aria-hidden="true">⌥</span>
                        <a href="<?php echo esc_url( $github ); ?>" target="_blank" rel="noopener noreferrer">
                            GitHub — fuadwasi
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php if ( $linkedin ) : ?>
                    <li class="contact-info-list__item">
                        <span class="contact-info-list__icon" aria-hidden="true">in</span>
                        <a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">
                            LinkedIn — fuadwasi
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>

                <!-- Download CV CTA -->
                <div style="margin-top:2rem;">
                    <a href="<?php echo esc_url( fhp_get_cv_url() ); ?>" class="btn btn--primary">
                        <?php echo esc_html( fhp_option( 'cv_download_label', __( 'Download CV', 'fuadhasan-portfolio' ) ) ); ?>
                    </a>
                </div>
            </div>

            <!-- Contact form column -->
            <div class="contact-page__form card">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php
                    $content = get_the_content();
                    if ( $content ) :
                        echo wp_kses_post( apply_filters( 'the_content', $content ) );
                    else :
                    ?>
                        <p class="text-muted">
                            <?php esc_html_e( 'To enable the contact form: edit this page in the WordPress admin and paste your WPForms or Contact Form 7 shortcode.', 'fuadhasan-portfolio' ); ?>
                        </p>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

        </div><!-- .contact-page__grid -->
    </div>
</section>

<?php get_footer();
