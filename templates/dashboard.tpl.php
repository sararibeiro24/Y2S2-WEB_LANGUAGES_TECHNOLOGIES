<?php
declare(strict_types=1);

function drawDashboardMain(string $role, string $tab, array $classes, array $trainers, array $users, array $equipment, array $nutritionPlans, PDO $db, int $userId, string $bio, string $specializations, string $certifications): void {
    ?>
    <main class="container" style="padding-top: 1em;">
        <div class="page-header">
            <h2><?= htmlspecialchars(ucfirst($role)) ?> Dashboard</h2>
            <p>Welcome back! Monitor and update gym services here.</p>
        </div>
        <?php drawDashboardTabs($tab, $role);
        if ($tab === 'users' && $role === 'admin') {
            drawManageUsers($users);
        } elseif ($tab === 'equipment' && $role === 'admin') {
            drawManageEquipment($equipment);
        } elseif ($tab === 'classes') {
            drawManageClasses($classes, $trainers, $db, $role, $userId);
        } elseif ($tab === 'trainer_profile' && $role === 'trainer') { ?>
            <div class="dashboard-layout">
                <section class="dashboard-card">
                    <h3>Your Profile</h3>
                    <form action="../actions/action_update_trainer_profile.php" method="post" class="trainer-profile-form">
                        <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea id="bio" name="bio" rows="4" maxlength="500"><?= htmlspecialchars($bio) ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="specializations">Specializations <span class="field-hint">(comma-separated)</span></label>
                            <input type="text" id="specializations" name="specializations" value="<?= htmlspecialchars($specializations) ?>" placeholder="e.g. Yoga, Pilates, Meditation">
                        </div>
                        <div class="form-group">
                            <label for="certifications">Certifications <span class="field-hint">(comma-separated)</span></label>
                            <input type="text" id="certifications" name="certifications" value="<?= htmlspecialchars($certifications) ?>" placeholder="e.g. NASM, ACE, Yoga Alliance">
                        </div>
                        <button class="button" type="submit">Save Profile</button>
                    </form>
                </section>
                <section class="dashboard-card">
                    <h3>Your Upcoming Classes</h3>
                    <?php if (empty($classes)): ?>
                        <p class="empty-state">You have no upcoming classes scheduled.</p>
                    <?php else: ?>
                        <?php foreach ($classes as $c) { drawClassRosterBlock($c, $db); } ?>
                    <?php endif; ?>
                </section>
            </div>
        <?php } elseif ($tab === 'nutrition' && $role === 'trainer') {
            drawTrainerNutritionPlans($nutritionPlans);
        } ?>
    </main>
    <?php
}

function drawDashboardTabs(string $currentTab, string $role): void { ?>
    <div class="admin-tabs">
        <?php if ($role === 'admin'): ?>
            <a href="?tab=users"     class="admin-tab <?= $currentTab === 'users'     ? 'active' : '' ?>">Users</a>
            <a href="?tab=classes"   class="admin-tab <?= $currentTab === 'classes'   ? 'active' : '' ?>">Classes</a>
            <a href="?tab=equipment" class="admin-tab <?= $currentTab === 'equipment' ? 'active' : '' ?>">Equipment</a>
        <?php endif; ?>
        <?php if ($role === 'trainer'): ?>
            <a href="?tab=trainer_profile" class="admin-tab <?= $currentTab === 'trainer_profile' ? 'active' : '' ?>">Public Profile</a>
            <a href="?tab=classes"         class="admin-tab <?= $currentTab === 'classes'         ? 'active' : '' ?>">All Classes</a>
            <a href="?tab=nutrition"       class="admin-tab <?= $currentTab === 'nutrition'       ? 'active' : '' ?>">Nutrition</a>
        <?php endif; ?>
    </div>
<?php }

