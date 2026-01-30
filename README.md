# ARVON Website - cPanel Ready

## 🚀 Quick Setup Guide

### Step 1: Upload Files
1. Log into your cPanel account
2. Go to **File Manager**
3. Navigate to `public_html` folder
4. Upload ALL files from this folder
5. Extract if needed

### Step 2: Create Database
1. Go to **cPanel > MySQL Databases**
2. Create a new database (e.g., `arvon_db`)
3. Create a database user
4. Add user to database with ALL PRIVILEGES
5. Note down:
   - Database name
   - Database username
   - Database password

### Step 3: Import Database
1. Go to **cPanel > phpMyAdmin**
2. Select your database
3. Click **Import** tab
4. Choose `database.sql` file
5. Click **Go**

### Step 4: Configure
1. Edit `config.php` file
2. Update these lines:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_db_username');    // Change this
   define('DB_PASS', 'your_db_password');    // Change this
   define('DB_NAME', 'arvon_db');            // Change this
   define('SITE_URL', 'https://arvon.pk');   // Change this
   ```

### Step 5: Admin Access
- URL: `https://arvon.pk/admin`
- Default Username: `admin`
- Default Password: `admin123`
- **CHANGE PASSWORD IMMEDIATELY!**

### Step 6: Test
Visit your website: `https://arvon.pk`

## 📁 Folder Structure
```
public_html/
├── index.php              # Homepage
├── about.php             # About page
├── products.php          # Products listing
├── manufacturing.php     # Manufacturing page
├── gallery.php           # Gallery page
├── contact.php           # Contact form
├── config.php            # Database config
├── database.sql          # Database schema
├── admin/                # Admin dashboard
│   ├── index.php
│   ├── login.php
│   └── ...
├── api/                  # Backend APIs
│   ├── products.php
│   ├── categories.php
│   ├── contact.php
│   └── gallery.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── includes/
    ├── header.php
    └── footer.php
```

## ✅ Features
- ✅ No build process required
- ✅ Works on any cPanel hosting
- ✅ Admin dashboard to manage everything
- ✅ Contact form with email notifications
- ✅ Product search & filtering
- ✅ Category management
- ✅ Gallery management
- ✅ Mobile responsive
- ✅ SEO friendly

## 🔐 Security
1. Change admin password immediately
2. Use strong database password
3. Enable HTTPS (SSL certificate)
4. Keep backups via cPanel

## 📧 Email Setup
Contact form emails go to: `info@arvon.pk`

To ensure emails work:
1. cPanel > Email Accounts
2. Create `info@arvon.pk` mailbox
3. PHP mail() will work automatically

## 🆘 Troubleshooting

**500 Internal Server Error?**
- Check file permissions (755 for folders, 644 for files)
- Check .htaccess file

**Database connection error?**
- Verify config.php credentials
- Check database user has privileges

**Contact form not working?**
- Ensure email account exists
- Check PHP mail is enabled

## 📱 Support
For issues, check error logs in cPanel or contact your hosting provider.

---
**Built with ❤️ for easy cPanel deployment**
