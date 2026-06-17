# PackNGo — Free Hosting Deployment Guide
# =========================================================
# Compatible with: InfinityFree, FreeSQLdatabase, Freehosting.com, AwardSpace
# PHP: 8.1+ | MySQL: 5.7+ | SMTP Email: Gmail App Password

---

## 🚀 Recommended Free Hosting Options

| Host | PHP | MySQL | HTTPS | Email (SMTP) | File Manager | Notes |
|------|-----|-------|-------|--------------|--------------|-------|
| **InfinityFree** | ✅ 8.x | ✅ | ✅ Free SSL | ✅ External SMTP | ✅ cPanel | Best overall free host |
| **Freehosting.com** | ✅ 8.x | ✅ | ✅ | ✅ External SMTP | ✅ cPanel | 10 GB space |
| **AwardSpace** | ✅ 7.4+ | ✅ | ✅ | ✅ (limited) | ✅ | Good uptime |
| **000webhost** | ✅ 7.x | ✅ | ✅ | ❌ PHP mail only | ✅ | Limited PHP version |
| **Byet Host** | ✅ 8.x | ✅ | ✅ | ✅ External SMTP | ✅ | InfinityFree sister |

> **Recommendation: Use InfinityFree** — it supports PHP 8.x, free MySQL, free SSL
> (Let's Encrypt), cPanel file manager, and allows external SMTP (Gmail).

---

## 📋 PRE-DEPLOYMENT CHECKLIST

Before uploading, make sure you have:
- [ ] Free hosting account created (InfinityFree recommended)
- [ ] MySQL database created from your hosting control panel
- [ ] Database credentials (host, name, user, password) noted
- [ ] Gmail App Password ready for email OTP

---

## STEP 1 — REGISTER & SETUP HOSTING

### InfinityFree (Recommended)
1. Go to: **https://www.infinityfree.com**
2. Sign up for a free account
3. Click **"Create Account"** → choose a free subdomain like `packngo.epizy.com`
4. Go to **Control Panel → MySQL Databases**
5. Create a new database — note the **hostname, database name, username, password**

---

## STEP 2 — CONFIGURE YOUR PROJECT

### Option A — Using `.env` file (recommended)
Edit the `.env` file in the project root:

```ini
APP_NAME="PackNGo"
APP_ENV=production
APP_URL=https://packngo.epizy.com     ← Your actual domain
APP_DEBUG=false
APP_SECRET_KEY=your_random_64_char_key_here

DB_HOST=sql123.infinityfree.com       ← From cPanel MySQL info
DB_PORT=3306
DB_NAME=epiz_12345678_packngo         ← Your database name
DB_USER=epiz_12345678_user            ← Your database username
DB_PASS=your_db_password

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your_16_char_app_password
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="PackNGo Concierge"
ADMIN_EMAIL=admin@yourdomain.com
```

### Option B — Using `config.php` (if .env doesn't load on your host)
1. Copy `config.sample.php` → rename it to `config.php`
2. Fill in the same values as above
3. This file is auto-loaded as a fallback to `.env`

> ⚠️ **IMPORTANT:** Change `APP_SECRET_KEY` to a random 64-character string.
> You can generate one at: https://www.allkeysgenerator.com/Random/Security-Encryption-Key-Generator.aspx

---

## STEP 3 — SETUP GMAIL SMTP (for OTP Emails)

1. Enable **2-Factor Authentication** on your Gmail account:
   → https://myaccount.google.com/security

2. Generate an **App Password**:
   → https://myaccount.google.com/apppasswords
   → Select: Mail → Other (name it "PackNGo")
   → Copy the 16-character password

3. Set in `.env` or `config.php`:
   ```ini
   MAIL_USERNAME=your@gmail.com
   MAIL_PASSWORD=abcd efgh ijkl mnop   ← 16-char app password (spaces ok)
   ```

> **Alternative SMTP providers** (higher limits than Gmail):
> - **Brevo (Sendinblue)**: 300 emails/day free → smtp-relay.brevo.com port 587
> - **Mailjet**: 200 emails/day free → in-v3.mailjet.com port 587
> - **Elastic Email**: 100 emails/day free

---

## STEP 4 — UPLOAD FILES

### Using cPanel File Manager (InfinityFree):
1. Log in to cPanel → **File Manager**
2. Navigate to `public_html/`
3. Upload the entire project ZIP → Extract it
4. Make sure files are at: `public_html/` (root) OR `public_html/PackNGo/`
5. If in subdirectory, open `.htaccess` and uncomment:
   ```apache
   RewriteBase /PackNGo/
   ```

### Using FTP (FileZilla):
- Host: Your FTP hostname from cPanel
- Port: 21
- Upload everything to `public_html/`

---

## STEP 5 — IMPORT DATABASE

### Via phpMyAdmin (easiest):
1. In cPanel → **phpMyAdmin**
2. Select your database from the left panel
3. Click **Import** → Choose file: `database/schema.sql`
4. Click **Go** → Wait for success message

### Via Migration Script (online browser method):
1. Open: `https://yourdomain.com/database/migrate.php?secret=YOUR_SECRET_KEY`
2. Replace `YOUR_SECRET_KEY` with your `APP_SECRET_KEY` from config
3. You should see: "✔ Migration complete."
4. **DELETE** the migrate.php file immediately after!

---

## STEP 6 — SET FOLDER PERMISSIONS

Using cPanel File Manager → Right-click → Change Permissions:

| Folder | Permission |
|--------|------------|
| `public/uploads/` | **755** |
| `public/uploads/gallery/` | **755** |
| `logs/` | **755** |
| `storage/` | **755** |
| `.env` | **644** (readable, not executable) |

---

## STEP 7 — CONFIGURE SSL (HTTPS)

### InfinityFree Free SSL:
1. cPanel → **Softaculous SSL** or **Let's Encrypt SSL**
2. Install certificate for your domain
3. Once done, update `.env`:
   ```ini
   APP_URL=https://yourdomain.com
   ```

### Force HTTPS (add to `.htaccess` at the top):
```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
```

---

## STEP 8 — TEST YOUR INSTALLATION

After upload, test these URLs:

| Test | URL |
|------|-----|
| Homepage | `https://yourdomain.com` |
| Reservation form | `https://yourdomain.com/Reservation.html` |
| Admin panel | `https://yourdomain.com/admin/index.php` |
| API health check | `https://yourdomain.com/index.php?_url=/api/packages` |

**Default Admin Login:**
- Email: `admin@packngo.store`
- Password: `Admin@PackNGo123`
- ⚠️ **Change this immediately after first login!**

---

## STEP 9 — POST-DEPLOYMENT SECURITY

After everything works:
- [ ] **Delete** `database/migrate.php`
- [ ] **Delete** `database/seed_admin.php`
- [ ] Set `APP_DEBUG=false` in `.env`
- [ ] Change default admin password
- [ ] Verify `.env` is not publicly accessible (test: `https://yourdomain.com/.env` should return 403)
- [ ] Enable HSTS in `.htaccess` (uncomment the HSTS line)

---

## 🔧 TROUBLESHOOTING

### 500 Internal Server Error
- Check `.htaccess` — comment out `RewriteBase` if not in subfolder
- Verify `APP_DEBUG=true` temporarily to see errors
- Check `logs/app.log` via File Manager

### Database Connection Failed
- Verify DB hostname — on InfinityFree it's like `sql123.infinityfree.com`, NOT `localhost`
- Check DB credentials exactly from cPanel
- Make sure database was created before running schema

### Emails Not Sending
- Verify Gmail App Password (not regular password)
- Make sure 2FA is enabled on Gmail
- Try Brevo as alternative SMTP

### .env Not Loading
- Use `config.php` fallback instead (copy `config.sample.php` → `config.php`)
- Verify file is in project root (same level as `index.php`)

### Admin Panel Not Loading
- Access via `/admin/index.php` not `/admin/`
- Check session cookies are working (HTTPS recommended)

---

## 📧 SMTP Alternatives for Free Email

If Gmail blocks or limits your emails:

| Provider | Free Limit | SMTP Host | Port |
|----------|-----------|-----------|------|
| Brevo | 300/day | smtp-relay.brevo.com | 587 |
| Mailjet | 200/day | in-v3.mailjet.com | 587 |
| SendGrid | 100/day | smtp.sendgrid.net | 587 |
| Elastic Email | 100/day | smtp.elasticemail.com | 2525 |

For any of these, get credentials from their dashboard and update:
```ini
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_login
MAIL_PASSWORD=your_brevo_smtp_key
```

---

*PackNGo — Luxury Travel Platform | Deployment Guide v1.0*
