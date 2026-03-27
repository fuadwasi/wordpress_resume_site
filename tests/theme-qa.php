<?php
/**
 * Theme Static QA Tests
 *
 * A lightweight, zero-dependency PHP test runner that validates the
 * fuadhasan-portfolio theme against WCAG 2.1 AA accessibility rules,
 * security escaping requirements, and structural completeness.
 *
 * Run from the repository root:
 *   php tests/theme-qa.php
 *
 * Exit code 0 = all tests passed.
 * Exit code 1 = one or more tests failed.
 *
 * @package FuadHasanPortfolio
 */

define( 'FHP_THEME_DIR', __DIR__ . '/../wp-content/themes/fuadhasan-portfolio' );
define( 'FHP_TESTS_PASS', "\033[32m✔ PASS\033[0m" );
define( 'FHP_TESTS_FAIL', "\033[31m✘ FAIL\033[0m" );
define( 'FHP_TESTS_WARN', "\033[33m⚠ WARN\033[0m" );
define( 'FHP_TESTS_INFO', "\033[36mℹ INFO\033[0m" );

$results = [ 'pass' => 0, 'fail' => 0, 'warn' => 0 ];

/* ==============================================================
   HELPER FUNCTIONS
============================================================== */

/**
 * Read a file relative to the theme directory.
 */
function fhp_read( string $relative_path ): string {
    $path = FHP_THEME_DIR . '/' . ltrim( $relative_path, '/' );
    return file_exists( $path ) ? (string) file_get_contents( $path ) : '';
}

/**
 * Assert that a condition is true.
 */
function assert_true( string $test_id, string $description, bool $condition, array &$results, bool $warn_only = false ): void {
    if ( $condition ) {
        echo FHP_TESTS_PASS . " [{$test_id}] {$description}\n";
        $results['pass']++;
    } elseif ( $warn_only ) {
        echo FHP_TESTS_WARN . " [{$test_id}] {$description}\n";
        $results['warn']++;
    } else {
        echo FHP_TESTS_FAIL . " [{$test_id}] {$description}\n";
        $results['fail']++;
    }
}

/**
 * Assert that a pattern does NOT appear in file content.
 */
function assert_not_contains( string $test_id, string $description, string $content, string $pattern, array &$results ): void {
    $found = ( preg_match( $pattern, $content ) === 1 );
    assert_true( $test_id, $description, ! $found, $results );
}

/**
 * Assert that a pattern DOES appear in file content.
 */
function assert_contains( string $test_id, string $description, string $content, string $pattern, array &$results, bool $warn_only = false ): void {
    $found = ( preg_match( $pattern, $content ) === 1 );
    assert_true( $test_id, $description, $found, $results, $warn_only );
}

