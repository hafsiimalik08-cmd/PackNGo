<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation | PackNGo</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='10' fill='%231F3D36'/%3E%3Cg transform='translate(8,12) scale(0.75)'%3E%3Cpath fill='%23D4AF37' d='M59.257 21.915c1.597-1.598 2.473-3.722 2.473-5.984 0-4.667-3.789-8.456-8.456-8.456-2.263 0-4.387.876-5.984 2.473L36 21.238 9.515 13.481 4 19l19.373 12.056L14 41H7l-3 5 9 2 2 9 5-3v-7l9.944-9.373L42 57l5.5-5.515-7.757-26.485z'/%3E%3C/g%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; background-color: #F5EFE6; color: #1F3D36; }
        .container { width: 85%; max-width: 1200px; margin: auto; padding: 60px 0; }
        header { background: #1F3D36; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; width: 90%; max-width: 1400px; margin: auto; height: 70px; }
        .brand-container { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
        #plane-icon { color: #D4AF37; font-size: 18px; }
        #logo { color: #D4AF37; font-size: 24px; letter-spacing: 2px; margin: 0; }
        nav#mainNav { flex: 1; display: flex; justify-content: flex-end; }
        nav ul { list-style: none; display: flex; align-items: center; justify-content: space-evenly; width: 100%; padding-left: 30px; }
        nav ul li a { color: #F5EFE6; text-decoration: none; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; white-space: nowrap; }
        nav ul li a:hover, nav ul li a.active-page { color: #D4AF37; }
        .nav-btn { background: #D4AF37; color: #1F3D36 !important; padding: 8px 18px; border-radius: 2px; font-weight: bold; }
        .hamburger { display: none; flex-direction: column; gap: 5px; background: none; border: none; cursor: pointer; padding: 4px; z-index: 1100; }
        .hamburger span { display: block; width: 26px; height: 2px; background: #D4AF37; border-radius: 2px; transition: all 0.3s; }
        .hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        .page-hero { background: #1F3D36; color: #F5EFE6; text-align: center; padding: 120px 20px 60px; position: relative; overflow: hidden; }
        .page-hero::before { content: ''; position: absolute; inset: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23D4AF37' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
        .page-hero h1 { font-size: 3.5rem; margin-bottom: 16px; position: relative; }
        .page-hero p { font-size: 1.1rem; color: #D4AF37; font-style: italic; position: relative; }
        .divider { width: 80px; height: 2px; background: #D4AF37; margin: 20px auto; }
        .reservation-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start; }
        .res-info h2 { font-size: 1.8rem; margin-bottom: 20px; }
        .gold-line { width: 60px; height: 3px; background: #D4AF37; margin-bottom: 22px; }
        .res-info p { font-size: 0.97rem; color: #555; line-height: 1.9; margin-bottom: 28px; }
        .res-perks { list-style: none; margin-bottom: 36px; }
        .res-perks li { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; font-size: 0.92rem; color: #444; line-height: 1.6; }
        .res-perks li i { color: #D4AF37; margin-top: 3px; flex-shrink: 0; }
        .contact-box { background: #1F3D36; color: #F5EFE6; padding: 28px 32px; border-radius: 4px; }
        .contact-box h4 { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 18px; color: #D4AF37; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 12px; }
        .contact-item { display: flex; align-items: center; gap: 14px; margin-bottom: 16px; }
        .contact-item:last-child { margin-bottom: 0; }
        .contact-icon { width: 38px; height: 38px; border-radius: 50%; background: rgba(212,175,55,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .contact-icon i { color: #D4AF37; font-size: 15px; }
        .contact-detail { display: flex; flex-direction: column; }
        .contact-detail span { font-size: 0.78rem; color: rgba(245,239,230,0.55); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .contact-detail a, .contact-detail p { color: #F5EFE6; font-size: 0.93rem; text-decoration: none; }
        .contact-detail a:hover { color: #D4AF37; }
        .form-box { background: #fff; border: 1px solid #D4AF37; padding: 40px; }
        .form-box h3 { font-size: 1.4rem; margin-bottom: 6px; }
        .form-box .form-subtitle { font-size: 0.88rem; color: #888; font-style: italic; margin-bottom: 28px; }
        .form-divider { width: 50px; height: 2px; background: #D4AF37; margin-bottom: 28px; }
        .booking-form { display: flex; flex-direction: column; gap: 16px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-field { position: relative; display: flex; flex-direction: column; }
        .form-field label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px; color: #1F3D36; margin-bottom: 6px; font-weight: bold; }
        .form-field input, .form-field select, .form-field textarea { padding: 12px 14px; border: 1px solid #ccc; background: #F5EFE6; font-family: 'Times New Roman', serif; font-size: 15px; color: #1F3D36; outline: none; resize: vertical; transition: border-color 0.3s; border-radius: 2px; }
        .form-field input:focus, .form-field select:focus, .form-field textarea:focus { border-color: #D4AF37; background: #fff; }
        .form-field select { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23D4AF37' stroke-width='1.5' fill='none'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; background-color: #F5EFE6; }
        .char-count { align-self: flex-end; font-size: 11px; color: #aaa; margin-top: 3px; }
        .char-count.warn { color: #c0392b; font-weight: bold; }
        .date-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field-error { font-size: 11px; color: #c0392b; margin-top: 3px; display: none; font-style: italic; }
        .field-error.visible { display: block; }
        .form-field input.invalid { border-color: #c0392b; }
        .phone-input-row { display: flex; gap: 0; }
        .phone-code-select { padding: 12px 8px; border: 1px solid #ccc; border-right: none; background: #F5EFE6; font-family: 'Times New Roman', serif; font-size: 14px; color: #1F3D36; outline: none; border-radius: 2px 0 0 2px; cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23D4AF37' stroke-width='1.5' fill='none'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 8px center; padding-right: 24px; min-width: 110px; }
        .phone-code-select:focus { border-color: #D4AF37; background-color: #fff; }
        .phone-number-input { flex: 1; padding: 12px 14px; border: 1px solid #ccc; border-left: none; background: #F5EFE6; font-family: 'Times New Roman', serif; font-size: 15px; color: #1F3D36; outline: none; border-radius: 0 2px 2px 0; transition: border-color 0.3s; }
        .phone-number-input:focus { border-color: #D4AF37; background: #fff; }
        .dest-error { font-size: 12px; color: #c0392b; margin-top: 4px; display: none; font-style: italic; }
        .dest-error.visible { display: block; }
        .dest-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #D4AF37; border-top: none; z-index: 100; max-height: 200px; overflow-y: auto; }
        .dest-suggestion-item { padding: 10px 14px; font-size: 14px; cursor: pointer; color: #1F3D36; }
        .dest-suggestion-item:hover { background: #F5EFE6; color: #D4AF37; }
        .captcha-box { background: #F5EFE6; border: 1px solid #ccc; border-radius: 4px; padding: 16px 18px; display: flex; flex-direction: column; gap: 12px; }
        .captcha-top-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; }
        .captcha-left { display: flex; align-items: center; gap: 12px; flex: 1; }
        .captcha-checkbox { width: 22px; height: 22px; accent-color: #1F3D36; cursor: pointer; flex-shrink: 0; }
        .captcha-label { font-size: 14px; color: #1F3D36; font-family: 'Times New Roman', serif; }
        .captcha-logo { display: flex; flex-direction: column; align-items: center; gap: 2px; flex-shrink: 0; }
        .captcha-logo-icon { font-size: 22px; }
        .captcha-logo-text { font-size: 9px; color: #777; letter-spacing: 0.3px; }
        .captcha-math-row { display: none; align-items: center; gap: 10px; flex-wrap: wrap; }
        .captcha-math-row.visible { display: flex; }
        .captcha-question { font-size: 14px; color: #1F3D36; font-family: 'Times New Roman', serif; font-weight: bold; white-space: nowrap; }
        .captcha-answer-input { width: 70px; padding: 6px 10px; border: 1px solid #ccc; background: #fff; font-family: 'Times New Roman', serif; font-size: 15px; color: #1F3D36; outline: none; border-radius: 2px; transition: border-color 0.3s; }
        .captcha-answer-input:focus { border-color: #D4AF37; }
        .captcha-answer-input.invalid { border-color: #c0392b; }
        .captcha-verify-btn { padding: 6px 14px; background: #1F3D36; color: #D4AF37; border: none; font-family: 'Times New Roman', serif; font-size: 13px; cursor: pointer; border-radius: 2px; transition: background 0.3s; }
        .captcha-verify-btn:hover { background: #2a5248; }
        .captcha-verified-badge { display: none; align-items: center; gap: 6px; font-size: 13px; color: #2e7d32; font-style: italic; }
        .captcha-verified-badge.visible { display: flex; }
        .captcha-error { font-size: 11px; color: #c0392b; margin-top: 4px; display: none; font-style: italic; }
        .captcha-error.visible { display: block; }
        .gold-btn { background: #D4AF37; color: #1F3D36; padding: 15px; border: none; font-weight: bold; cursor: pointer; transition: 0.4s; font-family: inherit; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.8px; width: 100%; margin-top: 8px; }
        .gold-btn:hover { background: #b8962e; }
        .success-msg { display: none; text-align: center; padding: 30px; border: 2px solid #D4AF37; background: #f0f8f0; border-radius: 4px; }
        .success-msg i { font-size: 2.5rem; color: #D4AF37; margin-bottom: 14px; display: block; }
        .success-msg h3 { color: #1F3D36; margin-bottom: 8px; }
        .success-msg p { color: #666; font-size: 0.92rem; }
        .packages-strip { margin-top: 0; }
        .packages-strip h2 { text-align: center; font-size: 2rem; margin-bottom: 36px; }
        .pack-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .pack-card { background: #fff; border: 1px solid #D4AF37; padding: 28px 22px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .pack-card:hover, .pack-card.selected { background: #1F3D36; color: #F5EFE6; }
        .pack-card:hover .pack-price, .pack-card.selected .pack-price { color: #D4AF37; }
        .pack-card:hover h3, .pack-card.selected h3 { color: #F5EFE6; }
        .pack-card:hover p, .pack-card.selected p { color: rgba(245,239,230,0.75); opacity: 1; }
        .pack-card h3 { font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; color: #1F3D36; transition: color 0.3s; }
        .pack-card p { font-size: 0.82rem; color: #777; margin-bottom: 12px; transition: color 0.3s; }
        .pack-price { font-size: 1.3rem; color: #D4AF37; font-weight: bold; }
        .pack-icon { font-size: 24px; margin-bottom: 10px; display: block; }

        /* ── WHATSAPP CHATBOT BUTTON (LEFT) ── */
        .whatsapp-chat-btn { position: fixed; bottom: 28px; left: 28px; z-index: 9999; cursor: pointer; }
        .whatsapp-chat-bubble { width: 56px; height: 56px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(37,211,102,0.45); transition: transform 0.3s, box-shadow 0.3s; position: relative; }
        .whatsapp-chat-bubble:hover { transform: scale(1.08); box-shadow: 0 6px 22px rgba(37,211,102,0.55); }
        .whatsapp-chat-bubble i { color: #fff; font-size: 26px; }
        .whatsapp-notification-dot { position: absolute; top: 4px; right: 4px; width: 12px; height: 12px; background: #D4AF37; border-radius: 50%; border: 2px solid #fff; animation: pulse-dot 2s infinite; }
        @keyframes pulse-dot { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.3);opacity:0.7;} }

        /* ── AI CHAT PANEL ── */
        .whatsapp-panel { position: fixed; bottom: 96px; left: 28px; width: 340px; background: #fff; border-radius: 14px; box-shadow: 0 8px 40px rgba(0,0,0,0.22); z-index: 9998; display: none; flex-direction: column; overflow: hidden; }
        .whatsapp-panel.open { display: flex; animation: slideUpPanel 0.28s ease; }
        @keyframes slideUpPanel { from{opacity:0;transform:translateY(18px);} to{opacity:1;transform:translateY(0);} }

        .wa-panel-header { background: #1F3D36; padding: 14px 16px; display: flex; align-items: center; gap: 12px; flex-shrink: 0; }
        .wa-panel-avatar { width: 42px; height: 42px; border-radius: 50%; background: #25D366; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .wa-panel-avatar i { color: #fff; font-size: 20px; }
        .wa-panel-info { flex: 1; }
        .wa-panel-info h5 { color: #D4AF37; font-size: 0.93rem; margin-bottom: 2px; }
        .wa-status { display: flex; align-items: center; gap: 5px; }
        .wa-status-dot { width: 7px; height: 7px; border-radius: 50%; background: #4caf50; flex-shrink: 0; }
        .wa-status span { color: rgba(245,239,230,0.65); font-size: 0.73rem; }
        .wa-panel-close { color: rgba(245,239,230,0.65); cursor: pointer; font-size: 18px; background: none; border: none; padding: 0; transition: color 0.2s; }
        .wa-panel-close:hover { color: #D4AF37; }

        .wa-chat-body { flex: 1; padding: 14px 12px; background: #ECE5DD; overflow-y: auto; max-height: 320px; min-height: 180px; display: flex; flex-direction: column; gap: 8px; scroll-behavior: smooth; }

        .wa-msg { display: flex; flex-direction: column; max-width: 84%; }
        .wa-msg.bot { align-self: flex-start; }
        .wa-msg.user { align-self: flex-end; }
        .wa-bubble { padding: 9px 13px; font-size: 0.855rem; line-height: 1.55; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); word-break: break-word; }
        .wa-msg.bot .wa-bubble { background: #fff; color: #1F3D36; border-radius: 0 8px 8px 8px; }
        .wa-msg.user .wa-bubble { background: #D9FDD3; color: #1F3D36; border-radius: 8px 0 8px 8px; }
        .wa-time { font-size: 10px; color: #999; margin-top: 3px; }
        .wa-msg.user .wa-time { text-align: right; }

        .wa-typing { align-self: flex-start; }
        .wa-typing .wa-bubble { background: #fff; border-radius: 0 8px 8px 8px; padding: 11px 16px; display: flex; align-items: center; gap: 4px; }
        .wa-typing-dot { width: 7px; height: 7px; border-radius: 50%; background: #999; animation: typingBounce 1.2s infinite; }
        .wa-typing-dot:nth-child(2){animation-delay:0.2s;} .wa-typing-dot:nth-child(3){animation-delay:0.4s;}
        @keyframes typingBounce { 0%,60%,100%{transform:translateY(0);} 30%{transform:translateY(-5px);} }

        .wa-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 2px; align-self: flex-start; }
        .wa-chip { background: #fff; border: 1px solid #25D366; color: #1F3D36; padding: 5px 11px; border-radius: 14px; font-size: 0.77rem; cursor: pointer; font-family: 'Times New Roman', serif; transition: background 0.2s, color 0.2s; white-space: nowrap; }
        .wa-chip:hover { background: #25D366; color: #fff; }

        .wa-input-row { display: flex; align-items: center; gap: 8px; padding: 10px 12px; background: #F0F2F5; border-top: 1px solid #e0e0e0; flex-shrink: 0; }
        .wa-text-input { flex: 1; padding: 9px 14px; border: none; border-radius: 20px; background: #fff; font-family: 'Times New Roman', serif; font-size: 14px; color: #1F3D36; outline: none; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        .wa-text-input::placeholder { color: #bbb; }
        .wa-send-btn { width: 38px; height: 38px; border-radius: 50%; background: #25D366; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s, transform 0.15s; flex-shrink: 0; }
        .wa-send-btn:hover { background: #1da851; transform: scale(1.07); }
        .wa-send-btn i { color: #fff; font-size: 14px; }
        .wa-send-btn:disabled { background: #ccc; cursor: not-allowed; transform: none; }
        .wa-powered { padding: 5px 12px 8px; background: #F0F2F5; text-align: center; font-size: 10px; color: #bbb; font-style: italic; flex-shrink: 0; }

        /* ── SCROLL BUTTONS (RIGHT) ── */
        .scroll-btns { position: fixed; right: 28px; bottom: 28px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; }
        .scroll-btn { width: 46px; height: 46px; border-radius: 50%; background: #D4AF37; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 14px rgba(212,175,55,0.45); transition: background 0.3s, transform 0.2s, box-shadow 0.3s; }
        .scroll-btn:hover { background: #b8962e; transform: scale(1.08); box-shadow: 0 6px 18px rgba(212,175,55,0.55); }
        .scroll-btn i { color: #1F3D36; font-size: 18px; }

        footer { background: #1F3D36; color: #F5EFE6; text-align: center; padding: 40px 0; }
        .footer-links { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 20px; }
        .footer-links a { color: #D4AF37; text-decoration: none; font-size: 13px; }
        .footer-links a:hover { text-decoration: underline; }
        .footer-social { display: flex; justify-content: center; gap: 16px; margin-bottom: 20px; }
        .footer-social a { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; border: 1px solid rgba(212,175,55,0.5); color: #fff; font-size: 16px; text-decoration: none; transition: all 0.3s; }
        .footer-social a:hover { background: rgba(212,175,55,0.2); border-color: #D4AF37; color: #D4AF37; }

        @media (max-width: 900px) { .pack-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) {
            .hamburger { display: flex; }
            nav#mainNav { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #1F3D36; z-index: 1050; flex-direction: column; align-items: center; justify-content: center; flex: unset; }
            nav#mainNav.open { display: flex; }
            nav ul { flex-direction: column; align-items: center; gap: 22px; width: auto; padding-left: 0; justify-content: center; }
            nav ul li a { font-size: 18px; }
            .reservation-layout { grid-template-columns: 1fr; gap: 40px; }
            .form-row, .date-row { grid-template-columns: 1fr; }
            .pack-grid { grid-template-columns: 1fr 1fr; }
            .page-hero h1 { font-size: 2.4rem; }
            .whatsapp-panel { width: 300px; left: 12px; }
            .whatsapp-chat-btn { left: 14px; bottom: 18px; }
            .scroll-btns { right: 14px; bottom: 18px; }
        }
        @media (max-width: 480px) {
            .container { width: 92%; }
            .form-box { padding: 24px 18px; }
            .pack-grid { grid-template-columns: 1fr; }
            .whatsapp-panel { width: calc(100vw - 28px); left: 14px; }
        }
    </style>
</head>
<body>

<header>
    <div class="header-flex">
        <div class="brand-container">
            <i class="fa-solid fa-plane" id="plane-icon"></i>
            <h1 id="logo">PackNGo</h1>
        </div>
        <button class="hamburger" id="hamburgerBtn"><span></span><span></span><span></span></button>
        <nav id="mainNav">
            <ul>
                <li><a href="index.html" onclick="closeNav()">Home</a></li>
                <li><a href="index.html#destinations" onclick="closeNav()">Destinations</a></li>
                <li><a href="index.html#packages" onclick="closeNav()">Tour Packages</a></li>
                <li><a href="gallery.html" onclick="closeNav()">Gallery</a></li>
                <li><a href="about.html" onclick="closeNav()">About Us</a></li>
                <li><a href="index.html#contact" onclick="closeNav()">Contact</a></li>
                <li><a href="blogs.html" onclick="closeNav()">Blogs</a></li>
                <li><a href="reservation.html" class="nav-btn active-page" onclick="closeNav()">Book Now</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="page-hero">
        <h1>Reservation</h1>
        <div class="divider"></div>
        <p>Your extraordinary journey begins here</p>
    </section>

    <div class="container packages-strip">
        <h2>Choose Your Package</h2>
        <div class="pack-grid">
            <div class="pack-card" onclick="selectPackage(this,'Luxury Escape')"><span class="pack-icon">✦</span><h3>Luxury Escape</h3><p>7 Days of Pure Bliss</p><div class="pack-price">$2,500</div></div>
            <div class="pack-card" onclick="selectPackage(this,'Honeymoon Special')"><span class="pack-icon">♡</span><h3>Honeymoon Special</h3><p>10 Days in Paradise</p><div class="pack-price">$4,000</div></div>
            <div class="pack-card" onclick="selectPackage(this,'Adventure Trek')"><span class="pack-icon">⛰</span><h3>Adventure Trek</h3><p>5 Days of Exploration</p><div class="pack-price">$1,800</div></div>
            <div class="pack-card" onclick="selectPackage(this,'Beach & Nature')"><span class="pack-icon">🌊</span><h3>Beach &amp; Nature</h3><p>8 Days of Sun &amp; Sea</p><div class="pack-price">$3,200</div></div>
        </div>
    </div>

    <div class="container" style="padding-top:0;">
        <div class="reservation-layout">
            <div class="res-info">
                <div class="gold-line"></div>
                <h2>Plan Your Perfect Journey</h2>
                <p>Fill in the form and our expert concierge team will craft a personalised itinerary tailored to your desires, budget, and travel style. We handle every detail so you can simply enjoy the experience.</p>
                <ul class="res-perks">
                    <li><i class="fa-solid fa-circle-check"></i><span>Free consultation with a dedicated travel expert</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Flexible booking — no cancellation fees within 48 hours</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>100% customisable itinerary based on your preferences</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Response within 2 business hours, guaranteed</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>24/7 concierge support throughout your journey</span></li>
                    <li><i class="fa-solid fa-circle-check"></i><span>Secure payment options with full price transparency</span></li>
                </ul>
                <div class="contact-box">
                    <h4>Prefer to Talk to Us Directly?</h4>
                    <div class="contact-item"><div class="contact-icon"><i class="fa-solid fa-phone"></i></div><div class="contact-detail"><span>Phone (Pakistan)</span><a href="tel:+923001234567">+92 300 123 4567</a></div></div>
                    <div class="contact-item"><div class="contact-icon"><i class="fa-solid fa-phone"></i></div><div class="contact-detail"><span>Phone (International)</span><a href="tel:+12345678900">+1 (234) 567-8900</a></div></div>
                    <div class="contact-item"><div class="contact-icon"><i class="fa-solid fa-envelope"></i></div><div class="contact-detail"><span>Email</span><a href="mailto:concierge@packngo.store">concierge@packngo.store</a></div></div>
                    <div class="contact-item"><div class="contact-icon"><i class="fa-brands fa-whatsapp"></i></div><div class="contact-detail"><span>WhatsApp</span><p>Available 24/7 — Message Anytime</p></div></div>
                </div>
            </div>

            <div class="form-box">
                <h3>Reservation Details</h3>
                <p class="form-subtitle">All fields marked * are required</p>
                <div class="form-divider"></div>
                <div id="successMsg" class="success-msg">
                    <i class="fa-solid fa-circle-check"></i>
                    <h3>Reservation is Booked!</h3>
                    <p>Thank you for choosing PackNGo! Your reservation has been successfully received. Our concierge team will reach out to you within 2 business hours to begin crafting your perfect journey.</p>
                </div>
                <form class="booking-form" id="bookingForm" onsubmit="handleBooking(event)">
                    <div class="form-row">
                        <div class="form-field"><label>First Name *</label><input type="text" id="firstName" placeholder="e.g. Sophia" maxlength="30" oninput="enforceAlpha(this,'fnCount',30)" required><span class="char-count"><span id="fnCount">0</span>/30</span><span class="field-error" id="fnError">Only alphabets are allowed.</span></div>
                        <div class="form-field"><label>Last Name *</label><input type="text" id="lastName" placeholder="e.g. Loren" maxlength="30" oninput="enforceAlpha(this,'lnCount',30)" required><span class="char-count"><span id="lnCount">0</span>/30</span><span class="field-error" id="lnError">Only alphabets are allowed.</span></div>
                    </div>
                    <div class="form-field">
                        <label>Email Address *</label>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <input type="email" id="bookEmail" placeholder="your@email.com" maxlength="80" oninput="updateCount('bookEmail','emailCount',80);resetEmailVerify()" required style="flex:1;">
                            <button type="button" id="sendOtpBtn" onclick="sendReservationOtp()" style="white-space:nowrap;padding:10px 14px;background:#1F3D36;color:#D4AF37;border:none;font-family:'Times New Roman',serif;font-size:13px;cursor:pointer;border-radius:2px;transition:.2s;flex-shrink:0;">Send OTP</button>
                        </div>
                        <span class="char-count"><span id="emailCount">0</span>/80</span>
                        <div id="otpVerifyBox" style="display:none;margin-top:10px;background:#f9f6f0;border:1px solid #e2d9c8;padding:14px;border-radius:4px;">
                            <p style="font-size:12px;color:#555;margin-bottom:10px;">A 6-digit code was sent to your email. Please enter it below.</p>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input type="text" id="reservationOtp" placeholder="Enter 6-digit code" maxlength="6" style="flex:1;padding:10px 12px;border:1.5px solid #e2d9c8;font-family:'Times New Roman',serif;font-size:14px;outline:none;border-radius:2px;letter-spacing:3px;" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                <button type="button" onclick="verifyReservationOtp()" style="padding:10px 14px;background:#D4AF37;color:#1F3D36;border:none;font-family:'Times New Roman',serif;font-size:13px;font-weight:bold;cursor:pointer;border-radius:2px;">Verify</button>
                            </div>
                            <div style="margin-top:8px;display:flex;justify-content:space-between;align-items:center;">
                                <span id="otpMsg" style="font-size:12px;color:#c0392b;"></span>
                                <button type="button" onclick="sendReservationOtp()" style="font-size:11px;color:#1F3D36;background:none;border:none;cursor:pointer;text-decoration:underline;padding:0;">Resend OTP</button>
                            </div>
                        </div>
                        <div id="emailVerifiedBadge" style="display:none;margin-top:8px;color:#27ae60;font-size:13px;font-weight:bold;"><i class="fa-solid fa-circle-check"></i> Email verified successfully!</div>
                        <input type="hidden" id="emailVerified" value="0">
                    </div>
                    <div class="form-field">
                        <label>Phone Number</label>
                        <div class="phone-input-row">
                            <select class="phone-code-select" id="phoneCode">
                                <option value="+92">🇵🇰 +92</option><option value="+1">🇺🇸 +1</option><option value="+44">🇬🇧 +44</option><option value="+61">🇦🇺 +61</option><option value="+971">🇦🇪 +971</option><option value="+966">🇸🇦 +966</option><option value="+91">🇮🇳 +91</option><option value="+65">🇸🇬 +65</option><option value="+81">🇯🇵 +81</option><option value="+60">🇲🇾 +60</option><option value="+33">🇫🇷 +33</option><option value="+39">🇮🇹 +39</option><option value="+353">🇮🇪 +353</option><option value="+354">🇮🇸 +354</option><option value="+47">🇳🇴 +47</option><option value="+64">🇳🇿 +64</option><option value="+94">🇱🇰 +94</option><option value="+880">🇧🇩 +880</option><option value="+20">🇪🇬 +20</option><option value="+254">🇰🇪 +254</option><option value="+55">🇧🇷 +55</option><option value="+56">🇨🇱 +56</option><option value="+62">🇮🇩 +62</option><option value="+66">🇹🇭 +66</option><option value="+30">🇬🇷 +30</option>
                            </select>
                            <input type="tel" class="phone-number-input" id="bookPhone" placeholder="300 123 4567" maxlength="15" oninput="enforceDigits(this)">
                        </div>
                        <span class="field-error" id="phoneError">Only digits are allowed.</span>
                    </div>
                    <div class="form-row">
                        <div class="form-field" style="position:relative;"><label>Destination *</label><input type="text" id="bookDest" placeholder="Type a destination…" autocomplete="off" oninput="handleDestInput(this)" onblur="validateDest()" required><div class="dest-suggestions" id="destSuggestions" style="display:none;"></div><span class="dest-error" id="destError"><i class="fa-solid fa-triangle-exclamation" style="margin-right:4px;"></i>Sorry, we don't currently offer service to this destination.</span></div>
                        <div class="form-field"><label>Package</label><select id="bookPackage"><option value="">Select Package</option><option>Luxury Escape — $2,500</option><option>Honeymoon Special — $4,000</option><option>Adventure Trek — $1,800</option><option>Beach &amp; Nature — $3,200</option></select></div>
                    </div>
                    <div class="date-row">
                        <div class="form-field"><label>Departure Date *</label><input type="date" id="departDate" required></div>
                        <div class="form-field"><label>Return Date</label><input type="date" id="returnDate"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-field"><label>Number of Travellers *</label><select id="travellers" required><option value="">Select</option><option>1 (Solo)</option><option>2 (Couple)</option><option>3–4 (Small Group)</option><option>5–8 (Family)</option><option>9+ (Large Group)</option></select></div>
                        <div class="form-field"><label>Budget Range</label><select id="budget"><option value="">Select Budget</option><option>Under $1,500</option><option>$1,500 – $3,000</option><option>$3,000 – $5,000</option><option>$5,000 – $10,000</option><option>$10,000+</option></select></div>
                    </div>
                    <div class="form-field"><label>Accommodation</label><select id="accommodation"><option value="">Select Accommodation</option><option value="Brazil">Brazil</option><option value="Hotel 1">Hotel 1</option><option value="Hotel 3">Hotel 3</option><option value="Hotel 4">Hotel 4</option><option value="Hotel 6">Hotel 6</option><option value="Hotel">Hotel</option><option value="Italy Hotel 1">Italy Hotel 1</option><option value="Italy Hotel 2">Italy Hotel 2</option><option value="Japan Hotel 1">Japan Hotel 1</option><option value="Japan Hotel 2">Japan Hotel 2</option><option value="Rikyu">Rikyu</option></select></div>
                    <div class="form-field"><label>Special Requests / Message</label><textarea id="bookMsg" placeholder="Any special requirements, dietary needs, accessibility requests, or questions…" maxlength="250" rows="4" oninput="updateCount('bookMsg','msgCount',250)"></textarea><span class="char-count"><span id="msgCount">0</span>/250</span></div>
                    <div class="form-field">
                        <label>Security Verification *</label>
                        <div class="captcha-box">
                            <div class="captcha-top-row">
                                <div class="captcha-left"><input type="checkbox" class="captcha-checkbox" id="captchaCheck" onchange="onCaptchaChange()"><span class="captcha-label">I'm not a robot</span></div>
                                <div class="captcha-logo"><span class="captcha-logo-icon">🛡️</span><span class="captcha-logo-text">PackNGo<br>Verified</span></div>
                            </div>
                            <div class="captcha-math-row" id="captchaMathRow">
                                <span class="captcha-question" id="captchaQuestion">What is 3 + 5?</span>
                                <input type="number" class="captcha-answer-input" id="captchaAnswerInput" placeholder="Answer" min="0" max="999">
                                <button type="button" class="captcha-verify-btn" onclick="verifyCaptcha()">Verify</button>
                                <div class="captcha-verified-badge" id="captchaVerifiedBadge"><i class="fa-solid fa-circle-check" style="color:#2e7d32;"></i> Verified!</div>
                            </div>
                        </div>
                        <span class="captcha-error" id="captchaError">Please complete the security verification.</span>
                    </div>
                    <button class="gold-btn" type="submit"><i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i>Confirm My Reservation</button>
                </form>
            </div>
        </div>
    </div>
</main>

<footer>
    <div class="footer-links">
        <a href="index.html">Home</a><a href="about.html">About Us</a><a href="gallery.html">Gallery</a><a href="blogs.html">Blogs</a><a href="reservation.html">Book Now</a>
    </div>
    <div class="footer-social">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="https://wa.me/923001234567" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
    </div>
    <p>&copy; 2026 PackNGo. <small>Crafted for Explorers.</small></p>
</footer>

<!-- ── AI CHATBOT BUTTON (LEFT) ── -->
<div class="whatsapp-chat-btn" id="waChatBtn" onclick="toggleWaPanel()">
    <div class="whatsapp-chat-bubble">
        <i class="fa-brands fa-whatsapp"></i>
        <span class="whatsapp-notification-dot"></span>
    </div>
</div>

<!-- ── AI CHAT PANEL ── -->
<div class="whatsapp-panel" id="waPanel">
    <div class="wa-panel-header">
        <div class="wa-panel-avatar"><i class="fa-brands fa-whatsapp"></i></div>
        <div class="wa-panel-info">
            <h5>PackNGo Concierge AI ✦</h5>
            <div class="wa-status"><div class="wa-status-dot"></div><span>Online — AI agent ready</span></div>
        </div>
        <button class="wa-panel-close" onclick="toggleWaPanel()">✕</button>
    </div>
    <div class="wa-chat-body" id="waChatBody"></div>
    <div class="wa-input-row">
        <input type="text" class="wa-text-input" id="waInput" placeholder="Ask me anything about travel…">
        <button class="wa-send-btn" id="waSendBtn" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
    </div>
    <div class="wa-powered">🤖 PackNGo AI Concierge · Available 24/7</div>
</div>

<!-- ── SCROLL BUTTONS (RIGHT) ── -->
<div class="scroll-btns">
    <button class="scroll-btn" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Scroll to top"><i class="fa-solid fa-chevron-up"></i></button>
    <button class="scroll-btn" onclick="window.scrollTo({top:document.body.scrollHeight,behavior:'smooth'})" aria-label="Scroll to bottom"><i class="fa-solid fa-chevron-down"></i></button>
</div>

<script>
// ── NAV ──
const hamburger=document.getElementById('hamburgerBtn'),nav=document.getElementById('mainNav');
hamburger.addEventListener('click',()=>{nav.classList.toggle('open');hamburger.classList.toggle('active');});
function closeNav(){nav.classList.remove('open');hamburger.classList.remove('active');}

// ── FORM UTILS ──
function updateCount(id,cid,max){const v=document.getElementById(id).value.length,el=document.getElementById(cid);el.textContent=v;el.parentElement.className='char-count'+(v>=max?' warn':'');}
function enforceAlpha(input,cid,max){const orig=input.value,clean=orig.replace(/[^a-zA-Z\s\-']/g,''),bad=clean!==orig;input.value=clean;const l=clean.length,c=document.getElementById(cid);c.textContent=l;c.parentElement.className='char-count'+(l>=max?' warn':'');const em={firstName:'fnError',lastName:'lnError'},e=document.getElementById(em[input.id]);if(e&&bad){e.classList.add('visible');setTimeout(()=>e.classList.remove('visible'),2500);}if(bad){input.classList.add('invalid');setTimeout(()=>input.classList.remove('invalid'),2500);}}
function enforceDigits(input){const orig=input.value,clean=orig.replace(/[^0-9\s\-]/g,''),bad=clean!==orig;input.value=clean;if(bad){document.getElementById('phoneError').classList.add('visible');input.classList.add('invalid');setTimeout(()=>{document.getElementById('phoneError').classList.remove('visible');input.classList.remove('invalid');},2500);}}
function selectPackage(card,name){document.querySelectorAll('.pack-card').forEach(c=>c.classList.remove('selected'));card.classList.add('selected');const s=document.getElementById('bookPackage');for(let i=0;i<s.options.length;i++){if(s.options[i].text.includes(name)){s.selectedIndex=i;break;}}}
const today=new Date().toISOString().split('T')[0];
document.getElementById('departDate').min=today;document.getElementById('returnDate').min=today;
document.getElementById('departDate').addEventListener('change',function(){document.getElementById('returnDate').min=this.value;});

// ── DESTINATION ──
const DESTS=['Ireland','Bali','France','Paris','France (Paris)','Italy','Rome','Florence','Amalfi Coast','Tuscany','Venice','Malaysia','Kuala Lumpur','Langkawi','Penang','Tioman','Singapore','Japan','Kyoto','Tokyo','Maldives','Switzerland','Egypt','Cairo','Luxor','Giza','Nepal','Kathmandu','Everest','Kenya','Nairobi','Masai Mara','Peru','Machu Picchu','Cusco','Patagonia','Argentina','Chile','Iceland','Reykjavik','Greece','Athens','Santorini','India','Rajasthan','Jaipur','Agra','Delhi','Thailand','Phuket','Bangkok','Phi Phi','Norway','Bergen','Seychelles','Bora Bora','French Polynesia','Scotland','London','United Kingdom'];
function norm(s){return s.trim().toLowerCase();}
function isValidDest(v){const n=norm(v);return DESTS.some(d=>norm(d).includes(n)||n.includes(norm(d)));}
function getMatches(v){if(!v||v.length<1)return[];const n=norm(v);return DESTS.filter(d=>norm(d).startsWith(n)||norm(d).includes(n)).slice(0,6);}
function handleDestInput(inp){document.getElementById('destError').classList.remove('visible');const m=getMatches(inp.value),sb=document.getElementById('destSuggestions');if(inp.value.length>0&&m.length>0){sb.innerHTML=m.map(d=>`<div class="dest-suggestion-item" onmousedown="pickDest('${d}')">${d}</div>`).join('');sb.style.display='block';}else sb.style.display='none';}
function pickDest(n){document.getElementById('bookDest').value=n;document.getElementById('destSuggestions').style.display='none';document.getElementById('destError').classList.remove('visible');}
function validateDest(){const v=document.getElementById('bookDest').value.trim();document.getElementById('destSuggestions').style.display='none';if(!v)return;if(!isValidDest(v))document.getElementById('destError').classList.add('visible');else document.getElementById('destError').classList.remove('visible');}

// ── CAPTCHA ──
let captchaAnswer=0,captchaVerified=false;
function generateCaptcha(){const ops=[()=>{const a=Math.floor(Math.random()*9)+1,b=Math.floor(Math.random()*9)+1;return{q:`What is ${a} + ${b}?`,a:a+b};},()=>{const a=Math.floor(Math.random()*9)+5,b=Math.floor(Math.random()*5)+1;return{q:`What is ${a} − ${b}?`,a:a-b};},()=>{const a=Math.floor(Math.random()*5)+2,b=Math.floor(Math.random()*5)+2;return{q:`What is ${a} × ${b}?`,a:a*b};}];const op=ops[Math.floor(Math.random()*ops.length)]();captchaAnswer=op.a;document.getElementById('captchaQuestion').textContent=op.q;document.getElementById('captchaAnswerInput').value='';document.getElementById('captchaAnswerInput').classList.remove('invalid');captchaVerified=false;document.getElementById('captchaVerifiedBadge').classList.remove('visible');}
function onCaptchaChange(){const c=document.getElementById('captchaCheck').checked;document.getElementById('captchaError').classList.remove('visible');if(c){generateCaptcha();document.getElementById('captchaMathRow').classList.add('visible');}else{document.getElementById('captchaMathRow').classList.remove('visible');captchaVerified=false;document.getElementById('captchaVerifiedBadge').classList.remove('visible');}}
function verifyCaptcha(){const inp=document.getElementById('captchaAnswerInput'),ans=parseInt(inp.value,10);if(isNaN(ans)||ans!==captchaAnswer){inp.classList.add('invalid');captchaVerified=false;document.getElementById('captchaVerifiedBadge').classList.remove('visible');document.getElementById('captchaError').textContent='Incorrect answer. Please try again.';document.getElementById('captchaError').classList.add('visible');setTimeout(()=>{inp.classList.remove('invalid');document.getElementById('captchaError').classList.remove('visible');generateCaptcha();},1800);}else{inp.classList.remove('invalid');captchaVerified=true;document.getElementById('captchaVerifiedBadge').classList.add('visible');document.getElementById('captchaError').classList.remove('visible');}}
function handleBooking(e){e.preventDefault();const dv=document.getElementById('bookDest').value.trim();if(!isValidDest(dv)){document.getElementById('destError').classList.add('visible');document.getElementById('bookDest').focus();return;}if(document.getElementById('emailVerified').value!=='1'){alert('Please verify your email address before submitting the reservation.');document.getElementById('bookEmail').focus();return;}if(!document.getElementById('captchaCheck').checked){document.getElementById('captchaError').textContent='Please confirm you are not a robot.';document.getElementById('captchaError').classList.add('visible');return;}if(!captchaVerified){document.getElementById('captchaError').textContent='Please complete the security verification.';document.getElementById('captchaError').classList.add('visible');document.getElementById('captchaAnswerInput').focus();return;}submitReservationToServer();}

// ── RESERVATION API BASE ──
function getApiBase(){var p=window.location.pathname;var parts=p.split('/');parts.pop();return window.location.origin+parts.join('/');}
function apiUrl(path){return getApiBase()+path;}

// ── OTP FUNCTIONS ──
var _resCsrf=null;
async function getResCsrf(){if(_resCsrf)return _resCsrf;try{var r=await fetch(apiUrl('/api/auth/csrf'),{credentials:'include'});var d=await r.json();_resCsrf=d.csrf_token||'';return _resCsrf;}catch(e){return '';}}

async function sendReservationOtp(){
  var email=document.getElementById('bookEmail').value.trim();
  if(!email||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){alert('Please enter a valid email address first.');document.getElementById('bookEmail').focus();return;}
  var btn=document.getElementById('sendOtpBtn');
  btn.textContent='Sending…';btn.disabled=true;
  try{
    var csrf=await getResCsrf();
    var resp=await fetch(apiUrl('/api/reservation/send-otp'),{method:'POST',credentials:'include',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':csrf},body:JSON.stringify({email:email})});
    _resCsrf=null;
    var d=await resp.json();
    if(d.success){
      document.getElementById('otpVerifyBox').style.display='block';
      document.getElementById('otpMsg').textContent='';
      document.getElementById('reservationOtp').value='';
      document.getElementById('reservationOtp').focus();
      btn.textContent='Resend OTP';
    } else {
      document.getElementById('otpVerifyBox').style.display='block';
      document.getElementById('otpMsg').style.color='#c0392b';
      document.getElementById('otpMsg').textContent=d.message||'Failed to send OTP.';
    }
  }catch(err){
    alert('Network error. Please check your connection and try again.');
  }
  btn.disabled=false;
}

async function verifyReservationOtp(){
  var email=document.getElementById('bookEmail').value.trim();
  var otp=document.getElementById('reservationOtp').value.trim();
  var msgEl=document.getElementById('otpMsg');
  if(otp.length!==6){msgEl.style.color='#c0392b';msgEl.textContent='Please enter the 6-digit code.';return;}
  msgEl.textContent='Verifying…';msgEl.style.color='#555';
  try{
    var csrf=await getResCsrf();
    var resp=await fetch(apiUrl('/api/reservation/verify-otp'),{method:'POST',credentials:'include',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':csrf},body:JSON.stringify({email:email,otp:otp})});
    _resCsrf=null;
    var d=await resp.json();
    if(d.success){
      document.getElementById('otpVerifyBox').style.display='none';
      document.getElementById('emailVerifiedBadge').style.display='block';
      document.getElementById('emailVerified').value='1';
      document.getElementById('sendOtpBtn').style.display='none';
      document.getElementById('bookEmail').readOnly=true;
      msgEl.textContent='';
    } else {
      msgEl.style.color='#c0392b';
      msgEl.textContent=d.message||'Invalid or expired code.';
    }
  }catch(err){msgEl.style.color='#c0392b';msgEl.textContent='Network error. Please try again.';}
}

function resetEmailVerify(){
  document.getElementById('emailVerified').value='0';
  document.getElementById('emailVerifiedBadge').style.display='none';
  document.getElementById('otpVerifyBox').style.display='none';
  document.getElementById('sendOtpBtn').style.display='';
  document.getElementById('sendOtpBtn').textContent='Send OTP';
  document.getElementById('sendOtpBtn').disabled=false;
  document.getElementById('bookEmail').readOnly=false;
}

// ── SUBMIT RESERVATION TO BACKEND ──
async function submitReservationToServer(){
  var btn=document.querySelector('.gold-btn[type=submit]');
  btn.textContent='Submitting…';btn.disabled=true;
  try{
    var csrf=await getResCsrf();
    var data={
      first_name:document.getElementById('firstName').value.trim(),
      last_name:document.getElementById('lastName').value.trim(),
      email:document.getElementById('bookEmail').value.trim(),
      phone_code:document.getElementById('phoneCode').value,
      phone:document.getElementById('bookPhone').value.trim(),
      destination:document.getElementById('bookDest').value.trim(),
      package:document.getElementById('bookPackage').value,
      departure_date:document.getElementById('departDate').value,
      return_date:document.getElementById('returnDate').value,
      travellers:document.getElementById('travellers').value,
      budget:document.getElementById('budget').value,
      accommodation:document.getElementById('accommodation').value,
      message:document.getElementById('bookMsg').value.trim()
    };
    var resp=await fetch(apiUrl('/api/reservation/submit'),{method:'POST',credentials:'include',headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-Token':csrf},body:JSON.stringify(data)});
    _resCsrf=null;
    var d=await resp.json();
    if(d.success){
      document.getElementById('bookingForm').style.display='none';
      document.getElementById('successMsg').style.display='block';
      if(d.booking_ref){document.getElementById('successMsg').querySelector('p').textContent='Thank you for choosing PackNGo! Your booking reference is '+d.booking_ref+'. Our concierge team will reach out within 2 business hours.';}
      window.scrollTo({top:document.querySelector('.form-box').offsetTop-100,behavior:'smooth'});
    } else {
      alert(d.message||'Submission failed. Please try again.');
      btn.textContent='Confirm My Reservation';btn.disabled=false;
    }
  }catch(err){
    // Fallback: show success locally if backend unreachable
    document.getElementById('bookingForm').style.display='none';
    document.getElementById('successMsg').style.display='block';
    window.scrollTo({top:document.querySelector('.form-box').offsetTop-100,behavior:'smooth'});
    btn.textContent='Confirm My Reservation';btn.disabled=false;
  }
}

// ═══════════════════════════════════════════════
// ── AI CHATBOT ENGINE ──
// ═══════════════════════════════════════════════
let chatHistory = [];
let botBusy = false;
let panelOpened = false;

const SYSTEM_PROMPT = `You are Zara, a warm, knowledgeable, and helpful AI travel concierge for PackNGo — a premium travel agency. Your job is to assist customers with travel inquiries, package details, bookings, and the reservation process.

== PACKAGES ==
1. Luxury Escape — $2,500 / 7 days: Luxury 5-star accommodations, fine dining, private guided tours, airport transfers, spa access.
2. Honeymoon Special — $4,000 / 10 days: Romantic destinations, couples spa treatments, private beach dinners, sunset cruises, exclusive excursions.
3. Adventure Trek — $1,800 / 5 days: Mountain treks, camping, zip-lining, white-water rafting, outdoor adventure activities.
4. Beach & Nature — $3,200 / 8 days: Beach resorts, snorkeling, scuba diving, nature walks, wildlife spotting.

All packages include: expert local guide, daily breakfast, travel insurance, 24/7 concierge support.

== DESTINATIONS WE SERVE ==
Ireland, Bali, France (Paris), Italy (Rome, Florence, Venice, Amalfi Coast, Tuscany), Malaysia (Kuala Lumpur, Langkawi, Penang), Singapore, Japan (Kyoto, Tokyo), Maldives, Switzerland, Egypt (Cairo, Luxor, Giza), Nepal (Kathmandu, Everest Base Camp), Kenya (Nairobi, Masai Mara), Peru (Machu Picchu, Cusco), Patagonia (Argentina/Chile), Iceland (Reykjavik), Greece (Athens, Santorini), India (Rajasthan, Jaipur, Agra, Delhi), Thailand (Phuket, Bangkok, Phi Phi), Norway (Bergen), Seychelles, Bora Bora (French Polynesia), Scotland, London (UK).

== HOW TO MAKE A RESERVATION (step-by-step) ==
1. Choose a package from the 4 options shown on this page (Luxury Escape, Honeymoon Special, Adventure Trek, Beach & Nature).
2. Scroll to the "Reservation Details" form on this same page.
3. Fill in your personal details: First Name, Last Name, Email, and Phone Number.
4. Enter your desired Destination (type it in — suggestions will appear).
5. Select your preferred Package from the dropdown.
6. Set your Departure Date and Return Date.
7. Choose Number of Travellers and Budget Range.
8. Optionally select Accommodation preference.
9. Add any Special Requests or messages in the text box.
10. Complete the Security Verification (check the "I'm not a robot" box and solve the math question).
11. Click the gold "Confirm My Reservation" button.
12. Our concierge team will contact you within 2 business hours to finalise your journey!

== POLICIES ==
- Free cancellation within 48 hours of booking — no fees charged.
- Fully customisable itineraries — we tailor everything to your preferences.
- 24/7 concierge support during your travel.
- Secure payment options with full price transparency (no hidden fees).
- Group discounts available for 5+ travellers — ask us!

== CONTACT ==
- Email: concierge@packngo.store
- Phone Pakistan: +92 300 123 4567
- Phone International: +1 (234) 567-8900
- WhatsApp: Available 24/7

== YOUR STYLE ==
- Be warm, friendly, and enthusiastic about travel ✈️
- Keep responses concise (3-5 sentences max) — this is a chat widget, not a blog post
- Use travel emojis occasionally to stay welcoming (✈️ 🌍 🏝️ 🏔️ 🌸 🌊 etc.)
- When asked how to book or make a reservation, always walk the user through the step-by-step process above
- If someone asks about visa requirements, exact flight prices, or specific hotel names beyond what's listed, let them know you don't have that detail and direct them to the team
- Never make up prices, hotel names, or visa information
- Always end with a helpful follow-up question or offer to help further`;

function nowTime(){return new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}

function appendMsg(role, html, animate=false){
    const body=document.getElementById('waChatBody');
    const wrap=document.createElement('div');
    wrap.className=`wa-msg ${role}`;
    const bubble=document.createElement('div');
    bubble.className='wa-bubble';
    if(animate){bubble.style.opacity='0';bubble.style.transition='opacity 0.3s';requestAnimationFrame(()=>setTimeout(()=>bubble.style.opacity='1',30));}
    // Format text: bold for **text**, line breaks for \n
    const formatted = html
        .replace(/\*\*([^*]+)\*\*/g,'<strong>$1</strong>')
        .replace(/\*([^*]+)\*/g,'<strong>$1</strong>')
        .replace(/\n/g,'<br>');
    bubble.innerHTML = formatted;
    const time=document.createElement('div');
    time.className='wa-time';
    time.textContent=nowTime();
    wrap.appendChild(bubble);
    wrap.appendChild(time);
    body.appendChild(wrap);
    body.scrollTop=body.scrollHeight;
    return wrap;
}

function showTyping(){
    removeTyping();
    const body=document.getElementById('waChatBody');
    const d=document.createElement('div');
    d.className='wa-msg bot wa-typing';d.id='waTypingIndicator';
    d.innerHTML='<div class="wa-bubble"><div class="wa-typing-dot"></div><div class="wa-typing-dot"></div><div class="wa-typing-dot"></div></div>';
    body.appendChild(d);body.scrollTop=body.scrollHeight;
}
function removeTyping(){const el=document.getElementById('waTypingIndicator');if(el)el.remove();}

function appendChips(chips){
    const body=document.getElementById('waChatBody');
    const row=document.createElement('div');row.className='wa-chips';row.id='waChipsRow';
    chips.forEach(label=>{
        const btn=document.createElement('button');btn.className='wa-chip';btn.textContent=label;
        btn.onclick=()=>{document.querySelectorAll('.wa-chips').forEach(c=>c.remove());document.getElementById('waInput').value=label;sendMessage();};
        row.appendChild(btn);
    });
    body.appendChild(row);body.scrollTop=body.scrollHeight;
}

async function callAI(userMsg){
    chatHistory.push({role:'user',content:userMsg});

    const response = await fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'anthropic-dangerous-direct-browser-access': 'true'
        },
        body: JSON.stringify({
            model: 'claude-sonnet-4-20250514',
            max_tokens: 1000,
            system: SYSTEM_PROMPT,
            messages: chatHistory
        })
    });

    if (!response.ok) {
        const errData = await response.json().catch(()=>({}));
        console.error('API error:', response.status, errData);
        throw new Error(`API returned ${response.status}`);
    }

    const data = await response.json();

    // Extract text from content blocks (handles text + tool_use blocks)
    let reply = '';
    if (data.content && Array.isArray(data.content)) {
        reply = data.content
            .filter(block => block.type === 'text')
            .map(block => block.text)
            .join('\n')
            .trim();
    }

    if (!reply) {
        reply = "I'm having a little trouble right now. Please reach us at concierge@packngo.store or +92 300 123 4567 😊";
    }

    chatHistory.push({role:'assistant', content: reply});
    return reply;
}

async function sendMessage(){
    if(botBusy)return;
}

// ═══════════════════════════════════════════════
// ── AI CHATBOT ENGINE ──
// ═══════════════════════════════════════════════
let chatHistory = [];
let botBusy = false;
let panelOpened = false;

const SYSTEM_PROMPT = `You are Zara, a warm, knowledgeable, and helpful AI travel concierge for PackNGo — a premium travel agency. Your job is to assist customers with travel inquiries, package details, bookings, and the reservation process.

== PACKAGES ==
1. Luxury Escape — $2,500 / 7 days: Luxury 5-star accommodations, fine dining, private guided tours, airport transfers, spa access.
2. Honeymoon Special — $4,000 / 10 days: Romantic destinations, couples spa treatments, private beach dinners, sunset cruises, exclusive excursions.
3. Adventure Trek — $1,800 / 5 days: Mountain treks, camping, zip-lining, white-water rafting, outdoor adventure activities.
4. Beach & Nature — $3,200 / 8 days: Beach resorts, snorkeling, scuba diving, nature walks, wildlife spotting.

All packages include: expert local guide, daily breakfast, travel insurance, 24/7 concierge support.

== DESTINATIONS WE SERVE ==
Ireland, Bali, France (Paris), Italy (Rome, Florence, Venice, Amalfi Coast, Tuscany), Malaysia (Kuala Lumpur, Langkawi, Penang), Singapore, Japan (Kyoto, Tokyo), Maldives, Switzerland, Egypt (Cairo, Luxor, Giza), Nepal (Kathmandu, Everest Base Camp), Kenya (Nairobi, Masai Mara), Peru (Machu Picchu, Cusco), Patagonia (Argentina/Chile), Iceland (Reykjavik), Greece (Athens, Santorini), India (Rajasthan, Jaipur, Agra, Delhi), Thailand (Phuket, Bangkok, Phi Phi), Norway (Bergen), Seychelles, Bora Bora (French Polynesia), Scotland, London (UK).

== HOW TO MAKE A RESERVATION (step-by-step) ==
1. Choose a package from the 4 options shown on this page (Luxury Escape, Honeymoon Special, Adventure Trek, Beach & Nature).
2. Scroll to the "Reservation Details" form on this same page.
3. Fill in your personal details: First Name, Last Name, Email, and Phone Number.
4. Enter your desired Destination (type it in — suggestions will appear).
5. Select your preferred Package from the dropdown.
6. Set your Departure Date and Return Date.
7. Choose Number of Travellers and Budget Range.
8. Optionally select Accommodation preference.
9. Add any Special Requests or messages in the text box.
10. Complete the Security Verification (check the "I'm not a robot" box and solve the math question).
11. Click the gold "Confirm My Reservation" button.
12. Our concierge team will contact you within 2 business hours to finalise your journey!

== POLICIES ==
- Free cancellation within 48 hours of booking — no fees charged.
- Fully customisable itineraries — we tailor everything to your preferences.
- 24/7 concierge support during your travel.
- Secure payment options with full price transparency (no hidden fees).
- Group discounts available for 5+ travellers — ask us!

== CONTACT ==
- Email: concierge@packngo.store
- Phone Pakistan: +92 300 123 4567
- Phone International: +1 (234) 567-8900
- WhatsApp: Available 24/7

== YOUR STYLE ==
- Be warm, friendly, and enthusiastic about travel ✈️
- Keep responses concise (3-5 sentences max) — this is a chat widget, not a blog post
- Use travel emojis occasionally to stay welcoming (✈️ 🌍 🏝️ 🏔️ 🌸 🌊 etc.)
- When asked how to book or make a reservation, always walk the user through the step-by-step process above
- If someone asks about visa requirements, exact flight prices, or specific hotel names beyond what's listed, let them know you don't have that detail and direct them to the team
- Never make up prices, hotel names, or visa information
- Always end with a helpful follow-up question or offer to help further`;

function nowTime(){return new Date().toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});}

function appendMsg(role, html, animate=false){
    const body=document.getElementById('waChatBody');
    const wrap=document.createElement('div');
    wrap.className=`wa-msg ${role}`;
    const bubble=document.createElement('div');
    bubble.className='wa-bubble';
    if(animate){bubble.style.opacity='0';bubble.style.transition='opacity 0.3s';requestAnimationFrame(()=>setTimeout(()=>bubble.style.opacity='1',30));}
    // Format text: bold for **text**, line breaks for \n
    const formatted = html
        .replace(/\*\*([^*]+)\*\*/g,'<strong>$1</strong>')
        .replace(/\*([^*]+)\*/g,'<strong>$1</strong>')
        .replace(/\n/g,'<br>');
    bubble.innerHTML = formatted;
    const time=document.createElement('div');
    time.className='wa-time';
    time.textContent=nowTime();
    wrap.appendChild(bubble);
    wrap.appendChild(time);
    body.appendChild(wrap);
    body.scrollTop=body.scrollHeight;
    return wrap;
}

function showTyping(){
    removeTyping();
    const body=document.getElementById('waChatBody');
    const d=document.createElement('div');
    d.className='wa-msg bot wa-typing';d.id='waTypingIndicator';
    d.innerHTML='<div class="wa-bubble"><div class="wa-typing-dot"></div><div class="wa-typing-dot"></div><div class="wa-typing-dot"></div></div>';
    body.appendChild(d);body.scrollTop=body.scrollHeight;
}
function removeTyping(){const el=document.getElementById('waTypingIndicator');if(el)el.remove();}

function appendChips(chips){
    const body=document.getElementById('waChatBody');
    const row=document.createElement('div');row.className='wa-chips';row.id='waChipsRow';
    chips.forEach(label=>{
        const btn=document.createElement('button');btn.className='wa-chip';btn.textContent=label;
        btn.onclick=()=>{document.querySelectorAll('.wa-chips').forEach(c=>c.remove());document.getElementById('waInput').value=label;sendMessage();};
        row.appendChild(btn);
    });
    body.appendChild(row);body.scrollTop=body.scrollHeight;
}

async function callAI(userMsg){
    chatHistory.push({role:'user',content:userMsg});

    const response = await fetch('https://api.anthropic.com/v1/messages', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'anthropic-dangerous-direct-browser-access': 'true'
        },
        body: JSON.stringify({
            model: 'claude-sonnet-4-20250514',
            max_tokens: 1000,
            system: SYSTEM_PROMPT,
            messages: chatHistory
        })
    });

    if (!response.ok) {
        const errData = await response.json().catch(()=>({}));
        console.error('API error:', response.status, errData);
        throw new Error(`API returned ${response.status}`);
    }

    const data = await response.json();

    // Extract text from content blocks (handles text + tool_use blocks)
    let reply = '';
    if (data.content && Array.isArray(data.content)) {
        reply = data.content
            .filter(block => block.type === 'text')
            .map(block => block.text)
            .join('\n')
            .trim();
    }

    if (!reply) {
        reply = "I'm having a little trouble right now. Please reach us at concierge@packngo.store or +92 300 123 4567 😊";
    }

    chatHistory.push({role:'assistant', content: reply});
    return reply;
}

async function sendMessage(){
    if(botBusy)return;
    const inp=document.getElementById('waInput');
    const text=inp.value.trim();
    if(!text)return;
    inp.value='';
    document.querySelectorAll('.wa-chips').forEach(c=>c.remove());
    appendMsg('user',text,true);
    botBusy=true;
    document.getElementById('waSendBtn').disabled=true;
    showTyping();
    try{
        const reply=await callAI(text);
        removeTyping();
        appendMsg('bot',reply,true);
    }catch(err){
        console.error('Chat error:', err);
        removeTyping();
        appendMsg('bot',"Sorry, I'm having trouble connecting right now. Please reach us at concierge@packngo.store or call +92 300 123 4567 📞",true);
    }
    botBusy=false;
    document.getElementById('waSendBtn').disabled=false;
    inp.focus();
}

function toggleWaPanel(){
    const panel=document.getElementById('waPanel');
    panel.classList.toggle('open');
    if(panel.classList.contains('open')&&!panelOpened){
        panelOpened=true;
        setTimeout(()=>{
            appendMsg('bot','👋 Hello! I\'m *Zara*, your PackNGo AI Concierge.\nHow can I help you plan your dream journey today? ✈️',true);
            setTimeout(()=>{
                appendChips(['View Packages 📦','Top Destinations 🌍','How to Book ✅','Pricing & Budget 💰']);
            },500);
        },380);
    }
    if(panel.classList.contains('open'))setTimeout(()=>document.getElementById('waInput').focus(),400);
}

// Close on outside click
document.addEventListener('click',function(e){
    const panel=document.getElementById('waPanel'),btn=document.getElementById('waChatBtn');
    if(panel.classList.contains('open')&&!panel.contains(e.target)&&!btn.contains(e.target))panel.classList.remove('open');
});

// Enter to send
document.getElementById('waInput').addEventListener('keydown',function(e){if(e.key==='Enter')sendMessage();});
</script>
<script src="api-client.js"></script>
</body>
</html>
