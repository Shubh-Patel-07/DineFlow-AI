# DineFlow AI — Pilot Observability & System Monitoring Guide

This guide describes operational monitoring during the live pilot to ensure 99.9% uptime, real-time order delivery, and instant error detection.

---

## 1. Real-Time Application Log Monitoring

Monitor live application exceptions in production:

```bash
# Follow live Laravel errors in real-time
tail -f /var/www/dineflow/storage/logs/laravel.log

# Monitor Nginx web server access and error logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

---

## 2. Server Resource Monitoring

Check memory, CPU, and disk space utilization:

```bash
# Check CPU & RAM load
htop

# Check Disk space
df -h

# Check MySQL connection status
mysqladmin -u dineflow_user -p status
```

---

## 3. Key Operational Pilot Metrics to Watch

| Metric | Target | Action If Threshold Exceeded |
|---|---|---|
| **Order Placement Latency** | < 300ms | Check MySQL slow query log |
| **KDS Status Transition Sync** | Real-time | Verify client network & WebSocket/polling |
| **Payment Settlement** | 100% accurate | Cross-check UPI soundbox & POS transaction status |
| **Disk Space Usage** | < 75% | Rotate and clean old backups / logs |
