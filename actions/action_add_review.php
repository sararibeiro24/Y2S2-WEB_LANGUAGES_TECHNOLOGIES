<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');

Session::start();

if (!Session::isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'POST required']);
    exit;
}

$userId = Session::getUserId();
$scheduleId = (int) ($_POST['schedule_id'] ?? 0);
$rating = (int) ($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($scheduleId <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$db = getDatabaseConnection();

// Check class exists, is in the past, and user was enrolled
$stmt = $db->prepare('
    SELECT cs.id, cs.class_id, cs.scheduled_at
    FROM class_schedule cs
    WHERE cs.id = ?
');
$stmt->execute([$scheduleId]);
$schedule = $stmt->fetch();

if (!$schedule) {
    http_response_code(404);
    echo json_encode(['error' => 'Class not found']);
    exit;
}

if (strtotime($schedule['scheduled_at']) > time()) {
    http_response_code(400);
    echo json_encode(['error' => 'Class has not taken place yet']);
    exit;
}

// Check enrollment
$stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$stmt->execute([$userId, $scheduleId]);
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['error' => 'You did not attend this class']);
    exit;
}

// Upsert review
$stmt = $db->prepare('
    INSERT INTO reviews (user_id, class_id, schedule_id, rating, comment)
    VALUES (?, ?, ?, ?, ?)
    ON CONFLICT(user_id, schedule_id) DO UPDATE SET
        rating = excluded.rating,
        comment = excluded.comment,
        created_at = CURRENT_TIMESTAMP
');
$stmt->execute([$userId, $schedule['class_id'], $scheduleId, $rating, $comment]);

echo json_encode(['success' => true, 'message' => 'Review saved']);
