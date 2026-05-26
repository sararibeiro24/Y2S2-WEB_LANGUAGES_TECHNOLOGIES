<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');

Session::start();
if (!Session::isLoggedIn() || !Session::isAdmin()) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

$tab = $_GET['tab'] ?? 'users';
$users = User::getAllUsers($db);
$classes = ClassSchedule::getAllClasses($db);
$equipment = ClassSchedule::getAllEquipment($db);
$trainers = Trainer::getAllTrainers($db);

drawHead("Admin Dashboard | Ladybug's Gym");
drawHeader();
drawMessages();
?>
<main class="container" style="padding-top: 1em;">
    <div class="page-header">
        <h2>Admin Dashboard</h2>
        <p>Manage users, classes, and equipment.</p>
    </div>

    <div class="admin-tabs">
        <a href="?tab=users" class="admin-tab <?= $tab === 'users' ? 'active' : '' ?>">Users</a>
        <a href="?tab=classes" class="admin-tab <?= $tab === 'classes' ? 'active' : '' ?>">Classes</a>
        <a href="?tab=equipment" class="admin-tab <?= $tab === 'equipment' ? 'active' : '' ?>">Equipment</a>
    </div>

    <?php if ($tab === 'users'): ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Users</h3>
            <button class="button button-small" onclick="this.nextElementSibling.style.display='block'">+ Add User</button>
            <div class="inline-form" style="display:none">
                <form action="../actions/action_admin_users.php" method="post" class="admin-form">
                    <input type="hidden" name="action" value="create_user">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="role">
                        <option value="member">Member</option>
                        <option value="trainer">Trainer</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button class="button button-small">Create</button>
                </form>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update_user">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($u['name']) ?>" class="inline-input" required>
                        </td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" class="inline-input" required>
                        </td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" style="display:inline">
                                <input type="hidden" name="action" value="set_role">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="member" <?= $u['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                                    <option value="trainer" <?= $u['role'] === 'trainer' ? 'selected' : '' ?>>Trainer</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" style="display:inline">
                                <input type="hidden" name="action" value="toggle_active">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="active" value="<?= $u['active'] ? 0 : 1 ?>">
                                <button class="badge badge-<?= $u['active'] ? 'active' : 'inactive' ?>"><?= $u['active'] ? 'Active' : 'Inactive' ?></button>
                            </form>
                        </td>
                        <td>
                                <button class="button button-small">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php elseif ($tab === 'classes'): ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Classes</h3>
            <button class="button button-small" onclick="this.nextElementSibling.style.display='block'">+ Add Class</button>
            <div class="inline-form" style="display:none">
                <form action="../actions/action_admin_classes.php" method="post" class="admin-form">
                    <input type="hidden" name="action" value="create">
                    <input type="text" name="name" placeholder="Class Name" required>
                    <input type="text" name="description" placeholder="Description">
                    <input type="number" name="capacity" placeholder="Capacity" min="1" required>
                    <select name="difficulty">
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Advanced">Advanced</option>
                    </select>
                    <button class="button button-small">Create</button>
                </form>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Difficulty</th><th>Capacity</th><th>Scheduled</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td>
                            <form action="../actions/action_admin_classes.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" class="inline-input" required>
                        </td>
                        <td>
                                <select name="difficulty">
                                    <option value="Beginner" <?= $c['difficulty'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                                    <option value="Intermediate" <?= $c['difficulty'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                    <option value="Advanced" <?= $c['difficulty'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                                </select>
                        </td>
                        <td>
                                <input type="number" name="capacity" value="<?= $c['capacity'] ?>" class="inline-input" style="width:60px" min="1">
                        </td>
                        <td><?= $c['scheduled_count'] ?></td>
                        <td class="action-cell">
                                <input type="text" name="description" value="<?= htmlspecialchars($c['description'] ?? '') ?>" class="inline-input" placeholder="Description" style="width:150px">
                                <button class="button button-small">Save</button>
                            </form>
                            <form action="../actions/action_admin_classes.php" method="post" style="display:inline" onsubmit="return confirm('Delete this class?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                <button class="button button-small button-outline" style="border-color:#888;color:#888">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <?php
                    // Show schedule slots for this class
                    $schedules = ClassSchedule::getScheduleForClass($c['id'], $db);
                    if (!empty($schedules)):
                    ?>
                    <tr class="schedule-subrow">
                        <td colspan="6">
                            <div class="schedule-slots">
                                <strong>Schedule:</strong>
                                <?php foreach ($schedules as $s):
                                    $st = date('M j, H:i', strtotime($s['scheduled_at']));
                                ?>
                                    <span class="slot-tag">
                                        <?= $st ?> — <?= htmlspecialchars($s['trainer_name']) ?>
                                        <form action="../actions/action_admin_classes.php" method="post" style="display:inline" onsubmit="return confirm('Remove this schedule slot?')">
                                            <input type="hidden" name="action" value="remove_schedule">
                                            <input type="hidden" name="schedule_id" value="<?= $s['id'] ?>">
                                            <button class="slot-remove" title="Remove">&times;</button>
                                        </form>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <!-- Add schedule row -->
                    <tr class="schedule-subrow">
                        <td colspan="6">
                            <form action="../actions/action_admin_classes.php" method="post" class="admin-form inline-form-row">
                                <input type="hidden" name="action" value="add_schedule">
                                <input type="hidden" name="class_id" value="<?= $c['id'] ?>">
                                <select name="trainer_id" required>
                                    <option value="">Select trainer...</option>
                                    <?php foreach ($trainers as $t): ?>
                                        <option value="<?= $t->getId() ?>"><?= htmlspecialchars($t->getName()) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="datetime-local" name="scheduled_at" required>
                                <button class="button button-small">+ Add Slot</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php elseif ($tab === 'equipment'): ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Equipment</h3>
            <button class="button button-small" onclick="this.nextElementSibling.style.display='block'">+ Add Equipment</button>
            <div class="inline-form" style="display:none">
                <form action="../actions/action_admin_equipment.php" method="post" class="admin-form">
                    <input type="hidden" name="action" value="create">
                    <input type="text" name="name" placeholder="Equipment Name" required>
                    <input type="number" name="total_quantity" placeholder="Total Quantity" min="1" required>
                    <button class="button button-small">Add</button>
                </form>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Total</th><th>Available</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($equipment as $eq): ?>
                    <tr>
                        <td><?= $eq['id'] ?></td>
                        <td>
                            <form action="../actions/action_admin_equipment.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= $eq['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($eq['name']) ?>" class="inline-input" required>
                        </td>
                        <td>
                                <input type="number" name="total_quantity" value="<?= $eq['total_quantity'] ?>" class="inline-input" style="width:70px" min="0">
                        </td>
                        <td>
                                <input type="number" name="available_quantity" value="<?= $eq['available_quantity'] ?>" class="inline-input" style="width:70px" min="0">
                        </td>
                        <td class="action-cell">
                                <button class="button button-small">Save</button>
                            </form>
                            <form action="../actions/action_admin_equipment.php" method="post" style="display:inline" onsubmit="return confirm('Delete this equipment?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $eq['id'] ?>">
                                <button class="button button-small button-outline" style="border-color:#888;color:#888">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php
drawFooter();
?>
