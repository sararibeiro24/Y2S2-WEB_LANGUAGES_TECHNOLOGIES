<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');
require_once(__DIR__ . '/../database/database.db.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isTrainer()) {
    Session::addMessage('error', 'Access denied.');
    header('Location: ../pages/login.php');
    exit;
}

$userId = Session::getUserId();
$bio = trim($_POST['bio'] ?? '');
$specializations = trim($_POST['specializations'] ?? '');
$certifications = trim($_POST['certifications'] ?? '');

$user = User::getById($userId);
if (!$user) {
    Session::addMessage('error', 'User not found.');
    header('Location: ../pages/trainer_dashboard.php');
    exit;
}

$db = getDatabaseConnection();
$trainer = new Trainer(
    $userId,
    $user->getUsername(),
    $user->getEmail(),
    $user->getName(),
    $user->getProfilePhoto() ?? '',
    $bio,
    $specializations,
    $certifications,
    0,
    $db
);

if ($trainer->updateProfile($bio, $specializations, $certifications)) {
    Session::addMessage('success', 'Profile updated successfully.');
} else {
    Session::addMessage('error', 'Failed to update profile.');
}

header('Location: ../pages/trainer_dashboard.php');
exit;
