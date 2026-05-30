<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/profile.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$userId = Session::getUserId();
$user = User::getById($userId);

if (!$user) {
    header('Location: logout.php');
    exit;
}

$Name = $user->getName();
$Username = $user->getUsername();
$Email = $user->getEmail();
$ProfilePhoto = resolvePhoto($user->getProfilePhoto()) ?? '../img/default.png';
$PlanName = $user->getPlanName();
$CreatedAt = $user->getCreatedAt();
$classesAttended = $user->getClassesAttendedCount();
$upcomingClasses = $user->getUpcomingClassesCount();
$Role = $user->getRole();
$nutritionPlan = null;

try {
    $db = getDatabaseConnection();
    $stmt = $db->prepare('SELECT np.goal, np.target_calories, np.meal_details, np.created_at, t.name AS trainer_name
                         FROM nutrition_plans np
                         JOIN users t ON np.trainer_id = t.id
                         WHERE np.user_id = ?
                         ORDER BY np.created_at DESC
                         LIMIT 1');
    $stmt->execute([$userId]);
    $nutritionPlan = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    error_log('Database error fetching nutrition plan: ' . $e->getMessage());
    $nutritionPlan = null;
}

drawHead("My Profile | Ladybug's Gym");
drawHeader();
drawMessages();
drawProfileForm($Name, $Username, $Email, $ProfilePhoto, $PlanName, $CreatedAt, $classesAttended, $upcomingClasses, $Role, $nutritionPlan);
drawFooter();
