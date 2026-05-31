<?php
declare(strict_types=1);
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {

    $username = trim($_GET['username'] ?? '');

    if (empty($username)) {
        echo json_encode(['available' => false, 'message' => 'Username cannot be empty.']);
        exit;
    }

    if (strlen($username) < 3) {
        echo json_encode(['available' => false, 'message' => 'Username must be at least 3 characters.']);
        exit;
    }

    if (User::isUsernameAvailable($username)) {
        echo json_encode(['available' => true,  'message' => 'Username is available!']);
    } else {
        echo json_encode(['available' => false, 'message' => 'Username is already taken.']);
    }
} catch (Exception $e) {
    error_log('Error in action_checkUser: ' . $e->getMessage());
    echo json_encode(['available' => false, 'message' => 'Server error. Please try again later.']);
}
exit;