<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

if (!Session::isLoggedIn()) {
    header('Location: ../pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/profile.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
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
    
   try {
        $user->updateEmail($newEmail);
        $hasChanges = true;
       
    } catch (PDOException $e) {
        // Check if it's a unique constraint violation error code (SQLSTATE 23000)
        if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'UNIQUE')) {
            Session::addMessage('error', 'This email address is already taken by another account.');
        } else {
            // Treat unexpected database faults generally
            Session::addMessage('error', 'A database error occurred while saving your changes.');
        }
        header('Location: ../pages/profile.php');
        exit;
    }
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

   if ($user->updatePassword($newPassword)) {
        $hasChanges = true;
    } else {
        Session::addMessage('error', 'New password cannot be the same as your previous password.');
        header('Location: ../pages/profile.php');
        exit;
    }
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
            $oldPhoto = $user->getProfilePhoto();
            if ($oldPhoto && str_starts_with($oldPhoto, 'uploads/')) {
                $oldPath = dirname(__DIR__) . '/' . $oldPhoto;
                if (file_exists($oldPath)) {
                unlink($oldPath);
                }
            }
            $user->updateProfilePhoto('uploads/' . $filename);
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