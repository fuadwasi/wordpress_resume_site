# Fuad Hasan Portfolio — Development Report

> **Repository:** [`fuadwasi/wordpress_resume_site`](https://github.com/fuadwasi/wordpress_resume_site)  
> **Branch:** `copilot/add-dynamic-wordpress-features`  
> **Theme:** `fuadhasan-portfolio` (custom, no parent theme)  
> **Last Updated:** 2026-03-26

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [How to Run the Project](#3-how-to-run-the-project)
   - [Prerequisites](#prerequisites)
   - [Option A — Automated Setup (WP-CLI)](#option-a--automated-setup-wp-cli)
   - [Option B — Manual Setup](#option-b--manual-setup)
   - [Staging Environment](#staging-environment)
4. [Repository Structure](#4-repository-structure)
5. [What Has Been Built (Sections 1–11 Phase 4)](#5-what-has-been-built-sections-111-phase-4)
6. [Customisations & How They Work](#6-customisations--how-they-work)
   - [Custom Post Types & Taxonomies](#custom-post-types--taxonomies)
   - [ACF Field Groups & Options Page](#acf-field-groups--options-page)
   - [Theme Templates](#theme-templates)
   - [CV Download System](#cv-download-system)
   - [Content Seeder](#content-seeder)
   - [Admin UI Enhancements](#admin-ui-enhancements)
   - [SEO Layer](#seo-layer)
   - [Performance Layer](#performance-layer)
   - [Plugin Compatibility Layer](#plugin-compatibility-layer)
   - [Environment Setup Module](#environment-setup-module)
7. [Admin Guide — Day-to-Day Content Management](#7-admin-guide--day-to-day-content-management)
8. [Development Progress](#8-development-progress)
9. [Manual Steps Remaining](#9-manual-steps-remaining)
10. [Security Notes](#10-security-notes)

---

## 1. Project Overview

This repository converts Fuad Hasan's static HTML portfolio into a fully dynamic, admin-managed **WordPress site**. Every piece of visible content (hero text, experience timeline, projects, skills, achievements, CV file, contact details, social links) is editable from the WordPress admin panel — no code changes required.

**Live static reference:** [resume-to-site-magic-70.lovable.app](https://resume-to-site-magic-70.lovable.app/)

### Key Design Decisions

| Decision | Rationale |
|----------|-----------|
| Custom theme (no parent) | Full control over output; no Twentyxx bloat |
| ACF Pro for fields | Cleanest admin UI for non-technical editors |
| All ACF fields in PHP | Version-controlled; never lost on DB reset |
| Content seeder | Idempotent; a fresh install has real data immediately |
| `/%postname%/` permalinks | SEO-friendly, required by CPT archives |
| Dark design system via CSS custom properties | Single-file token change updates the whole theme |

---

## 2. Tech Stack

| Layer | Technology |
|-------|-----------|
| CMS | WordPress 6.x (latest stable) |
| Backend | PHP 8.2+ |
| Database | MySQL 8.x / MariaDB 10.6+ |
| Custom Fields | Advanced Custom Fields (ACF) Pro |
| Forms | WPForms Lite or Contact Form 7 |
| SEO | Yoast SEO or Rank Math |
| Cache | WP Super Cache or W3 Total Cache |
| Security | Wordfence Security |
| Backups | UpdraftPlus |
| Server | Ubuntu 22.04 + Nginx + PHP-FPM |
| CDN | Cloudflare (free tier) |

---

## 3. How to Run the Project

### Prerequisites

- **WP-CLI** ≥ 2.8: [wp-cli.org](https://wp-cli.org/)
- **PHP** ≥ 8.2
- **MySQL** 8.x or **MariaDB** 10.6+
- A running local WordPress environment:
  - [Local by Flywheel](https://localwp.com/) ← recommended for macOS/Windows
  - XAMPP / Laragon / Lando / Docker
- **ACF Pro** licence (from [advancedcustomfields.com/pro](https://www.advancedcustomfields.com/pro/))

---

### Option A — Automated Setup (WP-CLI)

This script covers all Phase 1 environment tasks end-to-end.

```bash
# 1. Create a WordPress root directory and navigate to it
mkdir fuad-portfolio && cd fuad-portfolio

# 2. Clone this repository into the themes directory
mkdir -p wp-content/themes
git clone https://github.com/fuadwasi/wordpress_resume_site.git \
    wp-content/themes/fuadhasan-portfolio

# 3. Run the automated setup script (from the WP root)
bash wp-content/themes/fuadhasan-portfolio/setup/local-setup.sh
```

The script will:
1. Download WordPress core (latest)
2. Create `wp-config.php` (prompts you for DB credentials via env vars)
3. Install the WordPress database
4. Set permalink structure to `/%postname%/`
5. Install and activate 6 plugins (ACF free, Yoast SEO, WPForms Lite, WP Super Cache, Wordfence, UpdraftPlus)
6. Activate the `fuadhasan-portfolio` theme

**Configurable via environment variables:**

```bash
FHP_DB_NAME=fuad_portfolio_dev \
FHP_DB_USER=root \
FHP_DB_PASS=mypassword \
FHP_WP_URL=http://localhost/fuad-portfolio \
FHP_ADMIN_USER=fuad_admin \
FHP_ADMIN_PASS=mysecretpass \
bash wp-content/themes/fuadhasan-portfolio/setup/local-setup.sh
```

---

### Option B — Manual Setup

```bash
# 1. Download WordPress
wp core download

# 2. Configure database (edit the generated file with your DB credentials)
cp wp-content/themes/fuadhasan-portfolio/setup/wp-config-local.php.example wp-config.php
# Edit wp-config.php: DB_NAME, DB_USER, DB_PASSWORD, DB_HOST, security keys

# 3. Install WordPress
wp core install \
  --url="http://localhost/fuad-portfolio" \
  --title="Fuad Hasan — Senior Software Engineer II" \
  --admin_user=fuad_admin \
  --admin_email=fhassanwasi@gmail.com \
  --admin_password=YOUR_SECURE_PASSWORD

# 4. Activate theme (triggers automatic setup)
wp theme activate fuadhasan-portfolio

# 5. Set permalink structure
wp rewrite structure '/%postname%/' && wp rewrite flush

# 6. Install required plugins
wp plugin install advanced-custom-fields wordpress-seo wpforms-lite wp-super-cache --activate
```

#### After installation — first admin tasks:

1. **Install ACF Pro** (replaces the free version):
   - Download from [advancedcustomfields.com/pro](https://www.advancedcustomfields.com/pro/)
   - Upload at Admin → Plugins → Add New → Upload Plugin

2. **Upload profile photo:**
   - Admin → Site Settings → Hero & About → Profile Photo

3. **Upload CV PDF:**
   - Admin → Site Settings → CV / Resume → Active CV File (PDF)

4. **Enable search engine indexing** (when ready to launch):
   - Admin → Settings → Reading → uncheck "Discourage search engines"

5. **Configure WP Super Cache:**
   - Admin → Settings → WP Super Cache → Enable Caching

6. **Set up Yoast SEO / Rank Math:**
   - Follow the plugin's setup wizard

---

### Staging Environment

```bash
# Copy the staging config template to the server
scp wp-content/themes/fuadhasan-portfolio/setup/wp-config-staging.php.example \
    user@staging-server:/var/www/html/wp-config.php

# Edit on the server: update DB credentials, site URL, security keys
ssh user@staging-server "nano /var/www/html/wp-config.php"
```

The staging config (`setup/wp-config-staging.php.example`) includes:
- `WP_DEBUG = true` / `WP_DEBUG_DISPLAY = false` (errors go to log only)
- `NOBLOGREDIRECT` to prevent staging content appearing in Google
- Optional `WP_HTTP_BLOCK_EXTERNAL` to isolate staging from external APIs

---

## 4. Repository Structure

```
wordpress_resume_site/
├── .gitignore                          # Excludes wp-config*.php, .env*, uploads, debug.log
├── README.md                           # Project quick-start
├── Documents/
│   ├── wordpress_portfolio_development_plan.md  ← Master plan (Sections 1–11)
│   ├── DEVELOPMENT_REPORT.md           ← This file
│   ├── AGENT_CONTEXT.md                ← AI agent context for continued development
│   ├── Fuad Hasan Resume Senior Software Engineer.pdf
│   ├── Fuad_Linkedin_Profile.pdf
│   └── portfolio_requirements.txt
│
├── setup/
│   ├── local-setup.sh                  # Phase 1: full WP-CLI automation
│   ├── wp-config-local.php.example     # Local dev wp-config template
│   └── wp-config-staging.php.example  # Staging wp-config template
│
└── wp-content/themes/fuadhasan-portfolio/
    ├── style.css           # Theme header + CSS design tokens (Dark palette)
    ├── functions.php       # Bootstrap: load all inc/ files, theme supports, menus
    ├── index.php           # WordPress fallback template
    ├── page.php            # Generic page fallback
    ├── sidebar.php         # Optional sidebar (not used in default layout)
    ├── 404.php             # Custom 404 error page
    │
    ├── header.php          # Responsive header, primary nav, hamburger, CV CTA
    ├── footer.php          # Social links, copyright, back-to-top
    │
    ├── front-page.php      # Home page (Hero → Summary → Skills → Achievements → Projects)
    ├── archive-experience.php   # /experience — timeline layout
    ├── single-experience.php    # /experience/{slug} — detail view
    ├── archive-project.php      # /projects — filterable card grid
    ├── single-project.php       # /projects/{slug} — project detail
    │
    ├── page-about.php           # /about
    ├── page-skills.php          # /skills — categorised skill groups
    ├── page-achievements.php    # /achievements
    ├── page-contact.php         # /contact — form + contact block
    ├── page-download-cv.php     # /download-cv — serves PDF with force-download
    │
    ├── template-parts/
    │   ├── home/
    │   │   ├── hero.php               # Hero: name, title, CTA buttons, profile photo
    │   │   ├── summary.php            # Professional summary from Options Page
    │   │   ├── skills-overview.php    # Top skills snapshot (progress bars)
    │   │   ├── achievements-bar.php   # Key achievements strip
    │   │   └── featured-projects.php  # Featured projects grid (is_featured = true)
    │   ├── experience/
    │   │   └── timeline-item.php      # Single experience card in timeline
    │   ├── project/
    │   │   └── project-card.php       # Project card (grid item)
    │   ├── skill/
    │   │   └── skill-group.php        # Skill group: category heading + skill chips/bars
    │   └── shared/
    │       ├── section-header.php     # Reusable `<section>` heading block
    │       └── cta-buttons.php        # Download CV + Contact Me CTA pair
    │
    ├── assets/
    │   ├── css/
    │   │   ├── main.css               # Global reset, typography, layout utilities
    │   │   ├── hero.css               # Hero section specific styles
    │   │   ├── timeline.css           # Experience timeline layout
    │   │   ├── responsive.css         # Breakpoints: 576/768/1024/1280 px
    │   │   └── admin.css              # WordPress admin customisations
    │   ├── js/
    │   │   ├── main.js                # Core interactions: hamburger, scroll spy, project filter
    │   │   ├── smooth-scroll.js       # Smooth anchor scrolling polyfill
    │   │   └── skills-animation.js    # Intersection Observer — skill bar/counter animations
    │   └── images/
    │       └── placeholder/
    │           └── placeholder-project.svg  # Default project image
    │
    └── inc/
        ├── helpers.php            # fhp_field(), fhp_option(), fhp_get_cv_url(),
        │                          # fhp_get_experience_query(), fhp_get_featured_projects(),
        │                          # fhp_get_skills_by_category(), fhp_date_range(), etc.
        ├── custom-post-types.php  # Register experience/project/skill/achievement CPTs
        │                          # + tech_stack / skill_category / achievement_type taxonomies
        │                          # + fhp_seed_taxonomy_terms() (seeded on activation)
        ├── acf-options.php        # ACF Options Page: Site Settings (4 sub-pages)
        ├── acf-fields.php         # All ACF field groups defined in PHP (version-controlled)
        ├── enqueue.php            # wp_enqueue_scripts: Google Fonts, main CSS/JS, theme JS
        ├── setup.php              # Phase 1 activation: permalink, pages, site defaults, notice
        ├── cv-protection.php      # uploads/cv/.htaccess, MIME validation, download counter
        ├── cv-download.php        # /download-cv rewrite rule + force-download handler
        ├── seeder.php             # Idempotent content seeder: 7 experience, 7 projects,
        │                          # ~40 skills, 6 achievements, all Options Page defaults
        ├── admin-ui.php           # Custom CPT admin columns, sortable columns, dashboard widget
        ├── plugin-compat.php      # Graceful ACF / Yoast / WPForms integration shims
        ├── recommended-plugins.php # Admin notice for missing required plugins
        ├── seo.php                # JSON-LD schema (Person/WebPage/ItemList/BreadcrumbList),
        │                          # OG/Twitter meta, per-page title/description filters
        └── performance.php        # Resource hints, script defer, Cache-Control headers,
                                   # lazy loading, head cleanup, query-string removal
```

---

## 5. What Has Been Built (Sections 1–11 Phase 4)

| Section | Title | Status | Key Files |
|---------|-------|--------|-----------|
| 1 | Project Requirements | ✅ | `Documents/portfolio_requirements.txt` |
| 2 | Site Architecture | ✅ | `functions.php`, CPT slugs, page templates |
| 3 | Design System | ✅ | `style.css` (CSS tokens), `assets/css/main.css` |
| 4 | Content Structure | ✅ | `inc/acf-fields.php`, `inc/seeder.php` |
| 5 | Theme Development Plan | ✅ | All theme templates built |
| 6 | Plugin List & Configuration | ✅ | `inc/plugin-compat.php`, `inc/recommended-plugins.php` |
| 7 | CV Download Feature | ✅ | `inc/cv-download.php`, `inc/cv-protection.php`, `page-download-cv.php` |
| 8 | Admin Panel Capabilities | ✅ | `inc/admin-ui.php` |
| 9 | Content Migration Plan | ✅ | `inc/seeder.php` (full data set) |
| 10 | SEO & Performance | ✅ | `inc/seo.php`, `inc/performance.php` |
| 11 Phase 1 | Environment Setup | ✅ | `setup/`, `inc/setup.php` |
| 11 Phase 2 | CPTs & ACF Fields | ✅ | `inc/custom-post-types.php`, `inc/acf-fields.php`, `inc/acf-options.php` |
| 11 Phase 3 | Theme Development | ✅ | All templates + assets |
| 11 Phase 4 | Content Population | ✅ | `inc/seeder.php` (auto-runs on activation) |

---

## 6. Customisations & How They Work

### Custom Post Types & Taxonomies

**File:** `wp-content/themes/fuadhasan-portfolio/inc/custom-post-types.php`

Four CPTs registered with `register_post_type()` on the `init` hook:

| CPT | Archive Slug | Public | Supports |
|-----|-------------|--------|----------|
| `experience` | `/experience` | Yes | title, editor, thumbnail, revisions, page-attributes |
| `project` | `/projects` | Yes | title, editor, thumbnail, revisions, page-attributes |
| `skill` | — | No (admin-only) | title, page-attributes |
| `achievement` | `/achievements` | Yes | title, thumbnail, page-attributes |

Three taxonomies:

| Taxonomy | Applied To | Type |
|----------|-----------|------|
| `tech_stack` | experience, project | Flat (tag-style) |
| `skill_category` | skill | Hierarchical (category-style) |
| `achievement_type` | achievement | Hierarchical |

Default terms are inserted on `after_switch_theme` via `fhp_seed_taxonomy_terms()`.

---

### ACF Field Groups & Options Page

**Files:** `inc/acf-fields.php`, `inc/acf-options.php`

All 5 field groups are registered in PHP via `acf_add_local_field_group()` on the `acf/init` hook. They never need to be exported from the database — they are always version-controlled.

**Experience Details** (`group_fhp_experience`):
- Company name, logo (image), website URL
- Job title, employment type (select), location
- Start date / end date (date pickers, format: `F, Y`)
- Is current position (true/false toggle)
- Role description (WYSIWYG)
- Key contributions (repeater — text rows)
- Project references (repeater — name + URL)
- Display order (number)

**Project Details** (`group_fhp_project`):
- Short description (textarea — used on grid cards)
- Project type (select: B2B/B2C eCommerce, Plugin, POS, CMS, Open Source)
- Client/company, live URL, GitHub URL
- Featured project (true/false — controls Home page appearance)
- Display order (number)

**Skill Details** (`group_fhp_skill`):
- Skill icon (image, optional)
- Proficiency level (range slider 1–5)
- Display order (number)

**Achievement Details** (`group_fhp_achievement`):
- Issuing organisation, issue date, credential URL
- Description (textarea)
- Badge/icon (image)

**Options Page** (4 sub-pages under Admin → Site Settings):

| Sub-page | Slug | Key Fields |
|----------|------|-----------|
| Hero & About | `fhp-hero-about` | Full name, professional title, tagline, profile photo, hero background, about summary (WYSIWYG) |
| Contact & Social | `fhp-contact-social` | Email, phone, location, GitHub URL, LinkedIn URL, Codeforces URL, footer tagline |
| CV / Resume | `fhp-cv-settings` | Active CV file (PDF upload), download button label |
| Stats & Counters | `fhp-stats` | Years of experience, total projects, open-source contributions |

---

### Theme Templates

**How the homepage (`/`) is assembled:**

```
front-page.php
 ├── get_header()
 ├── get_template_part('template-parts/home/hero')
 │     └── Reads: hero_name, hero_title, hero_subtitle, profile_photo (from Options)
 ├── get_template_part('template-parts/home/summary')
 │     └── Reads: about_summary (Options)
 ├── get_template_part('template-parts/home/skills-overview')
 │     └── Reads: fhp_get_skills_by_category() → skill CPT query
 ├── get_template_part('template-parts/home/achievements-bar')
 │     └── Reads: fhp_get_achievements() → achievement CPT query
 ├── get_template_part('template-parts/home/featured-projects')
 │     └── Reads: fhp_get_featured_projects(4) → project CPT (is_featured=true)
 └── get_footer()
```

**How the experience timeline (`/experience`) works:**

```
archive-experience.php
 └── foreach( fhp_get_experience_query() )
       └── get_template_part('template-parts/experience/timeline-item')
             Fields: company_name, job_title, start_date, end_date, is_current,
                     location, job_description, key_contributions[], tech_stack terms
```

**Helper functions** (`inc/helpers.php`):
- `fhp_field($name, $post_id)` — ACF field with fallback to `get_post_meta()`
- `fhp_option($name, $fallback)` — ACF options page field
- `fhp_get_cv_url()` — returns the download URL for the active CV
- `fhp_get_experience_query()` — sorted WP_Query for experience CPT
- `fhp_get_featured_projects($limit)` — projects where `is_featured = true`
- `fhp_get_skills_by_category()` — skills grouped by `skill_category` taxonomy
- `fhp_date_range($start, $end, $is_current)` — formatted "Jan 2021 – Present"
- `fhp_tech_tags_html($post_id)` — renders `<span class="tag">` list from taxonomy

---

### CV Download System

**Files:** `inc/cv-protection.php`, `inc/cv-download.php`, `page-download-cv.php`

**How it works:**

1. `cv-protection.php` runs on `after_switch_theme`:
   - Creates `wp-content/uploads/cv/` directory
   - Writes an `.htaccess` that blocks direct Apache/Nginx access to the directory
   - Registers a download counter (stored in `fhp_cv_download_count` wp_option)

2. `cv-download.php` adds a WordPress rewrite rule:
   - URL `/download-cv` → calls `fhp_handle_cv_download()`
   - `fhp_handle_cv_download()` reads the active CV file from the ACF Options Page field (`active_cv_file`), validates MIME type, increments the counter, and serves the file with:
     ```php
     header('Content-Type: application/pdf');
     header('Content-Disposition: attachment; filename="Fuad_Hasan_CV.pdf"');
     ```

3. `page-download-cv.php` is a fallback template that shows a download button + file info if the rewrite rule doesn't fire.

4. The admin dashboard widget (in `inc/admin-ui.php`) shows the current download count.

---

### Content Seeder

**File:** `inc/seeder.php`

Runs once on `after_switch_theme` (priority 20). Protected by an `fhp_seeded` wp_option flag.

**What gets seeded:**

| Category | Count | Notes |
|----------|-------|-------|
| Work Experience | 7 | Brain Station 23 PLC (5 roles) + BSSIT (2 roles) |
| Projects | 7+ | B2B/B2C eCommerce, POS, NopCommerce plugins, open-source |
| Skills | ~40 | Across 7 categories with proficiency levels 1–5 |
| Achievements | 6 | Certifications, contest results, awards |
| Options Page fields | All | Hero text, contact info, social links, stats, CV label |

**Re-seeding:** Delete the `fhp_seeded` option from wp_options to re-run:
```bash
wp option delete fhp_seeded
```

---

### Admin UI Enhancements

**File:** `inc/admin-ui.php`

- **Custom CPT admin columns:**
  - Experience: Company, Role, Dates, Is Current
  - Project: Featured (✅/—), Type, Tech Stack, Links
  - Skill: Category, Level (★★★★☆)
  - Achievement: Type, Organisation, Date, Credential
- **Sortable columns:** dates, company name, proficiency level
- **Portfolio Status dashboard widget:** CPT counts, CV download count, quick admin links
- **Inline admin CSS** for column widths and badge styling

---

### SEO Layer

**File:** `inc/seo.php`

Falls back gracefully — only runs when no SEO plugin (Yoast/Rank Math) is active.

- **Per-page title filter** (`pre_get_document_title`): custom titles for all routes
- **Meta description** (`wp_head`): per-page descriptions from ACF or auto-generated
- **OG/Twitter meta tags:** title, description, image, type, URL, site name
- **JSON-LD Schema Markup** (injected via `wp_head`):
  - `Person` schema: name, job title, email, social profiles, coding profiles
  - `WebPage` schema: per-page or site-level
  - `ItemList` schema: on the projects archive (up to 10 projects)
  - `BreadcrumbList` schema: on inner pages

---

### Performance Layer

**File:** `inc/performance.php`

- **Resource hints:** `preconnect` + `dns-prefetch` for Google Fonts and CDN
- **Script defer:** JavaScript assets loaded with `defer` attribute
- **Cache-Control headers:** `public, max-age=31536000` for static assets; `no-cache` for HTML
- **Lazy loading:** all images get `loading="lazy"` via `wp_lazy_loading_enabled`
- **Head cleanup:** removes `wp_generator`, RSD link, Windows Live Writer manifest, shortlink
- **Query string removal:** strips `?ver=` from enqueued asset URLs

---

### Plugin Compatibility Layer

**File:** `inc/plugin-compat.php`

Registers integration hooks that activate only when the corresponding plugin is installed:

- **ACF Pro** — connects the theme's `fhp_option()` helper to ACF's `get_field( ..., 'option' )`
- **WPForms / Contact Form 7** — auto-inserts form shortcode on the Contact page if no custom content exists
- **Yoast SEO / Rank Math** — deactivates the theme's own SEO layer (`inc/seo.php`) to prevent duplicate meta output
- **WP Super Cache / W3 Total Cache** — applies cache-bypass headers on the CV download URL

---

### Environment Setup Module

**File:** `inc/setup.php`

Runs on `after_switch_theme` (priority 20):

1. **`fhp_configure_permalink_structure()`** — sets `/%postname%/` via `$wp_rewrite`, idempotent
2. **`fhp_create_required_pages()`** — creates About, Skills, Achievements, Contact, Download CV pages with correct `_wp_page_template` assignments; idempotent (guards with `get_page_by_path()`)
3. **`fhp_set_site_defaults()`** — timezone `Asia/Dhaka`, date format `F j, Y`, discourages search engine indexing, removes Hello World post + Sample Page
4. **`fhp_setup_admin_notice()`** — one-time dismissible admin notice listing what was auto-configured and what still needs manual action

---

## 7. Admin Guide — Day-to-Day Content Management

### Update CV / Resume

1. Admin → Site Settings → CV / Resume
2. Upload a new PDF to "Active CV File"
3. Save — the download URL (`/download-cv`) instantly serves the new file

### Add a New Work Experience Entry

1. Admin → Experience → Add New
2. Fill in: Post Title = Job Title
3. Fill in all "Experience Details" ACF fields:
   - Company Name, Job Title, Employment Type, Start Date, End Date
   - Is Current Position (toggle on if applicable)
   - Role Description (rich text)
   - Key Contributions (add rows to the repeater)
4. Add Tech Stack terms (right sidebar)
5. Set Menu Order (lower = earlier on timeline)
6. Publish

### Add a New Project

1. Admin → Projects → Add New
2. Fill in: Post Title = Project Name
3. Fill in "Project Details" ACF fields
4. Toggle "Featured Project" if it should appear on the Home page
5. Set Featured Image (project screenshot)
6. Publish

### Update a Skill

1. Admin → Skills → find the skill
2. Adjust the Proficiency Level slider (1–5)
3. Update

### Update Contact Info / Social Links

1. Admin → Site Settings → Contact & Social
2. Update any field and save

---

## 8. Development Progress

See [`Documents/wordpress_portfolio_development_plan.md`](wordpress_portfolio_development_plan.md) for the full checklist with `[x]` completion markers.

**Summary:**

| Phase | Description | Status |
|-------|-------------|--------|
| 1 | Environment Setup | ✅ Complete |
| 2 | CPTs & ACF Fields | ✅ Complete |
| 3 | Theme Development | ✅ Complete |
| 4 | Content Population | ✅ Complete |
| 5 | Testing & QA | ⏳ Pending |
| 6 | Deployment | ⏳ Pending |
| 7 | Post-Launch | ⏳ Pending |

---

## 9. Manual Steps Remaining

| Task | Where | Notes |
|------|-------|-------|
| Install ACF Pro | Admin → Plugins → Add New | Requires licence key |
| Upload profile photo | Admin → Site Settings → Hero & About | JPEG/PNG, min 400×400 px |
| Upload CV PDF | Admin → Site Settings → CV / Resume | Max file size per server config |
| Set contact form shortcode | Admin → Site Settings or page editor | Requires WPForms/CF7 installed |
| Enable search engine indexing | Admin → Settings → Reading | Disable before launch |
| Configure WP Super Cache | Admin → Settings → WP Super Cache | Enable + configure CDN if using Cloudflare |
| Set up Yoast/Rank Math | Plugin setup wizard | Creates sitemap automatically |
| Cross-browser QA | Manual testing | Chrome, Firefox, Safari, Edge |
| Mobile responsiveness QA | Manual testing | iPhone SE, iPhone 14, Samsung Galaxy, iPad |
| Google PageSpeed audit | [pagespeed.web.dev](https://pagespeed.web.dev/) | Target: Desktop > 95, Mobile > 85 |

---

## 10. Security Notes

| Concern | Mitigation |
|---------|-----------|
| `wp-config.php` exposure | Excluded from version control via `.gitignore` |
| CV file direct access | `uploads/cv/.htaccess` blocks direct file access |
| CV MIME validation | Only `application/pdf` accepted by the download handler |
| XSS | All output uses `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses()` throughout |
| SQL injection | No raw SQL; all queries use `WP_Query` with sanitised parameters |
| Environment variables | `wp-config-*.php` and `.env*` excluded from `.gitignore` |
| Search engine indexing | `blog_public = 0` set automatically on activation (dev/staging) |
| nonce protection | All admin form actions use `wp_nonce_field()` / `check_admin_referer()` |
