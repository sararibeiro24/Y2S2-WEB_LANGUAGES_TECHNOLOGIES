<?php
require_once(__DIR__ . '/../templates/common.tpl.php');

drawHead("Ladybug's Gym | Welcome");
drawHeader();
?>
<!DOCTYPE html>
<html lang="en">
<body>
    <main class="scrollable-home">
        
        <!-- PLANS -->
        <section class="teaser-section bg-light-red">
            <div class="container split-layout">
                <div class="teaser-text">
                    <h2 class="huge-text">BECOME<br>UN-BEETLE-ABLE<br>TODAY!</h2>
                    <a href="#" class="button">JOIN NOW!</a>
                </div>
                <div class="teaser-card card">
                    <h3>Plans From</h3>
                    <div class="price">2<span class="cents">,99€</span> <span class="per">/ week</span></div>
                    <ul class="perks">
                        <li>🐞 Forever!</li>
                        <li>🐞 Free schedule</li>
                        <li>🐞 No loyalty</li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CONCEPT / METAMORPHOSIS -->
        <section class="teaser-section bg-white">
            <div class="container center-layout">
                <h2>Time to become spotless!</h2>
                <p class="subtitle">Join our community and transform yourself.</p>
                
                <div class="metamorphosis-grid">
                    <div class="step">
                        <div class="icon">🐛</div>
                        <h4>Top-notch equipment</h4>
                    </div>
                    <div class="step">
                        <div class="icon">🧅</div> <!-- Placeholder for cocoon! -->
                        <h4>Worth your money</h4>
                    </div>
                    <div class="step">
                        <div class="icon">🦋</div>
                        <h4>No excuses. Guaranteed results.</h4>
                    </div>
                </div>
                <a href="#" class="button">JOIN NOW!</a>
            </div>
        </section>

        <!-- EQUIPMENT -->
        <section class="teaser-section bg-dark">
            <div class="container split-layout align-center">
                <div class="teaser-text text-white">
                    <h2>Perfect equipment to spot</h2>
                    <p class="subtitle">OVER 67 MACHINES TO CHOOSE FROM!</p>
                    <a href="#" class="button button-outline-white">SEE MACHINES!</a>
                </div>
                <div class="teaser-graphics">
                    <!-- Emojis as placeholders for your drawn icons (bike, weights) -->
                    <div class="floating-icon">🚲</div>
                    <div class="floating-icon">🏋️‍♂️</div>
                </div>
            </div>
        </section>

        <!-- CLASSES -->
        <section class="teaser-section bg-light-red">
            <div class="container split-layout align-center reverse-mobile">
                <div class="teaser-graphics">
                    <div class="floating-icon">🏊‍♀️</div>
                    <div class="floating-icon">🧘‍♀️</div>
                </div>
                <div class="teaser-text text-right">
                    <h2>Fly high with our classes</h2>
                    <p class="subtitle">OVER 67 CLASSES PER WEEK!</p>
                    <a href="schedule.php" class="button">SEE SCHEDULE!</a>
                </div>
            </div>
        </section>

        <!-- FEEDBACK -->
        <section class="teaser-section bg-white">
            <div class="container split-layout align-center">
                <div class="teaser-text">
                    <h2>YOUR OPINION<br>HELPS US TAKE WING!</h2>
                    <p>Read what our members have to say about us.</p>
                    <a href="#" class="button">LEAVE FEEDBACK</a>
                </div>
                <div class="testimonial-card card">
                    <p class="quote">"I love Sara Ribeiro she's a goat! Yay!"</p>
                    <div class="author">- Narciso TB ⭐⭐⭐⭐⭐</div>
                </div>
            </div>
        </section>

    </main>
</body>
</html>
<?php
drawFooter();
?>