/* ==============================================================
   TEST GROUP 1: FILE EXISTENCE
   All required template and inc/ files must be present.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 1: Required Files Exist\n";
echo str_repeat( '=', 60 ) . "\n";

$required_files = [
    'style.css',
    'functions.php',
    'header.php',
    'footer.php',
    'front-page.php',
    'index.php',
    'page.php',
    'page-about.php',
    'page-skills.php',
    'page-achievements.php',
    'page-contact.php',
    'page-download-cv.php',
    'archive-experience.php',
    'archive-project.php',
    'single-experience.php',
    'single-project.php',
    '404.php',
    'template-parts/home/hero.php',
    'template-parts/home/summary.php',
    'template-parts/home/skills-overview.php',
    'template-parts/home/achievements-bar.php',
    'template-parts/home/featured-projects.php',
    'template-parts/experience/timeline-item.php',
    'template-parts/project/project-card.php',
    'template-parts/skill/skill-group.php',
    'template-parts/shared/section-header.php',
    'template-parts/shared/cta-buttons.php',
    'inc/helpers.php',
    'inc/custom-post-types.php',
    'inc/acf-options.php',
    'inc/acf-fields.php',
    'inc/enqueue.php',
    'inc/setup.php',
    'inc/cv-protection.php',
    'inc/cv-download.php',
    'inc/seeder.php',
    'inc/admin-ui.php',
    'inc/plugin-compat.php',
    'inc/recommended-plugins.php',
    'inc/seo.php',
    'inc/performance.php',
    'assets/css/main.css',
    'assets/css/hero.css',
    'assets/css/timeline.css',
    'assets/css/responsive.css',
    'assets/css/admin.css',
    'assets/js/main.js',
    'assets/js/smooth-scroll.js',
    'assets/js/skills-animation.js',
];

foreach ( $required_files as $file ) {
    $path   = FHP_THEME_DIR . '/' . $file;
    $exists = file_exists( $path ) && filesize( $path ) > 0;
    assert_true( 'F-' . str_pad( (string) ( array_search( $file, $required_files ) + 1 ), 2, '0', STR_PAD_LEFT ),
        "File exists and non-empty: {$file}", $exists, $results );
}

/* ==============================================================
   TEST GROUP 2: PHP SYNTAX VALIDATION
   Every PHP file must have valid syntax.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 2: PHP Syntax Validation\n";
echo str_repeat( '=', 60 ) . "\n";

$php_files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator( FHP_THEME_DIR, FilesystemIterator::SKIP_DOTS )
);

$syntax_errors = 0;
$syntax_checked = 0;
foreach ( $php_files as $file ) {
    if ( $file->getExtension() !== 'php' ) {
        continue;
    }
    $syntax_checked++;
    $output     = [];
    $return_code = 0;
    exec( 'php -l ' . escapeshellarg( $file->getPathname() ) . ' 2>&1', $output, $return_code );
    if ( $return_code !== 0 ) {
        echo FHP_TESTS_FAIL . " [SYN] PHP syntax error in: " . str_replace( FHP_THEME_DIR . '/', '', $file->getPathname() ) . "\n";
        echo "       " . implode( "\n       ", $output ) . "\n";
        $syntax_errors++;
        $results['fail']++;
    }
}

if ( $syntax_errors === 0 ) {
    echo FHP_TESTS_PASS . " [SYN] All {$syntax_checked} PHP files pass syntax check\n";
    $results['pass']++;
} else {
    echo FHP_TESTS_FAIL . " [SYN] {$syntax_errors} PHP file(s) have syntax errors\n";
    $results['fail']++;
}

/* ==============================================================
   TEST GROUP 3: SECURITY — OUTPUT ESCAPING
   All echo statements must use an escaping function.
   We flag bare `echo $var` patterns (excluding comments).
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 3: Security — Output Escaping\n";
echo str_repeat( '=', 60 ) . "\n";

$security_files = glob( FHP_THEME_DIR . '/**/*.php', GLOB_BRACE ) ?: [];
// Add root-level php files
$security_files = array_merge(
    $security_files,
    glob( FHP_THEME_DIR . '/*.php' ) ?: [],
    glob( FHP_THEME_DIR . '/template-parts/**/*.php' ) ?: [],
    glob( FHP_THEME_DIR . '/template-parts/*.php' ) ?: []
);
$security_files = array_unique( $security_files );

// Pattern: echo + optional whitespace + $ (unescaped variable), excluding phpcs:ignore lines
$bare_echo_pattern = '/^\s*echo\s+\$(?!_)/m';

$security_violations = [];
foreach ( $security_files as $filepath ) {
    $content = file_get_contents( $filepath );
    // Strip lines with phpcs:ignore
    $lines   = explode( "\n", $content );
    $filtered = [];
    foreach ( $lines as $line ) {
        if ( strpos( $line, 'phpcs:ignore' ) === false ) {
            $filtered[] = $line;
        }
    }
    $clean = implode( "\n", $filtered );

    if ( preg_match( $bare_echo_pattern, $clean ) ) {
        $rel = str_replace( FHP_THEME_DIR . '/', '', $filepath );
        $security_violations[] = $rel;
    }
}

if ( empty( $security_violations ) ) {
    echo FHP_TESTS_PASS . " [SEC-1] No bare unescaped echo \$var detected in templates\n";
    $results['pass']++;
} else {
    foreach ( $security_violations as $vf ) {
        echo FHP_TESTS_FAIL . " [SEC-1] Possible unescaped output in: {$vf}\n";
        $results['fail']++;
    }
}

