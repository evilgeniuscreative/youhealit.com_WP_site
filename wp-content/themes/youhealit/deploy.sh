#!/usr/bin/env bash
set -euo pipefail

# =========================
# YHI deploy script
# =========================
# What it does:
# - rsync the youhealit theme to prod (excludes junk)
# - backup DB on prod
# - set permalinks and flush rewrite rules
# - ensure pages exist + assign templates:
#     /sitemap/  -> page-sitemap.php
#     /all-cities-sitemap/ -> page-all-cities-sitemap.php
# - ensure Services page exists (/services/)
# - (optional) add legacy redirects to .htaccess above the WP block
# - sanity checks (curl + WP-CLI eval)
#
# Usage:
#   chmod +x deploy.sh
#   ./deploy.sh --dry-run       # no writes
#   ./deploy.sh                 # real deploy
# Options:
#   --dry-run        : rsync dry run; skip DB backup & htaccess edit
#   --no-htaccess    : skip .htaccess legacy redirects edit
#   --no-backup      : skip DB export backup
#   --alias @custom  : use a different WP-CLI alias than @yhi
#   --host  NAME     : SSH host alias (default yhi-prod)
#   --remote PATH    : remote WP root (default /home3/evilgeo2/public_html/greatpieceofapp)
#   --theme  PATH    : local theme path (default ./wp-content/themes/youhealit)

# -------- configurable defaults --------
WP_ALIAS="@yhi12"
SSH_HOST="yhi12-prod"
REMOTE_ROOT="/home3/evilgeo2/public_html/YHI12"
LOCAL_THEME="./wp-content/themes/youhealit"
THEME_SLUG="youhealit"
DO_HTACCESS=1
DO_BACKUP=1
DRY_RUN=0

# -------- parse args --------
while [[ $# -gt 0 ]]; do
  case "$1" in
    --dry-run) DRY_RUN=1; DO_BACKUP=0; DO_HTACCESS=0; shift;;
    --no-htaccess) DO_HTACCESS=0; shift;;
    --no-backup) DO_BACKUP=0; shift;;
    --alias) WP_ALIAS="$2"; shift 2;;
    --host) SSH_HOST="$2"; shift 2;;
    --remote) REMOTE_ROOT="$2"; shift 2;;
    --theme) LOCAL_THEME="$2"; shift 2;;
    *) echo "Unknown option: $1" >&2; exit 2;;
  esac
done

REMOTE_THEME="${REMOTE_ROOT}/wp-content/themes/${THEME_SLUG}"
HTACCESS_PATH="${REMOTE_ROOT}/.htaccess"

log(){ printf "\033[1;34m==>\033[0m %s\n" "$*"; }
ok(){  printf "\033[1;32m✔\033[0m %s\n" "$*"; }
warn(){ printf "\033[1;33m!\033[0m %s\n" "$*"; }
die(){  printf "\033[1;31m✘ %s\033[0m\n" "$*" >&2; exit 1; }

trap 'die "Deploy failed at line $LINENO."' ERR

# -------- preflight --------
command -v rsync >/dev/null || die "rsync not found"
command -v ssh   >/dev/null || die "ssh not found"
command -v wp    >/dev/null || die "wp (WP-CLI) not found"
command -v curl  >/dev/null || die "curl not found"

[[ -d "$LOCAL_THEME" ]] || die "Local theme dir not found: $LOCAL_THEME"

log "Sanity: WP-CLI alias $WP_ALIAS"
wp "$WP_ALIAS" --info >/dev/null

# -------- rsync theme --------
log "Syncing theme to prod: $LOCAL_THEME -> $SSH_HOST:$REMOTE_THEME"
RSYNC_FLAGS=(-avz --delete
  --exclude 'venv/' --exclude 'node_modules/' --exclude '.git/' --exclude '.DS_Store'
  --exclude '*.map' --exclude 'dist/' --exclude '.env' --exclude '*.zip'
)
[[ $DRY_RUN -eq 1 ]] && RSYNC_FLAGS+=(--dry-run)
rsync "${RSYNC_FLAGS[@]}" "$LOCAL_THEME/" "$SSH_HOST:$REMOTE_THEME/"
ok "Theme synced"

# -------- DB backup --------
if [[ $DO_BACKUP -eq 1 ]]; then
  TS="$(date +%F-%H%M)"
  BACKUP_PATH="${REMOTE_ROOT}/db-backup-${TS}.sql"
  log "Backing up DB on prod to ${BACKUP_PATH}"
  wp "$WP_ALIAS" db export "$BACKUP_PATH"
  ok "DB backup created: ${BACKUP_PATH}"
else
  warn "Skipping DB backup (--no-backup or --dry-run)"
