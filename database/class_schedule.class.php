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
}
?>
