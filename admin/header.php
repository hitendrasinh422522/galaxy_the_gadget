<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../user/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Galaxy The Gadgets</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="admin-header">
        <h1>Galaxy The Gadgets - Admin Panel</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a>
            <a href="manage_products.php">Products</a>
            <a href="manage_users.php">Users</a>
            <a href="manage_orders.php">Orders</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </div>
    <div class="admin-container">
