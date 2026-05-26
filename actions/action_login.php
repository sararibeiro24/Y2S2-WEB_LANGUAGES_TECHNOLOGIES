<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

$usernameOrEmail = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$loginType = $_POST['login_type'] ?? 'user';

$redirect = $_SERVER['HTTP_REFERER'] ?? '../pages/index.php';

if ($loginType === 'user') {
    if (empty($usernameOrEmail) || empty($password)) {
        Session::addMessage('error', 'Please enter both username and password.');
        exit;
    }
    $user = User::authenticate($usernameOrEmail, $password);
        
        if ($user) {
            Session::setUser($user->getId(), $user->getRole());
            Session::addMessage('success', 'Welcome back, ' . htmlspecialchars($user->getName()) . '!');
            header('Location: ../pages/index.php');
            exit; 
        }

     else {
            Session::addMessage('error', 'Invalid username/email or password.');
        }
}
header('Location: ../pages/login.php');
exit;
?>
