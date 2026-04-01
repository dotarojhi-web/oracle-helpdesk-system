<?php
session_start();
include '../config/database.php';
include '../config/constants.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Get statistics
total_files = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM files"))['count'];
pending_files = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM files WHERE status = 'pending'"))['count'];
approved_files = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM files WHERE status = 'approved'"))['count'];
total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
unread_notifications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$_SESSION['user_id']}' AND is_read = 0"))['count'];

// Get recent activity
$activity_result = mysqli_query($conn, "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ORACLE Helpdesk</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            color: #333;
        }
        
        .header button {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-card h3 {
            color: #667eea;
            font-size: 32px;
            margin: 10px 0;
        }
        
        .stat-card p {
            color: #666;
            font-size: 14px;
        }
        
        .recent-activity {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .recent-activity h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .activity-list {
            list-style: none;
        }
        
        .activity-list li {
            padding: 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .activity-list li:last-child {
            border-bottom: none;
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
            <li><a href="notifications.php">🔔 Notifications (<?php echo $unread_notifications; ?>)</a></li>
            <li><a href="../auth/logout.php" class="logout-btn">🚪 Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
            <p>Date: <?php echo date('Y-m-d H:i'); ?></p>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <p>Total Files</p>
                <h3><?php echo $total_files; ?></h3>
            </div>
            <div class="stat-card">
                <p>Pending Files</p>
                <h3><?php echo $pending_files; ?></h3>
            </div>
            <div class="stat-card">
                <p>Approved Files</p>
                <h3><?php echo $approved_files; ?></h3>
            </div>
            <div class="stat-card">
                <p>Total Users</p>
                <h3><?php echo $total_users; ?></h3>
            </div>
        </div>
        
        <div class="recent-activity">
            <h2>Recent Activity</h2>
            <ul class="activity-list">
                <?php while ($activity = mysqli_fetch_assoc($activity_result)): ?>
                    <li>
                        <strong><?php echo $activity['action']; ?></strong>
                        <br>
                        <small><?php echo $activity['created_at']; ?></small>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>
