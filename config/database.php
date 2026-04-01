<?php
// ===============================
// DATABASE CONFIGURATION
// ===============================

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'oracle_helpdesk');

// Create connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");


// ===============================
// ERROR REPORTING
// ===============================
error_reporting(E_ALL);
ini_set('display_errors', 1);


// ===============================
// APPLICATION SETTINGS
// ===============================
define('APP_NAME', 'ORACLE Helpdesk System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/oracle-helpdesk-system/');


// ===============================
// FILE UPLOAD SETTINGS
// ===============================
define('MAX_FILE_SIZE', 10485760); // 10 MB

define('ALLOWED_EXTENSIONS', [
    'pdf', 'doc', 'docx', 'xls', 'xlsx',
    'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'
]);

define('UPLOAD_PATH', 'uploads/');
define('PENDING_PATH', 'uploads/pending/');
define('APPROVED_PATH', 'uploads/approved/');
define('ARCHIVED_PATH', 'uploads/archived/');


// ===============================
// SESSION SETTINGS
// ===============================
define('SESSION_TIMEOUT', 3600); // 1 hour


// ===============================
// KNOWLEDGE BASE CATEGORIES
// ===============================
define('CATEGORIES', [
    1 => 'Working Instructions',
    2 => 'Standard Operating Procedures',
    3 => 'Functional Specifications',
    4 => 'Templates'
]);


// ===============================
// NOTIFICATION TYPES
// ===============================
define('NOTIFICATION_TYPES', [
    'approval'   => 'File Approved',
    'rejection'  => 'File Rejected',
    'new_file'   => 'New File Uploaded',
    'system'     => 'System Message'
]);

?>
