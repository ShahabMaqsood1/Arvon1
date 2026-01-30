<?php 
$current_page = 'home';
$page_title = 'Home';
include 'includes/header.php'; 
?>

<!-- Hero Slider -->
<section class="hero-slider" id="heroSlider">
    <div class="hero-slides">
        <!-- Slides will be loaded via JavaScript -->
    </div>
    
    <button class="slider-btn slider-prev" onclick="previousSlide()">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
    </button>
    <button class="slider-btn slider-next" onclick="nextSlide()">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
    </button>
    
    <div class="slider-dots" id="sliderDots"></div>
</section>

<!-- Trust Badges -->
<section class="trust-badges">
    <div class="container">
        <div class="badges-grid">
            <div class="badge-item">
                <div class="badge-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
                </div>
                <h3>5+ Years</h3>
                <p>Industry Excellence</p>
            </div>
            <div class="badge-item">
                <div class="badge-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h3>100K+</h3>
                <p>Products Delivered</p>
            </div>
            <div class="badge-item">
                <div class="badge-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h3>98%</h3>
                <p>Client Satisfaction</p>
            </div>
        </div>
    </div>
</section>

<!-- Welcome Section -->
<section class="welcome-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Welcome to ARVON</span>
            <h2 class="section-title">
                Where Quality Meets
                <span class="gradient-text">Craftsmanship</span>
            </h2>
            <p class="section-desc">
                Premium apparel manufacturing solutions for brands, teams, and organizations. 
                We bring your vision to life with precision, quality, and unmatched expertise.
            </p>
            <a href="/about.php" class="btn btn-outline">Discover Our Story</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Our Collections</span>
            <h2 class="section-title">Product Categories</h2>
        </div>
        
        <div class="categories-grid" id="categoriesGrid">
            <!-- Categories will be loaded via JavaScript -->
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2 class="cta-title">Ready to Elevate Your Brand?</h2>
        <p class="cta-desc">
            Partner with ARVON for premium apparel manufacturing. Let's bring your vision to life with exceptional quality and craftsmanship.
        </p>
        <div class="cta-buttons">
            <a href="/contact.php" class="btn btn-primary">
                Start Your Project
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
            <a href="/products.php" class="btn btn-outline-white">View Our Work</a>
        </div>
    </div>
</section>

<script>
// Load featured products for slider
fetch('/api/products.php?featured=1')
    .then(res => res.json())
    .then(data => {
        initHeroSlider(data.data);
    });

// Load categories
fetch('/api/categories.php')
    .then(res => res.json())
    .then(data => {
        displayCategories(data.data);
    });
</script>

<?php include 'includes/footer.php'; ?>
