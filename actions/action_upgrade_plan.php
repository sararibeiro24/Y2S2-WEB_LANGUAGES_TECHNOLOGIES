<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');

Session::start();

if (!Session::isLoggedIn()) {
    Session::addMessage('error', 'You must be logged in to change your plan.');
    header('Location: ../pages/login.php');
    exit;
}

$planId = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
$csrfToken = $_POST['csrf_token'] ?? '';

if (!$planId || !Session::validateCsrfToken($csrfToken)) {
    Session::addMessage('error', 'Invalid request. Please try again.');
    header('Location: ../pages/plans.php');
    exit;
}

$userId = Session::getUserId();

$db = getDatabaseConnection();
$stmt = $db->prepare('SELECT id FROM plans WHERE id = ?');
$stmt->execute([$planId]);
if (!$stmt->fetch()) {
    Session::addMessage('error', 'Selected plan does not exist.');
    header('Location: ../pages/plans.php');
    exit;
}

$stmt = $db->prepare('UPDATE users SET plan_id = ? WHERE id = ?');
$stmt->execute([$planId, $userId]);

Session::addMessage('success', 'Your membership plan was updated successfully.');
header('Location: ../pages/plans.php');
exit;
?>
