<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Railway Debugger</h1>";

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

echo "<h2>Environment Variables Check:</h2>";
echo "MYSQLHOST: " . ($host ? "Set ($host)" : "<span style='color:red'>NOT SET (Defaulting to localhost which will fail)</span>") . "<br>";
echo "MYSQLUSER: " . ($user ? "Set ($user)" : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "MYSQLPASSWORD: " . ($pass ? "Set (***)" : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "MYSQLDATABASE: " . ($db ? "Set ($db)" : "<span style='color:red'>NOT SET</span>") . "<br>";
echo "MYSQLPORT: " . ($port ? "Set ($port)" : "<span style='color:red'>NOT SET</span>") . "<br>";

echo "<h2>Connection Attempt:</h2>";

// Force non-localhost fallback to see real error if env is missing
$target_host = $host ?: "localhost";
$target_user = $user ?: "root";
$target_pass = $pass ?: "";
$target_db = $db ?: "dormitory_db";
$target_port = $port ?: 3306;

try {
    $conn = mysqli_connect($target_host, $target_user, $target_pass, $target_db, $target_port);

    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
    echo "<p style='color:green'><strong>SUCCESS! Connected to MySQL.</strong></p>";
    echo "Host Info: " . mysqli_get_host_info($conn);

} catch (Exception $e) {
    echo "<p style='color:red'><strong>CONNECTION FAILED:</strong> " . $e->getMessage() . "</p>";
    echo "<p>If the variables above are 'NOT SET', you need to link the MySQL service variables to this project.</p>";
}
?>