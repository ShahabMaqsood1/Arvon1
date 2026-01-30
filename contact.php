<?php 
$current_page = 'contact';
$page_title = 'Contact Us';
include 'includes/header.php'; 
?>

<div id="contact-content"></div>

<script>
// Load page content
fetch('/api/page-content.php?page=contact')
    .then(res => res.json())
    .then(data => {
        const content = data.data.content_sections;
        renderContactPage(content);
    });

// Load settings for contact info
fetch('/api/settings.php?keys=contact_email,contact_phone,contact_address,business_hours')
    .then(res => res.json())
    .then(data => {
        renderContactInfo(data.data);
    });

function renderContactPage(content) {
    const container = document.getElementById('contact-content');
    container.innerHTML = `
        <section class="page-hero contact-hero">
            <div class="container">
                <span class="section-tag">${content.hero_tag}</span>
                <h1 class="page-title">${content.hero_title}</h1>
                <p class="page-desc">${content.hero_description}</p>
            </div>
        </section>
        
        <section class="contact-section">
            <div class="container">
                <div class="contact-grid">
                    <div class="contact-info" id="contact-info-section">
                        <!-- Will be populated by settings -->
                    </div>
                    
                    <div class="contact-form-container">
                        <h2>${content.form_title}</h2>
                        <p>${content.form_description}</p>
                        
                        <form id="contact-form" onsubmit="submitContact(event)">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Name *</label>
                                    <input type="text" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" name="phone">
                                </div>
                                <div class="form-group">
                                    <label>Subject *</label>
                                    <input type="text" name="subject" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Message *</label>
                                <textarea name="message" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submit-btn">
                                Send Message
                            </button>
                        </form>
                        
                        <div id="form-message" class="form-message"></div>
                    </div>
                </div>
            </div>
        </section>
    `;
}

function renderContactInfo(settings) {
    const hours = JSON.parse(settings.business_hours || '{}');
    const infoHTML = `
        <h2>Get in Touch</h2>
        <p>Have questions? We're here to help!</p>
        
        <div class="contact-details">
            <div class="contact-item">
                <strong>📧 Email</strong>
                <a href="mailto:${settings.contact_email}">${settings.contact_email}</a>
            </div>
            
            <div class="contact-item">
                <strong>📞 Phone</strong>
                <span>${settings.contact_phone}</span>
            </div>
            
            <div class="contact-item">
                <strong>📍 Location</strong>
                <span>${settings.contact_address}</span>
            </div>
        </div>
        
        <div class="business-hours">
            <h3>Business Hours</h3>
            ${Object.entries(hours).map(([day, time]) => `
                <div class="hours-row">
                    <span>${day.charAt(0).toUpperCase() + day.slice(1)}</span>
                    <span>${time}</span>
                </div>
            `).join('')}
        </div>
    `;
    
    document.getElementById('contact-info-section').innerHTML = infoHTML;
}

function submitContact(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const data = Object.fromEntries(formData);
    const btn = document.getElementById('submit-btn');
    const msgDiv = document.getElementById('form-message');
    
    btn.disabled = true;
    btn.textContent = 'Sending...';
    
    fetch('/api/contact.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            msgDiv.className = 'form-message success';
            msgDiv.textContent = 'Message sent successfully! We\'ll get back to you soon.';
            form.reset();
        } else {
            msgDiv.className = 'form-message error';
            msgDiv.textContent = response.message || 'Failed to send message. Please try again.';
        }
    })
    .catch(err => {
        msgDiv.className = 'form-message error';
        msgDiv.textContent = 'An error occurred. Please try again.';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Send Message';
    });
}
</script>

<style>
.contact-hero {
    background: linear-gradient(to right, var(--primary), var(--primary-light));
    color: white;
}

.contact-section {
    padding: 4rem 0;
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 3rem;
}

.contact-info {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.contact-info h2 {
    margin-bottom: 1rem;
}

.contact-details {
    margin: 2rem 0;
}

.contact-item {
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.contact-item strong {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--primary);
}

.contact-item a {
    color: #666;
    text-decoration: none;
}

.contact-item a:hover {
    color: var(--primary);
}

.business-hours {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 2px solid #eee;
}

.business-hours h3 {
    margin-bottom: 1rem;
}

.hours-row {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    font-size: 0.875rem;
}

.contact-form-container {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.contact-form-container h2 {
    margin-bottom: 0.5rem;
}

.contact-form-container > p {
    color: #666;
    margin-bottom: 2rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e5e5e5;
    border-radius: 8px;
    font-family: inherit;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--primary);
}

.form-message {
    margin-top: 1rem;
    padding: 1rem;
    border-radius: 8px;
    display: none;
}

.form-message.success {
    display: block;
    background: #d4edda;
    color: #155724;
}

.form-message.error {
    display: block;
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
