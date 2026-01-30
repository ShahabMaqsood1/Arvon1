# 🚀 ARVON Website - Complete Deployment Guide

## Pre-Deployment Checklist

### 1. Email & DNS Configuration (CRITICAL)

#### Spacemail Setup
1. Log into your Spacemail account
2. Add domain: `arvon.pk`
3. Create email accounts:
   - `no-reply@arvon.pk` (for sending notifications)
   - `info@arvon.pk` (for receiving inquiries)

#### DNS Records (Add in your domain DNS settings)

**SPF Record:**
```
Type: TXT
Host: @
Value: v=spf1 include:_spf.spacemail.com ~all
TTL: 3600
```

**DKIM Record:**
```
Type: TXT
Host: spacemail._domainkey
Value: [Get from Spacemail dashboard]
TTL: 3600
```

**DMARC Record:**
```
Type: TXT
Host: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:dmarc@arvon.pk; pct=100
TTL: 3600
```

#### Test Email Deliverability
After DNS propagation (24-48 hours):
1. Go to https://www.mail-tester.com/
2. Send test email from your site
3. Check score (aim for 10/10)
4. Test delivery to Gmail, Outlook, Yahoo

---

## 2. cPanel Upload & Setup

### Step 1: Upload Files
1. Log into cPanel
2. Go to **File Manager**
3. Navigate to `public_html`
4. Upload entire `/php_website/` contents
5. Extract if zipped

### Step 2: Set File Permissions
```bash
# Directories
chmod 755 public_html/
chmod 755 uploads/
chmod 755 admin/
chmod 755 api/

# Files
chmod 644 *.php
chmod 644 *.css
chmod 644 *.js
chmod 600 config.php  # Restrict config access
chmod 644 .htaccess
chmod 644 robots.txt
chmod 644 sitemap.xml

# Uploads (writable)
chmod 755 uploads/
chmod 755 uploads/products/
chmod 755 uploads/categories/
chmod 755 uploads/gallery/
```

**Never use 777 permissions!**

### Step 3: Create MySQL Database
1. cPanel → **MySQL Databases**
2. Create database: `arvon_db`
3. Create user: `arvon_user`
4. Set strong password (save it!)
5. Add user to database with **ALL PRIVILEGES**

### Step 4: Import Database
1. cPanel → **phpMyAdmin**
2. Select `arvon_db`
3. Click **Import** tab
4. Choose `database.sql`
5. Click **Go**

### Step 5: Configure Settings
Edit `config.php`:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'arvon_user');        // Your DB username
define('DB_PASS', 'your_db_password');  // Your DB password
define('DB_NAME', 'arvon_db');

// Site
define('SITE_URL', 'https://arvon.pk'); // Your domain

// Email (Spacemail)
define('SMTP_USERNAME', 'info@arvon.pk');
define('SMTP_PASSWORD', 'your_spacemail_password');
define('SMTP_FROM_EMAIL', 'no-reply@arvon.pk');
```

---

## 3. Admin Panel Security

### Option A: IP Whitelist (Recommended)
Edit `/admin/.htaccess`:
```apache
<RequireAny>
    Require ip YOUR_HOME_IP_HERE
    Require ip YOUR_OFFICE_IP_HERE
</RequireAny>
```

Get your IP: https://whatismyipaddress.com/

### Option B: Password Protection
```bash
# In cPanel, go to: Password Protect Directories
# Select: /admin/
# Set username & password
```

### Option C: Custom Admin URL
Rename `/admin/` to `/secret-panel-xyz123/`
Update all references in code.

### Change Default Admin Password
1. Visit: `https://arvon.pk/admin/`
2. Login: `admin` / `admin123`
3. Go to Settings → Change Password
4. Use strong password (16+ chars)

---

## 4. Backup & Restore

### Manual Backup
1. **Database:**
   ```bash
   cPanel → phpMyAdmin → Export → Go
   Save file: arvon_backup_YYYY-MM-DD.sql
   ```

2. **Uploads Folder:**
   ```bash
   cPanel → File Manager → Compress /uploads/
   Download: uploads_backup_YYYY-MM-DD.zip
   ```

### Automated Backup (cPanel)
1. cPanel → **Backup Wizard**
2. Set schedule: Daily/Weekly
3. Email notifications: your@email.com
4. Storage: Home directory

### Restore Process
1. **Database:**
   - phpMyAdmin → Import → Choose backup SQL
2. **Uploads:**
   - Extract uploads_backup.zip to /uploads/

---

## 5. SEO Configuration

