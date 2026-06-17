<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | PackNGo</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='10' fill='%231F3D36'/%3E%3Cg transform='translate(8,12) scale(0.75)'%3E%3Cpath fill='%23D4AF37' d='M59.257 21.915c1.597-1.598 2.473-3.722 2.473-5.984 0-4.667-3.789-8.456-8.456-8.456-2.263 0-4.387.876-5.984 2.473L36 21.238 9.515 13.481 4 19l19.373 12.056L14 41H7l-3 5 9 2 2 9 5-3v-7l9.944-9.373L42 57l5.5-5.515-7.757-26.485z'/%3E%3C/g%3E%3C/svg%3E">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Times New Roman', serif; background-color: #F5EFE6; color: #1F3D36; }
        .container { width: 85%; max-width: 1200px; margin: auto; padding: 60px 0; }
        header { background: #1F3D36; position: fixed; width: 100%; top: 0; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; width: 90%; max-width: 1400px; margin: auto; height: 70px; }
        .brand-container { display: flex; align-items: center; gap: 8px; }
        #plane-icon { color: #D4AF37; font-size: 18px; }
        #logo { color: #D4AF37; font-size: 24px; letter-spacing: 2px; margin: 0; }
        nav ul { list-style: none; display: flex; align-items: center; gap: 22px; }
        nav ul li a { color: #F5EFE6; text-decoration: none; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; white-space: nowrap; }
        nav ul li a:hover, nav ul li a.active-page { color: #D4AF37; }
        .nav-btn { background: #D4AF37; color: #1F3D36 !important; padding: 8px 18px; border-radius: 2px; font-weight: bold; }
        .hamburger { display: none; flex-direction: column; gap: 5px; background: none; border: none; cursor: pointer; padding: 4px; z-index: 1100; }
        .hamburger span { display: block; width: 26px; height: 2px; background: #D4AF37; border-radius: 2px; transition: all 0.3s; }
        .hamburger.active span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .hamburger.active span:nth-child(2) { opacity: 0; }
        .hamburger.active span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* PAGE HERO */
        .page-hero { background: #1F3D36; color: #F5EFE6; text-align: center; padding: 120px 20px 60px; position: relative; overflow: hidden; }
        .page-hero::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23D4AF37' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E"); }
        .page-hero h1 { font-size: 3.5rem; margin-bottom: 16px; position: relative; }
        .page-hero p { font-size: 1.1rem; color: #D4AF37; font-style: italic; position: relative; }
        .divider { width: 80px; height: 2px; background: #D4AF37; margin: 20px auto; }

        /* MISSION */
        .mission-section { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; }
        .mission-img { width: 100%; height: 400px; object-fit: cover; border: 4px solid #D4AF37; }
        .mission-text h2 { font-size: 2rem; margin-bottom: 20px; }
        .mission-text p { font-size: 1rem; line-height: 1.9; color: #444; margin-bottom: 16px; }
        .gold-line { width: 60px; height: 3px; background: #D4AF37; margin-bottom: 20px; }

        /* STATS */
        .stats-bg { background: #1F3D36; padding: 60px 0; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; text-align: center; }
        .stat-item { color: #F5EFE6; }
        .stat-number { font-size: 3rem; color: #D4AF37; display: block; font-weight: bold; }
        .stat-label { font-size: 0.9rem; letter-spacing: 1px; text-transform: uppercase; opacity: 0.8; margin-top: 6px; }

        /* VALUES */
        .section-heading { text-align: center; font-size: 2.5rem; margin-bottom: 50px; }
        .values-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .value-card { background: #fff; border: 1px solid #D4AF37; padding: 36px 28px; text-align: center; transition: box-shadow 0.3s, transform 0.3s; }
        .value-card:hover { box-shadow: 0 8px 30px rgba(212,175,55,0.2); transform: translateY(-4px); }
        .value-card i { font-size: 2.2rem; color: #D4AF37; margin-bottom: 18px; display: block; }
        .value-card h3 { font-size: 1.2rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; }
        .value-card p { font-size: 0.92rem; color: #555; line-height: 1.7; }

        /* TEAM */
        .team-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 40px; max-width: 760px; margin: 0 auto; }
        .team-card { text-align: center; }
        .team-card h3 { font-size: 1.1rem; margin-bottom: 4px; }
        .team-card span { font-size: 0.85rem; color: #D4AF37; text-transform: uppercase; letter-spacing: 0.5px; }
        .team-card p { font-size: 0.88rem; color: #666; margin-top: 10px; line-height: 1.6; }

        /* CTA */
        .cta-section { background: #D4AF37; padding: 70px 0; text-align: center; }
        .cta-section h2 { font-size: 2.2rem; color: #1F3D36; margin-bottom: 16px; }
        .cta-section p { color: #1F3D36; margin-bottom: 30px; font-size: 1.05rem; opacity: 0.85; }
        .dark-btn { background: #1F3D36; color: #D4AF37; padding: 14px 38px; border: none; font-weight: bold; cursor: pointer; font-family: inherit; font-size: 1rem; text-decoration: none; display: inline-block; transition: 0.3s; }
        .dark-btn:hover { background: #162d27; }

        /* FOOTER */
        footer { background: #1F3D36; color: #F5EFE6; text-align: center; padding: 40px 0; }
        .footer-links { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 20px; }
        .footer-links a { color: #D4AF37; text-decoration: none; font-size: 13px; }
        .footer-links a:hover { text-decoration: underline; }

        /* SOCIAL ICONS IN FOOTER */
        .footer-social { display: flex; justify-content: center; gap: 16px; margin-bottom: 20px; }
        .social-icon-btn {
            width: 44px; height: 44px; border-radius: 50%;
            border: 1.5px solid #D4AF37;
            display: flex; align-items: center; justify-content: center;
            color: #F5EFE6; font-size: 17px;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
            cursor: pointer;
            background: transparent;
        }
        .social-icon-btn:hover { background: #D4AF37; color: #1F3D36; }

        /* ===== UP/DOWN SCROLL BUTTONS ===== */
        .scroll-btn-group {
            position: fixed;
            right: 22px;
            bottom: 110px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 2000;
        }
        .scroll-btn {
            width: 46px; height: 46px;
            background: #D4AF37;
            border: none; border-radius: 50%;
            color: #1F3D36;
            font-size: 18px;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.18);
            transition: background 0.25s, transform 0.2s;
        }
        .scroll-btn:hover { background: #c49a2a; transform: scale(1.08); }

        /* ===== WHATSAPP CHAT BUBBLE ===== */
        #wa-bubble {
            position: fixed;
            left: 22px;
            bottom: 50px;
            z-index: 2100;
        }
        #wa-open-btn {
            width: 54px; height: 54px;
            background: #25D366;
            border: none; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 18px rgba(37,211,102,0.45);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        #wa-open-btn:hover { transform: scale(1.1); box-shadow: 0 6px 24px rgba(37,211,102,0.55); }
        #wa-open-btn svg { width: 30px; height: 30px; fill: #fff; }

        /* Chat Panel */
        #wa-panel {
            position: fixed;
            left: 22px;
            bottom: 116px;
            width: 320px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            display: none;
            flex-direction: column;
            overflow: hidden;
            z-index: 2200;
            font-family: 'Segoe UI', sans-serif;
        }
        #wa-panel.open { display: flex; }

        .wa-header {
            background: #1F3D36;
            color: #F5EFE6;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .wa-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: #D4AF37;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #1F3D36; font-weight: bold;
            flex-shrink: 0;
        }
        .wa-header-info .wa-name { font-weight: 600; font-size: 15px; }
        .wa-header-info .wa-status { font-size: 11px; color: #25D366; }
        .wa-close-btn {
            margin-left: auto; background: none; border: none;
            color: #F5EFE6; font-size: 20px; cursor: pointer; line-height: 1;
        }

        .wa-messages {
            flex: 1; overflow-y: auto; padding: 14px 14px 10px;
            background: #ECE5DD;
            min-height: 200px; max-height: 280px;
            display: flex; flex-direction: column; gap: 10px;
        }
        .wa-msg {
            max-width: 82%; padding: 9px 13px;
            border-radius: 12px; font-size: 13.5px; line-height: 1.5;
            position: relative;
        }
        .wa-msg.bot {
            background: #fff; color: #111;
            border-radius: 0 12px 12px 12px;
            align-self: flex-start;
        }
        .wa-msg.user {
            background: #DCF8C6; color: #111;
            border-radius: 12px 12px 0 12px;
            align-self: flex-end;
        }
        .wa-msg .wa-time {
            font-size: 10px; color: #aaa; display: block;
            text-align: right; margin-top: 4px;
        }

        /* Typing indicator */
        .wa-typing {
            background: #fff;
            border-radius: 0 12px 12px 12px;
            align-self: flex-start;
            padding: 10px 16px;
            display: flex; gap: 4px; align-items: center;
        }
        .wa-typing span {
            width: 7px; height: 7px; background: #aaa;
            border-radius: 50%; display: inline-block;
            animation: waTyping 1.2s infinite;
        }
        .wa-typing span:nth-child(2) { animation-delay: 0.2s; }
        .wa-typing span:nth-child(3) { animation-delay: 0.4s; }
        @keyframes waTyping { 0%,60%,100%{transform:translateY(0);opacity:0.5} 30%{transform:translateY(-5px);opacity:1} }

        .wa-quick-btns {
            display: flex; flex-wrap: wrap; gap: 6px;
            padding: 8px 14px; background: #ECE5DD;
        }
        .wa-quick-btn {
            background: #fff; border: 1px solid #D4AF37;
            color: #1F3D36; border-radius: 20px;
            padding: 5px 12px; font-size: 12px;
            cursor: pointer; transition: background 0.2s;
        }
        .wa-quick-btn:hover { background: #D4AF37; color: #fff; }

        .wa-input-row {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 12px; background: #F0F0F0;
            border-top: 1px solid #ddd;
        }
        .wa-input-row input {
            flex: 1; border: none; background: #fff;
            border-radius: 20px; padding: 8px 14px;
            font-size: 13px; outline: none;
        }
        .wa-send-btn {
            background: #25D366; border: none;
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; flex-shrink: 0;
        }
        .wa-send-btn svg { width: 18px; height: 18px; fill: #fff; }

        @media (max-width: 768px) {
            .hamburger { display: flex; }
            nav#mainNav { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #1F3D36; z-index: 1050; flex-direction: column; align-items: center; justify-content: center; }
            nav#mainNav.open { display: flex; }
            nav ul { flex-direction: column; align-items: center; gap: 22px; }
            nav ul li a { font-size: 18px; }
            .mission-section { grid-template-columns: 1fr; }
            .mission-img { height: 280px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .values-grid { grid-template-columns: 1fr 1fr; }
            .team-grid { grid-template-columns: 1fr 1fr; max-width: 100%; }
            .page-hero h1 { font-size: 2.4rem; }
            #wa-panel { width: 290px; left: 10px; }
            .scroll-btn-group { right: 12px; }
            #wa-bubble { left: 12px; }
        }
        @media (max-width: 480px) {
            .container { width: 92%; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .values-grid { grid-template-columns: 1fr; }
            .team-grid { grid-template-columns: 1fr; }
            .section-heading { font-size: 1.8rem; }
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
                <li><a href="index.html#accessibility" onclick="closeNav()">Accessibility</a></li>
                <li><a href="about.html" class="active-page" onclick="closeNav()">About Us</a></li>
                <li><a href="index.html#contact" onclick="closeNav()">Contact</a></li>
                <li><a href="blogs.html" onclick="closeNav()">Blogs</a></li>
                <li><a href="reservation.html" class="nav-btn" onclick="closeNav()">Book Now</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <!-- PAGE HERO -->
    <section class="page-hero">
        <h1>About Us</h1>
        <div class="divider"></div>
        <p>The story behind the journeys we craft</p>
    </section>

    <!-- MISSION -->
    <div class="container">
        <div class="mission-section">
            <div>
                <img src="images/Ireland.jpg" alt="Our Mission" class="mission-img">
            </div>
            <div class="mission-text">
                <div class="gold-line"></div>
                <h2>Our Mission</h2>
                <p>At PackNGo, we believe that travel is more than just moving from one place to another — it is the art of connecting with the soul of the world. Since our founding, we have dedicated ourselves to creating bespoke journeys that go far beyond ordinary tourism.</p>
                <p>Every itinerary we design is handcrafted by passionate travel experts who know their destinations intimately. We carefully select luxury accommodations, immersive cultural experiences, and hidden gems that most travelers never discover.</p>
                <p>Our mission is simple: to transform your dream destination into an unforgettable reality, with every detail attended to and every moment crafted with intention.</p>
            </div>
        </div>
    </div>

    <!-- STATS -->
    <section class="stats-bg">
        <div class="container" style="padding:0;">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">12+</span>
                    <span class="stat-label">Years of Excellence</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">8,500+</span>
                    <span class="stat-label">Happy Travellers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">60+</span>
                    <span class="stat-label">Destinations</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">98%</span>
                    <span class="stat-label">Satisfaction Rate</span>
                </div>
            </div>
        </div>
    </section>

    <!-- VALUES -->
    <div class="container">
        <h2 class="section-heading">Our Core Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <i class="fa-solid fa-gem"></i>
                <h3>Luxury Without Compromise</h3>
                <p>We partner only with the finest hotels, airlines, and experience providers to ensure every moment of your journey exceeds expectations.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-heart"></i>
                <h3>Personalised Care</h3>
                <p>No two travellers are alike. Every itinerary is tailored to your unique desires, travel style, and budget — crafted just for you.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-leaf"></i>
                <h3>Responsible Travel</h3>
                <p>We are committed to sustainable tourism practices that protect the destinations we love and benefit the communities we visit.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-shield-halved"></i>
                <h3>Safety First</h3>
                <p>Your safety is our highest priority. We provide comprehensive travel insurance, 24/7 support, and pre-screened, verified suppliers.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-handshake"></i>
                <h3>Transparency</h3>
                <p>No hidden fees, no fine print surprises. We believe in honest pricing and clear communication at every step of your journey.</p>
            </div>
            <div class="value-card">
                <i class="fa-solid fa-star"></i>
                <h3>Expert Knowledge</h3>
                <p>Our team has personally visited every destination we offer, giving you authentic, first-hand recommendations you can trust.</p>
            </div>
        </div>
    </div>

    <!-- TEAM -->
    <div class="container" style="padding-top: 0;">
        <h2 class="section-heading">Meet Our Team</h2>
        <div class="team-grid">
            <div class="team-card">
                <h3>Hania Kanwal</h3>
                <p>With a passion for crafting unforgettable experiences, Hania founded PackNGo with a vision to redefine what luxury travel truly means for every explorer.</p>
            </div>
            <div class="team-card">
                <h3>Ifra Malik</h3>
                <p>A dedicated globe-trotter and destination specialist, Ifra personally curates every journey and experience in our portfolio to ensure pure perfection.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <section class="cta-section">
        <h2>Ready to Begin Your Story?</h2>
        <p>Let our experts craft the perfect journey just for you.</p>
        <a href="reservation.html" class="dark-btn">Make a Reservation</a>
    </section>
</main>

<footer>
    <!-- Footer Nav Links -->
    <div class="footer-links">
        <a href="index.html">Home</a>
        <a href="about.html">About Us</a>
        <a href="gallery.html">Gallery</a>
        <a href="blogs.html">Blogs</a>
        <a href="reservation.html">Book Now</a>
    </div>

    <!-- Social Icons -->
    <div class="footer-social">
        <a href="https://facebook.com" target="_blank" class="social-icon-btn" aria-label="Facebook">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
        <a href="https://instagram.com" target="_blank" class="social-icon-btn" aria-label="Instagram">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <!-- WhatsApp opens the side chatbot -->
        <button class="social-icon-btn" aria-label="WhatsApp" onclick="openWaPanel()">
            <i class="fa-brands fa-whatsapp"></i>
        </button>
    </div>

    <p>&copy; 2026 PackNGo. <small>Crafted for Explorers.</small></p>
</footer>

<!-- ===== UP / DOWN SCROLL BUTTONS ===== -->
<div class="scroll-btn-group">
    <button class="scroll-btn" id="scrollUpBtn" onclick="scrollPage('up')" aria-label="Scroll Up">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
    <button class="scroll-btn" id="scrollDownBtn" onclick="scrollPage('down')" aria-label="Scroll Down">
        <i class="fa-solid fa-chevron-down"></i>
    </button>
</div>

<!-- ===== WHATSAPP FLOATING BUTTON ===== -->
<div id="wa-bubble">
    <button id="wa-open-btn" onclick="toggleWaPanel()" aria-label="Chat on WhatsApp">
        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.002 2.667C8.636 2.667 2.667 8.636 2.667 16c0 2.37.64 4.687 1.856 6.712L2.667 29.333l6.794-1.782A13.286 13.286 0 0016.002 29.333C23.368 29.333 29.333 23.364 29.333 16S23.368 2.667 16.002 2.667zm0 24.296a11.016 11.016 0 01-5.615-1.54l-.403-.24-4.032 1.058 1.078-3.93-.263-.413A11.01 11.01 0 014.963 16C4.963 9.897 9.897 4.963 16.002 4.963 22.103 4.963 27.037 9.897 27.037 16c0 6.105-4.934 11.039-11.035 11.039-1.287 0-2.534-.22-3.695-.616zm6.063-8.27c-.334-.168-1.972-.973-2.278-1.084-.307-.111-.53-.168-.752.168-.223.335-.862 1.084-1.057 1.307-.194.224-.39.252-.723.084-.334-.168-1.411-.52-2.687-1.659-.993-.886-1.664-1.979-1.857-2.313-.194-.335-.021-.516.145-.682.15-.15.334-.39.5-.585.168-.195.224-.335.335-.558.111-.224.056-.419-.028-.587-.084-.168-.752-1.813-1.03-2.48-.272-.652-.548-.563-.752-.574l-.64-.012c-.224 0-.586.084-.893.419-.307.335-1.17 1.14-1.17 2.782s1.197 3.228 1.365 3.452c.167.224 2.355 3.598 5.706 5.047.797.344 1.418.55 1.903.704.799.254 1.527.218 2.103.132.641-.095 1.972-.806 2.25-1.585.279-.78.279-1.447.196-1.585-.083-.14-.307-.224-.64-.392z"/>
        </svg>
    </button>
</div>

<!-- ===== WHATSAPP CHAT PANEL ===== -->
<div id="wa-panel">
    <div class="wa-header">
        <div class="wa-avatar">P</div>
        <div class="wa-header-info">
            <div class="wa-name">PackNGo Support</div>
            <div class="wa-status">● Online</div>
        </div>
        <button class="wa-close-btn" onclick="toggleWaPanel()" aria-label="Close">&times;</button>
    </div>

    <div class="wa-messages" id="wa-messages">
        <div class="wa-msg bot">
            👋 Hello! Welcome to <strong>PackNGo</strong>. How can we help you today?
            <span class="wa-time" id="init-time"></span>
        </div>
    </div>

    <!-- Quick reply buttons -->
    <div class="wa-quick-btns" id="wa-quick-btns">
        <button class="wa-quick-btn" onclick="quickReply('book')">📅 Book a Tour</button>
        <button class="wa-quick-btn" onclick="quickReply('destinations')">🌍 Destinations</button>
        <button class="wa-quick-btn" onclick="quickReply('packages')">💰 Packages</button>
        <button class="wa-quick-btn" onclick="quickReply('agent')">🧑‍💼 Agent</button>
    </div>

    <div class="wa-input-row">
        <input type="text" id="wa-input" placeholder="Type a message..." onkeydown="if(event.key==='Enter') sendWaMsg()" />
        <button class="wa-send-btn" onclick="sendWaMsg()" aria-label="Send">
            <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
        </button>
    </div>
</div>

<script>
    /* ----- NAV ----- */
    const hamburger = document.getElementById('hamburgerBtn');
    const nav = document.getElementById('mainNav');
    hamburger.addEventListener('click', () => { nav.classList.toggle('open'); hamburger.classList.toggle('active'); });
    function closeNav() { nav.classList.remove('open'); hamburger.classList.remove('active'); }

    /* ----- SCROLL BUTTONS ----- */
    function scrollPage(dir) {
        const amount = window.innerHeight * 0.8;
        window.scrollBy({ top: dir === 'up' ? -amount : amount, behavior: 'smooth' });
    }

    /* ----- WHATSAPP PANEL ----- */
    const waPanel = document.getElementById('wa-panel');
    const waMessages = document.getElementById('wa-messages');
    const waInput = document.getElementById('wa-input');
    const waQuickBtns = document.getElementById('wa-quick-btns');

    document.getElementById('init-time').textContent = getTime();

    function getTime() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    function toggleWaPanel() { waPanel.classList.toggle('open'); }
    function openWaPanel()   { waPanel.classList.add('open'); }

    function appendMsg(html, type) {
        const div = document.createElement('div');
        div.className = 'wa-msg ' + type;
        div.innerHTML = html + '<span class="wa-time">' + getTime() + '</span>';
        waMessages.appendChild(div);
        waMessages.scrollTop = waMessages.scrollHeight;
        return div;
    }

    function showTyping() {
        const dot = document.createElement('div');
        dot.className = 'wa-typing';
        dot.id = 'wa-typing-indicator';
        dot.innerHTML = '<span></span><span></span><span></span>';
        waMessages.appendChild(dot);
        waMessages.scrollTop = waMessages.scrollHeight;
    }

    function removeTyping() {
        const t = document.getElementById('wa-typing-indicator');
        if (t) t.remove();
    }

    /* ---- All bot responses in one place ---- */
    const BOT = {
        book:         'Great! To book a tour, please visit our <a href="reservation.html" style="color:#25D366;font-weight:600;">Reservation page</a> or call us at <strong>+92-300-0000000</strong>. Our team will get you sorted right away! ✈️',
        destinations: 'We offer <strong>60+ stunning destinations</strong> including Europe, Southeast Asia, the Middle East, and more! 🌍 Visit our <a href="index.html#destinations" style="color:#25D366;font-weight:600;">Destinations page</a> to explore them all.',
        packages:     'Our packages range from budget-friendly to ultra-luxury. 💎 Check out our <a href="index.html#packages" style="color:#25D366;font-weight:600;">Tour Packages page</a> for detailed pricing and inclusions.',
        agent:        'Connecting you with a live agent! 🧑‍💼 Reach us at <strong>packNgo@email.com</strong> or WhatsApp: <strong>+92-300-0000000</strong>. We\'re available 9 AM – 10 PM daily.',
        greeting:     'Hello! 👋 How can we help you today? You can ask about booking, destinations, packages, or speak to an agent.',
        thanks:       'You\'re most welcome! 😊 Is there anything else we can help you with?',
        fallback:     'Thanks for reaching out! 😊 Our team will respond shortly. For immediate help, call <strong>+92-300-0000000</strong> or email <strong>packNgo@email.com</strong>.'
    };

    /* Match user text to a bot response key */
    function matchIntent(text) {
        const t = text.toLowerCase();
        if (/\b(hi|hello|hey|salam|assalam|greet)\b/.test(t))                     return 'greeting';
        if (/\b(thank|thanks|shukriya|thx)\b/.test(t))                            return 'thanks';
        if (/\b(book|reserv|schedul|appoint|buy|purchas)\b/.test(t))              return 'book';
        if (/\b(destinat|countr|place|where|location|city|travel to)\b/.test(t))  return 'destinations';
        if (/\b(price|cost|fee|package|deal|offer|plan|budget|luxury|cheap)\b/.test(t)) return 'packages';
        if (/\b(agent|human|person|staff|support|talk|speak|contact|help me)\b/.test(t)) return 'agent';
        return 'fallback';
    }

    /* Quick-reply buttons pass a direct intent key */
    function quickReply(intentKey) {
        // Show a friendly label as the user's bubble
        const labels = { book: '📅 Book a Tour', destinations: '🌍 Destinations', packages: '💰 Pricing & Packages', agent: '🧑‍💼 Speak to an Agent' };
        appendMsg(labels[intentKey] || intentKey, 'user');
        waQuickBtns.style.display = 'none';

        showTyping();
        setTimeout(() => {
            removeTyping();
            appendMsg(BOT[intentKey] || BOT.fallback, 'bot');
        }, 800);
    }

    /* Free-text input */
    function sendWaMsg() {
        const val = waInput.value.trim();
        if (!val) return;
        appendMsg(val, 'user');
        waInput.value = '';
        waQuickBtns.style.display = 'none';

        const intent = matchIntent(val);
        showTyping();
        setTimeout(() => {
            removeTyping();
            appendMsg(BOT[intent], 'bot');
        }, 900);
    }
</script>
<script src="api-client.js"></script>
</body>
</html>
