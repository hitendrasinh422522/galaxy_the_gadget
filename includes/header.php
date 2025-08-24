<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Galaxy The Gadget</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="css/headers.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .dropdown-menu { min-width: 200px; }
    .profile-dropdown { position: relative; }
    .profile-dropdown .dropdown-menu { right: 0; left: auto; }
    .nav-item.dropdown:hover .dropdown-menu { display: block; }
    .user-avatar { width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 8px; }
    .profile-icon { font-size: 1.2rem; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <span class="brand-text">Galaxy The Gadget</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <!-- ADMIN NAV -->
          <li class="nav-item"><a class="nav-link" href="admin/dashboard.php"><i class="fas fa-tachometer-alt me-1"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="admin/products/index.php"><i class="fas fa-boxes me-1"></i> Manage Products</a></li>
          <li class="nav-item"><a class="nav-link" href="admin/users/index.php"><i class="fas fa-users me-1"></i> Manage Users</a></li>
          <li class="nav-item"><a class="nav-link" href="admin/orders/index.php"><i class="fas fa-clipboard-list me-1"></i> Orders</a></li>
        
        <?php else: ?>
          <!-- USER NAV -->
          <li class="nav-item"><a class="nav-link" href="home.php"><i class="fas fa-home me-1"></i> Home</a></li>
          <li class="nav-item"><a class="nav-link" href="product_detail.php"><i class="fas fa-box-open me-1"></i> Products</a></li>
          <li class="nav-item"><a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i> Cart</a></li>
          <li class="nav-item"><a class="nav-link" href="pages/wishlist.php"><i class="fas fa-heart me-1"></i> Wishlist</a></li>
          <li class="nav-item"><a class="nav-link" href="pages/orders.php"><i class="fas fa-clipboard-list me-1"></i> Orders</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item dropdown profile-dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (isset($_SESSION['user_id'])): ?>
              <?php if (!empty($_SESSION['user_avatar'])): ?>
                <img src="/galaxy_the_gadget/uploads/avatars/<?= htmlspecialchars($_SESSION['user_avatar']) ?>" class="user-avatar" alt="Profile">
              <?php else: ?>
                <i class="fas fa-user-circle profile-icon me-1"></i>
              <?php endif; ?>
              <?= htmlspecialchars($_SESSION['user_name']); ?>
            <?php else: ?>
              <i class="fas fa-user-circle profile-icon"></i>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
            <?php if (isset($_SESSION['user_id'])): ?>
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-cogs me-2"></i> Admin Panel</a></li>
              <?php else: ?>
                <li><a class="dropdown-item" href="my_profile.php"><i class="fas fa-user me-2"></i> My Profile</a></li>
                <li><a class="dropdown-item" href="view_all_order.php"><i class="fas fa-clipboard-list me-2"></i> My Orders</a></li>
              <?php endif; ?>
            

              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
            <?php else: ?>
              <li><a class="dropdown-item" href="login.php"><i class="fas fa-sign-in-alt me-2"></i> Login</a></li>
              <li><a class="dropdown-item" href="register.php"><i class="fas fa-user-plus me-2"></i> Register</a></li>
            <?php endif; ?>
          </ul>
        </li>
      </ul>

    </div>
  </div>
</nav>

<div class="container mt-4">
