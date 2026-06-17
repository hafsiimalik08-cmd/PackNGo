# PackNGo — User Authentication System (Added)

This document describes the **authentication system** added on top of your
existing PackNGo frontend + backend. No existing design, CSS, JS, layout, or
database structure was removed — only additive changes were made.

---

## 1. What was added

### New pages (`/auth/`)
All pages match your existing theme (colors `#1F3D36` / `#D4AF37` / `#F5EFE6`,
`Times New Roman`/Georgia font, same header style).

| File                          | Purpose                                          |
|--------------------------------|--------------------------------------------------|
| `auth/_header.php`             | Shared bootstrap: session start, CSRF, config    |
| `auth/signup.php`               | Registration form → `/api/auth/register`         |
| `auth/verify-otp.php`           | 6-digit OTP entry → `/api/auth/verify-otp`        |
| `auth/login.php`                | Login form → `/api/auth/login`                    |
| `auth/logout.php`               | Destroys session, redirects to login              |
| `auth/forgot-password.php`      | Request reset link → `/api/auth/forgot-password`  |
| `auth/reset-password.php`       | Set new password → `/api/auth/reset-password`     |
| `auth/profile.php`              | View/edit profile + change password (auth only)   |

### Modified backend files
| File                                          | Change                                                            |
|------------------------------------------------|--------------------------------------------------------------------|
| `app/Controllers/AuthController.php`           | Added `verifyOtp()`, `resendOtp()`, `updateProfile()`, `changePassword()`; `register()` now sends an OTP instead of a link; `login()` blocks unverified accounts and auto-resends an OTP |
| `app/Models/UserModel.php`                     | Added `generateOtp()`, `verifyOtp()`, `updateProfile()`; `safeData()` strips OTP fields |
| `helpers/Mailer.php`                           | Added `sendOtpEmail()` — styled 6-digit code email                |
| `routes/api.php`                               | Added routes for OTP verify/resend and profile update/change-password |
| `index.html`                                   | Added one **"Login / Register"** link to the existing nav (no other changes) |

### Database
| File                                  | Purpose                                                |
|----------------------------------------|----------------------------------------------------------|
| `database/schema.sql`                  | Updated — `users` table now includes `otp_code`, `otp_expires` (for fresh installs) |
| `database/add_otp_columns.sql`         | **Run this if your database already exists** — adds the two new columns |

---

## 2. Setup steps

1. **If your database already exists**, run the migration once:
   ```bash
   mysql -u root packngo_db < database/add_otp_columns.sql
   ```
   (Fresh installs using `database/schema.sql` already include these columns.)

2. **Make sure your `.env` mail settings are filled in** (`MAIL_USERNAME`,
   `MAIL_PASSWORD`, etc.) — OTP codes are sent via the same `Mailer` class
   used for booking confirmations. If PHPMailer isn't installed
   (`vendor/autoload.php` missing), the system falls back to PHP's `mail()`
   function, which works for local XAMPP testing with a configured SMTP relay.

3. **No other configuration needed.** The `/auth/` pages reuse your existing
   `Database`, `Session`, `CsrfMiddleware`, `Sanitizer`, `Validator`, and
   `Response` classes — same PDO connection, same `.env`.

---

## 3. How the flow works

1. **Register** (`auth/signup.php`) → validates input, hashes password with
   `password_hash()` (bcrypt), inserts user with `email_verified = 0`,
   generates a 6-digit OTP (bcrypt-hashed, 15-minute expiry), emails it.
2. **Verify OTP** (`auth/verify-otp.php`) → user enters the code; on success
   `email_verified` is set to `1` and the OTP is cleared (single-use,
   cannot be replayed).
3. **Login** (`auth/login.php`) → if the account isn't verified yet, a new
   OTP is generated/emailed automatically and the user is redirected back to
   the OTP page. Verified users get a session (`session_regenerate_id`,
   `last_activity` timestamp for timeout).
4. **Profile** (`auth/profile.php`) → shows the logged-in user's data from
   the database (name, email, phone, member-since date, role). Users can
   update first/last name + phone, and change their password (requires
   current password).
5. **Forgot / Reset Password** → unchanged flow, but the reset link now
   points to `auth/reset-password.php?token=...` instead of a non-existent
   frontend route.
6. **Logout** (`auth/logout.php`) → destroys the PHP session and redirects
   to login.

---

## 4. Security measures included

- **PDO prepared statements** everywhere (via existing `BaseModel`/`Database`).
- **Passwords**: `password_hash()` / `password_verify()`, bcrypt cost from `.env`.
- **OTP codes**: stored as bcrypt hashes (not plain text), 15-minute expiry,
  single-use (cleared after success), rate-limited resend (5 / 15 min).
- **CSRF protection**: every mutating request sends `_csrf` (double-submit
  cookie pattern via existing `CsrfMiddleware`).
- **Rate limiting**: register, login, verify-otp, resend-otp, forgot-password
  all use the existing `RateLimiter`.
- **Session security**: `session_regenerate_id()` on login, `Session::destroy()`
  on logout, idle-timeout enforced on every `/auth/*` page load via
  `SESSION_LIFETIME` from `.env`.
- **Input validation/sanitization**: existing `Validator` + `Sanitizer`
  classes used for all new endpoints.
- **No email enumeration**: forgot-password and resend-otp always return a
  generic success message.
