<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../templates/equipment.tpl.php');
Session::start();

drawHead("Equipment Availability | Ladybug's Gym");
drawHeader();
drawEquipmentPage();
drawFooter();
?>
