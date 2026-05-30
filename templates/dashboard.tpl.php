<?php
declare(strict_types=1);
function drawDashboardMain(string $role, string $tab, array $classes, array $trainers, array $users, array $equipment, array $nutritionPlans, PDO $db, int $userId): void {
    ?>
    <main class="container" style="padding-top: 1em;">
        <div class="page-header">
            <h2><?= htmlspecialchars(ucfirst($role)) ?> Dashboard</h2>
            <p>Welcome back! Monitor and update gym services here.</p>
        </div>

        <?php drawDashboardTabs($tab, $role); ?>

        <?php 
        if ($tab === 'users' && $role === 'admin') {
            drawManageUsers($users);
        } 
        
        elseif ($tab === 'classes') {
            drawManageClasses($classes, $trainers, $db, $role, $userId);
        } 
        
        elseif ($tab === 'equipment' && $role === 'admin') {
            drawManageEquipment($equipment);
        } 
        
        elseif ($tab === 'nutrition' && $role === 'trainer') {
            drawTrainerNutritionPlans($nutritionPlans);
        }
        ?>
    </main>
    <?php
}

function drawDashboardTabs(string $currentTab, string $role): void {
    ?>
    <div class="admin-tabs">
        <?php if ($role === 'admin'): ?>
            <a href="?tab=users" class="admin-tab <?= $currentTab === 'users' ? 'active' : '' ?>">Users</a>
        <?php endif; ?>
        
        <a href="?tab=classes" class="admin-tab <?= $currentTab === 'classes' ? 'active' : '' ?>">Classes</a>
        
        <?php if ($role === 'admin'): ?>
            <a href="?tab=equipment" class="admin-tab <?= $currentTab === 'equipment' ? 'active' : '' ?>">Equipment</a>
        <?php endif; ?>

        <?php if ($role === 'trainer'): ?>
            <a href="?tab=nutrition" class="admin-tab <?= $currentTab === 'nutrition' ? 'active' : '' ?>">Nutrition Plans</a>
        <?php endif; ?>
    </div>
    <?php
}

function drawManageUsers(array $users): void {
    ?>
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
                        <th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update_user">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($u['name']) ?>" class="inline-input" required>
                        </td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" class="inline-input" required>
                        </td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" style="display:inline">
                                <input type="hidden" name="action" value="set_role">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
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
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
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
    <?php
}

function drawManageClasses(array $classes, array $trainers, PDO $db, string $role, int $currentUserId): void {
    ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Classes</h3>
            <?php if ($role === 'admin'): ?>
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
            <?php endif; ?>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Difficulty</th><th>Capacity</th><th>Scheduled Slots</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($classes as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td>
                            <form action="../actions/action_admin_classes.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" class="inline-input" <?= $role !== 'admin' ? 'disabled' : 'required' ?>>
                        </td>
                        <td>
                            <select name="difficulty" <?= $role !== 'admin' ? 'disabled' : '' ?>>
                                <option value="Beginner" <?= $c['difficulty'] === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
                                <option value="Intermediate" <?= $c['difficulty'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                <option value="Advanced" <?= $c['difficulty'] === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="capacity" value="<?= (int)$c['capacity'] ?>" class="inline-input" style="width:60px" min="1" <?= $role !== 'admin' ? 'disabled' : '' ?>>
                        </td>
                        <td><?= (int)($c['scheduled_count'] ?? 0) ?></td>
                        <td class="action-cell">
                            <input type="text" name="description" value="<?= htmlspecialchars($c['description'] ?? '') ?>" class="inline-input" placeholder="Description" style="width:150px" <?= $role !== 'admin' ? 'disabled' : '' ?>>
                            <?php if ($role === 'admin'): ?>
                                <button class="button button-small">Save</button>
                            </form>
                            <form action="../actions/action_admin_classes.php" method="post" style="display:inline" onsubmit="return confirm('Delete this class?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                <button class="button button-small button-outline" style="border-color:#888;color:#888">Delete</button>
                            </form>
                            <?php else: ?></form><?php endif; ?>
                        </td>
                    </tr>

                    <?php
                    $allSchedules = ClassSchedule::getScheduleForClass($c['id'], $db);
                    $schedules = array_filter($allSchedules, function($s) use ($role, $currentUserId) {
                        return $role === 'admin' || (int)$s['trainer_id'] === $currentUserId;
                    });

                    if (!empty($schedules)):
                    ?>
                    <tr class="schedule-subrow">
                        <td colspan="6">
                            <div class="schedule-slots">
                                <strong>Your Slots:</strong>
                                <?php foreach ($schedules as $s): 
                                    $st = date('M j, H:i', strtotime($s['scheduled_at']));
                                ?>
                                    <span class="slot-tag">
                                        <?= $st ?> — <?= htmlspecialchars($s['trainer_name']) ?>
                                        <form action="../actions/action_admin_classes.php" method="post" style="display:inline" onsubmit="return confirm('Remove this schedule slot?')">
                                            <input type="hidden" name="action" value="remove_schedule">
                                            <input type="hidden" name="schedule_id" value="<?= (int)$s['id'] ?>">
                                            <button class="slot-remove" title="Remove">&times;</button>
                                        </form>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <tr class="schedule-subrow">
                        <td colspan="6">
                            <form action="../actions/action_admin_classes.php" method="post" class="admin-form inline-form-row">
                                <input type="hidden" name="action" value="add_schedule">
                                <input type="hidden" name="class_id" value="<?= (int)$c['id'] ?>">
                                
                                <?php if ($role === 'admin'): ?>
                                    <select name="trainer_id" required>
                                        <option value="">Select trainer...</option>
                                        <?php foreach ($trainers as $t): ?>
                                            <option value="<?= $t->getId() ?>"><?= htmlspecialchars($t->getName()) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <input type="hidden" name="trainer_id" value="<?= $currentUserId ?>">
                                <?php endif; ?>

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
    <?php
}

function drawManageEquipment(array $equipment): void {
    ?>
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
                        <td><?= (int)$eq['id'] ?></td>
                        <td>
                            <form action="../actions/action_admin_equipment.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="id" value="<?= (int)$eq['id'] ?>">
                                <input type="text" name="name" value="<?= htmlspecialchars($eq['name']) ?>" class="inline-input" required>
                        </td>
                        <td>
                            <input type="number" name="total_quantity" value="<?= (int)$eq['total_quantity'] ?>" class="inline-input" style="width:70px" min="0">
                        </td>
                        <td>
                            <input type="number" name="available_quantity" value="<?= (int)$eq['available_quantity'] ?>" class="inline-input" style="width:70px" min="0">
                        </td>
                        <td class="action-cell">
                            <button class="button button-small">Save</button>
                            </form>
                            <form action="../actions/action_admin_equipment.php" method="post" style="display:inline" onsubmit="return confirm('Delete this equipment?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$eq['id'] ?>">
                                <button class="button button-small button-outline" style="border-color:#888;color:#888">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
    <?php
}

function drawTrainerNutritionPlans(array $nutritionPlans): void {
   //fazer aqui
}