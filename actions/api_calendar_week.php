<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

header('Content-Type: application/json');

$weekStart = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $weekStart)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

$filters = [
    'trainer_id' => isset($_GET['trainer_id']) ? (int)$_GET['trainer_id'] : 0,
    'query'      => trim($_GET['query'] ?? ''),
    'difficulty' => trim($_GET['difficulty'] ?? ''),
];

$weekEnd = date('Y-m-d 23:59:59', strtotime($weekStart . ' +6 days'));
$classes = ClassSchedule::getFilteredWeekClasses($weekStart, $filters);

echo json_encode([
    'week_start' => $weekStart,
    'week_end'   => $weekEnd,
    'classes'    => $classes,
]);