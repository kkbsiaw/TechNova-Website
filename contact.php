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
  <title>TechNova Solutions — Contact</title>
  <link rel="stylesheet" href="css/style.css?v=4" />
  <link rel="icon" href="images/logo.svg" />
</head>
<body>
  <header>
    <div class="nav">
      <div class="brand"><img src="images/logo.svg" alt="TechNova logo" />Tech<span>Nova</span></div>
      <nav>
        <button class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false" onclick="this.setAttribute('aria-expanded', document.getElementById('navMenu').classList.toggle('open'))">&#9776;</button>
        <ul id="navMenu">
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="contact.php" class="active">Contact</a></li>
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

  <section class="hero">
    <h1>Contact Us</h1>
    <p>We'd love to hear from you. Reach out any time.</p>
  </section>

  <div class="container">
    <div class="cols">
      <div class="card">
        <h2 class="section-title">Get In Touch</h2>
        <p>📍 <b>Address:</b> MSC IT Group 7, Cape Coast, Ghana</p><br>
        <p>📞 <b>Phone:</b> +233 20 000 0000</p><br>
        <p>✉️ <b>Email:</b> support@technova.example</p><br>
        <p>🕒 <b>Helpdesk:</b> Open 24 hours, 7 days a week</p>
      </div>

      <div class="card">
        <h2 class="section-title">Send a Message</h2>
        <div id="c-msg"></div>
        <form onsubmit="return sendMessage(event)">
          <label for="c-name">Full Name</label>
          <input type="text" id="c-name" required />
          <label for="c-email">Email</label>
          <input type="email" id="c-email" required />
          <label for="c-body">Message</label>
          <textarea id="c-body" rows="4" required></textarea>
          <br />
          <button type="submit" class="btn" style="margin-top:14px;">Send Message</button>
        </form>
      </div>
    </div>
  </div>

  <footer>&copy; 2026 TechNova Solutions. Built by Group of 7. &nbsp;|&nbsp; <a href="index.php">Home</a></footer>

  <script src="js/script.js?v=3"></script>
  <script>
    // Simple front-end demo handler (no server required)
    function sendMessage(e) {
      e.preventDefault();
      var box = document.getElementById("c-msg");
      box.className = "alert ok";
      box.textContent = "✅ Thanks! Your message has been received. We'll reply shortly.";
      e.target.reset();
      return false;
    }
  </script>
</body>
</html>
