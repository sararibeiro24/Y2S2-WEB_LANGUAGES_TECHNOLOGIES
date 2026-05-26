<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../templates/login.tpl.php');
Session::start();

// If already logged in, redirect to profile
if (Session::isLoggedIn()) {
    header('Location: profile.php');
    exit;
}

drawHead("Login | Ladybug's Gym");
drawHeader();
drawPageHeader("Welcome Back","Please log in to access your account.");
drawLoginForm();
drawFooter();
?>