# PackNGo — Complete Backend Setup Guide
# =====================================================================
# Compatible with: XAMPP (local), InfinityFree, 000webhost,
#                  AwardSpace, FreeHostia (free hosting)
# PHP requirement: 8.1+  |  MySQL: 5.7+ / MariaDB 10.3+
# =====================================================================

## ── PROJECT FOLDER STRUCTURE ──────────────────────────────────────

```
PackNGo/                         ← Place this in htdocs/ (XAMPP)
├── index.html                   ← Frontend home page (UNCHANGED)
├── About.html                   ← Frontend about page (UNCHANGED)
├── Blogs.html                   ← Frontend blogs page (UNCHANGED)
├── Gallery.html                 ← Frontend gallery page (UNCHANGED)
├── Reservation.html             ← Frontend booking page (UNCHANGED)
├── Travel.mp4                   ← Hero video (UNCHANGED)
├── Images/                      ← All original images (UNCHANGED)
├── api-client.js                ← ★ ADD this script tag to every HTML page
│
├── index.php                    ← PHP entry point (backend router)
├── .htaccess                    ← URL rewriting + security headers
├── .env                         ← Your private config (copy from .env.example)
├── .env.example                 ← Template (commit this, NOT .env)
├── composer.json                ← PHP dependency declaration
│
├── config/
│   ├── app.php                  ← Config loader (reads .env)
│   └── Database.php             ← PDO singleton connection class
│
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php       ← Register / Login / Logout / Password reset
│   │   ├── ReservationController.php← Booking form submit + status check
│   │   ├── DestinationController.php← Destinations & Packages API
│   │   ├── BlogController.php       ← Blog posts & categories API
│   │   ├── GalleryController.php    ← Gallery images API
│   │   ├── NewsletterController.php ← Subscribe / unsubscribe
│   │   ├── ContactController.php    ← Contact form
│   │   ├── UploadController.php     ← Admin image uploads
│   │   └── AdminController.php      ← Admin CRUD dashboard API
│   │
│   └── Models/
│       ├── BaseModel.php            ← Shared DB helpers (insert/update/paginate)
│       ├── UserModel.php            ← Auth, bcrypt, token management
│       ├── ReservationModel.php     ← Booking reference generation, status
│       ├── DestinationModel.php     ← Destination search & filter
│       ├── PackageModel.php         ← Tour packages
│       ├── BlogModel.php            ← Posts, categories, featured
│       ├── GalleryModel.php         ← Gallery image metadata
│       ├── NewsletterModel.php      ← Subscribe/unsubscribe token
│       ├── ContactModel.php         ← Enquiry messages
│       └── BlogModel.php
│
├── routes/
│   ├── api.php                  ← Route table (all API endpoints)
│   └── routes.php               ← Router class (dispatches requests)
│
├── middleware/
│   ├── CsrfMiddleware.php       ← Double-submit CSRF protection
│   └── AdminAuthMiddleware.php  ← Admin session guard
│
├── helpers/
│   ├── Validator.php            ← Fluent input validation
│   ├── Sanitizer.php            ← Input cleaning (XSS prevention)
│   ├── Response.php             ← JSON response + HTTP codes
│   ├── RateLimiter.php          ← IP-based rate limiting (DB-backed)
│   ├── Session.php              ← Secure session wrapper
│   └── Mailer.php               ← Email sending (SMTP + fallback)
│
├── admin/
│   ├── index.html               ← Full Admin Dashboard (single-page app)
│   └── .htaccess                ← Protects admin folder
│
├── database/
│   ├── schema.sql               ← All tables + seed data (run once)
│   └── migrate.php              ← Setup script (run then DELETE)
│
├── public/
│   └── uploads/
│       └── gallery/             ← Uploaded gallery images (writable)
│
├── logs/                        ← PHP error logs (writable, never public)
└── storage/
    └── cache/                   ← Future caching (writable)
```

---

## ── STEP 1: COPY FILES ────────────────────────────────────────────

1. Copy your existing frontend files into the PackNGo folder:
   ```
   C:\xampp\htdocs\PackNGo\
   ```

