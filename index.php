<?php include('includes/header.php'); ?>


<!-- You should link CSS file, not index.php -->
<link rel="stylesheet" href="css/index1.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<div class="container mt-4">
  <div class="text-center mb-5">
    <h1 class="display-5">Welcome to Galaxy The Gadget</h1>
    <p class="lead">Your one-stop shop for smart gadgets – Smartwatches, Earbuds, Smart Lights, and more!</p>
  </div>

  <h3 class="mb-4">Featured Products</h3>
  <div class="row row-cols-1 row-cols-md-3 g-4">

    <!-- Product 1 -->
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="img/SW1-Pro_Silver_Main.webp" class="card-img-top" alt="Smartwatch">
        <div class="card-body">
          <h5 class="card-title">Smartwatch X1</h5>
          <p class="card-text">Track your health in style with the Smartwatch X1.</p>
          <a href="product_detail.php?id=1" class="btn btn-primary">View Details</a>
        </div>
      </div>
    </div>

    <!-- Product 2 -->
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="img/Cadence-grey.webp" class="card-img-top" alt="Earbuds">
        <div class="card-body">
          <h5 class="card-title">Wireless Earbuds Z3</h5>
          <p class="card-text">Experience true wireless freedom with crystal-clear sound.</p>
          <a href="product_detail.php?id=2" class="btn btn-primary">View Details</a>
        </div>
      </div>
    </div>

    <!-- Product 3 -->
    <div class="col">
      <div class="card h-100 shadow-sm">
        <img src="img/Luma_feat.webp" class="card-img-top" alt="Smart Light">
        <div class="card-body">
          <h5 class="card-title">Smart Light Luma</h5>
          <p class="card-text">Control your lights remotely and set the perfect mood.</p>
          <a href="product_detail.php?id=3" class="btn btn-primary">View Details</a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include('includes/footer.php'); ?>
