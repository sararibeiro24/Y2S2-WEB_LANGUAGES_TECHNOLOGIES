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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['profilePhoto']['name'])) {
    $file = $_FILES['profilePhoto'];

    if (in_array($file['type'], ['image/jpeg', 'image/png']) && $file['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'user_' . $user->getId() . '.' . $ext;
        $uploadDir = dirname(__DIR__) . '/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $destination = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $user->updateProfilePhoto('/uploads/' . $filename);
        }
    }

    header('Location: profile.php');
    exit;
}
$Name = $user->getName();
$Username = $user->getUsername();
$Email = $user->getEmail();
$ProfilePhoto = $user->getProfilePhoto() ?? "/img/default.png";

drawHead("My Profile | Ladybug's Gym");
drawHeader();
drawProfileForm($Name, $Username, $Email, $ProfilePhoto);
drawFooter();

?>
