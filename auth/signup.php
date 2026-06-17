<?php
require_once __DIR__ . '/_header.php';
if (Session::isLoggedIn()) {
    header('Location: ' . $appUrl . '/auth/profile.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — PackNGo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:#F5EFE6;font-family:'Times New Roman',Georgia,serif;color:#1F3D36;min-height:100vh;}
        header{background:#1F3D36;position:fixed;width:100%;top:0;z-index:1000;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .header-flex{display:flex;justify-content:space-between;align-items:center;width:90%;max-width:1400px;margin:auto;height:70px;}
        .logo{color:#D4AF37;font-size:1.6rem;letter-spacing:3px;font-weight:bold;text-decoration:none;}
        nav ul{list-style:none;display:flex;align-items:center;gap:22px;}
        nav ul li a{color:#F5EFE6;text-decoration:none;font-size:12px;text-transform:uppercase;letter-spacing:1px;transition:0.3s;}
        nav ul li a:hover{color:#D4AF37;}
        .nav-btn{background:#D4AF37;color:#1F3D36!important;padding:8px 18px;border-radius:2px;font-weight:bold;}
        .page-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:100px 20px 50px;}
        .auth-card{background:#fff;border:1px solid #D4AF37;border-radius:6px;padding:48px 44px;width:100%;max-width:520px;box-shadow:0 10px 40px rgba(31,61,54,0.12);}
        .auth-logo{text-align:center;color:#D4AF37;font-size:2rem;letter-spacing:3px;font-weight:bold;margin-bottom:6px;}
        .auth-subtitle{text-align:center;color:#888;font-size:0.9rem;margin-bottom:30px;}
        .divider-line{width:60px;height:2px;background:#D4AF37;margin:0 auto 28px;}
        .form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .form-group{margin-bottom:20px;}
        label{display:block;font-size:12px;text-transform:uppercase;letter-spacing:0.8px;color:#1F3D36;margin-bottom:7px;font-weight:bold;}
        input{width:100%;padding:12px 16px;border:1.5px solid #ddd;background:#fafaf8;font-family:'Times New Roman',serif;font-size:15px;color:#1F3D36;border-radius:3px;outline:none;transition:border-color .3s;}
        input:focus{border-color:#D4AF37;background:#fff;}
        input.error{border-color:#c0392b;}
        .field-error{color:#c0392b;font-size:12px;margin-top:5px;display:none;}
        .field-error.show{display:block;}
        .alert{padding:12px 16px;border-radius:3px;margin-bottom:20px;font-size:14px;display:none;}
        .alert.show{display:block;}
        .alert-error{background:#fdf0ef;border:1px solid #e74c3c;color:#c0392b;}
        .alert-success{background:#eafaf1;border:1px solid #27ae60;color:#1e8449;}
        .btn-gold{width:100%;background:#D4AF37;color:#1F3D36;border:none;padding:14px;font-family:'Times New Roman',serif;font-size:15px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;cursor:pointer;border-radius:3px;transition:background .3s;margin-top:6px;}
        .btn-gold:hover{background:#b8962e;}
        .btn-gold:disabled{opacity:.6;cursor:not-allowed;}
        .auth-link{text-align:center;margin-top:22px;font-size:14px;color:#666;}
        .auth-link a{color:#D4AF37;text-decoration:none;font-weight:bold;}
        .auth-link a:hover{text-decoration:underline;}
        .password-wrap{position:relative;}
        .password-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#888;cursor:pointer;font-size:15px;}
        .password-strength{margin-top:6px;height:4px;border-radius:2px;background:#eee;overflow:hidden;}
        .strength-bar{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
        @media(max-width:600px){.form-row{grid-template-columns:1fr;}.auth-card{padding:32px 22px;}}
    </style>
</head>
<body>
<header>
    <div class="container header-flex">
        <a href="<?= $appUrl ?>/index.html" class="logo">✈ PackNGo</a>
        <nav><ul>
            <li><a href="<?= $appUrl ?>/index.html">Home</a></li>
            <li><a href="<?= $appUrl ?>/index.html#destinations">Destinations</a></li>
            <li><a href="<?= $appUrl ?>/Reservation.html">Book Now</a></li>
            <li><a href="<?= $appUrl ?>/auth/login.php" class="nav-btn">Login</a></li>
        </ul></nav>
    </div>
</header>

<div class="page-wrap">
    <div class="auth-card">
        <div class="auth-logo">✈ PackNGo</div>
        <p class="auth-subtitle">Start your journey — create your account</p>
        <div class="divider-line"></div>

        <div class="alert alert-error"   id="alertError"></div>
        <div class="alert alert-success" id="alertSuccess"></div>

        <form id="signupForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Ahmed" autocomplete="given-name" required>
                    <span class="field-error" id="err_first_name"></span>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Khan" autocomplete="family-name" required>
                    <span class="field-error" id="err_last_name"></span>
                </div>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="email" required>
                <span class="field-error" id="err_email"></span>
            </div>
            <div class="form-group">
                <label for="phone">Phone (Optional)</label>
                <input type="tel" id="phone" name="phone" placeholder="03001234567" autocomplete="tel">
                <span class="field-error" id="err_phone"></span>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" placeholder="At least 8 characters" autocomplete="new-password" required>
                    <button type="button" class="password-toggle" onclick="togglePwd('password',this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                <div class="password-strength"><div class="strength-bar" id="strengthBar"></div></div>
                <span class="field-error" id="err_password"></span>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <div class="password-wrap">
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Repeat password" autocomplete="new-password" required>
                    <button type="button" class="password-toggle" onclick="togglePwd('password_confirm',this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                <span class="field-error" id="err_password_confirm"></span>
            </div>
            <button type="submit" class="btn-gold" id="submitBtn">
                <i class="fa-solid fa-user-plus"></i> Create Account
            </button>
        </form>
        <div class="auth-link">Already have an account? <a href="<?= $appUrl ?>/auth/login.php">Sign In</a></div>
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

document.getElementById('password').addEventListener('input', function () {
    const v = this.value, bar = document.getElementById('strengthBar');
    let s = 0;
    if (v.length >= 8) s++;
    if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++;
    if (/[^A-Za-z0-9]/.test(v)) s++;
    const colors = ['', '#e74c3c', '#e67e22', '#f1c40f', '#27ae60'];
    bar.style.width = (s * 25) + '%';
    bar.style.background = colors[s] || '';
});

function showError(field, msg) {
    const el = document.getElementById('err_' + field);
    const inp = document.getElementById(field);
    if (el) { el.textContent = msg; el.classList.add('show'); }
    if (inp) inp.classList.add('error');
}
function clearErrors() {
    document.querySelectorAll('.field-error').forEach(e => { e.textContent = ''; e.classList.remove('show'); });
    document.querySelectorAll('input').forEach(e => e.classList.remove('error'));
    ['alertError', 'alertSuccess'].forEach(id => { const el = document.getElementById(id); el.textContent = ''; el.classList.remove('show'); });
}
function showAlert(type, msg) {
    const el = document.getElementById(type === 'error' ? 'alertError' : 'alertSuccess');
    el.textContent = msg; el.classList.add('show');
}

document.getElementById('signupForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    clearErrors();

    const data = {
        _csrf:            document.querySelector('[name="_csrf"]').value,
        first_name:       document.getElementById('first_name').value.trim(),
        last_name:        document.getElementById('last_name').value.trim(),
        email:            document.getElementById('email').value.trim(),
        phone:            document.getElementById('phone').value.trim(),
        password:         document.getElementById('password').value,
        password_confirm: document.getElementById('password_confirm').value,
    };

    let hasErr = false;
    if (!data.first_name) { showError('first_name', 'First name is required.'); hasErr = true; }
    if (!data.last_name)  { showError('last_name', 'Last name is required.');   hasErr = true; }
    if (!data.email)      { showError('email', 'Email is required.');           hasErr = true; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(data.email)) { showError('email', 'Enter a valid email address.'); hasErr = true; }
    if (data.password.length < 8) { showError('password', 'Password must be at least 8 characters.'); hasErr = true; }
    if (data.password !== data.password_confirm) { showError('password_confirm', 'Passwords do not match.'); hasErr = true; }
    if (hasErr) return;

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating Account...';

    try {
        const res  = await fetch(API + '/api/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': data._csrf },
            body: JSON.stringify(data),
            credentials: 'include'
        });
        const json = await res.json();

        if (json.success) {
            window.location.href = '<?= $appUrl ?>/auth/verify-otp.php?email=' + encodeURIComponent(json.email);
        } else if (json.errors) {
            Object.entries(json.errors).forEach(([f, msgs]) => showError(f, msgs[0]));
        } else {
            showAlert('error', json.message || 'Registration failed. Please try again.');
        }
    } catch {
        showAlert('error', 'Network error. Please check your connection and try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-user-plus"></i> Create Account';
    }
});
</script>
</body>
</html>
