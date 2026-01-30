<?php 
$current_page = 'gallery';
$page_title = 'Gallery';
include 'includes/header.php'; 
?>

<div id="gallery-content"></div>

<script>
fetch('/api/gallery.php')
    .then(res => res.json())
    .then(data => {
        const images = data.data;
        const container = document.getElementById('gallery-content');
        
        container.innerHTML = `
            <section class="page-hero">
                <div class="container">
                    <span class="section-tag">Our Work</span>
                    <h1 class="page-title">Gallery</h1>
                    <p class="page-desc">Explore our products, facility, and manufacturing excellence</p>
                </div>
            </section>
            
            <section class="gallery-section">
                <div class="container">
                    <div class="gallery-grid">
                        ${images.map((img, index) => `
                            <div class="gallery-item ${index % 5 === 0 ? 'large' : ''}">
                                <img src="${img.url}" alt="${img.alt}">
                                <div class="gallery-overlay">
                                    <p>${img.alt}</p>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </section>
        `;
    });
</script>

<style>
.gallery-section {
    padding: 3rem 0;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.gallery-item {
    position: relative;
    height: 300px;
    border-radius: 16px;
    overflow: hidden;
    cursor: pointer;
}

.gallery-item.large {
    grid-column: span 2;
    grid-row: span 2;
    height: 100%;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

.gallery-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    display: flex;
    align-items: flex-end;
    padding: 1.5rem;
    opacity: 0;
    transition: opacity 0.3s;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-overlay p {
    color: white;
    font-size: 1.125rem;
    font-weight: 600;
}
</style>

<?php include 'includes/footer.php'; ?>
