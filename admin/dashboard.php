<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<style>
    /* Content Styles */
    .content {
        flex: 1;
        padding: 2rem;
        margin-left: var(--sidebar-width);
        transition: var(--transition);
    }

    .dashboard-title {
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        color: var(--dark);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .welcome-text {
        background: white;
        padding: 1.8rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 2rem;
        transition: var(--transition);
    }

    .welcome-text:hover {
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.8rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        display: flex;
        align-items: center;
        gap: 1.2rem;
        transition: var(--transition);
        cursor: pointer;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 65px;
        height: 65px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        color: white;
        transition: var(--transition);
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1);
    }

    .stat-icon.products {
        background: linear-gradient(135deg, var(--primary), var(--secondary));
    }

    .stat-icon.users {
        background: linear-gradient(135deg, var(--success), #3a86ff);
    }

    .stat-icon.orders {
        background: linear-gradient(135deg, var(--warning), #b5179e);
    }

    .stat-icon.revenue {
        background: linear-gradient(135deg, var(--danger), #ff6b6b);
    }

    .stat-info h3 {
        font-size: 1.8rem;
        margin-bottom: 0.3rem;
        color: var(--dark);
    }

    .stat-info p {
        color: var(--gray);
        font-size: 0.9rem;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .action-btn {
        background: white;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.8rem;
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
        color: var(--dark);
    }

    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px极狐 25px rgba(0, 0, 0, 0.1);
        color: var(--primary);
    }

    .action-btn i {
        font-size: 1.8rem;
        color: var(--primary);
    }

    .action-btn span {
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .stat-card, .welcome-text, .action-btn {
        animation: fadeIn 0.5s ease-out;
    }

    .stat-card:nth-child(2) { animation-delay: 0.1s; }
    .stat-card:nth-child(3) { animation-delay: 0.2s; }
    .stat-card:nth-child(4) { animation-delay: 0.3s; }

    /* Responsive Design for Content */
    @media (max-width: 992px) {
        .content {
            margin-left: 70px;
        }
        
        .stats-container {
            grid-template-columns: 1fr 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: 1fr;
        }
        
        .quick-actions {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .content {
            margin-left: 0;
            padding: 1.5rem;
        }
    }
</style>

<h1 class="dashboard-title"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>

<div class="welcome-text">
    <h2>Welcome Admin, <?php echo $_SESSION["name"]; ?> 👋</h2>
    <p>Use the menu to manage products, users, and orders. Here's an overview of your store performance.</p>
</div>

<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon products">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h3>542</h3>
            <p>Total Products</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon users">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>1,248</h3>
            <p>Registered Users</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orders">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h3>356</h3>
            <p>Pending Orders</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon revenue">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <h3>$24,582</h3>
            <p>Total Revenue</p>
        </div>
    </div>
</div>

<div class="quick-actions">
    <a href="#" class="action-btn">
        <i class="fas fa-plus-circle"></i>
        <span>Add Product</span>
    </a>
    <a href="#" class="action-btn">
        <i class="fas fa-user-plus"></i>
        <span>Add User</span>
    </a>
    <a href="#" class="action-btn">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>View Orders</span>
    </a>
    <a href="#" class="action-btn">
        <i class="fas fa-chart-line"></i>
        <span>View Reports</span>
    </a>
</div>

<div class="welcome-text">
    <h3>Recent Activity</h3>
    <p>You have 12 new orders, 3 new user registrations, and 5 product reviews waiting for approval.</p>
</div>

<?php include 'footer.php'; ?>