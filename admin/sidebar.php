<style>
    /* Sidebar Styles */
    .sidebar {
        width: var(--sidebar-width);
        background: white;
        box-shadow: var(--box-shadow);
        padding: 1.5rem 0;
        position: fixed;
        height: calc(100vh - var(--header-height));
        overflow-y: auto;
        transition: var(--transition);
        z-index: 999;
    }

    .sidebar-menu {
        list-style: none;
    }

    .menu-item {
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--gray);
        text-decoration: none;
        transition: var(--transition);
        font-weight: 500;
        border-left: 4px solid transparent;
    }

    .menu-item:hover, .menu-item.active {
        background: var(--primary-light);
        color: var(--primary);
        border-left: 4px solid var(--primary);
    }

    .menu-item i {
        font-size: 1.2rem;
        width: 25px;
    }

    /* Responsive Design for Sidebar */
    @media (max-width: 992px) {
        .sidebar {
            width: 70px;
            overflow: hidden;
        }
        
        .menu-item span {
            display: none;
        }
    }

    @media (max-width: 576px) {
        .sidebar {
            transform: translateX(-100%);
            width: var(--sidebar-width);
        }
        
        .sidebar.active {
            transform: translateX(0);
        }
        
        .menu-item span {
            display: inline;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: var(--header-height);
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
    }
</style>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="menu-item active"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
        <li><a href="products.php" class="menu-item"><i class="fas fa-box"></i> <span>Products</span></a></li>
        <li><a href="users.php" class="menu-item"><i class="fas fa-users"></i> <span>Users</span></a></li>
        <li><a href="orders.php" class="menu-item"><i class="fas fa-shopping-cart"></i> <span>Orders</span></a></li>
        <li><a href="analytics.php" class="menu-item"><i class="fas fa-chart-bar"></i> <span>Analytics</span></a></li>
        <li><a href="settings.php" class="menu-item"><i class="fas fa-cog"></i> <span>Settings</span></a></li>
        <li><a href="logout.php" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
    </ul>
</aside>

<!-- Content Area -->
<main class="content">