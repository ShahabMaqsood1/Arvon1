<header class="admin-header">
    <div class="header-left">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
        <span class="admin-logo">ARVON Admin</span>
    </div>
    
    <div class="header-right">
        <span class="admin-username"><?php echo $_SESSION['admin_username']; ?></span>
        <a href="/admin/settings.php" class="header-btn" title="Settings">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="3"></circle>
                <path d="M12 1v6m0 6v6m5.5-13.5L16 7m-8-1.5L9.5 7M1 12h6m6 0h6m-13.5 5.5L7 16m10 1.5L15.5 16"></path>
            </svg>
        </a>
        <a href="?action=logout" class="header-btn" title="Logout">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </a>
    </div>
</header>
