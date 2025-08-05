<?php
include('includes/header.php');
?>
<link rel="stylesheet" href="/galaxy_the_gadget/css/home.css">

<div class="container my-4">
  <!-- Hero Section -->
  <div class="p-5 mb-4 bg-light rounded-3 shadow-sm">
    <div class="container-fluid py-5">
      <h1 class="display-5 fw-bold">Welcome to Galaxy The Gadget</h1>
      <p class="col-md-8 fs-4">Discover the latest smartwatches, earbuds, smart lights, and more!</p>
      <a href="product_detail.php" class="btn btn-primary btn-lg">Shop Now</a>
    </div>
  </div>

  <!-- Product Grid -->
  <h2 class="mb-4">Featured Products</h2>
  <div class="row row-cols-1 row-cols-md-3 g-4">

    <!-- Product 1 -->
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="\galaxy_the_gadget\img\SW1-Pro_Silver_Main.webp" class="card-img-top" alt="Smartwatch">
        <div class="card-body">
          <h5 class="card-title">Smartwatch Pro</h5>
          <p class="card-text">Track your fitness, get notifications and much more on your wrist.</p>
          <p class="fw-bold">₹4,999</p>
          <a href="product_detail.php?id=1" class="btn btn-outline-primary">View Details</a>
        </div>
      </div>
    </div>

    <!-- Product 2 -->
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="../assets/images/earbuds.jpg" class="card-img-top" alt="Earbuds">
        <div class="card-body">
          <h5 class="card-title">Galaxy Earbuds</h5>
          <p class="card-text">Experience premium sound quality with noise cancellation.</p>
          <p class="fw-bold">₹2,499</p>
          <a href="product_detail.php?id=2" class="btn btn-outline-primary">View Details</a>
        </div>
      </div>
    </div>

    <!-- Product 3 -->
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="../assets/images/smartlight.jpg" class="card-img-top" alt="Smart Light">
        <div class="card-body">
          <h5 class="card-title">Smart Light Bulb</h5>
          <p class="card-text">Control your lights with your phone or voice assistant.</p>
          <p class="fw-bold">₹999</p>
          <a href="product_detail.php?id=3" class="btn btn-outline-primary">View Details</a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php
include('includes/footer.php');
?>
