#!/usr/bin/env bash
# =============================================================================
# Section 11 Phase 5 — WP-CLI Install QA Script
#
# Validates a live WordPress installation of the fuadhasan-portfolio theme
# against all Phase 5 checklist items.
#
# Usage (from WordPress root):
#   bash setup/qa-check.sh [--url <site-url>] [--path <wp-path>]
#
# Examples:
#   bash setup/qa-check.sh
#   bash setup/qa-check.sh --url https://fuadhasan.com --path /var/www/html
#
# Requirements:
#   - wp-cli installed (https://wp-cli.org/)
#   - curl installed
#   - PHP 8.0+
#   - Access to the WordPress installation (local or SSH)
#
# Exit codes:
#   0 = All checks passed
#   1 = One or more checks failed
# =============================================================================
set -euo pipefail

# ─── Colour helpers ─────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'
BOLD='\033[1m'; RESET='\033[0m'

PASS="${GREEN}✔ PASS${RESET}"
FAIL="${RED}✘ FAIL${RESET}"
WARN="${YELLOW}⚠ WARN${RESET}"
INFO="${CYAN}ℹ INFO${RESET}"

PASS_COUNT=0; FAIL_COUNT=0; WARN_COUNT=0

# ─── Parse arguments ────────────────────────────────────────────────────────
WP_URL=""
WP_PATH="."

while [[ $# -gt 0 ]]; do
    case "$1" in
        --url)   WP_URL="$2";   shift 2 ;;
        --path)  WP_PATH="$2";  shift 2 ;;
        *)       shift ;;
    esac
done

WP_CMD="wp --path=${WP_PATH} --allow-root"

# If URL not provided, try to fetch from WP config
if [[ -z "$WP_URL" ]]; then
    WP_URL=$(${WP_CMD} option get siteurl 2>/dev/null || echo "http://localhost")
fi

# ─── Helper functions ────────────────────────────────────────────────────────

check() {
    local id="$1" description="$2" result="$3"
    if [[ "$result" == "pass" ]]; then
        echo -e "${PASS} [${id}] ${description}"
        (( PASS_COUNT++ )) || true
    elif [[ "$result" == "warn" ]]; then
        echo -e "${WARN} [${id}] ${description}"
        (( WARN_COUNT++ )) || true
    else
        echo -e "${FAIL} [${id}] ${description}"
        (( FAIL_COUNT++ )) || true
    fi
}

check_cmd() {
    local id="$1" description="$2" cmd="$3"
    if eval "$cmd" &>/dev/null; then
        check "$id" "$description" "pass"
    else
        check "$id" "$description" "fail"
    fi
}

http_status() {
    curl -s -o /dev/null -w "%{http_code}" --max-time 10 "$1" 2>/dev/null || echo "000"
}

# ─── Prerequisites ───────────────────────────────────────────────────────────
echo -e "\n${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}WordPress Portfolio QA Check${RESET}"
echo -e "${BOLD}Site: ${WP_URL}${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}\n"

if ! command -v wp &>/dev/null; then
    echo -e "${FAIL} [PRE] wp-cli not found. Install from https://wp-cli.org/"
    exit 1
fi

if ! ${WP_CMD} core is-installed &>/dev/null; then
    echo -e "${FAIL} [PRE] WordPress is not installed at: ${WP_PATH}"
    exit 1
fi

echo -e "${INFO}  wp-cli: $(wp --version 2>/dev/null)"
echo -e "${INFO}  WordPress: $(${WP_CMD} core version 2>/dev/null)"
echo ""

