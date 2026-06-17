<?php
/**
 * PackNGo — Admin Panel Entry Point
 * Access: website.com/PackNGo/admin/index.php
 * NOT linked from any frontend page.
 * Completely standalone admin interface.
 */
declare(strict_types=1);
// No session or auth check here — auth is handled client-side via API
// The actual PHP session check happens in AdminController via AdminAuthMiddleware
$appConfig = require dirname(__DIR__) . '/config/app.php';
$appUrl    = rtrim($appConfig['app']['url'], '/');
$appName   = $appConfig['app']['name'] ?? 'PackNGo';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($appName) ?> — Admin Panel</title>
<meta name="robots" content="noindex, nofollow">
<style>
/* ══════════════════════════════════════════════════════
   CSS VARIABLES & RESET
══════════════════════════════════════════════════════ */
:root {
  --gold:#D4AF37; --gold-hover:#b8962e; --dark:#1F3D36; --dark2:#162e28;
  --cream:#F5EFE6; --light:#f9f6f0; --border:#e2d9c8; --border2:#ede6d8;
  --red:#c0392b; --red2:#a93226; --green:#27ae60; --blue:#2980b9;
  --sidebar:250px; --font:'Times New Roman',Georgia,serif;
  --shadow:0 2px 12px rgba(0,0,0,.08);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font);background:var(--light);color:#333;display:flex;min-height:100vh;font-size:14px;}
a{text-decoration:none;color:inherit;}
button{cursor:pointer;font-family:var(--font);}

/* ══════════════════════════════════════════════════════
   LOGIN PAGE
══════════════════════════════════════════════════════ */
#loginPage{
  display:flex;align-items:center;justify-content:center;
  min-height:100vh;width:100%;background:var(--dark);
  background-image:radial-gradient(circle at 30% 50%, rgba(212,175,55,.08) 0%, transparent 60%);
}
.login-wrap{text-align:center;}
.login-logo{color:var(--gold);font-size:2.2rem;letter-spacing:4px;font-weight:bold;margin-bottom:6px;}
.login-logo small{display:block;color:rgba(245,239,230,.4);font-size:11px;letter-spacing:2px;margin-top:4px;}
.login-box{
  background:#fff;width:360px;padding:40px 36px;margin-top:24px;
  border-top:4px solid var(--gold);box-shadow:0 20px 60px rgba(0,0,0,.3);
}
.login-box h2{color:var(--dark);font-size:18px;margin-bottom:4px;letter-spacing:1px;}
.login-box p{color:#999;font-size:13px;margin-bottom:28px;}
.login-field{margin-bottom:16px;text-align:left;}
.login-field label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.8px;color:#666;margin-bottom:6px;font-weight:bold;}
.login-field input{width:100%;padding:11px 14px;border:1.5px solid var(--border);font-family:var(--font);font-size:14px;outline:none;transition:.3s;border-radius:2px;}
.login-field input:focus{border-color:var(--gold);}
#loginError{color:var(--red);font-size:13px;text-align:center;margin-bottom:12px;min-height:18px;line-height:1.5;}
#loginBtn{
  width:100%;padding:13px;background:var(--gold);border:none;color:var(--dark);
  font-weight:bold;font-family:var(--font);font-size:15px;letter-spacing:1px;
  border-radius:2px;transition:.2s;
}
#loginBtn:hover{background:var(--gold-hover);}
#loginBtn:disabled{opacity:.6;cursor:not-allowed;}

/* ══════════════════════════════════════════════════════
   ADMIN APP LAYOUT
══════════════════════════════════════════════════════ */
#adminApp{display:none;width:100%;flex-direction:row;}
#adminApp.show{display:flex;}

/* Sidebar */
#sidebar{
  width:var(--sidebar);background:var(--dark);color:var(--cream);
  display:flex;flex-direction:column;position:fixed;top:0;left:0;
  height:100vh;z-index:200;transition:transform .3s;overflow-y:auto;
}
.sb-logo{padding:22px 20px 18px;border-bottom:1px solid rgba(212,175,55,.25);text-align:center;}
.sb-logo .mark{color:var(--gold);font-size:1.5rem;font-weight:bold;letter-spacing:2px;}
.sb-logo small{display:block;color:rgba(245,239,230,.4);font-size:10px;letter-spacing:2px;margin-top:3px;}
.nav-section{padding:16px 20px 5px;font-size:10px;letter-spacing:2px;color:rgba(245,239,230,.3);text-transform:uppercase;}
.nav-link{
  display:flex;align-items:center;gap:11px;padding:12px 20px;
  color:rgba(245,239,230,.7);font-size:13px;transition:.2s;
  border-left:3px solid transparent;position:relative;
}
.nav-link:hover,.nav-link.active{
  background:rgba(212,175,55,.1);color:var(--gold);border-left-color:var(--gold);
}
.nav-link .ni{font-size:15px;width:18px;text-align:center;}
.nav-badge{margin-left:auto;background:var(--red);color:#fff;padding:1px 7px;border-radius:10px;font-size:10px;font-weight:bold;display:none;}
.sb-footer{margin-top:auto;padding:16px 20px;border-top:1px solid rgba(212,175,55,.15);}
.sb-admin-name{font-size:11px;color:rgba(245,239,230,.4);margin-bottom:10px;}
#logoutBtn{
  width:100%;background:transparent;border:1px solid rgba(212,175,55,.35);
  color:var(--gold);padding:9px;font-family:var(--font);font-size:13px;
  border-radius:2px;transition:.2s;
}
#logoutBtn:hover{background:rgba(212,175,55,.12);}

