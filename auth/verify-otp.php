<?php
require_once __DIR__ . '/_header.php';
if (Session::isLoggedIn()) { header('Location: ' . $appUrl . '/auth/profile.php'); exit; }
$email = htmlspecialchars(urldecode($_GET['email'] ?? ''), ENT_QUOTES);
if (!$email) { header('Location: ' . $appUrl . '/auth/signup.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — PackNGo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        body{background:#F5EFE6;font-family:'Times New Roman',Georgia,serif;color:#1F3D36;min-height:100vh;}
        header{background:#1F3D36;position:fixed;width:100%;top:0;z-index:1000;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        .header-flex{display:flex;justify-content:space-between;align-items:center;width:90%;max-width:1400px;margin:auto;height:70px;}
        .logo{color:#D4AF37;font-size:1.6rem;letter-spacing:3px;font-weight:bold;text-decoration:none;}
        nav ul{list-style:none;display:flex;align-items:center;gap:22px;}
        nav ul li a{color:#F5EFE6;text-decoration:none;font-size:12px;text-transform:uppercase;letter-spacing:1px;}
        nav ul li a:hover{color:#D4AF37;}
        .nav-btn{background:#D4AF37;color:#1F3D36!important;padding:8px 18px;border-radius:2px;font-weight:bold;}
        .page-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:100px 20px 50px;}
        .auth-card{background:#fff;border:1px solid #D4AF37;border-radius:6px;padding:48px 44px;width:100%;max-width:460px;box-shadow:0 10px 40px rgba(31,61,54,0.12);text-align:center;}
        .auth-logo{color:#D4AF37;font-size:2rem;letter-spacing:3px;font-weight:bold;margin-bottom:6px;}
        .otp-icon{font-size:3rem;color:#D4AF37;margin:10px 0 18px;}
        h2{font-size:1.5rem;color:#1F3D36;margin-bottom:8px;}
        .sub{color:#888;font-size:0.9rem;margin-bottom:6px;}
        .email-badge{display:inline-block;background:#F5EFE6;border:1px solid #D4AF37;color:#1F3D36;font-size:13px;padding:4px 14px;border-radius:20px;margin-bottom:28px;font-weight:bold;word-break:break-all;}
        .divider-line{width:60px;height:2px;background:#D4AF37;margin:0 auto 28px;}
        .alert{padding:12px 16px;border-radius:3px;margin-bottom:20px;font-size:14px;display:none;}
        .alert.show{display:block;}
        .alert-error{background:#fdf0ef;border:1px solid #e74c3c;color:#c0392b;}
        .alert-success{background:#eafaf1;border:1px solid #27ae60;color:#1e8449;}
        .otp-inputs{display:flex;gap:12px;justify-content:center;margin:20px 0 28px;}
        .otp-inputs input{width:48px;height:58px;text-align:center;font-size:1.6rem;font-weight:bold;border:2px solid #ddd;border-radius:4px;font-family:'Times New Roman',serif;color:#1F3D36;outline:none;transition:border-color .3s;}
        .otp-inputs input:focus{border-color:#D4AF37;background:#fffdf5;}
        .btn-gold{width:100%;background:#D4AF37;color:#1F3D36;border:none;padding:14px;font-family:'Times New Roman',serif;font-size:15px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;cursor:pointer;border-radius:3px;transition:background .3s;}
        .btn-gold:hover{background:#b8962e;}
        .btn-gold:disabled{opacity:.6;cursor:not-allowed;}
        .resend-row{margin-top:20px;font-size:14px;color:#666;}
        .resend-btn{background:none;border:none;color:#D4AF37;font-weight:bold;cursor:pointer;font-family:inherit;font-size:14px;padding:0;}
        .resend-btn:hover{color:#b8962e;}
        .resend-btn:disabled{color:#aaa;cursor:not-allowed;}
        .back-link{display:block;margin-top:20px;font-size:14px;color:#888;text-decoration:none;}
        .back-link:hover{color:#1F3D36;}
        @media(max-width:420px){.otp-inputs{gap:8px;} .otp-inputs input{width:42px;height:52px;font-size:1.4rem;} .auth-card{padding:32px 22px;}}
    </style>
</head>
<body>
<header>
    <div class="container header-flex">
        <a href="<?= $appUrl ?>/index.html" class="logo">✈ PackNGo</a>
        <nav><ul>
            <li><a href="<?= $appUrl ?>/index.html">Home</a></li>
            <li><a href="<?= $appUrl ?>/auth/login.php" class="nav-btn">Login</a></li>
        </ul></nav>
    </div>
</header>

<div class="page-wrap">
    <div class="auth-card">
        <div class="auth-logo">✈ PackNGo</div>
        <div class="otp-icon"><i class="fa-solid fa-envelope-open-text"></i></div>
        <h2>Verify Your Email</h2>
        <p class="sub">We sent a 6-digit code to</p>
        <span class="email-badge"><?= $email ?></span>
        <div class="divider-line"></div>

        <div class="alert alert-error"   id="alertError"></div>
        <div class="alert alert-success" id="alertSuccess"></div>

        <form id="otpForm" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES) ?>">
            <input type="hidden" id="hiddenEmail" value="<?= $email ?>">

            <div class="otp-inputs" id="otpInputs">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp0" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp1" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp2" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp3" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp4" autocomplete="off">
                <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp5" autocomplete="off">
            </div>

            <button type="submit" class="btn-gold" id="submitBtn">
                <i class="fa-solid fa-check-circle"></i> Verify &amp; Activate
            </button>
        </form>

        <div class="resend-row">
            Didn't receive the code?
            <button class="resend-btn" id="resendBtn" disabled onclick="resendOtp()">
                Resend Code <span id="timerTxt">(60s)</span>
            </button>
        </div>
        <a href="<?= $appUrl ?>/auth/signup.php" class="back-link">← Back to Sign Up</a>
    </div>
</div>

<script>
const API    = '<?= $appUrl ?>/index.php';
const inputs = Array.from(document.querySelectorAll('.otp-inputs input'));

inputs.forEach((inp, i) => {
    inp.addEventListener('input', () => {
        inp.value = inp.value.replace(/\D/g, '');
        if (inp.value && i < 5) inputs[i + 1].focus();
    });
    inp.addEventListener('keydown', e => {
        if (e.key === 'Backspace' && !inp.value && i > 0) inputs[i - 1].focus();
    });
    inp.addEventListener('paste', e => {
        e.preventDefault();
        const digits = (e.clipboardData.getData('text').replace(/\D/g, '')).split('').slice(0, 6);
        digits.forEach((d, j) => { if (inputs[j]) inputs[j].value = d; });
        const last = Math.min(5, digits.length - 1);
        if (inputs[last]) inputs[last].focus();
    });
});

function getOtp() { return inputs.map(i => i.value).join(''); }

let secs = 60;
const timerTxt  = document.getElementById('timerTxt');
const resendBtn = document.getElementById('resendBtn');
function startTimer() {
    secs = 60; timerTxt.textContent = '(60s)'; resendBtn.disabled = true;
    const cd = setInterval(() => {
        secs--; timerTxt.textContent = secs > 0 ? `(${secs}s)` : '';
        if (secs <= 0) { clearInterval(cd); resendBtn.disabled = false; timerTxt.textContent = ''; }
    }, 1000);
}
startTimer();

function showAlert(type, msg) {
    const el = document.getElementById(type === 'error' ? 'alertError' : 'alertSuccess');
    el.textContent = msg; el.classList.add('show');
    document.getElementById(type === 'error' ? 'alertSuccess' : 'alertError').classList.remove('show');
}

document.getElementById('otpForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const otp   = getOtp();
    const email = document.getElementById('hiddenEmail').value;
    const csrf  = document.querySelector('[name="_csrf"]').value;

    if (otp.length < 6 || /\D/.test(otp)) {
        showAlert('error', 'Please enter all 6 digits of your verification code.');
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

    try {
        const res  = await fetch(API + '/api/auth/verify-otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ email, otp, _csrf: csrf }),
            credentials: 'include'
        });
        const json = await res.json();
        if (json.success) {
            showAlert('success', '✓ Email verified! Redirecting to login...');
            setTimeout(() => window.location.href = '<?= $appUrl ?>/auth/login.php?verified=1', 1800);
        } else {
            showAlert('error', json.message || 'Invalid OTP. Please try again.');
            inputs.forEach(i => i.value = '');
            inputs[0].focus();
        }
    } catch {
        showAlert('error', 'Network error. Please try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Verify &amp; Activate';
    }
});

async function resendOtp() {
    const email = document.getElementById('hiddenEmail').value;
    const csrf  = document.querySelector('[name="_csrf"]').value;
    resendBtn.disabled = true;
    try {
        const res  = await fetch(API + '/api/auth/resend-otp', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
            body: JSON.stringify({ email, _csrf: csrf }),
            credentials: 'include'
        });
        const json = await res.json();
        showAlert(json.success ? 'success' : 'error', json.message);
        if (json.success) startTimer();
        else resendBtn.disabled = false;
    } catch {
        showAlert('error', 'Network error.'); resendBtn.disabled = false;
    }
}

inputs[0].focus();
</script>
</body>
</html>
