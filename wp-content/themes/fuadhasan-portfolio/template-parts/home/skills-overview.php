<?php
/**
 * Template Part: Skills Overview (Home Page)
 *
 * A compact snapshot of top skills grouped by category.
 * Full list is on /skills.
 *
 * @package FuadHasanPortfolio
 */
defined( 'ABSPATH' ) || exit;

// Show at most 5 skills per category on the home page overview
$skills_by_cat = fhp_get_skills_by_category();
?>

<section class="section section--dark skills-overview" aria-label="<?php esc_attr_e( 'Skills Overview', 'fuadhasan-portfolio' ); ?>">
    <div class="container">

        <?php get_template_part( 'template-parts/shared/section-header', null, [
            'label'   => __( 'What I Work With', 'fuadhasan-portfolio' ),
            'heading' => __( 'Skills & Technologies', 'fuadhasan-portfolio' ),
        ] ); ?>

        <?php if ( ! empty( $skills_by_cat ) ) : ?>
            <div class="skills-overview__grid grid-3">
                <?php foreach ( $skills_by_cat as $category => $skills ) :
                    $preview = array_slice( $skills, 0, 5 );
                ?>
                <div class="card skills-overview__card">
                    <h3 class="skills-overview__cat-title"><?php echo esc_html( $category ); ?></h3>
                    <div class="tag-list">
                        <?php foreach ( $preview as $skill ) : ?>
                            <span class="tag"><?php echo esc_html( get_the_title( $skill->ID ) ); ?></span>
                        <?php endforeach; ?>
                        <?php if ( count( $skills ) > 5 ) : ?>
                            <span class="tag tag--cyan">+<?php echo esc_html( count( $skills ) - 5 ); ?> <?php esc_html_e( 'more', 'fuadhasan-portfolio' ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center" style="margin-top:2rem;">
                <a href="<?php echo esc_url( home_url( '/skills' ) ); ?>" class="btn btn--outline">
                    <?php esc_html_e( 'View All Skills →', 'fuadhasan-portfolio' ); ?>
                </a>
            </div>
        <?php else : ?>
            <!-- Fallback static tags when no skills CPT entries exist yet -->
            <div class="tag-list" style="justify-content:center;">
                <?php
                $static_skills = [
                    'ASP.NET Core','C#','NopCommerce','gRPC','RabbitMQ','MongoDB',
                    'Angular','Next.js','MSSQL','Azure DevOps','REST APIs','Microservices',
                    'SAP ERP','ZohoCRM','TaxJar','PHP','WordPress',
                ];
                foreach ( $static_skills as $s ) :
                ?>
                <span class="tag"><?php echo esc_html( $s ); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
