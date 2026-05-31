<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
Session::start();

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
    header('Location: ../pages/register.php');
    exit;
}

$name = trim($_POST['name'] ?? ''); 
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
    Session::addMessage('error', 'All fields are required.');
    header('Location:  ../pages/register.php');
    exit;
}


if ($password !== $confirmPassword) {
    Session::addMessage('error', 'Passwords do not match.');
    header('Location:  ../pages/register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Session::addMessage('error', 'Invalid email format.');
    header('Location:  ../pages/register.php');
    exit;
}


try {
    $user = User::register($username, $email, $password, $name);

    if ($user) {
        Session::setUser($user->getId(), $user->getRole());
        Session::addMessage('success', 'Welcome to Ladybug\'s Gym, ' . htmlspecialchars($user->getName()) . '!');
        header('Location: ../pages/index.php');
        exit;
    } else {
        Session::addMessage('error', 'Failed to create account. Please try again.');
    }
} catch (Exception $e) {

    Session::addMessage('error', $e->getMessage());
}

header('Location: ../pages/register.php');
exit;
?>