/* Main Content */
#mainWrap{margin-left:var(--sidebar);flex:1;display:flex;flex-direction:column;min-height:100vh;}
#topbar{
  background:#fff;border-bottom:1px solid var(--border);
  padding:0 28px;height:58px;display:flex;align-items:center;
  justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:var(--shadow);
}
#topbar h1{font-size:17px;color:var(--dark);font-weight:normal;letter-spacing:.5px;}
.topbar-right{display:flex;align-items:center;gap:16px;font-size:12px;color:#999;}
#hamburger{display:none;background:none;border:none;font-size:22px;color:var(--dark);}

/* Pages */
.page{display:none;padding:28px;}
.page.active{display:block;}

/* ══════════════════════════════════════════════════════
   COMMON COMPONENTS
══════════════════════════════════════════════════════ */
/* Stat grid */
.stat-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:18px;margin-bottom:28px;}
.stat-card{background:#fff;border:1px solid var(--border);border-top:3px solid var(--gold);padding:20px 22px;border-radius:2px;}
.stat-card .sc-label{font-size:10px;text-transform:uppercase;letter-spacing:1.2px;color:#999;margin-bottom:8px;}
.stat-card .sc-value{font-size:30px;color:var(--dark);font-weight:bold;line-height:1;}
.stat-card .sc-sub{font-size:11px;color:#bbb;margin-top:5px;}

/* Cards */
.card{background:#fff;border:1px solid var(--border);border-radius:2px;margin-bottom:24px;overflow:hidden;}
.card-head{
  padding:14px 20px;border-bottom:1px solid var(--border);
  display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;
}
.card-head h2{font-size:14px;font-weight:bold;color:var(--dark);letter-spacing:.3px;}

/* Tables */
.table-wrap{overflow-x:auto;}
table{width:100%;border-collapse:collapse;font-size:13px;}
th{padding:10px 14px;text-align:left;background:var(--cream);color:var(--dark);
   font-weight:bold;font-size:10px;text-transform:uppercase;letter-spacing:.8px;
   border-bottom:1px solid var(--border);white-space:nowrap;}
td{padding:11px 14px;border-bottom:1px solid #f0ebe1;vertical-align:middle;}
tr:last-child td{border-bottom:none;}
tr:hover td{background:#fdfaf5;}
.td-action{display:flex;gap:6px;flex-wrap:wrap;}

/* Status badges */
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:bold;white-space:nowrap;}
.badge-pending  {background:#fff3cd;color:#856404;}
.badge-confirmed{background:#d4edda;color:#155724;}
.badge-cancelled{background:#f8d7da;color:#721c24;}
.badge-completed{background:#cce5ff;color:#004085;}
.badge-active   {background:#d4edda;color:#155724;}
.badge-blocked  {background:#f8d7da;color:#721c24;}
.badge-verified {background:#cce5ff;color:#004085;}
.badge-unverified{background:#fff3cd;color:#856404;}
.badge-published{background:#d4edda;color:#155724;}
.badge-draft    {background:#e2e3e5;color:#495057;}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border:none;
     font-family:var(--font);font-size:13px;border-radius:2px;transition:.2s;white-space:nowrap;}
.btn-gold{background:var(--gold);color:var(--dark);font-weight:bold;}
.btn-gold:hover{background:var(--gold-hover);}
.btn-dark{background:var(--dark);color:var(--cream);}
.btn-dark:hover{background:var(--dark2);}
.btn-red{background:var(--red);color:#fff;}
.btn-red:hover{background:var(--red2);}
.btn-blue{background:var(--blue);color:#fff;}
.btn-blue:hover{background:#2471a3;}
.btn-green{background:var(--green);color:#fff;}
.btn-green:hover{background:#219a52;}
.btn-outline{background:transparent;border:1px solid var(--border);color:#555;}
.btn-outline:hover{border-color:var(--gold);color:var(--dark);}
.btn-sm{padding:4px 10px;font-size:12px;}
.btn:disabled{opacity:.55;cursor:not-allowed;}

/* Toolbar / Filters */
.toolbar{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
.f-input{padding:7px 11px;border:1px solid var(--border);background:#fff;font-family:var(--font);font-size:13px;border-radius:2px;color:#333;outline:none;}
.f-input:focus{border-color:var(--gold);}
.f-select{padding:7px 11px;border:1px solid var(--border);background:#fff;font-family:var(--font);font-size:13px;border-radius:2px;color:#333;outline:none;}

/* Pagination */
.pagination{display:flex;gap:5px;align-items:center;justify-content:flex-end;padding:12px 16px;border-top:1px solid var(--border2);}
.pg-btn{padding:5px 11px;border:1px solid var(--border);background:#fff;font-family:var(--font);font-size:12px;border-radius:2px;}
.pg-btn.active{background:var(--gold);border-color:var(--gold);color:var(--dark);font-weight:bold;}
.pg-btn:hover:not(.active){border-color:var(--gold);}
.pg-info{font-size:11px;color:#aaa;margin-right:auto;}

/* Modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;align-items:center;justify-content:center;}
.modal-bg.open{display:flex;}
.modal{background:#fff;width:92%;max-width:580px;max-height:92vh;overflow-y:auto;
       border-top:4px solid var(--gold);border-radius:2px;padding:26px 30px;position:relative;}
.modal-lg{max-width:760px;}
.modal h3{font-size:16px;color:var(--dark);margin-bottom:20px;}
.modal-x{position:absolute;top:14px;right:16px;background:none;border:none;font-size:20px;color:#bbb;transition:.2s;}
.modal-x:hover{color:var(--red);}
.modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:20px;padding-top:16px;border-top:1px solid var(--border);}

/* Form */
.fg{margin-bottom:16px;}
.fg label{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:#666;margin-bottom:5px;font-weight:bold;}
.fg input,.fg select,.fg textarea{
  width:100%;padding:9px 12px;border:1px solid var(--border);
  font-family:var(--font);font-size:13px;border-radius:2px;outline:none;color:#333;
}
.fg input:focus,.fg select:focus,.fg textarea:focus{border-color:var(--gold);}
.fg textarea{resize:vertical;min-height:80px;}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.fg-row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;}

/* Alert */
.alert{padding:11px 15px;border-radius:2px;font-size:13px;margin-bottom:16px;}
.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
.alert-error  {background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
.alert-info   {background:#d1ecf1;color:#0c5460;border:1px solid #bee5eb;}

/* Empty state */
.empty{text-align:center;padding:48px 20px;color:#ccc;}
.empty .eicon{font-size:44px;margin-bottom:12px;}

/* Spinner */
.spin{display:inline-block;width:18px;height:18px;border:2px solid rgba(212,175,55,.25);
      border-top-color:var(--gold);border-radius:50%;animation:spin .7s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.loading-row td{text-align:center;padding:32px!important;}

/* Gallery grid */
.gallery-grid{padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;}
.gallery-item{position:relative;border:1px solid var(--border);border-radius:2px;overflow:hidden;background:#f9f6f0;}
.gallery-item img{width:100%;height:120px;object-fit:cover;display:block;}
.gallery-item .gi-name{padding:6px 8px;font-size:11px;color:#666;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.gallery-item .gi-del{position:absolute;top:5px;right:5px;background:rgba(192,57,43,.85);color:#fff;border:none;border-radius:2px;padding:3px 7px;font-size:11px;}

/* Report chart bars */
.bar-chart{padding:16px 20px;}
.bar-row{display:flex;align-items:center;gap:12px;margin-bottom:10px;font-size:12px;}
.bar-label{width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#555;flex-shrink:0;}
.bar-track{flex:1;height:18px;background:#f0ebe1;border-radius:2px;overflow:hidden;}
.bar-fill{height:100%;background:var(--gold);border-radius:2px;transition:width .5s ease;}
.bar-val{width:60px;text-align:right;color:#888;flex-shrink:0;}

/* Toggle switch */
.toggle{position:relative;display:inline-block;width:40px;height:22px;}
.toggle input{opacity:0;width:0;height:0;}
.toggle-slider{position:absolute;inset:0;background:#ccc;border-radius:22px;transition:.3s;cursor:pointer;}
.toggle-slider:before{content:'';position:absolute;width:16px;height:16px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.3s;}
.toggle input:checked + .toggle-slider{background:var(--green);}
.toggle input:checked + .toggle-slider:before{transform:translateX(18px);}

/* Responsive */
@media(max-width:900px){
  #sidebar{transform:translateX(-100%);}
  #sidebar.open{transform:none;}
  #mainWrap{margin-left:0;}
  #hamburger{display:block;}
  .stat-grid{grid-template-columns:1fr 1fr;}
  .fg-row,.fg-row3{grid-template-columns:1fr;}
}
@media(max-width:520px){
  .stat-grid{grid-template-columns:1fr;}
  .page{padding:16px;}
  .modal{padding:18px 16px;}
}
</style>
</head>
<body>

<!-- ══════════════════════════════════════════
     LOGIN
══════════════════════════════════════════ -->
<div id="loginPage">
  <div class="login-wrap">
    <div class="login-logo">✈ <?= htmlspecialchars($appName) ?><small>ADMINISTRATION PANEL</small></div>
    <div class="login-box">
      <h2>Secure Admin Login</h2>
      <p>This panel is restricted to authorized personnel only.</p>
      <div id="loginError"></div>
      <div class="login-field">
        <label>Admin Email</label>
        <input type="email" id="lEmail" placeholder="admin@packngo.store" autocomplete="username">
      </div>
      <div class="login-field">
        <label>Password</label>
        <input type="password" id="lPass" placeholder="••••••••" autocomplete="current-password">
      </div>
      <button id="loginBtn" onclick="doLogin()">Login to Dashboard</button>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════
     ADMIN APP
══════════════════════════════════════════ -->
<div id="adminApp">

  <!-- Sidebar -->
  <aside id="sidebar">
    <div class="sb-logo">
      <div class="mark">✈ <?= htmlspecialchars($appName) ?></div>
      <small>ADMIN PANEL</small>
    </div>
    <nav id="sideNav">
      <div class="nav-section">Overview</div>
      <a class="nav-link active" data-page="dashboard">
        <span class="ni">📊</span> Dashboard
      </a>
      <a class="nav-link" data-page="reports">
        <span class="ni">📈</span> Reports
      </a>

      <div class="nav-section">Bookings</div>
      <a class="nav-link" data-page="reservations">
        <span class="ni">🗓</span> Reservations
        <span class="nav-badge" id="badgePending">0</span>
      </a>

      <div class="nav-section">Customers</div>
      <a class="nav-link" data-page="users">
        <span class="ni">👥</span> Users
      </a>

      <div class="nav-section">Content</div>
      <a class="nav-link" data-page="destinations">
        <span class="ni">🌍</span> Destinations
      </a>
      <a class="nav-link" data-page="packages">
        <span class="ni">🧳</span> Tour Packages
      </a>
      <a class="nav-link" data-page="blog">
        <span class="ni">📝</span> Blog Posts
      </a>
      <a class="nav-link" data-page="gallery">
        <span class="ni">🖼</span> Gallery
      </a>

      <div class="nav-section">CRM</div>
      <a class="nav-link" data-page="messages">
        <span class="ni">✉️</span> Messages
        <span class="nav-badge" id="badgeMsg">0</span>
      </a>
      <a class="nav-link" data-page="subscribers">
        <span class="ni">📧</span> Subscribers
      </a>
    </nav>
    <div class="sb-footer">
      <div class="sb-admin-name" id="adminLabel">—</div>
      <button id="logoutBtn" onclick="doLogout()">⬡ Sign Out</button>
    </div>
  </aside>

  <!-- Main -->
  <div id="mainWrap">
    <div id="topbar">
      <div style="display:flex;align-items:center;gap:14px;">
        <button id="hamburger" onclick="toggleSidebar()">☰</button>
        <h1 id="pageTitle">Dashboard</h1>
      </div>
      <div class="topbar-right">
        <span id="topDate"></span>
        <a href="<?= htmlspecialchars($appUrl) ?>/index.html" target="_blank" style="color:var(--gold);">← View Site</a>
      </div>
    </div>

    <!-- ── DASHBOARD ── -->
    <div class="page active" id="page-dashboard">
      <div class="stat-grid" id="statGrid">
        <div class="stat-card"><div class="sc-label">Total Bookings</div><div class="sc-value" id="st-total">—</div></div>
        <div class="stat-card"><div class="sc-label">Pending</div><div class="sc-value" id="st-pending" style="color:#856404;">—</div></div>
        <div class="stat-card"><div class="sc-label">Confirmed</div><div class="sc-value" id="st-confirmed" style="color:#155724;">—</div></div>
        <div class="stat-card"><div class="sc-label">Completed</div><div class="sc-value" id="st-completed" style="color:#004085;">—</div></div>
        <div class="stat-card"><div class="sc-label">Today's Bookings</div><div class="sc-value" id="st-today">—</div></div>
        <div class="stat-card"><div class="sc-label">Total Revenue (USD)</div><div class="sc-value" id="st-revenue" style="font-size:20px;">—</div></div>
        <div class="stat-card"><div class="sc-label">Total Users</div><div class="sc-value" id="st-users">—</div></div>
        <div class="stat-card"><div class="sc-label">Total Visitors</div><div class="sc-value" id="st-visitors">—</div></div>
        <div class="stat-card"><div class="sc-label">Unread Messages</div><div class="sc-value" id="st-msgs" style="color:#856404;">—</div></div>
        <div class="stat-card"><div class="sc-label">Newsletter Subs</div><div class="sc-value" id="st-subs">—</div></div>
        <div class="stat-card"><div class="sc-label">Active Destinations</div><div class="sc-value" id="st-dests">—</div></div>
      </div>

      <div style="display:grid;grid-template-columns:3fr 2fr;gap:20px;">
        <div class="card">
          <div class="card-head">
            <h2>Recent Reservations</h2>
            <button class="btn btn-outline btn-sm" onclick="showPage('reservations')">View All</button>
          </div>
          <div class="table-wrap">
            <table><thead><tr><th>Ref</th><th>Guest</th><th>Destination</th><th>Departure</th><th>Status</th></tr></thead>
            <tbody id="recentBody"><tr class="loading-row"><td colspan="5"><div class="spin"></div></td></tr></tbody></table>
          </div>
        </div>
        <div class="card">
          <div class="card-head"><h2>Recent Registrations</h2></div>
          <div class="table-wrap">
            <table><thead><tr><th>Name</th><th>Email</th><th>Joined</th></tr></thead>
            <tbody id="recentUsersBody"><tr class="loading-row"><td colspan="3"><div class="spin"></div></td></tr></tbody>
          </div>
        </div>
      </div>
    </div>

    <!-- ── REPORTS ── -->
    <div class="page" id="page-reports">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
        <div class="card">
          <div class="card-head"><h2>Revenue by Package</h2></div>
          <div class="bar-chart" id="chartByPackage"><div class="spin"></div></div>
        </div>
        <div class="card">
          <div class="card-head"><h2>Top Destinations</h2></div>
          <div class="bar-chart" id="chartByDest"><div class="spin"></div></div>
        </div>
      </div>
      <div class="card">
        <div class="card-head"><h2>Monthly Performance (Last 12 Months)</h2></div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Month</th><th>Total Bookings</th><th>Confirmed</th><th>Completed</th><th>Cancelled</th><th>Revenue (USD)</th></tr></thead>
            <tbody id="monthlyBody"><tr class="loading-row"><td colspan="6"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
      </div>

      <!-- ── Visitor Traffic & Activity Reports ── -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;margin-top:20px;">
        <div class="card">
          <div class="card-head"><h2>Daily Visitors (Last 14 Days)</h2></div>
          <div class="bar-chart" id="chartDailyVisitors"><div class="spin"></div></div>
        </div>
        <div class="card">
          <div class="card-head"><h2>Top Visited Pages</h2></div>
          <div class="bar-chart" id="chartTopPages"><div class="spin"></div></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
        <div class="card">
          <div class="card-head"><h2>Weekly & Monthly Visitor Traffic</h2></div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Time Period</th><th colspan="2">Unique Visitor Count</th></tr></thead>
              <tbody id="visitorPeriodBody"><tr class="loading-row"><td colspan="3"><div class="spin"></div></td></tr></tbody>
            </table>
          </div>
        </div>
        <div class="card">
          <div class="card-head"><h2>Recent Visitor Activity Logs</h2></div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Timestamp</th><th>Page URL</th><th>Visitor IP (Browser)</th></tr></thead>
              <tbody id="visitorActivityBody"><tr class="loading-row"><td colspan="3"><div class="spin"></div></td></tr></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- ── RESERVATIONS ── -->
    <div class="page" id="page-reservations">
      <div class="card">
        <div class="card-head">
          <h2>All Reservations</h2>
          <div class="toolbar">
            <input class="f-input" id="resSearch" placeholder="Search ref / name / email…" oninput="debounce(()=>loadRes(1),400)()">
            <select class="f-select" id="resStatusFilter" onchange="loadRes(1)">
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="cancelled">Cancelled</option>
              <option value="completed">Completed</option>
            </select>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Ref</th><th>Guest</th><th>Email</th><th>Destination</th><th>Package</th><th>Departure</th><th>Travellers</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="resBody"><tr class="loading-row"><td colspan="10"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
        <div class="pagination" id="resPag"></div>
      </div>
    </div>

    <!-- ── USERS ── -->
    <div class="page" id="page-users">
      <div class="card">
        <div class="card-head">
          <h2>Registered Users</h2>
          <div class="toolbar">
            <input class="f-input" id="userSearch" placeholder="Search name / email…" oninput="debounce(()=>loadUsers(1),400)()">
            <select class="f-select" id="userStatusFilter" onchange="loadUsers(1)">
              <option value="">All Users</option>
              <option value="active">Active</option>
              <option value="inactive">Blocked</option>
              <option value="verified">Verified</option>
            </select>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th>Verified</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody id="usersBody"><tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
        <div class="pagination" id="usersPag"></div>
      </div>
    </div>

    <!-- ── DESTINATIONS ── -->
    <div class="page" id="page-destinations">
      <div class="card">
        <div class="card-head">
          <h2>Destinations</h2>
          <button class="btn btn-gold" onclick="openDestModal()">+ Add Destination</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Name / Slug</th><th>Country</th><th>Category</th><th>Best Season</th><th>Active</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody id="destBody"><tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── PACKAGES ── -->
    <div class="page" id="page-packages">
      <div class="card">
        <div class="card-head">
          <h2>Tour Packages</h2>
          <button class="btn btn-gold" onclick="openPkgModal()">+ Add Package</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Name</th><th>Duration</th><th>Price (USD)</th><th>Featured</th><th>Active</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody id="pkgBody"><tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ── BLOG ── -->
    <div class="page" id="page-blog">
      <div class="card">
        <div class="card-head">
          <h2>Blog Posts</h2>
          <button class="btn btn-gold" onclick="openPostModal()">+ Add Post</button>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Read Time</th><th>Featured</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody id="blogBody"><tr class="loading-row"><td colspan="8"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
        <div class="pagination" id="blogPag"></div>
      </div>
    </div>

    <!-- ── GALLERY ── -->
    <div class="page" id="page-gallery">
      <div class="card">
        <div class="card-head">
          <h2>Gallery Images</h2>
          <label class="btn btn-gold" style="cursor:pointer;">
            + Upload Image
            <input type="file" id="galleryUpload" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="uploadGallery(this)">
          </label>
        </div>
        <div class="gallery-grid" id="galleryGrid"><div class="spin"></div></div>
      </div>
    </div>

    <!-- ── MESSAGES ── -->
    <div class="page" id="page-messages">
      <div class="card">
        <div class="card-head">
          <h2>Contact Messages</h2>
          <div class="toolbar">
            <select class="f-select" id="msgFilter" onchange="loadMessages()">
              <option value="">Unread Only</option>
              <option value="all">All Messages</option>
            </select>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>From</th><th>Email</th><th>Subject</th><th>Message Preview</th><th>Received</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="msgBody"><tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
        <div class="pagination" id="msgPag"></div>
      </div>
    </div>

    <!-- ── SUBSCRIBERS ── -->
    <div class="page" id="page-subscribers">
      <div class="card">
        <div class="card-head">
          <h2>Newsletter Subscribers</h2>
          <span id="subTotal" style="font-size:12px;color:#aaa;"></span>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>#</th><th>Email</th><th>Name</th><th>Subscribed</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody id="subBody"><tr class="loading-row"><td colspan="6"><div class="spin"></div></td></tr></tbody>
          </table>
        </div>
        <div class="pagination" id="subPag"></div>
      </div>
    </div>

  </div><!-- /mainWrap -->
</div><!-- /adminApp -->

<!-- ══════════════════════════════════════════
     MODALS
══════════════════════════════════════════ -->

<!-- Reservation Detail Modal -->
<div class="modal-bg" id="mRes">
  <div class="modal">
    <button class="modal-x" onclick="closeModal('mRes')">✕</button>
    <h3>Reservation Details</h3>
    <div id="mResContent"></div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:16px;">
      <div class="fg">
        <label>Update Status</label>
        <select class="f-select" id="mResStatus" style="width:100%;">
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="cancelled">Cancelled</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      <div class="fg">
        <label>Total Price (USD)</label>
        <input type="number" id="mResPrice" class="f-input" style="width:100%;" min="0" step="0.01">
      </div>
    </div>
    <div class="fg">
      <label>Admin Notes</label>
      <textarea id="mResNotes" class="f-input" style="width:100%;" rows="2"></textarea>
    </div>
    <div class="modal-footer">
      <button class="btn btn-red btn-sm" onclick="deleteRes()">Delete</button>
      <button class="btn btn-outline" onclick="closeModal('mRes')">Cancel</button>
      <button class="btn btn-gold" onclick="saveResStatus()">Update</button>
    </div>
  </div>
</div>

<!-- Contact Message Reply Modal -->
<div class="modal-bg" id="mMsgReply">
  <div class="modal">
    <button class="modal-x" onclick="closeModal('mMsgReply')">✕</button>
    <h3>Feedback / Contact Message</h3>
    <input type="hidden" id="mMsgReplyId">
    <div id="mMsgReplyContent"></div>
    <div class="fg" style="margin-top:14px;">
      <label>Your Reply</label>
      <textarea id="mMsgReplyText" class="f-input" style="width:100%;" rows="4" maxlength="4000" placeholder="Type your reply to the sender…"></textarea>
      <div style="font-size:12px;color:#c0392b;margin-top:4px;" id="mMsgReplyError"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('mMsgReply')">Cancel</button>
      <button class="btn btn-gold" id="mMsgReplyBtn" onclick="sendMsgReply()">Send Reply</button>
    </div>
  </div>
</div>

<!-- User Detail Modal -->
<div class="modal-bg" id="mUser">
  <div class="modal modal-lg">
    <button class="modal-x" onclick="closeModal('mUser')">✕</button>
    <h3>User Details</h3>
    <div id="mUserContent"></div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('mUser')">Close</button>
      <button class="btn btn-red" id="mUserBlockBtn" onclick="toggleBlock()">Block User</button>
      <button class="btn btn-red" onclick="deleteUser()">Delete User</button>
    </div>
  </div>
</div>

<!-- Destination Modal -->
<div class="modal-bg" id="mDest">
  <div class="modal modal-lg">
    <button class="modal-x" onclick="closeModal('mDest')">✕</button>
    <h3 id="mDestTitle">Add Destination</h3>
    <input type="hidden" id="dId">
    <div class="fg-row">
      <div class="fg"><label>Name *</label><input id="dName" placeholder="e.g. Paris"></div>
      <div class="fg"><label>Slug *</label><input id="dSlug" placeholder="e.g. paris"></div>
    </div>
    <div class="fg"><label>Tagline</label><input id="dTagline" placeholder="One-line description"></div>
    <div class="fg-row">
      <div class="fg"><label>Category *</label>
        <select id="dCat"><option value="beach">Beach</option><option value="adventure">Adventure</option>
        <option value="culture">Culture</option><option value="romance">Romance</option><option value="nature">Nature</option></select>
      </div>
      <div class="fg"><label>Country</label><input id="dCountry" placeholder="e.g. France"></div>
    </div>
    <div class="fg-row">
      <div class="fg"><label>Best Season</label><input id="dSeason" placeholder="e.g. June – September"></div>
      <div class="fg"><label>Currency</label><input id="dCurrency" placeholder="e.g. Euro (€)"></div>
    </div>
    <div class="fg-row">
      <div class="fg"><label>Language</label><input id="dLang" placeholder="e.g. French"></div>
      <div class="fg"><label>Climate</label><input id="dClimate" placeholder="e.g. Mediterranean"></div>
    </div>
    <div class="fg-row">
      <div class="fg"><label>Timezone</label><input id="dTz" placeholder="e.g. CET (UTC+1)"></div>
      <div class="fg"><label>Sort Order</label><input id="dOrder" type="number" value="99"></div>
    </div>
    <div class="fg"><label>About</label><textarea id="dAbout" rows="3" placeholder="Description…"></textarea></div>
    <div class="fg"><label>Active</label>
      <select id="dActive"><option value="1">Yes — Visible</option><option value="0">No — Hidden</option></select>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('mDest')">Cancel</button>
      <button class="btn btn-gold" onclick="saveDest()">Save Destination</button>
    </div>
  </div>
</div>

<!-- Package Modal -->
<div class="modal-bg" id="mPkg">
  <div class="modal modal-lg">
    <button class="modal-x" onclick="closeModal('mPkg')">✕</button>
    <h3 id="mPkgTitle">Add Package</h3>
    <input type="hidden" id="pkgId">
    <div class="fg-row">
      <div class="fg"><label>Name *</label><input id="pkgName" placeholder="e.g. Luxury Escape"></div>
      <div class="fg"><label>Slug *</label><input id="pkgSlug" placeholder="e.g. luxury-escape"></div>
    </div>
    <div class="fg"><label>Tagline</label><input id="pkgTagline" placeholder="Short one-liner"></div>
    <div class="fg-row">
      <div class="fg"><label>Price (USD) *</label><input id="pkgPrice" type="number" min="0" step="0.01" placeholder="0.00"></div>
      <div class="fg"><label>Duration (Days)</label><input id="pkgDays" type="number" min="1" value="7"></div>
    </div>
    <div class="fg-row">
      <div class="fg"><label>Icon (emoji)</label><input id="pkgIcon" placeholder="✦" maxlength="4"></div>
      <div class="fg"><label>Sort Order</label><input id="pkgOrder" type="number" value="99"></div>
    </div>
    <div class="fg"><label>Description</label><textarea id="pkgDesc" rows="3" placeholder="Full description…"></textarea></div>
    <div class="fg"><label>Inclusions (one per line)</label><textarea id="pkgIncludes" rows="4" placeholder="5-star accommodation&#10;Airport transfers&#10;Guided tours"></textarea></div>
    <div class="fg-row">
      <div class="fg"><label>Featured</label>
        <select id="pkgFeatured"><option value="0">No</option><option value="1">Yes</option></select>
      </div>
      <div class="fg"><label>Active</label>
        <select id="pkgActive"><option value="1">Yes</option><option value="0">No</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('mPkg')">Cancel</button>
      <button class="btn btn-gold" onclick="savePkg()">Save Package</button>
    </div>
  </div>
</div>

<!-- Blog Post Modal -->
<div class="modal-bg" id="mPost">
  <div class="modal modal-lg">
    <button class="modal-x" onclick="closeModal('mPost')">✕</button>
    <h3 id="mPostTitle">Add Blog Post</h3>
    <input type="hidden" id="postId">
    <div class="fg-row">
      <div class="fg"><label>Title *</label><input id="postTitle" placeholder="Post title…"></div>
      <div class="fg"><label>Slug *</label><input id="postSlug" placeholder="post-slug"></div>
    </div>
    <div class="fg-row">
      <div class="fg"><label>Category</label>
        <select id="postCat"><option value="">— Select —</option></select>
      </div>
      <div class="fg"><label>Author</label><input id="postAuthor" value="PackNGo Team"></div>
    </div>
    <div class="fg"><label>Excerpt</label><textarea id="postExcerpt" rows="2" placeholder="Short description…"></textarea></div>
    <div class="fg"><label>Content</label><textarea id="postContent" rows="8" placeholder="Full post content…"></textarea></div>
    <div class="fg-row">
      <div class="fg"><label>Read Time (mins)</label><input id="postRead" type="number" min="1" value="5"></div>
      <div class="fg"><label>Featured</label>
        <select id="postFeatured"><option value="0">No</option><option value="1">Yes</option></select>
      </div>
    </div>
    <div class="fg"><label>Status</label>
      <select id="postPublished"><option value="0">Draft</option><option value="1">Published</option></select>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="closeModal('mPost')">Cancel</button>
      <button class="btn btn-gold" onclick="savePost()">Save Post</button>
    </div>
  </div>
</div>

<!-- Toast notification -->
<div id="toast" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:none;
     padding:12px 20px;border-radius:3px;font-size:13px;box-shadow:0 4px 20px rgba(0,0,0,.15);
     max-width:320px;transition:.3s;"></div>

<script>
/* ══════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════ */
var BASE = (function(){
  var p = window.location.pathname;
  // Remove /admin/... suffix to get the project root
  var idx = p.toLowerCase().indexOf('/admin');
  if(idx > 0) return window.location.origin + p.substring(0, idx);
  // Fallback: go one directory up from current path
  var parts = p.split('/');
  parts.pop();
  if(parts[parts.length-1].toLowerCase()==='admin') parts.pop();
  return window.location.origin + parts.join('/');
})();
// Direct PHP file call with _url param - bypasses .htaccess rewrite issues
var API = BASE;
var _csrf = null;
var currentAdmin = null;
// Page state
var resPage=1, userPage=1, blogPage=1, subPage=1, msgPage=1;
var selectedResId=null, selectedUserId=null;
var blogCats=[];

/* ══════════════════════════════════════════════════════
   UTILITIES
══════════════════════════════════════════════════════ */
function esc(s){ return s==null?'':String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function fmtDate(dt){ return dt ? new Date(dt).toLocaleString('en-GB',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) : '—'; }
function fmtMoney(v){ return '$'+Number(v||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }

function debounce(fn, ms) {
  var t; return function(){ clearTimeout(t); t=setTimeout(fn, ms); };
}

function toast(msg, type) {
  var el = document.getElementById('toast');
  el.textContent = msg;
  el.style.background = type==='error' ? '#f8d7da' : type==='success' ? '#d4edda' : '#fff3cd';
  el.style.color = type==='error' ? '#721c24' : type==='success' ? '#155724' : '#856404';
  el.style.border = '1px solid '+(type==='error'?'#f5c6cb':type==='success'?'#c3e6cb':'#ffc107');
  el.style.display = 'block';
  clearTimeout(el._t);
  el._t = setTimeout(function(){ el.style.display='none'; }, 3500);
}

function statusBadge(s){
  return '<span class="badge badge-'+s+'">'+esc(s)+'</span>';
}

function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }

function renderPag(elId, pag, fn) {
  var el = document.getElementById(elId);
  if (!el || pag.last_page<=1){ if(el)el.innerHTML=''; return; }
  var h='<span class="pg-info">'+pag.total+' total — page '+pag.current_page+' of '+pag.last_page+'</span>';
  if(pag.current_page>1) h+='<button class="pg-btn" onclick="('+fn.name+')('+(pag.current_page-1)+')">‹</button>';
  var s=Math.max(1,pag.current_page-2), e=Math.min(pag.last_page,pag.current_page+2);
  for(var i=s;i<=e;i++) h+='<button class="pg-btn'+(i===pag.current_page?' active':'')+'" onclick="('+fn.name+')('+i+')">'+i+'</button>';
  if(pag.current_page<pag.last_page) h+='<button class="pg-btn" onclick="('+fn.name+')('+(pag.current_page+1)+')">›</button>';
  el.innerHTML=h;
}

function detailRow(label, val){
  return '<tr><td style="padding:7px 10px;color:#888;border-bottom:1px solid #f0ebe1;width:130px;white-space:nowrap;">'+label+'</td>'
       + '<td style="padding:7px 10px;border-bottom:1px solid #f0ebe1;">'+val+'</td></tr>';
}

/* ══════════════════════════════════════════════════════
   API WRAPPER
══════════════════════════════════════════════════════ */
async function getCsrf(){
  if(_csrf) return _csrf;
  var r=await fetch(API+'/api/auth/csrf',{credentials:'include'});
  var d=await r.json();
  return (_csrf=d.csrf_token||'');
}

async function api(path, method, body){
  method=(method||'GET').toUpperCase();
  var token=await getCsrf();
  var opts={ method, credentials:'include', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'} };
  if(token&&method!=='GET') opts.headers['X-CSRF-Token']=token;
  if(body!=null) opts.body=JSON.stringify(body);
  if(method!=='GET') _csrf=null;
  var resp=await fetch(API+path,opts);
  if(!resp.ok && resp.status===404){return {success:false,message:'API endpoint not found.'};}
  return resp.json();
}

/* ══════════════════════════════════════════════════════
   LOGIN / LOGOUT
══════════════════════════════════════════════════════ */
document.getElementById('lPass').addEventListener('keydown',function(e){ if(e.key==='Enter') doLogin(); });

async function doLogin(){
  var email=document.getElementById('lEmail').value.trim();
  var pass=document.getElementById('lPass').value;
  var errEl=document.getElementById('loginError');
  var btn=document.getElementById('loginBtn');
  errEl.textContent='';
  if(!email||!pass){ errEl.textContent='Please enter your email and password.'; return; }
  btn.textContent='Signing in…'; btn.disabled=true;
  try{
    var d=await api('/api/auth/login','POST',{email,password:pass});
    if(d.success && d.user.role==='admin'){
      _csrf=d.csrf_token;
      currentAdmin=d.user;
      document.getElementById('loginPage').style.display='none';
      document.getElementById('adminApp').classList.add('show');
      document.getElementById('adminLabel').textContent=d.user.first_name+' '+d.user.last_name;
      document.getElementById('topDate').textContent=new Date().toLocaleDateString('en-GB',{weekday:'short',day:'numeric',month:'long',year:'numeric'});
      initApp();
    } else {
      errEl.textContent = d.message || (d.user&&d.user.role!=='admin' ? 'Access denied: not an admin account.' : 'Invalid credentials.');
    }
  } catch(e){ errEl.textContent='Network error. Is the server running?'; }
  btn.textContent='Login to Dashboard'; btn.disabled=false;
}

async function doLogout(){
  await api('/api/auth/logout','POST',{});
  location.reload();
}

/* ══════════════════════════════════════════════════════
   NAVIGATION
══════════════════════════════════════════════════════ */
function showPage(name){
  document.querySelectorAll('.page').forEach(function(p){ p.classList.remove('active'); });
  document.querySelectorAll('.nav-link').forEach(function(a){ a.classList.remove('active'); });
  var pg=document.getElementById('page-'+name);
  if(pg) pg.classList.add('active');
  var lnk=document.querySelector('.nav-link[data-page="'+name+'"]');
  if(lnk) lnk.classList.add('active');
  var titles={dashboard:'Dashboard',reports:'Reports & Analytics',reservations:'Reservations',
    users:'Users',destinations:'Destinations',packages:'Tour Packages',blog:'Blog Posts',
    gallery:'Gallery',messages:'Contact Messages',subscribers:'Newsletter Subscribers'};
  document.getElementById('pageTitle').textContent=titles[name]||name;
  // Load data for the page
  if(name==='dashboard')    loadDashboard();
  if(name==='reports')      loadReports();
  if(name==='reservations') loadRes(1);
  if(name==='users')        loadUsers(1);
  if(name==='destinations') loadDests();
  if(name==='packages')     loadPkgs();
  if(name==='blog')         { loadBlogCats(); loadBlog(1); }
  if(name==='gallery')      loadGallery();
  if(name==='messages')     loadMessages();
  if(name==='subscribers')  loadSubs(1);
  // Close sidebar on mobile
  if(window.innerWidth<900) document.getElementById('sidebar').classList.remove('open');
}

document.querySelectorAll('.nav-link').forEach(function(a){
  a.addEventListener('click',function(e){ e.preventDefault(); showPage(this.dataset.page); });
});

function toggleSidebar(){ document.getElementById('sidebar').classList.toggle('open'); }

/* ══════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════ */
function initApp(){ loadDashboard(); }

/* ══════════════════════════════════════════════════════
   DASHBOARD
══════════════════════════════════════════════════════ */
async function loadDashboard(){
  var d=await api('/api/admin/dashboard');
  if(!d.success) return;
  var s=d.data.stats, r=s.reservations;
  document.getElementById('st-total').textContent     = r.total||0;
  document.getElementById('st-pending').textContent   = r.pending||0;
  document.getElementById('st-confirmed').textContent = r.confirmed||0;
  document.getElementById('st-completed').textContent = r.completed||0;
  document.getElementById('st-today').textContent     = r.today||0;
  document.getElementById('st-revenue').textContent   = fmtMoney(r.revenue);
  document.getElementById('st-users').textContent     = s.total_users||0;
  document.getElementById('st-visitors').textContent  = s.total_visitors||0;
  document.getElementById('st-msgs').textContent      = s.unread_messages||0;
  document.getElementById('st-subs').textContent      = s.newsletter_subs||0;
  document.getElementById('st-dests').textContent     = s.total_dests||0;

  // Badges
  var pb=parseInt(r.pending)||0;
  var mb=parseInt(s.unread_messages)||0;
  var pbEl=document.getElementById('badgePending'), mbEl=document.getElementById('badgeMsg');
  if(pb>0){pbEl.textContent=pb;pbEl.style.display='inline';}
  if(mb>0){mbEl.textContent=mb;mbEl.style.display='inline';}

  // Recent reservations
  var rb=document.getElementById('recentBody');
  if(!d.data.recent_reservations||!d.data.recent_reservations.length){
    rb.innerHTML='<tr><td colspan="5"><div class="empty"><div class="eicon">📋</div>No reservations yet.</div></td></tr>'; 
  } else {
    rb.innerHTML=d.data.recent_reservations.map(function(r){
      return '<tr>'
        +'<td><strong>'+esc(r.booking_ref)+'</strong></td>'
        +'<td>'+esc(r.first_name+' '+r.last_name)+'</td>'
        +'<td>'+esc(r.destination_name||'—')+'</td>'
        +'<td>'+esc(r.departure_date||'—')+'</td>'
        +'<td>'+statusBadge(r.status)+'</td>'
      +'</tr>';
    }).join('');
  }

  // Recent users
  var ub=document.getElementById('recentUsersBody');
  if(!d.data.recent_users||!d.data.recent_users.length){
    ub.innerHTML='<tr><td colspan="3"><div class="empty">No users yet.</div></td></tr>';
  } else {
    ub.innerHTML=d.data.recent_users.map(function(u){
      return '<tr><td>'+esc(u.first_name+' '+u.last_name)+'</td><td>'+esc(u.email)+'</td><td>'+fmtDate(u.created_at)+'</td></tr>';
    }).join('');
  }
}

/* ══════════════════════════════════════════════════════
   REPORTS
══════════════════════════════════════════════════════ */
async function loadReports(){
  document.getElementById('chartByPackage').innerHTML='<div class="spin"></div>';
  document.getElementById('chartByDest').innerHTML='<div class="spin"></div>';
  document.getElementById('monthlyBody').innerHTML='<tr class="loading-row"><td colspan="6"><div class="spin"></div></td></tr>';
  document.getElementById('chartDailyVisitors').innerHTML='<div class="spin"></div>';
  document.getElementById('chartTopPages').innerHTML='<div class="spin"></div>';
  document.getElementById('visitorPeriodBody').innerHTML='<tr class="loading-row"><td colspan="3"><div class="spin"></div></td></tr>';
  document.getElementById('visitorActivityBody').innerHTML='<tr class="loading-row"><td colspan="3"><div class="spin"></div></td></tr>';
  var d=await api('/api/admin/reports');
  if(!d.success){ toast('Failed to load reports.','error'); return; }

  // Bar chart: revenue by package
  var maxPkg=Math.max(1,...d.data.by_package.map(function(x){ return parseFloat(x.revenue)||0; }));
  document.getElementById('chartByPackage').innerHTML=d.data.by_package.map(function(p){
    var pct=((parseFloat(p.revenue)||0)/maxPkg*100).toFixed(1);
    return '<div class="bar-row">'
      +'<div class="bar-label" title="'+esc(p.package)+'">'+esc(p.package)+'</div>'
      +'<div class="bar-track"><div class="bar-fill" style="width:'+pct+'%"></div></div>'
      +'<div class="bar-val">'+fmtMoney(p.revenue)+'</div>'
    +'</div>';
  }).join('') || '<div class="empty">No data.</div>';

  // Bar chart: bookings by destination
  var maxDest=Math.max(1,...d.data.by_destination.map(function(x){ return parseInt(x.bookings)||0; }));
  document.getElementById('chartByDest').innerHTML=d.data.by_destination.map(function(p){
    var pct=((parseInt(p.bookings)||0)/maxDest*100).toFixed(1);
    return '<div class="bar-row">'
      +'<div class="bar-label" title="'+esc(p.destination)+'">'+esc(p.destination)+'</div>'
      +'<div class="bar-track"><div class="bar-fill" style="width:'+pct+'%;background:var(--dark);"></div></div>'
      +'<div class="bar-val" style="color:#333;">'+esc(p.bookings)+' trips</div>'
    +'</div>';
  }).join('') || '<div class="empty">No data.</div>';

  // Monthly table
  document.getElementById('monthlyBody').innerHTML=d.data.monthly.length
    ? d.data.monthly.map(function(m){
        return '<tr><td><strong>'+esc(m.month_label)+'</strong></td>'
          +'<td>'+esc(m.total)+'</td><td>'+esc(m.confirmed)+'</td>'
          +'<td>'+esc(m.completed)+'</td><td>'+esc(m.cancelled)+'</td>'
          +'<td><strong>'+fmtMoney(m.revenue)+'</strong></td></tr>';
      }).join('')
    : '<tr><td colspan="6"><div class="empty">No data available yet.</div></td></tr>';

  // Daily visitors chart
  var maxDailyVis=Math.max(1,...d.data.daily_visitors.map(function(x){ return parseInt(x.visitor_count)||0; }));
  document.getElementById('chartDailyVisitors').innerHTML=d.data.daily_visitors.map(function(dv){
    var pct=((parseInt(dv.visitor_count)||0)/maxDailyVis*100).toFixed(1);
    return '<div class="bar-row">'
      +'<div class="bar-label" title="'+esc(dv.date_label)+'">'+esc(dv.date_label)+'</div>'
      +'<div class="bar-track"><div class="bar-fill" style="width:'+pct+'%;background:var(--gold);"></div></div>'
      +'<div class="bar-val">'+esc(dv.visitor_count)+' visits</div>'
    +'</div>';
  }).join('') || '<div class="empty">No daily visitor data.</div>';

  // Top visited pages chart
  var maxPages=Math.max(1,...d.data.top_pages.map(function(x){ return parseInt(x.visit_count)||0; }));
  document.getElementById('chartTopPages').innerHTML=d.data.top_pages.map(function(tp){
    var pct=((parseInt(tp.visit_count)||0)/maxPages*100).toFixed(1);
    return '<div class="bar-row">'
      +'<div class="bar-label" title="'+esc(tp.page_url)+'">'+esc(tp.page_url)+'</div>'
      +'<div class="bar-track"><div class="bar-fill" style="width:'+pct+'%;background:var(--dark);"></div></div>'
      +'<div class="bar-val">'+esc(tp.visit_count)+' views</div>'
    +'</div>';
  }).join('') || '<div class="empty">No page visit data.</div>';

  // Weekly & Monthly visitor stats table
  var visitorPeriodBody=document.getElementById('visitorPeriodBody');
  var weeklyHtml=d.data.weekly_visitors.map(function(wv){
    return '<tr><td><strong>'+esc(wv.week_label)+'</strong></td>'
      +'<td colspan="2">'+esc(wv.visitor_count)+' unique visitors</td></tr>';
  }).join('');
  
  var monthlyHtml=d.data.monthly_visitors.map(function(mv){
    return '<tr style="background:#fdfaf5;"><td><strong>'+esc(mv.month_label)+' (Month)</strong></td>'
      +'<td colspan="2">'+esc(mv.visitor_count)+' unique visitors</td></tr>';
  }).join('');
  
  visitorPeriodBody.innerHTML=(weeklyHtml || monthlyHtml) ? (weeklyHtml + monthlyHtml) : '<tr><td colspan="3"><div class="empty">No data available yet.</div></td></tr>';

  // Recent activity logs table
  document.getElementById('visitorActivityBody').innerHTML=d.data.recent_activity.length
    ? d.data.recent_activity.map(function(act){
        var browser='Device';
        var ua=act.user_agent || '';
        if(ua.indexOf('Firefox')!==-1) browser='Firefox';
        else if(ua.indexOf('Chrome')!==-1) browser='Chrome';
        else if(ua.indexOf('Safari')!==-1) browser='Safari';
        else if(ua.indexOf('Edge')!==-1) browser='Edge';
        else if(ua.indexOf('MSIE')!==-1 || ua.indexOf('Trident')!==-1) browser='IE';
        
        return '<tr>'
          +'<td>'+fmtDate(act.visited_at)+'</td>'
          +'<td><span style="color:var(--dark);font-weight:bold;">'+esc(act.page_url)+'</span></td>'
          +'<td>'+esc(act.ip_address || '—')+' <span style="font-size:11px;color:#aaa;">('+browser+')</span></td>'
          +'</tr>';
      }).join('')
    : '<tr><td colspan="3"><div class="empty">No visitor activity logged.</div></td></tr>';
}

/* ══════════════════════════════════════════════════════
   RESERVATIONS
══════════════════════════════════════════════════════ */
async function loadRes(page){
  resPage=page||1;
  var status=document.getElementById('resStatusFilter').value;
  var search=document.getElementById('resSearch').value.trim();
  var q='/api/admin/reservations?page='+resPage+'&limit=20'+(status?'&status='+status:'')+(search?'&search='+encodeURIComponent(search):'');
  document.getElementById('resBody').innerHTML='<tr class="loading-row"><td colspan="10"><div class="spin"></div></td></tr>';
  var d=await api(q);
  if(!d.success||!d.data.data.length){
    document.getElementById('resBody').innerHTML='<tr><td colspan="10"><div class="empty"><div class="eicon">📋</div>No reservations found.</div></td></tr>';
    document.getElementById('resPag').innerHTML='';
    return;
  }
  document.getElementById('resBody').innerHTML=d.data.data.map(function(r){
    return '<tr>'
      +'<td><strong>'+esc(r.booking_ref)+'</strong></td>'
      +'<td>'+esc(r.first_name+' '+r.last_name)+'</td>'
      +'<td>'+esc(r.email)+'</td>'
      +'<td>'+esc(r.destination_name||'—')+'</td>'
      +'<td>'+esc(r.package_name||'—')+'</td>'
      +'<td>'+esc(r.departure_date||'—')+'</td>'
      +'<td>'+esc(r.travellers||'—')+'</td>'
      +'<td>'+(r.total_price?fmtMoney(r.total_price):'—')+'</td>'
      +'<td>'+statusBadge(r.status)+'</td>'
      +'<td><button class="btn btn-outline btn-sm" onclick="openRes('+r.id+')">Details</button></td>'
    +'</tr>';
  }).join('');
  renderPag('resPag', d.data, loadRes);
}

async function openRes(id){
  selectedResId=id;
  document.getElementById('mResContent').innerHTML='<div class="spin"></div>';
  openModal('mRes');
  var d=await api('/api/admin/reservations/'+id);
  if(!d.success){ document.getElementById('mResContent').innerHTML='<p style="color:red;">Failed to load.</p>'; return; }
  var r=d.data;
  document.getElementById('mResStatus').value=r.status;
  document.getElementById('mResNotes').value=r.admin_notes||'';
  document.getElementById('mResPrice').value=r.total_price||'';
  document.getElementById('mResContent').innerHTML=
    '<table style="width:100%;margin-bottom:12px;">'
    +detailRow('Booking Ref','<strong>'+esc(r.booking_ref)+'</strong>')
    +detailRow('Guest',esc(r.first_name+' '+r.last_name))
    +detailRow('Email',esc(r.email))
    +detailRow('Phone',esc((r.phone_code||'')+' '+(r.phone||'—')))
    +detailRow('Destination',esc(r.destination_name||r.destination_text||'—'))
    +detailRow('Package',esc(r.package_name||r.package_text||'—'))
    +detailRow('Departure',esc(r.departure_date||'—'))
    +detailRow('Return',esc(r.return_date||'—'))
    +detailRow('Travellers',esc(r.travellers||'—'))
    +detailRow('Budget',esc(r.budget_range||'—'))
    +detailRow('Accommodation',esc(r.accommodation||'—'))
    +detailRow('Special Requests',esc(r.special_requests||'—'))
    +detailRow('User Account',esc(r.user_name||'Guest'))
    +detailRow('IP Address',esc(r.ip_address||'—'))
    +detailRow('Submitted',fmtDate(r.created_at))
    +'</table>';
}

async function saveResStatus(){
  if(!selectedResId) return;
  var d=await api('/api/admin/reservations/'+selectedResId+'/status','PATCH',{
    status:document.getElementById('mResStatus').value,
    admin_notes:document.getElementById('mResNotes').value,
    total_price:document.getElementById('mResPrice').value||null,
  });
  if(d.success){ closeModal('mRes'); loadRes(resPage); loadDashboard(); toast('Reservation updated.','success'); }
  else toast(d.message||'Update failed.','error');
}

async function deleteRes(){
  if(!selectedResId||!confirm('Delete this reservation? This cannot be undone.')) return;
  var d=await api('/api/admin/reservations/'+selectedResId,'DELETE',{});
  if(d.success){ closeModal('mRes'); loadRes(1); loadDashboard(); toast('Reservation deleted.','success'); }
  else toast(d.message||'Delete failed.','error');
}

/* ══════════════════════════════════════════════════════
   USERS
══════════════════════════════════════════════════════ */
async function loadUsers(page){
  userPage=page||1;
  var search=document.getElementById('userSearch').value.trim();
  var status=document.getElementById('userStatusFilter').value;
  var q='/api/admin/users?page='+userPage+(search?'&search='+encodeURIComponent(search):'')+(status?'&status='+status:'');
  document.getElementById('usersBody').innerHTML='<tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr>';
  var d=await api(q);
  if(!d.success||!d.data.data.length){
    document.getElementById('usersBody').innerHTML='<tr><td colspan="7"><div class="empty"><div class="eicon">👥</div>No users found.</div></td></tr>';
    document.getElementById('usersPag').innerHTML=''; return;
  }
  document.getElementById('usersBody').innerHTML=d.data.data.map(function(u){
    return '<tr>'
      +'<td><strong>'+esc(u.first_name+' '+u.last_name)+'</strong></td>'
      +'<td>'+esc(u.email)+'</td>'
      +'<td>'+esc(u.phone||'—')+'</td>'
      +'<td>'+(u.is_active?'<span class="badge badge-active">Active</span>':'<span class="badge badge-blocked">Blocked</span>')+'</td>'
      +'<td>'+(u.email_verified?'<span class="badge badge-verified">Verified</span>':'<span class="badge badge-unverified">Pending</span>')+'</td>'
      +'<td>'+fmtDate(u.created_at)+'</td>'
      +'<td class="td-action">'
        +'<button class="btn btn-outline btn-sm" onclick="openUser('+u.id+')">View</button>'
        +'<button class="btn '+(u.is_active?'btn-red':'btn-green')+' btn-sm" onclick="quickToggleBlock('+u.id+','+u.is_active+')">'+(u.is_active?'Block':'Unblock')+'</button>'
      +'</td>'
    +'</tr>';
  }).join('');
  renderPag('usersPag',d.data,loadUsers);
}

async function openUser(id){
  selectedUserId=id;
  document.getElementById('mUserContent').innerHTML='<div class="spin" style="margin:20px auto;display:block;"></div>';
  openModal('mUser');
  var d=await api('/api/admin/users/'+id);
  if(!d.success){ document.getElementById('mUserContent').innerHTML='<p style="color:red;">Failed to load.</p>'; return; }
  var u=d.data.user, bk=d.data.bookings;
  document.getElementById('mUserBlockBtn').textContent=u.is_active?'Block User':'Unblock User';
  document.getElementById('mUserContent').innerHTML=
    '<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">'
      +'<table style="font-size:13px;">'
        +detailRow('Name',esc(u.first_name+' '+u.last_name))
        +detailRow('Email',esc(u.email))
        +detailRow('Phone',esc((u.phone_code||'')+' '+(u.phone||'—')))
        +detailRow('Status',u.is_active?'<span class="badge badge-active">Active</span>':'<span class="badge badge-blocked">Blocked</span>')
        +detailRow('Verified',u.email_verified?'<span class="badge badge-verified">Yes</span>':'<span class="badge badge-unverified">No</span>')
        +detailRow('Joined',fmtDate(u.created_at))
      +'</table>'
      +'<table style="font-size:13px;">'
        +detailRow('Total Bookings','<strong>'+esc(u.booking_count)+'</strong>')
        +detailRow('Total Spent','<strong>'+fmtMoney(u.total_spent)+'</strong>')
      +'</table>'
    +'</div>'
    +'<div style="font-weight:bold;color:var(--dark);font-size:12px;margin-bottom:8px;text-transform:uppercase;letter-spacing:.8px;">Booking History</div>'
    +(bk.length ? '<div class="table-wrap"><table>'
      +'<thead><tr><th>Ref</th><th>Destination</th><th>Departure</th><th>Status</th><th>Total</th></tr></thead><tbody>'
      +bk.map(function(b){
        return '<tr><td>'+esc(b.booking_ref)+'</td><td>'+esc(b.destination||'—')+'</td>'
          +'<td>'+esc(b.departure_date||'—')+'</td><td>'+statusBadge(b.status)+'</td>'
          +'<td>'+(b.total_price?fmtMoney(b.total_price):'—')+'</td></tr>';
      }).join('')
      +'</tbody></table></div>'
    : '<p style="color:#aaa;font-size:13px;">No bookings yet.</p>');
}

async function toggleBlock(){
  if(!selectedUserId) return;
  var d=await api('/api/admin/users/'+selectedUserId+'/toggle-block','PATCH',{});
  if(d.success){ closeModal('mUser'); loadUsers(userPage); toast(d.message,'success'); }
  else toast(d.message||'Failed.','error');
}

async function quickToggleBlock(id, currentActive){
  var action=currentActive?'block':'unblock';
  if(!confirm((currentActive?'Block':'Unblock')+' this user?')) return;
  var d=await api('/api/admin/users/'+id+'/toggle-block','PATCH',{});
  if(d.success){ loadUsers(userPage); toast(d.message,'success'); }
  else toast(d.message||'Failed.','error');
}

async function deleteUser(){
  if(!selectedUserId||!confirm('Permanently delete this user and all their data? This cannot be undone.')) return;
  var d=await api('/api/admin/users/'+selectedUserId,'DELETE',{});
  if(d.success){ closeModal('mUser'); loadUsers(1); toast('User deleted.','success'); }
  else toast(d.message||'Failed.','error');
}

/* ══════════════════════════════════════════════════════
   DESTINATIONS
══════════════════════════════════════════════════════ */
async function loadDests(){
  document.getElementById('destBody').innerHTML='<tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr>';
  var d=await api('/api/admin/destinations');
  if(!d.success||!d.data.length){
    document.getElementById('destBody').innerHTML='<tr><td colspan="7"><div class="empty"><div class="eicon">🌍</div>No destinations. Add one!</div></td></tr>'; return;
  }
  document.getElementById('destBody').innerHTML=d.data.map(function(x){
    return '<tr>'
      +'<td><strong>'+esc(x.name)+'</strong><br><small style="color:#aaa;">'+esc(x.slug)+'</small></td>'
      +'<td>'+esc(x.country||'—')+'</td>'
      +'<td style="text-transform:capitalize;">'+esc(x.category)+'</td>'
      +'<td>'+esc(x.best_season||'—')+'</td>'
      +'<td>'+(x.is_active==1?'<span class="badge badge-active">Yes</span>':'<span class="badge badge-blocked">No</span>')+'</td>'
      +'<td>'+esc(x.sort_order)+'</td>'
      +'<td class="td-action">'
        +'<button class="btn btn-outline btn-sm" onclick=\'editDest('+JSON.stringify(x).replace(/\'/g,"\\'")+')\'> Edit</button>'
        +'<button class="btn btn-red btn-sm" onclick="deleteDest('+x.id+',\''+esc(x.name)+'\')">Delete</button>'
      +'</td>'
    +'</tr>';
  }).join('');
}

function openDestModal(){
  document.getElementById('mDestTitle').textContent='Add Destination';
  ['dId','dName','dSlug','dTagline','dCountry','dSeason','dCurrency','dLang','dClimate','dTz','dAbout'].forEach(function(i){ document.getElementById(i).value=''; });
  document.getElementById('dOrder').value=99;
  document.getElementById('dActive').value=1;
  document.getElementById('dCat').value='beach';
  openModal('mDest');
}

function editDest(x){
  document.getElementById('mDestTitle').textContent='Edit Destination';
  document.getElementById('dId').value=x.id;
  document.getElementById('dName').value=x.name||'';
  document.getElementById('dSlug').value=x.slug||'';
  document.getElementById('dTagline').value=x.tagline||'';
  document.getElementById('dCat').value=x.category||'beach';
  document.getElementById('dCountry').value=x.country||'';
  document.getElementById('dSeason').value=x.best_season||'';
  document.getElementById('dCurrency').value=x.currency||'';
  document.getElementById('dLang').value=x.language||'';
  document.getElementById('dClimate').value=x.climate||'';
  document.getElementById('dTz').value=x.timezone||'';
  document.getElementById('dAbout').value=x.about||'';
  document.getElementById('dOrder').value=x.sort_order||99;
  document.getElementById('dActive').value=x.is_active;
  openModal('mDest');
}

async function saveDest(){
  var id=document.getElementById('dId').value;
  var p={
    name:document.getElementById('dName').value.trim(),
    slug:document.getElementById('dSlug').value.trim(),
    tagline:document.getElementById('dTagline').value.trim(),
    category:document.getElementById('dCat').value,
    country:document.getElementById('dCountry').value.trim(),
    best_season:document.getElementById('dSeason').value.trim(),
    currency:document.getElementById('dCurrency').value.trim(),
    language:document.getElementById('dLang').value.trim(),
    climate:document.getElementById('dClimate').value.trim(),
    timezone:document.getElementById('dTz').value.trim(),
    about:document.getElementById('dAbout').value.trim(),
    sort_order:parseInt(document.getElementById('dOrder').value)||99,
    is_active:document.getElementById('dActive').value,
  };
  if(!p.name||!p.slug||!p.category){ toast('Name, Slug, and Category are required.','error'); return; }
  var d=id ? await api('/api/admin/destinations/'+id,'PUT',p) : await api('/api/admin/destinations','POST',p);
  if(d.success){ closeModal('mDest'); loadDests(); toast(id?'Destination updated.':'Destination created.','success'); }
  else toast(d.message||'Save failed.','error');
}

async function deleteDest(id,name){
  if(!confirm('Delete "'+name+'"? This cannot be undone.')) return;
  var d=await api('/api/admin/destinations/'+id,'DELETE',{});
  if(d.success){ loadDests(); toast('Destination deleted.','success'); }
  else toast(d.message||'Failed.','error');
}

// Auto-generate slug
document.getElementById('dName').addEventListener('input',function(){
  if(!document.getElementById('dId').value){
    document.getElementById('dSlug').value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
  }
});

/* ══════════════════════════════════════════════════════
   PACKAGES
══════════════════════════════════════════════════════ */
async function loadPkgs(){
  document.getElementById('pkgBody').innerHTML='<tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr>';
  var d=await api('/api/admin/packages');
  if(!d.success||!d.data.length){
    document.getElementById('pkgBody').innerHTML='<tr><td colspan="7"><div class="empty"><div class="eicon">🧳</div>No packages. Add one!</div></td></tr>'; return;
  }
  document.getElementById('pkgBody').innerHTML=d.data.map(function(x){
    var incl=[];
    try{ incl=JSON.parse(x.includes||'[]'); } catch(e){}
    return '<tr>'
      +'<td><strong>'+esc(x.icon||'')+'  '+esc(x.name)+'</strong><br><small style="color:#aaa;">'+esc(x.tagline||'')+'</small></td>'
      +'<td>'+esc(x.duration_days)+' days</td>'
      +'<td><strong>'+fmtMoney(x.price_usd)+'</strong></td>'
      +'<td>'+(x.is_featured==1?'<span class="badge badge-confirmed">★ Yes</span>':'<span class="badge badge-draft">No</span>')+'</td>'
      +'<td>'+(x.is_active==1?'<span class="badge badge-active">Yes</span>':'<span class="badge badge-blocked">No</span>')+'</td>'
      +'<td>'+esc(x.sort_order)+'</td>'
      +'<td class="td-action">'
        +'<button class="btn btn-outline btn-sm" onclick=\'editPkg('+JSON.stringify(x).replace(/\'/g,"\\'")+')\'> Edit</button>'
        +'<button class="btn btn-red btn-sm" onclick="deletePkg('+x.id+',\''+esc(x.name)+'\')">Delete</button>'
      +'</td>'
    +'</tr>';
  }).join('');
}

function openPkgModal(){
  document.getElementById('mPkgTitle').textContent='Add Package';
  ['pkgId','pkgName','pkgSlug','pkgTagline','pkgDesc','pkgIncludes'].forEach(function(i){ document.getElementById(i).value=''; });
  document.getElementById('pkgPrice').value='';
  document.getElementById('pkgDays').value=7;
  document.getElementById('pkgIcon').value='✦';
  document.getElementById('pkgOrder').value=99;
  document.getElementById('pkgFeatured').value=0;
  document.getElementById('pkgActive').value=1;
  openModal('mPkg');
}

function editPkg(x){
  document.getElementById('mPkgTitle').textContent='Edit Package';
  document.getElementById('pkgId').value=x.id;
  document.getElementById('pkgName').value=x.name||'';
  document.getElementById('pkgSlug').value=x.slug||'';
  document.getElementById('pkgTagline').value=x.tagline||'';
  document.getElementById('pkgDesc').value=x.description||'';
  document.getElementById('pkgPrice').value=x.price_usd||'';
  document.getElementById('pkgDays').value=x.duration_days||7;
  document.getElementById('pkgIcon').value=x.icon||'✦';
  document.getElementById('pkgOrder').value=x.sort_order||99;
  document.getElementById('pkgFeatured').value=x.is_featured||0;
  document.getElementById('pkgActive').value=x.is_active!=null?x.is_active:1;
  var incl=[]; try{incl=JSON.parse(x.includes||'[]');}catch(e){}
  document.getElementById('pkgIncludes').value=incl.join('\n');
  openModal('mPkg');
}

async function savePkg(){
  var id=document.getElementById('pkgId').value;
  var p={
    name:document.getElementById('pkgName').value.trim(),
    slug:document.getElementById('pkgSlug').value.trim(),
    tagline:document.getElementById('pkgTagline').value.trim(),
    description:document.getElementById('pkgDesc').value.trim(),
    price_usd:document.getElementById('pkgPrice').value,
    duration_days:document.getElementById('pkgDays').value,
    icon:document.getElementById('pkgIcon').value||'✦',
    includes:document.getElementById('pkgIncludes').value,
    sort_order:document.getElementById('pkgOrder').value,
    is_featured:document.getElementById('pkgFeatured').value,
    is_active:document.getElementById('pkgActive').value,
  };
  if(!p.name||!p.slug||!p.price_usd){ toast('Name, Slug, and Price are required.','error'); return; }
  var d=id ? await api('/api/admin/packages/'+id,'PUT',p) : await api('/api/admin/packages','POST',p);
  if(d.success){ closeModal('mPkg'); loadPkgs(); toast(id?'Package updated.':'Package created.','success'); }
  else toast(d.message||'Save failed.','error');
}

async function deletePkg(id,name){
  if(!confirm('Delete package "'+name+'"?')) return;
  var d=await api('/api/admin/packages/'+id,'DELETE',{});
  if(d.success){ loadPkgs(); toast('Package deleted.','success'); }
  else toast(d.message||'Failed.','error');
}

document.getElementById('pkgName').addEventListener('input',function(){
  if(!document.getElementById('pkgId').value){
    document.getElementById('pkgSlug').value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
  }
});

/* ══════════════════════════════════════════════════════
   BLOG
══════════════════════════════════════════════════════ */
async function loadBlogCats(){
  if(blogCats.length) return;
  var d=await api('/api/admin/blog/categories');
  if(d.success){ blogCats=d.data; refreshBlogCatSelect(); }
}

function refreshBlogCatSelect(){
  var sel=document.getElementById('postCat');
  sel.innerHTML='<option value="">— Select Category —</option>';
  blogCats.forEach(function(c){ sel.innerHTML+='<option value="'+c.id+'">'+esc(c.name)+'</option>'; });
}

async function loadBlog(page){
  blogPage=page||1;
  document.getElementById('blogBody').innerHTML='<tr class="loading-row"><td colspan="8"><div class="spin"></div></td></tr>';
  var d=await api('/api/admin/blog/posts?page='+blogPage);
  if(!d.success||!d.data.data.length){
    document.getElementById('blogBody').innerHTML='<tr><td colspan="8"><div class="empty"><div class="eicon">📝</div>No blog posts yet.</div></td></tr>';
    document.getElementById('blogPag').innerHTML=''; return;
  }
  document.getElementById('blogBody').innerHTML=d.data.data.map(function(p){
    return '<tr>'
      +'<td><strong>'+esc(p.title)+'</strong><br><small style="color:#aaa;">'+esc(p.slug)+'</small></td>'
      +'<td>'+esc(p.category_name||'—')+'</td>'
      +'<td>'+esc(p.author||'—')+'</td>'
      +'<td>'+esc(p.read_minutes)+' min</td>'
      +'<td>'+(p.is_featured?'<span class="badge badge-confirmed">★ Yes</span>':'—')+'</td>'
      +'<td>'+(p.is_published?'<span class="badge badge-published">Published</span>':'<span class="badge badge-draft">Draft</span>')+'</td>'
      +'<td>'+fmtDate(p.created_at)+'</td>'
      +'<td class="td-action">'
        +'<button class="btn btn-outline btn-sm" onclick="editPost('+p.id+')">Edit</button>'
        +'<button class="btn btn-red btn-sm" onclick="deletePost('+p.id+')">Delete</button>'
      +'</td>'
    +'</tr>';
  }).join('');
  renderPag('blogPag',d.data,loadBlog);
}

function openPostModal(){
  document.getElementById('mPostTitle').textContent='Add Blog Post';
  ['postId','postTitle','postSlug','postExcerpt','postContent'].forEach(function(i){ document.getElementById(i).value=''; });
  document.getElementById('postAuthor').value='PackNGo Team';
  document.getElementById('postRead').value=5;
  document.getElementById('postFeatured').value=0;
  document.getElementById('postPublished').value=0;
  document.getElementById('postCat').value='';
  openModal('mPost');
}

async function editPost(id){
  var d=await api('/api/admin/blog/posts/'+id);
  if(!d.success){ toast('Failed to load post.','error'); return; }
  var p=d.data;
  document.getElementById('mPostTitle').textContent='Edit Blog Post';
  document.getElementById('postId').value=p.id;
  document.getElementById('postTitle').value=p.title||'';
  document.getElementById('postSlug').value=p.slug||'';
  document.getElementById('postExcerpt').value=p.excerpt||'';
  document.getElementById('postContent').value=p.content||'';
  document.getElementById('postAuthor').value=p.author||'PackNGo Team';
  document.getElementById('postRead').value=p.read_minutes||5;
  document.getElementById('postFeatured').value=p.is_featured||0;
  document.getElementById('postPublished').value=p.is_published||0;
  document.getElementById('postCat').value=p.category_id||'';
  openModal('mPost');
}

async function savePost(){
  var id=document.getElementById('postId').value;
  var p={
    title:document.getElementById('postTitle').value.trim(),
    slug:document.getElementById('postSlug').value.trim(),
    excerpt:document.getElementById('postExcerpt').value.trim(),
    content:document.getElementById('postContent').value.trim(),
    author:document.getElementById('postAuthor').value.trim(),
    read_minutes:document.getElementById('postRead').value,
    is_featured:document.getElementById('postFeatured').value,
    is_published:document.getElementById('postPublished').value,
    category_id:document.getElementById('postCat').value||null,
  };
  if(!p.title||!p.slug){ toast('Title and Slug are required.','error'); return; }
  var d=id ? await api('/api/admin/blog/posts/'+id,'PUT',p) : await api('/api/admin/blog/posts','POST',p);
  if(d.success){ closeModal('mPost'); loadBlog(blogPage); toast('Post saved.','success'); }
  else toast(d.message||'Save failed.','error');
}

async function deletePost(id){
  if(!confirm('Delete this blog post?')) return;
  var d=await api('/api/admin/blog/posts/'+id,'DELETE',{});
  if(d.success){ loadBlog(1); toast('Post deleted.','success'); }
  else toast(d.message||'Failed.','error');
}

document.getElementById('postTitle').addEventListener('input',function(){
  if(!document.getElementById('postId').value){
    document.getElementById('postSlug').value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
  }
});

/* ══════════════════════════════════════════════════════
   GALLERY
══════════════════════════════════════════════════════ */
async function loadGallery(){
  document.getElementById('galleryGrid').innerHTML='<div style="padding:20px;"><div class="spin"></div></div>';
  var d=await api('/api/gallery');
  if(!d.success||!d.data.length){
    document.getElementById('galleryGrid').innerHTML='<div class="empty"><div class="eicon">🖼</div><p>No images uploaded yet.</p></div>'; return;
  }
  document.getElementById('galleryGrid').innerHTML=d.data.map(function(img){
    return '<div class="gallery-item">'
      +'<img src="../'+esc(img.file_path)+'" alt="'+esc(img.alt_text||img.title||'')+'" loading="lazy" onerror="this.src=\'https://placehold.co/150x120/f5efe6/1f3d36?text=IMG\'">'
      +'<div class="gi-name">'+esc(img.title||img.file_name)+'</div>'
      +'<button class="gi-del" onclick="deleteGallery('+img.id+')">✕</button>'
    +'</div>';
  }).join('');
}

async function uploadGallery(input){
  if(!input.files.length) return;
  var formData=new FormData();
  formData.append('image',input.files[0]);
  formData.append('title',input.files[0].name.replace(/\.[^.]+$/,''));
  var token=await getCsrf(); _csrf=null;
  toast('Uploading…','info');
  var resp=await fetch(API+'/api/admin/upload',{
    method:'POST',credentials:'include',
    headers:{'X-CSRF-Token':token,'X-Requested-With':'XMLHttpRequest'},
    body:formData,
  });
  var d=await resp.json();
  if(d.success){ toast('Image uploaded!','success'); loadGallery(); }
  else toast(d.message||'Upload failed.','error');
  input.value='';
}

async function deleteGallery(id){
  if(!confirm('Delete this image?')) return;
  var d=await api('/api/admin/gallery/'+id,'DELETE',{});
  if(d.success){ loadGallery(); toast('Image deleted.','success'); }
  else toast(d.message||'Failed.','error');
}

/* ══════════════════════════════════════════════════════
   MESSAGES
══════════════════════════════════════════════════════ */
var msgCache={};

function msgStatusBadge(m){
  if(m.reply_message) return '<span class="badge badge-confirmed">Replied</span>';
  if(m.is_read)        return '<span class="badge badge-pending">Read</span>';
  return '<span class="badge badge-draft">New</span>';
}

async function loadMessages(){
  var all=document.getElementById('msgFilter').value==='all';
  var url=all?'/api/admin/messages?all=true&page='+msgPage:'/api/admin/messages';
  document.getElementById('msgBody').innerHTML='<tr class="loading-row"><td colspan="7"><div class="spin"></div></td></tr>';
  var d=await api(url);
  var rows=all?(d.data&&d.data.data?d.data.data:[]):d.data;
  if(!d.success||!rows||!rows.length){
    document.getElementById('msgBody').innerHTML='<tr><td colspan="7"><div class="empty"><div class="eicon">✉️</div>No messages found.</div></td></tr>';
    document.getElementById('msgPag').innerHTML=''; return;
  }
  msgCache={};
  document.getElementById('msgBody').innerHTML=rows.map(function(m){
    msgCache[m.id]=m;
    return '<tr style="'+(m.is_read?'':'font-weight:bold;background:#fffdf5;')+'">'
      +'<td>'+esc(m.name)+'</td>'
      +'<td>'+esc(m.email)+'</td>'
      +'<td>'+esc(m.subject||'—')+'</td>'
      +'<td style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">'+esc((m.message||'').substring(0,100))+'…</td>'
      +'<td>'+fmtDate(m.created_at)+'</td>'
      +'<td>'+msgStatusBadge(m)+'</td>'
      +'<td class="td-action">'
        +'<button class="btn btn-gold btn-sm" onclick="openReplyModal('+m.id+')">'+(m.reply_message?'View / Re-reply':'Reply')+'</button>'
        +(m.is_read?'':'<button class="btn btn-outline btn-sm" onclick="markMsgRead('+m.id+')">✓ Read</button>')
        +'<button class="btn btn-red btn-sm" onclick="deleteMsg('+m.id+')">Delete</button>'
      +'</td>'
    +'</tr>';
  }).join('');
  if(all&&d.data) renderPag('msgPag',d.data,function(p){ msgPage=p; loadMessages(); });
  else document.getElementById('msgPag').innerHTML='';
}

async function markMsgRead(id){
  var d=await api('/api/admin/messages/'+id+'/read','PATCH',{});
  if(d.success){ loadMessages(); loadDashboard(); toast('Marked as read.','success'); }
  else toast('Failed.','error');
}

async function deleteMsg(id){
  if(!confirm('Delete this message?')) return;
  var d=await api('/api/admin/messages/'+id,'DELETE',{});
  if(d.success){ loadMessages(); toast('Deleted.','success'); }
  else toast('Failed.','error');
}

function openReplyModal(id){
  var m=msgCache[id];
  if(!m) return;
  document.getElementById('mMsgReplyContent').innerHTML=
      detailRow('From', esc(m.name)+' &lt;'+esc(m.email)+'&gt;')
    + detailRow('Subject', esc(m.subject||'General Enquiry'))
    + detailRow('Received', fmtDate(m.created_at))
    + detailRow('Message', '<div style="white-space:pre-wrap;">'+esc(m.message)+'</div>')
    + (m.reply_message
        ? detailRow('Previous Reply', '<div style="white-space:pre-wrap;color:#1F3D36;">'+esc(m.reply_message)+'</div>'
            + '<div style="font-size:11px;color:#999;margin-top:4px;">Sent '+fmtDate(m.replied_at)+'</div>')
        : '');
  document.getElementById('mMsgReplyText').value='';
  document.getElementById('mMsgReplyError').textContent='';
  document.getElementById('mMsgReplyId').value=id;
  openModal('mMsgReply');
}

async function sendMsgReply(){
  var id=document.getElementById('mMsgReplyId').value;
  var text=document.getElementById('mMsgReplyText').value.trim();
  var errEl=document.getElementById('mMsgReplyError');
  errEl.textContent='';
  if(text.length<2){ errEl.textContent='Please write a reply before sending.'; return; }
  if(text.length>4000){ errEl.textContent='Reply must not exceed 4000 characters.'; return; }
  var btn=document.getElementById('mMsgReplyBtn');
  btn.disabled=true; btn.textContent='Sending…';
  try{
    var d=await api('/api/admin/messages/'+id+'/reply','POST',{reply:text});
    if(d.success){
      closeModal('mMsgReply');
      loadMessages(); loadDashboard();
      toast(d.message||'Reply sent.','success');
    } else {
      errEl.textContent = d.message || (d.errors && Object.values(d.errors)[0][0]) || 'Failed to send reply.';
    }
  } catch(e){
    errEl.textContent='Network error. Please try again.';
  } finally {
    btn.disabled=false; btn.textContent='Send Reply';
  }
}

/* ══════════════════════════════════════════════════════
   SUBSCRIBERS
══════════════════════════════════════════════════════ */
async function loadSubs(page){
  subPage=page||1;
  document.getElementById('subBody').innerHTML='<tr class="loading-row"><td colspan="6"><div class="spin"></div></td></tr>';
  var d=await api('/api/admin/subscribers?page='+subPage);
  if(!d.success||!d.data.data.length){
    document.getElementById('subBody').innerHTML='<tr><td colspan="6"><div class="empty"><div class="eicon">📧</div>No subscribers.</div></td></tr>';
    document.getElementById('subPag').innerHTML=''; return;
  }
  document.getElementById('subTotal').textContent='Total: '+d.data.total+' active subscribers';
  var offset=(subPage-1)*30;
  document.getElementById('subBody').innerHTML=d.data.data.map(function(s,i){
    return '<tr>'
      +'<td>'+(offset+i+1)+'</td>'
      +'<td>'+esc(s.email)+'</td>'
      +'<td>'+esc(s.name||'—')+'</td>'
      +'<td>'+fmtDate(s.subscribed_at)+'</td>'
      +'<td>'+(s.is_active?'<span class="badge badge-active">Active</span>':'<span class="badge badge-blocked">Unsubscribed</span>')+'</td>'
      +'<td><button class="btn btn-red btn-sm" onclick="deleteSub('+s.id+')">Remove</button></td>'
    +'</tr>';
  }).join('');
  renderPag('subPag',d.data,loadSubs);
}

async function deleteSub(id){
  if(!confirm('Remove this subscriber?')) return;
  var d=await api('/api/admin/subscribers/'+id,'DELETE',{});
  if(d.success){ loadSubs(subPage); toast('Subscriber removed.','success'); }
  else toast('Failed.','error');
}

/* ══════════════════════════════════════════════════════
   MODAL CLOSE ON OVERLAY
══════════════════════════════════════════════════════ */
document.querySelectorAll('.modal-bg').forEach(function(el){
  el.addEventListener('click',function(e){ if(e.target===this) this.classList.remove('open'); });
});
</script>
</body>
</html>
