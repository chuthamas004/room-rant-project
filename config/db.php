<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "dormitory_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to safely escape strings
function escape($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}
?>
