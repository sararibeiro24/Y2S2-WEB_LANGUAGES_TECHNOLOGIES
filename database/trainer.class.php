<?php
declare(strict_types=1);

require_once(__DIR__ . '/database.db.php');

class Trainer {
    private int $id;
    private string $username;
    private string $email;
    private string $name;
    private string $profilePhoto;
    private ?string $bio;
    private ?string $specializations;
    private ?string $certifications;
    private int $yearsExperience;
    private ?PDO $db;

    public function __construct(
        int $id,
        string $username,
        string $email,
        string $name,
        string $profilePhoto = '',
        ?string $bio = null,
        ?string $specializations = null,
        ?string $certifications = null,
        int $yearsExperience = 0,
        ?PDO $db = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->name = $name;
        $this->profilePhoto = $profilePhoto;
        $this->bio = $bio;
        $this->specializations = $specializations;
        $this->certifications = $certifications;
        $this->yearsExperience = $yearsExperience;
        $this->db = $db ?? getDatabaseConnection();
    }

    public function getId(): int { return $this->id; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getName(): string { return $this->name; }
    public function getProfilePhoto(): string { return $this->profilePhoto; }
    public function getBio(): ?string { return $this->bio; }
    public function getSpecializations(): ?string { return $this->specializations; }
    public function getCertifications(): ?string { return $this->certifications; }
    public function getYearsExperience(): int { return $this->yearsExperience; }

    public function getSpecializationsList(): array {
        if ($this->specializations === null || $this->specializations === '') {
            return [];
        }
        return array_map('trim', explode(',', $this->specializations));
    }

    public function getClasses(): array {
        $stmt = $this->db->prepare('
            SELECT DISTINCT c.name, COUNT(cs.id) AS session_count
            FROM classes c
            JOIN class_schedule cs ON cs.class_id = c.id
            WHERE cs.trainer_id = ?
            GROUP BY c.id
            ORDER BY session_count DESC
        ');
        $stmt->execute([$this->id]);
        return $stmt->fetchAll();
    }

    public function updateProfile(string $bio, string $specializations, string $certifications): bool {
        $stmt = $this->db->prepare('
            INSERT INTO trainer_profiles (user_id, bio, specializations, certifications)
            VALUES (?, ?, ?, ?)
            ON CONFLICT(user_id) DO UPDATE SET
                bio = excluded.bio,
                specializations = excluded.specializations,
                certifications = excluded.certifications
        ');
        return $stmt->execute([$this->id, $bio, $specializations, $certifications]);
    }

    public static function getAllTrainers(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('
            SELECT
                u.id,
                u.username,
                u.email,
                u.name,
                COALESCE(u.profile_photo, \'\') AS profile_photo,
                tp.bio,
                tp.specializations,
                tp.certifications,
                COALESCE(tp.years_experience, 0) AS years_experience
            FROM users u
            LEFT JOIN trainer_profiles tp ON u.id = tp.user_id
            WHERE u.role = \'trainer\' AND u.active = 1
            ORDER BY u.name ASC
        ');
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $trainers = [];
        foreach ($rows as $row) {
            $trainers[] = new self(
                $row['id'],
                $row['username'],
                $row['email'],
                $row['name'],
                $row['profile_photo'],
                $row['bio'],
                $row['specializations'],
                $row['certifications'],
                (int)$row['years_experience'],
                $db
            );
        }
        return $trainers;
    }
    public static function getProfileByUserId(int $userId, ?PDO $db = null): ?array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT bio, specializations, certifications FROM trainer_profiles WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function getIdNameList(?PDO $db = null): array {
        $db = $db ?? getDatabaseConnection();
        $stmt = $db->prepare('SELECT id, name FROM users WHERE role = \'trainer\' AND active = 1 ORDER BY name ASC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
