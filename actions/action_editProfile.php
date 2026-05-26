<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

// Kick unauthorized access attempts out
if (!Session::isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

$userId = Session::getUserId();
$user = User::getById($userId);

if (!$user) {
    header('Location: action_logout.php');
    exit;
}

$hasChanges = false;


$newEmail = trim($_POST['email'] ?? '');
if (!empty($newEmail) && $newEmail !== $user->getEmail()) {
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        Session::addMessage('error', 'Invalid email format.');
        header('Location: ../pages/profile.php');
        exit;
    }
    
    $user->updateEmail($newEmail);
    $hasChanges = true;
}

$newPassword = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

if (!empty($newPassword) || !empty($confirmPassword)) {
    if ($newPassword !== $confirmPassword) {
        Session::addMessage('error', 'Passwords do not match.');
        header('Location: ../pages/profile.php');
        exit;
    } 
    
    if (strlen($newPassword) < 6) {
        Session::addMessage('error', 'Password must be at least 6 characters.');
        header('Location: ../pages/profile.php');
        exit;
    }

    $user->updatePassword($newPassword);
    $hasChanges = true;
}


if (!empty($_FILES['profile_photo']['name'])) {
    $file = $_FILES['profile_photo'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if (in_array($file['type'], $allowedTypes) && $file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user->getId() . '_' . time() . '.' . $ext;
        $uploadDir = dirname(__DIR__) . '/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $user->updateProfilePhoto('/uploads/' . $filename);
            $hasChanges = true;
        } else {
            Session::addMessage('error', 'Failed to save uploaded image.');
        }
    } else {
        Session::addMessage('error', 'Invalid image format. Only JPG, PNG, and WebP are allowed.');
    }
}

if ($hasChanges) {
    Session::addMessage('success', 'Profile updated successfully!');
} else {
    Session::addMessage('error', 'No changes were made.');
}

header('Location: ../pages/profile.php');
exit;