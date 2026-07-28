<?php
session_start();
require "db.php";

$msg = "";
$msgClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim(strtolower($_POST["email"] ?? ""));
    $pass  = $_POST["pass"] ?? "";

    $stmt = mysqli_prepare($conn, "SELECT id, name, password, role, status FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid, $uname, $uhash, $urole, $ustatus);

    if (mysqli_stmt_fetch($stmt) && password_verify($pass, $uhash)) {
        if ($ustatus !== "active") {
            // Correct credentials, but the account has not been approved yet
            $msg = "Your account is awaiting administrator approval. Please try again later.";
            $msgClass = "err";
        } else {
            $_SESSION["uid"]   = $uid;
            $_SESSION["uname"] = $uname;
            $_SESSION["role"]  = $urole;
            header("Location: dashboard.php");
            exit;
        }
    } else {
        $msg = "Invalid email or password."; $msgClass = "err";
    }
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechNova Solutions — Login</title>
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
          <li><a href="contact.php">Contact</a></li>
          <li><a href="login.php" class="active">Login</a></li>
          <li><a href="register.php">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <div class="card form-wrap">
      <h2 class="section-title" style="text-align:center;">Login to Support Portal</h2>
      <p class="section-sub" style="text-align:center;">Access the ticket database.</p>
      <?php if ($msg): ?><div class="alert <?= $msgClass ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <form method="post" action="login.php">
        <label for="log-email">Email</label>
        <input type="email" id="log-email" name="email" required />
        <label for="log-pass">Password</label>
        <input type="password" id="log-pass" name="pass" required />
        <button type="submit" class="btn" style="width:100%; margin-top:16px;">Login</button>
      </form>
      <p class="note">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>

  <footer>&copy; 2026 TechNova Solutions. Built by Group of 7.</footer>
</body>
</html>
