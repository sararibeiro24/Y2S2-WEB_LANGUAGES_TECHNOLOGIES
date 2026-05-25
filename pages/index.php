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
                    <a href="login.php" class="button">JOIN NOW!</a>
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

        <!-- LOGIN / SIGN UP -->
        <section class="teaser-section bg-dark">
            <div class="container split-layout align-center reverse-mobile">
                <div class="login-teaser-cards">
                    <a href="login.php" class="card action-card">
                        <h3>CREATE ACCOUNT</h3>
                        <div class="arrow-circle">➔</div>
                    </a>
                    <a href="login.php" class="card action-card outline-card">
                        <h3>LOG IN AS A<br>MEMBER/TRAINER</h3>
                        <div class="arrow-circle">➔</div>
                    </a>
                </div>
                <div class="teaser-text text-right">
                    <h2>INTERESTED?<br>CREATE AN ACCOUNT!</h2>
                    <p class="subtitle text-white">Join the Ladybug family today and start your journey.</p>
                </div>
            </div>
        </section>

        <!-- NEWS -->
        <section class="teaser-section bg-white">
            <div class="container center-layout news-section">
                <h2>NEWS & INFO</h2>
                <p class="subtitle large-subtitle">Here could be some news about the gym, like new openings, new classes, huge achievements, etc...</p>
                
                <div class="news-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
            </div>
        </section>

        <!-- CONCEPT (METAMORPHOSIS) -->
        <section class="teaser-section bg-light-red">
            <div class="container center-layout">
                <h2>Time to become spotless!</h2>
                <p class="subtitle">Join our community and transform yourself.</p>
                
                <div class="metamorphosis-grid">
                    <div class="step">
                        <div class="icon">🐛</div>
                        <h4>Top-notch equipment</h4>
                    </div>
                    <div class="step">
                        <div class="icon">🧅</div>
                        <h4>Worth your money</h4>
                    </div>
                    <div class="step">
                        <div class="icon">🦋</div>
                        <h4>No excuses.<br>Guaranteed results.</h4>
                    </div>
                </div>
                <a href="login.php" class="button">JOIN NOW!</a>
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
                    <div class="floating-icon">🚲</div>
                    <div class="floating-icon">🏋️‍♂️</div>
                </div>
            </div>
        </section>

        <!-- CLASSES -->
        <section class="teaser-section bg-white">
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

        <!-- TRAINERS -->
        <section class="teaser-section bg-light-red">
            <div class="container split-layout align-center">
                <div class="teaser-text">
                    <h2>MEET THE TEAM!</h2>
                    <p class="subtitle">Our dedicated professionals are here to help you fly.</p>
                    <a href="#" class="button">SEE MORE</a>
                    <span class="callout-text text-rotated">Easy to contact! ⚡</span>
                </div>
                <div class="carousel-card card">
                    <div class="carousel-nav left">❮</div>
                    <div class="carousel-content">
                        <div class="icon">👤</div>
                        <h3>JOHN DOE</h3>
                        <p>Strength & Conditioning</p>
                    </div>
                    <div class="carousel-nav right">❯</div>
                </div>
            </div>
        </section>

        <!-- NUTRITION -->
        <section class="teaser-section bg-dark">
            <div class="container split-layout align-center reverse-mobile">
                <div class="carousel-card card">
                    <div class="carousel-nav left">❮</div>
                    <div class="carousel-content">
                        <span class="badge badge-red">400 kcal only!</span>
                        <div class="icon">🍛</div>
                        <h3>TURKEY W/ RICE</h3>
                        <p>High protein, low carb.</p>
                    </div>
                    <div class="carousel-nav right">❯</div>
                </div>
                <div class="teaser-text text-right text-white">
                    <h2>SPOT-ON NUTRITION</h2>
                    <p class="subtitle">Tailored meal plans for muscle gain or weight loss.</p>
                    <span class="callout-text text-rotated">Free of charge! ✨</span><br><br>
                    <a href="#" class="button button-outline-white">SEE MORE</a>
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

        <!-- Q&A -->
        <section class="teaser-section bg-light-red">
            <div class="container split-layout align-center reverse-mobile">
                <div class="qa-card card">
                    <div class="q-block">
                        <strong>Q:</strong> Se a Sara é de Águas Santas, isso faz dela o que?
                    </div>
                    <div class="a-block">
                        <strong>A:</strong> Gay, acho eu.
                    </div>
                </div>
                <div class="teaser-text text-right">
                    <h2>WHEN IN DOUBT<br>BUG IT OUT!</h2>
                    <p class="subtitle">Frequently asked questions and support.</p>
                    <a href="#" class="button">CONTACT US!</a>
                </div>
            </div>
        </section>

    </main>
</body>
</html>
<?php
drawFooter();
?>