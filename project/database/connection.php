<?php
// Simple .env loader
$env = parse_ini_file(__DIR__ . '/../.env');

$username = $env['DB_USER'] ?? "root";
$password = $env['DB_PASS'] ?? "";
$host     = $env['DB_HOST'] ?? "localhost";
$dbname   = $env['DB_NAME'] ?? "";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>