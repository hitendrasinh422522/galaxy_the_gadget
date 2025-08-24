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
                        <div class="category-title">
                            <h5>Smartphones</h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Laptop Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('laptops')">
                    <div class="category-bg" style="background-image: url('https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');">
                        <div class="category-title">
                            <h5>Laptops</h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Smartwatch Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('smartwatches')">
                    <div class="category-bg" style="background-image: url('https://m.media-amazon.com/images/I/61zNIEb0T5L.jpg');">
                        <div class="category-title">
                            <h5>Smartwatches</h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Smart Light Category -->
            <div class="col-md-3 col-6">
                <div class="card category-card" onclick="showProducts('smartlights')">
                    <div class="category-bg" style="background-image: url('https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60');">
                        <div class="category-title">
                            <h5>Smart Lights</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Container (Initially hidden) -->
    <div class="container" id="productsContainer">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 id="categoryHeading">Smartphones</h2>
            <button class="btn btn-outline-secondary" onclick="backToCategories()">
                <i class="fas fa-arrow-left me-2"></i>Back to Categories
            </button>
        </div>

        <!-- Category Navigation -->
        <ul class="nav nav-tabs mb-4" id="categoryNav">
            <li class="nav-item">
                <a class="nav-link active-category" id="phones-tab" onclick="filterProducts('phones')">Phones</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="laptops-tab" onclick="filterProducts('laptops')">Laptops</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="smartwatches-tab" onclick="filterProducts('smartwatches')">Smartwatches</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="smartlights-tab" onclick="filterProducts('smartlights')">Smart Lights</a>
            </li>
        </ul>

        <!-- Filter Section -->
        <div class="row mb-4">
            <div class="col-md-3">
                <select class="form-select" id="sortSelect">
                    <option>Sort by: Popularity</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Newest First</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Search in category...">
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row g-4" id="productsGrid">
            <!-- Products will be dynamically inserted here -->
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample product data
        const products = {
    phones: [
        { id: 1, name: "iPhone 15 Pro", desc: "6.1\" Super Retina XDR, A17 Pro, 128GB", price: "₹89,999", rating: 4.5, image: "https://i.ytimg.com/vi/CREM-mFuyyo/hq720.jpg?sqp=-oaymwEhCK4FEIIDSFryq4qpAxMIARUAAAAAGAElAADIQj0AgKJD&rs=AOn4CLB6WA-0L8c5lu2OMH5V2bxFi7WXQQ" },
        { id: 2, name: "Samsung Galaxy S23", desc: "6.2\" AMOLED, Snapdragon 8 Gen 2, 256GB", price: "₹74,999", rating: 4.3, image: "https://images.samsung.com/is/image/samsung/p6pim/in/sm-s711blgbins/gallery/in-galaxy-s23-fe-s711-479553-sm-s711blgbins-538355944?$684_547_PNG$" },
        { id: 3, name: "OnePlus 11 5G", desc: "6.7\" Fluid AMOLED, Snapdragon 8 Gen 2, 128GB", price: "₹56,999", rating: 4.6, image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLr31uh6fVDlsQQhpXMLowNlRkXnPg5zrg0Q&s" },
        { id: 4, name: "Google Pixel 7 Pro", desc: "6.7\" OLED, Tensor G2, 128GB", price: "₹79,999", rating: 4.4, image: "https://m.media-amazon.com/images/I/51OFxuD1GgL.jpg" },
        { id: 5, name: "Xiaomi Redmi Note 11T 5G", desc: "6.6\" FHD+ Dot Display, Dimensity 810, 128GB", price: "₹19,999", rating: 4.5, image: "https://www.bewakoof.com/blog/wp-content/uploads/2022/09/Xiaomi-Redmi-Note-11T-5G.jpg" },
        { id: 6, name: "Moto g85 5g", desc: "6.5\" 3D Curved Display, Snapdragon 6s Gen 3 Processor, 64GB", price: "₹15,999", rating: 4.4, image: "https://m.media-amazon.com/images/I/61wo1ca9wfL._UF1000,1000_QL80_.jpg" },
        { id: 7, name: "Vivo v27 5g", desc: "6.7\" AMOLED Display, 128GB", price: "₹29,999", rating: 4.5, image: "https://www.myg.in/images/detailed/92/image-removebg-preview_-_2024-11-01T120246.193.png" },
        { id: 8, name: "One plus nord2 5g", desc: "6.7\" OLED, Tensor G2, 128GB", price: "₹79,999", rating: 4.4, image: "https://m.media-amazon.com/images/I/51OFxuD1GgL.jpg" }
    ],
    laptops: [
        { id: 9, name: "MacBook Pro 14\"", desc: "M2 Pro chip, 16GB RAM, 512GB SSD", price: "₹1,59,999", rating: 4.8, image: "https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 10, name: "Dell XPS 15", desc: "Intel i7, 16GB RAM, 1TB SSD, RTX 3050", price: "₹1,39,999", rating: 4.6, image: "https://images.unsplash.com/photo-1593642632823-8f785ba67e45?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 11, name: "Acer Aspire 7 Gaming Laptop", desc: "Acer Aspire 7 Intel Core i5 12th Gen", price: "₹56,990", rating: 4.5, image: "https://rukminim2.flixcart.com/image/1200/1200/xif0q/computer/e/e/l/-original-imahcd9qjff2phhz.jpeg" },
        { id: 12, name: "Lenovo LOQ 15ARP9 ", desc: "AMD Ryzen 5 Gaming Laptop 83JCXA", price: "₹66,490", rating: 4.5, image: "https://encrypted-tbn1.gstatic.com/shopping?q=tbn:ANd9GcRNcxgImUYs8pmrJ0I3HSoy4OsS40vLQfmWv3WcV3sfbfDrPj0hJZLI9tZAzBq5XbAlfIHCjlSgmhffYn01M-Aw9UkVBa3Z_UPU_d_b1wSptGCPqt3Qo3ay9g" },
        { id: 13, name: "ASUS Vivobook S14 (2025) ", desc: "Intel Core Ultra 7 255H", price: "₹82,990", rating: 4.5, image: "https://rukminim2.flixcart.com/image/312/312/xif0q/computer/6/5/g/-original-imahehkgvkzbqakw.jpeg?q=70" },
        { id: 14, name: "HP Victus, AMD Ryzen 5 5600H", desc: "NVIDIA RTX 3050, 16GB DDR4, 512GB SSD", price: "₹61,190", rating: 4.5, image: "https://m.media-amazon.com/images/I/71J2F-6lIPL._AC_UY327_FMwebp_QL65_.jpg" },
        { id: 15, name: "HP Spectre x360", desc: "Intel i7, 16GB RAM, 1TB SSD, 13.5\" 3K", price: "₹1,29,999", rating: 4.5, image: "https://images.unsplash.com/photo-1587202372775-e229f1725e0e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 16, name: "HP Spectre x360", desc: "Intel i7, 16GB RAM, 1TB SSD, 13.5\" 3K", price: "₹1,29,999", rating: 4.5, image: "https://images.unsplash.com/photo-1587202372775-e229f1725e0e?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" }
    ],
    smartwatches: [
        { id: 17, name: "Apple Watch Series 8", desc: "45mm, GPS + Cellular, Aluminum", price: "₹41,999", rating: 4.7, image: "https://images.unsplash.com/photo-1617627143750-d86bc21e42bb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 18, name: "Samsung Galaxy Watch 5", desc: "44mm, Bluetooth, Wear OS", price: "₹24,999", rating: 4.4, image: "https://images.unsplash.com/photo-1664478546384-d60aff7bf7c1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 19, name: "Fitbit Sense 2", desc: "Advanced health & fitness smartwatch", price: "₹29,999", rating: 4.2, image: "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 20, name: "Fitbit Sense 2", desc: "Advanced health & fitness smartwatch", price: "₹29,999", rating: 4.2, image: "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 21, name: "Fitbit Sense 2", desc: "Advanced health & fitness smartwatch", price: "₹29,999", rating: 4.2, image: "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 22, name: "Fitbit Sense 2", desc: "Advanced health & fitness smartwatch", price: "₹29,999", rating: 4.2, image: "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 23, name: "Fitbit Sense 2", desc: "Advanced health & fitness smartwatch", price: "₹29,999", rating: 4.2, image: "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 24, name: "Fitbit Sense 2", desc: "Advanced health & fitness smartwatch", price: "₹29,999", rating: 4.2, image: "https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" }
    ],
    smartlights: [
        { id: 25, name: "Philips Hue White & Color", desc: "Smart LED bulb, 16 million colors", price: "₹5,499", rating: 4.5, image: "https://images.unsplash.com/photo-1540932239986-30128078f3c5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 26, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 27, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 28, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 29, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 30, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 31, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" },
        { id: 32, name: "Wiz Connected Bulb", desc: "Wi-Fi smart bulb, RGBW, 9W", price: "₹2,199", rating: 4.1, image: "https://images.unsplash.com/photo-1517991104123-1d56a6e81ed9?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60" }
    ]
};


        // Show products for a specific category
        function showProducts(category) {
            document.getElementById('categorySelection').style.display = 'none';
            document.getElementById('productsContainer').style.display = 'block';
            
            // Update heading
            const heading = document.getElementById('categoryHeading');
            heading.textContent = category.charAt(0).toUpperCase() + category.slice(1).replace('s', '');
            if (category === 'smartwatches') heading.textContent = 'Smartwatches';
            
            // Highlight the active tab
            document.querySelectorAll('#categoryNav .nav-link').forEach(link => {
                link.classList.remove('active-category');
            });
            document.getElementById(`${category}-tab`).classList.add('active-category');
            
            // Display products
            filterProducts(category);
        }

        // Filter products by category
        function filterProducts(category) {
            const productsGrid = document.getElementById('productsGrid');
            productsGrid.innerHTML = '';
            
            products[category].forEach(product => {
                const productHTML = `
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="card product-card h-100">
                            <img src="${product.image}" class="card-img-top p-3" alt="${product.name}" style="height: 200px; object-fit: contain;">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="text-muted">${product.desc}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="text-primary">${product.price}</h5>
                                    <span class="badge bg-success">${product.rating} ★</span>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <button class="btn btn-outline-primary w-100">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                `;
                productsGrid.innerHTML += productHTML;
            });
        }

        // Go back to category selection
        function backToCategories() {
            document.getElementById('categorySelection').style.display = 'block';
            document.getElementById('productsContainer').style.display = 'none';
        }
    </script>
</body>
</html>