<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$keys = $_GET['keys'] ?? '';

$conn = getDBConnection();

if (empty($keys)) {
    $sql = "SELECT * FROM site_settings";
    $result = $conn->query($sql);
} else {
    $keyArray = explode(',', $keys);
    $placeholders = str_repeat('?,', count($keyArray) - 1) . '?';
    $stmt = $conn->prepare("SELECT * FROM site_settings WHERE setting_key IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($keyArray)), ...$keyArray);
    $stmt->execute();
    $result = $stmt->get_result();
}

$settings = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

jsonResponse(['success' => true, 'data' => $settings]);
?>
