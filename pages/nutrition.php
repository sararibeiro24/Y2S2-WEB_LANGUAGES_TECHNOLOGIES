<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/nutrition.tpl.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$trainers = [];
    try {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('SELECT id, name FROM users WHERE role = "trainer" ORDER BY name ASC');
        $stmt->execute();
        $trainers = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Database error fetching trainers: ' . $e->getMessage());
        $trainers = [];
    }
drawHead("Nutrition Plans | Ladybug's Gym");
drawHeader();
drawNutritionPage($trainers);
drawFooter();
?>