// Check that no raw SQL SELECT/INSERT is used
$raw_sql_pattern = '/\$(?:wpdb|WPDB|db)\s*->\s*(?:get_results|get_row|get_var|query)\s*\(\s*["\']SELECT/i';
$sql_violations  = [];
foreach ( $security_files as $filepath ) {
    $content = file_get_contents( $filepath );
    if ( preg_match( $raw_sql_pattern, $content ) ) {
        $rel = str_replace( FHP_THEME_DIR . '/', '', $filepath );
        // Allow if there's prepare() call
        if ( strpos( $content, '->prepare(' ) === false ) {
            $sql_violations[] = $rel;
        }
    }
}

assert_true( 'SEC-2', 'No raw un-prepared SQL queries detected', empty( $sql_violations ), $results );

// Check cv-download.php has X-Content-Type-Options header
$cv_download = fhp_read( 'inc/cv-download.php' );
assert_contains( 'SEC-3', 'CV download sets X-Content-Type-Options: nosniff header', $cv_download, '/X-Content-Type-Options/', $results );

// Check cv-download.php validates MIME type
assert_contains( 'SEC-4', 'CV download validates PDF MIME type', $cv_download, '/application\/pdf/', $results );

/* ==============================================================
   TEST GROUP 4: ACCESSIBILITY (WCAG 2.1 AA)
   Static checks on PHP template output patterns.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 4: Accessibility — WCAG 2.1 AA\n";
echo str_repeat( '=', 60 ) . "\n";

// 4.1 Skip link present in header
$header = fhp_read( 'header.php' );
assert_contains( 'A11Y-1', 'Skip navigation link present in header.php', $header, '/skip-link/', $results );

// 4.2 Main landmark has id="main-content" for skip link target
assert_contains( 'A11Y-2', 'Main landmark has id="main-content"', $header, '/id=["\']main-content["\']/', $results );

// 4.3 Primary nav has aria-label
assert_contains( 'A11Y-3', 'Primary <nav> has aria-label', $header, '/<nav[^>]+aria-label/', $results );

// 4.4 Hamburger button has aria-expanded and aria-controls
assert_contains( 'A11Y-4', 'Hamburger button has aria-expanded attribute', $header, '/aria-expanded/', $results );
assert_contains( 'A11Y-5', 'Hamburger button has aria-controls attribute', $header, '/aria-controls/', $results );

// 4.5 <html> element uses language_attributes()
assert_contains( 'A11Y-6', '<html> uses language_attributes() for lang attribute', $header, '/language_attributes\(\)/', $results );

// 4.6 footer has role="contentinfo"
$footer = fhp_read( 'footer.php' );
assert_contains( 'A11Y-7', '<footer> has role="contentinfo"', $footer, '/<footer[^>]+role=["\']contentinfo["\']/', $results );

// 4.7 Footer nav has aria-label
assert_contains( 'A11Y-8', 'Footer <nav> has aria-label', $footer, '/<nav[^>]+aria-label/', $results );

// 4.8 Back-to-top button has aria-label
assert_contains( 'A11Y-9', 'Back-to-top button has aria-label', $footer, '/back-to-top[^>]+aria-label|aria-label[^>]+back-to-top/', $results );

// 4.9 All <img> in template-parts must have alt attribute (decorative: alt="" with aria-hidden)
$template_files = array_merge(
    glob( FHP_THEME_DIR . '/template-parts/**/*.php' ) ?: [],
    glob( FHP_THEME_DIR . '/template-parts/*.php' ) ?: []
);
$img_no_alt = [];
foreach ( $template_files as $filepath ) {
    $lines = file( $filepath, FILE_IGNORE_NEW_LINES );
    foreach ( $lines as $ln_idx => $line ) {
        // Find lines that open an <img tag
        if ( ! preg_match( '/<img\b/i', $line ) ) {
            continue;
        }
        // Collect up to 6 subsequent lines to cover multi-line attributes
        $window = implode( ' ', array_slice( $lines, $ln_idx, 6 ) );
        if ( stripos( $window, 'alt=' ) === false ) {
            $rel = str_replace( FHP_THEME_DIR . '/', '', $filepath );
            $img_no_alt[] = $rel . ':' . ( $ln_idx + 1 ) . ' — ' . trim( $line );
        }
    }
}
assert_true( 'A11Y-10', 'All <img> tags in template-parts have alt attribute', empty( $img_no_alt ), $results );
if ( ! empty( $img_no_alt ) ) {
    foreach ( $img_no_alt as $violation ) {
        echo "         → " . FHP_TESTS_INFO . " {$violation}\n";
    }
}

