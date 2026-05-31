<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class Review {
    public static function getSchedule(int $scheduleId, ?PDO $db = null): ?array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT cs.id, cs.class_id, cs.scheduled_at
            FROM class_schedule cs
            WHERE cs.id = ?
        ');
        $stmt->execute([$scheduleId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function isEnrolled(int $userId, int $scheduleId, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
        $stmt->execute([$userId, $scheduleId]);
        return (bool)$stmt->fetch();
    }

    public static function upsert(int $userId, int $classId, int $scheduleId, int $rating, string $comment, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            INSERT INTO reviews (user_id, class_id, schedule_id, rating, comment)
            VALUES (?, ?, ?, ?, ?)
            ON CONFLICT(user_id, schedule_id) DO UPDATE SET
                rating = excluded.rating,
                comment = excluded.comment,
                created_at = CURRENT_TIMESTAMP
        ');
        return $stmt->execute([$userId, $classId, $scheduleId, $rating, $comment]);
    }
}