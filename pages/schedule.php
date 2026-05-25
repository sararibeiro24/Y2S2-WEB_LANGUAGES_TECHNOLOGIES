<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/schedule.tpl.php');

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/classes.class.php');

drawHead("Class Schedule | Ladybug's Gym"); 
drawHeader();

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

<!DOCTYPE html>
<html lang="en">

<body>

    <main class="container schedule-page">
        
        <div class="page-header">
            <h2>Class Schedule</h2>
            <p>Find and book your next workout session.</p>
        </div>

        <div class="schedule-layout">
            
            <aside class="filter-sidebar ">
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
                                <option value="john">John Doe</option>
                                <option value="maria">Maria Silva</option>
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
            </div>

        </div>
    </main>  
</body>
</html>
<?php
drawFooter();
?>
