<?php
declare(strict_types=1);

function drawManageClasses(array $mySchedules, array $allClasses): void {
?>
<main class="container" style="padding-top: 1em;">
    <div class="page-header">
        <h2>Manage Your Classes</h2>
        <p>Add, edit or remove your scheduled classes.</p>
    </div>

    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Add New Schedule</h3>
        </div>
        <form action="../actions/action_trainer_schedule.php" method="post" class="admin-form inline-form-row">
            <input type="hidden" name="action" value="add">
            <select name="class_id" required>
                <option value="">Select class...</option>
                <?php foreach ($allClasses as $c): ?>
                    <option value="<?= (int)$c['id'] ?>"><?= htmlspecialchars($c['name']) ?> (<?= htmlspecialchars($c['difficulty']) ?>)</option>
                <?php endforeach; ?>
            </select>
            <input type="datetime-local" name="scheduled_at" required>
            <button class="button button-small">+ Add Slot</button>
        </form>
    </section>

    <section class="admin-section">
        <div class="admin-section-header">
            <h3>Your Scheduled Classes</h3>
        </div>
        <?php if (empty($mySchedules)): ?>
            <p class="empty-state">You have no classes scheduled.</p>
        <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Class</th>
                        <th>Difficulty</th>
                        <th>Date & Time</th>
                        <th>Enrolled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mySchedules as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['difficulty']) ?></td>
                        <td>
                            <form action="../actions/action_trainer_schedule.php" method="post" class="inline-edit" style="display:inline">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="schedule_id" value="<?= (int)$s['schedule_id'] ?>">
                                <input type="datetime-local" name="scheduled_at" value="<?= date('Y-m-d\TH:i', strtotime($s['scheduled_at'])) ?>" class="inline-input" required>
                                <button class="button button-small">Save</button>
                            </form>
                        </td>
                        <td><?= (int)$s['enrolled'] ?>/<?= (int)$s['capacity'] ?></td>
                        <td class="action-cell">
                            <form action="../actions/action_trainer_schedule.php" method="post" style="display:inline" onsubmit="return confirm('Remove this schedule slot?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="schedule_id" value="<?= (int)$s['schedule_id'] ?>">
                                <button class="button button-small button-outline" style="border-color:#888;color:#888">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>
</main>
<?php
}