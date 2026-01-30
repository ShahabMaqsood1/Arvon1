<?php 
$current_page = 'manufacturing';
$page_title = 'Manufacturing';
include 'includes/header.php'; 
?>

<div id="manufacturing-content"></div>

<script>
fetch('/api/page-content.php?page=manufacturing')
    .then(res => res.json())
    .then(data => {
        const content = data.data.content_sections;
        const container = document.getElementById('manufacturing-content');
        
        container.innerHTML = `
            <section class="page-hero manufacturing-hero">
                <div class="container">
                    <span class="section-tag">${content.hero_tag}</span>
                    <h1 class="page-title">${content.hero_title}</h1>
                    <p class="page-desc">${content.hero_description}</p>
                </div>
            </section>
            
            <section class="capabilities-section">
                <div class="container">
                    <h2 class="section-title">Our Capabilities</h2>
                    <div class="capabilities-grid">
                        ${JSON.parse(content.capabilities).map(cap => `
                            <div class="capability-card">
                                <div class="capability-icon">${getIcon(cap.icon)}</div>
                                <h3>${cap.title}</h3>
                                <p>${cap.description}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </section>
            
            <section class="process-section">
                <div class="container">
                    <h2 class="section-title">Our Process</h2>
                    <div class="process-steps">
                        <div class="step">
                            <div class="step-number">01</div>
                            <h3>Design</h3>
                            <p>Concept development and technical design</p>
                        </div>
                        <div class="step">
                            <div class="step-number">02</div>
                            <h3>Production</h3>
                            <p>Cutting, sewing, and assembly</p>
                        </div>
                        <div class="step">
                            <div class="step-number">03</div>
                            <h3>Quality Check</h3>
                            <p>Multi-stage inspection process</p>
                        </div>
                        <div class="step">
                            <div class="step-number">04</div>
                            <h3>Delivery</h3>
                            <p>Packaging and shipment</p>
                        </div>
                    </div>
                </div>
            </section>
        `;
    });

function getIcon(iconName) {
    const icons = {
        'factory': '🏭',
        'palette': '🎨',
        'shield-check': '✅',
        'clock': '⏱️'
    };
    return icons[iconName] || '📦';
}
</script>

<style>
.manufacturing-hero {
    background: linear-gradient(to right, var(--primary-dark), var(--primary));
    color: white;
}

.capabilities-section {
    padding: 6rem 0;
    background: white;
}

.capabilities-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.capability-card {
    padding: 2rem;
    border: 2px solid #e5e5e5;
    border-radius: 16px;
    transition: all 0.3s;
}

.capability-card:hover {
    border-color: var(--primary);
    box-shadow: 0 10px 30px rgba(139, 21, 56, 0.1);
    transform: translateY(-5px);
}

.capability-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.capability-card h3 {
    font-size: 1.5rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.process-section {
    padding: 6rem 0;
    background: #f9f9f9;
}

.process-steps {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-top: 3rem;
}

.step {
    text-align: center;
}

.step-number {
    width: 80px;
    height: 80px;
    background: var(--primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    margin: 0 auto 1.5rem;
}

.step h3 {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

.step p {
    color: #666;
}
</style>

<?php include 'includes/footer.php'; ?>
