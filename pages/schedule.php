<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$userId = Session::getUserId();

$weekStart = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
$classes = ClassSchedule::getWeekClasses($weekStart);

// Group by day
$byDay = [];
foreach ($classes as $c) {
    $day = date('Y-m-d', strtotime($c['scheduled_at']));
    $byDay[$day][] = $c;
}

// Get user enrollments
$enrolledIds = $isLoggedIn ? ClassSchedule::getUserEnrollmentIds($userId) : [];

drawHead("Class Schedule | Ladybug's Gym");
drawHeader();
drawPageHeader('Class Schedule', 'Find and book your next workout session.');
date_default_timezone_set('UTC');
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

    <div class="calendar-grid" id="calendarGrid" data-week="<?= $weekStart ?>">
        <?php
        $dayNames = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $today = date('Y-m-d');

        // Row 1: Day headers
        foreach ($dayNames as $name):
        ?>
            <div class="calendar-day-header"><?= $name ?></div>
        <?php endforeach; ?>

        <?php
        // Row 2: Day cells
        foreach ($dayNames as $i => $name):
            $date = date('Y-m-d', strtotime($weekStart . " +$i days"));
            $isToday = $date === $today;
            $dayClasses = 'calendar-day';
            if ($isToday) $dayClasses .= ' today';
            $dayNum = date('j', strtotime($date));
        ?>
            <div class="<?= $dayClasses ?>" data-date="<?= $date ?>">
                <div class="calendar-day-number"><?= $dayNum ?></div>
                <?php if (isset($byDay[$date])): ?>
                    <?php foreach ($byDay[$date] as $c):
                        $isFull = $c['enrolled'] >= $c['capacity'];
                        $isEnrolled = in_array((string)$c['schedule_id'], $enrolledIds, true);
                    ?>
                        <div class="calendar-class-card<?= $isFull ? ' full' : '' ?>"
                             data-id="<?= $c['schedule_id'] ?>"
                             onclick="openClassModal(<?= $c['schedule_id'] ?>)">
                            <div class="ccal-time"><?= date('H:i', strtotime($c['scheduled_at'])) ?></div>
                            <div class="ccal-name"><?= htmlspecialchars($c['name']) ?></div>
                            <div class="ccal-trainer"><?= htmlspecialchars($c['trainer']) ?></div>
                            <div class="ccal-spots"><?= $c['enrolled'] ?>/<?= $c['capacity'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Class Detail Modal -->
<div class="modal-overlay" id="classModal">
    <div class="modal-content" id="classModalContent">
        <button class="modal-close" onclick="closeModal('classModal')">&times;</button>
        <div id="classModalBody">Loading...</div>
    </div>
</div>

<!-- Auth Modal -->
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

<script>
    const CURRENT_WEEK = '<?= $weekStart ?>';
    const IS_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;
    const ENROLLED_IDS = <?= json_encode($enrolledIds) ?>;
</script>
<script src="../javascript/schedule_filter.js" defer></script>
<?php
drawFooter();
?>
