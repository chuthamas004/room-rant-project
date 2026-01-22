<?php
require_once 'config/db.php';

echo "<h1>Starting Database Setup...</h1>";

// Read the SQL file
$sqlFile = 'database.sql';
if (!file_exists($sqlFile)) {
    die("Error: database.sql file not found!");
}

$sql = file_get_contents($sqlFile);

// Remove "CREATE DATABASE" and "USE" commands because Railway handles the database creation
// and we are already connected to the correct database via config/db.php
$sql = preg_replace('/CREATE DATABASE.*?;/s', '', $sql);
$sql = preg_replace('/USE .*?;/s', '', $sql);

// Split into individual queries (assuming ; at end of line is the delimiter)
$queries = explode(';', $sql);

foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if (mysqli_query($conn, $query)) {
            echo "<p style='color:green'>Query executed successfully: " . substr($query, 0, 50) . "...</p>";
        } else {
            echo "<p style='color:red'>Error executing query: " . mysqli_error($conn) . "</p>";
        }
    }
}

echo "<h2>Database setup completed!</h2>";
echo "<a href='index.php'>Go to Home Page</a>";
?>,