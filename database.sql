-- ==========================================
-- ARVON DATABASE SCHEMA (cPanel SAFE)
-- Database must already exist in cPanel
-- ==========================================

-- =========================
-- Categories
-- =========================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- Products
-- =========================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    keywords VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category
        FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE CASCADE
);

-- =========================
-- Gallery
-- =========================
CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image VARCHAR(255) NOT NULL,
    alt_text VARCHAR(200),
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Contact Messages
-- =========================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new','read','replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Admin Users
-- =========================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    must_change_password TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- Site Settings
-- =========================
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    setting_label VARCHAR(200),
    setting_type ENUM('text','textarea','email','url','number') DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- Pages Content
-- =========================
CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(50) UNIQUE NOT NULL,
    page_title VARCHAR(200) NOT NULL,
    meta_description TEXT,
    meta_keywords TEXT,
    content_sections JSON,
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

-- =========================
-- Navigation Menu
-- =========================
CREATE TABLE IF NOT EXISTS navigation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- DEFAULT DATA
-- ==========================================

-- Admin user
-- username: admin
-- password: admin123  (CHANGE IMMEDIATELY)
INSERT INTO admin_users (username, password, email, must_change_password)
VALUES (
  'admin',
  '$2y$10$wH1g8V2Q1QK4x7UjKQz6Ee9w9U7T2M0J9j0BzKZlQx5Zz2K9nCq1O',
  'info@arvon.pk',
  1
);

-- Categories
INSERT INTO categories (name, description, image, display_order) VALUES
('Sportswear','High-performance athletic apparel','',1),
('Casual Wear','Comfortable everyday clothing','',2),
('Custom Apparel','Tailored solutions for your brand','',3);

-- Products
INSERT INTO products (category_id,name,description,image,keywords,is_featured,display_order) VALUES
(1,'Performance Track Jacket','Lightweight breathable jacket','', 'jacket,athletic',1,1),
(1,'Pro Training Shorts','Flexible workout shorts','', 'shorts,training',1,2),
(2,'Cotton Crew Neck Tee','Premium cotton t-shirt','', 'tshirt,cotton',0,3),
(2,'Relaxed Fit Hoodie','Comfortable hoodie','', 'hoodie,casual',1,4),
(3,'Custom Team Jersey','Fully customizable jerseys','', 'custom,jersey',0,5);

-- Gallery
INSERT INTO gallery (image,alt_text,display_order) VALUES
('','Manufacturing facility',1),
('','Product showcase',2),
('','Design process',3);

-- Site Settings
INSERT INTO site_settings (setting_key,setting_value,setting_label,setting_type) VALUES
('site_title','ARVON - Premium Apparel Manufacturing','Site Title','text'),
('site_description','Premium apparel manufacturing solutions','Site Description','textarea'),
('admin_email','info@arvon.pk','Admin Email','email'),
('contact_email','info@arvon.pk','Contact Email','email'),
('contact_phone','+92 300 0000000','Contact Phone','text'),
('contact_address','Pakistan','Contact Address','text');

-- Navigation
INSERT INTO navigation (label,url,display_order,is_active) VALUES
('Home','/',1,1),
('About','/about.php',2,1),
('Products','/products.php',3,1),
('Manufacturing','/manufacturing.php',4,1),
('Gallery','/gallery.php',5,1),
('Contact','/contact.php',6,1);

-- Pages
INSERT INTO pages (page_key,page_title,meta_description,meta_keywords,content_sections) VALUES
(
 'home',
 'Home - ARVON',
 'Premium apparel manufacturing',
 'apparel,manufacturing',
 '{"hero_title":"Where Quality Meets Craftsmanship"}'
),
(
 'about',
 'About Us - ARVON',
 'Learn about ARVON',
 'about,company',
 '{"title":"About ARVON"}'
),
(
 'manufacturing',
 'Manufacturing - ARVON',
 'Our production capabilities',
 'manufacturing,factory',
 '{"title":"Manufacturing Facility"}'
),
(
 'contact',
 'Contact Us - ARVON',
 'Get in touch',
 'contact,email',
 '{"title":"Contact ARVON"}'
);
