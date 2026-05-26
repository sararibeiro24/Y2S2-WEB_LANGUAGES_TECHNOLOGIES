<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();
$trainerId = (int) ($_GET['id'] ?? 0);

if ($trainerId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$stmt = $db->prepare('
    SELECT
        u.id,
        u.name,
        u.profile_photo,
        tp.bio,
        tp.specializations,
        tp.certifications,
        tp.years_experience
    FROM users u
    LEFT JOIN trainer_profiles tp ON u.id = tp.user_id
    WHERE u.id = ? AND u.role = \'trainer\'
');
$stmt->execute([$trainerId]);
$trainer = $stmt->fetch();

if (!$trainer) {
    http_response_code(404);
    echo json_encode(['error' => 'Trainer not found']);
    exit;
}

$trainer['specializations_list'] = $trainer['specializations']
    ? array_map('trim', explode(',', $trainer['specializations']))
    : [];

// Get classes this trainer teaches
$stmt = $db->prepare('
    SELECT DISTINCT c.name, COUNT(cs.id) AS session_count
    FROM classes c
    JOIN class_schedule cs ON cs.class_id = c.id
    WHERE cs.trainer_id = ?
    GROUP BY c.id
    ORDER BY session_count DESC
');
$stmt->execute([$trainerId]);
$trainer['classes'] = $stmt->fetchAll();

// Get upcoming schedule for this trainer
$stmt = $db->prepare('
    SELECT cs.id AS schedule_id, c.name, cs.scheduled_at
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    WHERE cs.trainer_id = ? AND cs.scheduled_at >= datetime(\'now\')
    ORDER BY cs.scheduled_at ASC
    LIMIT 5
');
$stmt->execute([$trainerId]);
$trainer['upcoming_sessions'] = $stmt->fetchAll();

echo json_encode($trainer);
