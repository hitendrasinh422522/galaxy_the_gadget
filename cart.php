<?php
session_start();
require_once 'config/database.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = (new Database())->connect();
$error = '';
$success = '';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;
    
    // Fetch product details
    $stmt = $db->prepare("SELECT id, name, price, image FROM products WHERE id = :id");
    $stmt->bindParam(":id", $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }
        $success = "Product added to cart!";
    } else {
        $error = "Product not found!";
    }
}

// Handle cart quantity updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    $success = "Cart updated successfully!";
}

// Handle remove item from cart
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $success = "Item removed from cart!";
    }
}

// Calculate cart totals
$subtotal = 0;
$total_items = 0;

foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
    $total_items += $item['quantity'];
}

// Shipping and tax calculation
$shipping = $subtotal > 0 ? 5.00 : 0; // Flat rate shipping
$tax = $subtotal * 0.08; // 8% tax
$total = $subtotal + $shipping + $tax;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Galaxy The Gadget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #a29bfe;
            --dark-color: #2d3436;
            --light-color: #f5f6fa;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .cart-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .cart-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
        }
        
        .cart-item {
            border-bottom: 1px solid #eee;
            padding: 20px 0;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .quantity-input {
            width: 60px;
            text-align: center;
        }
        
        .summary-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
        }
        
        .btn-primary:hover {
            background: var(--secondary-color);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px 0;
        }
        
        .empty-cart-icon {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="cart-container">
                <div class="cart-header">
                    <h2><i class="fas fa-shopping-cart me-2"></i>Your Shopping Cart</h2>
                </div>
                
                <div class="p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= htmlspecialchars($success) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($_SESSION['cart'])): ?>
                        <div class="empty-cart">
                            <div class="empty-cart-icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3>Your cart is empty</h3>
                            <p>Looks like you haven't added any items to your cart yet.</p>
                            <a href="products.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-lg-8">
                                <form method="post" action="cart.php">
                                    <?php foreach ($_SESSION['cart'] as $item): ?>
                                        <div class="cart-item">
                                            <div class="row align-items-center">
                                                <div class="col-md-2">
                                                    <img src="uploads/products/<?= htmlspecialchars($item['image']) ?>" 
                                                         alt="<?= htmlspecialchars($item['name']) ?>" class="product-img">
                                                </div>
                                                <div class="col-md-4">
                                                    <h5><?= htmlspecialchars($item['name']) ?></h5>
                                                    <p class="text-muted mb-0">$<?= number_format($item['price'], 2) ?></p>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <input type="number" name="quantities[<?= $item['id'] ?>]" 
                                                               value="<?= $item['quantity'] ?>" min="1" 
                                                               class="form-control quantity-input">
                                                        <a href="cart.php?remove=<?= $item['id'] ?>" 
                                                           class="btn btn-outline-danger" title="Remove">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 text-end">
                                                    <h5>$<?= number_format($item['price'] * $item['quantity'], 2) ?></h5>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="products.php" class="btn btn-outline-primary">
                                            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                                        </a>
                                        <button type="submit" name="update_cart" class="btn btn-primary">
                                            <i class="fas fa-sync-alt me-2"></i>Update Cart
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="col-lg-4 mt-4 mt-lg-0">
                                <div class="summary-card">
                                    <h4 class="mb-4">Order Summary</h4>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal (<?= $total_items ?> items)</span>
                                        <span>$<?= number_format($subtotal, 2) ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping</span>
                                        <span>$<?= number_format($shipping, 2) ?></span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Tax</span>
                                        <span>$<?= number_format($tax, 2) ?></span>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-4">
                                        <h5>Total</h5>
                                        <h5>$<?= number_format($total, 2) ?></h5>
                                    </div>
                                    
                                    <a href="checkout.php" class="btn btn-primary w-100 py-2">
                                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>