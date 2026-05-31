<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

if (!Session::isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$db = getDatabaseConnection();
$userId = Session::getUserId();

$stmt = $db->prepare('
    SELECT np.id, np.goal, np.target_calories, np.meal_details, np.created_at,
           u.name AS trainer_name
    FROM nutrition_plans np
    JOIN users u ON np.trainer_id = u.id
    WHERE np.user_id = ?
    ORDER BY np.created_at DESC
');
$stmt->execute([$userId]);
$plans = $stmt->fetchAll();

echo json_encode(['success' => true, 'plans' => $plans]);
