<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/nutrition.class.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['query'] ?? '');
$goal = trim($_GET['goal'] ?? '');
$trainer = trim($_GET['trainer'] ?? '');

$db = getDatabaseConnection();

$filters = [
    'query' => $query,
    'goal' => $goal,
    'trainer_id' => $trainer !== '' ? (int)$trainer : null,
    'exclude_pending' => true,
];

if (!Session::isAdmin() && Session::isLoggedIn()) {
    $filters['user_id'] = Session::getUserId();
}

$data = NutritionPlan::getPlans($filters, $db);

echo json_encode($data);
exit;
?>