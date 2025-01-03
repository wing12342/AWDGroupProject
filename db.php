<?php
$server = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$dbname = 'ElectricVehicleChargers';

// Create database connection
$conn = new mysqli($server, $dbuser, $dbpassword, $dbname);
if ($conn->connect_errno) {
    die('Database connection failed: ' . $conn->connect_error);
}

?>