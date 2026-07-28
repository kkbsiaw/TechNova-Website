<?php
session_start();
require "db.php";

// ---------- Auth guard: must be logged in ----------
if (!isset($_SESSION["uid"])) {
    header("Location: login.php");
    exit;
}
$uname   = $_SESSION["uname"];
$isAdmin = (($_SESSION["role"] ?? "user") === "admin");

$msg = "";
$msgClass = "ok";
$editRow = null;   // holds a record when we are updating

// Message shown when a non-admin is bounced from the admin panel
if (isset($_GET["denied"])) {
    $msg = "Access denied: the Admin Panel is for administrators only.";
    $msgClass = "err";
}

// ---------- Handle ADD / UPDATE (POST) ----------
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "save") {
    $id       = intval($_POST["id"] ?? 0);
    $client   = trim($_POST["client"] ?? "");
    $device   = trim($_POST["device"] ?? "");
    $issue    = trim($_POST["issue"] ?? "");
    $priority = $_POST["priority"] ?? "Medium";
    $status   = $_POST["status"] ?? "Open";

    if ($id > 0) {
        // UPDATE existing record
        $stmt = mysqli_prepare($conn,
            "UPDATE tickets SET client=?, device=?, issue=?, priority=?, status=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "sssssi", $client, $device, $issue, $priority, $status, $id);
        mysqli_stmt_execute($stmt);
        $msg = "Record #$id updated.";
    } else {
        // ADD new record
        $today = date("Y-m-d");
        $stmt = mysqli_prepare($conn,
            "INSERT INTO tickets (client, device, issue, priority, status, created) VALUES (?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $client, $device, $issue, $priority, $status, $today);
        mysqli_stmt_execute($stmt);
        $msg = "Record added successfully.";
    }
    mysqli_stmt_close($stmt);
}

// ---------- Handle DELETE (GET) ----------
if (($_GET["action"] ?? "") === "delete" && isset($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = mysqli_prepare($conn, "DELETE FROM tickets WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $msg = "Record #$id deleted.";
}

// ---------- Load a record for editing (GET) ----------
if (isset($_GET["edit"])) {
    $id = intval($_GET["edit"]);
    $stmt = mysqli_prepare($conn, "SELECT id, client, device, issue, priority, status FROM tickets WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $editRow = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
}

// ---------- RETRIEVE all records ----------
$tickets = mysqli_query($conn, "SELECT * FROM tickets ORDER BY id ASC");

function h($v) { return htmlspecialchars($v ?? ""); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechNova Solutions — Support Portal</title>
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/logo.svg" />
</head>
<body>
  <header>
    <div class="nav">
      <div class="brand"><img src="images/logo.svg" alt="TechNova logo" />Tech<span>Nova</span></div>
      <nav>
        <button class="hamburger" aria-label="Toggle navigation menu" aria-expanded="false" onclick="this.setAttribute('aria-expanded', document.getElementById('navMenu').classList.toggle('open'))">&#9776;</button>
        <ul id="navMenu">
          <li><a href="index.html">Home</a></li>
          <li><a href="about.html">About</a></li>
          <li><a href="contact.html">Contact</a></li>
          <?php if ($isAdmin): ?><li><a href="admin.php">Admin</a></li><?php endif; ?>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <h1>Support Ticket Database</h1>
    <p>Welcome, <?= h($uname) ?> — Add, Retrieve, Update &amp; Delete support records.</p>
  </section>

  <div class="container">
    <?php if ($msg): ?><div class="alert <?= $msgClass ?>"><?= h($msg) ?></div><?php endif; ?>

    <!-- ===== Add / Update form ===== -->
    <div class="card form-wrap" style="max-width:640px;">
      <h2 class="section-title"><?= $editRow ? "Update Ticket #" . h($editRow["id"]) : "Add New Ticket" ?></h2>
      <form method="post" action="dashboard.php">
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="id" value="<?= $editRow ? h($editRow["id"]) : "" ?>" />
        <div class="form-row">
          <div>
            <label for="t-client">Client Name</label>
            <input type="text" id="t-client" name="client" value="<?= $editRow ? h($editRow["client"]) : "" ?>" required />
          </div>
          <div>
            <label for="t-device">Device</label>
            <input type="text" id="t-device" name="device" placeholder="e.g. Dell Laptop" value="<?= $editRow ? h($editRow["device"]) : "" ?>" required />
          </div>
        </div>
        <label for="t-issue">Issue Description</label>
        <input type="text" id="t-issue" name="issue" value="<?= $editRow ? h($editRow["issue"]) : "" ?>" required />
        <div class="form-row">
          <div>
            <label for="t-priority">Priority</label>
            <select id="t-priority" name="priority">
              <?php foreach (["Low","Medium","High"] as $p): ?>
                <option <?= ($editRow && $editRow["priority"]===$p) ? "selected" : ($p==="Medium" && !$editRow ? "selected" : "") ?>><?= $p ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="t-status">Status</label>
            <select id="t-status" name="status">
              <?php foreach (["Open","In Progress","Resolved"] as $s): ?>
                <option <?= ($editRow && $editRow["status"]===$s) ? "selected" : ($s==="Open" && !$editRow ? "selected" : "") ?>><?= $s ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div style="margin-top:16px;">
          <button type="submit" class="btn"><?= $editRow ? "Save Changes" : "Add Record" ?></button>
          <?php if ($editRow): ?>
            <a href="dashboard.php" class="btn gray">Cancel</a>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- ===== Retrieve: records table ===== -->
    <h2 class="section-title">All Support Tickets</h2>
    <p class="section-sub">The database returns every record in the table below.</p>
    <?php if (mysqli_num_rows($tickets) === 0): ?>
      <p class="section-sub">No records yet — add one above.</p>
    <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Client</th><th>Device</th><th>Issue</th>
            <th>Priority</th><th>Status</th><th>Date</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($tickets)): ?>
          <tr>
            <td><?= h($row["id"]) ?></td>
            <td><?= h($row["client"]) ?></td>
            <td><?= h($row["device"]) ?></td>
            <td><?= h($row["issue"]) ?></td>
            <td><?= h($row["priority"]) ?></td>
            <td><?= h($row["status"]) ?></td>
            <td><?= h($row["created"]) ?></td>
            <td class="actions-cell">
              <a class="btn alt" href="dashboard.php?edit=<?= h($row["id"]) ?>">Update</a>
              <a class="btn danger" href="dashboard.php?action=delete&id=<?= h($row["id"]) ?>"
                 onclick="return confirm('Delete record #<?= h($row["id"]) ?>? This cannot be undone.');">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>

  <footer>&copy; 2026 TechNova Solutions. Built by Group of 7.</footer>
</body>
</html>
