<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isTrainer()) {
    Session::addMessage('error', 'Access denied. Only trainers can approve nutrition plans.');
    header('Location: ../pages/login.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
    header('Location: ../pages/dashboard.php?tab=nutrition');
    exit;
}

$trainerId = Session::getUserId();
$nutritionId = (int)($_POST['nutrition_id'] ?? 0);
$mealDetails = trim($_POST['meal_details'] ?? '');

if ($nutritionId <= 0) {
    Session::addMessage('error', 'Invalid nutrition plan ID.');
    header('Location: ../pages/dashboard.php?tab=nutrition');
    exit;
}

if (empty($mealDetails)) {
    Session::addMessage('error', 'Meal plan details are required.');
    header('Location: ../pages/dashboard.php?tab=nutrition');
    exit;
}

try {
    $db = getDatabaseConnection();
    
    // Verify the nutrition plan exists and belongs to this trainer
    $verifyStmt = $db->prepare('SELECT id FROM nutrition_plans WHERE id = ? AND trainer_id = ?');
    $verifyStmt->execute([$nutritionId, $trainerId]);
    
    if (!$verifyStmt->fetch()) {
        Session::addMessage('error', 'This nutrition plan does not belong to you or does not exist.');
        header('Location: ../pages/dashboard.php?tab=nutrition');
        exit;
    }
    
    // Update the nutrition plan with the meal details
    $updateStmt = $db->prepare('UPDATE nutrition_plans SET meal_details = ? WHERE id = ?');
    $success = $updateStmt->execute([$mealDetails, $nutritionId]);
    
    if ($success) {
        Session::addMessage('success', 'Nutrition plan approved and completed successfully!');
    } else {
        Session::addMessage('error', 'Failed to update the nutrition plan.');
    }
} catch (PDOException $e) {
    Session::addMessage('error', 'Database error occurred.');
    error_log('Database error in action_approve_nutrition: ' . $e->getMessage());
}

header('Location: ../pages/dashboard.php?tab=nutrition');
exit;
?>
