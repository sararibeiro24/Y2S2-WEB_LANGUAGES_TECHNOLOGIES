<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/dashboard.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');

Session::start();

if (!Session::isLoggedIn() || (!Session::isAdmin() && !Session::isTrainer())) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();
$role = Session::getUserRole();
$userId = Session::getUserId();

$tab = $_GET['tab'] ?? (($role === 'admin') ? 'users' : 'classes');
if ($role === 'trainer' && ($tab === 'users' || $tab === 'equipment')) {
    $tab = 'classes';
}

$users = [];
$equipment = [];
$nutritionPlans = [];
$classes = [];
$trainers = [];

if ($tab === 'users' && $role === 'admin') {
    $users = User::getAllUsers($db);
} 

elseif ($tab === 'classes') {
    $classes = ClassSchedule::getAllClasses($db);
    $trainers = ($role === 'admin') ? Trainer::getAllTrainers($db) : [];
} 

elseif ($tab === 'equipment' && $role === 'admin') {
    $equipment = ClassSchedule::getAllEquipment($db);
} 

elseif ($tab === 'nutrition' && $role === 'trainer') {

    $nutritionPlans = []; // faz aqui as coisas
}

drawHead("Dashboard | Ladybug's Gym");
drawHeader();
drawMessages();

drawDashboardMain($role, $tab, $classes, $trainers, $users, $equipment, $nutritionPlans, $db, $userId);

drawFooter();
?>