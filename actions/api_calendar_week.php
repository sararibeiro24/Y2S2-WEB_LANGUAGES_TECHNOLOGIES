<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$weekStart = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekStart)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

$weekEnd = date('Y-m-d 23:59:59', strtotime($weekStart . ' +6 days'));

// Filter params
$trainerId  = isset($_GET['trainer_id']) ? (int)$_GET['trainer_id'] : 0;
$search     = trim($_GET['query'] ?? '');
$difficulty = trim($_GET['difficulty'] ?? '');

$sql = '
    SELECT
        cs.id AS schedule_id,
        cs.class_id,
        cs.trainer_id,
        cs.scheduled_at,
        c.name,
        c.description,
        c.capacity,
        c.difficulty,
        u.name AS trainer,
        u.id AS trainer_user_id,
        COUNT(e.id) AS enrolled
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    JOIN users u ON cs.trainer_id = u.id
    LEFT JOIN enrollments e ON e.schedule_id = cs.id
    WHERE cs.scheduled_at >= ? AND cs.scheduled_at <= ?
';

$params = [$weekStart, $weekEnd];

if ($trainerId > 0) {
    $sql .= ' AND cs.trainer_id = ?';
    $params[] = $trainerId;
}

if ($search !== '') {
    $sql .= ' AND LOWER(c.name) LIKE ?';
    $params[] = '%' . strtolower($search) . '%';
}

if ($difficulty !== '') {
    $sql .= ' AND LOWER(c.difficulty) = ?';
    $params[] = strtolower($difficulty);
}

$sql .= ' GROUP BY cs.id ORDER BY cs.scheduled_at ASC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

echo json_encode([
    'week_start' => $weekStart,
    'week_end' => $weekEnd,
    'classes' => $rows,
]);