2. Copy all backend files into the SAME folder (they sit alongside the HTML).

---

## ── STEP 2: ADD API CLIENT TO EACH HTML PAGE ──────────────────────

Add this ONE line before `</body>` in EACH of your HTML files:

**index.html, About.html, Gallery.html:**
```html
<script src="api-client.js"></script>
```

**Reservation.html:**  (replaces the existing handleBooking with real API)
```html
<script src="api-client.js"></script>
```

**Blogs.html:**  (connects subscribeNewsletter() to real API)
```html
<script src="api-client.js"></script>
```

That's it. No other HTML changes needed.

---

## ── STEP 3: CONFIGURE ENVIRONMENT ────────────────────────────────

1. Copy `.env.example` to `.env`:
   ```
   copy .env.example .env
   ```

2. Edit `.env` — minimum required settings for XAMPP:
   ```ini
   APP_URL=http://localhost/PackNGo
   APP_DEBUG=true
   APP_SECRET_KEY=any_random_64_char_string_here

   DB_HOST=127.0.0.1
   DB_USER=root
   DB_PASS=
   DB_NAME=packngo_db
   ```

3. For email (Gmail recommended):
   - Enable 2-Factor Auth on your Gmail
   - Create an App Password at: https://myaccount.google.com/apppasswords
   ```ini
   MAIL_USERNAME=your@gmail.com
   MAIL_PASSWORD=your_16_char_app_password
   MAIL_FROM_ADDRESS=your@gmail.com
   ADMIN_EMAIL=your@gmail.com
   ```

---

## ── STEP 4: CREATE DATABASE ───────────────────────────────────────

### Option A — phpMyAdmin (easiest):
1. Open http://localhost/phpmyadmin
2. Click "New" → create database named: `packngo_db`
3. Select `packngo_db` → click "Import"
4. Choose `database/schema.sql` → click "Go"

### Option B — Migration Script:
1. Open: http://localhost/PackNGo/database/migrate.php?secret=YOUR_APP_SECRET_KEY
   (Replace YOUR_APP_SECRET_KEY with the value in your .env)

### Option C — Command Line:
```bash
mysql -u root -e "CREATE DATABASE packngo_db CHARACTER SET utf8mb4;"
mysql -u root packngo_db < database/schema.sql
```

**After setup: DELETE database/migrate.php** (security risk if left public).

**Upgrading an existing database?** If `packngo_db` already existed before the
admin "Reply to Feedback" feature was added, run the small migration below
once (fresh installs already get these columns from `schema.sql`):
```bash
mysql -u root packngo_db < database/add_contact_reply_columns.sql
```

---

## ── STEP 5: INSTALL PHPMAILER (Optional but recommended) ──────────

If Composer is installed:
```bash
cd C:\xampp\htdocs\PackNGo
composer install
```

If Composer is not installed, email still works via PHP mail() locally.
For production/hosting, install Composer or upload the vendor/ folder.

---

## ── STEP 6: SET FOLDER PERMISSIONS ──────────────────────────────

Make these folders writable (chmod 755 on Linux/hosting):
```
public/uploads/gallery/
logs/
storage/cache/
```

On XAMPP (Windows) these are writable by default.
On Linux hosting:
```bash
chmod 755 public/uploads/gallery logs storage/cache
```

---

## ── STEP 7: TEST ──────────────────────────────────────────────────

1. Frontend:      http://localhost/PackNGo/index.html
2. Booking form:  http://localhost/PackNGo/Reservation.html
3. API health:    http://localhost/PackNGo/index.php?_url=/api/packages
4. Admin panel:   http://localhost/PackNGo/admin/index.html

**Default Admin Login:**
- Email:    admin@packngo.store
- Password: Admin@PackNGo123
⚠️ Change this password immediately after first login.

---

## ── API REFERENCE ─────────────────────────────────────────────────

