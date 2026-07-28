<?php
session_start();
$isAdmin  = (($_SESSION["role"] ?? "") === "admin");
$loggedIn = isset($_SESSION["uid"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechNova Solutions — Home</title>
  <link rel="stylesheet" href="css/style.css?v=4" />
  <link rel="icon" href="images/logo.svg" />
</head>
<body>
  <!-- ===== Header / Navigation (Req 1) ===== -->
  <header>
    <div class="nav">
      <div class="brand">
        <img src="images/logo.svg" alt="TechNova logo" />
        Tech<span>Nova</span>
      </div>
      <nav>
        <button class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false" onclick="this.setAttribute('aria-expanded', document.getElementById('navMenu').classList.toggle('open'))">&#9776;</button>
        <ul id="navMenu">
          <li><a href="index.php" class="active">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
          <?php if ($loggedIn): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <?php if ($isAdmin): ?><li><a href="admin.php">Admin</a></li><?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </header>

  <!-- ===== Scrolling text (Req 5) ===== -->
  <div class="ticker">
    <span>🚀 Welcome to TechNova Solutions — Your trusted IT partner &nbsp; • &nbsp; 24/7 Support Desk &nbsp; • &nbsp; Cloud • Cybersecurity • Networking &nbsp; • &nbsp; Log in to open a support ticket today!</span>
  </div>

  <!-- ===== Hero ===== -->
  <section class="hero">
    <h1>TechNova Solutions</h1>
    <p>Delivering smart, reliable and secure IT services to businesses across the region.</p>
  </section>

  <div class="container">
    <!-- ===== Image swap / slideshow, max 5 images (Req 6) ===== -->
    <h2 class="section-title">What We Do</h2>
    <p class="section-sub">Auto-rotating showcase (click the dots to swap images manually).</p>
    <div class="slideshow">
      <img src="images/slide1.svg?v=3" class="show" alt="Innovating IT" />
      <img src="images/slide2.svg?v=3" alt="24/7 Support" />
      <img src="images/slide3.svg?v=3" alt="Cloud Solutions" />
      <img src="images/slide4.svg?v=3" alt="Cybersecurity" />
      <img src="images/slide5.svg?v=3" alt="Trusted Partner" />
    </div>
    <div class="slide-controls">
      <span class="dot active" onclick="currentSlide(0)"></span>
      <span class="dot" onclick="currentSlide(1)"></span>
      <span class="dot" onclick="currentSlide(2)"></span>
      <span class="dot" onclick="currentSlide(3)"></span>
      <span class="dot" onclick="currentSlide(4)"></span>
    </div>

    <!-- ===== Our Services (icon cards) ===== -->
    <h2 class="section-title" style="margin-top:36px;">Our Services</h2>
    <p class="section-sub">Everything your business needs to run smarter and safer.</p>
    <div class="cols">
      <div class="card" style="text-align:center;">
        <div class="svc-icon"><svg viewBox="0 0 24 24"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="2" y="14" width="4" height="7" rx="1"/><rect x="18" y="14" width="4" height="7" rx="1"/><path d="M20 21a4 4 0 0 1-4 3h-3"/></svg></div>
        <h3>IT Support &amp; Helpdesk</h3>
        <p class="section-sub" style="margin:6px 0 0;">24/7 remote and on-site technical support for hardware and software.</p>
      </div>
      <div class="card" style="text-align:center;">
        <div class="svc-icon"><svg viewBox="0 0 24 24"><path d="M17 18a4 4 0 0 0 0-8 6 6 0 0 0-11.3-1.5A4.5 4.5 0 0 0 6 18z"/><path d="M12 21v-6M9.5 17.5 12 15l2.5 2.5"/></svg></div>
        <h3>Cloud Solutions</h3>
        <p class="section-sub" style="margin:6px 0 0;">Migration, hosting, and secure automated backups in the cloud.</p>
      </div>
      <div class="card" style="text-align:center;">
        <div class="svc-icon"><svg viewBox="0 0 24 24"><path d="M12 2l8 3v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V5z"/><path d="M9 12l2 2 4-4"/></svg></div>
        <h3>Cybersecurity</h3>
        <p class="section-sub" style="margin:6px 0 0;">Threat monitoring, firewalls, and staff security training.</p>
      </div>
      <div class="card" style="text-align:center;">
        <div class="svc-icon"><svg viewBox="0 0 24 24"><circle cx="12" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><path d="M12 7.5v4M11 12l-4.5 5M13 12l4.5 5"/></svg></div>
        <h3>Networking</h3>
        <p class="section-sub" style="margin:6px 0 0;">Design and maintenance of secure office networks and Wi-Fi.</p>
      </div>
      <div class="card" style="text-align:center;">
        <div class="svc-icon"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v6c0 1.7 3.6 3 8 3s8-1.3 8-3V5"/><path d="M4 11v6c0 1.7 3.6 3 8 3s8-1.3 8-3v-6"/></svg></div>
        <h3>Data Backup</h3>
        <p class="section-sub" style="margin:6px 0 0;">Reliable, encrypted backups so you never lose critical data.</p>
      </div>
      <div class="card" style="text-align:center;">
        <div class="svc-icon"><svg viewBox="0 0 24 24"><path d="M8 9l-4 3 4 3"/><path d="M16 9l4 3-4 3"/><path d="M13 5l-2 14"/></svg></div>
        <h3>Web Development</h3>
        <p class="section-sub" style="margin:6px 0 0;">Modern, responsive websites and web apps for your business.</p>
      </div>
    </div>

    <!-- ===== Pop-up demos (Req 4: three pop-ups) ===== -->
    <div class="card" style="text-align:center;">
      <h2 class="section-title">Interactive Pop-ups</h2>
      <p class="section-sub">Demonstration of three JavaScript pop-ups.</p>
      <button class="btn" onclick="alert('ℹ️ Pop-up 1 (alert): TechNova has served 500+ clients since 2015!')">Pop-up 1: Alert</button>
      <button class="btn alt" onclick="openModal('infoModal')">Pop-up 2: Modal</button>
      <button class="btn gray" onclick="confirmPopup()">Pop-up 3: Confirm</button>
      <p class="section-sub" style="margin-top:14px;">A welcome <b>alert</b> pop-up also fires automatically when the page loads.</p>
    </div>

    <!-- ===== Team profiles: 13 members (Req 1) ===== -->
    <h2 class="section-title">Meet Our Team</h2>
    <p class="section-sub">Group members — Picture, Full Name and ID.</p>
    <div class="grid">
      <div class="member"><img src="images/fsiaw.jpg" alt="Francis K. Amponsah-Siaw" /><h3>Francis K. Amponsah-Siaw</h3><div class="id">MS/ITE/25/0001</div></div>
      <div class="member"><img src="images/Peter Effah Otoo.jpg" alt="Peter Effah Otoo" /><h3>Peter Effah Otoo</h3><div class="id">MS/ITE/25/0009</div></div>
      <div class="member"><img src="images/Yaw Amponsah Kankam.jpg" alt="Yaw Amponsah Kankam" /><h3>Yaw Amponsah Kankam</h3><div class="id">MS/ITE/25/0015</div></div>
      <div class="member"><img src="images/BRIGHT SAAH.jpg" alt="Bright Saah" /><h3>Bright Saah</h3><div class="id">MS/ITE/25/0024</div></div>
      <div class="member"><img src="images/Fredrick Barnaby Dodoo.jpg" alt="Fredrick Barnaby Dodoo" /><h3>Fredrick Barnaby Dodoo</h3><div class="id">MS/ITE/25/0027</div></div>
      <div class="member"><img src="images/EBENEZER DUKU-AFFUL.jpg" alt="Duke-Afful Ebenezer" /><h3>Duke-Afful Ebenezer</h3><div class="id">MS/ITE/25/0028</div></div>
      <div class="member"><img src="images/Freeman Darko.jpg" alt="Freeman Darko" /><h3>Freeman Darko</h3><div class="id">MS/ITE/25/0040</div></div>
    </div>
  </div>

  <!-- ===== Modal (Pop-up 2) ===== -->
  <div class="modal-overlay" id="infoModal">
    <div class="modal">
      <h3>About TechNova</h3>
      <p>We are a team of 7 IT specialists providing support, cloud, networking and security services. Register and log in to open a support ticket!</p>
      <div class="modal-actions">
        <button class="btn" onclick="closeModal('infoModal')">Got it</button>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2026 TechNova Solutions. Built by Group of 7. &nbsp;|&nbsp;
    <a href="contact.php">Contact us</a>
  </footer>

  <script src="js/script.js?v=3"></script>
</body>
</html>
