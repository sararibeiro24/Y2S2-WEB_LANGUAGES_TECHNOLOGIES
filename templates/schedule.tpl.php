<?php

function drawScheduleFilters(array $trainers): void {
?>
<div class="container">
    <div class="schedule-filters">
        <div class="filter-row">
            <div class="filter-group">
                <label for="filterSearch">Search class</label>
                <input id="filterSearch" type="text" class="input-field" placeholder="Class name...">
            </div>
            <div class="filter-group">
                <label for="filterTrainer">Trainer</label>
                <select id="filterTrainer" class="input-field">
                    <option value="">All trainers</option>
                    <?php foreach ($trainers as $t): ?>
                        <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterDifficulty">Difficulty</label>
                <select id="filterDifficulty" class="input-field">
                    <option value="">All levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="filterDate">Jump to date</label>
                <input id="filterDate" type="date" class="input-field">
            </div>
            <div class="filter-group filter-actions">
                <label>&nbsp;</label>
                <button class="button button-small" onclick="clearFilters()">Clear</button>
            </div>
        </div>
    </div>
</div>
<?php
}

function drawScheduleCalendar(string $weekStart, array $byDay, array $enrolledIds, bool $isLoggedIn): void {
    $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $today = date('Y-m-d');
?>
<main class="container schedule-page">
    <div class="calendar-header">
        <div class="calendar-nav">
            <button class="calendar-nav-btn" onclick="navigateWeek(-1)" title="Previous week">&#10094;</button>
            <span class="calendar-week-label" id="weekLabel">
                <?= date('M j', strtotime($weekStart)) ?> &ndash; <?= date('M j, Y', strtotime($weekStart . ' +6 days')) ?>
            </span>
            <button class="calendar-nav-btn" onclick="navigateWeek(1)" title="Next week">&#10095;</button>
            <button class="button button-small" onclick="navigateWeek(0)" style="margin-left: 0.5em;">Today</button>
        </div>
    </div>

    <div class="calendar-grid" id="calendarGrid" data-week="<?= htmlspecialchars($weekStart) ?>">

        <?php foreach ($dayNames as $name): ?>
            <div class="calendar-day-header"><?= $name ?></div>
        <?php endforeach; ?>

        <?php foreach ($dayNames as $i => $name):
            $date     = date('Y-m-d', strtotime($weekStart . " +$i days"));
            $isToday  = $date === $today;
            $dayClass = 'calendar-day' . ($isToday ? ' today' : '');
            $dayNum   = date('j', strtotime($date));
        ?>
            <div class="<?= $dayClass ?>" data-date="<?= $date ?>">
                <div class="calendar-day-number"><?= $dayNum ?></div>

                <?php if (isset($byDay[$date])): ?>
                    <?php foreach ($byDay[$date] as $c):
                        $isFull = $c['enrolled'] >= $c['capacity'];
                        $isEnrolled = in_array($c['schedule_id'], $enrolledIds);
                        $isPast = strtotime($c['scheduled_at']) < time();
                    ?>
                        <div class="calendar-class-card<?= $isFull ? ' full' : '' ?><?= $isEnrolled ? ' enrolled' : '' ?><?= $isPast ? ' past' : '' ?>"
                             data-id="<?= $c['schedule_id'] ?>"
                             onclick="openClassModal(<?= $c['schedule_id'] ?>)">
                            <div class="ccal-time"><?= date('H:i', strtotime($c['scheduled_at'])) ?></div>
                            <div class="ccal-name"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="ccal-trainer"><?= htmlspecialchars($c['trainer']) ?></div>
                            <div class="ccal-spots"><?= $c['enrolled'] ?>/<?= $c['capacity'] ?></div>
                            <?php if ($isEnrolled): ?>
                                <div class="ccal-enrolled-badge">Enrolled</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>
<?php
}

function drawScheduleModals(): void {
?>
<div class="modal-overlay" id="classModal">
    <div class="modal-content" id="classModalContent">
        <button class="modal-close" onclick="closeModal('classModal')">&times;</button>
        <div id="classModalBody">Loading...</div>
    </div>
</div>

<div class="modal-overlay" id="authModal">
    <div class="modal-content" style="text-align: center;">
        <button class="modal-close" onclick="closeModal('authModal')">&times;</button>
        <h3 class="modal-title" style="margin-top: 0.5em;">Join the Hive!</h3>
        <p class="modal-body" style="margin: 1em 0;">You need an account to enroll in classes. Ready to start your transformation?</p>
        <div class="auth-modal-btns">
            <a href="login.php" class="button">Log In</a>
            <a href="register.php" class="button button-outline">Sign Up</a>
        </div>
    </div>
</div>

<div class="modal-overlay" id="confirmModal">
    <div class="modal-content" style="text-align: center; max-width: 400px;">
        <h3 class="modal-title" style="margin-top: 0.3em;">Confirm</h3>
        <p class="modal-body" id="confirmMsg"></p>
        <div style="display: flex; gap: 1em; justify-content: center; margin-top: 1.5em;">
            <button class="button" id="confirmYes">Yes</button>
            <button class="button button-outline" id="confirmNo">Cancel</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="alertModal">
    <div class="modal-content" style="text-align: center; max-width: 400px;">
        <p class="modal-body" id="alertMsg" style="margin: 1em 0;"></p>
        <button class="button" id="alertOk">OK</button>
    </div>
</div>
<?php
}

function drawScheduleBootstrap(string $weekStart, bool $isLoggedIn, array $enrolledIds, ?int $userId, ?string $userRole): void {
?>
<script>
    const CURRENT_WEEK  = '<?= htmlspecialchars($weekStart) ?>';
    const IS_LOGGED_IN  = <?= $isLoggedIn ? 'true' : 'false' ?>;
    const USER_ID       = <?= $userId ?: 'null' ?>;
    const USER_ROLE     = '<?= $userRole ?? '' ?>';
    let   ENROLLED_IDS  = <?= json_encode($enrolledIds) ?>;
</script>
<script src="../javascript/schedule_filter.js" defer></script>
<?php
}