### Edit Meta Tags (All Pages)
Each page has editable meta tags:
```php
// In each .php file
<title>Page Title - ARVON</title>
<meta name="description" content="Your page description">
<meta name="keywords" content="apparel, manufacturing, pakistan">
```

### Sitemap
- Located at: `https://arvon.pk/sitemap.xml`
- Submit to Google Search Console
- Submit to Bing Webmaster Tools

### Google Search Console
1. Verify ownership: https://search.google.com/search-console
2. Submit sitemap
3. Request indexing

---

## 6. SSL Certificate (HTTPS)

### Enable in cPanel
1. cPanel → **SSL/TLS**
2. Enable **AutoSSL** (free Let's Encrypt)
3. Or install custom SSL certificate

### Force HTTPS
Already configured in `.htaccess`:
```apache
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 7. Testing Checklist

### Functionality Tests
- [ ] Homepage loads
- [ ] All navigation links work
- [ ] Product search works
- [ ] Contact form submits
- [ ] Email received at info@arvon.pk
- [ ] Reply-To header set correctly
- [ ] Admin login works
- [ ] Image uploads work
- [ ] Products CRUD works
- [ ] Categories CRUD works
- [ ] Mobile responsive

### Security Tests
- [ ] Cannot access config.php via browser
- [ ] Cannot access database.sql via browser
- [ ] Admin requires login
- [ ] Session timeout works
- [ ] CSRF protection active
- [ ] Upload restrictions work (only images)

### Email Tests
- [ ] Contact form email sends
- [ ] FROM: no-reply@arvon.pk
- [ ] TO: info@arvon.pk
- [ ] Reply-To: user's email
- [ ] HTML formatting works
- [ ] Deliverability to Gmail ✓
- [ ] Deliverability to Outlook ✓
- [ ] Deliverability to Yahoo ✓
- [ ] Not in spam folder

### Performance Tests
- [ ] Page load < 3 seconds
- [ ] Images optimized
- [ ] Gzip compression enabled
- [ ] Browser caching works

---

## 8. Maintenance

### Regular Tasks
**Weekly:**
- Check contact messages
- Backup database

**Monthly:**
- Review error logs
- Update products
- Check email deliverability

**Quarterly:**
- Change admin password
- Review security logs
- Update content

### Error Logs
Located at: `/error_log.txt`
Review regularly for issues.

### Monitor Uptime
Use: https://uptimerobot.com (free)

---

## 9. Common Issues & Solutions

### Issue: Emails not sending
**Solution:**
1. Check SMTP credentials in config.php
2. Verify Spacemail account active
3. Check DNS records propagated
4. Review /error_log.txt

### Issue: Images not uploading
**Solution:**
1. Check /uploads/ permissions (755)
2. Verify PHP upload_max_filesize
3. Check disk space

### Issue: Admin can't login
**Solution:**
1. Clear browser cache
2. Check session permissions
3. Reset password via phpMyAdmin

### Issue: 500 Internal Server Error
**Solution:**
1. Check .htaccess syntax
2. Review error logs
3. Verify file permissions
4. Check PHP version (7.4+)

---

## 10. Support Contacts

### Hosting Issues
- Contact your cPanel hosting provider

### Email Issues
- Spacemail Support: support@spacemail.com

### DNS Issues
- Your domain registrar support

---

## Final Launch Checklist

- [ ] Database imported
- [ ] config.php configured
- [ ] SMTP credentials added
- [ ] DNS records configured (SPF, DKIM, DMARC)
- [ ] SSL certificate active
- [ ] Admin password changed
- [ ] Admin access restricted (IP/password)
- [ ] File permissions set correctly
- [ ] Backup system configured
- [ ] Test emails sent successfully
- [ ] All pages tested
- [ ] Mobile responsiveness checked
- [ ] Sitemap submitted to Google
- [ ] robots.txt configured
- [ ] Error pages work (404, 500)
- [ ] Remove dummy data via admin

---

## Quick Reference

**Admin URL:** https://arvon.pk/admin/
**Default Login:** admin / admin123 (CHANGE IMMEDIATELY)
**Email Setup:** no-reply@arvon.pk → info@arvon.pk
**Backup:** Weekly automatic via cPanel
**Support:** Review error_log.txt for issues

---

**🎉 Your website is now LIVE and OWNER-MANAGED!**

No developer needed for:
✅ Content updates
✅ Products & categories
✅ Images & gallery
✅ Contact messages
✅ Settings & configuration

Everything is controlled from your admin dashboard.
