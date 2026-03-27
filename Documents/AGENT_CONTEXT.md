# Agent Context — Fuad Hasan Portfolio (WordPress)

> This file provides a complete, self-contained briefing for an AI coding agent
> continuing development on this repository. Read this file **first** before making
> any changes. Verify key facts using the cited files before acting.

---

## 1. Repository Identity

| Key | Value |
|-----|-------|
| GitHub repo | `fuadwasi/wordpress_resume_site` |
| Active branch | `copilot/add-dynamic-wordpress-features` |
| Theme directory | `wp-content/themes/fuadhasan-portfolio/` |
| Theme slug | `fuadhasan-portfolio` |
| PHP namespace prefix | `fhp_` (all functions, constants, option keys) |
| Text domain | `fuadhasan-portfolio` |
| Version constant | `FHP_VERSION` = `'1.0.0'` (defined in `functions.php`) |

---

## 2. Current Development State

All **Sections 1–11 Phase 4** are complete. The implementation is in **PHP + WordPress theme code only** — no build tools, no package.json, no webpack.

### What is done
- ✅ Custom Post Types: `experience`, `project`, `skill`, `achievement`
- ✅ Three taxonomies: `tech_stack`, `skill_category`, `achievement_type`
- ✅ ACF field groups (all in PHP, version-controlled)
- ✅ ACF Options Page (4 sub-pages under Admin → Site Settings)
- ✅ Full theme: all page templates, template-parts, header, footer
- ✅ Assets: CSS (design tokens, hero, timeline, responsive, admin), JS (main, smooth-scroll, skills-animation)
- ✅ Content seeder: 7 experience, 7 projects, ~40 skills, 6 achievements, all defaults
- ✅ CV download system (protected upload dir + force-download handler)
- ✅ Admin UI: custom columns, sortable, dashboard widget
- ✅ SEO: JSON-LD (Person/WebPage/ItemList/BreadcrumbList), OG/Twitter, per-page titles
- ✅ Performance: defer, Cache-Control, resource hints, lazy-load, head cleanup
- ✅ Phase 1 setup automation: `setup/local-setup.sh`, `setup/wp-config-*.example`, `inc/setup.php`
- ✅ Phase 5 QA: 132-test static suite (`tests/theme-qa.php`), WP-CLI QA script (`setup/qa-check.sh`), `Documents/QA_REPORT.md`; accessibility fixes: landmark `aria-label`, `aria-pressed` on project filters, `aria-live` live region, back-to-top button, focus management

### What is next
- ⏳ **Phase 6 — Deployment** (Ubuntu 22.04 + Nginx + SSL + Cloudflare)
- ⏳ **Phase 7 — Post-Launch** (GA4, admin training, uptime monitoring)

---

## 3. Coding Conventions

### PHP
- All functions **prefixed** with `fhp_`: `fhp_function_name()`
- All option keys **prefixed** with `fhp_`: `get_option('fhp_seeded')`
- All hooks/actions use anonymous closures or named `fhp_` functions
- **No raw SQL** — use `WP_Query`, `get_posts()`, `wp_insert_post()`, `update_post_meta()`
- **All output escaped**: `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses()`
- PHP 8.2+ features are fine: typed properties, named arguments, `match`, `enum`
- `defined('ABSPATH') || exit;` at the top of every inc/ file
- No closing `?>` tag at the end of PHP files

### CSS
- Design tokens live in `style.css` as CSS custom properties (`--color-*`, `--font-*`, `--spacing-*`)
- Never hardcode colours — always reference `var(--color-*)` tokens
- BEM-ish class naming: `.component__element--modifier`
- Mobile-first breakpoints: `576px`, `768px`, `1024px`, `1280px` (in `assets/css/responsive.css`)

### ACF Fields
- All field groups defined **programmatically** in `inc/acf-fields.php` via `acf_add_local_field_group()`
- Always use `fhp_field($field_name, $post_id)` and `fhp_option($field_name)` helpers (never raw `get_field()`)
- These helpers fall back to `get_post_meta()` / `get_option()` when ACF is not active

