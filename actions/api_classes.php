<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();

$search = $_GET['search'] ?? '';
$trainerId = $_GET['trainer'] ?? '';
$date = $_GET['date'] ?? '';

$sql = '
    SELECT
        cs.id AS schedule_id,
        cs.class_id,
        cs.trainer_id,
        cs.scheduled_at,
        c.name,
        c.description,
        c.capacity,
        u.name AS trainer,
        COUNT(e.id) AS enrolled
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    JOIN users u ON cs.trainer_id = u.id
    LEFT JOIN enrollments e ON e.schedule_id = cs.id
    WHERE cs.scheduled_at >= datetime(\'now\')
';

$params = [];

if ($search !== '') {
    $sql .= ' AND c.name LIKE ?';
    $params[] = '%' . $search . '%';
}

if ($trainerId !== '' && $trainerId !== 'all') {
    $sql .= ' AND cs.trainer_id = ?';
    $params[] = (int)$trainerId;
}

if ($date !== '') {
    $sql .= ' AND date(cs.scheduled_at) = ?';
    $params[] = $date;
}

$sql .= ' GROUP BY cs.id ORDER BY cs.scheduled_at ASC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

echo json_encode($rows);
