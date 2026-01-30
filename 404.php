<?php
http_response_code(404);
$current_page = '404';
$page_title = 'Page Not Found';
include 'includes/header.php';
?>

<style>
.error-page {
    min-height: calc(100vh - 80px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 1rem;
    background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
}

.error-content {
    text-align: center;
    max-width: 600px;
}

.error-code {
    font-size: 8rem;
    font-weight: 800;
    font-family: 'Playfair Display', serif;
    background: linear-gradient(135deg, #8B1538 0%, #D4AF37 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 1rem;
}

.error-title {
    font-family: 'Playfair Display', serif;
    font-size: 2.5rem;
    color: #1a1a1a;
    margin-bottom: 1rem;
}

.error-desc {
    font-size: 1.125rem;
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.8;
}

.error-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<div class="error-page">
    <div class="error-content">
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-desc">
            Sorry, the page you're looking for doesn't exist or has been moved.
            Let's get you back on track.
        </p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">Go to Homepage</a>
            <a href="/products.php" class="btn btn-outline">View Products</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
