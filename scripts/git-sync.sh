#!/bin/bash
# ============================================
# Auto Git Sync for BUMDes Digital
# ============================================
# Jalankan script ini setelah ada perubahan:
#   bash /www/wwwroot/bumdes/scripts/git-sync.sh "pesan commit"
#
# Atau tanpa pesan (otomatis timestamp):
#   bash /www/wwwroot/bumdes/scripts/git-sync.sh
# ============================================

PROJECT_DIR="/www/wwwroot/bumdes"
cd "$PROJECT_DIR" || exit 1

# Check if there are changes
if git diff --quiet HEAD 2>/dev/null && git diff --cached --quiet 2>/dev/null; then
    echo "✅ Tidak ada perubahan. Skip commit."
    exit 0
fi

# Get commit message
if [ -n "$1" ]; then
    COMMIT_MSG="$1"
else
    COMMIT_MSG="auto: Update $(date '+%Y-%m-%d %H:%M:%S')"
fi

# Stage all changes
git add -A

# Count files changed
FILES_CHANGED=$(git diff --cached --numstat | wc -l)

# Commit
git commit -m "$COMMIT_MSG" --quiet

# Push
git push origin main --quiet 2>&1

echo "✅ Berhasil sync! $FILES_CHANGED file di-push."
echo "🔗 https://github.com/mrobish/website-bumdes"
