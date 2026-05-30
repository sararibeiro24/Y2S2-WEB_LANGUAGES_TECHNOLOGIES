<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/trainer_dashboard.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isTrainer()) {
    header('Location: ../pages/login.php');
    exit;
}

$userId = Session::getUserId();
$user = User::getById($userId);

if (!$user) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

$stmt = $db->prepare('SELECT bio, specializations, certifications FROM trainer_profiles WHERE user_id = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch();

$bio = $profile['bio'] ?? '';
$specializations = $profile['specializations'] ?? '';
$certifications = $profile['certifications'] ?? '';

$classes = ClassSchedule::getTrainerUpcomingSchedules($userId, $db);

drawHead("Trainer Dashboard | Ladybug's Gym");
drawHeader();
drawMessages();

drawTrainerDashboard($bio, $specializations, $certifications, $classes, $db);

drawFooter();