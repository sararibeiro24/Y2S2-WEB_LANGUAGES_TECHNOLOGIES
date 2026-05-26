<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

header('Content-Type: application/json');

$email = trim($_GET['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'message' => 'Invalid email layout format.']);
    exit;
}

if (Session::isLoggedIn()) {
    $currentUser = User::getById(Session::getUserId());
    if ($currentUser && strtolower($email) === strtolower($currentUser->getEmail())) {
        echo json_encode(['available' => true, 'message' => 'This is your current email address.']);
        exit;
    }
}

$db = getDatabaseConnection();
$stmt = $db->prepare('SELECT id FROM users WHERE LOWER(email) = ?');
$stmt->execute([strtolower($email)]);

if ($stmt->fetch()) {
    echo json_encode(['available' => false, 'message' => 'Email address is already in use.']);
} else {
    echo json_encode(['available' => true, 'message' => 'Email address is available!']);
}
exit;