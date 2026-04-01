<?php
session_start();
include '../config/database.php';
include '../config/constants.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$file_id = isset($_GET['file_id']) ? $_GET['file_id'] : '';

if ($action == 'approve' && $file_id) {
    $update_query = "UPDATE files SET status = 'approved', approved_by = '{$_SESSION['user_id']}', approved_at = NOW() WHERE id = '$file_id'";
    if (mysqli_query($conn, $update_query)) {
        // Get file uploader and send notification
        $file_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT uploaded_by FROM files WHERE id = '$file_id'"));
        $notify_query = "INSERT INTO notifications (user_id, file_id, message, type) VALUES ('{
        $file_result['uploaded_by']}', '$file_id', 'Your file has been approved!', 'approval')";
        mysqli_query($conn, $notify_query);
        
        $_SESSION['success'] = 'File approved successfully!';
    }
}

if ($action == 'disapprove' && $file_id) {
    $reason = mysqli_real_escape_string($conn, $_POST['reason'] ?? '');
    $update_query = "UPDATE files SET status = 'disapproved', rejection_reason = '$reason' WHERE id = '$file_id'";
    if (mysqli_query($conn, $update_query)) {
        $file_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT uploaded_by FROM files WHERE id = '$file_id'"));
        $notify_query = "INSERT INTO notifications (user_id, file_id, message, type) VALUES ('{
        $file_result['uploaded_by']}', '$file_id', 'Your file has been rejected!', 'rejection')";
        mysqli_query($conn, $notify_query);
        
        $_SESSION['success'] = 'File rejected successfully!';
    }
}

// Get pending files
$pending_result = mysqli_query($conn, "SELECT f.*, c.name as category_name, u.full_name FROM files f 
                                       LEFT JOIN categories c ON f.category_id = c.id 
                                       LEFT JOIN users u ON f.uploaded_by = u.id 
                                       WHERE f.status = 'pending' 
                                       ORDER BY f.uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Files - ORACLE Helpdesk</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            overflow-y: auto;
            padding: 20px;
        }
        
        .sidebar h2 {
            margin-bottom: 30px;
            text-align: center;
            font-size: 18px;
        }
        
        .sidebar ul {
            list-style: none;
        }
        
        .sidebar li {
            margin: 15px 0;
        }
        
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .sidebar a:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #667eea;
            color: white;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 5px;
        }
        
        .btn-approve {
            background: #28a745;
            color: white;
        }
        
        .btn-disapprove {
            background: #dc3545;
            color: white;
        }
        
        .btn-view {
            background: #007bff;
            color: white;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .logout-btn {
            background: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🔧 Admin Panel</h2>
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="manage-files.php">📁 Manage Files</a></li>
            <li><a href="approve-files.php">✅ Approve Files</a></li>
            <li><a href="manage-users.php">👥 Manage Users</a></li>
            <li><a href="notifications.php">🔔 Notifications</a></li>
            <li><a href="../auth/logout.php" class="logout-btn">🚪 Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>✅ Approve Pending Files</h1>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Category</th>
                    <th>Uploaded By</th>
                    <th>Uploaded Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($file = mysqli_fetch_assoc($pending_result)): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($file['file_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($file['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($file['full_name']); ?></td>
                        <td><?php echo date('Y-m-d H:i', strtotime($file['uploaded_at'])); ?></td>
                        <td><?php echo htmlspecialchars(substr($file['description'], 0, 50)); ?></td>
                        <td>
                            <button class="btn btn-approve" onclick="approveFile(<?php echo $file['id']; ?>)">✅ Approve</button>
                            <button class="btn btn-disapprove" onclick="disapproveFile(<?php echo $file['id']; ?>)">❌ Reject</button>
                            <a href="../<?php echo $file['file_path']; ?>" class="btn btn-view">👁️ View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function approveFile(fileId) {
            if (confirm('Are you sure you want to approve this file?')) {
                window.location.href = '?action=approve&file_id=' + fileId;
            }
        }
        
        function disapproveFile(fileId) {
            const reason = prompt('Please enter rejection reason:');
            if (reason !== null) {
                alert('File rejected with reason: ' + reason);
                window.location.href = '?action=disapprove&file_id=' + fileId + '&reason=' + reason;
            }
        }
    </script>
</body>
</html>
