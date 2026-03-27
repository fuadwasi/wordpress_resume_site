# Fuad Hasan — WordPress Portfolio Site

A high-performance, recruiter-focused personal portfolio built on WordPress.

---

## 🚀 Quick Start (Docker)

> **Requirements:** [Docker Desktop](https://docs.docker.com/get-docker/) (includes Docker Compose v2)

```bash
# 1. Clone the repository
git clone https://github.com/fuadwasi/wordpress_resume_site.git
cd wordpress_resume_site

# 2. Run the setup script (copies .env, starts containers)
./setup.sh
```

That's it! The script will:
- Create a `.env` file from `.env.example`
- Start WordPress, MySQL and phpMyAdmin via Docker Compose
- Wait for WordPress to be ready and print all URLs

### URLs

| Service          | URL                                    |
|------------------|----------------------------------------|
| 🌐 Portfolio site | http://localhost:8080                  |
| 🔧 WP Admin       | http://localhost:8080/wp-admin         |
| 🗄️ phpMyAdmin     | http://localhost:8081                  |

---

## ⚙️ Manual Setup (step-by-step)

```bash
# Copy environment file
cp .env.example .env

# Start all services
docker compose up -d

# View logs
docker compose logs -f wordpress
```

### Stop / restart

```bash
docker compose down          # stop containers (data is preserved)
docker compose down -v       # stop AND delete all data volumes
docker compose restart       # restart all containers
```

---

## 🔑 First-Time WordPress Install

1. Open http://localhost:8080/wp-admin/install.php
2. Choose language → click **Continue**
3. Fill in site title, admin username/password, and email
4. Click **Install WordPress**
5. Log in and go to **Appearance → Themes**
6. Activate the **Fuad Hasan Portfolio** theme
7. Set a static front page: **Settings → Reading → Your homepage displays → A static page → Front Page**

### Recommended Plugins

Install from **Plugins → Add New**:

| Plugin | Purpose |
|--------|---------|
| [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/) | Powers the Portfolio Settings options page and all CPT meta fields |
| [WPForms Lite](https://wordpress.org/plugins/wpforms-lite/) | Contact form |
| [Yoast SEO](https://wordpress.org/plugins/wordpress-seo/) | SEO |
| [WP Super Cache](https://wordpress.org/plugins/wp-super-cache/) | Page caching |

---

## 📁 Project Structure

```
wordpress_resume_site/
├── docker-compose.yml          ← Docker Compose orchestration
├── .env.example                ← Environment variable template
├── setup.sh                    ← One-command setup script
├── wp-content/
│   ├── themes/
│   │   └── fuadhasan-portfolio/   ← Custom portfolio theme
│   │       ├── style.css          ← Theme stylesheet + metadata
│   │       ├── functions.php      ← Theme bootstrap
│   │       ├── front-page.php     ← Homepage template
│   │       ├── index.php          ← Fallback template
│   │       ├── page.php           ← Page template
│   │       ├── header.php         ← Site header / nav
│   │       ├── footer.php         ← Site footer
│   │       ├── inc/
│   │       │   ├── cpt.php        ← Custom post types (Experience, Project, Skill, Achievement)
│   │       │   ├── acf-fields.php ← ACF field group definitions
│   │       │   ├── helpers.php    ← Template helper functions
│   │       │   └── shortcodes.php ← [fhp_projects] and [fhp_skills] shortcodes
│   │       └── assets/
│   │           └── js/main.js     ← Smooth scroll & nav scripts
│   └── plugins/                   ← Plugins are installed via WP Admin
└── Documents/
    └── portfolio_requirements.txt ← Full project requirements
```

---

## 🎨 Theme Features

| Feature | Details |
|---------|---------|
| **Custom Post Types** | Experience, Project, Skill, Achievement — all manageable from WP Admin |
| **ACF Integration** | Structured fields for each CPT (role, dates, tech stack, URLs, etc.) |
| **Portfolio Settings** | ACF Options Page for site-wide data (name, title, email, CV file, social links) |
| **Shortcodes** | `[fhp_projects]`, `[fhp_skills]` for embedding content anywhere |
| **Dark theme** | GitHub-inspired dark colour palette |
| **Mobile-first** | Responsive layout using CSS Grid / Flexbox |
| **SEO-ready** | `title-tag` support, schema-friendly HTML5 semantics |

---

## 🌍 Environment Variables

Copy `.env.example` to `.env` and adjust as needed:

```ini
# MySQL
MYSQL_ROOT_PASSWORD=rootpassword
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=wordpress

# WordPress (must match MySQL values above)
WORDPRESS_TABLE_PREFIX=wp_

# Ports
# WP_PORT=8080      (set in docker-compose.yml, change there if needed)
PMA_PORT=8081
```

---

## 📋 Requirements

- Docker Desktop ≥ 4.x (Docker Engine ≥ 24, Compose v2)
- Any modern browser

No PHP, MySQL, or Nginx installation needed — everything runs in Docker.

---

## 📄 Documents

- [`Documents/portfolio_requirements.txt`](Documents/portfolio_requirements.txt) — Full site requirements
- [`Documents/Fuad Hasan Resume Senior Software Engineer.pdf`](Documents/Fuad%20Hasan%20Resume%20Senior%20Software%20Engineer.pdf) — CV
