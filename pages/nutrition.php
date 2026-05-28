<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/nutrition.tpl.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();

drawHead("Nutrition Plans | Ladybug's Gym");
drawHeader();
drawNutritionPage();
drawFooter();
?>