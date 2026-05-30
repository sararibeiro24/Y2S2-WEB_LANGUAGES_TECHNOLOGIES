<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/manage_classes.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isTrainer()) {
    header('Location: ../pages/login.php');
    exit;
}

$userId = Session::getUserId();
$db = getDatabaseConnection();

$mySchedules = ClassSchedule::getTrainerSchedules($userId, $db);
$allClasses = ClassSchedule::getAllClasses($db);

drawHead("Manage Classes | Ladybug's Gym");
drawHeader();
drawMessages();
drawManageClasses($mySchedules, $allClasses);
drawFooter();