// 4.10 Sections with primary content should have aria-label
$sections_needing_label = [
    'archive-experience.php' => '/aria-label=/',
    'page-skills.php'        => '/aria-label=/',
    'page-contact.php'       => '/aria-label=/',
    '404.php'                => '/aria-label=/',
    'archive-project.php'    => '/aria-label=/',
];
foreach ( $sections_needing_label as $file => $pattern ) {
    $content = fhp_read( $file );
    assert_contains( 'A11Y-11.' . pathinfo( $file, PATHINFO_FILENAME ), "Section in {$file} has aria-label", $content, $pattern, $results );
}

// 4.11 Project filter buttons have aria-pressed
$archive_project = fhp_read( 'archive-project.php' );
assert_contains( 'A11Y-12', 'Project filter buttons have aria-pressed attribute', $archive_project, '/aria-pressed=/', $results );

// 4.12 Live region for filter announcements
assert_contains( 'A11Y-13', 'Filter live region (aria-live="polite") present', $archive_project, '/aria-live=["\']polite["\']/', $results );

// 4.13 Skill progress bars have role="progressbar" + aria-valuenow
$skill_group = fhp_read( 'template-parts/skill/skill-group.php' );
assert_contains( 'A11Y-14', 'Skill bars have role="progressbar"', $skill_group, '/role=["\']progressbar["\']/', $results );
assert_contains( 'A11Y-15', 'Skill bars have aria-valuenow', $skill_group, '/aria-valuenow=/', $results );
assert_contains( 'A11Y-16', 'Skill bars have aria-label', $skill_group, '/aria-label=/', $results );

// 4.14 Hero section has aria-label
$hero = fhp_read( 'template-parts/home/hero.php' );
assert_contains( 'A11Y-17', 'Hero section has aria-label', $hero, '/aria-label=/', $results );

// 4.15 Hero animated title has aria-live for screen readers
assert_contains( 'A11Y-18', 'Hero animated title has aria-live', $hero, '/aria-live=/', $results );

// 4.16 Hero stats have accessible label
assert_contains( 'A11Y-19', 'Hero stats bar has aria-label', $hero, '/aria-label[^>]+Career stats|Career stats[^"]*aria-label/', $results );

// 4.17 sr-only class is defined in stylesheet
$style_css = fhp_read( 'style.css' );
assert_contains( 'A11Y-20', '.sr-only utility class defined in style.css', $style_css, '/\.sr-only\s*\{/', $results );

// 4.18 :focus-visible styles are defined
assert_contains( 'A11Y-21', ':focus-visible outline styles defined in style.css', $style_css, '/:focus-visible\s*\{/', $results );

