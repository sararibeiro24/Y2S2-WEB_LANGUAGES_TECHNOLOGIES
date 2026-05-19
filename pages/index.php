<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ladybug's Gym | Home</title>
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
                    <li><a href="schedule.php">Classes</a></li>
                    <li><a href="#">Trainers</a></li>
                    <li><a href="#">Equipment</a></li>
                    <li><a href="login.php" class="button button-small">Login</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h2>Train Smarter, Not Harder</h2>
                <p>Book classes, track equipment, and improve your fitness.</p>
                <a href="schedule.php" class="button">Explore Classes</a>
            </div>
        </section>

        <section class="classes">
            <div class="container">
                <h2>Featured Classes</h2>
                <div class="grid-container">
                    <article class="card">
                        <h3>Yoga</h3>
                        <p>Relax and improve flexibility.</p>
                    </article>
                    <article class="card">
                        <h3>HIIT</h3>
                        <p>High intensity training for fast results.</p>
                    </article>
                    <article class="card">
                        <h3>Spinning</h3>
                        <p>Burn calories with intense cycling sessions.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="trainers">
            <div class="container">
                <h2>Our Trainers</h2>
                <div class="grid-container">
                    <article class="card trainer-card">
                        <img src="../img/John_Doe.png"  alt="Portrait of Trainer John Doe">
                        <h3>John Doe</h3>
                        <p class="specialization">Strength & Conditioning</p>
                    </article>
                    <article class="card trainer-card">
                        <img src="../img/Maria_Silva.png" alt="Portrait of Trainer Maria Silva">
                        <h3>Maria Silva</h3>
                        <p class="specialization">Yoga Instructor</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="equipment">
            <div class="container">
                <div class="equipment-box">
                    <h2>Equipment Availability</h2>
                    <ul class="equipment-list">

                        <li><span>Treadmills</span> <span class="status available"> Available</span></li>
                        <li><span>Bikes</span> <span class="status limited"> Limited</span></li>
                        <li><span>Weight Benches</span> <span class="status unavailable"> Unavailable</span></li>
                    </ul>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Ladybug's Gym</p>
        </div>
    </footer>
</body>
</html>
