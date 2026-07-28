<?php
session_start();
require "db.php";

$msg = "";
$msgClass = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name   = trim($_POST["name"]  ?? "");
    $email  = trim(strtolower($_POST["email"] ?? ""));
    $pass   = $_POST["pass"]  ?? "";
    $pass2  = $_POST["pass2"] ?? "";

    if ($name === "" || $email === "" || $pass === "") {
        $msg = "Please fill in all fields."; $msgClass = "err";
    } elseif ($pass !== $pass2) {
        $msg = "Passwords do not match."; $msgClass = "err";
    } else {
        // Is the email already registered?
        $check = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($check, "s", $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $msg = "An account with this email already exists."; $msgClass = "err";
        } else {
            // Store the password securely (hashed)
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hash);
            if (mysqli_stmt_execute($stmt)) {
                $msg = "Account created! Your account is pending administrator approval. "
                     . "You'll be able to log in once an admin activates it.";
                $msgClass = "ok";
            } else {
                $msg = "Something went wrong. Please try again."; $msgClass = "err";
            }
        }
        mysqli_stmt_close($check);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechNova Solutions — Register</title>
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
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php" class="active">Register</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <div class="card form-wrap">
      <h2 class="section-title" style="text-align:center;">Create an Account</h2>
      <p class="section-sub" style="text-align:center;">Register to open support tickets.</p>
      <?php if ($msg): ?><div class="alert <?= $msgClass ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
      <form method="post" action="register.php">
        <label for="reg-name">Full Name</label>
        <input type="text" id="reg-name" name="name" required />
        <label for="reg-email">Email</label>
        <input type="email" id="reg-email" name="email" required />
        <div class="form-row">
          <div>
            <label for="reg-pass">Password</label>
            <input type="password" id="reg-pass" name="pass" required />
          </div>
          <div>
            <label for="reg-pass2">Confirm Password</label>
            <input type="password" id="reg-pass2" name="pass2" required />
          </div>
        </div>
        <button type="submit" class="btn" style="width:100%; margin-top:16px;">Register</button>
      </form>
      <p class="note">Already registered? <a href="login.php">Login here</a></p>
    </div>
  </div>

  <footer>&copy; 2026 TechNova Solutions. Built by Group of 7.</footer>
</body>
</html>
