<?php
session_start();
require_once "config/database.php";

// Create DB connection
$db = new Database();
$conn = $db->connect();

// Handle filtering - only date filter since status column doesn't exist
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with filters
$query = "SELECT * FROM orders";
$params = [];

if (!empty($date_filter)) {
    $query .= " WHERE DATE(created_at) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY order_id DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders");
$count_stmt->execute();
$count_data = $count_stmt->fetch(PDO::FETCH_ASSOC);
$all_count = $count_data['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management - Galaxy The Gadget</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4e54c8;
      --secondary: #8f94fb;
      --light: #f8f9fa;
      --dark: #343a40;
      --success: #28a745;
      --warning: #ffc107;
      --danger: #dc3545;
      --info: #17a2b8;
    }
    
    body {
      background: linear-gradient(to right, var(--light), #eef2f5);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .navbar-brand {
      font-weight: 700;
      color: var(--primary);
    }
    
    .order-container {
      max-width: 1200px;
      margin: 30px auto;
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 25px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    
    .page-header {
      background: linear-gradient(to right, var(--primary), var(--secondary));
      color: white;
      padding: 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    
    .filter-section {
      padding: 20px;
      background: var(--light);
      border-bottom: 1px solid #dee2e6;
    }
    
    .order-card {
      border: none;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      margin-bottom: 20px;
      transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .order-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
    }
    
    .order-body {
      padding: 20px;
    }
    
    .order-footer {
      padding: 15px 20px;
      background: #f9f9f9;
      border-top: 1px solid #eee;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .order-meta {
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }
    
    .order-meta-item {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 0.9rem;
      color: #6c757d;
    }
    
    .btn-action {
      border-radius: 20px;
      padding: 5px 15px;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .filter-btn {
      border-radius: 20px;
      margin: 0 5px 10px 0;
    }
    
    .stats-card {
      background: white;
      border-radius: 10px;
      padding: 15px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
      text-align: center;
      margin-bottom: 20px;
    }
    
    .stats-number {
      font-size: 1.8rem;
      font-weight: 700;
      margin: 10px 0;
    }
    
    @media (max-width: 768px) {
      .order-header, .order-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
      }
      
      .order-actions {
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="#">
        <i class="fas fa-rocket me-2"></i>Galaxy The Gadget
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="dashboard.php"><i class="fas fa-home me-1"></i> Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" href="#"><i class="fas fa-list-alt me-1"></i> Orders</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fas fa-cog me-1"></i> Settings</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="fas fa-user me-1"></i> Profile</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <?php if (isset($_SESSION['message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['message'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="row mb-4">
      <div class="col-md-4">
        <div class="stats-card">
          <div class="text-primary">
            <i class="fas fa-shopping-cart fa-2x"></i>
          </div>
          <div class="stats-number"><?= $all_count ?></div>
          <div class="text-muted">Total Orders</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card">
          <div class="text-info">
            <i class="fas fa-truck fa-2x"></i>
          </div>
          <div class="stats-number"><?= $all_count ?></div>
          <div class="text-muted">All Orders</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="stats-card">
          <div class="text-success">
            <i class="fas fa-check-circle fa-2x"></i>
          </div>
          <div class="stats-number"><?= $all_count ?></div>
          <div class="text-muted">All Orders</div>
        </div>
      </div>
    </div>

    <div class="order-container">
      <div class="page-header">
        <h2><i class="fas fa-list-alt me-2"></i> Order Management</h2>
        <div>
          <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fas fa-filter me-1"></i> Filters
          </button>
          <button class="btn btn-light">
            <i class="fas fa-download me-1"></i> Export
          </button>
        </div>
      </div>

      <div class="filter-section">
        <div class="d-flex flex-wrap align-items-center">
          <span class="me-2">Showing all orders:</span>
          <a href="?" class="btn btn-sm btn-primary filter-btn">
            All <span class="badge bg-secondary"><?= $all_count ?></span>
          </a>
        </div>
        
        <?php if (!empty($date_filter)): ?>
          <div class="mt-2">
            <span class="badge bg-info">
              Date: <?= $date_filter ?>
              <a href="?" class="text-white ms-2"><i class="fas fa-times"></i></a>
            </span>
          </div>
        <?php endif; ?>
      </div>

      <div class="p-4">
        <?php if (count($orders) > 0): ?>
          <?php foreach ($orders as $order): ?>
            <?php
              $order_date = $order['created_at'] ?? '';
              
              // Get order items details
              $stmt_items = $conn->prepare("SELECT SUM(quantity) as total_items, SUM(price * quantity) as total_amount 
                                            FROM order_items WHERE order_id = ?");
              $stmt_items->execute([$order['order_id']]);
              $item_data = $stmt_items->fetch(PDO::FETCH_ASSOC);
              
              $total_items = $item_data['total_items'] ?? 0;
              $total_amount = $item_data['total_amount'] ?? 0;
              
              // Get customer info
              $customer_name = "Guest";
              if (!empty($order['customer_id'])) {
                $stmt_customer = $conn->prepare("SELECT name FROM customers WHERE customer_id = ?");
                $stmt_customer->execute([$order['customer_id']]);
                $customer = $stmt_customer->fetch(PDO::FETCH_ASSOC);
                $customer_name = $customer['name'] ?? 'Guest';
              }
              
              // Safely get all order details with default values
              $shipping_address = isset($order['shipping_address']) ? substr($order['shipping_address'], 0, 50) . '...' : 'Not provided';
              $contact_number = $order['contact_number'] ?? 'N/A';
              $payment_method = isset($order['payment_method']) ? ucfirst($order['payment_method']) : 'Not specified';
            ?>
            <div class="order-card">
              <div class="order-header">
                <div>
                  <h5 class="mb-0">Order #<?= $order['order_id'] ?></h5>
                  <small class="text-muted">Placed by <?= htmlspecialchars($customer_name) ?></small>
                </div>
                <span class="badge bg-info">
                  Order Placed
                </span>
              </div>
              
              <div class="order-body">
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-2"><i class="far fa-calendar-alt me-2"></i> 
                      <?= !empty($order_date) ? date('M j, Y \a\t g:i A', strtotime($order_date)) : 'Date not available' ?>
                    </p>
                    <p class="mb-0"><i class="fas fa-box me-2"></i> <?= $total_items ?> items</p>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-2"><i class="fas fa-money-bill-wave me-2"></i> Total: <strong>₹<?= number_format($total_amount, 2) ?></strong></p>
                    <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i> <?= htmlspecialchars($shipping_address) ?></p>
                  </div>
                </div>
              </div>
              
              <div class="order-footer">
                <div class="order-meta">
                  <div class="order-meta-item">
                    <i class="fas fa-user"></i>
                    <span><?= htmlspecialchars($customer_name) ?></span>
                  </div>
                  <div class="order-meta-item">
                    <i class="fas fa-phone"></i>
                    <span><?= htmlspecialchars($contact_number) ?></span>
                  </div>
                  <div class="order-meta-item">
                    <i class="fas fa-credit-card"></i>
                    <span><?= htmlspecialchars($payment_method) ?></span>
                  </div>
                </div>
                
                <div class="order-actions">
                  <a href="order_detail.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-primary btn-action">
                    <i class="fas fa-eye me-1"></i> View
                  </a>
                  <a href="invoice.php?order_id=<?= $order['order_id'] ?>" class="btn btn-sm btn-outline-secondary btn-action">
                    <i class="fas fa-file-invoice me-1"></i> Invoice
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="text-center py-5">
            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">No orders found</h4>
            <p class="text-muted">Try adjusting your filters or check back later.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Filter Modal -->
  <div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Filter Orders</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form method="GET">
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date_filter) ?>">
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Apply Filter</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <footer class="mt-5 py-4 bg-white text-center text-muted">
    <div class="container">
      <p>© 2023 Galaxy The Gadget. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Simple JavaScript to enhance interactivity
    document.addEventListener('DOMContentLoaded', function() {
      // Auto-dismiss alerts after 5 seconds
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        setTimeout(() => {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        }, 5000);
      });
    });
  </script>
</body>
</html>