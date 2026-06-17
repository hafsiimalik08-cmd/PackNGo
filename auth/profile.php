<?php
require_once __DIR__ . '/_header.php';
if (!Session::isLoggedIn()) {
    header('Location: ' . $appUrl . '/auth/login.php');
    exit;
}
$user    = Session::user();
$saved   = ($_GET['saved']    ?? '') === '1';
$pwSaved = ($_GET['pw_saved'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile — PackNGo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:#F5EFE6;font-family:'Times New Roman',Georgia,serif;color:#1F3D36;min-height:100vh;}
        header{background:#1F3D36;position:fixed;width:100%;top:0;z-index:1000;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .header-flex{display:flex;justify-content:space-between;align-items:center;width:90%;max-width:1400px;margin:auto;height:70px;}
        .logo{color:#D4AF37;font-size:1.6rem;letter-spacing:3px;font-weight:bold;text-decoration:none;}
        nav ul{list-style:none;display:flex;align-items:center;gap:22px;flex-wrap:wrap;}
        nav ul li a{color:#F5EFE6;text-decoration:none;font-size:12px;text-transform:uppercase;letter-spacing:1px;transition:0.3s;}
        nav ul li a:hover{color:#D4AF37;}
        .nav-btn{background:#D4AF37;color:#1F3D36!important;padding:8px 18px;border-radius:2px;font-weight:bold;}
        .nav-btn-outline{border:1px solid #D4AF37;color:#D4AF37!important;padding:7px 16px;border-radius:2px;}
        .page-wrap{max-width:800px;margin:0 auto;padding:100px 20px 50px;}
        .profile-header{background:#1F3D36;color:#F5EFE6;border-radius:6px 6px 0 0;padding:32px 40px;display:flex;align-items:center;gap:24px;}
        .profile-avatar{width:72px;height:72px;background:#D4AF37;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:bold;color:#1F3D36;flex-shrink:0;}
        .profile-name{font-size:1.5rem;color:#D4AF37;font-weight:bold;letter-spacing:1px;}
        .profile-meta{font-size:13px;color:rgba(245,239,230,0.7);margin-top:4px;}
        .profile-badge{display:inline-block;background:rgba(212,175,55,0.2);border:1px solid #D4AF37;color:#D4AF37;font-size:11px;padding:2px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:1px;margin-top:6px;}
        .card{background:#fff;border:1px solid #D4AF37;border-top:none;padding:36px 40px;margin-bottom:20px;}
        .card:last-child{border-radius:0 0 6px 6px;margin-bottom:0;}
        .card-title{font-size:1rem;text-transform:uppercase;letter-spacing:1.5px;color:#1F3D36;font-weight:bold;margin-bottom:24px;padding-bottom:12px;border-bottom:2px solid #D4AF37;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .form-group{margin-bottom:18px;}
        label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:0.8px;color:#888;margin-bottom:6px;font-weight:bold;}
        input[type=text],input[type=email],input[type=tel],input[type=password]{width:100%;padding:11px 14px;border:1.5px solid #ddd;background:#fafaf8;font-family:'Times New Roman',serif;font-size:15px;color:#1F3D36;border-radius:3px;outline:none;transition:border-color .3s;}
        input:focus{border-color:#D4AF37;background:#fff;}
        input[readonly]{background:#f0f0ec;color:#888;cursor:default;}
        .field-error{color:#c0392b;font-size:12px;margin-top:4px;display:none;}
        .field-error.show{display:block;}
        .alert{padding:12px 16px;border-radius:3px;margin-bottom:20px;font-size:14px;display:none;}
        .alert.show{display:block;}
        .alert-error{background:#fdf0ef;border:1px solid #e74c3c;color:#c0392b;}
        .alert-success{background:#eafaf1;border:1px solid #27ae60;color:#1e8449;}
        .btn-gold{background:#D4AF37;color:#1F3D36;border:none;padding:12px 28px;font-family:'Times New Roman',serif;font-size:14px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;cursor:pointer;border-radius:3px;transition:background .3s;}
        .btn-gold:hover{background:#b8962e;}
        .btn-gold:disabled{opacity:.6;cursor:not-allowed;}
        .btn-row{display:flex;gap:12px;margin-top:6px;}
        .password-wrap{position:relative;}
        .password-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:#888;cursor:pointer;font-size:14px;}
        .small-note{font-size:12px;color:#888;display:block;margin-top:4px;}
        @media(max-width:600px){.form-row{grid-template-columns:1fr;} .card{padding:24px 20px;} .profile-header{flex-direction:column;text-align:center;padding:24px 20px;} .btn-row{flex-direction:column;}}
    </style>
</head>
<body>
<header>
    <div class="container header-flex">
        <a href="<?= $appUrl ?>/index.html" class="logo">✈ PackNGo</a>
        <nav><ul>
            <li><a href="<?= $appUrl ?>/index.html">Home</a></li>
            <li><a href="<?= $appUrl ?>/Reservation.html">Book Now</a></li>
            <li><a href="<?= $appUrl ?>/auth/profile.php" class="nav-btn-outline">My Profile</a></li>
            <li><a href="<?= $appUrl ?>/auth/logout.php" class="nav-btn">Logout</a></li>
        </ul></nav>
    </div>
</header>

<div class="page-wrap">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar"><?= htmlspecialchars(strtoupper(mb_substr($user['first_name'], 0, 1) . mb_substr($user['last_name'], 0, 1)), ENT_QUOTES) ?></div>
        <div>
            <div class="profile-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES) ?></div>
            <div class="profile-meta"><?= htmlspecialchars($user['email'], ENT_QUOTES) ?></div>
            <div class="profile-meta">Member since <?= date('F Y', strtotime($user['created_at'])) ?></div>
            <div class="profile-badge"><?= htmlspecialchars($user['role'], ENT_QUOTES) ?></div>
        </div>
    </div>

    <!-- Edit Profile -->
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-user-pen"></i> Personal Information</div>

        <?php if ($saved): ?>
        <div class="alert alert-success show">✓ Profile updated successfully.</div>
        <?php endif; ?>
        <div class="alert alert-error"   id="alertError"></div>
        <div class="alert alert-success" id="alertSuccess"></div>

        <form id="profileForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user['first_name'], ENT_QUOTES) ?>" required>
                    <span class="field-error" id="err_first_name"></span>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user['last_name'], ENT_QUOTES) ?>" required>
                    <span class="field-error" id="err_last_name"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>" readonly>
                <span class="small-note">Email cannot be changed. Contact support if needed.</span>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES) ?>" placeholder="03001234567">
                <span class="field-error" id="err_phone"></span>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn-gold" id="profileBtn">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password -->
    <div class="card">
        <div class="card-title"><i class="fa-solid fa-shield-halved"></i> Change Password</div>

        <?php if ($pwSaved): ?>
        <div class="alert alert-success show">✓ Password changed successfully.</div>
        <?php endif; ?>
        <div class="alert alert-error"   id="pwAlertError"></div>
        <div class="alert alert-success" id="pwAlertSuccess"></div>

        <form id="passwordForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <div class="password-wrap">
                    <input type="password" id="current_password" name="current_password" placeholder="Your current password" autocomplete="current-password">
                    <button type="button" class="password-toggle" onclick="togglePwd('current_password',this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                <span class="field-error" id="err_current_password"></span>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-wrap">
                        <input type="password" id="new_password" name="new_password" placeholder="At least 8 characters" autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePwd('new_password',this)"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <span class="field-error" id="err_new_password"></span>
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Confirm New Password</label>
                    <div class="password-wrap">
                        <input type="password" id="new_password_confirm" name="new_password_confirm" placeholder="Repeat new password" autocomplete="new-password">
                        <button type="button" class="password-toggle" onclick="togglePwd('new_password_confirm',this)"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <span class="field-error" id="err_new_password_confirm"></span>
                </div>
            </div>
            <div class="btn-row">
                <button type="submit" class="btn-gold" id="pwBtn">
                    <i class="fa-solid fa-key"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const API = '<?= $appUrl ?>/index.php';

function togglePwd(id, btn) {
    const inp = document.getElementById(id);
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.innerHTML = show ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
}

function showFieldErr(field, msg) {
    const el = document.getElementById('err_' + field);
    if (el) { el.textContent = msg; el.classList.add('show'); }
}

// ── Profile Form ────────────────────────────────────────────────
document.getElementById('profileForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const csrf = document.querySelector('#profileForm [name="_csrf"]').value;
    const data = {
        _csrf:      csrf,
        first_name: document.getElementById('first_name').value.trim(),
        last_name:  document.getElementById('last_name').value.trim(),
        phone:      document.getElementById('phone').value.trim(),
    };

    document.querySelectorAll('#profileForm .field-error').forEach(el => { el.textContent = ''; el.classList.remove('show'); });
    document.getElementById('alertError').classList.remove('show');
    document.getElementById('alertSuccess').classList.remove('show');

    if (!data.first_name) { showFieldErr('first_name', 'First name is required.'); return; }
    if (!data.last_name)  { showFieldErr('last_name', 'Last name is required.'); return; }

    const btn = document.getElementById('profileBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving...';

    try {
        const res  = await fetch(API + '/api/auth/profile/update', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify(data), credentials: 'include'
        });
        const json = await res.json();
        if (json.success) {
            document.getElementById('alertSuccess').textContent = '✓ Profile updated successfully.';
            document.getElementById('alertSuccess').classList.add('show');
        } else if (json.errors) {
            Object.entries(json.errors).forEach(([f, msgs]) => showFieldErr(f, msgs[0]));
        } else {
            document.getElementById('alertError').textContent = json.message || 'Update failed.';
            document.getElementById('alertError').classList.add('show');
        }
    } catch {
        document.getElementById('alertError').textContent = 'Network error. Please try again.';
        document.getElementById('alertError').classList.add('show');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Save Changes';
    }
});

// ── Password Form ───────────────────────────────────────────────
document.getElementById('passwordForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const csrf    = document.querySelector('#passwordForm [name="_csrf"]').value;
    const current = document.getElementById('current_password').value;
    const newPwd  = document.getElementById('new_password').value;
    const confirm = document.getElementById('new_password_confirm').value;

    document.querySelectorAll('#passwordForm .field-error').forEach(el => { el.textContent = ''; el.classList.remove('show'); });
    document.getElementById('pwAlertError').classList.remove('show');
    document.getElementById('pwAlertSuccess').classList.remove('show');

    let hasErr = false;
    if (!current) { showFieldErr('current_password', 'Current password is required.'); hasErr = true; }
    if (newPwd.length < 8) { showFieldErr('new_password', 'Must be at least 8 characters.'); hasErr = true; }
    if (newPwd !== confirm) { showFieldErr('new_password_confirm', 'Passwords do not match.'); hasErr = true; }
    if (hasErr) return;

    const btn = document.getElementById('pwBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Changing...';

    try {
        const res  = await fetch(API + '/api/auth/profile/change-password', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ _csrf: csrf, current_password: current, new_password: newPwd }), credentials: 'include'
        });
        const json = await res.json();
        if (json.success) {
            document.getElementById('pwAlertSuccess').textContent = '✓ Password changed successfully.';
            document.getElementById('pwAlertSuccess').classList.add('show');
            document.getElementById('passwordForm').reset();
        } else if (json.errors) {
            Object.entries(json.errors).forEach(([f, msgs]) => showFieldErr(f, msgs[0]));
        } else {
            document.getElementById('pwAlertError').textContent = json.message || 'Failed to change password.';
            document.getElementById('pwAlertError').classList.add('show');
        }
    } catch {
        document.getElementById('pwAlertError').textContent = 'Network error. Please try again.';
        document.getElementById('pwAlertError').classList.add('show');
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-key"></i> Change Password';
    }
});
</script>
</body>
</html>
