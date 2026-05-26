<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

header('Content-Type: application/json');

Session::start();
$userId = Session::isLoggedIn() ? Session::getUserId() : null;

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
        cs.class_id,
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
        COUNT(DISTINCT e.id) AS enrolled,
        ROUND(AVG(r.rating), 1) AS avg_rating,
        COUNT(DISTINCT r.id) AS review_count
    FROM class_schedule cs
    JOIN classes c ON cs.class_id = c.id
    JOIN users u ON cs.trainer_id = u.id
    LEFT JOIN trainer_profiles tp ON tp.user_id = u.id
    LEFT JOIN enrollments e ON e.schedule_id = cs.id
    LEFT JOIN reviews r ON r.schedule_id = cs.id
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

// Get reviews for this schedule
$stmt = $db->prepare('
    SELECT r.id, r.rating, r.comment, r.created_at, u.name AS user_name, u.profile_photo AS user_photo
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.schedule_id = ?
    ORDER BY r.created_at DESC
');
$stmt->execute([$scheduleId]);
$row['reviews'] = $stmt->fetchAll();

// Check if user can review and their existing review
$row['can_review'] = false;
$row['user_review'] = null;

if ($userId && strtotime($row['scheduled_at']) < time()) {
    // Check enrollment
    $stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
    $stmt->execute([$userId, $scheduleId]);
    if ($stmt->fetch()) {
        $row['can_review'] = true;
    }

    // Check existing review
    $stmt = $db->prepare('SELECT id, rating, comment FROM reviews WHERE user_id = ? AND schedule_id = ?');
    $stmt->execute([$userId, $scheduleId]);
    $existing = $stmt->fetch();
    if ($existing) {
        $row['user_review'] = $existing;
    }
}

echo json_encode($row);
