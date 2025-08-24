<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config/database.php";

$db = new Database();
$conn = $db->connect();

if (!isset($_GET['order_id'])) {
    header("Location:view_all_order.php");
    exit();
}
$order_id = intval($_GET['order_id']);

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Fetch order items
$stmt_items = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt_items->execute([$order_id]);
$items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

// Calculate item counts and totals for display
$item_count = 0;
$subtotal = 0;
foreach ($items as $item) {
    $item_count += $item['quantity'];
    $subtotal += $item['price'] * $item['quantity'];
}

// Handle order date safely
$orderDate = "Unknown";
if (!empty($order['order_date'])) {
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['order_date']));
} elseif (!empty($order['created_at'])) {
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['created_at']));
} elseif (!empty($order['date'])) {
    $orderDate = date('F j, Y \a\t g:i A', strtotime($order['date']));
}

// ✅ Handle shipping safely (default 0 if column missing)
$shipping = isset($order['shipping']) ? (float)$order['shipping'] : 0.00;

// ✅ Handle tax safely (default 0 if column missing)
$tax = isset($order['tax']) ? (float)$order['tax'] : 0.00;

// ✅ Handle total safely (default subtotal + shipping + tax if missing)
$total = isset($order['total']) ? (float)$order['total'] : $subtotal + $shipping + $tax;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Confirmation - Galaxy The Gadget</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Orbitron:wght@600;700&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #3a36e7;
    --primary-gradient: linear-gradient(135deg, #3a36e7 0%, #6d6aff 100%);
    --secondary: #ff4d8d;
    --dark: #0d102c;
    --light: #f8f9ff;
    --success: #2ecc71;
    --border-radius: 16px;
    --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s ease;
}

body {
    font-family: 'Montserrat', sans-serif;
    background-color: #fafbff;
    color: #33334d;
    line-height: 1.6;
}

.confirmation-header {
    background: var(--primary-gradient);
    color: white;
    padding: 3rem 0;
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.confirmation-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,128L48,117.3C96,107,192,85,288,112C384,139,480,213,576,218.7C672,224,768,160,864,138.7C960,117,1056,139,1152,149.3C1248,160,1344,160,1392,160L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
    background-size: cover;
    background-position: center;
}

.confirmation-icon {
    font-size: 5rem;
    margin-bottom: 1rem;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
}

.confirmation-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 2.5rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
    transition: var(--transition);
}

.confirmation-card:hover {
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
}

.confirmation-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 5px;
    height: 100%;
    background: var(--primary-gradient);
}

.order-badge {
    background: var(--primary-gradient);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-block;
    margin-bottom: 0.5rem;
}

.detail-title {
    color: var(--dark);
    margin: 1.5rem 0 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #f0f1f7;
    display: flex;
    align-items: center;
}

.detail-title i {
    margin-right: 0.75rem;
    color: var(--primary);
}

.product-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    margin: 1.5rem 0;
}

.product-table thead {
    background: #f8f9ff;
}

.product-table th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--dark);
    border-bottom: 2px solid #f0f1f7;
}

.product-table td {
    padding: 1.2rem 1rem;
    border-bottom: 1px solid #f0f1f7;
    transition: var(--transition);
}

.product-table tr:hover td {
    background: #f8f9ff;
}

.product-table tr:last-child td {
    border-bottom: none;
}

.total-display {
    background: #f8f9ff;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    margin: 2rem 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
}

.grand-total {
    font-weight: 700;
    font-size: 1.2rem;
    color: var(--primary);
    border-top: 2px solid #e0e2f0;
    padding-top: 1rem;
    margin-top: 0.5rem;
}

.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 2rem 0 1rem;
}

.btn-action {
    padding: 0.8rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: var(--transition);
}

.btn-primary-action {
    background: var(--primary-gradient);
    color: white;
    border: none;
}

.btn-primary-action:hover {
    transform: translateY(-3px);
    box-shadow: 0 7px 15px rgba(58, 54, 231, 0.3);
    color: white;
}

.btn-outline-action {
    background: transparent;
    color: var(--primary);
    border: 2px solid var(--primary);
}

.btn-outline-action:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-3px);
}

.print-only {
    display: none;
}

