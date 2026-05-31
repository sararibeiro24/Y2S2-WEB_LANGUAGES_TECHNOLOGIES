<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/feedback.class.php');

Session::start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
    header('Location: ../pages/index.php#feedback');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');
$rating  = (int)($_POST['rating'] ?? 0);

if (empty($name) || empty($message) || $rating < 1 || $rating > 5) {
    Session::addMessage('error', 'All fields are required and rating must be between 1 and 5.');
    header('Location: ../pages/index.php#feedback');
    exit;
}

try {
    $userId = Session::isLoggedIn() ? Session::getUserId() : null;

    Feedback::submit($userId, $name, $message, $rating)
        ? Session::addMessage('success', 'Thank you for your feedback!')
        : Session::addMessage('error', 'Failed to submit feedback.');
} catch (PDOException $e) {
    error_log('Feedback error: ' . $e->getMessage());
    Session::addMessage('error', 'Failed to submit feedback.');
}

header('Location: ../pages/index.php#feedback');
exit;