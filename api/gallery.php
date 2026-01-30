<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = getDBConnection();

$sql = "SELECT * FROM gallery ORDER BY display_order ASC";
$result = $conn->query($sql);

$gallery = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $gallery[] = [
            'id' => $row['id'],
            'url' => $row['image'],
            'alt' => $row['alt_text']
        ];
    }
}

$conn->close();
jsonResponse(['success' => true, 'data' => $gallery]);
?>
