<?php
require_once '../config.php';
startSecureSession();
requireAdminLogin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_page') {
            $pageKey = sanitize($_POST['page_key']);
            $pageTitle = sanitize($_POST['page_title']);
            $metaDesc = sanitize($_POST['meta_description']);
            $metaKeywords = sanitize($_POST['meta_keywords']);
            $contentSections = json_encode($_POST['content'] ?? []);
            
            $stmt = $conn->prepare("UPDATE pages SET page_title = ?, meta_description = ?, meta_keywords = ?, content_sections = ? WHERE page_key = ?");
            $stmt->bind_param("sssss", $pageTitle, $metaDesc, $metaKeywords, $contentSections, $pageKey);
            
            if ($stmt->execute()) {
                $message = 'Page updated successfully';
            } else {
                $error = 'Failed to update page';
            }
            $stmt->close();
        } elseif ($action === 'update_navigation') {
            // Delete all existing nav items
            $conn->query("DELETE FROM navigation");
            
            // Insert updated nav items
            $stmt = $conn->prepare("INSERT INTO navigation (label, url, display_order, is_active) VALUES (?, ?, ?, 1)");
            
            foreach ($_POST['nav_items'] as $index => $item) {
                $label = sanitize($item['label']);
                $url = sanitize($item['url']);
                $order = (int)$index;
                
                $stmt->bind_param("ssi", $label, $url, $order);
                $stmt->execute();
            }
            
            $stmt->close();
            $message = 'Navigation updated successfully';
        }
    }
}

// Get pages
$pages = [];
$result = $conn->query("SELECT * FROM pages ORDER BY page_key");
while ($row = $result->fetch_assoc()) {
    $row['content_sections'] = json_decode($row['content_sections'], true);
    $pages[] = $row;
}

// Get navigation
$navigation = [];
$result = $conn->query("SELECT * FROM navigation ORDER BY display_order");
while ($row = $result->fetch_assoc()) {
    $navigation[] = $row;
}

$conn->close();

// Current editing page
$editingPage = $_GET['page'] ?? 'home';
$currentPage = array_filter($pages, fn($p) => $p['page_key'] === $editingPage)[0] ?? $pages[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages - ARVON Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="page-header">
                <h1>Page Content Manager</h1>
                <p>Edit website pages and navigation</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('content')">Page Content</button>
                <button class="tab-btn" onclick="switchTab('navigation')">Navigation Menu</button>
            </div>
            
            <!-- Page Content Tab -->
            <div id="content-tab" class="tab-content active">
                <div class="page-selector">
                    <label>Select Page:</label>
                    <select onchange="location.href='?page=' + this.value" class="form-control">
                        <?php foreach ($pages as $page): ?>
                            <option value="<?php echo $page['page_key']; ?>" <?php echo $page['page_key'] === $editingPage ? 'selected' : ''; ?>>
                                <?php echo ucfirst($page['page_key']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <form method="POST" class="page-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_page">
                    <input type="hidden" name="page_key" value="<?php echo $currentPage['page_key']; ?>">
                    
                    <div class="form-group">
                        <label>Page Title (for browser tab)</label>
                        <input type="text" name="page_title" value="<?php echo htmlspecialchars($currentPage['page_title']); ?>" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Meta Description (for SEO)</label>
                        <textarea name="meta_description" class="form-control" rows="2"><?php echo htmlspecialchars($currentPage['meta_description']); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Meta Keywords (for SEO)</label>
                        <input type="text" name="meta_keywords" value="<?php echo htmlspecialchars($currentPage['meta_keywords']); ?>" class="form-control">
                    </div>
                    
                    <h3>Page Content</h3>
                    
                    <?php foreach ($currentPage['content_sections'] as $key => $value): ?>
                        <div class="form-group">
                            <label><?php echo ucwords(str_replace('_', ' ', $key)); ?></label>
                            <?php if (is_array($value)): ?>
                                <textarea name="content[<?php echo $key; ?>]" class="form-control" rows="4"><?php echo htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT)); ?></textarea>
                                <small>JSON format for complex content</small>
                            <?php else: ?>
                                <textarea name="content[<?php echo $key; ?>]" class="form-control" rows="3"><?php echo htmlspecialchars($value); ?></textarea>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
            
            <!-- Navigation Tab -->
            <div id="navigation-tab" class="tab-content">
                <form method="POST" class="nav-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_navigation">
                    
                    <div id="nav-items">
                        <?php foreach ($navigation as $index => $item): ?>
                            <div class="nav-item-row">
                                <input type="text" name="nav_items[<?php echo $index; ?>][label]" value="<?php echo htmlspecialchars($item['label']); ?>" placeholder="Label" class="form-control">
                                <input type="text" name="nav_items[<?php echo $index; ?>][url]" value="<?php echo htmlspecialchars($item['url']); ?>" placeholder="URL" class="form-control">
                                <button type="button" onclick="removeNavItem(this)" class="btn btn-danger btn-sm">Remove</button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" onclick="addNavItem()" class="btn btn-secondary">Add Menu Item</button>
                    <button type="submit" class="btn btn-primary">Save Navigation</button>
                </form>
            </div>
        </main>
    </div>
    
    <script>
    let navItemCount = <?php echo count($navigation); ?>;
    
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        event.target.classList.add('active');
        document.getElementById(tab + '-tab').classList.add('active');
    }
    
    function addNavItem() {
        const container = document.getElementById('nav-items');
        const div = document.createElement('div');
        div.className = 'nav-item-row';
        div.innerHTML = `
            <input type="text" name="nav_items[${navItemCount}][label]" placeholder="Label" class="form-control">
            <input type="text" name="nav_items[${navItemCount}][url]" placeholder="URL" class="form-control">
            <button type="button" onclick="removeNavItem(this)" class="btn btn-danger btn-sm">Remove</button>
        `;
        container.appendChild(div);
        navItemCount++;
    }
    
    function removeNavItem(btn) {
        btn.parentElement.remove();
    }
    </script>
    <script src="/assets/js/admin.js"></script>
</body>
</html>
