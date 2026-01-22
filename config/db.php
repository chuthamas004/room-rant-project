<?php
$servername = getenv('MYSQLHOST') ?: "localhost";
$username = getenv('MYSQLUSER') ?: "root";
$password = getenv('MYSQLPASSWORD') ?: "";
$dbname = getenv('MYSQLDATABASE') ?: "dormitory_db";
$port = getenv('MYSQLPORT') ?: 3306;

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname, $port);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to safely escape strings
function escape($conn, $string)
{
    return mysqli_real_escape_string($conn, $string);
}
?>