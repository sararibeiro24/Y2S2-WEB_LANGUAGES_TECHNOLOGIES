<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();
session_regenerate_id(true);

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
    header('Location: ../pages/login.php');
    exit;
}

$usernameOrEmail = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$loginType = $_POST['login_type'] ?? 'user';

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
