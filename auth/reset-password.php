<?php
require_once __DIR__ . '/_header.php';
if (Session::isLoggedIn()) { header('Location: ' . $appUrl . '/auth/profile.php'); exit; }
$token = htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES);
if (!$token) { header('Location: ' . $appUrl . '/auth/forgot-password.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — PackNGo</title>
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
        .auth-card{background:#fff;border:1px solid #D4AF37;border-radius:6px;padding:48px 44px;width:100%;max-width:440px;box-shadow:0 10px 40px rgba(31,61,54,0.12);}
        .auth-logo{text-align:center;color:#D4AF37;font-size:2rem;letter-spacing:3px;font-weight:bold;margin-bottom:6px;}
        .icon-wrap{text-align:center;font-size:3rem;color:#D4AF37;margin:10px 0 16px;}
        h2{text-align:center;font-size:1.4rem;color:#1F3D36;margin-bottom:8px;}
        .sub{text-align:center;color:#888;font-size:0.88rem;margin-bottom:28px;}
        .divider-line{width:60px;height:2px;background:#D4AF37;margin:0 auto 28px;}
        .form-group{margin-bottom:20px;}
        label{display:block;font-size:12px;text-transform:uppercase;letter-spacing:0.8px;color:#1F3D36;margin-bottom:7px;font-weight:bold;}
        input{width:100%;padding:12px 16px;border:1.5px solid #ddd;background:#fafaf8;font-family:'Times New Roman',serif;font-size:15px;color:#1F3D36;border-radius:3px;outline:none;transition:border-color .3s;}
        input:focus{border-color:#D4AF37;background:#fff;}
        .field-error{color:#c0392b;font-size:12px;margin-top:5px;display:none;}
        .field-error.show{display:block;}
        .alert{padding:12px 16px;border-radius:3px;margin-bottom:20px;font-size:14px;display:none;}
        .alert.show{display:block;}
        .alert-error{background:#fdf0ef;border:1px solid #e74c3c;color:#c0392b;}
        .alert-success{background:#eafaf1;border:1px solid #27ae60;color:#1e8449;}
        .btn-gold{width:100%;background:#D4AF37;color:#1F3D36;border:none;padding:14px;font-family:'Times New Roman',serif;font-size:15px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;cursor:pointer;border-radius:3px;transition:background .3s;}
        .btn-gold:hover{background:#b8962e;}
        .btn-gold:disabled{opacity:.6;cursor:not-allowed;}
        .password-wrap{position:relative;}
        .password-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#888;cursor:pointer;font-size:15px;}
        .auth-link{text-align:center;margin-top:22px;font-size:14px;color:#666;}
        .auth-link a{color:#D4AF37;text-decoration:none;font-weight:bold;}
        .password-strength{margin-top:6px;height:4px;border-radius:2px;background:#eee;overflow:hidden;}
        .strength-bar{height:100%;width:0;border-radius:2px;transition:width .3s,background .3s;}
    </style>
</head>
<body>
<header>
    <div class="container header-flex">
        <a href="<?= $appUrl ?>/index.html" class="logo">✈ PackNGo</a>
        <nav><ul>
            <li><a href="<?= $appUrl ?>/auth/login.php">Login</a></li>
            <li><a href="<?= $appUrl ?>/auth/signup.php" class="nav-btn">Register</a></li>
        </ul></nav>
    </div>
</header>

<div class="page-wrap">
    <div class="auth-card">
        <div class="auth-logo">✈ PackNGo</div>
        <div class="icon-wrap"><i class="fa-solid fa-key"></i></div>
        <h2>Set New Password</h2>
        <p class="sub">Choose a strong new password for your account.</p>
        <div class="divider-line"></div>

        <div class="alert alert-error"   id="alertError"></div>
        <div class="alert alert-success" id="alertSuccess"></div>

        <form id="resetForm" novalidate>
            <input type="hidden" name="_csrf"  value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <input type="hidden" id="resetToken" value="<?= $token ?>">

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" placeholder="At least 8 characters" autocomplete="new-password" required>
                    <button type="button" class="password-toggle" onclick="togglePwd('password',this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                <div class="password-strength"><div class="strength-bar" id="strengthBar"></div></div>
                <span class="field-error" id="err_password"></span>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <div class="password-wrap">
                    <input type="password" id="password_confirm" placeholder="Repeat password" autocomplete="new-password" required>
                    <button type="button" class="password-toggle" onclick="togglePwd('password_confirm',this)"><i class="fa-solid fa-eye"></i></button>
                </div>
                <span class="field-error" id="err_confirm"></span>
            </div>
            <button type="submit" class="btn-gold" id="submitBtn">
                <i class="fa-solid fa-shield-halved"></i> Reset Password
            </button>
        </form>
        <div class="auth-link"><a href="<?= $appUrl ?>/auth/login.php">← Back to Sign In</a></div>
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
    if (v.length >= 8) s++; if (/[A-Z]/.test(v)) s++;
    if (/[0-9]/.test(v)) s++; if (/[^A-Za-z0-9]/.test(v)) s++;
    const colors = ['', '#e74c3c', '#e67e22', '#f1c40f', '#27ae60'];
    bar.style.width = (s * 25) + '%'; bar.style.background = colors[s] || '';
});

function showAlert(type, msg) {
    ['alertError', 'alertSuccess'].forEach(i => { const e = document.getElementById(i); e.textContent = ''; e.classList.remove('show'); });
    document.getElementById(type === 'error' ? 'alertError' : 'alertSuccess').textContent = msg;
    document.getElementById(type === 'error' ? 'alertError' : 'alertSuccess').classList.add('show');
}

document.getElementById('resetForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const password = document.getElementById('password').value;
    const confirm  = document.getElementById('password_confirm').value;
    const token    = document.getElementById('resetToken').value;
    const csrf     = document.querySelector('[name="_csrf"]').value;

    document.querySelectorAll('.field-error').forEach(el => { el.textContent = ''; el.classList.remove('show'); });

    let hasErr = false;
    if (password.length < 8) { const e = document.getElementById('err_password'); e.textContent = 'At least 8 characters required.'; e.classList.add('show'); hasErr = true; }
    if (password !== confirm) { const e = document.getElementById('err_confirm'); e.textContent = 'Passwords do not match.'; e.classList.add('show'); hasErr = true; }
    if (hasErr) return;

    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Resetting...';

    try {
        const res  = await fetch(API + '/api/auth/reset-password', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ token, password, _csrf: csrf }), credentials: 'include'
        });
        const json = await res.json();
        if (json.success) {
            showAlert('success', '✓ Password reset! Redirecting to login...');
            setTimeout(() => window.location.href = '<?= $appUrl ?>/auth/login.php', 2000);
        } else {
            showAlert('error', json.message || 'Reset failed. The link may have expired.');
        }
    } catch { showAlert('error', 'Network error. Please try again.'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Reset Password'; }
});
</script>
</body>
</html>
