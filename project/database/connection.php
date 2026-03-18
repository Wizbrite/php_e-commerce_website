<?php
$username = "root";
$password = "njini000";
$host="localhost";
$dbname="BA2A_PHP";

// $conn = new mysqli($host, $username, $password, $dbname);

// if($conn->connect_error){
//     die("Connection failed: " . $conn->connect_error);
// }

$conn = mysqli_connect($host, $username, $password, $dbname);
if(!$conn){
    die("connection failed");
}
// Removed debug echo to prevent header errors

?>