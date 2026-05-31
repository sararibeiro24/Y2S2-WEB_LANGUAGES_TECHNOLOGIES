<?php
declare(strict_types=1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isAdmin()) {
    http_response_code(403);
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (!Session::validateCsrfToken($token)) {
    Session::addMessage('error', 'Invalid security token.');
    header('Location: ../pages/dashboard.php?tab=users');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'toggle_active':
        $userId = (int)($_POST['user_id'] ?? 0);
        $active = (int)($_POST['active'] ?? 0);
        $user = $userId > 0 ? User::getById($userId) : null;
        if (!$user) {
            Session::addMessage('error', 'User not found.');
            break;
        }
        $user->setActive((bool)$active)
            ? Session::addMessage('success', 'User status updated.')
            : Session::addMessage('error', 'Failed to update user status.');
        break;

    case 'set_role':
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        $user = $userId > 0 ? User::getById($userId) : null;
        if (!$user) {
            Session::addMessage('error', 'User not found.');
            break;
        }
        $user->setRole($role)
            ? Session::addMessage('success', 'User role updated.')
            : Session::addMessage('error', 'Invalid role specified.');
        break;

    case 'update_user':
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $user = $userId > 0 ? User::getById($userId) : null;
        if (!$user) {
            Session::addMessage('error', 'User not found.');
            break;
        }
        if ($name === '' || $email === '') {
            Session::addMessage('error', 'Name and email are required.');
            break;
        }
        $user->updateEmail($email);
        $user->updateName($name);
        Session::addMessage('success', 'User updated.');
        break;

    case 'create_user':
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';
        if (!$username || !$email || !$name || !$password) {
            Session::addMessage('error', 'All fields required.');
            break;
        }
        try {
            User::registerWithRole($username, $email, $password, $name, $role)
                ? Session::addMessage('success', "User '$username' created.")
                : Session::addMessage('error', 'Failed to create user.');
        } catch (Exception $e) {
            Session::addMessage('error', $e->getMessage());
        }
        break;

    default:
        Session::addMessage('error', 'Invalid action.');
}

header('Location: ../pages/dashboard.php?tab=users');
exit;