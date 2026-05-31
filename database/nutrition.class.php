<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class NutritionPlan {
    public static function getPlans(array $filters = [], ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();

        $sql = 'SELECT np.id, np.target_calories, np.goal, np.meal_details, np.created_at, member.name AS member_name, trainer.name AS trainer_name
                FROM nutrition_plans np
                JOIN users member ON np.user_id = member.id
                JOIN users trainer ON np.trainer_id = trainer.id
                WHERE 1=1';

        $params = [];

        if (!empty($filters['user_id'])) {
            $sql .= ' AND np.user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['query'])) {
            $sql .= ' AND (LOWER(np.goal) LIKE ? OR LOWER(np.meal_details) LIKE ? OR LOWER(trainer.name) LIKE ?)';
            $term = '%' . strtolower($filters['query']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['goal'])) {
            $sql .= ' AND np.goal = ?';
            $params[] = $filters['goal'];
        }

        if (!empty($filters['trainer_id'])) {
            $sql .= ' AND np.trainer_id = ?';
            $params[] = $filters['trainer_id'];
        }

        if (!empty($filters['exclude_pending'])) {
            $sql .= ' AND np.meal_details NOT LIKE ?';
            $params[] = 'Pending approval%';
        }

        $sql .= ' ORDER BY np.id DESC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['target_calories'] = $row['target_calories'] !== null ? (int)$row['target_calories'] : null;
        }

        return $rows;
    }

    public static function getLatestPlanForUser(int $userId, ?PDO $db = null): ?array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT np.goal, np.target_calories, np.meal_details, np.created_at, t.name AS trainer_name
                              FROM nutrition_plans np
                              JOIN users t ON np.trainer_id = t.id
                              WHERE np.user_id = ?
                              ORDER BY np.created_at DESC
                              LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $row['target_calories'] = $row['target_calories'] !== null ? (int)$row['target_calories'] : null;
        return $row;
    }

    public static function getRequestsForTrainer(int $trainerId, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT np.id, np.goal, np.target_calories, np.meal_details, np.created_at,
                                      u.name AS member_name, u.email AS member_email,
                                      CASE WHEN np.meal_details LIKE ? THEN 0 ELSE 1 END AS is_approved
                              FROM nutrition_plans np
                              JOIN users u ON np.user_id = u.id
                              WHERE np.trainer_id = ?
                              ORDER BY is_approved ASC, np.created_at DESC');
        $stmt->execute(['Pending approval%', $trainerId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['target_calories'] = $row['target_calories'] !== null ? (int)$row['target_calories'] : null;
        }

        return $rows;
    }

    public static function belongsToTrainer(int $nutritionId, int $trainerId, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT id FROM nutrition_plans WHERE id = ? AND trainer_id = ?');
        $stmt->execute([$nutritionId, $trainerId]);
        return (bool)$stmt->fetch();
    }

    public static function approvePlan(int $nutritionId, string $mealDetails, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('UPDATE nutrition_plans SET meal_details = ? WHERE id = ?');
        return $stmt->execute([$mealDetails, $nutritionId]);
    }
    public static function isValidTrainer(int $trainerId, ?PDO $db = null): bool {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('SELECT id FROM users WHERE id = ? AND role = "trainer"');
    $stmt->execute([$trainerId]);
    return (bool)$stmt->fetch();
}

public static function createPlan(int $userId, int $trainerId, int $targetCalories, string $goal, ?PDO $db = null): bool {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('INSERT INTO nutrition_plans (user_id, trainer_id, target_calories, goal, meal_details) VALUES (?, ?, ?, ?, ?)');
    return $stmt->execute([
        $userId,
        $trainerId,
        $targetCalories,
        $goal,
        'Pending approval from your trainer. Check back soon!'
    ]);
}
}
