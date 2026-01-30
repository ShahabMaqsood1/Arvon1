<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ARVON - Premium Apparel Manufacturing for Sportswear, Casual Wear, and Custom Solutions">
    <title><?php echo isset($page_title) ? $page_title . ' - ARVON' : 'ARVON - Premium Apparel Manufacturing'; ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-wrapper">
                <a href="/" class="logo">
                    <span class="logo-text">ARVON</span>
                </a>
                
                <div class="nav-menu" id="navMenu">
                    <a href="/" class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>">Home</a>
                    <a href="/about.php" class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>">About</a>
                    <a href="/products.php" class="nav-link <?php echo ($current_page == 'products') ? 'active' : ''; ?>">Products</a>
                    <a href="/manufacturing.php" class="nav-link <?php echo ($current_page == 'manufacturing') ? 'active' : ''; ?>">Manufacturing</a>
                    <a href="/gallery.php" class="nav-link <?php echo ($current_page == 'gallery') ? 'active' : ''; ?>">Gallery</a>
                    <a href="/contact.php" class="nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>">Contact</a>
                </div>
                
                <button class="mobile-toggle" id="mobileToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </nav>
