#!/usr/bin/env bash
# Nightly backup: DB dump + moodledata, encrypted, with retention.
# Run from the deploy/ directory. Schedule via cron on the host, e.g.:
#   30 2 * * *  cd /opt/sfs/deploy && ./backup.sh >> /var/log/sfs-backup.log 2>&1
set -euo pipefail

STAMP="$(date +%Y%m%d-%H%M%S)"
DEST="${BACKUP_DIR:-/opt/sfs/backups}"
KEEP_DAYS="${KEEP_DAYS:-14}"
COMPOSE="docker compose --env-file .env.prod -f docker-compose.prod.yml"

mkdir -p "$DEST"

echo "[$(date -Is)] DB dump…"
# --single-transaction = consistent snapshot without locking (InnoDB).
$COMPOSE exec -T mysql sh -c \
  'exec mariadb-dump --single-transaction --quick --routines --triggers \
     -uroot -p"$(cat /run/secrets/db_root_password)" "$MYSQL_DATABASE"' \
  | gzip > "$DEST/db-$STAMP.sql.gz"

echo "[$(date -Is)] moodledata archive…"
# Read the volume from a throwaway container so we don't depend on host paths.
docker run --rm \
  -v sfs_moodledata:/data:ro \
  -v "$DEST":/backup \
  alpine:3 sh -c "tar czf /backup/moodledata-$STAMP.tar.gz -C /data ."

# Optional: encrypt at rest if BACKUP_GPG_RECIPIENT is set.
if [[ -n "${BACKUP_GPG_RECIPIENT:-}" ]]; then
  echo "[$(date -Is)] encrypting…"
  for f in "$DEST/db-$STAMP.sql.gz" "$DEST/moodledata-$STAMP.tar.gz"; do
    gpg --yes --batch --encrypt --recipient "$BACKUP_GPG_RECIPIENT" "$f"
    rm -f "$f"
  done
fi

echo "[$(date -Is)] pruning backups older than ${KEEP_DAYS}d…"
find "$DEST" -type f -mtime "+$KEEP_DAYS" -name '*-*' -print -delete

# TODO: sync $DEST to off-site storage (rclone/rsync) — a backup on the same
# box does not survive disk loss or ransomware.
echo "[$(date -Is)] done."
