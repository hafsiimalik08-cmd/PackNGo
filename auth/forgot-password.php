<?php
require_once __DIR__ . '/_header.php';
if (Session::isLoggedIn()) { header('Location: ' . $appUrl . '/auth/profile.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — PackNGo</title>
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
        .sub{text-align:center;color:#888;font-size:0.88rem;margin-bottom:28px;line-height:1.6;}
        .divider-line{width:60px;height:2px;background:#D4AF37;margin:0 auto 28px;}
        .form-group{margin-bottom:20px;}
        label{display:block;font-size:12px;text-transform:uppercase;letter-spacing:0.8px;color:#1F3D36;margin-bottom:7px;font-weight:bold;}
        input{width:100%;padding:12px 16px;border:1.5px solid #ddd;background:#fafaf8;font-family:'Times New Roman',serif;font-size:15px;color:#1F3D36;border-radius:3px;outline:none;transition:border-color .3s;}
        input:focus{border-color:#D4AF37;background:#fff;}
        .alert{padding:12px 16px;border-radius:3px;margin-bottom:20px;font-size:14px;display:none;}
        .alert.show{display:block;}
        .alert-error{background:#fdf0ef;border:1px solid #e74c3c;color:#c0392b;}
        .alert-success{background:#eafaf1;border:1px solid #27ae60;color:#1e8449;}
        .btn-gold{width:100%;background:#D4AF37;color:#1F3D36;border:none;padding:14px;font-family:'Times New Roman',serif;font-size:15px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;cursor:pointer;border-radius:3px;transition:background .3s;}
        .btn-gold:hover{background:#b8962e;}
        .btn-gold:disabled{opacity:.6;cursor:not-allowed;}
        .auth-link{text-align:center;margin-top:22px;font-size:14px;color:#666;}
        .auth-link a{color:#D4AF37;text-decoration:none;font-weight:bold;}
        .auth-link a:hover{text-decoration:underline;}
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
        <div class="icon-wrap"><i class="fa-solid fa-lock-open"></i></div>
        <h2>Forgot Your Password?</h2>
        <p class="sub">Enter your registered email and we'll send a reset link to your inbox.</p>
        <div class="divider-line"></div>

        <div class="alert alert-error"   id="alertError"></div>
        <div class="alert alert-success" id="alertSuccess"></div>

        <form id="forgotForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required autocomplete="email">
            </div>
            <button type="submit" class="btn-gold" id="submitBtn">
                <i class="fa-solid fa-paper-plane"></i> Send Reset Link
            </button>
        </form>
        <div class="auth-link"><a href="<?= $appUrl ?>/auth/login.php">← Back to Sign In</a></div>
    </div>
</div>

<script>
const API = '<?= $appUrl ?>/index.php';
function showAlert(type, msg) {
    ['alertError', 'alertSuccess'].forEach(i => { const e = document.getElementById(i); e.textContent = ''; e.classList.remove('show'); });
    const el = document.getElementById(type === 'error' ? 'alertError' : 'alertSuccess');
    el.textContent = msg; el.classList.add('show');
}
document.getElementById('forgotForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const email = document.getElementById('email').value.trim();
    const csrf  = document.querySelector('[name="_csrf"]').value;
    if (!email) { showAlert('error', 'Please enter your email address.'); return; }
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending...';
    try {
        const res  = await fetch(API + '/api/auth/forgot-password', {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ email, _csrf: csrf }), credentials: 'include'
        });
        const json = await res.json();
        showAlert(json.success ? 'success' : 'error', json.message);
    } catch { showAlert('error', 'Network error. Please try again.'); }
    finally { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Reset Link'; }
});
</script>
</body>
</html>
