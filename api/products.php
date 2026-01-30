<?php
require_once '../config.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$conn = getDBConnection();

// Get query parameters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;

// Build query
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";

if ($category !== 'all') {
    $sql .= " AND p.category_id = " . (int)$category;
}

if ($search !== '') {
    $sql .= " AND (p.name LIKE '%" . $conn->real_escape_string($search) . "%' 
              OR p.description LIKE '%" . $conn->real_escape_string($search) . "%'
              OR p.keywords LIKE '%" . $conn->real_escape_string($search) . "%')";
}

if ($featured !== null) {
    $sql .= " AND p.is_featured = " . $featured;
}

$sql .= " ORDER BY p.display_order ASC, p.created_at DESC";

$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'image' => $row['image'],
            'category' => $row['category_name'],
            'categoryId' => $row['category_id'],
            'keywords' => $row['keywords'],
            'featured' => (bool)$row['is_featured']
        ];
    }
}

$conn->close();
jsonResponse(['success' => true, 'data' => $products]);
?>
