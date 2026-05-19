<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Schedule | Ladybug's Gym</title>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/pages.css">
</head>
<body>

    <header class="site-header">
        <div class="container header-container">
            <h1>Ladybug's Gym</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#">Trainers</a></li>
                    <li><a href="#">Equipment</a></li>
                    <li><a href="login.php" class="button button-small">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

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
                    
            
                    <article class="card class-card">
                        <div class="class-time">09:00 AM</div>
                        <span class="badge badge-green">Available</span>
                        <h3 class="class-title">Morning Yoga</h3>
                        <p class="class-trainer">with Maria Silva</p>
                        <div class="class-meta">
                            <span> <img src="../img/relogio.png" alt="Clock icon">60 min</span>
                            <span><img src="../img/follower.png" alt="Users icon">12/20 spots</span>
                        </div>
                       
                        <a href="#" class="button button-small button-outline">Enroll Now</a>
                    </article>

                    <article class="card class-card">
                        <div class="class-time">12:30 PM</div>
                        <span class="badge badge-red">Full</span>
                        <h3 class="class-title">Lunchtime HIIT</h3>
                        <p class="class-trainer">with John Doe</p>
                        <div class="class-meta">
                            <span><img src="../img/relogio.png" alt="Clock icon">  45 min</span>
                            <span><img src="../img/follower.png" alt="Users icon">  15/15 spots</span>
                        </div>
                        <button class="button button-small" disabled>Waitlist</button>
                    </article>

                    <article class="card class-card">
                        <div class="class-time">18:00 PM</div>
                        <span class="badge badge-yellow">Few Spots</span>
                        <h3 class="class-title">Advanced Spinning</h3>
                        <p class="class-trainer">with John Doe</p>
                        <div class="class-meta">
                            <span><img src="../img/relogio.png" alt="Clock icon">  60 min</span>
                            <span><img src="../img/follower.png" alt="Users icon">  18/20 spots</span>
                        </div>
                        <a href="#" class="button button-small button-outline">Enroll Now</a>
                    </article>

                </div>
            </div>

        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Ladybug's Gym</p>
        </div>
    </footer>

</body>
</html>
