<?php
// ==========================================
// ARVON WEBSITE CONFIGURATION
// ==========================================
// Instructions: Update these values after uploading to cPanel

// ==========================================
// DATABASE CONFIGURATION
// ==========================================
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');     // Change in cPanel
define('DB_PASS', 'your_db_password');     // Change in cPanel
define('DB_NAME', 'arvon_db');             // Change in cPanel

// ==========================================
// SITE CONFIGURATION
// ==========================================
define('SITE_URL', 'https://arvon.pk');    // Your domain
define('SITE_NAME', 'ARVON');
define('ADMIN_EMAIL', 'info@arvon.pk');

// ==========================================
// SPACEMAIL SMTP CONFIGURATION
// ==========================================
define('SMTP_HOST', 'smtp.spacemail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'info@arvon.pk');      // Your Spacemail username
define('SMTP_PASSWORD', 'your_smtp_pass');     // Your Spacemail password
define('SMTP_FROM_EMAIL', 'no-reply@arvon.pk'); // Sender email (no-reply)
define('SMTP_FROM_NAME', 'ARVON Website');
define('SMTP_REPLY_TO', 'info@arvon.pk');      // Default reply-to

// ==========================================
// SECURITY CONFIGURATION
// ==========================================
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// ==========================================
// PATHS
// ==========================================
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
    mkdir(UPLOAD_DIR . 'products/', 0755, true);
    mkdir(UPLOAD_DIR . 'categories/', 0755, true);
    mkdir(UPLOAD_DIR . 'gallery/', 0755, true);
}

// ==========================================
// DATABASE CONNECTION
// ==========================================
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            die("Database connection failed. Please check your configuration.");
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        die("Database error occurred.");
    }
}

// ==========================================
// SECURITY HELPER FUNCTIONS
// ==========================================
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return time() . '_' . $filename;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ==========================================
// SESSION MANAGEMENT
// ==========================================
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 1);
        session_start();
        
        // Check session timeout
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
                session_unset();
                session_destroy();
                return false;
            }
        }
        $_SESSION['last_activity'] = time();
    }
    return true;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

// ==========================================
// JSON RESPONSE HELPER
// ==========================================
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// ==========================================
// EMAIL FUNCTION USING SPACEMAIL
// ==========================================
function sendEmail($to, $subject, $body, $isHTML = true, $replyTo = null, $replyToName = null) {
    require_once __DIR__ . '/includes/smtp.php';
    
    try {
        $mail = new SMTPMailer();
        $mail->host = SMTP_HOST;
        $mail->port = SMTP_PORT;
        $mail->username = SMTP_USERNAME;
        $mail->password = SMTP_PASSWORD;
        $mail->from = SMTP_FROM_EMAIL;
        $mail->fromName = SMTP_FROM_NAME;
        $mail->to = $to;
        $mail->subject = $subject;
        $mail->body = $body;
        $mail->isHTML = $isHTML;
        $mail->replyTo = $replyTo;
        $mail->replyToName = $replyToName;
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

// ==========================================
// IMAGE UPLOAD HANDLER
// ==========================================
function uploadImage($file, $folder = 'gallery') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['success' => false, 'message' => 'File too large (max 5MB)'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = sanitizeFilename($file['name']);
    $targetDir = UPLOAD_DIR . $folder . '/';
    $targetPath = $targetDir . $filename;
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Resize image if needed
        resizeImage($targetPath, 1920, 1080);
        
        $url = UPLOAD_URL . $folder . '/' . $filename;
        return ['success' => true, 'url' => $url, 'path' => $targetPath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// ==========================================
// IMAGE RESIZE FUNCTION
// ==========================================
function resizeImage($filepath, $maxWidth = 1920, $maxHeight = 1080) {
    list($width, $height, $type) = getimagesize($filepath);
    
    if ($width <= $maxWidth && $height <= $maxHeight) {
        return; // No resize needed
    }
    
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = round($width * $ratio);
    $newHeight = round($height * $ratio);
    
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($filepath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($filepath);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($filepath);
            break;
        default:
            return;
    }
    
    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($newImage, $filepath, 85);
            break;
        case IMAGETYPE_PNG:
            imagepng($newImage, $filepath, 8);
            break;
        case IMAGETYPE_WEBP:
            imagewebp($newImage, $filepath, 85);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($newImage);
}

// ==========================================
// ERROR HANDLING
// ==========================================
// Disable error display in production
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// ==========================================
// TIMEZONE
// ==========================================
date_default_timezone_set('Asia/Karachi'); // Pakistan timezone
?>
