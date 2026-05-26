<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class User {
    private int $id;
    private string $username;
    private string $email;
    private string $name;
    private string $role;
    private bool $active;
    private ?PDO $db;

    public function __construct(int $id, string $username, string $email, string $name, string $role = 'member', bool $active = true, ?PDO $db = null) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->role = $role;
        $this->active = $active;
        $this->db = $db ?? getDatabaseConnection();
    }

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getName(): string { return $this->name; }
    public function getRole(): string { return $this->role; }
    public function isActive(): bool { return $this->active; }

    public static function getById(int $id, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT id, username, email, name, role, active FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }

        return new User(
            $row['id'],
            $row['username'],
            $row['email'],
            $row['name'],
            $row['role'],
            (bool)$row['active'],
            $db
        );
    }

    public static function getByUsernameOrEmail(string $usernameOrEmail, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT id, username, email, name, role, active 
            FROM users 
            WHERE (username = ? OR email = ?) AND active = 1
        ');
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }

        return new User(
            $row['id'],
            $row['username'],
            $row['email'],
            $row['name'],
            $row['role'],
            (bool)$row['active'],
            $db
        );
    }

    public static function authenticate(string $usernameOrEmail, string $password, ?PDO $db = null): ?User {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT id, username, email, name, role, password_hash, active 
            FROM users 
            WHERE (username = ? OR email = ?) AND active = 1
        ');
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $row = $stmt->fetch();
        
        if (!$row) {
            return null;
        }
        if (password_verify($password, $row['password_hash'])) {
        return new User(
            $row['id'],
            $row['username'],
            $row['email'],
            $row['name'],
            $row['role'],
            (bool)$row['active'],
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
            INSERT INTO users (username, email, name, password_hash, role, active) 
            VALUES (?, ?, ?, ?, \'member\', 1)
        ');

        $stmt->execute([$username, $email, $name, $hash]);

        $id = (int)$db->lastInsertId();
        
        return new User($id, $username, $email, $name, 'member', true, $db);
    }


}
?>
