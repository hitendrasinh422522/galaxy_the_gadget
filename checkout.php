<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config/database.php";  // include Database class

$db = new Database();
$conn = $db->connect();  // now $conn is a PDO object

// Redirect if cart empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Calculate totals
$grand_total = 0;
$item_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $total = $item['price'] * $item['quantity'];
    $grand_total += $total;
    $item_count += $item['quantity'];
}
$shipping = $grand_total > 50 ? 0 : 9.99;
$tax = $grand_total * 0.08;
$final_total = $grand_total + $shipping + $tax;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zip = $_POST['zip'];
    $paymentMethod = $_POST['paymentMethod'];
    $user_id = $_SESSION['user_id'] ?? 0; // assuming you store user_id in session

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders 
        (user_id, first_name, last_name, email, phone, address, city, state, zip, payment_method, total) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $user_id, $firstName, $lastName, $email, $phone,
        $address, $city, $state, $zip, $paymentMethod, $final_total
    ]);
    $order_id = $conn->lastInsertId();

    // Insert order items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($_SESSION['cart'] as $item) {
        $stmt_item->execute([$order_id, $item['name'], $item['quantity'], $item['price']]);
    }

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to confirmation page
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Galaxy The Gadget</title>
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
    
    .checkout-header {
      background: linear-gradient(135deg, var(--dark) 0%, #4a4ac4 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .checkout-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 2rem;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      border-left: 4px solid var(--primary);
    }
    
    .checkout-title {
      color: var(--dark);
      font-weight: 700;
      border-bottom: 2px solid var(--light);
      padding-bottom: 1rem;
      margin-bottom: 1.5rem;
      display: flex;
      align-items: center;
    }
    
    .checkout-title i {
      margin-right: 10px;
      background: var(--primary);
      color: white;
      width: 36px;
      height: 36px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    
    .form-label {
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }
    
    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 10px;
      border: 2px solid #e2e8f0;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.25rem rgba(108, 99, 255, 0.25);
    }
    
    .payment-option {
      border: 2px solid #e2e8f0;
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .payment-option:hover {
      border-color: var(--primary);
    }
    
    .payment-option.selected {
      border-color: var(--primary);
      background-color: rgba(108, 99, 255, 0.05);
    }
    
    .payment-option input[type="radio"] {
      margin-right: 10px;
    }
    
    .order-summary {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
      position: sticky;
      top: 2rem;
    }
    
    .order-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #f1f1f1;
    }
    
    .order-item:last-child {
      border-bottom: none;
    }
    
    .order-item-name {
      font-weight: 500;
    }
    
    .order-item-quantity {
      color: var(--gray);
      font-size: 0.9rem;
    }
    
    .order-total {
      font-weight: 800;
      font-size: 1.3rem;
      color: var(--primary);
      border-top: 2px solid var(--light);
      padding-top: 1rem;
      margin-top: 1rem;
    }
    
    .btn-place-order {
      background: var(--primary);
      color: white;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1.1rem;
      width: 100%;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
    }
    
    .btn-place-order:hover {
      background: #554fd8;
      transform: translateY(-2px);
    }
    
    .secure-notice {
      text-align: center;
      margin-top: 1rem;
    }
    
    .step-progress {
      display: flex;
      justify-content: space-between;
      margin-bottom: 2rem;
      position: relative;
    }
    
    .step-progress::before {
      content: '';
      position: absolute;
      top: 20px;
      left: 0;
      right: 0;
      height: 4px;
      background: #e2e8f0;
      z-index: 1;
    }
    
    .step {
      position: relative;
      z-index: 2;
      text-align: center;
      width: 33.33%;
    }
    
    .step-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: white;
      border: 4px solid #e2e8f0;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 0.5rem;
      font-weight: bold;
      color: var(--gray);
    }
    
    .step.active .step-icon {
      background: var(--primary);
      border-color: var(--primary);
      color: white;
    }
    
    .step.completed .step-icon {
      background: var(--primary);
      border-color: var(--primary);
      color: white;
    }
    
    .step.completed .step-icon::after {
      content: '✓';
    }
    
    .step-label {
      font-size: 0.9rem;
      color: var(--gray);
      font-weight: 500;
    }
    
    .step.active .step-label {
      color: var(--primary);
      font-weight: 600;
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

<div class="checkout-header">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="display-5 fw-bold"><i class="fas fa-check-circle me-3"></i>Checkout</h1>
        <p class="lead">Complete your purchase securely</p>
      </div>
      <div class="col-md-4 text-md-end">
        <div class="cart-icon">
          <i class="fas fa-credit-card fa-2x"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container py-4">
  <!-- Progress Steps -->
  <div class="row mb-5">
    <div class="col-12">
      <div class="step-progress">
        <div class="step completed">
          <div class="step-icon"></div>
          <div class="step-label">Cart</div>
        </div>
        <div class="step active">
          <div class="step-icon">2</div>
          <div class="step-label">Checkout</div>
        </div>
        <div class="step">
          <div class="step-icon">3</div>
          <div class="step-label">Confirmation</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <form action="checkout.php" method="POST" id="checkout-form">
        <!-- Contact Information -->
        <div class="checkout-card">
          <h3 class="checkout-title"><i class="fas fa-user"></i> Contact Information</h3>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="firstName" class="form-label">First Name</label>
              <input type="text" class="form-control" id="firstName" name="firstName" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="lastName" class="form-label">Last Name</label>
              <input type="text" class="form-control" id="lastName" name="lastName" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" class="form-control" id="phone" name="phone" required>
          </div>
        </div>

        <!-- Shipping Address -->
        <div class="checkout-card">
          <h3 class="checkout-title"><i class="fas fa-truck"></i> Shipping Address</h3>
          <div class="mb-3">
            <label for="address" class="form-label">Street Address</label>
            <input type="text" class="form-control" id="address" name="address" required>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <label for="city" class="form-label">City</label>
              <input type="text" class="form-control" id="city" name="city" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="state" class="form-label">State</label>
              <input type="text" class="form-control" id="state" name="state" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="zip" class="form-label">ZIP Code</label>
              <input type="text" class="form-control" id="zip" name="zip" required>
            </div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="billingSameAsShipping">
            <label class="form-check-label" for="billingSameAsShipping">
              Billing address same as shipping
            </label>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="checkout-card">
          <h3 class="checkout-title"><i class="fas fa-credit-card"></i> Payment Method</h3>
          
          <div class="payment-option selected">
            <input type="radio" id="creditCard" name="paymentMethod" value="creditCard" checked>
            <label for="creditCard">Credit Card</label>
            
            <div class="mt-3 credit-card-form">
              <div class="row">
                <div class="col-12 mb-3">
                  <label for="cardNumber" class="form-label">Card Number</label>
                  <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="expiryDate" class="form-label">Expiry Date</label>
                  <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="cvv" class="form-label">CVV</label>
                  <input type="text" class="form-control" id="cvv" placeholder="123" required>
                </div>
                <div class="col-12 mb-3">
                  <label for="cardName" class="form-label">Name on Card</label>
                  <input type="text" class="form-control" id="cardName" required>
                </div>
              </div>
            </div>
          </div>
          
          <div class="payment-option">
            <input type="radio" id="paypal" name="paymentMethod" value="paypal">
            <label for="paypal">PayPal</label>
          </div>
          
          <div class="payment-option">
            <input type="radio" id="applePay" name="paymentMethod" value="applePay">
            <label for="applePay">Apple Pay</label>
          </div>
        </div>
      </form>
    </div>
    
    <div class="col-lg-4">
      <div class="order-summary">
        <h3 class="checkout-title"><i class="fas fa-receipt"></i> Order Summary</h3>
        
        <?php foreach ($_SESSION['cart'] as $item): ?>
          <div class="order-item">
            <div>
              <div class="order-item-name"><?= htmlspecialchars($item['name']) ?></div>
              <div class="order-item-quantity">Qty: <?= $item['quantity'] ?></div>
            </div>
            <div class="fw-bold">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
          </div>
        <?php endforeach; ?>
        
        <div class="order-item">
          <div>Subtotal</div>
          <div>$<?= number_format($grand_total, 2) ?></div>
        </div>
        
        <div class="order-item">
          <div>Shipping</div>
          <div><?= $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2) ?></div>
        </div>
        
        <div class="order-item">
          <div>Tax</div>
          <div>$<?= number_format($tax, 2) ?></div>
        </div>
        
        <div class="order-item order-total">
          <div>Total</div>
          <div>$<?= number_format($final_total, 2) ?></div>
        </div>
        
        <button type="submit" form="checkout-form" name="place_order" class="btn-place-order">
          Place Order <i class="fas fa-lock ms-2"></i>
        </button>
        
        <div class="secure-notice">
          <small class="text-muted">
            <i class="fas fa-lock me-1"></i> Your payment information is secure and encrypted
          </small>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Payment method selection
    const paymentOptions = document.querySelectorAll('.payment-option');
    paymentOptions.forEach(option => {
      option.addEventListener('click', () => {
        paymentOptions.forEach(opt => opt.classList.remove('selected'));
        option.classList.add('selected');
        const radio = option.querySelector('input[type="radio"]');
        radio.checked = true;
      });
    });
    
    // Form validation
    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', function(e) {
      let isValid = true;
      const requiredFields = form.querySelectorAll('[required]');
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          isValid = false;
          field.classList.add('is-invalid');
        } else {
          field.classList.remove('is-invalid');
        }
      });
      
      if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
      }
    });
  });
</script>
</body>
</html>