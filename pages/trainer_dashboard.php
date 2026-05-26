<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();

if (!Session::isLoggedIn() || !Session::isTrainer()) {
    header('Location: ../pages/login.php');
    exit;
}

$userId = Session::getUserId();
$user = User::getById($userId);

if (!$user) {
    header('Location: ../pages/login.php');
    exit;
}

$db = getDatabaseConnection();

// Get trainer profile data
$stmt = $db->prepare('SELECT * FROM trainer_profiles WHERE user_id = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch();

$bio = $profile['bio'] ?? '';
$specializations = $profile['specializations'] ?? '';
$certifications = $profile['certifications'] ?? '';

// Get upcoming classes
$classes = ClassSchedule::getTrainerSchedules($userId, $db);

drawHead("Trainer Dashboard | Ladybug's Gym");
drawHeader();
drawMessages();
?>
<main class="container" style="padding-top: 1em;">
    <div class="page-header">
        <h2>Trainer Dashboard</h2>
        <p>Manage your profile and view class rosters.</p>
    </div>

    <div class="dashboard-layout">
        <section class="dashboard-card">
            <h3>Your Profile</h3>
            <form action="../actions/action_update_trainer_profile.php" method="post" class="trainer-profile-form">
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
                <?php foreach ($classes as $c):
                    $isFull = $c['enrolled'] >= $c['capacity'];
                    $date = date('D, M j', strtotime($c['scheduled_at']));
                    $time = date('H:i', strtotime($c['scheduled_at']));
                    $roster = ClassSchedule::getEnrolledMembers($c['schedule_id'], $db);
                ?>
                    <div class="class-roster-block">
                        <div class="class-roster-header">
                            <div>
                                <strong><?= htmlspecialchars($c['name']) ?></strong>
                                <span class="roster-meta"><?= $date ?> &middot; <?= $time ?> &middot; <?= $c['difficulty'] ?></span>
                            </div>
                            <span class="roster-count <?= $isFull ? 'full' : '' ?>"><?= $c['enrolled'] ?>/<?= $c['capacity'] ?> enrolled</span>
                        </div>
                        <?php if (!empty($roster)): ?>
                            <table class="roster-table">
                                <thead>
                                    <tr>
                                        <th>Member</th>
                                        <th>Email</th>
                                        <th>Enrolled</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roster as $m): ?>
                                        <tr>
                                            <td>
                                                <div class="roster-member">
                                                    <?php if ($m['profile_photo']): ?>
                                                        <img src="../img/<?= htmlspecialchars($m['profile_photo']) ?>" alt="" class="roster-photo">
                                                    <?php else: ?>
                                                        <div class="roster-photo-placeholder"><?= strtoupper(substr($m['name'], 0, 1)) ?></div>
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($m['name']) ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($m['email']) ?></td>
                                            <td><?= date('M j, H:i', strtotime($m['enrolled_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="empty-state" style="margin: 0.5em 0 0; font-size: 0.9em;">No members enrolled yet.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>
</main>

<?php
drawFooter();
?>
