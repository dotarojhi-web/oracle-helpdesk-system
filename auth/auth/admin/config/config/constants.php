<?php
// Application Constants
define('APP_NAME', 'ORACLE Helpdesk System');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/oracle-helpdesk-system/');

// File Upload Settings
define('MAX_FILE_SIZE', 10485760); // 10 MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png']);
define('UPLOAD_PATH', 'uploads/');
define('PENDING_PATH', 'uploads/pending/');
define('APPROVED_PATH', 'uploads/approved/');
define('ARCHIVED_PATH', 'uploads/archived/');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour

// Categories
define('CATEGORIES', [
    1 => 'Working Instructions',
    2 => 'Standard Operating Procedures',
    3 => 'Functional Specifications',
    4 => 'Templates'
]);

// Notification Types
define('NOTIFICATION_TYPES', [
    'approval' => 'File Approved',
    'rejection' => 'File Rejected',
    'new_file' => 'New File Uploaded',
    'system' => 'System Message'
]);
?>
