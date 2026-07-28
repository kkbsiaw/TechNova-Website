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
    $DB_HOST = "sql110.infinityfree.com";     // MySQL host name
    $DB_USER = "if0_42523368";                // MySQL username
    $DB_PASS = "";                            // <-- set your MySQL password locally (kept out of git)
    $DB_NAME = "if0_42523368_XXX";            // database name (replace XXX if not literal)
}

// Create the connection
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Stop with a friendly message if the connection fails
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
