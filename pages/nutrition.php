<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/nutrition.tpl.php');
require_once(__DIR__ . '/../database/trainer.class.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$trainers = [];
try {
    $trainers = Trainer::getIdNameList();
} catch (PDOException $e) {
    error_log('Database error fetching trainers: ' . $e->getMessage());
    $trainers = [];
}
drawHead("Nutrition Plans | Ladybug's Gym");
drawHeader();
drawNutritionPage($trainers);
drawFooter();
?>