<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/trainer.class.php');

header('Content-Type: application/json');

$trainerId = (int)($_GET['id'] ?? 0);

if ($trainerId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$trainer = Trainer::getById($trainerId);

if (!$trainer) {
    http_response_code(404);
    echo json_encode(['error' => 'Trainer not found']);
    exit;
}

$trainer['specializations_list'] = $trainer['specializations']
    ? array_map('trim', explode(',', $trainer['specializations']))
    : [];

$trainer['classes']           = Trainer::getClassesById($trainerId);
$trainer['upcoming_sessions'] = Trainer::getUpcomingSessions($trainerId);

echo json_encode($trainer);