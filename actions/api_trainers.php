<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');

header('Content-Type: application/json');

$db = getDatabaseConnection();

$search = $_GET['search'] ?? '';

$sql = '
    SELECT
        u.id,
        u.name,
        u.profile_photo,
        tp.bio,
        tp.specializations,
        tp.certifications
    FROM users u
    LEFT JOIN trainer_profiles tp ON u.id = tp.user_id
    WHERE u.role = \'trainer\' AND u.active = 1
';

$params = [];

if ($search !== '') {
    $sql .= ' AND (u.name LIKE ? OR tp.specializations LIKE ? OR tp.bio LIKE ?)';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
    $params[] = '%' . $search . '%';
}

$sql .= ' ORDER BY u.name ASC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Parse specializations into array for each trainer
foreach ($rows as &$row) {
    $row['specializations_list'] = $row['specializations']
        ? array_map('trim', explode(',', $row['specializations']))
        : [];
}

echo json_encode($rows);
