<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/nutrition.class.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

if (!Session::isLoggedIn()) {
    Session::addMessage('error', 'Access denied. You must be logged in.');
    header('Location: ../pages/login.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
    header('Location: ../pages/nutrition.php');
    exit;
}

$userId = Session::getUserId();
$goal = trim($_POST['goal'] ?? '');
$trainerId = (int)($_POST['trainer_id'] ?? 0);

if (empty($goal)) {
    Session::addMessage('error', 'Goal selection is required.');
    header('Location: ../pages/nutrition.php');
    exit;
}

if ($trainerId <= 0) {
    Session::addMessage('error', 'You must choose a trainer to request a nutrition plan.');
    header('Location: ../pages/nutrition.php');
    exit;
}

$targetCalories = match($goal) {
    'Weight Loss' => 1600,
    'Muscle Gain' => 2800,
    default       => 2000,
};

try {
    if (!NutritionPlan::isValidTrainer($trainerId)) {
        Session::addMessage('error', 'Invalid trainer selected.');
        header('Location: ../pages/nutrition.php');
        exit;
    }

    if (NutritionPlan::createPlan($userId, $trainerId, $targetCalories, $goal)) {
        Session::addMessage('success', 'Plan requested successfully!');
    } else {
        Session::addMessage('error', 'Failed to submit request.');
    }
} catch (PDOException $e) {
    Session::addMessage('error', 'Database system fault occurred.');
}

header('Location: ../pages/nutrition.php');
exit;