<?php
/* ============================================================
   TechNova Solutions - Database Connection
   Uses mysqli to connect to the MySQL database created by
   database.sql. These are the default XAMPP settings:
     host = localhost, user = root, password = "" (blank)
   Change them only if your MySQL uses a different user/password.
   ============================================================ */

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";            // default XAMPP MySQL password is empty
$DB_NAME = "technova_db";

// Create the connection
$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Stop with a friendly message if the connection fails
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error() .
        "<br>Make sure MySQL is running in XAMPP and that you imported database.sql.");
}
?>
