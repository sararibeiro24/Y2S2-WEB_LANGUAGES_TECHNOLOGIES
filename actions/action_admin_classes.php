<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();
if (!Session::isLoggedIn() || !Session::isAdmin()) {
    http_response_code(403);
    exit;
}

$action = $_POST['action'] ?? '';
$db = getDatabaseConnection();

switch ($action) {
    case 'create':
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        $capacity = (int)($_POST['capacity'] ?? 0);
        $difficulty = $_POST['difficulty'] ?? 'Beginner';
        if ($name && $capacity > 0) {
            ClassSchedule::createClass($name, $desc, $capacity, $difficulty, $db);
            Session::addMessage('success', "Class '$name' created.");
        } else {
            Session::addMessage('error', 'Name and capacity required.');
        }
        break;

    case 'update':
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
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            ClassSchedule::deleteClass($id, $db);
            Session::addMessage('success', "Class deleted.");
        }
        break;

    case 'add_schedule':
        $classId = (int)($_POST['class_id'] ?? 0);
        $trainerId = (int)($_POST['trainer_id'] ?? 0);
        $scheduledAt = $_POST['scheduled_at'] ?? '';
        if ($classId > 0 && $trainerId > 0 && $scheduledAt) {
            ClassSchedule::addSchedule($classId, $trainerId, $scheduledAt, $db);
            Session::addMessage('success', 'Schedule added.');
        }
        break;

    case 'remove_schedule':
        $scheduleId = (int)($_POST['schedule_id'] ?? 0);
        if ($scheduleId > 0) {
            // Delete enrollments first
            $db->prepare('DELETE FROM enrollments WHERE schedule_id = ?')->execute([$scheduleId]);
            ClassSchedule::removeSchedule($scheduleId, $db);
            Session::addMessage('success', 'Schedule removed.');
        }
        break;
}

header('Location: ../pages/admin_dashboard.php');
exit;