### File placement
- New inc/ modules → `wp-content/themes/fuadhasan-portfolio/inc/`
- New inc/ file must be added to `$fhp_includes` array in `functions.php`
- New page templates → root of theme directory (e.g. `page-{slug}.php`)
- New template parts → `template-parts/{category}/`

---

## 4. Key File Map

| File | Purpose |
|------|---------|
| `functions.php` | Bootstrap — loads all inc/ files, theme supports, menus, widget areas |
| `inc/helpers.php` | All template utility functions — read this before writing any template code |
| `inc/custom-post-types.php` | CPT + taxonomy registration + taxonomy term seeding |
| `inc/acf-fields.php` | ALL ACF field group definitions (5 groups) |
| `inc/acf-options.php` | ACF Options Page registration (4 sub-pages) |
| `inc/seeder.php` | Idempotent content seeder — 7 experience, 7 projects, ~40 skills, 6 achievements |
| `inc/setup.php` | Theme activation: permalink, pages, site defaults, admin notice |
| `inc/cv-protection.php` | .htaccess for uploads/cv/, MIME validation, download counter |
| `inc/cv-download.php` | /download-cv rewrite + force-download handler |
| `inc/admin-ui.php` | CPT admin columns, sortable, dashboard widget |
| `inc/seo.php` | JSON-LD schema, OG meta, per-page titles — only active when no SEO plugin present |
| `inc/performance.php` | Defer scripts, Cache-Control, resource hints, lazy-load, head cleanup |
| `inc/plugin-compat.php` | Integration shims for ACF/Yoast/WPForms/Cache plugins |
| `inc/recommended-plugins.php` | Admin notice for missing required plugins |
| `inc/enqueue.php` | wp_enqueue_scripts hook for all CSS/JS |
| `style.css` | Theme header + ALL CSS design tokens |
| `assets/css/main.css` | Global reset, typography, layout utilities, component base styles |
| `assets/css/responsive.css` | All breakpoint overrides |
| `assets/js/main.js` | Hamburger nav, scroll-spy, project filter |
| `assets/js/skills-animation.js` | Intersection Observer skill bar animations |
| `assets/js/smooth-scroll.js` | Smooth anchor scroll |
| `setup/local-setup.sh` | WP-CLI full local setup script |
| `setup/wp-config-local.php.example` | Local dev wp-config template |
| `setup/wp-config-staging.php.example` | Staging wp-config template |
| `Documents/wordpress_portfolio_development_plan.md` | Master plan — check Phase checklist before starting any work |
| `Documents/DEVELOPMENT_REPORT.md` | Full project documentation |

---

## 5. Include Load Order (functions.php `$fhp_includes`)

```php
'/helpers.php'              // 1st — utility functions required by all others
'/custom-post-types.php'    // CPTs + taxonomies
'/acf-options.php'          // ACF Options Page registration
'/acf-fields.php'           // ACF field group definitions
'/enqueue.php'              // CSS/JS enqueue
'/setup.php'                // Theme activation setup
'/cv-protection.php'        // CV upload dir + .htaccess + download counter
'/cv-download.php'          // /download-cv rewrite handler
'/seeder.php'               // Content seeder
'/admin-ui.php'             // Admin columns + dashboard widget
'/plugin-compat.php'        // Plugin integration shims
'/recommended-plugins.php'  // Admin notice for missing plugins
'/seo.php'                  // SEO (JSON-LD, OG, titles)
'/performance.php'          // Performance optimisations
```

---

## 6. WordPress Hook Priorities Used

