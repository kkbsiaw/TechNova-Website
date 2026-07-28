#!/bin/bash
# ============================================================
# Deploy TechNova to InfinityFree over FTP.
#
# Uploads all the site files into the server's htdocs folder.
# Your FTP password is typed at the prompt (or via the FTP_PASS
# env var) — it is never stored in this script or in git.
#
# Usage:
#   cd /Applications/XAMPP/htdocs/TechNova
#   ./deploy_to_infinityfree.sh
# ============================================================
set -euo pipefail
cd "$(dirname "$0")"

FTP_HOST="ftpupload.net"
FTP_USER="if0_42523368"
REMOTE_DIR="htdocs"          # InfinityFree serves the site from here

# --- Safety check: is the live DB password filled in? ---
if grep -q 'DB_PASS = "";' db.php; then
  echo "WARNING: db.php still has a blank live password (\$DB_PASS = \"\")."
  echo "         Set your MySQL password in the LIVE block of db.php first,"
  echo "         or the deployed site will fail to connect to the database."
  read -r -p "Continue uploading anyway? [y/N] " ans
  [ "${ans:-N}" = "y" ] || [ "${ans:-N}" = "Y" ] || { echo "Aborted."; exit 1; }
fi

# --- Ask for the FTP password (hidden) if not already in env ---
if [ -z "${FTP_PASS:-}" ]; then
  read -s -r -p "InfinityFree FTP password for $FTP_USER: " FTP_PASS
  echo
fi

echo "Uploading to ftp://$FTP_HOST/$REMOTE_DIR/ ..."

# Collect the files to deploy (php, css, js, images, the hosting SQL).
# Excludes: .git, README, local-only database.sql, this script, dotfiles.
find . -type f \
  \( -name '*.php' -o -path './css/*' -o -path './js/*' \
     -o -path './images/*' -o -name 'database_infinityfree.sql' \) \
  -not -path './.git/*' -not -name '.DS_Store' -print0 |
while IFS= read -r -d '' f; do
  rel="${f#./}"
  printf '  -> %s\n' "$rel"
  curl -sS --ftp-create-dirs -T "$f" \
       "ftp://$FTP_HOST/$REMOTE_DIR/$rel" --user "$FTP_USER:$FTP_PASS" \
    || { echo "FAILED on $rel"; exit 1; }
done

echo
echo "Upload complete."
echo "Next: import database_infinityfree.sql via InfinityFree phpMyAdmin,"
echo "then open your InfinityFree URL and log in."
