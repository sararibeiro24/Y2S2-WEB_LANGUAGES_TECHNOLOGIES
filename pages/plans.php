<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/plans.tpl.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/plan.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$userId     = Session::getUserId();
$csrfToken  = Session::getCsrfToken();

$db = getDatabaseConnection();

$currentPlanId = null;
if ($isLoggedIn && $userId !== null) {
    $currentPlanId = User::getPlanIdById($userId, $db);
}

$plans = Plan::getAllPlans();

drawHead("Membership Plans | Ladybug's Gym");
drawHeader();
drawPlansPage($plans, $isLoggedIn, $currentPlanId, $csrfToken);
drawFooter();
?>