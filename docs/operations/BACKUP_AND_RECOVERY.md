# DineFlow AI — Backup, Sync Queue & Disaster Recovery Guide

This guide establishes the comprehensive data safety strategy across both **Cloud MySQL Infrastructure** and **Local Desktop SQLite Terminals**.

---

## 1. Cloud Database Backup Strategy

### Automated Daily Cron Backup (`/usr/local/bin/dineflow_backup.sh`)

```bash
#!/bin/bash
BACKUP_DIR="/var/backups/dineflow"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="dineflow_ai"
DB_USER="dineflow_user"
DB_PASS="StrongDBPassword123!#"

# Create backup directory if not exists
mkdir -p "$BACKUP_DIR"

# Perform MySQL dump with structure and data
mysqldump -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" | gzip > "$BACKUP_DIR/dineflow_backup_$TIMESTAMP.sql.gz"

# Retention Policy: Delete backups older than 14 days
find "$BACKUP_DIR" -type f -name "dineflow_backup_*.sql.gz" -mtime +14 -exec rm {} \;

echo "[$(date)] Cloud Backup completed: dineflow_backup_$TIMESTAMP.sql.gz"
```

### Verified Restore Procedure
```bash
# 1. Unzip the backup file
gunzip /var/backups/dineflow/dineflow_backup_YYYYMMDD_HHMMSS.sql.gz

# 2. Restore into MySQL
mysql -u dineflow_user -pStrongDBPassword123!# dineflow_ai < /var/backups/dineflow/dineflow_backup_YYYYMMDD_HHMMSS.sql

# 3. Clear Laravel Cache
cd /var/www/dineflow
php artisan cache:clear
php artisan config:clear
```

---

## 2. Local Desktop SQLite & Sync Queue Recovery

On cashier POS terminals running the desktop application, the local database is saved at:
`%APPDATA%/dineflow-app/database/dineflow-local.sqlite`

### Automatic Crash Recovery
1. **Write-Ahead Logging (WAL)**: SQLite writes changes to `.sqlite-wal` instantly before committing, preventing database corruption during unexpected power outages.
2. **Sync Queue Preservation**: The `sync_queue` table persists unsynced records across application restarts.
3. **Automated Resumption**: When the desktop application launches, `syncEngine.processQueue()` immediately queries for any items with status `'pending'` or `'failed'` and attempts reconciliation once internet connectivity is confirmed.

---

## 3. Offsite Disaster Recovery & Asset Protection

- **Uploaded Dish & Logo Assets**: Synchronized daily from `storage/app/public/` to offsite cloud storage (AWS S3 / Cloudflare R2).
- **Point-in-Time Recovery**: MySQL binary logging enabled for 7-day point-in-time rollbacks.
