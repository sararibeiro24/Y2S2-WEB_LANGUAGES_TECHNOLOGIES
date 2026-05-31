<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/dashboard.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');

Session::start();

if (!Session::isLoggedIn() || (!Session::isAdmin() && !Session::isTrainer())) {
    header('Location: ../pages/login.php');
    exit;
}

$db     = getDatabaseConnection();
$role   = Session::getUserRole();
$userId = Session::getUserId();

$tab = $_GET['tab'] ?? (($role === 'admin') ? 'users' : 'trainer_profile');

if ($role === 'trainer' && in_array($tab, ['users', 'equipment'], true)) {
    $tab = 'trainer_profile';
}

$users         = [];
$equipment     = [];
$nutritionPlans = [];
$classes       = [];
$trainers      = [];
$bio           = '';
$specializations = '';
$certifications  = '';

if ($tab === 'users' && $role === 'admin') {
    $users = User::getAllUsers($db);

}
elseif ($tab === 'classes') {

    if ($role === 'admin') {
        $classes  = ClassSchedule::getAllClasses($db);
        $trainers = array_map(function (Trainer $t): array {
            return [
                'id'   => $t->getId(),
                'name' => $t->getName(),
            ];
        }, Trainer::getAllTrainers($db));
    } else {
        $allClasses      = ClassSchedule::getAllClasses($db);
        $trainerClassIds = array_unique(array_column(
            ClassSchedule::getTrainerSchedules($userId, $db),
            'class_id'
        ));
        $classes = array_values(array_filter($allClasses, function ($c) use ($trainerClassIds) {
            return in_array((int)$c['id'], $trainerClassIds, true);
        }));
    }

} 
elseif ($tab === 'equipment' && $role === 'admin') {

    $equipment = ClassSchedule::getAllEquipment($db);

} 
elseif ($tab === 'trainer_profile' && $role === 'trainer') {

    $profile = Trainer::getProfileByUserId($userId, $db);
    if ($profile) {
        $bio             = $profile['bio']             ?? '';
        $specializations = $profile['specializations'] ?? '';
        $certifications  = $profile['certifications']  ?? '';
    }
    $classes = ClassSchedule::getTrainerUpcomingSchedules($userId, $db);

}
elseif($tab === 'nutrition' && $role ==='trainer'){
    try {
        $nutritionStmt = $db->prepare('
            SELECT np.id, np.goal, np.target_calories, np.meal_details, np.created_at, 
                   u.name AS member_name, u.email AS member_email,
                   CASE WHEN np.meal_details LIKE \'Pending approval%\' THEN 0 ELSE 1 END AS is_approved
            FROM nutrition_plans np 
            JOIN users u ON np.user_id = u.id 
            WHERE np.trainer_id = ? 
            ORDER BY is_approved ASC, np.created_at DESC
        ');
        $nutritionStmt->execute([$userId]);
        $nutritionPlans = $nutritionStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log('Database error fetching nutrition requests: ' . $e->getMessage());
        $nutritionPlans = [];
    }
}
drawHead("Dashboard | Ladybug's Gym");
drawHeader();
drawMessages();

drawDashboardMain($role, $tab, $classes, $trainers, $users, $equipment, $nutritionPlans, $db, $userId, $bio, $specializations, $certifications);

drawFooter();
?>