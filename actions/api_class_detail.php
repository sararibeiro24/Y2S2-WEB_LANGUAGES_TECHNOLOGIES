<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');
require_once(__DIR__ . '/../utils/session.php');

header('Content-Type: application/json');

Session::start();
$userId = Session::isLoggedIn() ? Session::getUserId() : null;

$scheduleId = (int)($_GET['id'] ?? 0);

if ($scheduleId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$row = ClassSchedule::getDetailWithReviews($scheduleId);

if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'Class not found']);
    exit;
}

$row['specs_list'] = $row['trainer_specs']
    ? array_map('trim', explode(',', $row['trainer_specs']))
    : [];

$row['reviews']     = ClassSchedule::getReviewsForSchedule($scheduleId);
$row['can_review']  = false;
$row['user_review'] = null;

if ($userId && strtotime($row['scheduled_at']) < time()) {
    if (ClassSchedule::isUserEnrolled($userId, $scheduleId)) {
        $row['can_review'] = true;
    }
    $row['user_review'] = ClassSchedule::getUserReview($userId, $scheduleId);
}

echo json_encode($row);