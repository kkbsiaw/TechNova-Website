<?php
session_start();
require "db.php";

// ---------- Guard: must be logged in AND an admin ----------
if (!isset($_SESSION["uid"])) {
    header("Location: login.php");
    exit;
}
if (($_SESSION["role"] ?? "user") !== "admin") {
    // Logged in but not privileged -> bounce to the normal dashboard
    header("Location: dashboard.php?denied=1");
    exit;
}

$me   = intval($_SESSION["uid"]);
$msg  = "";
$mcls = "ok";

// ---------- Handle privilege actions ----------
$action = $_GET["action"] ?? "";
$tid    = intval($_GET["id"] ?? 0);

if ($action && $tid > 0) {
    if ($action === "promote") {
        $stmt = mysqli_prepare($conn, "UPDATE users SET role='admin' WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $tid);
        mysqli_stmt_execute($stmt);
        $msg = "User #$tid promoted to admin.";
    } elseif ($action === "demote") {
        if ($tid === $me) { $msg = "You cannot remove your own admin rights."; $mcls = "err"; }
        else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET role='user' WHERE id=?");
            mysqli_stmt_bind_param($stmt, "i", $tid);
            mysqli_stmt_execute($stmt);
            $msg = "Admin rights revoked for user #$tid.";
        }
    } elseif ($action === "deluser") {
        if ($tid === $me) { $msg = "You cannot delete your own account."; $mcls = "err"; }
        else {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id=?");
            mysqli_stmt_bind_param($stmt, "i", $tid);
            mysqli_stmt_execute($stmt);
            $msg = "User #$tid deleted.";
        }
    } elseif ($action === "activate") {
        $stmt = mysqli_prepare($conn, "UPDATE users SET status='active' WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $tid);
        mysqli_stmt_execute($stmt);
        $msg = "Account #$tid activated — the user can now log in.";
    } elseif ($action === "deactivate") {
        if ($tid === $me) { $msg = "You cannot deactivate your own account."; $mcls = "err"; }
        else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET status='pending' WHERE id=?");
            mysqli_stmt_bind_param($stmt, "i", $tid);
            mysqli_stmt_execute($stmt);
            $msg = "Account #$tid deactivated — the user can no longer log in.";
        }
    }
}

// ---------- Stats ----------
function scalar($conn, $sql) {
    $r = mysqli_query($conn, $sql);
    $row = mysqli_fetch_row($r);
    return $row[0];
}
$totalUsers  = scalar($conn, "SELECT COUNT(*) FROM users");
$pendingUsers= scalar($conn, "SELECT COUNT(*) FROM users WHERE status='pending'");
$totalAdmins = scalar($conn, "SELECT COUNT(*) FROM users WHERE role='admin'");
$totalTix    = scalar($conn, "SELECT COUNT(*) FROM tickets");

// ---------- All users ----------
$users = mysqli_query($conn, "SELECT id, name, email, role, status, created FROM users ORDER BY id ASC");

function h($v) { return htmlspecialchars($v ?? ""); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechNova Solutions — Admin Panel</title>
  <link rel="stylesheet" href="css/style.css?v=2" />
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
          <li><a href="dashboard.php">Dashboard</a></li>
          <li><a href="admin.php" class="active">Admin</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <h1>Admin Panel</h1>
    <p>Signed in as <?= h($_SESSION["uname"]) ?> (Administrator) — manage users and privileges.</p>
  </section>

  <div class="container">
    <?php if ($msg): ?><div class="alert <?= $mcls ?>"><?= h($msg) ?></div><?php endif; ?>

    <!-- ===== Stat cards ===== -->
    <div class="cols">
      <div class="card" style="text-align:center;">
        <h2 class="section-title" style="font-size:2.2rem;"><?= h($totalUsers) ?></h2>
        <p class="section-sub" style="margin:0;">Total Users</p>
      </div>
      <div class="card" style="text-align:center;">
        <h2 class="section-title" style="font-size:2.2rem; color:<?= $pendingUsers>0 ? '#d97706' : 'var(--dark)' ?>;"><?= h($pendingUsers) ?></h2>
        <p class="section-sub" style="margin:0;">Pending Approval</p>
      </div>
      <div class="card" style="text-align:center;">
        <h2 class="section-title" style="font-size:2.2rem;"><?= h($totalAdmins) ?></h2>
        <p class="section-sub" style="margin:0;">Administrators</p>
      </div>
      <div class="card" style="text-align:center;">
        <h2 class="section-title" style="font-size:2.2rem;"><?= h($totalTix) ?></h2>
        <p class="section-sub" style="margin:0;">Total Tickets</p>
      </div>
    </div>

    <!-- ===== User management ===== -->
    <h2 class="section-title" style="margin-top:26px;">Manage Users</h2>
    <p class="section-sub">Grant or revoke administrator privileges, or remove accounts.</p>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Registered</th><th>Actions</th></tr>
        </thead>
        <tbody>
          <?php while ($u = mysqli_fetch_assoc($users)): ?>
          <tr>
            <td><?= h($u["id"]) ?></td>
            <td><?= h($u["name"]) ?><?= ($u["id"]==$me) ? ' <small style="color:var(--brand);">(you)</small>' : '' ?></td>
            <td><?= h($u["email"]) ?></td>
            <td>
              <?php if ($u["role"]==="admin"): ?>
                <span style="color:#16a34a;font-weight:700;">Admin</span>
              <?php else: ?>
                <span style="color:var(--muted);">User</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($u["status"]==="active"): ?>
                <span style="color:#16a34a;font-weight:700;">Active</span>
              <?php else: ?>
                <span style="color:#d97706;font-weight:700;">Pending</span>
              <?php endif; ?>
            </td>
            <td><?= h($u["created"]) ?></td>
            <td class="actions-cell">
              <?php if ($u["status"]!=="active"): ?>
                <a class="btn" href="admin.php?action=activate&id=<?= h($u["id"]) ?>"
                   onclick="return confirm('Activate <?= h($u["name"]) ?>\'s account?');">Activate</a>
              <?php elseif ($u["id"] != $me): ?>
                <a class="btn gray" href="admin.php?action=deactivate&id=<?= h($u["id"]) ?>"
                   onclick="return confirm('Deactivate <?= h($u["name"]) ?>\'s account? They will be unable to log in.');">Deactivate</a>
              <?php endif; ?>
              <?php if ($u["role"]==="admin"): ?>
                <?php if ($u["id"] != $me): ?>
                  <a class="btn gray" href="admin.php?action=demote&id=<?= h($u["id"]) ?>"
                     onclick="return confirm('Revoke admin rights for <?= h($u["name"]) ?>?');">Revoke Admin</a>
                <?php else: ?>
                  <span class="section-sub" style="margin:0;">&mdash;</span>
                <?php endif; ?>
              <?php else: ?>
                <a class="btn alt" href="admin.php?action=promote&id=<?= h($u["id"]) ?>"
                   onclick="return confirm('Make <?= h($u["name"]) ?> an admin?');">Make Admin</a>
              <?php endif; ?>
              <?php if ($u["id"] != $me): ?>
                <a class="btn danger" href="admin.php?action=deluser&id=<?= h($u["id"]) ?>"
                   onclick="return confirm('Delete <?= h($u["name"]) ?>? This cannot be undone.');">Delete</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <p style="margin-top:20px;"><a href="dashboard.php" class="btn">← Back to Support Dashboard</a></p>
  </div>

  <footer>&copy; 2026 TechNova Solutions. Built by Group of 7.</footer>
</body>
</html>
