<?php include 'includes/header.php'; ?>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <div class="main-content-inner">
                <header class="main-header">
                    <button class="main-menu-btn" id="mobileMenuBtn">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <h1 class="main-title">Dashboard</h1>
                </header>

                <div class="main-body">
                    <div class="content-card">
                        <h2 class="content-title">Welcome to your Dashboard</h2>
                        <p class="content-text">
                            This is your main content area. The sidebar can be toggled and is fully responsive. On mobile devices,
                            it becomes an overlay, while on desktop it pushes the content.
                        </p>
                    </div>

                    <div class="content-grid">
                        <div class="content-card">
                            <h3 class="content-subtitle">Card 1</h3>
                            <p class="content-text">Sample content for demonstration.</p>
                        </div>
                        <div class="content-card">
                            <h3 class="content-subtitle">Card 2</h3>
                            <p class="content-text">More sample content here.</p>
                        </div>
                        <div class="content-card">
                            <h3 class="content-subtitle">Card 3</h3>
                            <p class="content-text">Additional content example.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    
<?php include 'includes/footer.php'; ?>
