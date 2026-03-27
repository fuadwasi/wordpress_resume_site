# Fuad Hasan — Dynamic Portfolio (WordPress)

> **Converting a static HTML portfolio into a fully dynamic, admin-managed WordPress site.**

Live static reference: [resume-to-site-magic-70.lovable.app](https://resume-to-site-magic-70.lovable.app/)

---

## Project Overview

This repository contains the custom WordPress theme **`fuadhasan-portfolio`** that powers
Fuad Hasan's personal portfolio website. Fuad is a Senior Software Engineer II at Brain Station 23 PLC,
a Certified NopCommerce Developer, and an open-source contributor with 3+ years of B2B/B2C
eCommerce experience.

### Why WordPress?

The static HTML version already had a great look & feel. WordPress adds:

| Feature | Benefit |
|---------|---------|
| Custom Post Types (CPT) | Experience, Projects, Skills, Achievements — all admin-editable |
| ACF Options Page | Hero text, profile photo, CV file — no code edits needed |
| CV Download handler | Upload a new PDF in the admin → download URL updates site-wide |
| Contact form | WPForms / Contact Form 7 — fully managed, spam-protected |
| SEO plugin | Yoast / Rank Math — structured data, sitemaps, per-page meta |
| Caching | WP Super Cache / W3 Total Cache — sub-2-second page loads |

### Target Audience

| Audience | Primary Goal |
|----------|-------------|
| Recruiters & Hiring Managers | Quick overview, downloadable CV, contact form |
| CTOs / Engineering Leads | Technical depth — projects, tech stack, open-source contributions |
| Clients | Portfolio credibility, project references, easy contact |

### Core Goals

- **Strong first impression** — Hero section with name, title, and clear CTAs
- **Easy CV access** — Always-latest downloadable resume PDF served from `/download-cv`
- **Structured experience** — Timeline-style work history, fully admin-editable
- **100 % manageable** — No code changes needed to update any content

---

## Tech Stack

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
| Images | Smush / ShortPixel (WebP conversion) |
| Server | Ubuntu 22.04 + Nginx + PHP-FPM |
| CDN | Cloudflare (free tier) |

---

## Site Architecture

```
/                    → Home       (Hero, Summary, Skills, Achievements, Featured Projects)
/about               → About      (Full summary, career focus, coding profiles)
/experience          → Experience (Timeline: Brain Station 23 progression + BSSIT)
/projects            → Projects   (Grid / card layout with category filter)
/skills              → Skills     (Categorised skill sets with proficiency levels)
/achievements        → Achievements (Certifications, awards, competitive programming)
/contact             → Contact    (Form, email, phone, social links)
/download-cv         → CV Download (always serves the latest active PDF)
/blog                → Blog       (Optional — technical posts)
```

Navigation: **Home | About | Experience | Projects | Skills | Achievements | Contact**  
Hero CTAs: **\[Download CV\]** · **\[Contact Me\]**  
Footer: GitHub · LinkedIn · Email · Codeforces

---

## Custom Post Types

| CPT Slug | Label | Key Fields |
|----------|-------|-----------|
| `experience` | Work Experience | company, role, dates, description, tech stack, contributions |
| `project` | Projects | title, description, tech, live URL, GitHub URL, is_featured |
| `skill` | Skills | name, category, proficiency level (1–5), icon |
| `achievement` | Achievements | title, type, issuing org, date, credential URL |

All content is editable from **WordPress Admin → Posts → \[CPT name\]**.  
Site-wide settings (hero text, profile photo, active CV, contact info) live in
**Admin → Site Settings** (ACF Options Page).

---

## Quick Start (Local Development)

### Prerequisites

- [Local by Flywheel](https://localwp.com/) **or** XAMPP / Lando / Docker
- PHP 8.2+
- MySQL 8.x / MariaDB 10.6+
- WordPress 6.x installed and running
- [ACF Pro](https://www.advancedcustomfields.com/pro/) licence

### Steps

```bash
# 1. Clone this repo into your WordPress theme folder
cd /path/to/wordpress/wp-content/themes/
git clone https://github.com/fuadwasi/wordpress_resume_site.git
# (or copy the fuadhasan-portfolio folder from wp-content/themes/ of this repo)

# 2. Activate the theme
# WordPress Admin → Appearance → Themes → Fuad Hasan Portfolio → Activate

# 3. Install required plugins (see docs/plugins.md)
# ACF Pro, WPForms Lite, Yoast SEO, WP Super Cache, Wordfence

# 4. Flush rewrite rules
# WordPress Admin → Settings → Permalinks → Save Changes

# 5. Import ACF field groups
# Custom Fields → Tools → Import → upload wp-content/themes/fuadhasan-portfolio/inc/acf-fields.json
# (or they auto-register via acf-fields.php on theme activation)

# 6. Set up the Options Page
# Admin → Site Settings → fill in all fields

# 7. Create required pages with exact slugs:
#   about, experience, projects, skills, achievements, contact, download-cv
```

---

## Folder Structure

```
wp-content/themes/fuadhasan-portfolio/
├── style.css                      # Theme declaration + CSS design tokens
├── functions.php                  # Theme bootstrap (loads all inc/ files)
├── index.php                      # WordPress fallback template
├── front-page.php                 # Home page
├── page.php                       # Generic page
├── 404.php                        # 404 error page
├── sidebar.php                    # Optional sidebar
├── header.php                     # Site header & primary navigation
├── footer.php                     # Footer with social links
│
├── archive-experience.php         # /experience  — timeline view
├── single-experience.php          # /experience/{slug}
├── archive-project.php            # /projects — card grid
├── single-project.php             # /projects/{slug} — detail page
│
├── page-about.php                 # /about
├── page-skills.php                # /skills
├── page-achievements.php          # /achievements
├── page-contact.php               # /contact
├── page-download-cv.php           # /download-cv
│
├── template-parts/
│   ├── home/
│   │   ├── hero.php               # Hero section
│   │   ├── summary.php            # Professional summary
│   │   ├── skills-overview.php    # Skills snapshot
│   │   ├── achievements-bar.php   # Key achievements strip
│   │   └── featured-projects.php  # Featured projects grid
│   ├── experience/
│   │   └── timeline-item.php      # Single timeline entry
│   ├── project/
│   │   └── project-card.php       # Project card component
│   ├── skill/
│   │   └── skill-group.php        # Skill category group
│   └── shared/
│       ├── section-header.php     # Reusable section heading
│       └── cta-buttons.php        # Call-to-action buttons
│
├── assets/
│   ├── css/
│   │   ├── main.css               # Design tokens + global reset
│   │   ├── hero.css               # Hero section styles
│   │   ├── timeline.css           # Experience timeline
│   │   └── responsive.css         # Breakpoint overrides
│   ├── js/
│   │   ├── main.js                # Core interactions
│   │   ├── smooth-scroll.js       # Smooth anchor scrolling
│   │   └── skills-animation.js    # Skill bar / counter animations
│   └── images/
│       └── placeholder/           # Default placeholder images
│
└── inc/
    ├── custom-post-types.php      # Register CPTs + taxonomies
    ├── acf-fields.php             # ACF field group definitions
    ├── acf-options.php            # ACF options page
    ├── enqueue.php                # Enqueue CSS & JS assets
    ├── cv-download.php            # /download-cv rewrite & handler
    └── helpers.php                # Utility / template helper functions
```

---

## Development Plan

See [`Documents/wordpress_portfolio_development_plan.md`](Documents/wordpress_portfolio_development_plan.md)
for the full 7-phase development plan, acceptance criteria, plugin list, and content migration guide.

---

## Licence

Private / proprietary — all rights reserved by Fuad Hasan.
