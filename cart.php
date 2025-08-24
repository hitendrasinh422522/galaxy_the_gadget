<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $quantity = $_POST['quantity'] ?? 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If product exists, increase quantity
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product_id) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    unset($item);

    // Otherwise add new
    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $product_id,
            'name' => $product_name,
            'price' => $product_price,
            'image' => $product_image,
            'quantity' => $quantity
        ];
    }

    header("Location: cart.php");
    exit();
}

// Handle Remove
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    $_SESSION['cart'] = array_filter($_SESSION['cart'], function($item) use ($remove_id) {
        return $item['id'] != $remove_id;
    });
    header("Location: cart.php");
    exit();
}

// Handle Empty
if (isset($_GET['empty'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Shopping Cart - Galaxy The Gadget</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <style>
    :root {
      --primary: #6c63ff;
      --secondary: #ff6584;
      --dark: #2a2a72;
      --light: #f8f9fa;
      --gray: #8a8a8a;
    }
    
    body {
      background-color: #f9fafb;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .cart-header {
      background: linear-gradient(135deg, var(--dark) 0%, #4a4ac4 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .cart-item {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      border-left: 4px solid var(--primary);
    }
    
    .cart-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .product-img {
      width: 100px;
      height: 100px;
      object-fit: contain;
      border-radius: 10px;
      padding: 10px;
      background: var(--light);
    }
    
    .item-name {
      font-weight: 600;
      color: var(--dark);
      font-size: 1.1rem;
    }
    
    .item-price {
      font-weight: 700;
      color: var(--primary);
    }
    
    .quantity-badge {
      background: var(--light);
      color: var(--dark);
      font-weight: 600;
      padding: 0.5rem 1rem;
      border-radius: 8px;
    }
    
    .remove-btn {
      background: rgba(255, 101, 132, 0.1);
      color: var(--secondary);
      border: none;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
    }
    
    .remove-btn:hover {
      background: var(--secondary);
      color: white;
    }
    
    .summary-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 2rem;
    }
    
    .summary-title {
      color: var(--dark);
      font-weight: 700;
      border-bottom: 2px solid var(--light);
      padding-bottom: 1rem;
      margin-bottom: 1.5rem;
    }
    
    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
    }
    
    .grand-total {
      font-weight: 800;
      font-size: 1.3rem;
      color: var(--primary);
      border-top: 2px solid var(--light);
      padding-top: 1rem;
    }
    
    .empty-cart {
      text-align: center;
      padding: 4rem 2rem;
    }
    
    .empty-cart i {
      font-size: 5rem;
      color: #e0e0e0;
      margin-bottom: 1.5rem;
    }
    
    .empty-cart p {
      color: var(--gray);
      font-size: 1.2rem;
      margin-bottom: 2rem;
    }
    
    .action-btn {
      padding: 0.8rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-checkout {
      background: var(--primary);
      color: white;
      width: 100%;
      padding: 1rem;
      font-size: 1.1rem;
    }
    
    .btn-checkout:hover {
      background: #554fd8;
      transform: translateY(-2px);
    }
    
    .btn-continue {
      background: white;
      color: var(--primary);
      border: 2px solid var(--primary);
    }
    
    .btn-continue:hover {
      background: var(--light);
    }
    
    .btn-empty {
      background: rgba(255, 101, 132, 0.1);
      color: var(--secondary);
      border: 2px solid transparent;
    }
    
    .btn-empty:hover {
      background: var(--secondary);
      color: white;
    }
    
    .cart-icon {
      background: rgba(255, 255, 255, 0.2);
      width: 60px;
      height: 60px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem;
    }
  </style>
</head>
<body>
<?php include('includes/header.php'); ?>

<div class="cart-header">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="display-5 fw-bold"><i class="fas fa-shopping-cart me-3"></i>Your Shopping Cart</h1>
        <p class="lead">Review and manage your gadgets before checkout</p>
      </div>
      <div class="col-md-4 text-md-end">
        <div class="cart-icon">
          <i class="fas fa-shopping-basket fa-2x"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <div class="row">
    <div class="col-lg-8">
      <?php if (!empty($_SESSION['cart'])): ?>
        <?php foreach ($_SESSION['cart'] as $item): 
          $total = $item['price'] * $item['quantity'];
        ?>
          <div class="cart-item">
            <div class="row align-items-center">
              <div class="col-3 col-md-2">
                <img src="<?= $item['image'] ?>" class="product-img img-fluid" alt="<?= htmlspecialchars($item['name']) ?>">
              </div>
              <div class="col-9 col-md-4">
                <h3 class="item-name"><?= htmlspecialchars($item['name']) ?></h3>
                <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
              </div>
              <div class="col-6 col-md-3">
                <div class="d-flex align-items-center">
                  <span class="quantity-badge">Qty: <?= $item['quantity'] ?></span>
                </div>
              </div>
              <div class="col-4 col-md-2">
                <div class="fw-bold text-dark">$<?= number_format($total, 2) ?></div>
              </div>
              <div class="col-2 col-md-1 text-end">
                <a href="cart.php?remove=<?= $item['id'] ?>" class="remove-btn" title="Remove item">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        
        <div class="d-flex justify-content-between mt-4">
          <a href="cart.php?empty=1" class="btn btn-empty action-btn">
            <i class="fas fa-trash-alt me-2"></i>Empty Cart
          </a>
          <a href="product_detail.php"  class="btn btn-continue action-btn">
            <i class="fas fa-arrow-left me-2"></i>Continue Shopping
          </a>
        </div>
      <?php else: ?>
        <div class="empty-cart">
          <i class="fas fa-shopping-cart"></i>
          <h3 class="text-muted">Your cart is empty</h3>
          <p>Looks like you haven't added any gadgets to your cart yet.</p>
          <a href="product_detail.php" class="btn btn-primary btn-lg action-btn">
            <i class="fas fa-shopping-bag me-2"></i>Start Shopping
          </a>
        </div>
      <?php endif; ?>
    </div>
    
    <?php if (!empty($_SESSION['cart'])): ?>
    <div class="col-lg-4 mt-4 mt-lg-0">
      <div class="summary-card">
        <h3 class="summary-title">Order Summary</h3>
        
        <?php 
        $grand_total = 0;
        $item_count = 0;
        foreach ($_SESSION['cart'] as $item): 
          $total = $item['price'] * $item['quantity'];
          $grand_total += $total;
          $item_count += $item['quantity'];
        ?>
        <?php endforeach; ?>
        
        <div class="summary-item">
          <span>Items (<?= $item_count ?>):</span>
          <span>$<?= number_format($grand_total, 2) ?></span>
        </div>
        
        <div class="summary-item">
          <span>Shipping:</span>
          <span><?= $grand_total > 50 ? 'FREE' : '$9.99' ?></span>
        </div>
        
        <div class="summary-item">
          <span>Tax:</span>
          <span>$<?= number_format($grand_total * 0.08, 2) ?></span>
        </div>
        
        <?php 
        $shipping = $grand_total > 50 ? 0 : 9.99;
        $tax = $grand_total * 0.08;
        $final_total = $grand_total + $shipping + $tax;
        ?>
        
        <div class="summary-item grand-total">
          <span>Total:</span>
          <span>$<?= number_format($final_total, 2) ?></span>
        </div>
        
        <div class="mt-4">
          <a href=checkout.php class="btn btn-checkout action-btn">
            Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div>
        
        <div class="mt-3 text-center">
          <small class="text-muted">
            <i class="fas fa-lock me-1"></i> Your payment information is secure and encrypted
          </small>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
  // Add subtle animations
  document.addEventListener('DOMContentLoaded', function() {
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
      item.style.animationDelay = `${index * 0.1}s`;
      item.classList.add('animate__animated', 'animate__fadeInUp');
    });
  });
</script>
</body>
</html>