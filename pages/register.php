<?php

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/register.tpl.php');
drawHead("Register | Ladybug's Gym");
drawHeader();
drawPageHeader("Create Your Account","Join Ladybug's Gym to book classes and track your fitness journey.");
drawMessages();
drawRegisterForm(); 
?>