function drawManageUsers(array $users): void { ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Users</h3>
            <button class="button button-small" onclick="this.nextElementSibling.style.display='block'">+ Add User</button>
            <div class="inline-form" style="display:none">
                <form action="../actions/action_admin_users.php" method="post" class="admin-form">
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <input type="text"     name="username" placeholder="Username"  required>
                    <input type="email"    name="email"    placeholder="Email"     required>
                    <input type="text"     name="name"     placeholder="Full Name" required>
                    <input type="password" name="password" placeholder="Password"  required>
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
                    <tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= htmlspecialchars($u['name']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" style="display:inline">
                                <input type="hidden" name="action" value="set_role">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <select name="role" onchange="this.form.submit()">
                                    <option value="member"  <?= $u['role'] === 'member'  ? 'selected' : '' ?>>Member</option>
                                    <option value="trainer" <?= $u['role'] === 'trainer' ? 'selected' : '' ?>>Trainer</option>
                                    <option value="admin"   <?= $u['role'] === 'admin'   ? 'selected' : '' ?>>Admin</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" style="display:inline">
                                <input type="hidden" name="action" value="toggle_active">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="hidden" name="active" value="<?= $u['active'] ? 0 : 1 ?>">
                                <button class="badge badge-<?= $u['active'] ? 'active' : 'inactive' ?>"><?= $u['active'] ? 'Active' : 'Inactive' ?></button>
                            </form>
                        </td>
                        <td>
                            <form action="../actions/action_admin_users.php" method="post" style="display:inline">
                                <input type="hidden" name="action" value="update_user">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                                <input type="text"  name="name"  value="<?= htmlspecialchars($u['name'])  ?>" class="inline-input" required>
                                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" class="inline-input" required>
                                <button class="button button-small">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php }

function drawManageClasses(array $classes, array $trainers, PDO $db, string $role, int $currentUserId): void {
    $classData = [];
    foreach ($classes as $c) {
        $classId = (int)($c['id'] ?? $c['class_id'] ?? 0);
        if ($classId === 0) continue;

        $allSchedules = ClassSchedule::getScheduleForClass($classId, $db);

        $schedules = array_values(array_filter($allSchedules, function ($s) use ($role, $currentUserId) {
            return $role === 'admin' || (int)($s['trainer_id'] ?? 0) === $currentUserId;
        }));

        if ($role === 'trainer' && empty($schedules)) continue;

        $classData[] = [
            'id'          => $classId,
            'name'        => (string)($c['name']        ?? ''),
            'description' => (string)($c['description'] ?? ''),
            'difficulty'  => (string)($c['difficulty']  ?? ''),
            'capacity'    => (int)($c['capacity']       ?? 0),
            'schedules'   => $schedules,
        ];
    }
    ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Classes</h3>
            <?php if ($role === 'admin'): ?>
            <button class="button button-small" onclick="this.nextElementSibling.style.display='block'">+ Add Class</button>
            <div class="inline-form" style="display:none">
                <form action="../actions/action_classes.php" method="post" class="admin-form">
                    <input type="hidden" name="action"     value="create">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <input type="text"   name="name"        placeholder="Class Name"  required>
                    <input type="text"   name="description" placeholder="Description">
                    <input type="number" name="capacity"    placeholder="Capacity" min="1" required>
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

        <?php if (empty($classData)): ?>
            <p class="empty-state">No classes found.</p>
        <?php else: ?>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Difficulty</th>
                        <th>Capacity</th>
                        <th>Slots</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($classData as $c): ?>
                    <tr>
                        <td><?= $c['id'] ?></td>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['difficulty']) ?></td>
                        <td><?= $c['capacity'] ?></td>
                        <td><?= count($c['schedules']) ?></td>
                        <td>
                            <button class="button button-small"
                                onclick="var el=document.getElementById('edit-cls-<?= $c['id'] ?>'); el.style.display=el.style.display==='none'?'block':'none'">
                                Edit
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php foreach ($classData as $c): ?>
        <div id="edit-cls-<?= $c['id'] ?>" class="class-detail-block" style="margin-top:1.2em; padding:1em 1.2em; border:1px solid rgba(255,255,255,.1); border-radius:8px; display: none;">

            <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:.6em;">
                <strong><?= htmlspecialchars($c['name']) ?></strong>
                <span style="font-size:.82em; opacity:.55;"><?= htmlspecialchars($c['difficulty']) ?> &middot; cap.&nbsp;<?= $c['capacity'] ?></span>
            </div>

            <?php if (!empty($c['schedules'])): ?>
            <div class="schedule-slots" style="margin-bottom:.7em;">
                <strong style="font-size:.85em;">Slots:</strong>
                <?php foreach ($c['schedules'] as $s):
                    $label = isset($s['scheduled_at']) ? date('M j, H:i', strtotime($s['scheduled_at'])) : 'TBD';
                    $sId   = (int)($s['id'] ?? $s['schedule_id'] ?? 0);
                ?>
                <span class="slot-tag">
                    <?= $label ?> &mdash; <?= htmlspecialchars($s['trainer_name'] ?? 'Unknown') ?>
                    <form action="../actions/action_classes.php" method="post" style="display:inline"
                          onsubmit="return confirm('Remove this slot?')">
                        <input type="hidden" name="action"      value="remove_schedule">
                        <input type="hidden" name="csrf_token"  value="<?= Session::getCsrfToken() ?>">
                        <input type="hidden" name="schedule_id" value="<?= $sId ?>">
                        <button class="slot-remove" title="Remove">&times;</button>
                    </form>
    
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form action="../actions/action_classes.php" method="post"
                  style="display:flex; gap:.5em; flex-wrap:nowrap; align-items:center; margin-bottom:1.2em;">
                <input type="hidden" name="action"     value="add_schedule">
                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                <input type="hidden" name="class_id"   value="<?= $c['id'] ?>">
                
                <?php if ($role === 'admin'): ?>
                <select name="trainer_id" class="input-field" required style="flex: 1; min-width: 120px;">
                    <option value="">Select trainer...</option>
                    <?php foreach ($trainers as $t): ?>
                    <option value="<?= (int)($t['id'] ?? $t['user_id'] ?? 0) ?>">
                        <?= htmlspecialchars($t['name'] ?? '') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <input type="hidden" name="trainer_id" value="<?= $currentUserId ?>">
                <?php endif; ?>
                
                <input type="datetime-local" name="scheduled_at" class="input-field" required style="flex: 1; min-width: 120px;">
                <button class="button button-small" style="flex-shrink: 0;">+ Add Slot</button>
            </form>

            <?php if ($role === 'admin'): ?>
            <div style="padding-top:.7em; border-top:1px solid rgba(255,255,255,.1); margin-top:.5em;">
                <form action="../actions/action_classes.php" method="post" style="display:flex; flex-direction:column; gap:.6em;">
                    <input type="hidden" name="action"     value="update">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <input type="hidden" name="id"         value="<?= $c['id'] ?>">
                    
                    <div style="display:flex; gap:.5em; width:100%;">
                        <input type="text"   name="name"     value="<?= htmlspecialchars($c['name']) ?>" class="input-field inline-input" placeholder="Name" required style="flex: 1;">
                        <input type="number" name="capacity" value="<?= $c['capacity'] ?>"               class="input-field inline-input" placeholder="Cap" min="1" style="width:70px; flex-shrink: 0;">
                        <select name="difficulty" class="input-field" style="flex: 2;">
                            <option value="Beginner"     <?= $c['difficulty'] === 'Beginner'     ? 'selected' : '' ?>>Beginner</option>
                            <option value="Intermediate" <?= $c['difficulty'] === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
                            <option value="Advanced"     <?= $c['difficulty'] === 'Advanced'     ? 'selected' : '' ?>>Advanced</option>
                        </select>
                    </div>
                    
                    <div style="width:100%;">
                        <input type="text" name="description" value="<?= htmlspecialchars($c['description']) ?>" class="input-field inline-input" placeholder="Description" style="width:100%; box-sizing: border-box;">
                    </div>
                    
                    <div style="display:flex; justify-content:flex-start; margin-top:.2em;">
                        <button class="button button-small">Save</button>
                    </div>
                </form>
                
                <form action="../actions/action_classes.php" method="post" style="margin-top:.6em"
                      onsubmit="return confirm('Delete this class?')">
                    <input type="hidden" name="action"     value="delete">
                    <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                    <input type="hidden" name="id"         value="<?= $c['id'] ?>">
                    <button class="button button-small button-outline" style="border-color:#888;color:#888">Delete class</button>
                </form>
            </div>
            <?php endif; ?>

        </div>
        <?php endforeach; ?>

        <?php endif; ?>
    </section>
<?php }

