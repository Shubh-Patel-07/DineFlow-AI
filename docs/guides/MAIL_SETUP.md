# DineFlow AI — Production Mail & SMTP Setup Guide

This document explains how to configure production SMTP credentials in DineFlow AI for registration confirmations, password resets, and automated daily sales summaries.

---

## 1. Environment Configuration

Open `.env` on your production server:

```env
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.your_actual_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="support@your-domain.com"
MAIL_FROM_NAME="DineFlow AI Support"
```

---

## 2. Supported SMTP Providers

### Option A: SendGrid
- **Host**: `smtp.sendgrid.net`
- **Port**: `587`
- **Encryption**: `tls`
- **Username**: `apikey`
- **Password**: Your SendGrid API Key starting with `SG.`

### Option B: Mailgun
- **Host**: `smtp.mailgun.org`
- **Port**: `587`
- **Encryption**: `tls`
- **Username**: `postmaster@yourdomain.mailgun.org`
- **Password**: Your Mailgun SMTP password

### Option C: Amazon SES
- **Host**: `email-smtp.us-east-1.amazonaws.com`
- **Port**: `587`
- **Encryption**: `tls`
- **Username**: Your SES SMTP Access Key
- **Password**: Your SES SMTP Secret Key

---

## 3. Testing SMTP Connection

Run artisan tinker to send a test email:

```bash
php artisan tinker --execute="
Mail::raw('DineFlow AI SMTP Test Successful!', function(\$message) {
    \$message->to('owner@your-restaurant.com')->subject('DineFlow AI SMTP Test');
});
echo 'Mail sent successfully!';
"
```
