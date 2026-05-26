<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$scheduleId = (int) ($_GET['id'] ?? 0);

if ($scheduleId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$stmt = $db->prepare('
    SELECT
        cs.id AS schedule_id,
        cs.scheduled_at,
        c.name,
        c.description,
        c.capacity,
        c.difficulty,
        u.name AS trainer,
        u.id AS trainer_id,
        u.profile_photo AS trainer_photo,
        tp.bio AS trainer_bio,
        tp.specializations AS trainer_specs,
        tp.years_experience,
        tp.certifications,
        COUNT(e.id) AS enrolled
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    JOIN users u ON cs.trainer_id = u.id
    LEFT JOIN trainer_profiles tp ON tp.user_id = u.id
    LEFT JOIN enrollments e ON e.schedule_id = cs.id
    WHERE cs.id = ?
    GROUP BY cs.id
');
$stmt->execute([$scheduleId]);
$row = $stmt->fetch();

if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'Class not found']);
    exit;
}

$row['specs_list'] = $row['trainer_specs']
    ? array_map('trim', explode(',', $row['trainer_specs']))
    : [];

echo json_encode($row);
