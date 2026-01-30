<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$page = $_GET['page'] ?? 'home';

$conn = getDBConnection();
$stmt = $conn->prepare("SELECT * FROM pages WHERE page_key = ? AND is_active = 1 LIMIT 1");
$stmt->bind_param("s", $page);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $pageData = $result->fetch_assoc();
    $pageData['content_sections'] = json_decode($pageData['content_sections'], true);
    
    $stmt->close();
    $conn->close();
    jsonResponse(['success' => true, 'data' => $pageData]);
} else {
    $stmt->close();
    $conn->close();
    jsonResponse(['success' => false, 'message' => 'Page not found'], 404);
}
?>
