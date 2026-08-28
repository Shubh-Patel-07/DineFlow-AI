#!/bin/bash
# ==============================================================================
# TENDORABUY — AUTOMATED DAILY DATABASE & MEDIA BACKUP SCRIPT
# ==============================================================================

set -e

BACKUP_DIR="/var/backups/tendorabuy"
DATE=$(date +"%Y-%m-%d_%H-%M-%S")
DB_NAME="dineflow_ai"
DB_USER="dineflow_user"
DB_PASS="YOUR_SECURE_PASSWORD" # Read from /root/.my.cnf or env in production

mkdir -p $BACKUP_DIR

echo ">> Starting daily database backup for ${DB_NAME}..."
mysqldump -u ${DB_USER} -p"${DB_PASS}" --single-transaction --quick --lock-tables=false ${DB_NAME} | gzip > ${BACKUP_DIR}/db_backup_${DATE}.sql.gz

echo ">> Database backup saved: ${BACKUP_DIR}/db_backup_${DATE}.sql.gz"

# Keep last 7 days of backups, delete older
find ${BACKUP_DIR} -type f -name "db_backup_*.sql.gz" -mtime +7 -exec rm {} \;

echo ">> Backup rotation complete. Old backups (>7 days) purged."
