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
    } else {
        $user = User::authenticate($usernameOrEmail, $password);
        
        if ($user) {
            Session::setUser($user->getId());
            Session::addMessage('success', 'Welcome back, ' . htmlspecialchars($user->getName()) . '!');
            header('Location: ../pages/profile.php');
            exit;
        } else {
            Session::addMessage('error', 'Invalid username/email or password.');
        }
    }
} elseif ($loginType === 'admin') {
    $adminId = $_POST['adminId'] ?? '';
    $adminPassword = $_POST['password'] ?? '';
    $adminToken = $_POST['token'] ?? '';
    
    if (empty($adminId) || empty($adminPassword) || empty($adminToken)) {
        Session::addMessage('error', 'Please fill in all admin fields.');
    } else {
        $user = User::getById((int)$adminId);
        
        if ($user && $user->getRole() === 'admin') {
            $adminUser = User::authenticate($user->getUsername(), $adminPassword);
            
            if ($adminUser) {
                Session::setUser($user->getId());
                Session::addMessage('success', 'Admin access granted. Welcome ' . htmlspecialchars($user->getName()) . '!');
                header('Location: ../pages/profile.php');
                exit;
            } else {
                Session::addMessage('error', 'Invalid admin credentials.');
            }
        } else {
            Session::addMessage('error', 'Invalid admin ID or credentials.');
        }
    }
}

header('Location: ../pages/login.php');
exit;
?>
