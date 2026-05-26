<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

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

// Check if enrolled
$stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$stmt->execute([$userId, $scheduleId]);

if (!$stmt->fetch()) {
    http_response_code(404);
    if ($isAjax) { echo json_encode(['error' => 'Not enrolled']); exit; }
    die('Not enrolled');
}

// Unenroll
$stmt = $db->prepare('DELETE FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$stmt->execute([$userId, $scheduleId]);

if ($isAjax) {
    echo json_encode(['success' => true, 'message' => 'Unenrolled successfully']);
    exit;
}

Session::addMessage('success', 'Successfully unenrolled from class.');
header('Location: ../pages/schedule.php');
exit;
