

<?php
require_once(__DIR__ . '/../templates/common.tpl.php');

drawHead("Welcome to Ladybug's Gym");
drawHeader();



?>
<!DOCTYPE html>
<html lang="en">

<body>

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
</body>
</html>
<?php
drawFooter();
?>