<?php 
$current_page = 'products';
$page_title = 'Products';
include 'includes/header.php'; 
?>

<!-- Products Page -->
<div id="products-content"></div>

<script>
let allProducts = [];
let allCategories = [];

// Load categories
fetch('/api/categories.php')
    .then(res => res.json())
    .then(data => {
        allCategories = data.data;
        renderCategories();
    });

// Load products
fetch('/api/products.php')
    .then(res => res.json())
    .then(data => {
        allProducts = data.data;
        renderProducts(allProducts);
    });

function renderCategories() {
    const filterHTML = `
        <button class="filter-btn active" onclick="filterProducts('all')">All Products</button>
        ${allCategories.map(cat => `
            <button class="filter-btn" onclick="filterProducts('${cat.id}')">${cat.name}</button>
        `).join('')}
    `;
    document.getElementById('category-filters').innerHTML = filterHTML;
}

function filterProducts(categoryId) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Filter products
    const filtered = categoryId === 'all' 
        ? allProducts 
        : allProducts.filter(p => p.categoryId == categoryId);
    
    renderProducts(filtered);
}

function searchProducts() {
    const query = document.getElementById('search-input').value.toLowerCase();
    const filtered = allProducts.filter(p => 
        p.name.toLowerCase().includes(query) ||
        p.description.toLowerCase().includes(query) ||
        p.keywords.toLowerCase().includes(query)
    );
    renderProducts(filtered);
}

function renderProducts(products) {
    const container = document.getElementById('products-grid');
    
    if (products.length === 0) {
        container.innerHTML = `
            <div class="no-results">
                <h3>No products found</h3>
                <p>Try adjusting your search or filters</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = products.map(product => `
        <div class="product-card">
            <div class="product-image">
                <img src="${product.image}" alt="${product.name}">
                ${product.featured ? '<span class="featured-badge">Featured</span>' : ''}
            </div>
            <div class="product-info">
                <span class="product-category">${product.category}</span>
                <h3 class="product-name">${product.name}</h3>
                <p class="product-desc">${product.description}</p>
            </div>
        </div>
    `).join('');
}
</script>

<section class="products-page">
    <div class="page-hero">
        <div class="container">
            <span class="section-tag">Discover Excellence</span>
            <h1 class="page-title">Our Products</h1>
            <p class="page-desc">Browse our complete collection of premium apparel</p>
        </div>
    </div>
    
    <div class="filters-bar">
        <div class="container">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Search products..." onkeyup="searchProducts()">
            </div>
            <div id="category-filters" class="category-filters"></div>
        </div>
    </div>
    
    <div class="container">
        <div id="products-grid" class="products-grid"></div>
    </div>
</section>

<style>
.filters-bar {
    position: sticky;
    top: 80px;
    background: white;
    padding: 1.5rem 0;
    border-bottom: 1px solid #eee;
    z-index: 10;
}

.search-box {
    margin-bottom: 1rem;
}

.search-box input {
    width: 100%;
    padding: 1rem 1.5rem;
    border: 2px solid #e5e5e5;
    border-radius: 12px;
    font-size: 1rem;
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
}

.category-filters {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 50px;
    background: #f5f5f5;
    color: #666;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
}

.filter-btn:hover,
.filter-btn.active {
    background: linear-gradient(to right, var(--primary), var(--primary-light));
    color: white;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    padding: 3rem 0;
}

.product-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(139, 21, 56, 0.15);
}

.product-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s;
}

.product-card:hover .product-image img {
    transform: scale(1.1);
}

.featured-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: var(--accent);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
}

.product-info {
    padding: 1.5rem;
}

.product-category {
    color: var(--accent);
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
}

.product-name {
    font-size: 1.5rem;
    color: var(--primary);
    margin: 0.5rem 0;
}

.product-desc {
    color: #666;
    line-height: 1.6;
}

.no-results {
    text-align: center;
    padding: 4rem 0;
    grid-column: 1 / -1;
}
</style>

<?php include 'includes/footer.php'; ?>
