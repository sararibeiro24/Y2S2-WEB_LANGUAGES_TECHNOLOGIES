<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

if (!Session::isLoggedIn() || (!Session::isAdmin() && !Session::isTrainer())) {
    http_response_code(403);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid CSRF token.');
    header('Location: ../pages/dashboard.php');
    exit;
}

$action = $_POST['action'] ?? '';
$db = getDatabaseConnection();
$role = Session::getUserRole();
$currentUserId = Session::getUserId();

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? ''; 

        if (!$name || $capacity <= 0) {
            Session::addMessage('error', 'Name and capacity required.');
            break;
        }

        $existingClass = ClassSchedule::getClassByName($name, $db);

        if ($existingClass) {
            $classId = (int)$existingClass['id'];
            Session::addMessage('success', "Found existing class '$name'.");
        } else {
            if ($role !== 'admin') {
                Session::addMessage('error', 'Trainers can only schedule existing classes.');
                break;
            }
            $desc = trim($_POST['description'] ?? '');
            $difficulty = $_POST['difficulty'] ?? 'Beginner';
            $classId = ClassSchedule::createClass($name, $desc, $capacity, $difficulty, $db);
            Session::addMessage('success', "New class '$name' created into system.");
        }

        if ($classId > 0 && !empty($scheduledAt)) {
            $trainerId = ($role === 'trainer') ? $currentUserId : (int)($_POST['trainer_id'] ?? $currentUserId);
            
            if (ClassSchedule::trainerHasClassAtTime($trainerId, $scheduledAt, null, $db)) {
                Session::addMessage('error', 'You already have a class scheduled for this time!');
            } else {
                ClassSchedule::addSchedule($classId, $trainerId, $scheduledAt, $db);
                Session::addMessage('success', 'Schedule slot successfully added.');
            }
        }
        break;

    case 'update':
        if ($role !== 'admin') {
            http_response_code(403);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $difficulty = $_POST['difficulty'] ?? 'Beginner';
        
        if ($id > 0 && $name && $capacity > 0) {
            ClassSchedule::updateClass($id, $name, $desc, $capacity, $difficulty, $db);
            Session::addMessage('success', "Class updated.");
        }
        break;

    case 'delete':
        if ($role !== 'admin') {
            http_response_code(403);
            exit;
        }
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            ClassSchedule::deleteClass($id, $db);
            Session::addMessage('success', "Class deleted.");
        }
        break;

    case 'add_schedule':
        $classId = (int)($_POST['class_id'] ?? 0);
        $trainerId = ($role === 'trainer') ? $currentUserId : (int)($_POST['trainer_id'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? '';

        if ($classId > 0 && $trainerId > 0 && $scheduledAt) {
            if (ClassSchedule::trainerHasClassAtTime($trainerId, $scheduledAt, null, $db)) {
                Session::addMessage('error', 'Trainer has a schedule conflict at this time.');
            } else {
                ClassSchedule::addSchedule($classId, $trainerId, $scheduledAt, $db);
                Session::addMessage('success', 'Schedule slot added.');
            }
        }
        break;

    case 'remove_schedule':
       
        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        if ($scheduleId > 0) {
            ClassSchedule::clearScheduleEnrollments($scheduleId, $db);
            ClassSchedule::removeSchedule($scheduleId, $db);
            Session::addMessage('success', 'Schedule removed.');
        }
        break;
}

header('Location: ../pages/dashboard.php?tab=classes');
exit;