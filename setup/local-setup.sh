#!/usr/bin/env bash
# =====================================================================
# Fuad Hasan Portfolio — Local Development Setup Script (WP-CLI)
#
# Run this script from your WordPress root directory to fully
# provision a local development environment in one command.
#
# Usage:
#   cd /path/to/your/wordpress-root/
#   bash wp-content/themes/fuadhasan-portfolio/setup/local-setup.sh
#
# Requirements:
#   - WP-CLI  (https://wp-cli.org/)
#   - PHP 8.2+
#   - MySQL 8.x / MariaDB 10.6+
#   - A running database server
#
# Environment variables (all optional — sensible defaults provided):
#   FHP_DB_NAME       Database name              (default: fuad_portfolio_dev)
#   FHP_DB_USER       Database username           (default: root)
#   FHP_DB_PASS       Database password           (default: empty)
#   FHP_DB_HOST       Database host               (default: localhost)
#   FHP_DB_PREFIX     Table prefix                (default: fhp_)
#   FHP_WP_URL        Local site URL              (default: http://localhost/fuad-portfolio)
#   FHP_ADMIN_USER    WP admin username           (default: fuad_admin)
#   FHP_ADMIN_EMAIL   WP admin email              (default: fhassanwasi@gmail.com)
#   FHP_ADMIN_PASS    WP admin password           (default: auto-generated)
#
# Phase 1 tasks automated by this script:
#   ✓ Provision WordPress (download latest core)
#   ✓ Configure wp-config.php
#   ✓ Install WordPress database
#   ✓ Set permalink structure → /%postname%/
#   ✓ Install and activate required plugins
#   ✓ Activate the fuadhasan-portfolio theme
# =====================================================================

set -euo pipefail

# ---- Configuration --------------------------------------------------
DB_NAME="${FHP_DB_NAME:-fuad_portfolio_dev}"
DB_USER="${FHP_DB_USER:-root}"
DB_PASS="${FHP_DB_PASS:-}"
DB_HOST="${FHP_DB_HOST:-localhost}"
DB_PREFIX="${FHP_DB_PREFIX:-fhp_}"

WP_URL="${FHP_WP_URL:-http://localhost/fuad-portfolio}"
WP_TITLE="Fuad Hasan — Senior Software Engineer II"
WP_ADMIN_USER="${FHP_ADMIN_USER:-fuad_admin}"
WP_ADMIN_EMAIL="${FHP_ADMIN_EMAIL:-fhassanwasi@gmail.com}"
WP_ADMIN_PASS="${FHP_ADMIN_PASS:-$(openssl rand -base64 16 2>/dev/null || echo "changeme$(date +%s)")}"

THEME_DIR="wp-content/themes/fuadhasan-portfolio"

# ---- Colour helpers -------------------------------------------------
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
RED='\033[0;31m'
BOLD='\033[1m'
NC='\033[0m'

info()    { echo -e "${CYAN}[INFO]${NC}  $*"; }
success() { echo -e "${GREEN}[OK]${NC}    $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $*"; }
error()   { echo -e "${RED}[ERROR]${NC} $*" >&2; }
step()    { echo -e "\n${BOLD}──── Step $* ────${NC}"; }

# ---- Pre-flight checks ----------------------------------------------
step "0: Checking prerequisites"

if ! command -v wp &>/dev/null; then
    error "WP-CLI not found. Install from https://wp-cli.org/ and try again."
    exit 1
fi

if ! command -v php &>/dev/null; then
    error "PHP not found. Install PHP 8.2+ and try again."
    exit 1
fi

PHP_VER=$(php -r "echo PHP_VERSION_ID;" 2>/dev/null || echo 0)
if [ "$PHP_VER" -lt 80200 ]; then
    warn "PHP $(php -v | head -1) detected. PHP 8.2+ is strongly recommended."
fi

success "WP-CLI $(wp --version --allow-root 2>/dev/null | head -1) found"
success "PHP $(php -r 'echo PHP_VERSION;') found"

# ---- Step 1: Download WordPress -------------------------------------
step "1: WordPress core"

if [ -f "wp-includes/version.php" ]; then
    info "WordPress files already present — checking for updates..."
    wp core update --allow-root 2>/dev/null || info "Already at latest version"
else
    info "Downloading WordPress (latest stable)..."
    wp core download --allow-root
    success "WordPress downloaded"
fi

# ---- Step 2: Configure wp-config.php --------------------------------
step "2: wp-config.php"

if [ -f "wp-config.php" ]; then
    info "wp-config.php already exists — skipping creation"
else
    info "Creating wp-config.php from template..."

    # Generate security keys via WordPress.org API
    SALT_KEYS=$(curl -s --max-time 10 https://api.wordpress.org/secret-key/1.1/salt/ 2>/dev/null || echo "")

    wp config create \
        --dbname="${DB_NAME}" \
        --dbuser="${DB_USER}" \
        --dbpass="${DB_PASS}" \
        --dbhost="${DB_HOST}" \
        --dbprefix="${DB_PREFIX}" \
        --extra-php="$(cat << 'EXTRA'
// ---- Development settings ----------------------------------------
define( 'WP_DEBUG',          true  );
define( 'WP_DEBUG_LOG',      true  );  // log to wp-content/debug.log
define( 'WP_DEBUG_DISPLAY',  false );  // never show errors in browser
define( 'SCRIPT_DEBUG',      true  );  // use unminified JS/CSS
define( 'SAVEQUERIES',       true  );  // log DB queries for debugging
define( 'WP_POST_REVISIONS', 5     );  // limit revisions to save space
define( 'AUTOSAVE_INTERVAL', 300   );  // autosave every 5 minutes
EXTRA
)" \
        --force \
        --allow-root

    success "wp-config.php created"