| Method | Endpoint                          | Description              |
|--------|-----------------------------------|--------------------------|
| GET    | /api/packages                     | All tour packages        |
| GET    | /api/destinations                 | All active destinations  |
| GET    | /api/destinations?cat=beach       | Filter by category       |
| GET    | /api/destinations/search?q=paris  | Search destinations      |
| POST   | /api/reservation/submit           | Submit booking form      |
| GET    | /api/reservation/check?ref=PNG-…  | Check booking status     |
| POST   | /api/newsletter/subscribe         | Subscribe to newsletter  |
| GET    | /api/newsletter/unsubscribe?token=| Unsubscribe              |
| POST   | /api/contact/send                 | Send contact message     |
| GET    | /api/blog/posts                   | Blog posts (paginated)   |
| GET    | /api/blog/categories              | Blog categories          |
| GET    | /api/gallery                      | Gallery images           |
| POST   | /api/auth/login                   | Admin login              |
| POST   | /api/auth/logout                  | Logout                   |
| GET    | /api/admin/dashboard              | Dashboard stats (admin)  |
| GET    | /api/admin/reservations           | All bookings (admin)     |
| PATCH  | /api/admin/reservations/:id/status| Update booking status    |
| GET    | /api/admin/messages               | Unread messages (admin)  |
| POST   | /api/admin/messages/:id/reply      | Reply to a message (admin)|
| DELETE | /api/admin/messages/:id            | Delete a message (admin) |
| GET    | /api/admin/subscribers            | Newsletter list (admin)  |

---

## ── DEPLOYING TO FREE HOSTING ─────────────────────────────────────

### Recommended free hosts (PHP + MySQL):
- **InfinityFree** (https://infinityfree.net) — recommended
- **000webhost**   (https://www.000webhost.com)
- **AwardSpace**   (https://www.awardspace.com)

### Steps:
1. Create account → create a hosting account
2. Note your MySQL host, user, password, DB name from control panel
3. Update `.env` with those values + set `APP_ENV=production` + `APP_DEBUG=false`
4. Upload ALL files via File Manager or FTP (FileZilla)
5. Import `schema.sql` via phpMyAdmin in the control panel
6. Change `RewriteBase` in `.htaccess` if the app is NOT in a sub-folder:
   ```apache
   RewriteBase /         # if at domain root
   RewriteBase /PackNGo/ # if in a sub-folder (default)
   ```
7. Update `APP_URL` in `.env` to your hosting domain
8. Test: https://yourdomain.infinityfreeapp.com/admin/index.html

### Security checklist before going live:
- [ ] `.env` is NOT publicly accessible (test: visit /PackNGo/.env in browser — should 403)
- [ ] `APP_DEBUG=false` in `.env`
- [ ] `database/migrate.php` is deleted
- [ ] Admin password changed from default
- [ ] HTTPS enabled (InfinityFree provides free SSL)
- [ ] MAIL credentials are real (not empty)

---

## ── DATABASE ER DIAGRAM SUMMARY ──────────────────────────────────

```
users ──────────────────────────┐
  id (PK)                       │
  email (UNIQUE)                │
  password_hash                 │ FK: reservations.user_id
  role (customer | admin)       │
                                │
destinations ───────────────────┤
  id (PK)                       │ FK: reservations.destination_id
  slug (UNIQUE)                 │ FK: gallery_images.destination_id
  category (enum)               │
                                │
packages ───────────────────────┤
  id (PK)                       │ FK: reservations.package_id
  slug (UNIQUE)                 │
  price_usd                     │
                                │
reservations ◄──────────────────┘  (CORE TABLE)
  id (PK)
  booking_ref (UNIQUE)  PNG-YYYYMMDD-XXXX
  user_id → users.id
  destination_id → destinations.id
  package_id → packages.id
  status (pending|confirmed|cancelled|completed)

newsletter_subs
  id (PK)
  email (UNIQUE)
  token (unsubscribe)

blog_categories
  id (PK)
  slug (UNIQUE)

blog_posts
  id (PK)
  category_id → blog_categories.id
  slug (UNIQUE)

gallery_images
  id (PK)
  destination_id → destinations.id

contact_messages
  id (PK)
  is_read (boolean)

admin_sessions
  id (PK)
  user_id → users.id

rate_limit_log
  id (PK)
  ip_address + action (composite key)
```
