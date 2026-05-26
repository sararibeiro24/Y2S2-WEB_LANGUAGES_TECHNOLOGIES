<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/profile.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');
Session::start();
if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$userId = Session::getUserId(); 

$user = User::getById($userId);

if (!$user) {
    header('Location: logout.php');
    exit;
}
$Name = $user->getName();
$Username = $user->getUsername();
$Email = $user->getEmail();
$ProfilePhoto = $user->getProfilePhoto() ?? "../img/default.png";
drawHead("My Profile | Ladybug's Gym");
drawHeader();
drawProfileForm($Name, $Username, $Email, $ProfilePhoto);
drawFooter();

?>