function drawManageEquipment(array $equipment): void { ?>
    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Manage Equipment</h3>
            <button class="button button-small" onclick="this.nextElementSibling.style.display='block'">+ Add Equipment</button>
            <div class="inline-form" style="display:none">
                <form action="../actions/action_admin_equipment.php" method="post" class="admin-form">
                    <input type="hidden" name="action"         value="create">
                    <input type="hidden" name="csrf_token"     value="<?= Session::getCsrfToken() ?>">
                    <input type="text"   name="name"           placeholder="Equipment Name" required>
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
                        <td><?= htmlspecialchars($eq['name']) ?></td>
                        <td><?= (int)$eq['total_quantity'] ?></td>
                        <td><?= (int)$eq['available_quantity'] ?></td>
                        <td class="action-cell">
                            <form action="../actions/action_admin_equipment.php" method="post" style="display:inline">
                                <input type="hidden" name="action"             value="update">
                                <input type="hidden" name="csrf_token"         value="<?= Session::getCsrfToken() ?>">
                                <input type="hidden" name="id"                 value="<?= (int)$eq['id'] ?>">
                                <input type="text"   name="name"               value="<?= htmlspecialchars($eq['name']) ?>"  class="inline-input" required>
                                <input type="number" name="total_quantity"     value="<?= (int)$eq['total_quantity'] ?>"     class="inline-input" style="width:70px" min="0">
                                <input type="number" name="available_quantity" value="<?= (int)$eq['available_quantity'] ?>" class="inline-input" style="width:70px" min="0">
                                <button class="button button-small">Save</button>
                            </form>
                            <form action="../actions/action_admin_equipment.php" method="post" 
                                  onsubmit="return confirm('Delete this equipment?')">
                                <input type="hidden" name="action"     value="delete">
                                <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
                                <input type="hidden" name="id"         value="<?= (int)$eq['id'] ?>">
                                <button class="button button-small button-outline" >Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
<?php }

