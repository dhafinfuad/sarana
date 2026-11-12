#!/bin/bash
# Auto-deploy SARANA (Laravel) untuk 1Panel di Debian.
# Dijalankan lewat cron, contoh (tiap menit):
#   * * * * * /home/administrator/scripts/sync-sarana.sh >/dev/null 2>&1
export HOME="/home/administrator"
export USER="administrator"
export GIT_CONFIG_GLOBAL="/home/administrator/.gitconfig"
export GIT_SSH_COMMAND="ssh -i /home/administrator/.ssh/id_ed25519 -p 443 -o StrictHostKeyChecking=no"

# GANTI: pengaturan untuk repo ini
SITE_DIR="/opt/1panel/apps/openresty/openresty/www/sites/sarana"      # folder repo di server
BRANCH="main"                                                          # branch yang di-deploy
CONTAINER_NAME="1Panel-php8-LCJZ"                                      # container PHP
APP_DIR_IN_CONTAINER="/www/sites/sarana"                               # path folder yang sama di dalam container
LOG_FILE="/home/administrator/scripts/sync-sarana.log"                 # log terpisah per repo

# Cegah dua proses berjalan bersamaan untuk repo yang sama
exec 9>"/tmp/sync-$(basename "$SITE_DIR").lock"
flock -n 9 || exit 0

cd "$SITE_DIR" || exit 1

GIT="git -c safe.directory=*"
ts() { date '+%Y-%m-%d %H:%M:%S'; }
log() { echo "[$(ts)] $*" | tee -a "$LOG_FILE"; }
# Jalankan perintah di dalam container PHP (di folder aplikasi)
dexec() { docker exec "$CONTAINER_NAME" sh -c "cd $APP_DIR_IN_CONTAINER && $*" 2>&1 | tee -a "$LOG_FILE"; return "${PIPESTATUS[0]}"; }

$GIT fetch origin "$BRANCH" 2>&1 | tee -a "$LOG_FILE" >/dev/null

LOCAL=$($GIT rev-parse HEAD)
REMOTE=$($GIT rev-parse "origin/$BRANCH")

if [ "$LOCAL" = "$REMOTE" ]; then
    echo "[$(ts)] Sudah versi terbaru." >> "$LOG_FILE"
    exit 0
fi

log "Perubahan baru terdeteksi: $LOCAL -> $REMOTE"

# File apa saja yang berubah, untuk menentukan langkah yang perlu dijalankan
CHANGED=$($GIT diff --name-only "$LOCAL" "$REMOTE")

$GIT reset --hard "origin/$BRANCH" 2>&1 | tee -a "$LOG_FILE"
chown -R administrator:administrator "$SITE_DIR" 2>/dev/null

# 1) Dependensi PHP, hanya jika composer.json / composer.lock berubah
if echo "$CHANGED" | grep -qE '^composer\.(json|lock)$'; then
    log "composer install..."
    dexec "composer install --no-dev --optimize-autoloader --no-interaction" \
        || log "PERINGATAN: composer install gagal"
fi

# 2) Migrasi database, hanya jika ada file migrasi berubah
if echo "$CHANGED" | grep -q '^database/migrations/'; then
    log "php artisan migrate..."
    dexec "php artisan migrate --force" || log "PERINGATAN: migrate gagal"
fi

# 3) Build aset front-end (public/build di-.gitignore, jadi harus dibuild di server).
#    Dijalankan jika package*.json, vite/tailwind config, atau resources/ berubah.
if echo "$CHANGED" | grep -qE '^(package(-lock)?\.json|vite\.config\.js|tailwind\.config\.js|resources/(css|js|views)/)'; then
    if command -v npm >/dev/null 2>&1; then
        log "npm ci && npm run build..."
        (npm ci --no-audit --no-fund && npm run build) 2>&1 | tee -a "$LOG_FILE"
        [ "${PIPESTATUS[0]}" -eq 0 ] || log "PERINGATAN: build aset gagal"
    else
        log "PERINGATAN: npm tidak ditemukan di host; build aset dilewati (jalankan 'npm run build' manual)."
    fi
fi

# 4) Bersihkan cache Laravel
dexec "php artisan optimize:clear"

log "Sukses tersinkronisasi."
