<?php
require_once 'config/database.php';

try {
    $db = (new Database())->connect();

    $name     = "Admin User";
    $email    = "admin@example.com";
    $password = password_hash("123", PASSWORD_DEFAULT); // password = 123
    $role     = "admin";

    $stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":role", $role);

    if ($stmt->execute()) {
        echo "✅ Admin created! Email: admin@example.com | Password: 123";
    } else {
        echo "❌ Failed to create admin";
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
