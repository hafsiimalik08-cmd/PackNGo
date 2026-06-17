/**
 * PackNGo — Frontend API Client  v2.0
 * =====================================================================
 * Add ONE script tag at the BOTTOM of every HTML page (before </body>):
 *
 *   <script src="api-client.js"></script>
 *
 * This file hooks into the EXISTING frontend functions by overriding
 * them after the page loads. Zero HTML/CSS changes needed.
 *
 * Field IDs are taken directly from the existing HTML:
 *   firstName, lastName, bookEmail, phoneCode, bookPhone,
 *   bookDest, bookPackage, departDate, returnDate,
 *   travellers, budget, accommodation, bookMsg
 * =====================================================================
 */

(function () {
  "use strict";

  /* ── 1. Configuration ──────────────────────────────────────────── */
  // Works whether the site is at domain root OR in a sub-folder like /PackNGo/
  var scriptDir = (function () {
    var scripts = document.getElementsByTagName("script");
    var src = scripts[scripts.length - 1].src;
    return src.substring(0, src.lastIndexOf("/") + 1);
  })();

  var API_BASE = scriptDir; // e.g. http://localhost/PackNGo/

  /* ── 2. CSRF token ─────────────────────────────────────────────── */
  var _csrf = null;

  function getCsrf() {
    return new Promise(function (resolve) {
      if (_csrf) { resolve(_csrf); return; }
      fetch(API_BASE + "api/auth/csrf", { credentials: "include" })
        .then(function (r) { return r.json(); })
        .then(function (d) { _csrf = d.csrf_token || ""; resolve(_csrf); })
        .catch(function () { _csrf = ""; resolve(""); });
    });
  }

  /* ── 3. Core fetch helper ─────────────────────────────────────── */
  function api(path, method, body) {
    method = (method || "GET").toUpperCase();
    return getCsrf().then(function (token) {
      var opts = {
        method: method,
        credentials: "include",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
      };
      if (token && method !== "GET") {
        opts.headers["X-CSRF-Token"] = token;
      }
      if (body) {
        if (body && typeof body === "object") body._csrf = token;
        opts.body = JSON.stringify(body);
      }
      // path starts with /api/... so strip leading slash and append to BASE
      var cleanPath = path.replace(/^\//, '');
      var url = API_BASE + cleanPath;
      return fetch(url, opts).then(function (resp) {
        if (method !== "GET") _csrf = null; // rotate
        return resp.text().then(function (text) {
          try { return { status: resp.status, data: JSON.parse(text) }; }
          catch (e) { return { status: resp.status, data: { success: false, message: text } }; }
        });
      });
    });
  }

  /* ── 4. Toast notification ─────────────────────────────────────── */
  function toast(msg, type) {
    var colors = {
      success: { bg: "#1F3D36", fg: "#D4AF37" },
      error:   { bg: "#8B0000", fg: "#fff" },
      info:    { bg: "#1F3D36", fg: "#F5EFE6" },
    };
    var c = colors[type] || colors.info;
    var el = document.createElement("div");
    el.textContent = msg;
    Object.assign(el.style, {
      position: "fixed", bottom: "90px", right: "20px", zIndex: "99999",
      background: c.bg, color: c.fg,
      padding: "14px 22px", maxWidth: "320px",
      fontFamily: "'Times New Roman', Georgia, serif", fontSize: "14px",
      lineHeight: "1.5", borderRadius: "4px",
      boxShadow: "0 4px 20px rgba(0,0,0,.25)",
      opacity: "0", transition: "opacity .35s",
    });
    document.body.appendChild(el);
    setTimeout(function () { el.style.opacity = "1"; }, 20);
    setTimeout(function () {
      el.style.opacity = "0";
      setTimeout(function () { el.parentNode && el.parentNode.removeChild(el); }, 400);
    }, 4800);
  }

  /* ── 5. Helper: get value safely ──────────────────────────────── */
  function val(id) {
    var el = document.getElementById(id);
    return el ? (el.value || "").trim() : "";
  }

  /* ═══════════════════════════════════════════════════════════════
     6. OVERRIDE  handleBooking()  — used by Reservation.html
        The original function is defined inline; we replace it after
        the page loads so it runs our API call instead.
  ═══════════════════════════════════════════════════════════════ */
  window.handleBooking = function (e) {
    e.preventDefault();

    /* --- preserve existing frontend validations --- */
    var destVal = val("bookDest");
    if (typeof window.isValidDest === "function" && !window.isValidDest(destVal)) {
      var de = document.getElementById("destError");
      if (de) de.classList.add("visible");
      var bd = document.getElementById("bookDest");
      if (bd) bd.focus();
      return;
    }
    var captchaCheck = document.getElementById("captchaCheck");
    if (captchaCheck && !captchaCheck.checked) {
      var cerr = document.getElementById("captchaError");
      if (cerr) { cerr.textContent = "Please confirm you are not a robot."; cerr.classList.add("visible"); }
      return;
    }
    if (typeof window.captchaVerified !== "undefined" && !window.captchaVerified) {
      var cerr2 = document.getElementById("captchaError");
      if (cerr2) { cerr2.textContent = "Please complete the security verification."; cerr2.classList.add("visible"); }
      var cai = document.getElementById("captchaAnswerInput");
      if (cai) cai.focus();
      return;
    }

    /* --- gather payload using exact IDs from Reservation.html --- */
    var payload = {
      first_name:     val("firstName"),
      last_name:      val("lastName"),
      email:          val("bookEmail"),
      phone_code:     val("phoneCode") || "+92",
      phone:          val("bookPhone"),
      destination:    destVal,
      package:        val("bookPackage"),
      departure_date: val("departDate"),
      return_date:    val("returnDate"),
      travellers:     val("travellers"),
      budget:         val("budget"),
      accommodation:  val("accommodation"),
      message:        val("bookMsg"),
    };

    /* --- check email verification --- */
    var emailVerified = document.getElementById("emailVerified");
    if (emailVerified && emailVerified.value !== "1") {
      toast("Please verify your email address before submitting the reservation.", "error");
      var bookEmail = document.getElementById("bookEmail");
      if (bookEmail) bookEmail.focus();
      return;
    }

    /* --- basic required check --- */
    if (!payload.first_name || !payload.last_name || !payload.email || !payload.destination || !payload.departure_date || !payload.travellers) {
      toast("Please fill in all required fields.", "error");
      return;
    }

    /* --- disable submit button & show loading --- */
    var btn = document.querySelector("#bookingForm .gold-btn[type='submit'], #bookingForm button[type='submit']");
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin" style="margin-right:8px;"></i>Sending…'; }

    api("/api/reservation/submit", "POST", payload)
      .then(function (result) {
        var data = result.data;
        if (data.success) {
          /* Show existing success UI exactly as original */
          var form = document.getElementById("bookingForm");
          var success = document.getElementById("successMsg");
          if (form) form.style.display = "none";
          if (success) {
            success.style.display = "block";
            /* Inject booking reference into existing h3 */
            var h3 = success.querySelector("h3");
            if (h3) h3.textContent = "Reservation Confirmed!";
            var p = success.querySelector("p");
            if (p) p.insertAdjacentHTML("beforeend",
              '<br><br><strong style="color:#D4AF37;">Your Booking Reference: ' + data.booking_ref + '</strong>');
          }
          var formBox = document.querySelector(".form-box");
          if (formBox) window.scrollTo({ top: formBox.offsetTop - 100, behavior: "smooth" });
          toast("Booking received! Ref: " + data.booking_ref, "success");
        } else {
          var errMsg = data.message || "";
          if (data.errors) {
            errMsg = Object.values(data.errors).map(function (a) { return a[0]; }).join(" | ");
          }
          toast(errMsg || "Submission failed. Please try again.", "error");
          if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i>Confirm My Reservation'; }
        }
      })
      .catch(function () {
        toast("Network error. Please check your connection and try again.", "error");
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i>Confirm My Reservation'; }
      });
  };

  /* ═══════════════════════════════════════════════════════════════
     7. OVERRIDE  subscribeNewsletter()  — used by Blogs.html
        Original: just shows alert and clears field.
        New: calls real API then falls back to original UI behaviour.
  ═══════════════════════════════════════════════════════════════ */
  window.subscribeNewsletter = function () {
    var emailEl   = document.getElementById("newsletterEmail");
    var counterEl = document.getElementById("emailCounter");
    var errorEl   = document.getElementById("emailError");
    if (!emailEl) return;

    var email = emailEl.value.trim();
    if (!email) { toast("Please enter your email address.", "error"); return; }
    if (email.length > 120) { toast("Email address is too long.", "error"); return; }

    /* Validate format */
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(email)) { toast("Please enter a valid email address.", "error"); return; }

    api("/api/newsletter/subscribe", "POST", { email: email })
      .then(function (result) {
        var data = result.data;
        if (data.success) {
          emailEl.value = "";
          if (counterEl) counterEl.textContent = "0 / 30";
          if (errorEl)   errorEl.classList.remove("visible");
          toast("Thank you for subscribing! You'll receive our latest travel stories and exclusive offers.", "success");
        } else {
          toast(data.message || "Could not subscribe. Please try again.", "error");
        }
      })
      .catch(function () {
        toast("Network error. Please try again.", "error");
      });
  };

  /* ═══════════════════════════════════════════════════════════════
     8. Destination search enhancement — index.html
        Augments the existing static destinations array with DB results.
  ═══════════════════════════════════════════════════════════════ */
  document.addEventListener("DOMContentLoaded", function () {
    var searchInput = document.getElementById("searchInput");
    if (!searchInput) return;

    var debounce;
    searchInput.addEventListener("input", function () {
      clearTimeout(debounce);
      var q = this.value.trim();
      if (q.length < 2) return;
      debounce = setTimeout(function () {
        api("/api/destinations/search?q=" + encodeURIComponent(q))
          .then(function (r) {
            if (!r.data.success || !Array.isArray(r.data.data)) return;
            r.data.data.forEach(function (d) {
              if (!window.destinations) return;
              var exists = window.destinations.some(function (e) {
                return e.name && e.name.toLowerCase() === d.name.toLowerCase();
              });
              if (!exists) {
                window.destinations.push({ name: d.name, desc: d.tagline || "", cat: d.category || "beach" });
              }
            });
            if (typeof window.filterResults === "function") window.filterResults();
          })
          .catch(function () { /* silent — static search still works */ });
      }, 450);
    });
  });

  /* ── Expose public API for custom use ─────────────────────────── */
  window.PackNGoAPI = {
    submitReservation: function (data) { return api("/api/reservation/submit", "POST", data); },
    checkBooking:      function (ref)  { return api("/api/reservation/check?ref=" + encodeURIComponent(ref)); },
    getDestinations:   function (cat)  { return api("/api/destinations" + (cat ? "?cat=" + encodeURIComponent(cat) : "")); },
    searchDest:        function (q)    { return api("/api/destinations/search?q=" + encodeURIComponent(q)); },
    getPackages:       function ()     { return api("/api/packages"); },
    subscribe:         function (email, name) { return api("/api/newsletter/subscribe", "POST", { email: email, name: name || "" }); },
    login:             function (email, pw)   { return api("/api/auth/login",    "POST", { email: email, password: pw }); },
    register:          function (data)        { return api("/api/auth/register", "POST", data); },
    logout:            function ()            { return api("/api/auth/logout",   "POST", {}); },
    forgotPassword:    function (email)       { return api("/api/auth/forgot-password", "POST", { email: email }); },
  };

})();
