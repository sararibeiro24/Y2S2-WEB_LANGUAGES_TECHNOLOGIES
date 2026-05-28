<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/plans.tpl.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$userId     = Session::getUserId();
$csrfToken  = Session::getCsrfToken();

$db = getDatabaseConnection();

$currentPlanId = null;
if ($isLoggedIn && $userId !== null) {
    $stmt = $db->prepare('SELECT plan_id FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    $currentPlanId = $row['plan_id'] !== null ? (int)$row['plan_id'] : null;
}

$queryTerm   = trim($_GET['query']         ?? '');
$cycleFilter = trim($_GET['billing_cycle'] ?? '');

$sql = 'SELECT id, name, price, billing_cycle, features FROM plans';
$params = [];
$clauses = [];

if ($queryTerm !== '') {
    $clauses[] = '(name LIKE ? OR features LIKE ?)';
    $params[]  = "%{$queryTerm}%";
    $params[]  = "%{$queryTerm}%";
}
if ($cycleFilter !== '') {
    $clauses[] = 'billing_cycle = ?';
    $params[]  = $cycleFilter;
}
if ($clauses) {
    $sql .= ' WHERE ' . implode(' AND ', $clauses);
}

$stmt  = $db->prepare($sql);
$stmt->execute($params);
$plans = $stmt->fetchAll();


drawHead("Membership Plans | Ladybug's Gym");
drawHeader();
drawPlansPage($plans, $isLoggedIn, $currentPlanId, $csrfToken, $queryTerm, $cycleFilter);
drawFooter();
?>