<?php
session_start();
include '../config/database.php';
include '../config/constants.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['file']['name'];
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_size = $_FILES['file']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validation
        if ($file_size > MAX_FILE_SIZE) {
            $error = 'File size exceeds limit!';
        } elseif (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
            $error = 'File type not allowed!';
        } else {
            // Generate unique file name
            $new_file_name = time() . '_' . basename($file_name);
            $upload_path = PENDING_PATH . $new_file_name;
            
            if (move_uploaded_file($file_tmp, '../' . $upload_path)) {
                // Insert into database
                $query = "INSERT INTO files (file_name, file_path, category_id, uploaded_by, description, file_size, file_type, status) 
                         VALUES ('$file_name', '$upload_path', '$category_id', '{$_SESSION['user_id']}', '$description', '$file_size', '$file_ext', 'pending')";
                
                if (mysqli_query($conn, $query)) {
                    // Log activity
                    $action = 'File Uploaded: ' . $file_name;
                    $log_query = "INSERT INTO activity_log (user_id, action) VALUES ('{$_SESSION['user_id']}', '$action')";
                    mysqli_query($conn, $log_query);
                    
                    $success = 'File uploaded successfully! Waiting for admin approval.';
                } else {
                    $error = 'Database error!';
                }
            } else {
                $error = 'Failed to upload file!';
            }
        }
    } else {
        $error = 'Please select a file!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - ORACLE Helpdesk</title>
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
        
        .upload-container {
            background: white;
            padding: 30px;
            border-radius: 5px;
            max-width: 600px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #764ba2;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <h1>📤 Upload File</h1>
        </div>
        
        <div class="upload-container">
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Select Category --</option>
                        <option value="1">Working Instructions</option>
                        <option value="2">Standard Operating Procedures</option>
                        <option value="3">Functional Specifications</option>
                        <option value="4">Templates</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="file">Select File (Max 10 MB)</label>
                    <input type="file" id="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Enter file description..."></textarea>
                </div>
                
                <button type="submit" class="btn">📤 Upload File</button>
            </form>
        </div>
    </div>
</body>
</html>
