<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['query'] ?? '');
$goal = trim($_GET['goal'] ?? '');
$trainer = trim($_GET['trainer'] ?? '');

$db = getDatabaseConnection();
$totalNutrition = (int)$db->query('SELECT count(*) FROM nutrition_plans')->fetchColumn();
if ($totalNutrition === 0) {
    $db->beginTransaction();
    $db->exec("INSERT INTO nutrition_plans (user_id, trainer_id, target_calories, goal, meal_details) VALUES
        (1, 3, 1800, 'Weight Loss', 'High protein breakfast, light lunch, balanced dinner.'),
        (2, 4, 2500, 'Muscle Gain', 'Calorie surplus with lean proteins and carbs.'),
        (1, 4, 2000, 'Maintenance', 'Three meals with healthy fats and vegetables.')");
    $db->commit();
}

$sql = 'SELECT np.id, np.target_calories, np.goal, np.meal_details, np.created_at,
               member.name AS member_name,
               trainer.name AS trainer_name
        FROM nutrition_plans np
        JOIN users member ON np.user_id = member.id
        JOIN users trainer ON np.trainer_id = trainer.id';
$params = [];
$clauses = [];

if ($query !== '') {
    $clauses[] = '(LOWER(np.goal) LIKE ? OR LOWER(np.meal_details) LIKE ? OR LOWER(member.name) LIKE ? OR LOWER(trainer.name) LIKE ?)';
    $term = '%' . strtolower($query) . '%';
    $params = array_merge($params, [$term, $term, $term, $term]);
}
if ($goal !== '') {
    $clauses[] = 'np.goal = ?';
    $params[] = $goal;
}
if ($trainer !== '') {
    $clauses[] = 'trainer.name = ?';
    $params[] = $trainer;
}

if ($clauses) {
    $sql .= ' WHERE ' . implode(' AND ', $clauses);
}

$sql .= ' ORDER BY np.created_at DESC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

foreach ($data as &$item) {
    $item['target_calories'] = $item['target_calories'] !== null ? (int)$item['target_calories'] : null;
}

echo json_encode($data);
exit;
?>