| Hook | Priority | Function | File |
|------|----------|----------|------|
| `init` | 0 | `fhp_register_post_types()` | custom-post-types.php |
| `init` | 0 | `fhp_register_taxonomies()` | custom-post-types.php |
| `acf/init` | default | `fhp_register_acf_options_pages()` | acf-options.php |
| `acf/init` | default | `fhp_register_acf_fields()` | acf-fields.php |
| `wp_enqueue_scripts` | default | `fhp_enqueue_assets()` | enqueue.php |
| `after_switch_theme` | default | `fhp_seed_taxonomy_terms()` | custom-post-types.php |
| `after_switch_theme` | 20 | `fhp_run_activation_setup()` | setup.php |
| `after_switch_theme` | 20 | `fhp_seed_content()` | seeder.php |
| `after_switch_theme` | default | `fhp_install_cv_protection()` | cv-protection.php |
| `admin_notices` | default | `fhp_setup_admin_notice()` | setup.php |
| `pre_get_document_title` | 10 | `fhp_document_title()` | seo.php |
| `wp_head` | 1 | `fhp_output_meta_tags()` | seo.php |
| `wp_head` | 2 | `fhp_output_schema_json_ld()` | seo.php |
| `wp_head` | 5 | resource hints | performance.php |

---

## 7. Data Flow — How Content Reaches the Page

```
WordPress Admin (Editor enters data)
         │
         ▼
ACF Options Page / CPT Edit Screen
         │ (stored in wp_postmeta / wp_options via ACF)
         ▼
Template calls fhp_field($field_name, $post_id)
     or calls fhp_option($field_name)
         │ (calls update_field() / get_field() if ACF active, else get_post_meta())
         ▼
Template part renders HTML
         │ (all values escaped at output point)
         ▼
Page served to visitor
```

---

## 8. CPT Admin Paths

| CPT | Admin URL |
|-----|-----------|
| Experience | `/wp-admin/edit.php?post_type=experience` |
| Projects | `/wp-admin/edit.php?post_type=project` |
| Skills | `/wp-admin/edit.php?post_type=skill` |
| Achievements | `/wp-admin/edit.php?post_type=achievement` |
| Tech Stack taxonomy | `/wp-admin/edit-tags.php?taxonomy=tech_stack&post_type=experience` |
| Skill Categories | `/wp-admin/edit-tags.php?taxonomy=skill_category&post_type=skill` |
| Options Page (hero) | `/wp-admin/admin.php?page=fhp-hero-about` |
| Options Page (contact) | `/wp-admin/admin.php?page=fhp-contact-social` |
| Options Page (CV) | `/wp-admin/admin.php?page=fhp-cv-settings` |
| Options Page (stats) | `/wp-admin/admin.php?page=fhp-stats` |

---

## 9. Site URL Map

| URL | Template | Notes |
|-----|----------|-------|
| `/` | `front-page.php` | Hero → Summary → Skills → Achievements → Projects |
| `/about` | `page-about.php` | Full bio, career focus |
| `/experience` | `archive-experience.php` | Timeline of all experience CPT posts |
| `/experience/{slug}` | `single-experience.php` | Single role detail |
| `/projects` | `archive-project.php` | Filterable grid of all project CPT posts |
| `/projects/{slug}` | `single-project.php` | Single project detail |
| `/skills` | `page-skills.php` | Skills grouped by skill_category taxonomy |
| `/achievements` | `page-achievements.php` | All achievement CPT posts |
| `/contact` | `page-contact.php` | Contact form + contact info |
| `/download-cv` | `page-download-cv.php` | Forces PDF download of active CV |
| (404) | `404.php` | Custom 404 page |

---

## 10. Content Seeder — What's Pre-loaded

The seeder (`inc/seeder.php`) populates a fresh install. Re-run with:
```bash
wp option delete fhp_seeded --allow-root && wp eval 'fhp_seed_content();' --allow-root
```

### Experience (7 roles)
1. Senior Software Engineer II — Brain Station 23 PLC (current)
2. Senior Software Engineer I — Brain Station 23 PLC
3. Software Engineer — Brain Station 23 PLC
4. Junior Software Engineer — Brain Station 23 PLC
5. QA Engineer — Brain Station 23 PLC
6. Junior QA Engineer — Brain Station 23 PLC
7. Software Engineer — BSSIT

### Projects (7+)
1. Multi-Vendor B2C eCommerce Platform
2. B2B Multi-Channel Platform (NopCommerce)
3. Retail POS System Integration
4. Custom NopCommerce Payment Gateway Plugin
5. SAP ERP & NopCommerce Integration
6. ZohoCRM–NopCommerce Sync Plugin
7. Open-Source NopCommerce Extensions

