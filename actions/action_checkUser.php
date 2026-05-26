<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/user.class.php');


header('Content-Type: application/json');


$username = trim($_GET['username'] ?? '');

if (empty($username)) {
    echo json_encode(['available' => false, 'message' => 'Username cannot be empty.']);
    exit;
}

try {

    $user = User::getByUsernameOrEmail($username);

    if ($user === null) {

        echo json_encode(['available' => true, 'message' => 'Username is available!']);
    } else {
        echo json_encode(['available' => false, 'message' => 'Username is already taken.']);
    }
} catch (Exception $e) {
    echo json_encode(['available' => false, 'message' => 'Database error.']);
}
exit;