# =============================================================================
# GROUP 1: Theme Activation
# =============================================================================
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 1: Theme Activation${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

ACTIVE_THEME=$(${WP_CMD} theme list --status=active --field=name 2>/dev/null || echo "")
[[ "$ACTIVE_THEME" == "fuadhasan-portfolio" ]] && \
    check "TH-1" "fuadhasan-portfolio theme is active" "pass" || \
    check "TH-1" "fuadhasan-portfolio theme is active (active: ${ACTIVE_THEME})" "fail"

check_cmd "TH-2" "Theme has no PHP errors (wp theme status)" \
    "${WP_CMD} theme status fuadhasan-portfolio 2>&1 | grep -qv 'Error'"

# =============================================================================
# GROUP 2: Custom Post Types
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 2: Custom Post Types Registered${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

for cpt in experience project skill achievement; do
    if ${WP_CMD} post-type get "$cpt" &>/dev/null; then
        check "CPT-${cpt}" "CPT '${cpt}' is registered" "pass"
    else
        check "CPT-${cpt}" "CPT '${cpt}' is registered" "fail"
    fi
done

# Check taxonomies
for tax in skill_category achievement_type; do
    if ${WP_CMD} taxonomy get "$tax" &>/dev/null; then
        check "TAX-${tax}" "Taxonomy '${tax}' is registered" "pass"
    else
        check "TAX-${tax}" "Taxonomy '${tax}' is registered" "fail"
    fi
done

# =============================================================================
# GROUP 3: Required Pages
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 3: Required Pages${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

REQUIRED_PAGES=("about" "experience" "projects" "skills" "achievements" "contact" "download-cv")

for slug in "${REQUIRED_PAGES[@]}"; do
    PAGE_ID=$(${WP_CMD} post list --post_type=page --post_status=publish --field=ID \
        --name="$slug" 2>/dev/null | head -1 || echo "")
    [[ -n "$PAGE_ID" ]] && \
        check "PG-${slug}" "Page '/${slug}' exists and is published" "pass" || \
        check "PG-${slug}" "Page '/${slug}' exists and is published" "fail"
done

# =============================================================================
# GROUP 4: Seeded Content
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 4: Seeded Content${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

for cpt in experience project skill achievement; do
    COUNT=$(${WP_CMD} post list --post_type="$cpt" --post_status=publish \
        --format=count 2>/dev/null || echo "0")
    if [[ "$COUNT" -gt 0 ]]; then
        check "SEED-${cpt}" "${cpt} CPT has ${COUNT} published post(s)" "pass"
    else
        check "SEED-${cpt}" "${cpt} CPT has published posts (found: 0)" "warn"
    fi
done

# Check ACF options are seeded
HERO_NAME=$(${WP_CMD} option get options_hero_name 2>/dev/null || echo "")
[[ -n "$HERO_NAME" ]] && \
    check "SEED-opts" "ACF options hero_name is set: '${HERO_NAME}'" "pass" || \
    check "SEED-opts" "ACF options hero_name is set (empty)" "warn"

# =============================================================================
# GROUP 5: HTTP Responses — Cross-page Smoke Test
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 5: HTTP Smoke Tests (${WP_URL})${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

if ! command -v curl &>/dev/null; then
    echo -e "${WARN} [HTTP] curl not available — skipping HTTP tests"
    (( WARN_COUNT++ )) || true
else
    HTTP_PAGES=(
        "/"
        "/about"
        "/experience"
        "/projects"
        "/skills"
        "/achievements"
        "/contact"
        "/this-page-does-not-exist-404"
    )
    EXPECTED_CODES=("200" "200" "200" "200" "200" "200" "200" "404")

    for i in "${!HTTP_PAGES[@]}"; do
        PAGE="${HTTP_PAGES[$i]}"
        EXPECTED="${EXPECTED_CODES[$i]}"
        STATUS=$(http_status "${WP_URL}${PAGE}")
        [[ "$STATUS" == "$EXPECTED" ]] && \
            check "HTTP-${PAGE}" "GET ${PAGE} → HTTP ${STATUS}" "pass" || \
            check "HTTP-${PAGE}" "GET ${PAGE} → HTTP ${STATUS} (expected ${EXPECTED})" "fail"
    done
fi

# =============================================================================
# GROUP 6: CV Download Test
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 6: CV Download Pipeline${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

# 6.1 Rewrite rule is registered
REWRITE=$(${WP_CMD} eval 'global $wp_rewrite; echo json_encode(array_keys($wp_rewrite->rules));' 2>/dev/null || echo "[]")
if echo "$REWRITE" | grep -q "download-cv"; then
    check "CV-1" "CV rewrite rule registered in wp_rewrite->rules" "pass"
else
    check "CV-1" "CV rewrite rule registered in wp_rewrite->rules" "warn"
fi

# 6.2 /download-cv endpoint returns 200 or redirects to home (when no CV configured)
if command -v curl &>/dev/null; then
    CV_STATUS=$(http_status "${WP_URL}/download-cv")
    # Accept 200 (PDF served) or 302/301 (redirect when no CV configured)
    if [[ "$CV_STATUS" == "200" || "$CV_STATUS" == "301" || "$CV_STATUS" == "302" ]]; then
        check "CV-2" "GET /download-cv → HTTP ${CV_STATUS} (PDF or redirect)" "pass"
    else
        check "CV-2" "GET /download-cv → HTTP ${CV_STATUS} (unexpected)" "fail"
    fi

    # If 200, check content-type is PDF
    if [[ "$CV_STATUS" == "200" ]]; then
        CV_CONTENT_TYPE=$(curl -s -I --max-time 10 "${WP_URL}/download-cv" 2>/dev/null | \
            grep -i "content-type" | awk '{print $2}' | tr -d '\r')
        if echo "$CV_CONTENT_TYPE" | grep -q "application/pdf"; then
            check "CV-3" "CV Content-Type is application/pdf" "pass"
        else
            check "CV-3" "CV Content-Type is application/pdf (got: ${CV_CONTENT_TYPE})" "fail"
        fi

        # Check Content-Disposition: attachment
        CV_DISPOSITION=$(curl -s -I --max-time 10 "${WP_URL}/download-cv" 2>/dev/null | \
            grep -i "content-disposition" | tr -d '\r')
        if echo "$CV_DISPOSITION" | grep -qi "attachment"; then
            check "CV-4" "CV Content-Disposition is attachment (triggers download)" "pass"
        else
            check "CV-4" "CV Content-Disposition is attachment (got: ${CV_DISPOSITION})" "fail"
        fi

        # Check X-Content-Type-Options: nosniff
        CV_NOSNIFF=$(curl -s -I --max-time 10 "${WP_URL}/download-cv" 2>/dev/null | \
            grep -i "x-content-type-options" | tr -d '\r')
        if echo "$CV_NOSNIFF" | grep -qi "nosniff"; then
            check "CV-5" "CV X-Content-Type-Options: nosniff header present" "pass"
        else
            check "CV-5" "CV X-Content-Type-Options: nosniff header present" "warn"
        fi
    fi
fi

# 6.3 CV uploads directory exists with .htaccess
CV_DIR="${WP_PATH}/wp-content/uploads/cv"
[[ -d "$CV_DIR" ]] && \
    check "CV-6" "CV uploads directory exists: wp-content/uploads/cv" "pass" || \
    check "CV-6" "CV uploads directory exists: wp-content/uploads/cv" "warn"

[[ -f "${CV_DIR}/.htaccess" ]] && \
    check "CV-7" "CV directory has .htaccess protection" "pass" || \
    check "CV-7" "CV directory has .htaccess protection" "warn"

# =============================================================================
# GROUP 7: SEO Validation
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 7: SEO Meta Tags${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

if command -v curl &>/dev/null; then
    HOME_HTML=$(curl -s --max-time 15 "${WP_URL}/" 2>/dev/null || echo "")

    # Title tag
    if echo "$HOME_HTML" | grep -q "<title>"; then
        check "SEO-1" "Homepage has <title> tag" "pass"
    else
        check "SEO-1" "Homepage has <title> tag" "fail"
    fi

    # Meta description
    if echo "$HOME_HTML" | grep -qi 'name="description"'; then
        check "SEO-2" "Homepage has meta description" "pass"
    else
        check "SEO-2" "Homepage has meta description" "warn"
    fi

    # Open Graph
    if echo "$HOME_HTML" | grep -q 'property="og:title"'; then
        check "SEO-3" "Homepage has og:title Open Graph tag" "pass"
    else
        check "SEO-3" "Homepage has og:title Open Graph tag" "warn"
    fi

    # JSON-LD schema
    if echo "$HOME_HTML" | grep -q '"@type":"Person"'; then
        check "SEO-4" "Homepage has JSON-LD Person schema" "pass"
    else
        check "SEO-4" "Homepage has JSON-LD Person schema" "warn"
    fi

    # WordPress generator hidden (security)
    if echo "$HOME_HTML" | grep -q 'name="generator"'; then
        check "SEO-5" "WordPress generator meta tag is hidden (security)" "fail"
    else
        check "SEO-5" "WordPress generator meta tag is hidden" "pass"
    fi
fi

# =============================================================================
# GROUP 8: Performance Headers
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 8: Performance & Caching${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

if command -v curl &>/dev/null; then
    CSS_URL="${WP_URL}/wp-content/themes/fuadhasan-portfolio/style.css"
    CSS_STATUS=$(http_status "$CSS_URL")
    [[ "$CSS_STATUS" == "200" ]] && \
        check "PERF-1" "Theme stylesheet accessible (HTTP 200)" "pass" || \
        check "PERF-1" "Theme stylesheet accessible (HTTP ${CSS_STATUS})" "fail"

    # Check cache-control header on static asset
    CSS_CACHE=$(curl -s -I --max-time 10 "$CSS_URL" 2>/dev/null | \
        grep -i "cache-control" | tr -d '\r' | head -1)
    if [[ -n "$CSS_CACHE" ]]; then
        check "PERF-2" "Cache-Control header on stylesheet: ${CSS_CACHE#*: }" "pass"
    else
        check "PERF-2" "Cache-Control header on stylesheet (missing)" "warn"
    fi
fi

# Check WordPress cron is not blocked
check_cmd "PERF-3" "WordPress cron system is functional" \
    "${WP_CMD} cron test 2>&1 | grep -q 'Success'"

# =============================================================================
# GROUP 9: Admin Panel — CPT CRUD Smoke Test
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 9: Admin Panel — CPT CRUD${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

# Create a test experience post
TEST_ID=$(${WP_CMD} post create \
    --post_type=experience \
    --post_status=publish \
    --post_title="QA Test Experience — Delete Me" \
    --format=ids 2>/dev/null || echo "")

if [[ -n "$TEST_ID" ]]; then
    check "CRUD-1" "Can create experience CPT post (ID: ${TEST_ID})" "pass"

    # Update it
    if ${WP_CMD} post update "$TEST_ID" --post_title="QA Test Experience — Updated" &>/dev/null; then
        check "CRUD-2" "Can update experience CPT post" "pass"
    else
        check "CRUD-2" "Can update experience CPT post" "fail"
    fi

    # Delete it (force-delete to skip trash)
    if ${WP_CMD} post delete "$TEST_ID" --force &>/dev/null; then
        check "CRUD-3" "Can delete experience CPT post" "pass"
    else
        check "CRUD-3" "Can delete experience CPT post" "fail"
    fi
else
    check "CRUD-1" "Can create experience CPT post" "fail"
    check "CRUD-2" "Can update experience CPT post (skipped)" "warn"
    check "CRUD-3" "Can delete experience CPT post (skipped)" "warn"
fi

# =============================================================================
# GROUP 10: Accessibility Quick Check
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 10: Accessibility Quick Check${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

if command -v curl &>/dev/null; then
    HOME_HTML=$(curl -s --max-time 15 "${WP_URL}/" 2>/dev/null || echo "")

    # Skip link
    if echo "$HOME_HTML" | grep -q 'skip-link'; then
        check "A11Y-1" "Skip-to-content link present on homepage" "pass"
    else
        check "A11Y-1" "Skip-to-content link present on homepage" "fail"
    fi

    # HTML lang attribute
    if echo "$HOME_HTML" | grep -q '<html.*lang='; then
        check "A11Y-2" "HTML lang attribute set" "pass"
    else
        check "A11Y-2" "HTML lang attribute set" "fail"
    fi

    # Main landmark
    if echo "$HOME_HTML" | grep -q 'id="main-content"'; then
        check "A11Y-3" "Main landmark has id=\"main-content\"" "pass"
    else
        check "A11Y-3" "Main landmark has id=\"main-content\"" "fail"
    fi

    # Footer landmark
    if echo "$HOME_HTML" | grep -q 'role="contentinfo"'; then
        check "A11Y-4" "Footer has role=\"contentinfo\"" "pass"
    else
        check "A11Y-4" "Footer has role=\"contentinfo\"" "fail"
    fi

    # Back to top button
    if echo "$HOME_HTML" | grep -q 'back-to-top'; then
        check "A11Y-5" "Back-to-top button present" "pass"
    else
        check "A11Y-5" "Back-to-top button present" "fail"
    fi

    # Nav aria-label
    if echo "$HOME_HTML" | grep -q 'aria-label.*Navigation\|aria-label.*nav'; then
        check "A11Y-6" "Navigation has aria-label" "pass"
    else
        check "A11Y-6" "Navigation has aria-label" "warn"
    fi
fi

# =============================================================================
# GROUP 11: Security
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}GROUP 11: Security${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

# Check wp-config.php has proper keys
if ${WP_CMD} config get AUTH_KEY &>/dev/null; then
    check "SEC-1" "WordPress secret keys are configured" "pass"
else
    check "SEC-1" "WordPress secret keys are configured" "warn"
fi

# Check debug is off
WP_DEBUG=$(${WP_CMD} config get WP_DEBUG 2>/dev/null || echo "false")
[[ "$WP_DEBUG" != "true" && "$WP_DEBUG" != "1" ]] && \
    check "SEC-2" "WP_DEBUG is disabled (production-safe)" "pass" || \
    check "SEC-2" "WP_DEBUG is enabled — disable on production" "warn"

# Check file editing is disabled
FILE_EDIT=$(${WP_CMD} config get DISALLOW_FILE_EDIT 2>/dev/null || echo "")
[[ "$FILE_EDIT" == "true" || "$FILE_EDIT" == "1" ]] && \
    check "SEC-3" "DISALLOW_FILE_EDIT is enabled" "pass" || \
    check "SEC-3" "DISALLOW_FILE_EDIT is not set — add to wp-config.php for hardening" "warn"

# Verify uploads directory structure
if [[ -d "${WP_PATH}/wp-content/uploads/cv" ]]; then
    check "SEC-4" "CV upload subdirectory isolated from main uploads" "pass"
else
    check "SEC-4" "CV upload subdirectory isolated from main uploads (not created yet)" "warn"
fi

# =============================================================================
# SUMMARY
# =============================================================================
echo ""
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"
echo -e "${BOLD}RESULTS SUMMARY${RESET}"
echo -e "${BOLD}$(printf '=%.0s' {1..60})${RESET}"

TOTAL=$(( PASS_COUNT + FAIL_COUNT + WARN_COUNT ))
echo -e "${PASS}  Passed  : ${PASS_COUNT}"
echo -e "${FAIL}  Failed  : ${FAIL_COUNT}"
echo -e "${WARN}  Warnings: ${WARN_COUNT}"
echo -e "   Total   : ${TOTAL}"
echo ""

if [[ $FAIL_COUNT -gt 0 ]]; then
    echo -e "${RED}${BOLD}QA FAILED — ${FAIL_COUNT} check(s) did not pass.${RESET}"
    exit 1
else
    echo -e "${GREEN}${BOLD}QA PASSED — All critical checks passed${WARN_COUNT:+ with ${WARN_COUNT} warning(s)}.${RESET}"
    exit 0
fi