### Skills (~40 across 7 categories)
- Backend: ASP.NET Core, C#, NopCommerce, gRPC, RabbitMQ, ...
- Frontend: Angular, Next.js, JavaScript, jQuery, ...
- Database: MSSQL, MySQL, MongoDB, Redis
- DevOps & Cloud: Docker, Azure DevOps, CI/CD, ...
- Integrations: SAP ERP, ZohoCRM, TaxJar, ...
- Soft Skills: Team Leadership, Scrum/Agile, ...
- Competitive Programming: C++, Algorithms, Data Structures

### Achievements (6)
1. NopCommerce Certified Developer
2. Prompt Engineering Foundations (BS23)
3. ICPC Dhaka Regional Onsite — Rank 96/300
4. DIU Take-Off Programming Contest Champion — Rank 1/300
5. Programming for Everybody — Python (Coursera)
6. Digital Assets Security Awareness (BS23)

---

## 11. ACF Field Reference (Template Usage)

### Experience CPT fields (`inc/acf-fields.php` → `group_fhp_experience`)
```php
fhp_field('company_name',       $post_id)  // string
fhp_field('company_logo',       $post_id)  // image array: ['url', 'alt', 'sizes']
fhp_field('company_url',        $post_id)  // URL string
fhp_field('job_title',          $post_id)  // string
fhp_field('employment_type',    $post_id)  // label string: 'Full-time' etc.
fhp_field('start_date',         $post_id)  // 'Y-m-d' → use fhp_format_date()
fhp_field('end_date',           $post_id)  // 'Y-m-d' or empty if current
fhp_field('is_current',         $post_id)  // bool
fhp_field('location',           $post_id)  // string
fhp_field('job_description',    $post_id)  // HTML string (WYSIWYG — use wp_kses_post())
fhp_field('key_contributions',  $post_id)  // repeater: [ ['contribution_text' => '...'], ... ]
fhp_field('project_references', $post_id)  // repeater: [ ['ref_name' => '...', 'ref_url' => '...'], ... ]
```

### Project CPT fields
```php
fhp_field('short_description',  $post_id)  // string
fhp_field('project_type',       $post_id)  // label string: 'B2B eCommerce' etc.
fhp_field('client_company',     $post_id)  // string
fhp_field('live_url',           $post_id)  // URL
fhp_field('github_url',         $post_id)  // URL
fhp_field('is_featured',        $post_id)  // bool
```

### Skill CPT fields
```php
fhp_field('proficiency_level',  $post_id)  // int 1–5
fhp_field('skill_icon',         $post_id)  // image array or empty
```

### Achievement CPT fields
```php
fhp_field('issuing_organization', $post_id)  // string
fhp_field('issue_date',           $post_id)  // 'Y-m-d'
fhp_field('credential_url',       $post_id)  // URL
fhp_field('achievement_description', $post_id) // string
fhp_field('achievement_icon',     $post_id)  // image array or empty
```

### Options Page fields
```php
fhp_option('hero_name')              // 'Fuad Hasan'
fhp_option('hero_title')             // 'Senior Software Engineer II'
fhp_option('hero_subtitle')          // tagline
fhp_option('profile_photo')          // image array
fhp_option('hero_background_image')  // image array
fhp_option('about_summary')          // HTML string (WYSIWYG)
fhp_option('email_address')          // 'fhassanwasi@gmail.com'
fhp_option('phone_number')           // '+880 01792 478 378'
fhp_option('location')               // 'Dhaka, Bangladesh'
fhp_option('github_url')             // 'https://github.com/fuadwasi'
fhp_option('linkedin_url')           // LinkedIn URL
fhp_option('codeforces_url')         // Codeforces URL
fhp_option('footer_tagline')         // string
fhp_option('active_cv_file')         // file array: ['url', 'filename', 'mime_type']
fhp_option('cv_download_label')      // 'Download CV'
fhp_option('years_of_experience')    // int
fhp_option('total_projects')         // int
fhp_option('open_source_contributions') // int
```

