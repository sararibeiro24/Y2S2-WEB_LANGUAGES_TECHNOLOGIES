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

$action = $_POST['action'] ?? '';
$db = getDatabaseConnection();

switch ($action) {
    case 'toggle_active':
        $userId = (int)($_POST['user_id'] ?? 0);
        $active = (int)($_POST['active'] ?? 0);
        if ($userId > 0) {
            $stmt = $db->prepare('UPDATE users SET active = ? WHERE id = ?');
            $stmt->execute([$active, $userId]);
            Session::addMessage('success', 'User status updated.');
        }
        break;

    case 'set_role':
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        if ($userId > 0 && in_array($role, ['member', 'trainer', 'admin'])) {
            $stmt = $db->prepare('UPDATE users SET role = ? WHERE id = ?');
            $stmt->execute([$role, $userId]);
            Session::addMessage('success', 'User role updated.');
            if ($userId === (int)Session::getUserId()) {
                Session::setUserRole($role); 
            }
        }
   
        break;

    case 'update_user':
        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($userId > 0 && $name !== '' && $email !== '') {
            $stmt = $db->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $email, $userId]);
            Session::addMessage('success', 'User updated.');
        }
        break;

    case 'create_user':
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';
        if ($username && $email && $name && $password) {
            try {
                User::register($username, $email, $password, $name, $db);
                // Update role from default 'member'
                $stmt = $db->prepare('UPDATE users SET role = ? WHERE username = ?');
                $stmt->execute([$role, $username]);
                Session::addMessage('success', "User '$username' created.");
            } catch (Exception $e) {
                Session::addMessage('error', $e->getMessage());
            }
        } else {
            Session::addMessage('error', 'All fields required.');
        }
        break;
}

header('Location: ../pages/dashboard.php?tab=users');
exit;
