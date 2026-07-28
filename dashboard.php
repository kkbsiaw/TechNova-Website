<?php
session_start();
require "db.php";

// ---------- Auth guard: must be logged in ----------
if (!isset($_SESSION["uid"])) {
    header("Location: login.php");
    exit;
}
$uname   = $_SESSION["uname"];
$uid     = intval($_SESSION["uid"]);
$isAdmin = (($_SESSION["role"] ?? "user") === "admin");

$msg = "";
$msgClass = "ok";
$editRow  = null;   // holds a record when we are updating
$viewMode = false;  // true when a user is viewing a read-only ticket

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
        // UPDATE — admins may edit any ticket (incl. status); users only their
        // own, and users cannot change the status (it is preserved).
        if ($isAdmin) {
            $stmt = mysqli_prepare($conn,
                "UPDATE tickets SET client=?, device=?, issue=?, priority=?, status=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "sssssi", $client, $device, $issue, $priority, $status, $id);
        } else {
            // Users may edit their own ticket ONLY while it is still "Open"
            $stmt = mysqli_prepare($conn,
                "UPDATE tickets SET client=?, device=?, issue=?, priority=? WHERE id=? AND user_id=? AND status='Open'");
            mysqli_stmt_bind_param($stmt, "ssssii", $client, $device, $issue, $priority, $id, $uid);
        }
        mysqli_stmt_execute($stmt);
        if (mysqli_stmt_affected_rows($stmt) > 0) {
            $msg = "Record #$id updated.";
        } else {
            $msg = $isAdmin
                ? "Update failed."
                : "You can only edit your own tickets while they are still Open.";
            $msgClass = "err";
        }
    } else {
        // ADD — the new ticket is owned by the current user.
        // Users' new tickets are always "Open"; only admins may set the status.
        $today = date("Y-m-d");
        $newStatus = $isAdmin ? $status : "Open";
        $stmt = mysqli_prepare($conn,
            "INSERT INTO tickets (user_id, client, device, issue, priority, status, created) VALUES (?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "issssss", $uid, $client, $device, $issue, $priority, $newStatus, $today);
        mysqli_stmt_execute($stmt);
        $id = mysqli_insert_id($conn);
        $msg = "Record added successfully.";
    }
    mysqli_stmt_close($stmt);

    // ---------- Admin comment on the ticket (optional) ----------
    $comment = trim($_POST["comment"] ?? "");
    if ($isAdmin && $comment !== "" && $id > 0) {
        $cstmt = mysqli_prepare($conn,
            "INSERT INTO ticket_comments (ticket_id, author_id, author_name, comment) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($cstmt, "iiss", $id, $uid, $uname, $comment);
        mysqli_stmt_execute($cstmt);
        mysqli_stmt_close($cstmt);
        $msg = trim($msg . " Comment added.");
    }
}

// ---------- Handle DELETE (GET) — administrators only ----------
if (($_GET["action"] ?? "") === "delete" && isset($_GET["id"])) {
    if (!$isAdmin) {
        $msg = "Only administrators can delete tickets."; $msgClass = "err";
    } else {
        $id = intval($_GET["id"]);
        $stmt = mysqli_prepare($conn, "DELETE FROM tickets WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $msg = "Record #$id deleted.";
    }
}

// ---------- Load a record for editing (GET) — users only their own & Open ----------
if (isset($_GET["edit"])) {
    $id = intval($_GET["edit"]);
    if ($isAdmin) {
        $stmt = mysqli_prepare($conn, "SELECT id, client, device, issue, priority, status FROM tickets WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, client, device, issue, priority, status FROM tickets WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $editRow = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if (!$isAdmin && $editRow && $editRow["status"] !== "Open") {
        // Ticket is being handled by support — the user may only view it now
        $viewMode = true;
        $msg = "This ticket is being handled by support and can no longer be edited — view only.";
        $msgClass = "err";
    } elseif (!$editRow) {
        $msg = "You can only edit your own tickets."; $msgClass = "err";
    }
}

// ---------- Load a record for VIEWING (GET, read-only) ----------
if (isset($_GET["view"])) {
    $id = intval($_GET["view"]);
    if ($isAdmin) {
        $stmt = mysqli_prepare($conn, "SELECT id, client, device, issue, priority, status FROM tickets WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, client, device, issue, priority, status FROM tickets WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $uid);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $editRow = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
    if ($editRow) { $viewMode = true; }
    else { $msg = "Ticket not found."; $msgClass = "err"; }
}

// ---------- RETRIEVE — admins see all tickets, users see only their own ----------
if ($isAdmin) {
    $tickets = mysqli_query($conn, "SELECT * FROM tickets ORDER BY id ASC");
} else {
    $stmt = mysqli_prepare($conn, "SELECT * FROM tickets WHERE user_id = ? ORDER BY id ASC");
    mysqli_stmt_bind_param($stmt, "i", $uid);
    mysqli_stmt_execute($stmt);
    $tickets = mysqli_stmt_get_result($stmt);
}

// ---------- Load the comment thread for the ticket being edited/viewed ----------
$ticketComments = [];
if ($editRow) {
    $cid = intval($editRow["id"]);
    $cstmt = mysqli_prepare($conn, "SELECT author_name, comment, created FROM ticket_comments WHERE ticket_id = ? ORDER BY created ASC, id ASC");
    mysqli_stmt_bind_param($cstmt, "i", $cid);
    mysqli_stmt_execute($cstmt);
    $cres = mysqli_stmt_get_result($cstmt);
    while ($c = mysqli_fetch_assoc($cres)) { $ticketComments[] = $c; }
    mysqli_stmt_close($cstmt);
}

function h($v) { return htmlspecialchars($v ?? ""); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TechNova Solutions — Support Portal</title>
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
          <?php if ($isAdmin): ?><li><a href="admin.php">Admin</a></li><?php endif; ?>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <h1>Support Ticket Database</h1>
    <p>Welcome, <?= h($uname) ?> — <?= $isAdmin
        ? "Add, Retrieve, Update &amp; Delete all support records."
        : "Add and track your support tickets." ?></p>
  </section>

  <div class="container">
    <?php if ($msg): ?><div class="alert <?= $msgClass ?>"><?= h($msg) ?></div><?php endif; ?>

    <!-- ===== Add / Update form ===== -->
    <?php $dis = $viewMode ? "disabled" : ""; ?>
    <div class="card form-wrap" style="max-width:640px;">
      <h2 class="section-title"><?= $viewMode ? "View Ticket #" . h($editRow["id"]) : ($editRow ? "Update Ticket #" . h($editRow["id"]) : "Add New Ticket") ?></h2>
      <form method="post" action="dashboard.php"<?= ($editRow && !$viewMode) ? ' onsubmit="return confirm(\'Are you sure you want to save these changes?\');"' : '' ?>>
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="id" value="<?= $editRow ? h($editRow["id"]) : "" ?>" />
        <div class="form-row">
          <div>
            <label for="t-client">Client Name</label>
            <input type="text" id="t-client" name="client" value="<?= $editRow ? h($editRow["client"]) : "" ?>" required <?= $dis ?> />
          </div>
          <div>
            <label for="t-device">Device</label>
            <input type="text" id="t-device" name="device" placeholder="e.g. Dell Laptop" value="<?= $editRow ? h($editRow["device"]) : "" ?>" required <?= $dis ?> />
          </div>
        </div>
        <label for="t-issue">Issue Description</label>
        <input type="text" id="t-issue" name="issue" value="<?= $editRow ? h($editRow["issue"]) : "" ?>" required <?= $dis ?> />
        <div class="form-row">
          <div>
            <label for="t-priority">Priority</label>
            <select id="t-priority" name="priority" <?= $dis ?>>
              <?php foreach (["Low","Medium","High"] as $p): ?>
                <option <?= ($editRow && $editRow["priority"]===$p) ? "selected" : ($p==="Medium" && !$editRow ? "selected" : "") ?>><?= $p ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="t-status">Status</label>
            <?php if ($isAdmin): ?>
              <select id="t-status" name="status" <?= $dis ?>>
                <?php foreach (["Open","In Progress","Resolved"] as $s): ?>
                  <option <?= ($editRow && $editRow["status"]===$s) ? "selected" : ($s==="Open" && !$editRow ? "selected" : "") ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <?php $curStatus = $editRow ? $editRow["status"] : "Open"; ?>
              <select id="t-status" disabled>
                <option><?= h($curStatus) ?></option>
              </select>
              <small class="section-sub" style="display:block; margin-top:4px;">New tickets open as “Open”. Only an admin can change the status.</small>
            <?php endif; ?>
          </div>
        </div>

        <?php if ($editRow): ?>
        <!-- ===== Comment thread ===== -->
        <div style="margin-top:20px; border-top:1px solid #e2e8f0; padding-top:14px;">
          <label style="font-weight:700;">Comments</label>
          <?php if (empty($ticketComments)): ?>
            <p class="section-sub" style="margin:4px 0;">No comments yet.</p>
          <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:8px; margin:8px 0;">
              <?php foreach ($ticketComments as $c): ?>
                <div style="background:var(--light); border-radius:8px; padding:8px 12px;">
                  <div style="font-size:.8rem; color:var(--muted);"><b style="color:var(--brand);"><?= h($c["author_name"]) ?></b> &bull; <?= h($c["created"]) ?></div>
                  <div><?= nl2br(h($c["comment"])) ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
          <?php if ($isAdmin && !$viewMode): ?>
            <label for="t-comment" style="margin-top:10px;">Add a comment</label>
            <textarea id="t-comment" name="comment" rows="2" placeholder="Write a note or response for this ticket..."></textarea>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <div style="margin-top:16px;">
          <?php if ($viewMode): ?>
            <a href="dashboard.php" class="btn gray">← Back to list</a>
          <?php else: ?>
            <button type="submit" class="btn"><?= $editRow ? "Save Changes" : "Add Record" ?></button>
            <?php if ($editRow): ?><a href="dashboard.php" class="btn gray">Cancel</a><?php endif; ?>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <!-- ===== Retrieve: records table ===== -->
    <h2 class="section-title"><?= $isAdmin ? "All Support Tickets" : "My Support Tickets" ?></h2>
    <p class="section-sub"><?= $isAdmin
        ? "As an administrator you can view, edit and delete every ticket below."
        : "These are the tickets you created. You can add and edit your own tickets." ?></p>
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
              <?php if ($isAdmin): ?>
                <a class="btn alt" href="dashboard.php?edit=<?= h($row["id"]) ?>">Update</a>
                <a class="btn danger" href="dashboard.php?action=delete&id=<?= h($row["id"]) ?>"
                   onclick="return confirm('Delete record #<?= h($row["id"]) ?>? This cannot be undone.');">Delete</a>
              <?php elseif ($row["status"] === "Open"): ?>
                <a class="btn alt" href="dashboard.php?edit=<?= h($row["id"]) ?>">Update</a>
              <?php else: ?>
                <a class="btn gray" href="dashboard.php?view=<?= h($row["id"]) ?>">View</a>
              <?php endif; ?>
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