---

## 12. Adding New Sections or Pages — Step-by-Step

### Add a new page template
1. Create `page-{slug}.php` in the theme root
2. In `inc/setup.php` → `fhp_get_required_pages()`, add:
   ```php
   '{slug}' => [ 'Page Title', 'page-{slug}.php', $menu_order ],
   ```
3. Run theme re-activation or call `fhp_create_required_pages()` directly

### Add a new CPT
1. Add `fhp_register_cpt_{name}()` to `inc/custom-post-types.php`
2. Call it from `fhp_register_post_types()`
3. Add ACF field group in `inc/acf-fields.php`
4. Add admin columns in `inc/admin-ui.php`
5. Add seeder data in `inc/seeder.php` (new function + call from `fhp_seed_content()`)
6. Delete `fhp_seeded` option to re-run seeder

### Add a new ACF Options sub-page
1. In `inc/acf-options.php` → `fhp_register_acf_options_pages()`, add:
   ```php
   acf_add_options_sub_page( [ 'menu_slug' => 'fhp-{name}', ... ] );
   ```
2. In `inc/acf-fields.php`, add a new field group with location rule:
   ```php
   'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'fhp-{name}' ] ] ]
   ```

### Add a new inc/ module
1. Create `inc/{module}.php` with `defined('ABSPATH') || exit;` at top
2. Add `'/{module}.php'` to `$fhp_includes` in `functions.php` (in correct order)

---

## 13. WP-CLI Quick Reference

```bash
# Activate theme
wp theme activate fuadhasan-portfolio --allow-root

# Re-run content seeder
wp option delete fhp_seeded --allow-root
wp eval 'fhp_seed_content();' --allow-root

# Re-run site setup (permalink, pages, defaults)
wp option delete fhp_setup_version --allow-root
wp option delete fhp_defaults_applied --allow-root

# Flush rewrite rules
wp rewrite flush --allow-root

# Check permalink structure
wp option get permalink_structure --allow-root

# List all CPT posts
wp post list --post_type=experience --allow-root
wp post list --post_type=project --allow-root
wp post list --post_type=skill --allow-root
wp post list --post_type=achievement --allow-root

# Check CV download count
wp option get fhp_cv_download_count --allow-root

# Delete all seeded content (nuclear reset)
wp post delete $(wp post list --post_type=experience --format=ids --allow-root) --force --allow-root
wp post delete $(wp post list --post_type=project --format=ids --allow-root) --force --allow-root
wp option delete fhp_seeded --allow-root
```

---

## 14. Checklist for Any Code Change

Before submitting any code change to this repository:

- [ ] All new PHP functions use the `fhp_` prefix
- [ ] All output is escaped (`esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses()`)
- [ ] No raw SQL — use WordPress data APIs
- [ ] All new options/meta keys use `fhp_` prefix
- [ ] New inc/ files loaded in `functions.php` `$fhp_includes`
- [ ] No closing `?>` PHP tag at end of file
- [ ] CSS uses `var(--token-name)` for all colours/spacing
- [ ] PHP syntax check: `php -l wp-content/themes/fuadhasan-portfolio/inc/new-file.php`
- [ ] Development plan checklist updated (mark new items `[x]`)
- [ ] `Documents/DEVELOPMENT_REPORT.md` updated if architecture changes

---

## 15. Environment & Secrets Policy

| File | Status | Reason |
|------|--------|--------|
| `wp-config.php` | ❌ Never committed | Contains DB credentials + secret keys |
| `wp-config-local.php` | ❌ Never committed | Local credentials |
| `wp-config-staging.php` | ❌ Never committed | Staging credentials |
| `.env` / `.env.*` | ❌ Never committed | Environment variables |
| `wp-content/uploads/` | ❌ Never committed | User-uploaded media |
| `wp-content/debug.log` | ❌ Never committed | May contain sensitive paths |
| `setup/wp-config-local.php.example` | ✅ Committed | Template only — no real credentials |
| `setup/wp-config-staging.php.example` | ✅ Committed | Template only — no real credentials |
