<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['query'] ?? '');
$status = trim($_GET['status'] ?? '');

$db = getDatabaseConnection();
$sql = 'SELECT e.id, e.name, e.total_quantity, es.available_quantity, es.last_updated
        FROM equipment e
        LEFT JOIN equipment_status es ON e.id = es.equipment_id';
$params = [];
$clauses = [];

if ($query !== '') {
    $clauses[] = 'LOWER(e.name) LIKE ?';
    $params[] = '%' . strtolower($query) . '%';
}
if ($status === 'available') {
    $clauses[] = 'es.available_quantity > 0';
} elseif ($status === 'unavailable') {
    $clauses[] = 'es.available_quantity = 0';
}

if ($clauses) {
    $sql .= ' WHERE ' . implode(' AND ', $clauses);
}

$sql .= ' ORDER BY e.name ASC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

foreach ($data as &$item) {
    $item['available_quantity'] = (int)$item['available_quantity'];
    $item['total_quantity'] = (int)$item['total_quantity'];
}

echo json_encode($data);
exit;
?>
