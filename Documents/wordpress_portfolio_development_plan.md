# WordPress Portfolio Development Plan
## Fuad Hasan — Senior Software Engineer | Dynamic Portfolio Website

**Prepared:** March 2026  
**Target Site:** https://resume-to-site-magic-70.lovable.app/ (static reference)  
**Target Stack:** WordPress + PHP 8.x + MySQL/MariaDB  
**Owner:** Fuad Hasan — Senior Software Engineer II, Brain Station 23 PLC

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [Site Architecture & Page Map](#3-site-architecture--page-map)
4. [Custom Post Types & ACF Field Groups](#4-custom-post-types--acf-field-groups)
5. [Theme Development Plan](#5-theme-development-plan)
6. [Plugin List & Configuration](#6-plugin-list--configuration)
7. [CV Download Feature](#7-cv-download-feature)
8. [Admin Panel Capabilities](#8-admin-panel-capabilities)
9. [Content Migration Plan](#9-content-migration-plan)
10. [SEO & Performance](#10-seo--performance)
11. [Development Phases & Timeline](#11-development-phases--timeline)
12. [Acceptance Criteria](#12-acceptance-criteria)
13. [Optional / Future Enhancements](#13-optional--future-enhancements)

---

## 1. Project Overview

### Objective
Convert the existing static HTML portfolio (hosted at resume-to-site-magic-70.lovable.app) into a **fully dynamic, WordPress-powered website** that allows Fuad Hasan to manage all content — experience, projects, skills, achievements, and CV — through a user-friendly admin panel, without touching code.

### Target Audience
| Audience | Primary Goal |
|----------|-------------|
| Recruiters & Hiring Managers | Quick overview, downloadable CV, contact form |
| CTOs / Engineering Leads | Technical depth — project details, tech stack, open-source contributions |
| Clients | Portfolio credibility, project references, contact |

### Core Goals
- **Strong first impression** — hero section with name, title, and clear CTAs
- **Easy CV access** — always-latest downloadable resume PDF
- **Structured experience** — timeline-style work history, fully admin-editable
- **100% manageable via WordPress admin** — no code changes needed for content updates

---

## 2. Tech Stack

| Layer | Technology |
|-------|-----------|
| CMS | WordPress (latest stable, 6.x) |
| Backend | PHP 8.2+ |
| Database | MySQL 8.x / MariaDB 10.6+ |
| Template Engine | WordPress Block Theme or Classic Child Theme |
| Fields | Advanced Custom Fields (ACF) Pro |
| Forms | WPForms Lite or Contact Form 7 |
| SEO | Yoast SEO or Rank Math |
| Cache | WP Super Cache or W3 Total Cache |
| Security | Wordfence or iThemes Security |
| Deployment | Git + WP CLI or cPanel / VPS |
| Hosting | Recommended: SiteGround, Cloudways, or DigitalOcean (Ubuntu + Nginx) |

---

## 3. Site Architecture & Page Map

```
/                    → Home (Hero, Summary, Skills, Achievements, Featured Projects)
/about               → About (Full summary, career focus, coding profiles)
/experience          → Experience (Timeline: Brain Station 23, BSSIT)
/projects            → Projects (Grid/Card layout with filters)
/skills              → Skills (Categorized skill sets)
/achievements        → Achievements (Certifications, awards, competitive programming)
/contact             → Contact (Form, email, phone, social links)
/download-cv         → CV Download (always-latest PDF)
/blog                → (Optional) Blog posts
```

### Navigation Structure
```
Primary Nav: Home | About | Experience | Projects | Skills | Achievements | Contact
CTA Buttons (Hero): [Download CV]  [Contact Me]
Footer Links: GitHub | LinkedIn | Email | Codeforces
```

---

## 4. Custom Post Types & ACF Field Groups

### 4.1 Custom Post Type: `experience`
**Label:** Work Experience  
**Slug:** `/experience`

#### ACF Field Group: `experience_details`
| Field Name | Type | Description |
|-----------|------|-------------|
| `company_name` | Text | e.g., Brain Station 23 PLC |
| `company_logo` | Image | Company logo upload |
| `company_url` | URL | Company website link |
| `job_title` | Text | e.g., Senior Software Engineer II |
| `employment_type` | Select | Full-time / Part-time / Contract / Freelance |
| `start_date` | Date Picker | Start date of role |
| `end_date` | Date Picker | End date (leave blank if current) |
| `is_current` | True/False | Mark as current position |
| `location` | Text | e.g., Dhaka, Bangladesh |
| `job_description` | Wysiwyg | Full role description |
| `key_contributions` | Repeater | Bullet list of key achievements |
| `technologies_used` | Taxonomy | Links to Skills taxonomy |
| `project_references` | Repeater | Name + URL pairs |
| `order` | Number | Manual display order |

**Pre-populated Entries:**
1. **Senior Software Engineer II** — Brain Station 23 PLC (Jan 2026–Present)
2. **Senior Software Engineer I** — Brain Station 23 PLC (Jul 2024–Dec 2025)
3. **Software Engineer II** — Brain Station 23 PLC (Jul 2023–Jun 2024)
4. **Software Engineer** — Brain Station 23 PLC (Jul 2022–Aug 2023)
5. **Associate Software Engineer** — Brain Station 23 PLC (Jul 2021–Jun 2022)
6. **Software Engineering Trainee** — Brain Station 23 PLC (Mar–Jun 2021)
7. **Junior Software Engineer** — BSSIT (Jan 2021)

---

### 4.2 Custom Post Type: `project`
**Label:** Projects  
**Slug:** `/projects/{slug}`

#### ACF Field Group: `project_details`
| Field Name | Type | Description |
|-----------|------|-------------|
| `project_title` | Text | Project name |
| `project_thumbnail` | Image | Featured image / screenshot |
| `short_description` | Textarea | 1–2 sentence summary |
| `full_description` | Wysiwyg | Full details |
| `technologies` | Taxonomy | Links to Skills taxonomy |
| `live_url` | URL | Live site link |
| `github_url` | URL | Source code link |
| `client_company` | Text | Client or employer |
| `project_type` | Select | B2B eCommerce / B2C eCommerce / Plugin / POS / CMS / Open Source |
| `is_featured` | True/False | Show on Home page |
| `order` | Number | Manual ordering |

**Pre-populated Projects (from resume):**
1. Shawpno eCommerce Platform (microservices, gRPC, RabbitMQ, MongoDB)
2. Macsteel B2B eCommerce — South Africa (NopCommerce + SAP ERP)
3. NopCommerce Core Contributions (GitHub: fuadhasan28)
4. Online POS System (NopCommerce-based, 50+ retail stores)
5. Delivery Management System (DMS) Plugin
6. NopCommerce Plugins Suite (WebApi, TaxJar, ZohoCRM, Copy&Pay, etc.)
7. TARBIYAH CMS (ASP.NET, bKash, SSLCommerz)

---

### 4.3 Custom Post Type: `skill`
**Label:** Skills  
**Slug:** N/A (taxonomy-based display)

#### ACF Field Group: `skill_details`
| Field Name | Type | Description |
|-----------|------|-------------|
| `skill_name` | Text | Skill label |
| `skill_category` | Select | Backend / Frontend / Database / DevOps / Architecture / Soft Skills |
| `proficiency_level` | Range (1–5) | Visual indicator |
| `skill_icon` | Image | Optional icon/logo |
| `order` | Number | Display order within category |

**Skill Categories & Pre-populated Skills:**

**Backend:**  
C#, ASP.NET Core, REST APIs, NopCommerce, Microservices Architecture, gRPC, RabbitMQ, Entity Framework, AJAX

**Frontend:**  
Angular, Next.js, JavaScript, jQuery, HTML5, CSS3

**Database:**  
MSSQL, MongoDB, MySQL

**DevOps & Cloud:**  
Azure DevOps, Azure CI/CD, Linux, Firebase

**Integrations:**  
SAP ERP, ZohoCRM, TaxJar, Payment Gateways (SSLCommerz, bKash, Nagad, CyberSource, HyperPay Copy&Pay, City Bank), SMS Gateway, Push Notifications, Barcode Integration, Google Analytics, Sendinblue

**Soft Skills:**  
Team Leadership, Project Management (Sprint Planning), Client-facing Communication, International Team Collaboration, Scrum/Agile

**Competitive Programming:**  
C++, Algorithms, Data Structures (Codeforces: fhwasi)

---

### 4.4 Custom Post Type: `achievement`
**Label:** Achievements  
**Slug:** `/achievements`

#### ACF Field Group: `achievement_details`
| Field Name | Type | Description |
|-----------|------|-------------|
| `achievement_title` | Text | e.g., NopCommerce Certified Developer |
| `achievement_type` | Select | Certification / Award / Contest / Volunteer |
| `issuing_organization` | Text | e.g., NopCommerce, ICPC |
| `issue_date` | Date Picker | When awarded/issued |
| `credential_url` | URL | Verification link |
| `description` | Textarea | Optional details |
| `achievement_icon` | Image | Badge or logo |

**Pre-populated Achievements:**
1. NopCommerce Certified Developer (https://www.nopcommerce.com/en/fuad-hasan)
2. Prompt Engineering Foundations — Brain Station 23
3. ICPC Dhaka Regional Onsite Contestant — Rank 96/300
4. DIU Take-Off Programming Contest Champion — Rank 1/300, Summer 2017
5. Programming for Everybody (Python) — Coursera
6. Digital Assets Security Awareness

---

### 4.5 Options Page (Site-Wide Settings)
**Using ACF Options Page**

| Field Name | Type | Description |
|-----------|------|-------------|
| `hero_name` | Text | Full name display |
| `hero_title` | Text | Professional title |
| `hero_subtitle` | Textarea | Tagline or summary |
| `hero_background_image` | Image | Hero section background |
| `profile_photo` | Image | Professional headshot |
| `email_address` | Email | fhassanwasi@gmail.com |
| `phone_number` | Text | +880 01792 478 378 |
| `github_url` | URL | https://github.com/fuadwasi |
| `linkedin_url` | URL | https://www.linkedin.com/in/fuadwasi/ |
| `codeforces_url` | URL | https://codeforces.com/profile/fhwasi |
| `location` | Text | Dhaka, Bangladesh |
| `active_cv_file` | File | Currently active CV PDF |
| `cv_download_label` | Text | Button label, e.g., "Download CV" |
| `footer_tagline` | Text | Footer custom text |
| `years_of_experience` | Number | Auto-calculated or manual |
| `total_projects` | Number | Projects count for stats |
| `open_source_contributions` | Number | GitHub contribution count |

---

## 5. Theme Development Plan

### 5.1 Approach
**Recommended:** Develop a **custom child theme** based on a minimal, accessible parent theme (e.g., GeneratePress, Astra, or a blank starter like Underscores `_s`).

The design will **replicate the look & feel of the static site** at resume-to-site-magic-70.lovable.app, converting hard-coded content into dynamic WordPress template tags.

### 5.2 Theme File Structure
```
fuadhasan-portfolio/
├── style.css                    # Theme metadata + base styles
├── functions.php                # Enqueue scripts/styles, register CPTs, ACF init
├── index.php                    # Fallback template
├── front-page.php               # Home page template
├── page.php                     # Generic page template
├── single-experience.php        # Single experience post
├── archive-experience.php       # Experience list/timeline
├── single-project.php           # Single project detail
├── archive-project.php          # Projects grid
├── page-about.php               # About page template
├── page-skills.php              # Skills page template
├── page-achievements.php        # Achievements page template
├── page-contact.php             # Contact page template
├── page-download-cv.php         # CV download redirect
├── header.php                   # Site header & navigation
├── footer.php                   # Footer with social links
├── sidebar.php                  # (Optional) sidebar
├── 404.php                      # 404 page
├── template-parts/
│   ├── home/
│   │   ├── hero.php             # Hero section
│   │   ├── summary.php          # Professional summary
│   │   ├── skills-overview.php  # Skills snapshot
│   │   ├── achievements-bar.php # Key achievements strip
│   │   └── featured-projects.php # Featured projects grid
│   ├── experience/
│   │   └── timeline-item.php    # Single timeline entry
│   ├── project/
│   │   └── project-card.php     # Project card component
│   ├── skill/
│   │   └── skill-group.php      # Skill category group
│   └── shared/
│       ├── section-header.php   # Reusable section heading
│       └── cta-buttons.php      # Call-to-action buttons
├── assets/
│   ├── css/
│   │   ├── main.css             # Main compiled stylesheet
│   │   ├── hero.css
│   │   ├── timeline.css
│   │   └── responsive.css       # Media queries
│   ├── js/
│   │   ├── main.js              # Core interactions
│   │   ├── smooth-scroll.js
│   │   └── skills-animation.js  # Skill bar/progress animations
│   └── images/
│       └── placeholder/         # Default placeholder images
└── inc/
    ├── custom-post-types.php    # CPT registrations
    ├── acf-fields.php           # ACF field group registrations (export)
    ├── acf-options.php          # ACF options page
    ├── enqueue.php              # Asset enqueue logic
    ├── cv-download.php          # CV download handler
    └── helpers.php              # Utility functions
```

### 5.3 Design Tokens (from static site)
Match the visual design of the static portfolio:

```css
/* Colors */
--color-bg-primary: #0a0a0f;        /* Deep dark background */
--color-bg-secondary: #12121a;      /* Card/section background */
--color-accent: #6c63ff;            /* Purple accent (primary CTA) */
--color-accent-secondary: #00d4ff;  /* Cyan accent */
--color-text-primary: #ffffff;      /* White text */
--color-text-secondary: #a0aec0;    /* Gray secondary text */
--color-border: rgba(255,255,255,0.1);

/* Typography */
--font-primary: 'Inter', sans-serif;
--font-mono: 'Fira Code', monospace; /* For code/tech labels */
--font-size-hero: clamp(2.5rem, 5vw, 4rem);
--font-size-section: 2rem;

/* Spacing */
--spacing-section: 5rem;
--border-radius: 12px;
--card-shadow: 0 4px 24px rgba(108, 99, 255, 0.15);
```

---

## 6. Plugin List & Configuration

| Plugin | Purpose | Configuration Notes |
|--------|---------|---------------------|
| **Advanced Custom Fields (ACF) Pro** | Custom fields for all CPTs + Options Page | Export field groups as PHP code for version control |
| **WPForms Lite** or **Contact Form 7** | Contact form on `/contact` page | Email notifications → fhassanwasi@gmail.com |
| **Yoast SEO** or **Rank Math** | On-page SEO, meta tags, sitemaps | Configure schema: Person, Portfolio, WebSite |
| **WP Super Cache** or **W3 Total Cache** | Page caching for <2s load time | Enable on all public pages |
| **Wordfence Security** | Brute force protection, firewall | Enable 2FA for admin login |
| **UpdraftPlus** | Automated database + file backups | Daily backups to Google Drive / S3 |
| **Smush** or **ShortPixel** | Image compression & WebP conversion | Auto-compress on upload |
| **Redirection** | URL management, 301 redirects | Map static routes to WP permalinks |
| **WP Migrate DB** (Dev only) | Database migration between environments | Dev → Staging → Production |
| **Query Monitor** (Dev only) | Debug ACF queries and template loading | Disable on production |
| **GitHub Updater** (Optional) | Auto-update theme from GitHub repo | Link to `fuadwasi/wordpress_resume_site` |

---

## 7. CV Download Feature

### Implementation: Option A + Option B Hybrid (Recommended)

#### Frontend
- **Download CV button** in Hero section (Home page)
- **Download CV page** at `/download-cv` → redirects to PDF file
- Button triggers a direct file download (not browser preview)
- Shows current CV filename and last-updated date

#### Backend (WordPress Admin)
- **ACF Options Page field:** `active_cv_file` (File type, restricted to PDF)
- Admin uploads new CV PDF via Media Library
- Selects the newly uploaded file in the Options Page field
- The CV download URL automatically updates across all pages

#### CV Download Handler (`inc/cv-download.php`)
```php
// Flush rewrite rules on activation
add_action('init', function() {
    add_rewrite_rule('^download-cv/?$', 'index.php?cv_download=1', 'top');
});

add_filter('query_vars', function($vars) {
    $vars[] = 'cv_download';
    return $vars;
});

add_action('template_redirect', function() {
    if (get_query_var('cv_download')) {
        $cv_url = get_field('active_cv_file', 'option');
        if ($cv_url) {
            $file_path = get_attached_file(attachment_url_to_postid($cv_url['url']));
            if ($file_path && file_exists($file_path)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="Fuad_Hasan_CV.pdf"');
                header('Content-Length: ' . filesize($file_path));
                readfile($file_path);
                exit;
            }
        }
        wp_redirect(home_url('/'));
        exit;
    }
});
```

#### Security Considerations
- Restrict direct access to `wp-content/uploads/cv/` via `.htaccess`
- Only serve CV through the `/download-cv` handler
- Log download count (optional: custom post meta counter)

---

## 8. Admin Panel Capabilities

After development, Fuad can manage the following from `/wp-admin` without touching code:

| Section | How to Manage |
|---------|--------------|
| **Hero Name & Title** | Options Page → "Site Settings" |
| **Profile Photo** | Options Page → Upload new photo |
| **Professional Summary** | Options Page → Hero subtitle / About page content |
| **Work Experience** | Posts → Experience → Add/Edit/Delete entries |
| **Projects** | Posts → Projects → Add/Edit/Delete, set as featured |
| **Skills** | Posts → Skills → Add skill, assign category, set level |
| **Achievements** | Posts → Achievements → Add certifications/awards |
| **CV/Resume** | Options Page → Upload new PDF → set as active |
| **Contact Info** | Options Page → Email, phone, social links |
| **Contact Form** | WPForms / CF7 form builder (no code needed) |
| **SEO Meta** | Per-page via Yoast SEO / Rank Math widget |
| **Navigation Menu** | Appearance → Menus |
| **Blog Posts** | (Optional) Posts → Add Post |

---

## 9. Content Migration Plan

### Phase 1: Initial Data Population
All content from the resume and LinkedIn profile will be pre-loaded:

#### Personal Information
- **Name:** Fuad Hasan
- **Current Title:** Senior Software Engineer II
- **Company:** Brain Station 23 PLC
- **Location:** Dhaka, Bangladesh
- **Email:** fhassanwasi@gmail.com
- **Phone:** +880 01792 478 378
- **GitHub:** https://github.com/fuadwasi
- **LinkedIn:** https://www.linkedin.com/in/fuadwasi/
- **Codeforces:** https://codeforces.com/profile/fhwasi

#### Professional Summary
> "Certified NopCommerce Developer and Senior Software Engineer with 3+ years of experience designing and scaling B2B & B2C eCommerce platforms, ERP integrations, and enterprise-grade plugins. Skilled in ASP.NET Core, C#, and modern microservices architecture, I help businesses improve operations and customer experience through robust, future-ready solutions."

#### Key Achievements (for Hero/Summary section)
- Integrated SAP ERP for Macsteel — real-time sync across 10,000+ SKUs
- Led POS system development used in 50+ retail stores
- NopCommerce core contributor (Facebook auth, MFA, discount management)
- Built Shawpno microservices — 30% checkout latency reduction
- ICPC Dhaka Regional Contestant (Rank 96/300)
- DIU Take-Off Programming Contest Champion (Rank 1/300)

---

## 10. SEO & Performance

### SEO Configuration
```
Schema Types to enable:
- Person (Fuad Hasan)
- WebPage per page
- ItemList for projects
- BreadcrumbList for inner pages

Meta titles:
/ → "Fuad Hasan | Senior Software Engineer | NopCommerce Expert"
/about → "About Fuad Hasan | ASP.NET Core Developer | Bangladesh"
/experience → "Work Experience | Fuad Hasan | Brain Station 23"
/projects → "Projects Portfolio | Fuad Hasan | eCommerce & ERP"
/contact → "Contact Fuad Hasan | Senior Software Engineer"
```

### Performance Targets
| Metric | Target |
|--------|--------|
| Page Load Time | < 2 seconds |
| Google PageSpeed (Mobile) | > 85 |
| Google PageSpeed (Desktop) | > 95 |
| Core Web Vitals LCP | < 2.5s |
| Core Web Vitals CLS | < 0.1 |
| Core Web Vitals FID/INP | < 200ms |

### Performance Checklist
- [ ] Enable browser caching (Cache-Control headers)
- [ ] Gzip / Brotli compression on server
- [ ] Minify CSS & JS (via cache plugin)
- [ ] Serve images as WebP (Smush/ShortPixel)
- [ ] Lazy-load images below the fold
- [ ] Use system fonts or preload Google Fonts
- [ ] Enable CDN (Cloudflare free tier recommended)
- [ ] Optimize database queries (limit ACF nested loops)
- [ ] Preload critical CSS for above-the-fold content

---

## 11. Development Phases & Timeline

### Phase 1 — Environment Setup (Days 1–2)
- [ ] Provision WordPress hosting (local XAMPP/Local WP for dev)
- [ ] Install WordPress (latest), configure wp-config.php
- [ ] Set up Git repository structure (`fuadwasi/wordpress_resume_site`)
- [ ] Install and activate required plugins (ACF Pro, Yoast, WPForms, Cache)
- [ ] Configure permalink structure: `/%postname%/`
- [ ] Set up staging environment

### Phase 2 — Custom Post Types & ACF Fields (Days 3–5)
- [ ] Register CPT: `experience` (in `inc/custom-post-types.php`)
- [ ] Register CPT: `project`
- [ ] Register CPT: `skill`
- [ ] Register CPT: `achievement`
- [ ] Create ACF field group: `experience_details`
- [ ] Create ACF field group: `project_details`
- [ ] Create ACF field group: `skill_details`
- [ ] Create ACF field group: `achievement_details`
- [ ] Create ACF Options Page: `site_settings`
- [ ] Export all ACF field groups as PHP code (for version control)

### Phase 3 — Theme Development (Days 6–15)
- [ ] Set up theme boilerplate (`fuadhasan-portfolio`)
- [ ] Build `header.php` — responsive navigation with mobile hamburger menu
- [ ] Build `footer.php` — social links, copyright, back-to-top
- [ ] Build `front-page.php` — Home page layout
  - [ ] Hero section (name, title, CTA buttons, profile photo)
  - [ ] Summary section
  - [ ] Skills overview (progress bars / skill chips)
  - [ ] Key achievements strip
  - [ ] Featured projects grid (3–4 items)
  - [ ] Call-to-action section
- [ ] Build `archive-experience.php` — timeline layout
- [ ] Build `archive-project.php` — filterable grid
- [ ] Build `page-about.php`
- [ ] Build `page-skills.php` — categorized skill groups
- [ ] Build `page-achievements.php`
- [ ] Build `page-contact.php` — form + contact info
- [ ] Build `page-download-cv.php` — CV download handler
- [ ] Add JavaScript: smooth scroll, skill animations, project filter
- [ ] Make all sections responsive (mobile-first, breakpoints: 576/768/1024/1280px)

### Phase 4 — Content Population (Days 16–18)
- [ ] Add all work experience entries (7 roles)
- [ ] Add all projects (7+ projects with descriptions and tech tags)
- [ ] Add all skills (organized by category)
- [ ] Add all achievements and certifications
- [ ] Upload CV PDF and set as active in Options Page
- [ ] Fill in all Options Page fields (bio, contact, social links)
- [ ] Add profile photo to Options Page
- [ ] Create static pages: About, Contact, Skills, Achievements

### Phase 5 — Testing & QA (Days 19–21)
- [ ] Cross-browser testing (Chrome, Firefox, Safari, Edge)
- [ ] Mobile responsiveness testing (iPhone SE, iPhone 14, Samsung Galaxy, iPad)
- [ ] CV download test — verify correct file served, correct filename
- [ ] Contact form test — verify email delivery
- [ ] Admin panel test — add/edit/delete each CPT, verify changes on frontend
- [ ] SEO audit (Yoast / Rank Math) — fix all meta titles and descriptions
- [ ] Performance audit — Google PageSpeed Insights
- [ ] Security scan (Wordfence) — fix any vulnerabilities
- [ ] 404 page verification
- [ ] Accessibility check (WCAG 2.1 AA minimum)

### Phase 6 — Deployment (Days 22–23)
- [ ] Set up production server (recommended: Ubuntu 22.04 + Nginx + PHP 8.2 + MySQL 8)
- [ ] Configure SSL certificate (Let's Encrypt / Cloudflare)
- [ ] Deploy theme and plugins via WP CLI or SFTP
- [ ] Migrate database from dev to production (WP Migrate DB)
- [ ] Configure Cloudflare CDN (free plan)
- [ ] Set up automated daily backups (UpdraftPlus → cloud storage)
- [ ] Configure caching for production
- [ ] Test all features on live server
- [ ] Submit sitemap to Google Search Console

### Phase 7 — Post-Launch (Days 24–30)
- [ ] Monitor Google Search Console for indexing issues
- [ ] Install Google Analytics 4 (GA4) or connect via Rank Math
- [ ] Train Fuad on admin panel usage:
  - How to update CV
  - How to add new projects
  - How to add new experience
  - How to update skills
- [ ] Create admin usage documentation (simple PDF guide)
- [ ] Set up uptime monitoring (UptimeRobot free tier)

---

## 12. Acceptance Criteria

| Criteria | Status |
|----------|--------|
| CV is downloadable with correct filename from `/download-cv` | ✅ Required |
| Admin can upload a new CV and it replaces the old one across the site | ✅ Required |
| All sections (experience, projects, skills, achievements) are editable from WP admin | ✅ Required |
| Site is fully responsive on mobile, tablet, and desktop | ✅ Required |
| Page load time < 2 seconds on 3G (measured in Google PageSpeed) | ✅ Required |
| Professional, modern design matching static reference site | ✅ Required |
| Contact form sends email to fhassanwasi@gmail.com | ✅ Required |
| SEO meta titles and descriptions configured for all pages | ✅ Required |
| HTTPS enabled on production | ✅ Required |
| Navigation works correctly across all pages | ✅ Required |
| Hero section displays name, title, and CTA buttons | ✅ Required |
| Featured projects appear on Home page (admin-selectable) | ✅ Required |
| No broken links or 404 errors on launch | ✅ Required |
| Google PageSpeed Desktop score > 90 | ✅ Required |

---

## 13. Optional / Future Enhancements

| Feature | Priority | Notes |
|---------|---------|-------|
| **Blog section** | Medium | WordPress native Posts, tagged by technology |
| **Dark mode toggle** | Low | CSS custom properties + localStorage preference |
| **GitHub integration** | Medium | GitHub REST API — show latest repos / contribution graph |
| **Analytics dashboard** | Medium | GA4 embedded or Plausible Analytics |
| **Project detail pages** | High | Full case study format with screenshots |
| **Testimonials CPT** | Medium | Client/colleague recommendations |
| **PDF Resume viewer** | Low | Embedded PDF on About/CV page (PDF.js) |
| **Multilingual** | Low | WPML or Polylang for Bengali support |
| **Skills endorsement count** | Low | Mirror LinkedIn endorsement numbers |
| **Automated GitHub stats** | Medium | Daily cron to pull star count, PR count from GitHub API |

---

## Appendix A — Directory Structure for WordPress Repository

```
wordpress_resume_site/
├── Documents/                              # Reference documents
│   ├── Fuad Hasan Resume Senior Software Engineer.pdf
│   ├── Fuad_Linkedin_Profile.pdf
│   ├── portfolio_requirements.txt
│   └── wordpress_portfolio_development_plan.md  ← THIS FILE
├── wp-content/
│   └── themes/
│       └── fuadhasan-portfolio/            # Custom theme (to be built)
│           ├── style.css
│           ├── functions.php
│           ├── front-page.php
│           ├── archive-experience.php
│           ├── archive-project.php
│           ├── page-about.php
│           ├── page-skills.php
│           ├── page-achievements.php
│           ├── page-contact.php
│           ├── page-download-cv.php
│           ├── header.php
│           ├── footer.php
│           ├── template-parts/
│           ├── assets/
│           └── inc/
├── .gitignore
└── README.md
```

---

## Appendix B — Recommended Hosting Setup

```
Server: Ubuntu 22.04 LTS
Web Server: Nginx 1.24+
PHP: 8.2 (php-fpm)
Database: MySQL 8.0 or MariaDB 10.6
Cache: Redis (object cache) + Nginx FastCGI cache
SSL: Let's Encrypt via Certbot
CDN: Cloudflare (free plan)
Backup: UpdraftPlus → Google Drive (daily)
Monitoring: UptimeRobot (free)
```

**Recommended VPS providers (cost-effective):**
- DigitalOcean Droplet ($6/mo, 1GB RAM) — use 1-click WordPress droplet
- Cloudways (managed, from $14/mo)
- SiteGround GrowBig ($3.99/mo promo)

---

*Document created based on: resume PDF, LinkedIn profile, portfolio_requirements.txt, and static site analysis at resume-to-site-magic-70.lovable.app*
