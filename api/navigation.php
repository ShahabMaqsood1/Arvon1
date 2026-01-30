<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = getDBConnection();

$sql = "SELECT * FROM navigation WHERE is_active = 1 ORDER BY display_order ASC";
$result = $conn->query($sql);

$navigation = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $navigation[] = [
            'id' => $row['id'],
            'label' => $row['label'],
            'url' => $row['url'],
            'order' => $row['display_order']
        ];
    }
}

$conn->close();
jsonResponse(['success' => true, 'data' => $navigation]);
?>
