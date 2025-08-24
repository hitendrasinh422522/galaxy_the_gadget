<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tech Products | GizmoNest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .category-card {
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            overflow: hidden;
            height: 100%;
        }
        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .category-bg {
            height: 180px;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: flex-end;
        }
        .category-title {
            background: rgba(0,0,0,0.7);
            color: white;
            width: 100%;
            padding: 15px;
            text-align: center;
        }
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .hero-section {
            background: linear-gradient(90deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 2rem;
        }
        .active-category {
            border-bottom: 3px solid #2575fc;
            font-weight: bold;
        }
        #productsContainer {
            display: none; /* Initially hidden until category is selected */
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1>Explore Our Tech Collection</h1>
            <p class="lead">Discover the latest gadgets and smart devices</p>
        </div>
    </section>

    <!-- Category Selection -->
    <div class="container mb-5" id="categorySelection">
        <h2 class="text-center mb-4">Browse Categories</h2>
        <div class="row g-4">
            <!-- Phone Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('phones')">
                    <div class="category-bg" style="background-image: url('https://images.unsplash.com/photo-1601784551446-20c9e07cdbdb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');">
                        <div class="category-title"><h5>Smartphones</h5></div>
                    </div>
                </div>
            </div>
            <!-- Laptop Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('laptops')">
                    <div class="category-bg" style="background-image: url('https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');">
                        <div class="category-title"><h5>Laptops</h5></div>
                    </div>
                </div>
            </div>
            <!-- Smartwatch Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('smartwatches')">
                    <div class="category-bg" style="background-image: url('https://m.media-amazon.com/images/I/61zNIEb0T5L.jpg');">
                        <div class="category-title"><h5>Smartwatches</h5></div>
                    </div>
                </div>
            </div>
            <!-- Smart Light Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('smartlights')">
                    <div class="category-bg" style="background-image: url('https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');">
                        <div class="category-title"><h5>Smart Lights</h5></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Container -->
    <div class="container" id="productsContainer">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 id="categoryHeading">Smartphones</h2>
            <button class="btn btn-outline-secondary" onclick="backToCategories()">
                <i class="fas fa-arrow-left me-2"></i>Back to Categories
            </button>
        </div>

        <!-- Category Navigation -->
        <ul class="nav nav-tabs mb-4" id="categoryNav">
            <li class="nav-item"><a class="nav-link active-category" id="phones-tab" onclick="filterProducts('phones')">Phones</a></li>
            <li class="nav-item"><a class="nav-link" id="laptops-tab" onclick="filterProducts('laptops')">Laptops</a></li>
            <li class="nav-item"><a class="nav-link" id="smartwatches-tab" onclick="filterProducts('smartwatches')">Smartwatches</a></li>
            <li class="nav-item"><a class="nav-link" id="smartlights-tab" onclick="filterProducts('smartlights')">Smart Lights</a></li>
        </ul>

        <!-- Products Grid -->
        <div class="row g-4" id="productsGrid"></div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Product Data (shortened for demo)
        const products = {
            phones: [
                { id: 1, name: "iPhone 15 Pro", desc: "6.1\" Super Retina XDR, A17 Pro, 128GB", price: 89999, rating: 4.5, image: "https://i.ytimg.com/vi/CREM-mFuyyo/hq720.jpg" },
                { id: 2, name: "Samsung Galaxy S23", desc: "6.2\" AMOLED, Snapdragon 8 Gen 2, 256GB", price: 74999, rating: 4.3, image: "https://images.samsung.com/is/image/samsung/p6pim/in/sm-s711blgbins/gallery/in-galaxy-s23-fe-s711-479553-sm-s711blgbins-538355944?$684_547_PNG$" }
            ],
            laptops: [
                { id: 3, name: "MacBook Pro 14\"", desc: "M2 Pro chip, 16GB RAM, 512GB SSD", price: 159999, rating: 4.8, image: "https://images.unsplash.com/photo-1611186871348-b1ce696e52c9" }
            ],
            smartwatches: [
                { id: 4, name: "Apple Watch Series 8", desc: "45mm, GPS + Cellular, Aluminum", price: 41999, rating: 4.7, image: "https://images.unsplash.com/photo-1617627143750-d86bc21e42bb" }
            ],
            smartlights: [
                { id: 5, name: "Philips Hue White & Color", desc: "Smart LED bulb, 16 million colors", price: 5499, rating: 4.5, image: "https://images.unsplash.com/photo-1540932239986-30128078f3c5" }
            ]
        };

        // Show category products
        function showProducts(category) {
            document.getElementById('categorySelection').style.display = 'none';
            document.getElementById('productsContainer').style.display = 'block';
            document.querySelectorAll('#categoryNav .nav-link').forEach(link => link.classList.remove('active-category'));
            document.getElementById(`${category}-tab`).classList.add('active-category');
            filterProducts(category);
        }

        // Render products with Add to Cart form
        function filterProducts(category) {
            const productsGrid = document.getElementById('productsGrid');
            productsGrid.innerHTML = '';
            products[category].forEach(product => {
                const productHTML = `
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="card product-card h-100">
                            <img src="${product.image}" class="card-img-top p-3" alt="${product.name}" style="height:200px; object-fit:contain;">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="text-muted">${product.desc}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="text-primary">₹${product.price}</h5>
                                    <span class="badge bg-success">${product.rating} ★</span>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="product_id" value="${product.id}">
                                    <input type="hidden" name="product_name" value="${product.name}">
                                    <input type="hidden" name="product_price" value="${product.price}">
                                    <input type="hidden" name="product_image" value="${product.image}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="btn btn-outline-primary w-100">Add to Cart</button>
                                </form>
                            </div>
                        </div>
                    </div>
                `;
                productsGrid.innerHTML += productHTML;
            });
        }

        function backToCategories() {
            document.getElementById('categorySelection').style.display = 'block';
            document.getElementById('productsContainer').style.display = 'none';
        }
    </script>
</body>
</html>