fi

# ---- Step 3: Install WordPress database -----------------------------
step "3: WordPress database"

if wp core is-installed --allow-root 2>/dev/null; then
    info "WordPress already installed — skipping database install"
else
    info "Installing WordPress..."
    wp core install \
        --url="${WP_URL}" \
        --title="${WP_TITLE}" \
        --admin_user="${WP_ADMIN_USER}" \
        --admin_email="${WP_ADMIN_EMAIL}" \
        --admin_password="${WP_ADMIN_PASS}" \
        --skip-email \
        --allow-root

    success "WordPress installed"
    echo ""
    echo -e "  ${BOLD}Admin URL:${NC}      ${WP_URL}/wp-admin/"
    echo -e "  ${BOLD}Username:${NC}       ${WP_ADMIN_USER}"
    echo -e "  ${BOLD}Password:${NC}       ${WP_ADMIN_PASS}"
    echo ""
    warn "Save the admin password above — it will not be shown again."
fi

# ---- Step 4: Permalink structure ------------------------------------
step "4: Permalink structure"

info "Setting permalink structure to /%postname%/..."
wp rewrite structure '/%postname%/' --allow-root
wp rewrite flush --allow-root
success "Permalink structure: /%postname%/"

# ---- Step 5: Install & activate required plugins -------------------
step "5: Required plugins"

# Format: "slug:Display Name:required?"
PLUGINS=(
    "advanced-custom-fields:Advanced Custom Fields (free):required"
    "wordpress-seo:Yoast SEO:required"
    "wpforms-lite:WPForms Lite:required"
    "wp-super-cache:WP Super Cache:recommended"
    "wordfence:Wordfence Security:recommended"
    "updraftplus:UpdraftPlus Backups:recommended"
)

for entry in "${PLUGINS[@]}"; do
    IFS=':' read -r slug label importance <<< "${entry}"

    if wp plugin is-installed "${slug}" --allow-root 2>/dev/null; then
        info "${label} already installed"
    else
        info "Installing ${label}..."
        if wp plugin install "${slug}" --allow-root 2>/dev/null; then
            success "${label} installed"
        else
            if [ "${importance}" = "required" ]; then
                warn "${label} could not be installed automatically — install manually from WordPress.org"
            else
                info "${label} (${importance}) not installed — add manually if needed"
            fi
            continue
        fi
    fi

    wp plugin activate "${slug}" --allow-root 2>/dev/null \
        && success "${label} activated" \
        || warn "${label} could not be activated"
done

echo ""
warn "ACF Pro (paid) requires a licence key — install manually from:"
warn "  https://www.advancedcustomfields.com/pro/"
warn "  After installing, replace ACF free with ACF Pro in wp-admin."

# ---- Step 6: Activate the portfolio theme --------------------------
step "6: Theme activation"

if [ -d "${THEME_DIR}" ]; then
    info "Activating fuadhasan-portfolio theme..."
    wp theme activate fuadhasan-portfolio --allow-root
    success "Theme 'fuadhasan-portfolio' activated"
    info "Theme activation triggered setup.php — required pages created automatically."
else
    warn "Theme directory not found: ${THEME_DIR}"
    warn "Ensure this repository is cloned into wp-content/themes/:"
    warn "  cd wp-content/themes/"
    warn "  git clone https://github.com/fuadwasi/wordpress_resume_site.git"
    warn "Then re-run this script."
fi

# ---- Step 7: Flush rewrite rules -----------------------------------
step "7: Final flush"

wp rewrite flush --allow-root
success "Rewrite rules flushed"

# ---- Optional: Configure blog description --------------------------
wp option update blogdescription "Senior Software Engineer II | NopCommerce Expert | ASP.NET Core" --allow-root 2>/dev/null || true
wp option update blog_public 0 --allow-root 2>/dev/null || true   # discourage indexing in dev

# ---- Summary -------------------------------------------------------
echo ""
echo -e "${GREEN}════════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}  Phase 1 environment setup complete!${NC}"
echo -e "${GREEN}════════════════════════════════════════════════════════${NC}"
echo ""
echo -e "  ${BOLD}Site URL:${NC}       ${WP_URL}"
echo -e "  ${BOLD}Admin URL:${NC}      ${WP_URL}/wp-admin/"
echo ""
echo -e "  ${BOLD}Next steps:${NC}"
echo "  1. Install ACF Pro with your licence key (manual)"
echo "  2. Visit Admin → Site Settings → Hero & About → fill in bio & photo"
echo "  3. Visit Admin → Site Settings → CV / Resume → upload your PDF"
echo "  4. Visit Admin → Site Settings → Contact & Social → fill in links"
echo "  5. Enable WP Super Cache in Admin → Settings → WP Super Cache"
echo "  6. Configure Yoast SEO in Admin → SEO → Settings"
echo ""
