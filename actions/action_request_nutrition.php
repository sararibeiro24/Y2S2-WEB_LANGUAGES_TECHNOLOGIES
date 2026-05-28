<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

if (!Session::isLoggedIn()) {
    Session::addMessage('error', 'Access denied. You must be logged in.');
    header('Location: ../pages/login.php');
    exit;
}

$userId = Session::getUserId();
$goal = trim($_POST['goal'] ?? '');

if (empty($goal)) {
    Session::addMessage('error', 'Goal selection is required.');
    header('Location: ../pages/nutrition.php');
    exit;
}

$target_calories = 2000;
if ($goal === 'Weight Loss') $target_calories = 1600;
if ($goal === 'Muscle Gain') $target_calories = 2800;

try {
    $db = getDatabaseConnection();
    
    $stmt = $db->prepare('INSERT INTO nutrition_plans (user_id, trainer_id, target_calories, goal, meal_details) VALUES (?, ?, ?, ?, ?)');
    $success = $stmt->execute([
        $userId, 
        3, 
        $target_calories, 
        $goal, 
        'Pending approval from your trainer. Check back soon!'
    ]);

    if ($success) {
        Session::addMessage('success', 'Plan requested successfully!');
    } else {
        Session::addMessage('error', 'Failed to submit request.');
    }
} catch (PDOException $e) {
    Session::addMessage('error', 'Database system fault occurred.');
}

header('Location: ../pages/nutrition.php');
exit;
?>