/* ==============================================================
   TEST GROUP 5: PERFORMANCE CHECKS
   Static code patterns that affect front-end performance.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 5: Performance\n";
echo str_repeat( '=', 60 ) . "\n";

// 5.1 Images below hero use loading="lazy"
$img_lazy_files = [
    'template-parts/experience/timeline-item.php',
    'template-parts/project/project-card.php',
    'template-parts/skill/skill-group.php',
    'template-parts/home/achievements-bar.php',
];
foreach ( $img_lazy_files as $file ) {
    $content = fhp_read( $file );
    // If there's an <img> tag, it should have loading="lazy"
    if ( strpos( $content, '<img' ) !== false ) {
        assert_contains( 'PERF-1.' . pathinfo( $file, PATHINFO_FILENAME ), "Images in {$file} use loading=\"lazy\"", $content, '/loading=["\']lazy["\']/', $results );
    }
}

// 5.2 Hero image uses loading="eager" (above the fold)
$hero_content = fhp_read( 'template-parts/home/hero.php' );
assert_contains( 'PERF-2', 'Hero profile photo uses loading="eager"', $hero_content, '/loading=["\']eager["\']/', $results );

// 5.3 Scripts are enqueued in footer (3rd param = true in wp_enqueue_script)
$enqueue = fhp_read( 'inc/enqueue.php' );
assert_contains( 'PERF-3', 'Main JS enqueued in footer (in_footer=true)', $enqueue, '/wp_enqueue_script[^;]+true\s*\)/', $results );

// 5.4 wp_head() cleanup: wp_generator removed
$performance = fhp_read( 'inc/performance.php' );
assert_contains( 'PERF-4', 'WordPress generator tag removed in performance.php', $performance, '/wp_generator/', $results );

// 5.5 Resource hints (preconnect) for Google Fonts
assert_contains( 'PERF-5', 'Google Fonts preconnect resource hint present', $performance, '/preconnect.*fonts\.googleapis\.com|fonts\.googleapis\.com.*preconnect/', $results );

// 5.6 Cache-Control header on static assets
assert_contains( 'PERF-6', 'Cache-Control headers set in performance.php', $performance, '/Cache-Control/', $results );

/* ==============================================================
   TEST GROUP 6: SEO
   Validate SEO schema and meta output code.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 6: SEO\n";
echo str_repeat( '=', 60 ) . "\n";

$seo = fhp_read( 'inc/seo.php' );

// 6.1 Person schema
assert_contains( 'SEO-1', 'Person JSON-LD schema defined', $seo, '/@type.*Person|Person.*@type/', $results );

// 6.2 WebPage schema
assert_contains( 'SEO-2', 'WebPage JSON-LD schema defined', $seo, '/@type.*WebPage|WebPage.*@type/', $results );

// 6.3 Open Graph tags
assert_contains( 'SEO-3', 'Open Graph og:title meta tag output', $seo, '/og:title/', $results );
assert_contains( 'SEO-4', 'Open Graph og:description meta tag output', $seo, '/og:description/', $results );
assert_contains( 'SEO-5', 'Open Graph og:image meta tag output', $seo, '/og:image/', $results );

// 6.4 Twitter Card
assert_contains( 'SEO-6', 'Twitter Card meta tag output', $seo, '/twitter:card/', $results );

// 6.5 Plugin deference (no SEO output when Yoast/RankMath active)
assert_contains( 'SEO-7', 'SEO layer defers to active SEO plugin', $seo, '/fhp_seo_plugin_active\(\)/', $results );

// 6.6 Per-page document title filter
assert_contains( 'SEO-8', 'Per-page document title filter registered', $seo, '/pre_get_document_title/', $results );

/* ==============================================================
   TEST GROUP 7: CV DOWNLOAD
   Validate the CV download pipeline.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 7: CV Download Pipeline\n";
echo str_repeat( '=', 60 ) . "\n";

$cv_dl   = fhp_read( 'inc/cv-download.php' );
$cv_prot = fhp_read( 'inc/cv-protection.php' );

assert_contains( 'CV-1',  'CV rewrite rule registered', $cv_dl, '/add_rewrite_rule/', $results );
assert_contains( 'CV-2',  'CV download handler on template_redirect', $cv_dl, '/template_redirect/', $results );
assert_contains( 'CV-3',  'CV Content-Disposition: attachment header set', $cv_dl, '/Content-Disposition.*attachment/', $results );
assert_contains( 'CV-4',  'CV file MIME type validated', $cv_dl, '/application\/pdf/', $results );
assert_contains( 'CV-5',  'CV .htaccess protection created', $cv_prot, '/htaccess/', $results );
assert_contains( 'CV-6',  'CV download counter incremented', $cv_prot, '/fhp_cv_increment_download_count/', $results );
assert_contains( 'CV-7',  'CV download counter helper exposed', $cv_prot, '/fhp_cv_get_download_count/', $results );
assert_contains( 'CV-8',  'CV file existence checked before serving', $cv_dl, '/file_exists/', $results );
assert_contains( 'CV-9',  'CV path validation prevents directory traversal', $cv_dl, '/get_attached_file/', $results );
assert_contains( 'CV-10', 'CV download fires extensibility action hook', $cv_dl, '/do_action.*fhp_cv_downloaded/', $results );

/* ==============================================================
   TEST GROUP 8: THEME SETUP & ACTIVATION
   Validate the after_switch_theme hooks work correctly.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 8: Theme Setup & Activation\n";
echo str_repeat( '=', 60 ) . "\n";

$setup = fhp_read( 'inc/setup.php' );
assert_contains( 'SETUP-1', 'Permalink structure set on activation', $setup, '/permalink_structure|rewrite_structure/', $results );
assert_contains( 'SETUP-2', 'Required pages created on activation', $setup, '/fhp_create_required_pages/', $results );
assert_contains( 'SETUP-3', 'Admin notice registered for setup confirmation', $setup, '/fhp_setup_admin_notice/', $results );
assert_contains( 'SETUP-4', 'Setup is idempotent (guarded by option flag)', $setup, '/fhp_setup_version|fhp_defaults_applied/', $results );

$seeder = fhp_read( 'inc/seeder.php' );
assert_contains( 'SETUP-5', 'Content seeder has idempotency guard', $seeder, '/fhp_seeded/', $results );
assert_contains( 'SETUP-6', 'Experience CPT content seeded', $seeder, '/fhp_seed_experience/', $results );
assert_contains( 'SETUP-7', 'Projects CPT content seeded', $seeder, '/fhp_seed_projects/', $results );
assert_contains( 'SETUP-8', 'Skills CPT content seeded', $seeder, '/fhp_seed_skills/', $results );
assert_contains( 'SETUP-9', 'Achievements CPT content seeded', $seeder, '/fhp_seed_achievements/', $results );
assert_contains( 'SETUP-10', 'Options Page defaults seeded', $seeder, '/fhp_seed_site_options/', $results );

/* ==============================================================
   TEST GROUP 9: FUNCTIONS.PHP BOOTSTRAP
   Ensure all inc/ modules are loaded in the correct order.
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "GROUP 9: Bootstrap — functions.php\n";
echo str_repeat( '=', 60 ) . "\n";

$functions = fhp_read( 'functions.php' );
$inc_modules = [
    'helpers.php',
    'custom-post-types.php',
    'acf-options.php',
    'acf-fields.php',
    'enqueue.php',
    'setup.php',
    'cv-protection.php',
    'cv-download.php',
    'seeder.php',
    'admin-ui.php',
    'plugin-compat.php',
    'recommended-plugins.php',
    'seo.php',
    'performance.php',
];

foreach ( $inc_modules as $module ) {
    assert_contains( 'BOOT-' . str_pad( (string) ( array_search( $module, $inc_modules ) + 1 ), 2, '0', STR_PAD_LEFT ),
        "inc/{$module} listed in functions.php \$fhp_includes",
        $functions, '/' . preg_quote( $module, '/' ) . '/', $results );
}

// Version constant
assert_contains( 'BOOT-15', 'FHP_VERSION constant defined', $functions, '/FHP_VERSION/', $results );
assert_contains( 'BOOT-16', 'FHP_DIR constant defined', $functions, '/FHP_DIR/', $results );
assert_contains( 'BOOT-17', 'FHP_ASSETS constant defined', $functions, '/FHP_ASSETS/', $results );

/* ==============================================================
   SUMMARY
============================================================== */
echo "\n" . str_repeat( '=', 60 ) . "\n";
echo "RESULTS SUMMARY\n";
echo str_repeat( '=', 60 ) . "\n";

$total = $results['pass'] + $results['fail'] + $results['warn'];
echo FHP_TESTS_PASS . "  Passed  : {$results['pass']}\n";
echo FHP_TESTS_FAIL . "  Failed  : {$results['fail']}\n";
echo FHP_TESTS_WARN . "  Warnings: {$results['warn']}\n";
echo "   Total   : {$total}\n\n";

if ( $results['fail'] > 0 ) {
    echo "\033[31mQA FAILED — {$results['fail']} test(s) did not pass.\033[0m\n\n";
    exit( 1 );
} else {
    echo "\033[32mQA PASSED — All tests passed" . ( $results['warn'] > 0 ? " with {$results['warn']} warning(s)" : '' ) . ".\033[0m\n\n";
    exit( 0 );
}
