<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

if (!Session::isLoggedIn()) {
    http_response_code(403);
    die('Access denied');
}

$userId = Session::getUserId();
$scheduleId = (int) ($_POST['schedule_id'] ?? 0);

if ($scheduleId <= 0) {
    http_response_code(400);
    die('Invalid request');
}

$db = getDatabaseConnection();

// Check if enrolled
$stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$stmt->execute([$userId, $scheduleId]);

if (!$stmt->fetch()) {
    http_response_code(404);
    die('Not enrolled');
}

// Unenroll
$stmt = $db->prepare('DELETE FROM enrollments WHERE user_id = ? AND schedule_id = ?');
$stmt->execute([$userId, $scheduleId]);

Session::addMessage('success', 'Successfully unenrolled from class.');
header('Location: ../pages/schedule.php');
exit;
