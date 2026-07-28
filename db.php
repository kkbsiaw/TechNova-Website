<?php
/* ============================================================
   TechNova Solutions - Database Connection
   Works on BOTH local XAMPP and live InfinityFree hosting.
   It auto-detects the environment from the request hostname,
   so you can upload the same file to both places.
   ============================================================ */

$host = $_SERVER["HTTP_HOST"] ?? "";

if (strpos($host, "localhost") !== false || strpos($host, "127.0.0.1") !== false) {
    /* ---------- LOCAL (XAMPP) — default settings ---------- */
    $DB_HOST = "localhost";
    $DB_USER = "root";
    $DB_PASS = "";
    $DB_NAME = "technova_db";
} else {
    /* ---------- LIVE (InfinityFree) ----------
       Replace the 4 values below with the details from your
       InfinityFree control panel -> "MySQL Databases".
       (Leave the local block above unchanged.)                */
    $DB_HOST = "sqlXXX.infinityfree.com";     // MySQL host name (e.g. sql200.infinityfree.com)
    $DB_USER = "if0_42523368";                // MySQL username (usually your account id)
    $DB_PASS = "YOUR_DB_PASSWORD";            // MySQL / account password
    $DB_NAME = "if0_42523368_technova";       // the database name you created
}

// Create the connection
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Stop with a friendly message if the connection fails
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
