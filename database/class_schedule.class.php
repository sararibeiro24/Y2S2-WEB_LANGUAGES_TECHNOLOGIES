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
    public static function unenrollUser(int $userId, int $scheduleId, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM enrollments WHERE user_id = ? AND schedule_id = ?');
        return $stmt->execute([$userId, $scheduleId]);
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
            cs.class_id,
            c.name,
            c.capacity,
            c.difficulty,
            COUNT(e.id) AS enrolled
        FROM class_schedule cs
        JOIN classes c ON cs.class_id = c.id
        LEFT JOIN enrollments e ON e.schedule_id = cs.id
        WHERE cs.trainer_id = ?
        GROUP BY cs.id
        ORDER BY cs.scheduled_at ASC
    ');
    $stmt->execute([$trainerId]);
    return $stmt->fetchAll();
}

    public static function getTrainerUpcomingSchedules(int $trainerId, ?PDO $db = null): array {
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
public static function getClassesByTrainer(int $trainerId, ?PDO $db = null): array {
    return self::getTrainerSchedules($trainerId, $db);
}
public static function getClassByName(string $name, ?PDO $db = null): ?array {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('SELECT id, description, difficulty FROM classes WHERE name = ? LIMIT 1');
    $stmt->execute([$name]);
    $row = $stmt->fetch();
    return $row ?: null;
}

public static function clearScheduleEnrollments(int $scheduleId, ?PDO $db = null): bool {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('DELETE FROM enrollments WHERE schedule_id = ?');
    return $stmt->execute([$scheduleId]);
}

public static function trainerHasClassAtTime(int $trainerId, string $scheduledAt, ?int $excludeId = null, ?PDO $db = null): bool {
    $db = $db ?? getDatabaseConnection();
    $sql = 'SELECT COUNT(*) FROM class_schedule WHERE trainer_id = ? AND scheduled_at = ?';
    $params = [$trainerId, $scheduledAt];
    if ($excludeId) {
        $sql .= ' AND id != ?';
        $params[] = $excludeId;
    }
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn() > 0;
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

    // ── Class CRUD (admin) ────────────────────────────

    public static function getAllClasses(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->query('
            SELECT c.*, COUNT(cs.id) AS scheduled_count
            FROM classes c
            LEFT JOIN class_schedule cs ON cs.class_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ');
        return $stmt->fetchAll();
    }

    public static function createClass(string $name, string $description, int $capacity, string $difficulty, ?PDO $db = null): int {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO classes (name, description, capacity, difficulty) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $description, $capacity, $difficulty]);
        return (int)$db->lastInsertId();
    }

    public static function updateClass(int $id, string $name, string $description, int $capacity, string $difficulty, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('UPDATE classes SET name = ?, description = ?, capacity = ?, difficulty = ? WHERE id = ?');
        return $stmt->execute([$name, $description, $capacity, $difficulty, $id]);
    }

    public static function deleteClass(int $id, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM classes WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // ── Schedule assignments ─────────────────────────

    public static function getScheduleForClass(int $classId, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT cs.*, u.name AS trainer_name
            FROM class_schedule cs
            JOIN users u ON cs.trainer_id = u.id
            WHERE cs.class_id = ?
            ORDER BY cs.scheduled_at ASC
        ');
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }

    public static function addSchedule(int $classId, int $trainerId, string $scheduledAt, ?PDO $db = null): int {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO class_schedule (class_id, trainer_id, scheduled_at) VALUES (?, ?, ?)');
        $stmt->execute([$classId, $trainerId, $scheduledAt]);
        return (int)$db->lastInsertId();
    }

    public static function removeSchedule(int $scheduleId, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM class_schedule WHERE id = ?');
        return $stmt->execute([$scheduleId]);
    }

    // ── Equipment CRUD (admin) ─────────────────────────

    public static function getAllEquipment(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->query('
            SELECT e.id, e.name, e.total_quantity, COALESCE(es.available_quantity, e.total_quantity) AS available_quantity, es.last_updated
            FROM equipment e
            LEFT JOIN equipment_status es ON e.id = es.equipment_id
            ORDER BY e.name ASC
        ');
        return $stmt->fetchAll();
    }

    public static function getFilteredEquipment(string $query = '', ?string $status = null, ?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $sql = 'SELECT e.id, e.name, e.total_quantity, COALESCE(es.available_quantity, e.total_quantity) AS available_quantity, es.last_updated
                FROM equipment e
                LEFT JOIN equipment_status es ON e.id = es.equipment_id';
        $params = [];
        $clauses = [];

        if ($query !== '') {
            $clauses[] = 'LOWER(e.name) LIKE ?';
            $params[] = '%' . strtolower($query) . '%';
        }
        if ($status === 'available') {
            $clauses[] = 'COALESCE(es.available_quantity, e.total_quantity) > 0';
        } elseif ($status === 'unavailable') {
            $clauses[] = 'COALESCE(es.available_quantity, e.total_quantity) = 0';
        }

        if ($clauses) {
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= ' ORDER BY e.name ASC';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($data as &$item) {
            $item['available_quantity'] = (int)$item['available_quantity'];
            $item['total_quantity'] = (int)$item['total_quantity'];
        }

        return $data;
    }

    public static function addEquipment(string $name, int $totalQuantity, ?PDO $db = null): int {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO equipment (name, total_quantity) VALUES (?, ?)');
        $stmt->execute([$name, $totalQuantity]);
        $id = (int)$db->lastInsertId();
        $stmt = $db->prepare('INSERT INTO equipment_status (equipment_id, available_quantity) VALUES (?, ?)');
        $stmt->execute([$id, $totalQuantity]);
        return $id;
    }

    public static function updateEquipment(int $id, string $name, int $totalQuantity, int $availableQuantity, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        if ($availableQuantity > $totalQuantity) {
            throw new InvalidArgumentException('Available quantity cannot exceed total quantity.');
        }
        $stmt = $db->prepare('UPDATE equipment SET name = ?, total_quantity = ? WHERE id = ?');
        $stmt->execute([$name, $totalQuantity, $id]);
        // Upsert equipment_status
        $stmt = $db->prepare('SELECT id FROM equipment_status WHERE equipment_id = ?');
        $stmt->execute([$id]);
        if ($stmt->fetch()) {
            $stmt = $db->prepare('UPDATE equipment_status SET available_quantity = ?, last_updated = CURRENT_TIMESTAMP WHERE equipment_id = ?');
            return $stmt->execute([$availableQuantity, $id]);
        } else {
            $stmt = $db->prepare('INSERT INTO equipment_status (equipment_id, available_quantity) VALUES (?, ?)');
            return $stmt->execute([$id, $availableQuantity]);
        }
    }

    public static function deleteEquipment(int $id, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('DELETE FROM equipment_status WHERE equipment_id = ?');
        $stmt->execute([$id]);
        $stmt = $db->prepare('DELETE FROM equipment WHERE id = ?');
        return $stmt->execute([$id]);
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
    public static function updateSchedule(int $scheduleId, string $scheduledAt, ?PDO $db = null): bool {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('UPDATE class_schedule SET scheduled_at = ? WHERE id = ?');
        return $stmt->execute([$scheduledAt, $scheduleId]);
    }
    public static function getScheduleWithEnrollCount(int $scheduleId, ?PDO $db = null): ?array {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('
        SELECT
            cs.id,
            cs.trainer_id,
            cs.scheduled_at,
            c.capacity,
            c.name AS class_name,
            COUNT(e.id) AS enrolled
        FROM class_schedule cs
        JOIN classes c ON c.id = cs.class_id
        LEFT JOIN enrollments e ON e.schedule_id = cs.id
        WHERE cs.id = ?
        GROUP BY cs.id
    ');
    $stmt->execute([$scheduleId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

public static function isUserEnrolled(int $userId, int $scheduleId, ?PDO $db = null): bool {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('SELECT id FROM enrollments WHERE user_id = ? AND schedule_id = ?');
    $stmt->execute([$userId, $scheduleId]);
    return (bool)$stmt->fetch();
}
public static function getFilteredWeekClasses(string $weekStart, array $filters = [], ?PDO $db = null): array {
    $db = $db ?? getDatabaseConnection();

    $weekEnd = date('Y-m-d 23:59:59', strtotime($weekStart . ' +6 days'));

    $sql = '
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
    ';

    $params = [$weekStart, $weekEnd];

    if (!empty($filters['trainer_id'])) {
        $sql .= ' AND cs.trainer_id = ?';
        $params[] = (int)$filters['trainer_id'];
    }

    if (!empty($filters['query'])) {
        $sql .= ' AND LOWER(c.name) LIKE ?';
        $params[] = '%' . strtolower($filters['query']) . '%';
    }

    if (!empty($filters['difficulty'])) {
        $sql .= ' AND LOWER(c.difficulty) = ?';
        $params[] = strtolower($filters['difficulty']);
    }

    $sql .= ' GROUP BY cs.id ORDER BY cs.scheduled_at ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public static function getDetailWithReviews(int $scheduleId, ?PDO $db = null): ?array {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('
        SELECT
            cs.id AS schedule_id,
            cs.scheduled_at,
            cs.class_id,
            c.name,
            c.description,
            c.capacity,
            c.difficulty,
            u.name AS trainer,
            u.id AS trainer_id,
            u.profile_photo AS trainer_photo,
            tp.bio AS trainer_bio,
            tp.specializations AS trainer_specs,
            tp.years_experience,
            tp.certifications,
            COUNT(DISTINCT e.id) AS enrolled,
            ROUND(AVG(r.rating), 1) AS avg_rating,
            COUNT(DISTINCT r.id) AS review_count
        FROM class_schedule cs
        JOIN classes c ON cs.class_id = c.id
        JOIN users u ON cs.trainer_id = u.id
        LEFT JOIN trainer_profiles tp ON tp.user_id = u.id
        LEFT JOIN enrollments e ON e.schedule_id = cs.id
        LEFT JOIN reviews r ON r.schedule_id = cs.id
        WHERE cs.id = ?
        GROUP BY cs.id
    ');
    $stmt->execute([$scheduleId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

public static function getReviewsForSchedule(int $scheduleId, ?PDO $db = null): array {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('
        SELECT r.id, r.rating, r.comment, r.created_at, u.name AS user_name, u.profile_photo AS user_photo
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.schedule_id = ?
        ORDER BY r.created_at DESC
    ');
    $stmt->execute([$scheduleId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function getUserReview(int $userId, int $scheduleId, ?PDO $db = null): ?array {
    $db = $db ?? getDatabaseConnection();
    $stmt = $db->prepare('SELECT id, rating, comment FROM reviews WHERE user_id = ? AND schedule_id = ?');
    $stmt->execute([$userId, $scheduleId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}
public static function getUpcomingFiltered(array $filters = [], ?PDO $db = null): array {
    $db = $db ?? getDatabaseConnection();

    $sql = '
        SELECT
            cs.id AS schedule_id,
            cs.class_id,
            cs.trainer_id,
            cs.scheduled_at,
            c.name,
            c.description,
            c.capacity,
            u.name AS trainer,
            COUNT(e.id) AS enrolled
        FROM class_schedule cs
        JOIN classes c ON cs.class_id = c.id
        JOIN users u ON cs.trainer_id = u.id
        LEFT JOIN enrollments e ON e.schedule_id = cs.id
        WHERE cs.scheduled_at >= datetime(\'now\')
    ';

    $params = [];

    if (!empty($filters['search'])) {
        $sql .= ' AND c.name LIKE ?';
        $params[] = '%' . $filters['search'] . '%';
    }

    if (!empty($filters['trainer_id']) && $filters['trainer_id'] !== 'all') {
        $sql .= ' AND cs.trainer_id = ?';
        $params[] = (int)$filters['trainer_id'];
    }

    if (!empty($filters['date'])) {
        $sql .= ' AND date(cs.scheduled_at) = ?';
        $params[] = $filters['date'];
    }

    $sql .= ' GROUP BY cs.id ORDER BY cs.scheduled_at ASC';

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
  
?>
