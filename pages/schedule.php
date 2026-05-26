<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/schedule.tpl.php');

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/classes.class.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');
require_once(__DIR__ . '/../database/trainer.class.php');

drawHead("Class Schedule | Ladybug's Gym"); 
drawHeader();

$schedules = ClassSchedule::getUpcomingClasses();
$trainers = Trainer::getAllTrainers();

drawPageHeader('Class Schedule', 'Find and book your next workout session.');

$db = getDatabaseConnection();
$classes = GymClass::getAll($db);

/* depois mudar isto para ir buscar a base de dados*/
/*$classes = [
    [
        'time' => '09:00 AM',
        'status' => 'Available',
        'title' => 'Morning Yoga',
        'trainer' => 'Maria Silva',
        'duration' => '60',
        'spots' => '12/20'
    ],
    [
        'time' => '12:30 PM',
        'status' => 'Full',
        'title' => 'Lunchtime HIIT',
        'trainer' => 'John Doe',
        'duration' => '45',
        'spots' => '15/15',
        'buttonText' => 'Waitlist'
    ],
    [
        'time' => '18:00 PM',
        'status' => 'Few Spots',
        'title' => 'Advanced Spinning',
        'trainer' => 'John Doe',
        'duration' => '60',
        'spots' => '18/20'
    ]
];*/
?>



<main class="container schedule-page">
    <div class="schedule-layout">
        <aside class="filter-sidebar">
            <h3>Filter Classes</h3>
            <form action="#" method="GET">
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
            <div class="grid-container">
                <?php foreach ($schedules as $schedule):
                    $enrolledCount = $schedule->getEnrolledCount();
                    $capacity = $schedule->getCapacity();
                    $spots = $enrolledCount . '/' . $capacity;

            <div class="class-results">
                <div class="grid-container">
                    
                    
                <?php foreach ($classes as $class) { ?>

                    <?php

                    $status = 'Available';

                    if ($class['enrolled'] >= $class['capacity']) {
                        $status = 'Full';
                    }
                    elseif ($class['enrolled'] >= $class['capacity'] - 2) {
                        $status = 'Few Spots';
                    }

                    $time = date(
                        'H:i',
                        strtotime($class['scheduled_at'])
                    );

                    drawClassCard(
                        $class['schedule_id'],
                        $time,
                        $status,
                        $class['name'],
                        $class['trainer'],
                        60,
                        $class['enrolled'],
                        $class['capacity']
                    );

                    ?>

                <?php } ?>
                     

                    


                </div>
                    if ($enrolledCount >= $capacity) {
                        $status = 'Full';
                        $buttonText = 'Waitlist';
                    } elseif ($enrolledCount >= $capacity * 0.75) {
                        $status = 'Few Spots';
                        $buttonText = 'Enroll Now';
                    } else {
                        $status = 'Available';
                        $buttonText = 'Enroll Now';
                    }

                    $time = date('g:i A', strtotime($schedule->getScheduledAt()));
                ?>
                    <?php drawClassCard(
                        $time,
                        $status,
                        $schedule->getClassName() ?? 'Class',
                        $schedule->getTrainerName() ?? 'Trainer',
                        '60',
                        $spots,
                        'relogio.png',
                        'follower.png',
                        $buttonText
                    ); ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
<?php
drawFooter();
?>
