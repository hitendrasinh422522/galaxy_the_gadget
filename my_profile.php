

<?php

session_start();
include('includes/header.php');
require_once 'config/database.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = (new Database())->connect();
$error = '';
$success = '';

// Fetch user data with profile picture
$stmt = $db->prepare("SELECT id, name, email, profile_picture, created_at FROM users WHERE id = :id");
$stmt->bindParam(":id", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $uploadDir = 'uploads/profile_pictures/';
    
    // Create directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $file = $_FILES['profile_picture'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mime, $allowedTypes)) {
            $error = "Only JPG, PNG, and GIF files are allowed.";
        } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB limit
            $error = "File size must be less than 2MB.";
        } else {
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Delete old profile picture if exists
                if ($user['profile_picture'] && file_exists($uploadDir . $user['profile_picture'])) {
                    unlink($uploadDir . $user['profile_picture']);
                }
                
                // Update database
                $update = $db->prepare("UPDATE users SET profile_picture = :picture WHERE id = :id");
                $update->bindParam(":picture", $filename);
                $update->bindParam(":id", $_SESSION['user_id']);
                
                if ($update->execute()) {
                    $user['profile_picture'] = $filename;
                    $success = "Profile picture updated successfully!";
                } else {
                    $error = "Failed to update profile picture in database.";
                    unlink($destination); // Clean up
                }
            } else {
                $error = "Failed to upload file.";
            }
        }
    } else {
        $error = "File upload error: " . $file['error'];
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$name || !$email) {
        $error = "Name and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is being changed
        if ($email !== $user['email']) {
            $checkEmail = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $checkEmail->bindParam(":email", $email);
            $checkEmail->bindParam(":id", $_SESSION['user_id']);
            $checkEmail->execute();
            
            if ($checkEmail->rowCount() > 0) {
                $error = "Email already exists.";
            }
        }

        // Check if password is being changed
        $password_changed = false;
        if ($new_password || $confirm_password || $current_password) {
            if (!$current_password) {
                $error = "Current password is required to change password.";
            } else {
                // Verify current password
                $checkPass = $db->prepare("SELECT password FROM users WHERE id = :id");
                $checkPass->bindParam(":id", $_SESSION['user_id']);
                $checkPass->execute();
                $db_password = $checkPass->fetchColumn();
                
                if (!password_verify($current_password, $db_password)) {
                    $error = "Current password is incorrect.";
                } elseif ($new_password !== $confirm_password) {
                    $error = "New passwords do not match.";
                } elseif (strlen($new_password) < 8) {
                    $error = "New password must be at least 8 characters.";
                } else {
                    $password_changed = true;
                }
            }
        }

        if (!$error) {
            try {
                $db->beginTransaction();
                
                // Update basic info
                $update = $db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
                $update->bindParam(":name", $name);
                $update->bindParam(":email", $email);
                $update->bindParam(":id", $_SESSION['user_id']);
                $update->execute();
                
                // Update password if changed
                if ($password_changed) {
                    $hash = password_hash($new_password, PASSWORD_DEFAULT);
                    $updatePass = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
                    $updatePass->bindParam(":password", $hash);
                    $updatePass->bindParam(":id", $_SESSION['user_id']);
                    $updatePass->execute();
                }
                
                $db->commit();
                
                // Update session
                $_SESSION['user_name'] = $name;
                $user['name'] = $name;
                $user['email'] = $email;
                
                $success = "Profile updated successfully!";
            } catch (PDOException $e) {
                $db->rollBack();
                $error = "An error occurred while updating your profile.";
            }
        }
    }
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $confirm_password = $_POST['delete_password'] ?? '';
    
    if (!$confirm_password) {
        $error = "Please enter your password to confirm account deletion.";
    } else {
        // Verify password
        $checkPass = $db->prepare("SELECT password FROM users WHERE id = :id");
        $checkPass->bindParam(":id", $_SESSION['user_id']);
        $checkPass->execute();
        $db_password = $checkPass->fetchColumn();
        
        if (!password_verify($confirm_password, $db_password)) {
            $error = "Incorrect password.";
        } else {
            try {
                $db->beginTransaction();
                
                // Delete profile picture if exists
                if ($user['profile_picture']) {
                    $filePath = 'uploads/profile_pictures/' . $user['profile_picture'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                
                // Delete user
                $delete = $db->prepare("DELETE FROM users WHERE id = :id");
                $delete->bindParam(":id", $_SESSION['user_id']);
                $delete->execute();
                
                $db->commit();
                
                // Logout and redirect
                session_destroy();
                header("Location: login.php?account_deleted=1");
                exit;
            } catch (PDOException $e) {
                $db->rollBack();
                $error = "An error occurred while deleting your account.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Galaxy The Gadget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --dark-color: #2d3436;
            --light-color: #f5f6fa;
            --danger-color: #d63031;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .profile-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .profile-avatar-container {
            position: relative;
            width: fit-content;
            margin: 0 auto 15px;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 48px;
            font-weight: bold;
            border: 3px solid white;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-upload-label {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: var(--primary-color);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .avatar-upload-label:hover {
            background: var(--secondary-color);
            transform: scale(1.1);
        }
        
        .member-since {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .profile-body {
            padding: 30px;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(108, 92, 231, 0.25);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--danger-color);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }
        
        .input-icon input {
            padding-left: 45px;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary-color);
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .nav-pills .nav-link.active {
            background-color: var(--primary-color);
        }
        
        .nav-pills .nav-link {
            color: var(--dark-color);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar">
                            <?php if (!empty($user['profile_picture'])): ?>
                                <img src="uploads/profile_pictures/<?= htmlspecialchars($user['profile_picture']) ?>" 
                                     alt="Profile Picture">
                            <?php else: ?>
                                <?= strtoupper(substr($user['name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <form method="post" enctype="multipart/form-data" class="avatar-upload-form">
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" class="d-none">
                            <label for="profile_picture" class="avatar-upload-label" title="Change profile picture">
                                <i class="fas fa-camera"></i>
                            </label>
                        </form>
                    </div>
                    <h3><?= htmlspecialchars($user['name']) ?></h3>
                    <div class="member-since">
                        Member since <?= date('F Y', strtotime($user['created_at'])) ?>
                    </div>
                </div>
                
                <div class="profile-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <ul class="nav nav-pills mb-4" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="pill" data-bs-target="#profile" type="button" role="tab">
                                <i class="fas fa-user-circle me-2"></i>Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-lock me-2"></i>Security
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="danger-tab" data-bs-toggle="pill" data-bs-target="#danger" type="button" role="tab">
                                <i class="fas fa-exclamation-triangle me-2"></i>Danger Zone
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Profile Tab -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <h4 class="section-title">Personal Information</h4>
                            <form method="POST">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Full Name</label>
                                        <div class="input-icon">
                                            <i class="fas fa-user"></i>
                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Email Address</label>
                                        <div class="input-icon">
                                            <i class="fas fa-envelope"></i>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required />
                                        </div>
                                    </div>
                                </div>
                                
                                <h4 class="section-title mt-4">Change Password</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Current Password</label>
                                        <div class="input-icon">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" name="current_password" id="currentPassword" class="form-control" placeholder="Leave blank to keep current" />
                                            <span class="password-toggle" onclick="togglePassword('currentPassword')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">New Password</label>
                                        <div class="input-icon">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" name="new_password" id="newPassword" class="form-control" placeholder="Leave blank to keep current" />
                                            <span class="password-toggle" onclick="togglePassword('newPassword')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Confirm Password</label>
                                        <div class="input-icon">
                                            <i class="fas fa-lock"></i>
                                            <input type="password" name="confirm_password" id="confirmPassword" class="form-control" placeholder="Leave blank to keep current" />
                                            <span class="password-toggle" onclick="togglePassword('confirmPassword')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid mt-4">
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
                            <h4 class="section-title">Security Settings</h4>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-shield-alt me-2"></i>Two-Factor Authentication</h5>
                                    <p class="card-text">Add an extra layer of security to your account by enabling two-factor authentication.</p>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="twoFactorSwitch" disabled>
                                        <label class="form-check-label" for="twoFactorSwitch">Enable Two-Factor Authentication</label>
                                    </div>
                                    <small class="text-muted">Coming soon</small>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-laptop me-2"></i>Active Sessions</h5>
                                    <p class="card-text">View and manage devices that are currently logged in to your account.</p>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>This is your current session (<?= htmlspecialchars($_SERVER['REMOTE_ADDR']) ?>)
                                    </div>
                                    <button class="btn btn-outline-primary" disabled>
                                        <i class="fas fa-sign-out-alt me-2"></i>Log out all other sessions
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Danger Zone Tab -->
                        <div class="tab-pane fade" id="danger" role="tabpanel">
                            <h4 class="section-title text-danger">Danger Zone</h4>
                            <div class="card border-danger">
                                <div class="card-body">
                                    <h5 class="card-title text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Delete Account</h5>
                                    <p class="card-text">Once you delete your account, there is no going back. Please be certain.</p>
                                    <form method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This cannot be undone!');">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Enter your password to confirm</label>
                                            <div class="input-icon">
                                                <i class="fas fa-lock"></i>
                                                <input type="password" name="delete_password" class="form-control" placeholder="Your current password" required />
                                            </div>
                                        </div>
                                        <button type="submit" name="delete_account" class="btn btn-danger">
                                            <i class="fas fa-trash-alt me-2"></i>Delete My Account
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Profile picture upload preview and auto-submit
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            const avatar = document.querySelector('.profile-avatar');
            
            reader.onload = function(e) {
                if (avatar.querySelector('img')) {
                    avatar.querySelector('img').src = e.target.result;
                } else {
                    avatar.innerHTML = `<img src="${e.target.result}" alt="Profile Picture">`;
                }
                
                // Submit the form automatically after selection
                document.querySelector('.avatar-upload-form').submit();
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include('includes/footer.php'); ?>