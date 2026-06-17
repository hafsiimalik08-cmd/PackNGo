<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visual Gallery | PackNGo</title>
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
        .gallery-filters { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-bottom: 40px; }
        .filter-btn { background: #fff; border: 1px solid #D4AF37; color: #1F3D36; padding: 9px 20px; border-radius: 30px; font-size: 13px; cursor: pointer; font-family: 'Times New Roman', serif; transition: all 0.3s; }
        .filter-btn:hover, .filter-btn.active { background: #D4AF37; color: #1F3D36; font-weight: bold; box-shadow: 0 3px 10px rgba(212,175,55,0.3); }
        .gallery-masonry { columns: 3; column-gap: 14px; }
        .gallery-item { break-inside: avoid; margin-bottom: 14px; position: relative; overflow: hidden; cursor: pointer; }
        .gallery-item img { width: 100%; display: block; transition: transform 0.5s; }
        .gallery-item:hover img { transform: scale(1.05); }
        .gallery-overlay { position: absolute; inset: 0; background: rgba(31,61,54,0.7); opacity: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.4s; color: #fff; text-align: center; padding: 20px; }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay h3 { font-size: 1.1rem; margin-bottom: 6px; }
        .gallery-overlay span { font-size: 0.8rem; color: #D4AF37; text-transform: uppercase; letter-spacing: 1px; }
        .gallery-overlay i { font-size: 1.8rem; margin-bottom: 10px; color: #D4AF37; }
        .lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.92); z-index: 3000; align-items: center; justify-content: center; }
        .lightbox.active { display: flex; }
        .lightbox-inner { position: relative; max-width: 900px; width: 92%; }
        .lightbox-img { width: 100%; max-height: 80vh; object-fit: contain; display: block; border: 3px solid #D4AF37; }
        .lightbox-close { position: absolute; top: -18px; right: -18px; background: #D4AF37; color: #1F3D36; border: none; width: 36px; height: 36px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .lightbox-close:hover { background: #fff; }
        .lightbox-caption { text-align: center; color: #D4AF37; margin-top: 14px; font-style: italic; font-size: 0.95rem; }
        .lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); background: #D4AF37; color: #1F3D36; border: none; width: 40px; height: 40px; border-radius: 50%; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .lightbox-nav:hover { background: #fff; }
        .lb-prev { left: -20px; }
        .lb-next { right: -20px; }

        /* FOOTER */
        footer { background: #1F3D36; color: #F5EFE6; text-align: center; padding: 40px 0; }
        .footer-links { display: flex; justify-content: center; gap: 30px; flex-wrap: wrap; margin-bottom: 20px; }
        .footer-links a { color: #D4AF37; text-decoration: none; font-size: 13px; }
        .footer-links a:hover { text-decoration: underline; }
        .footer-social { display: flex; justify-content: center; gap: 16px; margin-bottom: 20px; }
        .footer-social a {
            display: flex; align-items: center; justify-content: center;
            width: 42px; height: 42px; border-radius: 50%;
            border: 1.5px solid #D4AF37; color: #fff;
            font-size: 17px; text-decoration: none;
            transition: background 0.3s, color 0.3s;
        }
        .footer-social a:hover { background: #D4AF37; color: #1F3D36; }

        /* SCROLL BUTTONS — RIGHT */
        .scroll-btns {
            position: fixed; right: 24px; bottom: 110px;
            display: flex; flex-direction: column; gap: 10px; z-index: 500;
        }
        .scroll-btn {
            width: 44px; height: 44px; background: #D4AF37; border: none;
            border-radius: 50%; cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: background 0.3s;
        }
        .scroll-btn:hover { background: #c4a030; }
        .scroll-btn i { color: #1F3D36; font-size: 15px; }

        /* WHATSAPP FAB — LEFT */
        .wa-fab {
            position: fixed; left: 24px; bottom: 24px; z-index: 600;
        }
        .wa-fab-btn {
            width: 52px; height: 52px; background: #25D366; border: none;
            border-radius: 50%; cursor: pointer; display: flex;
            align-items: center; justify-content: center;
            box-shadow: 0 3px 12px rgba(0,0,0,0.25); position: relative;
            transition: transform 0.2s;
        }
        .wa-fab-btn:hover { transform: scale(1.08); }
        .wa-fab-btn i { color: #fff; font-size: 26px; }
        .wa-dot {
            position: absolute; top: 2px; right: 2px;
            width: 10px; height: 10px; background: #D4AF37;
            border-radius: 50%; border: 2px solid #fff;
        }

        /* WHATSAPP PANEL */
        .wa-panel {
            position: fixed; left: 24px; bottom: 90px;
            width: 300px; background: #fff; border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            z-index: 700; flex-direction: column; overflow: hidden;
            display: none; transform: translateY(10px); opacity: 0;
            transition: transform 0.25s, opacity 0.25s;
        }
        .wa-panel.open {
            display: flex; transform: translateY(0); opacity: 1;
        }
        .wa-hdr {
            background: #1F3D36; padding: 14px 16px;
            display: flex; align-items: center; gap: 10px;
        }
        .wa-av {
            width: 36px; height: 36px; background: #25D366;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0;
        }
        .wa-av i { color: #fff; font-size: 18px; }
        .wa-hdr-name { font-size: 14px; font-family: 'Times New Roman', serif; color: #D4AF37; font-weight: bold; display: block; }
        .wa-hdr-status { font-size: 11px; color: #a8c4b0; }
        .wa-close-btn {
            background: none; border: none; color: #D4AF37;
            font-size: 22px; cursor: pointer; margin-left: auto; line-height: 1;
        }
        .wa-msgs {
            padding: 14px; background: #f0ebe1;
            min-height: 200px; max-height: 260px; overflow-y: auto;
            display: flex; flex-direction: column; gap: 8px;
        }
        .msg {
            max-width: 82%; padding: 8px 12px;
            font-size: 13px; line-height: 1.45;
        }
        .msg.bot {
            background: #fff; color: #1F3D36;
            border-radius: 0 10px 10px 10px; align-self: flex-start;
        }
        .msg.user {
            background: #1F3D36; color: #F5EFE6;
            border-radius: 10px 10px 0 10px; align-self: flex-end;
        }
        .wa-inp-row {
            display: flex; padding: 10px;
            border-top: 1px solid #e0dbd0; gap: 8px;
            background: #fff; align-items: center;
        }
        .wa-inp-row input {
            flex: 1; border: 1px solid #ddd; border-radius: 20px;
            padding: 8px 12px; font-size: 13px; outline: none;
            font-family: 'Times New Roman', serif;
        }
        .wa-inp-row input:disabled { background: #f5f5f5; cursor: not-allowed; }
        .wa-snd-btn {
            background: #25D366; border: none; width: 34px; height: 34px;
            border-radius: 50%; cursor: pointer; display: flex;
            align-items: center; justify-content: center; flex-shrink: 0;
        }
        .wa-snd-btn:disabled { background: #aaa; cursor: not-allowed; }
        .wa-snd-btn i { color: #fff; font-size: 13px; }
        .typing-row {
            display: none; align-self: flex-start;
            background: #fff; padding: 8px 12px;
            border-radius: 0 10px 10px 10px; gap: 4px; align-items: center;
        }
        .typing-row.show { display: flex; }
        .td {
            width: 6px; height: 6px; background: #888;
            border-radius: 50%; animation: waBounce 1s infinite;
        }
        .td:nth-child(2) { animation-delay: .15s; }
        .td:nth-child(3) { animation-delay: .3s; }
        @keyframes waBounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }

        @media (max-width: 768px) {
            .hamburger { display: flex; }
            nav#mainNav { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100vh; background: #1F3D36; z-index: 1050; flex-direction: column; align-items: center; justify-content: center; flex: unset; }
            nav#mainNav.open { display: flex; }
            nav ul { flex-direction: column; align-items: center; gap: 22px; width: auto; padding-left: 0; justify-content: center; }
            nav ul li a { font-size: 18px; }
            .gallery-masonry { columns: 2; }
            .page-hero h1 { font-size: 2.4rem; }
            .lightbox-nav { width: 32px; height: 32px; font-size: 13px; }
            .lb-prev { left: -10px; }
            .lb-next { right: -10px; }
            .wa-panel { width: 270px; }
        }
        @media (max-width: 480px) {
            .container { width: 92%; }
            .gallery-masonry { columns: 1; }
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
                <li><a href="gallery.html" class="active-page" onclick="closeNav()">Gallery</a></li>
                <li><a href="index.html#accessibility" onclick="closeNav()">Accessibility</a></li>
                <li><a href="about.html" onclick="closeNav()">About Us</a></li>
                <li><a href="index.html#contact" onclick="closeNav()">Contact</a></li>
                <li><a href="blogs.html" onclick="closeNav()">Blogs</a></li>
                <li><a href="reservation.html" class="nav-btn" onclick="closeNav()">Book Now</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <section class="page-hero">
        <h1>Visual Gallery</h1>
        <div class="divider"></div>
        <p>A world of beauty, captured one journey at a time</p>
    </section>
    <div class="container">
        <div class="gallery-filters">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="europe">Europe</button>
            <button class="filter-btn" data-filter="asia">Asia</button>
            <button class="filter-btn" data-filter="nature">Nature</button>
            <button class="filter-btn" data-filter="city">City</button>
            <button class="filter-btn" data-filter="africa">Africa</button>
            <button class="filter-btn" data-filter="americas">Americas</button>
            <button class="filter-btn" data-filter="luxury">Luxury</button>
            <button class="filter-btn" data-filter="accommodation">Accommodation</button>
        </div>
        <div class="gallery-masonry" id="galleryGrid">
            <div class="gallery-item" data-cat="africa" onclick="openLightbox(0)"><img src="images/African Safari — Kenya.jpg" alt="African Safari Kenya"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>African Safari</h3><span>Africa · Kenya</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(1)"><img src="images/Amalfi Coast Retreat.jpg" alt="Amalfi Coast Retreat"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Amalfi Coast Retreat</h3><span>Europe</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(2)"><img src="images/Amazon Rainforest.jpg" alt="Amazon Rainforest"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Amazon Rainforest</h3><span>Nature · Americas</span></div></div>
            <div class="gallery-item" data-cat="africa" onclick="openLightbox(3)"><img src="images/Ancient Egypt Explorer.jpg" alt="Ancient Egypt Explorer"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Ancient Egypt Explorer</h3><span>Africa · Egypt</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(4)"><img src="images/Athens & Santorini.jpg" alt="Athens and Santorini"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Athens &amp; Santorini</h3><span>Europe · Greece</span></div></div>
            <div class="gallery-item" data-cat="asia" onclick="openLightbox(5)"><img src="images/Bali Beach Retreat.jpg" alt="Bali Beach Retreat"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Bali Beach Retreat</h3><span>Asia · Indonesia</span></div></div>
            <div class="gallery-item" data-cat="asia" onclick="openLightbox(6)"><img src="images/Bali.jpg" alt="Bali"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Bali</h3><span>Asia · Indonesia</span></div></div>
            <div class="gallery-item" data-cat="luxury" onclick="openLightbox(7)"><img src="images/Bora Bora Honeymoon Special.jpg" alt="Bora Bora Honeymoon Special"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Bora Bora Honeymoon</h3><span>Luxury · Pacific</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(8)"><img src="images/Canadian Rockies.jpg" alt="Canadian Rockies"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Canadian Rockies</h3><span>Nature · Americas</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(9)"><img src="images/France.jpg" alt="France"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>France</h3><span>Europe</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(10)"><img src="images/Iceland Aurora Hunt.jpg" alt="Iceland Aurora Hunt"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Iceland Aurora Hunt</h3><span>Nature · Europe</span></div></div>
            <div class="gallery-item" data-cat="americas" onclick="openLightbox(11)"><img src="images/Inca Trail — Peru.jpg" alt="Inca Trail Peru"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Inca Trail — Peru</h3><span>Americas</span></div></div>
            <div class="gallery-item" data-cat="asia" onclick="openLightbox(12)"><img src="images/India Heritage Tour.jpg" alt="India Heritage Tour"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>India Heritage Tour</h3><span>Asia · India</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(13)"><img src="images/Ireland.jpg" alt="Ireland"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Emerald Ireland</h3><span>Europe</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(14)"><img src="images/Italy.jpg" alt="Italy"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Classic Italy</h3><span>Europe</span></div></div>
            <div class="gallery-item" data-cat="asia" onclick="openLightbox(15)"><img src="images/Kyoto Cultural Immersion.jpg" alt="Kyoto Cultural Immersion"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Kyoto Cultural Immersion</h3><span>Asia · Japan</span></div></div>
            <div class="gallery-item" data-cat="asia" onclick="openLightbox(16)"><img src="images/Malaysia.jpg" alt="Malaysia"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Tropical Malaysia</h3><span>Asia</span></div></div>
            <div class="gallery-item" data-cat="luxury" onclick="openLightbox(17)"><img src="images/Maldives Luxury Escape.jpg" alt="Maldives Luxury Escape"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Maldives Luxury Escape</h3><span>Luxury · Asia</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(18)"><img src="images/Nepal Himalayan Trek.jpg" alt="Nepal Himalayan Trek"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Nepal Himalayan Trek</h3><span>Nature · Asia</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(19)"><img src="images/New Zealand Fjords.jpg" alt="New Zealand Fjords"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>New Zealand Fjords</h3><span>Nature · Pacific</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(20)"><img src="images/Norway Fjords & Glaciers.jpg" alt="Norway Fjords and Glaciers"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Norway Fjords &amp; Glaciers</h3><span>Nature · Europe</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(21)"><img src="images/Paris.jpg" alt="Paris"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Parisian Lights</h3><span>Europe · France</span></div></div>
            <div class="gallery-item" data-cat="americas" onclick="openLightbox(22)"><img src="images/Patagonia Expedition.jpg" alt="Patagonia Expedition"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Patagonia Expedition</h3><span>Americas</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(23)"><img src="images/Santorini Sun & Sea.jpg" alt="Santorini Sun and Sea"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Santorini Sun &amp; Sea</h3><span>Europe · Greece</span></div></div>
            <div class="gallery-item" data-cat="luxury" onclick="openLightbox(24)"><img src="images/Seychelles Luxury.jpg" alt="Seychelles Luxury"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Seychelles Luxury</h3><span>Luxury · Africa</span></div></div>
            <div class="gallery-item" data-cat="city" onclick="openLightbox(25)"><img src="images/Singapore.jpg" alt="Singapore"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Dazzling Singapore</h3><span>City · Asia</span></div></div>
            <div class="gallery-item" data-cat="asia" onclick="openLightbox(26)"><img src="images/Thai Islands Escape.jpg" alt="Thai Islands Escape"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Thai Islands Escape</h3><span>Asia · Thailand</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(27)"><img src="images/Tuscany Wine & Villas.jpg" alt="Tuscany Wine and Villas"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Tuscany Wine &amp; Villas</h3><span>Europe · Italy</span></div></div>
            <div class="gallery-item" data-cat="europe" onclick="openLightbox(28)"><img src="images/Venice Lovers' Escape.jpg" alt="Venice Lovers Escape"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Venice Lovers' Escape</h3><span>Europe · Italy</span></div></div>
            <div class="gallery-item" data-cat="nature" onclick="openLightbox(29)"><img src="images/Cliffs of Moher.jpg" alt="Cliffs of Moher"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Cliffs of Moher</h3><span>Nature · Ireland</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(30)"><img src="images/Brazil.jpg" alt="Brazil Hotel"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Brazil Stay</h3><span>Accommodation · Americas</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(31)"><img src="images/Hotel 1.jpg" alt="Hotel 1"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Boutique Hotel</h3><span>Accommodation</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(32)"><img src="images/Hotel 3.jpg" alt="Hotel 3"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Poolside Retreat</h3><span>Accommodation</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(33)"><img src="images/Hotel 4.jpg" alt="Hotel 4"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Sunset Villa</h3><span>Accommodation</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(34)"><img src="images/Hotel 6.jpg" alt="Hotel 6"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Overwater Bungalow</h3><span>Accommodation</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(35)"><img src="images/Hotel.jpg" alt="Hotel"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Grand Hotel</h3><span>Accommodation</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(36)"><img src="images/Italy hotel 1.jpg" alt="Italy Hotel 1"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Italian Hillside Hotel</h3><span>Accommodation · Italy</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(37)"><img src="images/Italy hotel 2.jpg" alt="Italy Hotel 2"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Trulli Retreat</h3><span>Accommodation · Italy</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(38)"><img src="images/Japen hotel 1.jpg" alt="Japan Hotel 1"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Tokyo City Hotel</h3><span>Accommodation · Japan</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(39)"><img src="images/japen hotel 2.jpg" alt="Japan Hotel 2"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Japanese Ryokan</h3><span>Accommodation · Japan</span></div></div>
            <div class="gallery-item" data-cat="accommodation" onclick="openLightbox(40)"><img src="images/rikyu.jpg" alt="Rikyu Ryokan"><div class="gallery-overlay"><i class="fa-solid fa-magnifying-glass-plus"></i><h3>Rikyu Ryokan</h3><span>Accommodation · Japan</span></div></div>
        </div>
    </div>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="closeLightboxBg(event)">
        <div class="lightbox-inner">
            <button class="lightbox-close" onclick="closeLightbox()"><i class="fa-solid fa-xmark"></i></button>
            <button class="lightbox-nav lb-prev" onclick="changeLightbox(-1)"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="lightbox-nav lb-next" onclick="changeLightbox(1)"><i class="fa-solid fa-chevron-right"></i></button>
            <img id="lightboxImg" src="" alt="" class="lightbox-img">
            <p class="lightbox-caption" id="lightboxCaption"></p>
        </div>
    </div>
</main>

<footer>
    <div class="footer-links">
        <a href="index.html">Home</a>
        <a href="about.html">About Us</a>
        <a href="gallery.html">Gallery</a>
        <a href="blogs.html">Blogs</a>
        <a href="reservation.html">Book Now</a>
    </div>
    <div class="footer-social">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="WhatsApp" onclick="toggleWAPanel(); return false;">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    </div>
    <p>&copy; 2026 PackNGo. <small>Crafted for Explorers.</small></p>
</footer>

<!-- Scroll Up / Down Buttons — FIXED RIGHT -->
<div class="scroll-btns">
    <button class="scroll-btn" onclick="window.scrollBy({top:-400,behavior:'smooth'})" title="Scroll Up">
        <i class="fa-solid fa-chevron-up"></i>
    </button>
    <button class="scroll-btn" onclick="window.scrollBy({top:400,behavior:'smooth'})" title="Scroll Down">
        <i class="fa-solid fa-chevron-down"></i>
    </button>
</div>

<!-- Floating WhatsApp Button — FIXED LEFT -->
<div class="wa-fab">
    <button class="wa-fab-btn" onclick="toggleWAPanel()" aria-label="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
        <span class="wa-dot"></span>
    </button>
</div>

<!-- WhatsApp Chatbot Panel — FIXED LEFT -->
<div class="wa-panel" id="waChatPanel">
    <div class="wa-hdr">
        <div class="wa-av">
            <i class="fa-brands fa-whatsapp"></i>
        </div>
        <div>
            <span class="wa-hdr-name">PackNGo Support</span>
            <span class="wa-hdr-status">Online · Replies instantly</span>
        </div>
        <button class="wa-close-btn" onclick="toggleWAPanel()">&times;</button>
    </div>
    <div class="wa-msgs" id="waMsgBox">
        <div class="msg bot">👋 Hello! Welcome to PackNGo. How can we help you today?</div>
    </div>
    <div class="wa-inp-row">
        <input type="text" id="waChatInput" placeholder="Type a message..." onkeydown="if(event.key==='Enter') waSendMsg()"/>
        <button class="wa-snd-btn" id="waSendBtn" onclick="waSendMsg()">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </div>
</div>

<script>
    /* ── NAV ── */
    const hamburger = document.getElementById('hamburgerBtn');
    const nav = document.getElementById('mainNav');
    hamburger.addEventListener('click', () => { nav.classList.toggle('open'); hamburger.classList.toggle('active'); });
    function closeNav() { nav.classList.remove('open'); hamburger.classList.remove('active'); }

    /* ── GALLERY FILTER ── */
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const filter = btn.dataset.filter;
            document.querySelectorAll('.gallery-item').forEach(item => {
                item.style.display = (filter === 'all' || item.dataset.cat === filter) ? '' : 'none';
            });
        });
    });

    /* ── LIGHTBOX ── */
    const images = [
        { src: "images/African Safari — Kenya.jpg", caption: "African Safari — Kenya · Into the Wild" },
        { src: "images/Amalfi Coast Retreat.jpg", caption: "Amalfi Coast Retreat — Mediterranean Splendour" },
        { src: "images/Amazon Rainforest.jpg", caption: "Amazon Rainforest — Nature's Greatest Kingdom" },
        { src: "images/Ancient Egypt Explorer.jpg", caption: "Ancient Egypt Explorer — Timeless Wonders" },
        { src: "images/Athens & Santorini.jpg", caption: "Athens & Santorini — Greek Island Magic" },
        { src: "images/Bali Beach Retreat.jpg", caption: "Bali Beach Retreat — Tropical Bliss" },
        { src: "images/Bali.jpg", caption: "Bali — Island of the Gods" },
        { src: "images/Bora Bora Honeymoon Special.jpg", caption: "Bora Bora Honeymoon — Paradise Found" },
        { src: "images/Canadian Rockies.jpg", caption: "Canadian Rockies — Majestic Wilderness" },
        { src: "images/France.jpg", caption: "France — Art, Culture & Charm" },
        { src: "images/Iceland Aurora Hunt.jpg", caption: "Iceland Aurora Hunt — Dancing Northern Lights" },
        { src: "images/Inca Trail — Peru.jpg", caption: "Inca Trail — Peru · Ancient Pathways" },
        { src: "images/India Heritage Tour.jpg", caption: "India Heritage Tour — A Tapestry of History" },
        { src: "images/Ireland.jpg", caption: "Emerald Ireland — Wild Atlantic Beauty" },
        { src: "images/Italy.jpg", caption: "Classic Italy — Timeless Elegance" },
        { src: "images/Kyoto Cultural Immersion.jpg", caption: "Kyoto Cultural Immersion — Where Tradition Lives" },
        { src: "images/Malaysia.jpg", caption: "Tropical Malaysia — Lush & Vibrant" },
        { src: "images/Maldives Luxury Escape.jpg", caption: "Maldives Luxury Escape — Overwater Perfection" },
        { src: "images/Nepal Himalayan Trek.jpg", caption: "Nepal Himalayan Trek — Roof of the World" },
        { src: "images/New Zealand Fjords.jpg", caption: "New Zealand Fjords — Land of the Long White Cloud" },
        { src: "images/Norway Fjords & Glaciers.jpg", caption: "Norway Fjords & Glaciers — Arctic Grandeur" },
        { src: "images/Paris.jpg", caption: "Parisian Lights — City of Romance" },
        { src: "images/Patagonia Expedition.jpg", caption: "Patagonia Expedition — End of the Earth" },
        { src: "images/Santorini Sun & Sea.jpg", caption: "Santorini Sun & Sea — Aegean Dream" },
        { src: "images/Seychelles Luxury.jpg", caption: "Seychelles Luxury — Pearl of the Indian Ocean" },
        { src: "images/Singapore.jpg", caption: "Dazzling Singapore — Modern Marvels" },
        { src: "images/Thai Islands Escape.jpg", caption: "Thai Islands Escape — Emerald Waters & White Sands" },
        { src: "images/Tuscany Wine & Villas.jpg", caption: "Tuscany Wine & Villas — Rolling Hills & Fine Wine" },
        { src: "images/Venice Lovers' Escape.jpg", caption: "Venice Lovers' Escape — La Serenissima" },
        { src: "images/Cliffs of Moher.jpg", caption: "Cliffs of Moher — Nature's Grandeur" },
        { src: "images/Brazil.jpg", caption: "Brazil Stay — Vibrant Accommodation" },
        { src: "images/Hotel 1.jpg", caption: "Boutique Hotel — Curated Comfort" },
        { src: "images/Hotel 3.jpg", caption: "Poolside Retreat — Resort Living" },
        { src: "images/Hotel 4.jpg", caption: "Sunset Villa — Golden Hour Views" },
        { src: "images/Hotel 6.jpg", caption: "Overwater Bungalow — Above the Lagoon" },
        { src: "images/Hotel.jpg", caption: "Grand Hotel — Timeless Hospitality" },
        { src: "images/Italy hotel 1.jpg", caption: "Italian Hillside Hotel — La Dolce Vita" },
        { src: "images/Italy hotel 2.jpg", caption: "Trulli Retreat — Puglia's Iconic Stays" },
        { src: "images/Japen hotel 1.jpg", caption: "Tokyo City Hotel — Urban Sophistication" },
        { src: "images/japen hotel 2.jpg", caption: "Japanese Ryokan — Tradition & Tranquillity" },
        { src: "images/rikyu.jpg", caption: "Rikyu Ryokan — Zen Retreat, Japan" }
    ];
    let currentIndex = 0;
    function openLightbox(index) {
        currentIndex = index;
        document.getElementById('lightboxImg').src = images[index].src;
        document.getElementById('lightboxCaption').textContent = images[index].caption;
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('active');
        document.body.style.overflow = '';
    }
    function closeLightboxBg(e) {
        if (e.target === document.getElementById('lightbox')) closeLightbox();
    }
    function changeLightbox(dir) {
        currentIndex = (currentIndex + dir + images.length) % images.length;
        document.getElementById('lightboxImg').src = images[currentIndex].src;
        document.getElementById('lightboxCaption').textContent = images[currentIndex].caption;
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') changeLightbox(1);
        if (e.key === 'ArrowLeft') changeLightbox(-1);
    });

    /* ── WHATSAPP AI CHATBOT ── */

    // Conversation history for multi-turn memory
    const waHistory = [];

    // PackNGo system prompt so the AI knows its role
    const WA_SYSTEM = `You are PackNGo's friendly travel support assistant embedded in a WhatsApp-style chat widget on the PackNGo travel website. PackNGo is a premium travel agency offering curated tours to 40+ destinations worldwide including Europe, Asia, Africa, the Americas, and the Pacific. Packages include flights, hotels, and guided tours starting from $999/person. You also offer custom itineraries, honeymoon specials, luxury escapes, and adventure treks.

Your job is to:
- Answer questions about destinations, tour packages, pricing, booking, and travel tips.
- Help users choose the right package based on their interests, budget, and travel dates.
- Encourage users to book via the website or request a custom itinerary.
- Be warm, enthusiastic, concise, and professional — like a knowledgeable travel expert.
- Keep replies short (2–4 sentences max) since this is a chat widget.
- Do not mention that you are an AI unless directly asked.`;

    function toggleWAPanel() {
        const panel = document.getElementById('waChatPanel');
        panel.classList.toggle('open');
    }

    async function waSendMsg() {
        const input = document.getElementById('waChatInput');
        const sendBtn = document.getElementById('waSendBtn');
        const text = input.value.trim();
        if (!text) return;

        const box = document.getElementById('waMsgBox');

        // Show user message
        const userMsg = document.createElement('div');
        userMsg.className = 'msg user';
        userMsg.textContent = text;
        box.appendChild(userMsg);
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        // Add to conversation history
        waHistory.push({ role: 'user', content: text });

        // Show typing indicator
        const typing = document.createElement('div');
        typing.className = 'typing-row show';
        typing.innerHTML = '<div class="td"></div><div class="td"></div><div class="td"></div>';
        box.appendChild(typing);
        box.scrollTop = box.scrollHeight;

        try {
            const response = await fetch('https://api.anthropic.com/v1/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    model: 'claude-sonnet-4-20250514',
                    max_tokens: 1000,
                    system: WA_SYSTEM,
                    messages: waHistory
                })
            });

            const data = await response.json();
            const reply = data.content && data.content[0] && data.content[0].text
                ? data.content[0].text
                : "Sorry, I couldn't process your request. Please try again!";

            // Add assistant reply to history
            waHistory.push({ role: 'assistant', content: reply });

            typing.remove();
            const botMsg = document.createElement('div');
            botMsg.className = 'msg bot';
            botMsg.textContent = reply;
            box.appendChild(botMsg);

        } catch (err) {
            typing.remove();
            const errMsg = document.createElement('div');
            errMsg.className = 'msg bot';
            errMsg.textContent = "Oops! Something went wrong. Please try again or contact us directly.";
            box.appendChild(errMsg);
        }

        box.scrollTop = box.scrollHeight;
        input.disabled = false;
        sendBtn.disabled = false;
        input.focus();
    }
</script>
<script src="api-client.js"></script>
</body>
</html>
