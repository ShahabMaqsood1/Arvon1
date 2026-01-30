<?php 
$current_page = 'about';
$page_title = 'About Us';
include 'includes/header.php'; 
?>

<!-- About Content -->
<div id="about-content"></div>

<script>
// Load page content from API
fetch('/api/page-content.php?page=about')
    .then(res => res.json())
    .then(data => {
        const content = data.data.content_sections;
        const container = document.getElementById('about-content');
        
        container.innerHTML = `
            <!-- Hero Section -->
            <section class="page-hero">
                <div class="container">
                    <span class="section-tag">${content.hero_tag}</span>
                    <h1 class="page-title">${content.hero_title}</h1>
                    <p class="page-desc">${content.hero_description}</p>
                </div>
            </section>
            
            <!-- Values Section -->
            <section class="values-section">
                <div class="container">
                    <h2 class="section-title">Our Core Values</h2>
                    <div class="values-grid">
                        ${JSON.parse(content.values).map(value => `
                            <div class="value-card">
                                <h3>${value.title}</h3>
                                <p>${value.description}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </section>
            
            <!-- Stats Section -->
            <section class="stats-section">
                <div class="container">
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">5+</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">500+</div>
                            <div class="stat-label">Clients Served</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100K+</div>
                            <div class="stat-label">Products Delivered</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">Satisfaction Rate</div>
                        </div>
                    </div>
                </div>
            </section>
        `;
    })
    .catch(err => console.error('Error loading content:', err));
</script>

<style>
.page-hero {
    padding: 6rem 0;
    background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
    text-align: center;
}

.page-title {
    font-size: 3.5rem;
    margin-bottom: 1.5rem;
}

.page-desc {
    font-size: 1.25rem;
    color: #666;
    max-width: 800px;
    margin: 0 auto;
}

.values-section {
    padding: 6rem 0;
    background: white;
}

.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.value-card {
    padding: 2rem;
    background: #f9f9f9;
    border-radius: 16px;
    transition: transform 0.3s;
}

.value-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(139, 21, 56, 0.1);
}

.value-card h3 {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.stats-section {
    padding: 6rem 0;
    background: linear-gradient(to right, var(--primary), var(--primary-light));
    color: white;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}
</style>

<?php include 'includes/footer.php'; ?>
