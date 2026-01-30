<?php
require_once '../config.php';
startSecureSession();
requireAdminLogin();

$conn = getDBConnection();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token';
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_settings') {
            foreach ($_POST['settings'] as $key => $value) {
                $key = sanitize($key);
                $value = sanitize($value);
                
                $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->bind_param("ss", $value, $key);
                $stmt->execute();
                $stmt->close();
            }
            $message = 'Settings updated successfully';
        } elseif ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($current) || empty($new) || empty($confirm)) {
                $error = 'All password fields are required';
            } elseif ($new !== $confirm) {
                $error = 'New passwords do not match';
            } elseif (strlen($new) < 8) {
                $error = 'Password must be at least 8 characters';
            } else {
                // Verify current password
                $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
                $stmt->bind_param("i", $_SESSION['admin_id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $admin = $result->fetch_assoc();
                $stmt->close();
                
                if (password_verify($current, $admin['password'])) {
                    $newHash = password_hash($new, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $newHash, $_SESSION['admin_id']);
                    $stmt->execute();
                    $stmt->close();
                    $message = 'Password changed successfully';
                } else {
                    $error = 'Current password is incorrect';
                }
            }
        }
    }
}

// Get all settings
$result = $conn->query("SELECT * FROM site_settings ORDER BY setting_key");
$settings = [];
while ($row = $result->fetch_assoc()) {
    $settings[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - ARVON Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <div class="page-header">
                <h1>Settings</h1>
                <p>Configure your website settings</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="settings-grid">
                <!-- Site Settings -->
                <div class="settings-card">
                    <h2>Site Settings</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_settings">
                        
                        <?php foreach ($settings as $setting): ?>
                            <div class="form-group">
                                <label><?php echo ucwords(str_replace('_', ' ', $setting['setting_key'])); ?></label>
                                
                                <?php if ($setting['setting_type'] === 'textarea'): ?>
                                    <textarea 
                                        name="settings[<?php echo $setting['setting_key']; ?>]" 
                                        rows="3"
                                        class="form-control"
                                    ><?php echo htmlspecialchars($setting['setting_value']); ?></textarea>
                                <?php else: ?>
                                    <input 
                                        type="<?php echo $setting['setting_type'] ?? 'text'; ?>" 
                                        name="settings[<?php echo $setting['setting_key']; ?>]" 
                                        value="<?php echo htmlspecialchars($setting['setting_value']); ?>"
                                        class="form-control"
                                    >
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </form>
                </div>
                
                <!-- Change Password -->
                <div class="settings-card">
                    <h2>Change Password</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="8">
                            <small>Minimum 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    
    <script src="/assets/js/admin.js"></script>
</body>
</html>
