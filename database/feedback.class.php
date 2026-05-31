<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class Feedback {
    public static function getApprovedFeedback(int $limit = 6, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->query('SELECT name, message, rating FROM feedback WHERE rating >= 4 ORDER BY rating DESC, created_at DESC LIMIT ' . $limit);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
