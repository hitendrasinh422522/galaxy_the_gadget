<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        try {
            $db = (new Database())->connect();
            $stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role']      = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php"); // make sure this file exists
                } else {
                    header("Location: home.php");  // or home.php if you prefer
                }
                exit;
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Galaxy The Gadget</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #6c5ce7;
      --secondary: #a29bfe;
      --light: #f5f6fa;
      --dark: #2d3436;
    }
    body {
      background: linear-gradient(135deg, #6c5ce7, #00cec9);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .auth-container {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.2);
      overflow: hidden;
      width: 100%;
      max-width: 420px;
      animation: fadeIn 0.8s ease;
    }
    .auth-header {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: #fff;
      text-align: center;
      padding: 30px;
    }
    .auth-header h2 {
      margin: 0;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .auth-body { padding: 30px; }
    .form-control {
      border-radius: 8px;
      padding: 12px 15px;
      border: 2px solid #eee;
      transition: 0.3s;
    }
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 10px rgba(108,92,231,0.3);
    }
    .btn-auth {
      background: var(--primary);
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-weight: bold;
      color: #fff;
      width: 100%;
      transition: 0.3s;
    }
    .btn-auth:hover {
      background: var(--secondary);
      transform: scale(1.05);
    }
    .input-icon {
      position: relative;
    }
    .input-icon i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--secondary);
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
      color: var(--secondary);
    }
    .auth-footer {
      background: var(--light);
      text-align: center;
      padding: 15px;
    }
    .auth-footer a {
      text-decoration: none;
      font-weight: bold;
      color: var(--primary);
    }
    .alert {
      animation: shake 0.3s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      50% { transform: translateX(5px); }
      75% { transform: translateX(-5px); }
    }
  </style>
</head>
<body>

<div class="auth-container">
  <div class="auth-header">
    <h2><i class="fas fa-user-lock me-2"></i>Login</h2>
  </div>
  <div class="auth-body">
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3 input-icon">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
      </div>
      <div class="mb-3 input-icon">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
        <span class="password-toggle" onclick="togglePassword()"><i class="fas fa-eye"></i></span>
      </div>
      <button type="submit" class="btn-auth"><i class="fas fa-sign-in-alt me-2"></i>Login</button>
    </form>
  </div>
  <div class="auth-footer">
    <p>Don't have an account? <a href="register.php">Sign Up</a></p>
    <a href="index.php">← Back to Home</a>
  </div>
</div>

<script>
function togglePassword() {
  const input = document.getElementById("password");
  const icon = document.querySelector(".password-toggle i");
  if (input.type === "password") {
    input.type = "text";
    icon.classList.remove("fa-eye");
    icon.classList.add("fa-eye-slash");
  } else {
    input.type = "password";
    icon.classList.remove("fa-eye-slash");
    icon.classList.add("fa-eye");
  }
}
</script>

</body>
</html>