fi

# -------- Permalinks + rewrite --------
log "Setting permalink structure and flushing rewrite rules"
wp "$WP_ALIAS" option update permalink_structure '/%postname%/'
wp "$WP_ALIAS" rewrite flush --hard
ok "Permalinks ensured"

# -------- Ensure Services page --------
log "Ensuring Services page exists"
SERVICE_ID="$(wp "$WP_ALIAS" post list --post_type=page --name=services --field=ID --posts_per_page=1 || true)"
if [[ -z "${SERVICE_ID:-}" ]]; then
  SERVICE_ID="$(wp "$WP_ALIAS" post create --post_type=page --post_title='Services' --post_name='services' --post_status=publish --porcelain)"
  ok "Created Services page (ID $SERVICE_ID)"
else
  ok "Services page exists (ID $SERVICE_ID)"
fi

# -------- Ensure HTML sitemaps --------
ensure_page_with_template () {
  local SLUG="$1" TITLE="$2" TEMPLATE_FILE="$3"
  log "Ensuring page /${SLUG}/ exists and uses template ${TEMPLATE_FILE}"
  local PID
  PID="$(wp "$WP_ALIAS" post list --post_type=page --name="$SLUG" --field=ID --posts_per_page=1 || true)"
  if [[ -z "${PID:-}" ]]; then
    PID="$(wp "$WP_ALIAS" post create --post_type=page --post_title="$TITLE" --post_name="$SLUG" --post_status=publish --porcelain)"
    ok "Created page ${TITLE} (ID $PID)"
  else
    ok "Page ${TITLE} exists (ID $PID)"
  fi
  # Assign template (theme must contain the file)
  wp "$WP_ALIAS" post meta update "$PID" _wp_page_template "$TEMPLATE_FILE" >/dev/null
  ok "Template set: $TEMPLATE_FILE"
}

ensure_page_with_template "sitemap" "Sitemap" "page-sitemap.php"
ensure_page_with_template "all-cities-sitemap" "All Cities Sitemap" "page-all-cities-sitemap.php"

# -------- Legacy redirects in .htaccess --------
if [[ $DO_HTACCESS -eq 1 ]]; then
  log "Ensuring legacy redirects exist in ${HTACCESS_PATH}"
  ssh "$SSH_HOST" bash -lc "set -euo pipefail
    HT='${HTACCESS_PATH}'
    if [[ -f \"\$HT\" ]]; then
      if ! grep -q 'Legacy redirects (YHI12)' \"\$HT\"; then
        awk 'BEGIN{added=0}
             /# BEGIN WordPress/{
               if(!added){
                 print \"# Legacy redirects (YHI12)\"
                 print \"RewriteRule ^page-(.+)\\\\.php$ /\\\"\"1\"\\\"/ [R=301,L]\" | \"sed s/\\\\\\\"//g\"
                 print \"RewriteRule ^our-services/?$ /services/ [R=301,L]\"
                 added=1
               }
             }
             {print}
             END{
               if(!added){
                 print \"# Legacy redirects (YHI12)\"
                 print \"RewriteRule ^page-(.+)\\\\.php$ /\\\"\"1\"\\\"/ [R=301,L]\" | \"sed s/\\\\\\\"//g\"
                 print \"RewriteRule ^our-services/?$ /services/ [R=301,L]\"
               }
             }' \"\$HT\" > \"\$HT.tmp\"
        mv \"\$HT.tmp\" \"\$HT\"
      fi
    else
      echo \"No .htaccess at \$HT; skipping\"
    fi"
  ok "Legacy redirects ensured (or already present)"
else
  warn "Skipping .htaccess edit (--no-htaccess or --dry-run)"
fi

# -------- Sanity checks --------
log "Sanity checks"
HOME_URL="$(wp "$WP_ALIAS" option get home)"
ok "Home URL: $HOME_URL"

log "services_count via youhealit_get_services()"
wp "$WP_ALIAS" eval 'echo "services_count=" . (function_exists("youhealit_get_services") ? count(youhealit_get_services()) : -1) . PHP_EOL;'

log "HTTP checks"
curl -fsS -I "$HOME_URL" | head -n 5 || die "Home check failed"
curl -fsS "$HOME_URL/services/" | head -n 20 >/dev/null || warn "/services/ not rendering?"
curl -fsS "$HOME_URL/sitemap/" | head -n 20 >/dev/null || warn "/sitemap/ not rendering?"
curl -fsS "$HOME_URL/all-cities-sitemap/?letter=B" | head -n 20 >/dev/null || warn "/all-cities-sitemap/ letter=B not rendering?"

ok "Deploy complete."