function drawTrainerNutritionPlans(array $nutritionPlans): void {
?>
    <section class="dashboard-card nutrition-requests-card">
                <h3>Nutrition Plan Requests</h3>
                <?php if (empty($nutritionPlans)): ?>
                    <p class="empty-state">No nutrition plan requests yet.</p>
                <?php else: ?>
                    <div class="nutrition-requests-list">
                        <?php foreach ($nutritionPlans as $request): ?>
                            <article class="nutrition-request-card">
                                <div class="request-header">
                                    <div>
                                        <strong><?= htmlspecialchars($request['member_name']) ?></strong>
                                        <span class="request-email"><?= htmlspecialchars($request['member_email']) ?></span>
                                    </div>
                                    <span class="request-goal"><?= htmlspecialchars($request['goal']) ?></span>
                                </div>
                                <div class="request-meta">
                                    <span><strong>Calories:</strong> <?= htmlspecialchars((string)($request['target_calories'] ?? 'Custom')) ?> kcal</span>
                                    <span>Requested <?= htmlspecialchars((new DateTime($request['created_at']))->format('M d, Y')) ?></span>
                                </div>
                                <p class="meal-summary"><?= nl2br(htmlspecialchars($request['meal_details'])) ?></p>
                                <form class="nutrition-request-form" action="../actions/action_approve_nutrition.php" method="POST">
                                    <input type="hidden" name="nutrition_id" value="<?= htmlspecialchars((string)$request['id']) ?>">
                                    <label for="meal_details_<?= htmlspecialchars((string)$request['id']) ?>">Add meal plan details</label>
                                    <textarea id="meal_details_<?= htmlspecialchars((string)$request['id']) ?>" name="meal_details" rows="4" placeholder="Write the nutrition plan details here..." required></textarea>
                                    <button type="submit" class="button">Approve Request</button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
<?php
}

