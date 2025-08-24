<style>
    /* Footer Styles */
    .footer {
        background: white;
        padding: 1.5rem 2rem;
        text-align: center;
        box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.05);
        margin-top: auto;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-links {
        display: flex;
        gap: 1.5rem;
    }

    .footer-links a {
        color: var(--gray);
        text-decoration: none;
        transition: color 0.3s;
        font-size: 0.9rem;
    }

    .footer-links a:hover {
        color: var(--primary);
    }

    .copyright {
        color: var(--gray);
        font-size: 0.9rem;
    }

    /* Responsive Design for Footer */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            gap: 1rem;
        }
    }
</style>

</main>
</div>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="copyright">
            &copy; 2023 AdminPanel. All rights reserved.
        </div>
        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Help Center</a>
            <a href="#">Contact Us</a>
        </div>
    </div>
</footer>

<script>
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
    });
    
    sidebarOverlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });
    
    // Update active menu item
    document.addEventListener('DOMContentLoaded', function() {
        const menuItems = document.querySelectorAll('.menu-item');
        const currentPage = window.location.pathname.split('/').pop();
        
        menuItems.forEach(item => {
            if (item.getAttribute('href') === currentPage) {
                item.classList.add('active');
            }
            
            item.addEventListener('click', function() {
                menuItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                // On mobile, close sidebar after selection
                if (window.innerWidth <= 576) {
                    sidebar.classList.remove('active');
                    sidebarOverlay.classList.remove('active');
                }
            });
        });
    });
</script>
</body>
</html>