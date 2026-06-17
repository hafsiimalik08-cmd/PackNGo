<?php
require_once __DIR__ . '/_header.php';
if (Session::isLoggedIn()) { header('Location: ' . $appUrl . '/auth/profile.php'); exit; }
$verified  = ($_GET['verified']   ?? '') === '1';
$timeout   = ($_GET['timeout']    ?? '') === '1';
$loggedOut = ($_GET['logged_out'] ?? '') === '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — PackNGo</title>
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
        .auth-card{background:#fff;border:1px solid #D4AF37;border-radius:6px;padding:48px 44px;width:100%;max-width:460px;box-shadow:0 10px 40px rgba(31,61,54,0.12);}
        .auth-logo{text-align:center;color:#D4AF37;font-size:2rem;letter-spacing:3px;font-weight:bold;margin-bottom:6px;}
        .auth-subtitle{text-align:center;color:#888;font-size:0.9rem;margin-bottom:30px;}
        .divider-line{width:60px;height:2px;background:#D4AF37;margin:0 auto 28px;}
        .form-group{margin-bottom:20px;}
        label{display:block;font-size:12px;text-transform:uppercase;letter-spacing:0.8px;color:#1F3D36;margin-bottom:7px;font-weight:bold;}
        input{width:100%;padding:12px 16px;border:1.5px solid #ddd;background:#fafaf8;font-family:'Times New Roman',serif;font-size:15px;color:#1F3D36;border-radius:3px;outline:none;transition:border-color .3s;}
        input:focus{border-color:#D4AF37;background:#fff;}
        .alert{padding:12px 16px;border-radius:3px;margin-bottom:20px;font-size:14px;display:none;}
        .alert.show{display:block;}
        .alert-error{background:#fdf0ef;border:1px solid #e74c3c;color:#c0392b;}
        .alert-success{background:#eafaf1;border:1px solid #27ae60;color:#1e8449;}
        .alert-info{background:#eaf3fd;border:1px solid #3498db;color:#1a5276;}
        .alert-warning{background:#fef9e7;border:1px solid #f39c12;color:#7d6608;}
        .btn-gold{width:100%;background:#D4AF37;color:#1F3D36;border:none;padding:14px;font-family:'Times New Roman',serif;font-size:15px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;cursor:pointer;border-radius:3px;transition:background .3s;margin-top:6px;}
        .btn-gold:hover{background:#b8962e;}
        .btn-gold:disabled{opacity:.6;cursor:not-allowed;}
        .password-wrap{position:relative;}
        .password-toggle{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#888;cursor:pointer;font-size:15px;}
        .forgot-row{text-align:right;margin-top:6px;}
        .forgot-row a{color:#888;font-size:13px;text-decoration:none;}
        .forgot-row a:hover{color:#D4AF37;}
        .auth-link{text-align:center;margin-top:22px;font-size:14px;color:#666;}
        .auth-link a{color:#D4AF37;text-decoration:none;font-weight:bold;}
        .auth-link a:hover{text-decoration:underline;}
        @media(max-width:600px){.auth-card{padding:32px 22px;}}
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
            <li><a href="<?= $appUrl ?>/auth/signup.php" class="nav-btn">Register</a></li>
        </ul></nav>
    </div>
</header>

<div class="page-wrap">
    <div class="auth-card">
        <div class="auth-logo">✈ PackNGo</div>
        <p class="auth-subtitle">Welcome back — sign in to continue</p>
        <div class="divider-line"></div>

        <?php if ($verified): ?>
        <div class="alert alert-success show">✓ Email verified successfully! You can now sign in.</div>
        <?php elseif ($timeout): ?>
        <div class="alert alert-warning show">Your session expired. Please sign in again.</div>
        <?php elseif ($loggedOut): ?>
        <div class="alert alert-info show">You have been signed out. See you again soon!</div>
        <?php endif; ?>

        <div class="alert alert-error"   id="alertError"></div>
        <div class="alert alert-warning" id="alertWarning"></div>

        <form id="loginForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" autocomplete="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" placeholder="Your password" autocomplete="current-password" required>
                    <button type="button" class="password-toggle" onclick="togglePwd()"><i class="fa-solid fa-eye" id="eyeIcon"></i></button>
                </div>
                <div class="forgot-row"><a href="<?= $appUrl ?>/auth/forgot-password.php">Forgot password?</a></div>
            </div>
            <button type="submit" class="btn-gold" id="submitBtn">
                <i class="fa-solid fa-right-to-bracket"></i> Sign In
            </button>
        </form>
        <div class="auth-link">Don't have an account? <a href="<?= $appUrl ?>/auth/signup.php">Create One</a></div>
    </div>
</div>

<script>
const API = '<?= $appUrl ?>/index.php';

function togglePwd() {
    const inp = document.getElementById('password');
    const icon = document.getElementById('eyeIcon');
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    icon.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
}

function showAlert(id, msg) {
    ['alertError', 'alertWarning'].forEach(i => { const e = document.getElementById(i); e.textContent = ''; e.classList.remove('show'); });
    const el = document.getElementById(id);
    el.textContent = msg; el.classList.add('show');
}

document.getElementById('loginForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const email    = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const csrf     = document.querySelector('[name="_csrf"]').value;

    if (!email || !password) { showAlert('alertError', 'Please enter your email and password.'); return; }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Signing In...';

    try {
        const res  = await fetch(API + '/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ email, password, _csrf: csrf }),
            credentials: 'include'
        });
        const json = await res.json();

        if (json.success) {
            window.location.href = '<?= $appUrl ?>/auth/profile.php';
        } else if (json.email_unverified) {
            showAlert('alertWarning', json.message);
            setTimeout(() => {
                window.location.href = '<?= $appUrl ?>/auth/verify-otp.php?email=' + encodeURIComponent(json.email);
            }, 2000);
        } else {
            showAlert('alertError', json.message || 'Invalid credentials.');
        }
    } catch {
        showAlert('alertError', 'Network error. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i> Sign In';
    }
});
</script>
</body>
</html>
