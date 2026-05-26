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
        $total = (int)($_POST['total_quantity'] ?? 0);
        if ($name && $total > 0) {
            ClassSchedule::addEquipment($name, $total, $db);
            Session::addMessage('success', "Equipment '$name' added.");
        } else {
            Session::addMessage('error', 'Name and quantity required.');
        }
        break;

    case 'update':
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $total = (int)($_POST['total_quantity'] ?? 0);
        $available = (int)($_POST['available_quantity'] ?? 0);
        if ($id > 0 && $name && $total >= 0 && $available >= 0) {
            ClassSchedule::updateEquipment($id, $name, $total, $available, $db);
            Session::addMessage('success', 'Equipment updated.');
        } else {
            Session::addMessage('error', 'Invalid input.');
        }
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            ClassSchedule::deleteEquipment($id, $db);
            Session::addMessage('success', 'Equipment deleted.');
        }
        break;
}

header('Location: ../pages/admin_dashboard.php');
exit;
