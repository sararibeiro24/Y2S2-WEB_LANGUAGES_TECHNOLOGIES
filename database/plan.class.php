<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class Plan {
    public static function getAllPlans(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->query('SELECT id, name, price, billing_cycle, features FROM plans ORDER BY price ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPlans(array $filters = [], ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();

        $sql = 'SELECT id, name, price, billing_cycle, features FROM plans';
        $params = [];
        $clauses = [];

        if (!empty($filters['query'])) {
            $clauses[] = '(name LIKE ? OR features LIKE ?)';
            $term = '%' . $filters['query'] . '%';
            $params[] = $term;
            $params[] = $term;
        }

        if (!empty($filters['billing_cycle'])) {
            $clauses[] = 'billing_cycle = ?';
            $params[] = $filters['billing_cycle'];
        }

        if ($clauses) {
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= ' ORDER BY price ASC';

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getUserPlanId(int $userId, ?PDO $db = null): ?int {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT plan_id FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || $row['plan_id'] === null) {
            return null;
        }

        return (int)$row['plan_id'];
    }
}
