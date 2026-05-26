<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

if (!Session::isLoggedIn()) {
    http_response_code(403);
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) { echo json_encode(['error' => 'Access denied']); exit; }
    die('Access denied');
}

$userId = Session::getUserId();
$scheduleId = (int) ($_POST['schedule_id'] ?? 0);
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($scheduleId <= 0) {
    http_response_code(400);
    if ($isAjax) { echo json_encode(['error' => 'Invalid request']); exit; }
    die('Invalid request');
}

$db = getDatabaseConnection();

// Check capacity and prevent duplicates in one go
$stmt = $db->prepare('
    SELECT
        c.capacity,
        COUNT(e.id) AS enrolled
    FROM class_schedule cs
    JOIN classes c ON c.id = cs.class_id
    LEFT JOIN enrollments e ON e.schedule_id = cs.id
    WHERE cs.id = ?
');
$stmt->execute([$scheduleId]);
$class = $stmt->fetch();

if (!$class) {
    http_response_code(404);
    if ($isAjax) { echo json_encode(['error' => 'Class not found']); exit; }
    die('Class not found');
}

if ($class['enrolled'] >= $class['capacity']) {
    http_response_code(409);
    if ($isAjax) { echo json_encode(['error' => 'Class is full']); exit; }
    die('Class is full');
}

// Check for duplicate enrollment
$stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$stmt->execute([$userId, $scheduleId]);

if ($stmt->fetch()) {
    http_response_code(409);
    if ($isAjax) { echo json_encode(['error' => 'Already enrolled']); exit; }
    die('Already enrolled');
}

// Enroll
$stmt = $db->prepare('INSERT INTO enrollments (user_id, schedule_id) VALUES (?, ?)');
$stmt->execute([$userId, $scheduleId]);

if ($isAjax) {
    echo json_encode(['success' => true, 'message' => 'Enrolled successfully']);
    exit;
}

Session::addMessage('success', 'Successfully enrolled!');
header('Location: ../pages/schedule.php');
exit;
