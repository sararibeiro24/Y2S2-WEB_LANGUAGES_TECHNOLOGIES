<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class User {
    private int $id;
    private string $username;
    private string $email;
    private string $name;
    private string $role;
    private ?string $profilePhoto;
    private bool $active;
    private ?string $planName;
    private string $createdAt;
    private ?PDO $db;

    public function __construct(int $id, string $username, string $email, string $name, string $role = 'member', bool $active = true, ?string $profilePhoto = null, ?string $planName = null, string $createdAt = '', ?PDO $db = null) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->role = $role;
        $this->active = $active;
        $this->profilePhoto = $profilePhoto;
        $this->planName = $planName;
        $this->createdAt = $createdAt;
        $this->db = $db ?? getDatabaseConnection();
    }

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getName(): string { return $this->name; }
    public function getRole(): string { return $this->role; }
    public function getProfilePhoto(): ?string { return $this->profilePhoto; }
    public function isActive(): bool { return $this->active; }
    public function getPlanName(): ?string { return $this->planName; }
    public function getCreatedAt(): string { return $this->createdAt; }

    public static function getById(int $id, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT u.id, u.username, u.email, u.name, u.role, u.active, u.profile_photo, u.created_at, p.name AS plan_name 
            FROM users u
            LEFT JOIN plans p ON u.plan_id = p.id
            WHERE u.id = ?
        ');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }

        return new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['name'],
            $row['role'],
            (bool)$row['active'],
            $row['profile_photo'],
            $row['plan_name'],
            $row['created_at'],
            $db
        );
    }

    public static function getByUsernameOrEmail(string $usernameOrEmail, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT u.id, u.username, u.email, u.name, u.role, u.active, u.profile_photo, u.created_at, p.name AS plan_name 
            FROM users u
            LEFT JOIN plans p ON u.plan_id = p.id
            WHERE (u.username = ? OR u.email = ?) AND u.active = 1
        ');
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }

        return new User(
            (int)$row['id'],
            $row['username'],
            $row['email'],
            $row['name'],
            $row['role'],
            (bool)$row['active'],
            $row['profile_photo'],
            $row['plan_name'],
            $row['created_at'],
            $db
        );
    }

    public static function authenticate(string $usernameOrEmail, string $password, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT u.id, u.username, u.email, u.name, u.role, u.password_hash, u.active, u.profile_photo, u.created_at, p.name AS plan_name 
            FROM users u
            LEFT JOIN plans p ON u.plan_id = p.id
            WHERE (u.username = ? OR u.email = ?) AND u.active = 1
        ');
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) {
            return null;
        }

        if (password_verify($password, $row['password_hash'])) {
            return new User(
                (int)$row['id'],
                $row['username'],
                $row['email'],
                $row['name'],
                $row['role'],
                (bool)$row['active'],
                $row['profile_photo'],
                $row['plan_name'],
                $row['created_at'],
                $db
            );
        }   
        return null;
    }

    public static function register(string $username, string $email, string $password, string $name, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);

        if ($stmt->fetch()) {
            throw new Exception('Username or email already exists.');
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare('
            INSERT INTO users (username, email, name, password_hash, role, active, profile_photo) 
            VALUES (?, ?, ?, ?, \'member\', 1, NULL)
        ');

        $stmt->execute([$username, $email, $name, $hash]);

        $id = (int)$db->lastInsertId();
        
        return self::getById($id, $db);
    }

    public function updateProfilePhoto(string $photoPath): bool {
        $stmt = $this->db->prepare('UPDATE users SET profile_photo = ? WHERE id = ?');
        if ($stmt->execute([$photoPath, $this->id])) {
            $this->profilePhoto = $photoPath;
            return true;
        }
        return false;
    }

    public function updatePassword(string $newPassword) {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$this->id]);
        $row = $stmt->fetch();
        if ($row) {
            if (password_verify($newPassword, $row['password_hash'])) {
                return false; 
            }
        }
        $options = ['cost' => 12];
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, $options);
    
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        return $stmt->execute([$hashedPassword, $this->id]);
    }

    public function updateEmail(string $newEmail): bool {
        if (strtolower(trim($newEmail)) === strtolower(trim($this->email))) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE users SET email = ? WHERE id = ?');
        
        if ($stmt->execute([$newEmail, $this->id])) {
            $this->email = $newEmail; 
            return true;
        }
        return false;
    }

    public static function getAllUsers(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->query('
            SELECT u.id, u.username, u.email, u.name, u.role, u.active, u.profile_photo, u.created_at, p.name AS plan_name
            FROM users u
            LEFT JOIN plans p ON u.plan_id = p.id
            ORDER BY u.role ASC, u.name ASC
        ');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setActive(bool $active): bool {
        $stmt = $this->db->prepare('UPDATE users SET active = ? WHERE id = ?');
        return $stmt->execute([$active ? 1 : 0, $this->id]);
    }

    public function setRole(string $role): bool {
        $allowed = ['member', 'trainer', 'admin'];
        if (!in_array($role, $allowed)) return false;
        $stmt = $this->db->prepare('UPDATE users SET role = ? WHERE id = ?');
        return $stmt->execute([$role, $this->id]);
    }
    public function getClassesAttendedCount(): int {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) 
            FROM enrollments e
            JOIN class_schedule s ON e.schedule_id = s.id
            WHERE e.user_id = ? AND s.scheduled_at < CURRENT_TIMESTAMP
        ');
        $stmt->execute([$this->id]);
        return (int)$stmt->fetchColumn();
    }

    public function getUpcomingClassesCount(): int {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) 
            FROM enrollments e
            JOIN class_schedule s ON e.schedule_id = s.id
            WHERE e.user_id = ? AND s.scheduled_at >= CURRENT_TIMESTAMP
        ');
        $stmt->execute([$this->id]);
        return (int)$stmt->fetchColumn();
    }

    public static function getPlanIdById(int $userId, ?PDO $db = null): ?int {
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
?>