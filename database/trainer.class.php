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

    public function getSpecializationsList(): array {
        if ($this->specializations === null || $this->specializations === '') {
            return [];
        }
        return array_map('trim', explode(',', $this->specializations));
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
                tp.certifications
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
                $db
            );
        }
        return $trainers;
    }
}
?>
