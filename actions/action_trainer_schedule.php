<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isTrainer()) {
    header('Location: ../pages/login.php');
    exit;
}

$userId = Session::getUserId();
$db = getDatabaseConnection();
$action = $_POST['action'] ?? '';

if ($action === 'add') {
    $classId = (int)($_POST['class_id'] ?? 0);
    $scheduledAt = trim($_POST['scheduled_at'] ?? '');

    if (!$classId || !$scheduledAt) {
        Session::addMessage('error', 'Please fill in all fields.');
        header('Location: ../pages/manage_classes.php');
        exit;
    }
    $scheduledAt = date('Y-m-d H:i:s', strtotime($scheduledAt));

    if (ClassSchedule::trainerHasClassAtTime($userId, $scheduledAt, null, $db)) {
    Session::addMessage('error', 'You already have a class at that time.');
    header('Location: ../pages/manage_classes.php');
    exit;
    }

    ClassSchedule::addSchedule($classId, $userId, $scheduledAt, $db);
    Session::addMessage('success', 'Class slot added.');

} elseif ($action === 'update') {
    $scheduleId = (int)($_POST['schedule_id'] ?? 0);
    $scheduledAt = trim($_POST['scheduled_at'] ?? '');
    if (!$scheduleId || !$scheduledAt) {
        Session::addMessage('error', 'Invalid data.');
        header('Location: ../pages/manage_classes.php');
        exit;
    }
    $scheduledAt = date('Y-m-d H:i:s', strtotime($scheduledAt));

    if (ClassSchedule::trainerHasClassAtTime($userId, $scheduledAt, $scheduleId, $db)) {
    Session::addMessage('error', 'You already have a class at that time.');
    header('Location: ../pages/manage_classes.php');
    exit;
    }
    
    $schedule = ClassSchedule::getById($scheduleId, $db);

    if (!$schedule || $schedule->getTrainerId() !== $userId) {
        Session::addMessage('error', 'You can only edit your own classes.');
        header('Location: ../pages/manage_classes.php');
        exit;
    }
    ClassSchedule::updateSchedule($scheduleId, $scheduledAt, $db);
    Session::addMessage('success', 'Schedule updated.');

} elseif ($action === 'delete') {
    $scheduleId = (int)($_POST['schedule_id'] ?? 0);

    if (!$scheduleId) {
        Session::addMessage('error', 'Invalid schedule.');
        header('Location: ../pages/manage_classes.php');
        exit;
    }

    $schedule = ClassSchedule::getById($scheduleId, $db);

    if (!$schedule || $schedule->getTrainerId() !== $userId) {
        Session::addMessage('error', 'You can only delete your own classes.');
        header('Location: ../pages/manage_classes.php');
        exit;
    }

    ClassSchedule::removeSchedule($scheduleId, $db);
    Session::addMessage('success', 'Schedule slot removed.');

} else {
    Session::addMessage('error', 'Invalid action.');
}

header('Location: ../pages/manage_classes.php');
exit;