function drawTrainerProfileCard(string $bio, string $specializations, string $certifications): void { ?>
    <section class="dashboard-card" style="background:#1a1a1a; padding:1.5em; border-radius:8px;">
        <h3>Your Profile</h3>
        <form action="../actions/action_update_trainer_profile.php" method="post" class="trainer-profile-form">
            <input type="hidden" name="csrf_token" value="<?= Session::getCsrfToken() ?>">
            <div class="form-group" style="margin-bottom:1em;">
                <label for="bio" style="display:block; margin-bottom:.3em;">Bio</label>
                <textarea id="bio" name="bio" rows="4" maxlength="500"
                    style="width:100%; background:#2a2a2a; color:#fff; border:1px solid #444; border-radius:4px; padding:.5em;"><?= htmlspecialchars($bio) ?></textarea>
            </div>
            <div class="form-group" style="margin-bottom:1em;">
                <label for="specializations" style="display:block; margin-bottom:.3em;">Specializations
                    <span class="field-hint" style="font-size:.8em; color:#888;">(comma-separated)</span>
                </label>
                <input type="text" id="specializations" name="specializations"
                    value="<?= htmlspecialchars($specializations) ?>" placeholder="e.g. Yoga, Pilates"
                    style="width:100%; background:#2a2a2a; color:#fff; border:1px solid #444; border-radius:4px; padding:.5em;">
            </div>
            <div class="form-group" style="margin-bottom:1.5em;">
                <label for="certifications" style="display:block; margin-bottom:.3em;">Certifications
                    <span class="field-hint" style="font-size:.8em; color:#888;">(comma-separated)</span>
                </label>
                <input type="text" id="certifications" name="certifications"
                    value="<?= htmlspecialchars($certifications) ?>" placeholder="e.g. NASM, ACE"
                    style="width:100%; background:#2a2a2a; color:#fff; border:1px solid #444; border-radius:4px; padding:.5em;">
            </div>
            <button class="button" type="submit">Save Profile</button>
        </form>
    </section>
<?php }

function drawTrainerUpcomingClasses(array $classes, PDO $db): void { ?>
    <section class="dashboard-card" style="background:#1a1a1a; padding:1.5em; border-radius:8px;">
        <h3>Your Upcoming Classes &amp; Rosters</h3>
        <?php if (empty($classes)): ?>
            <p class="empty-state">You have no upcoming classes scheduled.</p>
        <?php else: ?>
            <?php foreach ($classes as $c) { drawClassRosterBlock($c, $db); } ?>
        <?php endif; ?>
    </section>
<?php }

function drawClassRosterBlock(array $c, PDO $db): void {
    $isFull     = ($c['enrolled'] ?? 0) >= ($c['capacity'] ?? 0);
    $date       = isset($c['scheduled_at']) ? date('D, M j', strtotime($c['scheduled_at'])) : 'TBD';
    $time       = isset($c['scheduled_at']) ? date('H:i',    strtotime($c['scheduled_at'])) : 'TBD';
    $scheduleId = (int)($c['id'] ?? $c['schedule_id'] ?? 0);
    $roster     = ClassSchedule::getEnrolledMembers($scheduleId, $db); ?>
    <div class="class-roster-block">
        <div class="class-roster-header">
            <div>
                <strong><?= htmlspecialchars($c['name'] ?? '') ?></strong>
                <span class="roster-meta"><?= $date ?> &middot; <?= $time ?> &middot; <?= htmlspecialchars($c['difficulty'] ?? '') ?></span>
            </div>
            <span class="roster-count <?= $isFull ? 'full' : '' ?>"><?= (int)($c['enrolled'] ?? 0) ?>/<?= (int)($c['capacity'] ?? 0) ?> enrolled</span>
        </div>
        <?php if (!empty($roster)): ?>
        <table class="roster-table">
            <thead><tr><th>Member</th><th>Email</th><th>Enrolled</th></tr></thead>
            <tbody>
                <?php foreach ($roster as $m): ?>
                <tr>
                    <td>
                        <div class="roster-member">
                            <?php if (!empty($m['profile_photo'])): ?>
                                <img src="<?= htmlspecialchars(resolvePhoto($m['profile_photo'])) ?>" alt="" class="roster-photo">
                            <?php else: ?>
                                <div class="roster-photo-placeholder"><?= htmlspecialchars(strtoupper(substr($m['name'] ?? 'U', 0, 1))) ?></div>
                            <?php endif; ?>
                            <?= htmlspecialchars($m['name'] ?? '') ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($m['email'] ?? '') ?></td>
                    <td><?= isset($m['enrolled_at']) ? (new DateTime($m['enrolled_at'], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Europe/Lisbon'))->format('M j, H:i') : '' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p class="empty-state" style="margin:.5em 0 0; font-size:.9em;">No members enrolled yet.</p>
        <?php endif; ?>
    </div>
<?php }