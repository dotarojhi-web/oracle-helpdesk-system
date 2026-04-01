<?php
$host = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'oracle_helpdesk';

// Create connection
$conn = new mysqli($host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Define error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
