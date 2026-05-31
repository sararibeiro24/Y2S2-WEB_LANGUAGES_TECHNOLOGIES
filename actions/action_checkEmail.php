<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

try {
    $email = trim($_GET['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['available' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    if (Session::isLoggedIn()) {
        $currentUser = User::getById(Session::getUserId());
        if ($currentUser && strtolower($email) === strtolower($currentUser->getEmail())) {
            echo json_encode(['available' => true, 'message' => 'This is your current email address.']);
            exit;
        }
    }

    if (User::isEmailAvailable($email)) {
        echo json_encode(['available' => true,  'message' => 'Email address is available!']);
    } else {
        echo json_encode(['available' => false, 'message' => 'Email address is already in use.']);
    }
} catch (Exception $e) {
    error_log('Error in action_checkEmail: ' . $e->getMessage());
    echo json_encode(['available' => false, 'message' => 'Server error. Please try again later.']);
}
exit;