/* Animation for order items */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.product-table tbody tr {
    animation: fadeIn 0.5s ease forwards;
}

.product-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
.product-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
.product-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
.product-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
.product-table tbody tr:nth-child(5) { animation-delay: 0.5s; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .confirmation-card {
        padding: 1.5rem;
    }
    
    .product-table {
        display: block;
        overflow-x: auto;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn-action {
        justify-content: center;
    }
}

/* Confetti effect */
.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    background: var(--secondary);
    opacity: 0.7;
    border-radius: 0;
    animation: confetti-fall 5s linear forwards;
}

@keyframes confetti-fall {
    0% {
        transform: translateY(-100px) rotate(0deg);
        opacity: 1;
    }
    100% {
        transform: translateY(500px) rotate(720deg);
        opacity: 0;
    }
}

/* Footer styling */
.footer {
    background: var(--dark);
    color: white;
    padding: 3rem 0 2rem;
    margin-top: 3rem;
}

/* Header styling */
.navbar {
    background: white;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
}

.navbar-brand {
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    color: var(--primary) !important;
}

/* Add some decorative elements */
.decorative-circle {
    position: absolute;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(58, 54, 231, 0.05);
    z-index: -1;
}

.decorative-circle-1 {
    top: -100px;
    right: -100px;
}

.decorative-circle-2 {
    bottom: -100px;
    left: -100px;
    width: 300px;
    height: 300px;
}
</style>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="confirmation-header">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="confirmation-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1 class="display-4 fw-bold">Order Confirmed!</h1>
                <p class="lead">Thank you for shopping with Galaxy The Gadget</p>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="confirmation-card">
                <div class="decorative-circle decorative-circle-1"></div>
                <div class="decorative-circle decorative-circle-2"></div>
                
                <div class="text-center mb-4">
                    <span class="order-badge">Order #<?= $order['order_id'] ?></span>
                    <p class="text-muted">Placed on <?= $orderDate ?></p>
                </div>

                <!-- Order summary -->
                <h4 class="detail-title"><i class="fas fa-receipt"></i> Order Summary</h4>
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['product_name']) ?></td>
                            <td>$<?= number_format($row['price'], 2) ?></td>
                            <td><?= $row['quantity'] ?></td>
                            <td>$<?= number_format($row['price'] * $row['quantity'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="total-display">
                    <div class="total-row">
                        <span>Subtotal (<?= $item_count ?> items):</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span><?= $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2) ?></span>
                    </div>
                    <div class="total-row">
                        <span>Tax:</span>
                        <span>$<?= number_format($tax, 2) ?></span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total:</span>
                        <span>$<?= number_format($total, 2) ?></span>
                    </div>
                </div>

                <div class="action-buttons">
                    <a href="products.php" class="btn-action btn-primary-action">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                    <a href="view_all_order.php" class="btn-action btn-outline-action">
                        <i class="fas fa-list me-2"></i>View All Orders
                    </a>
                    <button onclick="window.print()" class="btn-action btn-outline-action">
                        <i class="fas fa-print me-2"></i>Print Receipt
                    </button>
                </div>

                <div class="print-only mt-4">
                    <p class="text-center text-muted">Thank you for your purchase! | Galaxy The Gadget</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
// Create confetti effect
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.confirmation-header');
    for (let i = 0; i < 50; i++) {
        createConfetti(header);
    }
});

function createConfetti(container) {
    const confetti = document.createElement('div');
    confetti.className = 'confetti';
    
    // Random position
    const left = Math.random() * 100;
    confetti.style.left = left + '%';
    
    // Random size
    const size = Math.random() * 10 + 5;
    confetti.style.width = size + 'px';
    confetti.style.height = size + 'px';
    
    // Random color
    const colors = ['#3a36e7', '#ff4d8d', '#2ecc71', '#f1c40f', '#9b59b6'];
    const randomColor = colors[Math.floor(Math.random() * colors.length)];
    confetti.style.background = randomColor;
    
    // Random animation delay
    const delay = Math.random() * 2;
    confetti.style.animationDelay = delay + 's';
    
    container.appendChild(confetti);
    
    // Remove confetti after animation completes
    setTimeout(() => {
        confetti.remove();
    }, 5000);
}
</script>
</body>
</html>