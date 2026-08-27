# DineFlow AI — Backup & Disaster Recovery Guide

This guide outlines the production database backup strategy, automated daily cron script, retention policy, and tested restore procedure.

---

## 1. Automated Daily MySQL Backup Script

Create `/usr/local/bin/dineflow_backup.sh`:

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

echo "[$(date)] Backup completed: dineflow_backup_$TIMESTAMP.sql.gz"
```

Make executable:
```bash
sudo chmod +x /usr/local/bin/dineflow_backup.sh
```

---

## 2. Cron Job Setup (Daily at 2:00 AM)

```bash
sudo crontab -e
# Add line:
0 2 * * * /usr/local/bin/dineflow_backup.sh >> /var/log/dineflow_backup.log 2>&1
```

---

## 3. Verified Database Restore Procedure

To restore from a backup file:

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
