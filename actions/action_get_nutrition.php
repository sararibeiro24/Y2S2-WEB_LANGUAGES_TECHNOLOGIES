<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$userId = Session::getUserId();
header('Content-Type: application/json; charset=utf-8');

if (!$userId) {
    echo json_encode([]);
    exit;
}

$query = trim($_GET['query'] ?? '');
$goal = trim($_GET['goal'] ?? '');
$trainer = trim($_GET['trainer'] ?? '');

$db = getDatabaseConnection();

$sql = 'SELECT np.id, np.target_calories, np.goal, np.meal_details, np.created_at,
               member.name AS member_name,
               trainer.name AS trainer_name
        FROM nutrition_plans np
        JOIN users member ON np.user_id = member.id
        JOIN users trainer ON np.trainer_id = trainer.id
        WHERE np.user_id = ?';

$params = [$userId];

if ($query !== '') {
    $sql .= ' AND (LOWER(np.goal) LIKE ? OR LOWER(np.meal_details) LIKE ? OR LOWER(trainer.name) LIKE ?)';
    $term = '%' . strtolower($query) . '%';
    $params = array_merge($params, [$term, $term, $term]);
}
if ($goal !== '') {
    $sql .= ' AND np.goal = ?';
    $params[] = $goal;
}
if ($trainer !== '') {
    $sql .= ' AND trainer.name = ?';
    $params[] = $trainer;
}

$sql .= ' ORDER BY np.id DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as &$item) {
    $item['target_calories'] = $item['target_calories'] !== null ? (int)$item['target_calories'] : null;
}

echo json_encode($data);
exit;
?>