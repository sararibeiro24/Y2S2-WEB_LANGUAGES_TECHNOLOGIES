<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class ClassSchedule {
    private int $id;
    private int $classId;
    private int $trainerId;
    private string $scheduledAt;
    private ?string $className;
    private ?string $classDescription;
    private ?int $capacity;
    private ?string $trainerName;
    private ?PDO $db;

    public function __construct(
        int $id,
        int $classId,
        int $trainerId,
        string $scheduledAt,
        ?string $className = null,
        ?string $classDescription = null,
        ?int $capacity = null,
        ?string $trainerName = null,
        ?PDO $db = null
    ) {
        $this->id = $id;
        $this->classId = $classId;
        $this->trainerId = $trainerId;
        $this->scheduledAt = $scheduledAt;
        $this->className = $className;
        $this->classDescription = $classDescription;
        $this->capacity = $capacity;
        $this->trainerName = $trainerName;
        $this->db = $db ?? getDatabaseConnection();
    }

    public function getId(): int { return $this->id; }
    public function getClassId(): int { return $this->classId; }
    public function getTrainerId(): int { return $this->trainerId; }
    public function getScheduledAt(): string { return $this->scheduledAt; }
    public function getClassName(): ?string { return $this->className; }
    public function getClassDescription(): ?string { return $this->classDescription; }
    public function getCapacity(): ?int { return $this->capacity; }
    public function getTrainerName(): ?string { return $this->trainerName; }

    public function getEnrolledCount(): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM enrollments WHERE schedule_id = ?');
        $stmt->execute([$this->id]);
        return (int)$stmt->fetchColumn();
    }

    public function isFull(): bool {
        if ($this->capacity === null) {
            return false;
        }
        return $this->getEnrolledCount() >= $this->capacity;
    }

    public function enrollUser(int $userId): void {
        if ($this->isFull()) {
            throw new Exception('Class is full.');
        }
        $stmt = $this->db->prepare('
            INSERT OR IGNORE INTO enrollments (user_id, schedule_id)
            VALUES (?, ?)
        ');
        $stmt->execute([$userId, $this->id]);
    }

    public static function getById(int $id, ?PDO $db = null): ?self {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT
                cs.id,
                cs.class_id,
                cs.trainer_id,
                cs.scheduled_at,
                c.name AS class_name,
                c.description AS class_description,
                c.capacity,
                u.name AS trainer_name
            FROM class_schedule cs
            JOIN classes c ON cs.class_id = c.id
            JOIN users u ON cs.trainer_id = u.id
            WHERE cs.id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new self(
            $row['id'],
            $row['class_id'],
            $row['trainer_id'],
            $row['scheduled_at'],
            $row['class_name'],
            $row['class_description'],
            $row['capacity'],
            $row['trainer_name'],
            $db
        );
    }

    public static function getUpcomingClasses(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT
                cs.id,
                cs.class_id,
                cs.trainer_id,
                cs.scheduled_at,
                c.name AS class_name,
                c.description AS class_description,
                c.capacity,
                u.name AS trainer_name
            FROM class_schedule cs
            JOIN classes c ON cs.class_id = c.id
            JOIN users u ON cs.trainer_id = u.id
            WHERE cs.scheduled_at >= datetime(\'now\')
            ORDER BY cs.scheduled_at ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $schedules = [];
        foreach ($rows as $row) {
            $schedules[] = new self(
                $row['id'],
                $row['class_id'],
                $row['trainer_id'],
                $row['scheduled_at'],
                $row['class_name'],
                $row['class_description'],
                $row['capacity'],
                $row['trainer_name'],
                $db
            );
        }
        return $schedules;
    }

    public static function getUpcomingWithStats(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT
                cs.id AS schedule_id,
                cs.class_id,
                cs.trainer_id,
                cs.scheduled_at,
                c.name,
                c.description,
                c.capacity,
                c.difficulty,
                u.name AS trainer,
                COUNT(e.id) AS enrolled
            FROM class_schedule cs
            JOIN classes c ON cs.class_id = c.id
            JOIN users u ON cs.trainer_id = u.id
            LEFT JOIN enrollments e ON e.schedule_id = cs.id
            WHERE cs.scheduled_at >= datetime(\'now\')
            GROUP BY cs.id
            ORDER BY cs.scheduled_at ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getWeekClasses(string $weekStart, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $weekEnd = date('Y-m-d 23:59:59', strtotime($weekStart . ' +6 days'));
        $stmt = $db->prepare('
            SELECT
                cs.id AS schedule_id,
                cs.class_id,
                cs.trainer_id,
                cs.scheduled_at,
                c.name,
                c.description,
                c.capacity,
                c.difficulty,
                u.name AS trainer,
                u.id AS trainer_user_id,
                COUNT(e.id) AS enrolled
            FROM class_schedule cs
            JOIN classes c ON cs.class_id = c.id
            JOIN users u ON cs.trainer_id = u.id
            LEFT JOIN enrollments e ON e.schedule_id = cs.id
            WHERE cs.scheduled_at >= ? AND cs.scheduled_at <= ?
            GROUP BY cs.id
            ORDER BY cs.scheduled_at ASC
        ');
        $stmt->execute([$weekStart, $weekEnd]);
        return $stmt->fetchAll();
    }

    public static function getUserEnrollmentIds(int $userId, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT schedule_id FROM enrollments WHERE user_id = ?');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getTrainerSchedules(int $trainerId, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT
                cs.id AS schedule_id,
                cs.scheduled_at,
                c.name,
                c.capacity,
                c.difficulty,
                COUNT(e.id) AS enrolled
            FROM class_schedule cs
            JOIN classes c ON cs.class_id = c.id
            LEFT JOIN enrollments e ON e.schedule_id = cs.id
            WHERE cs.trainer_id = ? AND cs.scheduled_at >= datetime(\'now\')
            GROUP BY cs.id
            ORDER BY cs.scheduled_at ASC
        ');
        $stmt->execute([$trainerId]);
        return $stmt->fetchAll();
    }

    public static function getEnrolledMembers(int $scheduleId, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT u.id, u.name, u.username, u.email, u.profile_photo, e.created_at AS enrolled_at
            FROM enrollments e
            JOIN users u ON e.user_id = u.id
            WHERE e.schedule_id = ?
            ORDER BY e.created_at ASC
        ');
        $stmt->execute([$scheduleId]);
        return $stmt->fetchAll();
    }

    public static function getDetail(int $scheduleId, ?PDO $db = null): ?array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT
                cs.id AS schedule_id,
                cs.class_id,
                cs.trainer_id,
                cs.scheduled_at,
                c.name,
                c.description,
                c.capacity,
                c.difficulty,
                u.name AS trainer,
                u.profile_photo AS trainer_photo,
                tp.bio AS trainer_bio,
                tp.specializations AS trainer_specs,
                tp.years_experience,
                COUNT(e.id) AS enrolled
            FROM class_schedule cs
            JOIN classes c ON cs.class_id = c.id
            JOIN users u ON cs.trainer_id = u.id
            LEFT JOIN trainer_profiles tp ON tp.user_id = u.id
            LEFT JOIN enrollments e ON e.schedule_id = cs.id
            WHERE cs.id = ?
            GROUP BY cs.id
        ');
        $stmt->execute([$scheduleId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
?>
