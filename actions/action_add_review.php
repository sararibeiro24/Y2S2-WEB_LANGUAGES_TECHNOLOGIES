<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/review.class.php');

Session::start();

header('Content-Type: application/json');

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

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid security token']);
    exit;
}

$userId = Session::getUserId();
$scheduleId = (int)($_POST['schedule_id'] ?? 0);
$rating = (int)($_POST['rating'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($scheduleId <= 0 || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

try {
    $schedule = Review::getSchedule($scheduleId);

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

    if (!Review::isEnrolled($userId, $scheduleId)) {
        http_response_code(403);
        echo json_encode(['error' => 'You did not attend this class']);
        exit;
    }

    Review::upsert($userId, $schedule['class_id'], $scheduleId, $rating, $comment);

    echo json_encode(['success' => true, 'message' => 'Review saved']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
    error_log('Database error in action_review: ' . $e->getMessage());
}