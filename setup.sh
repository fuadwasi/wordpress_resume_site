#!/usr/bin/env bash
# =============================================================================
# setup.sh — One-command local setup for the WordPress Portfolio Project
# =============================================================================
# Usage:  ./setup.sh
# Requires: Docker, Docker Compose v2
# =============================================================================

set -euo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; CYAN='\033[0;36m'; NC='\033[0m'
info()    { echo -e "${CYAN}[INFO]${NC}  $*"; }
success() { echo -e "${GREEN}[OK]${NC}    $*"; }
warn()    { echo -e "${YELLOW}[WARN]${NC}  $*"; }
error()   { echo -e "${RED}[ERROR]${NC} $*"; exit 1; }

# ── Pre-flight checks ─────────────────────────────────────────────────────────
command -v docker >/dev/null 2>&1  || error "Docker is not installed. https://docs.docker.com/get-docker/"
docker compose version >/dev/null 2>&1 || error "Docker Compose v2 is not installed."

# ── Copy .env if missing ──────────────────────────────────────────────────────
if [ ! -f .env ]; then
  cp .env.example .env
  info "Created .env from .env.example. You can edit it before continuing."
fi

# Source .env
set -a; source .env; set +a

WP_PORT="${WP_PORT:-8080}"
PMA_PORT="${PMA_PORT:-8081}"

# ── Start services ────────────────────────────────────────────────────────────
info "Starting Docker containers..."
docker compose up -d --build

# ── Wait for WordPress to be ready ───────────────────────────────────────────
info "Waiting for WordPress to become ready..."
max_attempts=30
attempt=0
until curl -s -o /dev/null -w "%{http_code}" "http://localhost:${WP_PORT}" | grep -qE "^(200|301|302|303)"; do
  attempt=$((attempt + 1))
  if [ "$attempt" -ge "$max_attempts" ]; then
    warn "WordPress did not respond in time. Check docker compose logs."
    break
  fi
  echo -n "."
  sleep 3
done
echo ""

success "Setup complete!"
echo ""
echo -e "  🌐  WordPress site:  ${CYAN}http://localhost:${WP_PORT}${NC}"
echo -e "  🔧  WordPress admin: ${CYAN}http://localhost:${WP_PORT}/wp-admin${NC}"
echo -e "  🗄️   phpMyAdmin:      ${CYAN}http://localhost:${PMA_PORT}${NC}"
echo ""
echo -e "  Complete the 5-minute WordPress install at ${CYAN}http://localhost:${WP_PORT}/wp-admin/install.php${NC}"
echo -e "  Then activate the ${CYAN}Fuad Hasan Portfolio${NC} theme under Appearance → Themes."
echo ""
