<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

function respond(int $code, string $error = '', string $success = '', bool $isAjax = false, string $redirect = ''): void {
    http_response_code($code);
    if ($isAjax) {
        echo $error
            ? json_encode(['error' => $error])
            : json_encode(['success' => true, 'message' => $success]);
        exit;
    }
    if ($error) die($error);
    Session::addMessage('success', $success);
    header('Location: ' . $redirect);
    exit;
}

if (!Session::isLoggedIn()) {
    respond(403, 'Access denied', '', $isAjax);
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    respond(403, 'Invalid security token', '', $isAjax);
}

$userId     = Session::getUserId();
$scheduleId = (int)($_POST['schedule_id'] ?? 0);

if ($scheduleId <= 0) {
    respond(400, 'Invalid request', '', $isAjax);
}

if (!ClassSchedule::isUserEnrolled($userId, $scheduleId)) {
    respond(404, 'Not enrolled', '', $isAjax);
}

ClassSchedule::unenrollUser($userId, $scheduleId);

respond(200, '', 'Unenrolled successfully', $isAjax, '../pages/schedule.php');