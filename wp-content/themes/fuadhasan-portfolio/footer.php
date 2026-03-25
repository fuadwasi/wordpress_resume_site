</main><!-- #main-content -->

<!-- ================================================================
     SITE FOOTER
     ================================================================ -->
<footer class="site-footer" role="contentinfo">
    <div class="container site-footer__inner">

        <!-- Brand -->
        <div class="site-footer__brand">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-footer__name" rel="home">
                <?php echo esc_html( fhp_option( 'hero_name', get_bloginfo( 'name' ) ) ); ?>
            </a>
            <p class="site-footer__tagline">
                <?php fhp_the_option( 'footer_tagline', __( 'Building scalable eCommerce solutions, one commit at a time.', 'fuadhasan-portfolio' ) ); ?>
            </p>
        </div>

        <!-- Footer Navigation -->
        <nav class="site-footer__nav" aria-label="<?php esc_attr_e( 'Footer Navigation', 'fuadhasan-portfolio' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'footer',
                'menu_class'     => 'site-footer__nav-list',
                'container'      => false,
                'fallback_cb'    => 'fhp_fallback_footer_nav',
                'depth'          => 1,
            ] );
            ?>
        </nav>

        <!-- Social Links -->
        <div class="site-footer__social">
            <?php
            $social_links = [
                [
                    'url'   => fhp_option( 'github_url',    'https://github.com/fuadwasi' ),
                    'label' => 'GitHub',
                    'icon'  => 'github',
                ],
                [
                    'url'   => fhp_option( 'linkedin_url',  'https://www.linkedin.com/in/fuadwasi/' ),
                    'label' => 'LinkedIn',
                    'icon'  => 'linkedin',
                ],
                [
                    'url'   => fhp_get_email_link(),
                    'label' => 'Email',
                    'icon'  => 'email',
                ],
                [
                    'url'   => fhp_option( 'codeforces_url', 'https://codeforces.com/profile/fhwasi' ),
                    'label' => 'Codeforces',
                    'icon'  => 'code',
                ],
            ];

            foreach ( $social_links as $link ) :
                if ( empty( $link['url'] ) ) continue;
            ?>
            <a href="<?php echo esc_url( $link['url'] ); ?>"
               class="site-footer__social-link"
               target="_blank"
               rel="noopener noreferrer"
               aria-label="<?php echo esc_attr( $link['label'] ); ?>">
                <?php fhp_icon( $link['icon'], 'site-footer__social-icon' ); ?>
                <span class="sr-only"><?php echo esc_html( $link['label'] ); ?></span>
            </a>
            <?php endforeach; ?>
        </div>

    </div><!-- .site-footer__inner -->

    <!-- Bottom bar -->
    <div class="site-footer__bottom">
        <div class="container">
            <p class="site-footer__copy">
                &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
                <?php echo esc_html( fhp_option( 'hero_name', get_bloginfo( 'name' ) ) ); ?>.
                <?php esc_html_e( 'All rights reserved.', 'fuadhasan-portfolio' ); ?>
            </p>
            <p class="site-footer__built">
                <?php esc_html_e( 'Built with', 'fuadhasan-portfolio' ); ?>
                <a href="https://wordpress.org" target="_blank" rel="noopener">WordPress</a>
                &amp; <span class="text-accent">♥</span>
            </p>
        </div>
    </div>

</footer><!-- .site-footer -->

<?php wp_footer(); ?>
</body>
</html>
<?php
/**
 * Fallback footer navigation — mirrors primary nav.
 */
function fhp_fallback_footer_nav() {
    $pages = [
        home_url( '/about' )        => __( 'About',        'fuadhasan-portfolio' ),
        home_url( '/experience' )   => __( 'Experience',   'fuadhasan-portfolio' ),
        home_url( '/projects' )     => __( 'Projects',     'fuadhasan-portfolio' ),
        home_url( '/skills' )       => __( 'Skills',       'fuadhasan-portfolio' ),
        home_url( '/achievements' ) => __( 'Achievements', 'fuadhasan-portfolio' ),
        home_url( '/contact' )      => __( 'Contact',      'fuadhasan-portfolio' ),
    ];
    echo '<ul class="site-footer__nav-list">';
    foreach ( $pages as $url => $label ) {
        echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
    }
    echo '</ul>';
}
