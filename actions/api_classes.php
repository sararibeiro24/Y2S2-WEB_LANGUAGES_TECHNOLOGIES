<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

header('Content-Type: application/json');

$filters = [
    'search'     => $_GET['search']  ?? '',
    'trainer_id' => $_GET['trainer'] ?? '',
    'date'       => $_GET['date']    ?? '',
];

echo json_encode(ClassSchedule::getUpcomingFiltered($filters));