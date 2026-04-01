<?php
session_start();
include '../config/database.php';
include '../config/constants.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Show login success popup
$show_popup = isset($_SESSION['show_popup']) ? $_SESSION['show_popup'] : false;
unset($_SESSION['show_popup']);

// Get user stats
$my_submissions = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM files WHERE uploaded_by = '{$_SESSION['user_id']}'"))['count'];
$approved_files = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM files WHERE uploaded_by = '{$_SESSION['user_id']}' AND status = 'approved'"))['count'];
$pending_files = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM files WHERE uploaded_by = '{$_SESSION['user_id']}' AND status = 'pending'"))['count'];
unread_notifications = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM notifications WHERE user_id = '{$_SESSION['user_id']}' AND is_read = 0"))['count'];

// Get approved files count
$approved_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM files WHERE status = 'approved'");
total_approved = mysqli_fetch_assoc($approved_result)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - ORACLE Helpdesk</title>
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
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .action-btn {
            background: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .action-btn:hover {
            transform: translateY(-5px);
            background: #f0f0f0;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 10px;
            text-align: center;
            animation: slideIn 0.3s ease-in;
        }
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .modal-content h2 {
            color: #28a745;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .modal-content p {
            color: #666;
            margin-bottom: 30px;
        }
        .modal-content button {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .logout-btn {
            background: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>👤 User Panel</h2>
        <ul>
            <li><a href="dashboard.php">📊 Dashboard</a></li>
            <li><a href="upload-file.php">📤 Upload File</a></li>
            <li><a href="my-submissions.php">📋 My Submissions</a></li>
            <li><a href="view-kb.php">📚 Knowledge Base</a></li>
            <li><a href="../auth/logout.php" class="logout-btn">🚪 Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo $_SESSION['full_name']; ?>!</h1>
            <p>Date: <?php echo date('Y-m-d H:i'); ?></p>
        </div>
        
        <div class="quick-actions">
            <a href="upload-file.php" class="action-btn">📤 Upload File</a>
            <a href="my-submissions.php" class="action-btn">📋 My Files</a>
            <a href="view-kb.php" class="action-btn">📚 Knowledge Base</a>
        </div>
        
        <div class="stats">
            <div class="stat-card">
                <p>My Submissions</p>
                <h3><?php echo $my_submissions; ?></h3>
            </div>
            <div class="stat-card">
                <p>Approved Files</p>
                <h3><?php echo $approved_files; ?></h3>
            </div>
            <div class="stat-card">
                <p>Pending Files</p>
                <h3><?php echo $pending_files; ?></h3>
            </div>
            <div class="stat-card">
                <p>Available KB Files</p>
                <h3><?php echo $total_approved; ?></h3>
            </div>
        </div>
    </div>
    
    <!-- Login Success Popup -->
    <div id="successModal" class="modal" style="display: <?php echo $show_popup ? 'flex' : 'none'; ?>;">
        <div class="modal-content">
            <h2>✅ Login Successful!</h2>
            <p>Welcome to ORACLE Helpdesk System</p>
            <button onclick="closeModal()">Continue</button>
        </div>
    </div>
    
    <script>
        function closeModal() {
            document.getElementById('successModal').style.display = 'none';
        }
    </script>
</body>
</html>
