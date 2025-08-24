<?php
session_start();
include 'db.php'; // your DB connection file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "⚠ Please enter both email and password.";
        header("Location: login.php");
        exit();
    }

    // Query user
    $sql = "SELECT id, name, email, password, created_at, role FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password (using MD5 here for your example, but recommend password_hash later)
        if ($user['password'] === md5($password)) {
            // Store session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = $user['created_at'];

            // Redirect based on role
            if ($user['role'] === "admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "❌ Invalid password!";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "❌ No account found with this email!";
        header("Location: login.php");
        exit();
    }
}
?>
