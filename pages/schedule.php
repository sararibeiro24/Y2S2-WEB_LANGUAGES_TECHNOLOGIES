<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/schedule.tpl.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');

$schedules = ClassSchedule::getUpcomingWithStats();
$trainers = Trainer::getAllTrainers();

drawHead("Class Schedule | Ladybug's Gym");
drawHeader();
drawPageHeader('Class Schedule', 'Find and book your next workout session.');
?>
<main class="container schedule-page">
    <div class="schedule-layout">
        <aside class="filter-sidebar">
            <h3>Filter Classes</h3>
            <form action="#" method="GET" id="filterForm">
                <fieldset>
                    <legend class="sr-only">Search Filters</legend>

                    <div class="form-group">
                        <label for="searchQuery">Search Class</label>
                        <input type="text" id="searchQuery" name="searchQuery" class="input-field" placeholder="e.g., Yoga, HIIT">
                    </div>

                    <div class="form-group">
                        <label for="trainerSelect">Trainer</label>
                        <select id="trainerSelect" name="trainer" class="input-field">
                            <option value="all">All Trainers</option>
                            <?php foreach ($trainers as $trainer): ?>
                                <option value="<?= $trainer->getId() ?>"><?= htmlspecialchars($trainer->getName()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="classDate">Date</label>
                        <input type="date" id="classDate" name="date" class="input-field">
                    </div>

                    <button type="submit" class="button button-full">Apply Filters</button>
                </fieldset>
            </form>
        </aside>

        <div class="class-results">
            <div class="grid-container" id="classGrid">
                <?php foreach ($schedules as $class):
                    $status = 'Available';
                    if ($class['enrolled'] >= $class['capacity']) {
                        $status = 'Full';
                    } elseif ($class['enrolled'] >= $class['capacity'] - 1) {
                        $status = 'Few Spots';
                    }

                    $time = date('H:i', strtotime($class['scheduled_at']));
                ?>
                    <?php drawClassCard(
                        $class['schedule_id'],
                        $time,
                        $status,
                        $class['name'],
                        $class['trainer'],
                        60,
                        $class['enrolled'],
                        $class['capacity']
                    ); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
<script src="../javascript/schedule_filter.js" defer></script>
<?php
drawFooter();
?>
