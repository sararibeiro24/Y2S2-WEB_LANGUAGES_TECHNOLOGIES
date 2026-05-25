<?php
require_once(__DIR__ . '/../templates/common.tpl.php');

drawHead("Ladybug's Gym | Welcome");
drawHeader();
?>
    <main class="scrollable-home">

        <!-- HERO SECTION -->
        <section class="hero-section bg-hero">
            <div class="container hero-content">
                <p class="hero-eyebrow">Welcome to Ladybug's Gym</p>
                <h2 class="hero-headline">TRANSFORM.<br>EVOLVE.<br>FLY.</h2>
                <p class="hero-sub">Porto's most ambitious gym, built for people who don't settle. Open 24/7. Expert trainers. Results guaranteed.</p>
                <div class="hero-ctas">
                    <a href="register.php" class="button">START FREE TRIAL</a>
                    <a href="schedule.php" class="button button-outline-white">VIEW CLASSES</a>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong>500+</strong>
                        <span>Active Members</span>
                    </div>
                    <div class="hero-stat">
                        <strong>67+</strong>
                        <span>Weekly Classes</span>
                    </div>
                    <div class="hero-stat">
                        <strong>150+</strong>
                        <span>Machines</span>
                    </div>
                    <div class="hero-stat">
                        <strong>24/7</strong>
                        <span>Always Open</span>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- PLANS SECTION WITH CAROUSEL -->
        <section id="plans" class="teaser-section bg-hero slanted-bottom">
            <div class="container">
                <h2 class="huge-text text-white">BECOME UN-BEETLE-ABLE TODAY!</h2>
                <p class="subtitle text-white">Join now and start your transformation with our affordable plans</p>
                
                <div class="carousel-wrapper" style="margin-top: 3em;">
                    <button class="carousel-nav-btn prev" onclick="scrollCarousel('plans-carousel', -1)">❮</button>
                    <div class="carousel-container" id="plans-carousel">
                        <div class="carousel-item">
                            <div class="plan-card">
                                <h3 class="plan-name">Starter</h3>
                                <div class="plan-price">€9<span style="font-size: 0.6em;">/month</span></div>
                                <ul class="plan-features">
                                    <li>✓ Gym Access 24/7</li>
                                    <li>✓ Basic Equipment</li>
                                    <li>✓ Locker Room</li>
                                    <li>✗ Personal Training</li>
                                    <li>✗ Classes</li>
                                </ul>
                                <a href="login.php" class="button">GET STARTED</a>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="plan-card">
                                <h3 class="plan-name">Pro</h3>
                                <div class="plan-price">€19<span style="font-size: 0.6em;">/month</span></div>
                                <ul class="plan-features">
                                    <li>✓ Full Equipment Access</li>
                                    <li>✓ All Classes</li>
                                    <li>✓ Nutrition Guidance</li>
                                    <li>✓ Progress Tracking</li>
                                    <li>✓ Guest Pass</li>
                                </ul>
                                <a href="login.php" class="button">JOIN PRO</a>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="plan-card">
                                <h3 class="plan-name">Pro+</h3>
                                <div class="plan-price">€29<span style="font-size: 0.6em;">/month</span></div>
                                <ul class="plan-features">
                                    <li>✓ All Pro Features</li>
                                    <li>✓ Personal Trainer (4 sessions)</li>
                                    <li>✓ Advanced Nutrition Plan</li>
                                    <li>✓ Priority Class Booking</li>
                                    <li>✓ Sauna Access</li>
                                </ul>
                                <a href="login.php" class="button">GO PRO+</a>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="plan-card">
                                <h3 class="plan-name">Elite</h3>
                                <div class="plan-price">€49<span style="font-size: 0.6em;">/month</span></div>
                                <ul class="plan-features">
                                    <li>✓ Personal Trainer (Unlimited)</li>
                                    <li>✓ Premium Meal Plans</li>
                                    <li>✓ Priority Booking</li>
                                    <li>✓ Premium Support</li>
                                    <li>✓ VIP Lounge</li>
                                </ul>
                                <a href="login.php" class="button">GO ELITE</a>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-nav-btn next" onclick="scrollCarousel('plans-carousel', 1)">❯</button>
                </div>
            </div>
        </section>

        <!-- LOGIN SECTION (RIGHT AFTER PLANS) -->
        <section class="teaser-section bg-dark">
            <div class="container split-layout align-center">
                <div class="login-teaser-cards">
                    <a href="login.php" class="card action-card">
                        <h3>CREATE ACCOUNT</h3>
                        <div class="arrow-circle">➔</div>
                    </a>
                    <a href="login.php" class="card action-card outline-card">
                        <h3>LOG IN NOW</h3>
                        <div class="arrow-circle">➔</div>
                    </a>
                </div>
                <div class="teaser-text text-right">
                    <h2>READY TO TRANSFORM?</h2>
                    <p class="subtitle text-white">Join thousands of members who have already started their fitness journey with us today!</p>
                </div>
            </div>
        </section>

        <!-- NEWS SECTION -->
        <section id="news" class="teaser-section bg-dark">
            <div class="container center-layout">
                <h2>LATEST NEWS & UPDATES</h2>
                <p class="subtitle large-subtitle">Stay informed about gym events, new classes, and achievements from our community</p>
                
                <div class="cards-grid cards-grid-3">
                    <div class="news-card">
                        <img src="../img/girl training.jpg" alt="New Class" class="news-image">
                        <div class="news-content">
                            <h3 class="news-title">New HIIT Class Launched!</h3>
                            <p class="news-excerpt">Get ready to buzz with intensity! Our brand new high-intensity interval training class is here to transform your fitness routine.</p>
                        </div>
                    </div>
                    
                    <div class="news-card">
                        <img src="../img/man swimming.jpg" alt="Swimming" class="news-image">
                        <div class="news-content">
                            <h3 class="news-title">Pool Opening Celebration</h3>
                            <p class="news-excerpt">Join us for the grand opening of our Olympic-sized swimming pool with free classes for all members this weekend!</p>
                        </div>
                    </div>
                    
                    <div class="news-card">
                        <img src="../img/gym enviorment.jpg" alt="Gym" class="news-image">
                        <div class="news-content">
                            <h3 class="news-title">Facility Expansion Complete</h3>
                            <p class="news-excerpt">We've expanded our gym with 50 new machines and a state-of-the-art recovery area for all our hardworking members!</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PHILOSOPHY SECTION -->
        <section id="philosophy" class="teaser-section bg-hero">
            <div class="container center-layout">
                <h2>OUR 3-STEP PHILOSOPHY</h2>
                <p class="subtitle">Leave your old shell behind and transform with us</p>
                
                <div class="metamorphosis-grid">
                    <div class="step">
                        <div class="icon"><img src="../img/caterpillar.png" alt="Caterpillar"></div>
                        <h4>Top-Notch Equipment</h4>
                        <p>We source only the highest grade, heavy-duty machines. Whether you're lifting heavy iron or focusing on cardio, our floor is built for peak performance.</p>
                    </div>
                    <div class="step">
                        <div class="icon"><img src="../img/cocoon.png" alt="Cocoon"></div>
                        <h4>Worth Your Money</h4>
                        <p>Premium facilities shouldn't cost a fortune. Experience luxury locker rooms, expert staff, and pristine environments at unbeatable prices.</p>
                    </div>
                    <div class="step">
                        <div class="icon"><img src="../img/butterfly.png" alt="Butterfly"></div>
                        <h4>Guaranteed Results</h4>
                        <p>With our tailored tracking, professional trainers, and electric atmosphere, hitting your goals is an absolute certainty.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- EQUIPMENT SECTION (SCROLLABLE) -->
        <section id="equipment" class="teaser-section bg-equipment slanted-top slanted-bottom">
            <div class="container">
                <h2 class="text-white">PERFECT EQUIPMENT TO SPOT</h2>
                <p class="subtitle text-white">Over 150 state-of-the-art machines</p>
                
                <div class="carousel-wrapper" style="margin-top: 2em;">
                    <button class="carousel-nav-btn prev" onclick="scrollCarousel('equipment-carousel', -1)">❮</button>
                    <div class="carousel-container" id="equipment-carousel">
                        <div class="carousel-item">
                            <div class="equipment-card">
                                <img src="../img/leg press.jpg" alt="Leg Press" class="equipment-image">
                                <h3>Leg Press Machine</h3>
                                <p>Heavy-duty leg press for maximum strength building</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="equipment-card">
                                <img src="../img/cardio.jpg" alt="Cardio" class="equipment-image">
                                <h3>Cardio Treadmills</h3>
                                <p>State-of-the-art treadmills with built-in programs</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="equipment-card">
                                <img src="../img/dumbells.jpg" alt="Dumbbells" class="equipment-image">
                                <h3>Dumbbell Set</h3>
                                <p>Complete range from 5kg to 50kg dumbbells</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="equipment-card">
                                <img src="../img/dumbells 2.jpg" alt="Dumbbells 2" class="equipment-image">
                                <h3>Adjustable Dumbbells</h3>
                                <p>Space-saving adjustable dumbbell system</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="equipment-card">
                                <img src="../img/heavy weights.jpg" alt="Heavy Weights" class="equipment-image">
                                <h3>Barbell Rack</h3>
                                <p>Professional barbell rack with safety bars</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-nav-btn next" onclick="scrollCarousel('equipment-carousel', 1)">❯</button>
                </div>
                
                <a href="#" class="button" style="margin-top: 2em;">EXPLORE ALL MACHINES</a>
            </div>
        </section>

        <!-- CLASSES SECTION (SCROLLABLE) -->
        <section id="classes" class="teaser-section bg-classes">
            <div class="container">
                <h2 class="text-white">FLY HIGH WITH OUR CLASSES</h2>
                <p class="subtitle text-white">Over 67 classes per week</p>
                
                <div class="carousel-wrapper" style="margin-top: 2em;">
                    <button class="carousel-nav-btn prev" onclick="scrollCarousel('classes-carousel', -1)">❮</button>
                    <div class="carousel-container" id="classes-carousel">
                        <div class="carousel-item">
                            <div class="class-card">
                                <img src="../img/yoga.jpg" alt="Yoga" class="class-image">
                                <h3>Yoga</h3>
                                <p>Peaceful and grounded stretching sessions</p>
                                <p class="class-meta">Mon, Wed, Fri - 10:00 AM</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="class-card">
                                <img src="../img/yoga 2.jpg" alt="Yoga Advanced" class="class-image">
                                <h3>Advanced Yoga</h3>
                                <p>Intense yoga for advanced practitioners</p>
                                <p class="class-meta">Tue, Thu - 6:00 PM</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="class-card">
                                <img src="../img/box ring.jpg" alt="Boxing" class="class-image">
                                <h3>Boxing</h3>
                                <p>High-energy boxing training sessions</p>
                                <p class="class-meta">Mon, Wed, Sat - 7:00 PM</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="class-card">
                                <img src="../img/swim.jpg" alt="Swimming" class="class-image">
                                <h3>Swimming</h3>
                                <p>Learn and improve your swimming skills</p>
                                <p class="class-meta">Tue, Thu, Sat - 3:00 PM</p>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <div class="class-card">
                                <img src="../img/man swimming.jpg" alt="Aqua Fitness" class="class-image">
                                <h3>Aqua Fitness</h3>
                                <p>Low-impact cardio in the water</p>
                                <p class="class-meta">Daily - 11:00 AM & 5:00 PM</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-nav-btn next" onclick="scrollCarousel('classes-carousel', 1)">❯</button>
                </div>
                
                <a href="schedule.php" class="button" style="margin-top: 2em;">SEE FULL SCHEDULE</a>
            </div>
        </section>
        <!-- TRAINERS SECTION WITH CAROUSEL -->
        <section id="trainers" class="teaser-section bg-trainers">
            <div class="container">
                <h2 class="text-white">MEET OUR EXPERT TRAINERS</h2>
                <p class="subtitle text-white">Certified professionals dedicated to your success</p>
                
                <div class="carousel-wrapper" style="margin-top: 3em;">
                    <button class="carousel-nav-btn prev" onclick="scrollCarousel('trainers-carousel', -1)">❮</button>
                    <div class="carousel-container" id="trainers-carousel">
                        <div class="carousel-item">
                            <div class="team-member">
                                <img src="../img/John_Doe.png" alt="John Doe" class="team-member-image">
                                <div class="team-member-info">
                                    <h3 class="team-member-name">John Doe</h3>
                                    <p class="team-member-role">Strength & Conditioning</p>
                                    <p class="team-member-description">10+ years of experience helping athletes reach peak performance</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="team-member">
                                <img src="../img/female staff-trainer.jpg" alt="Sarah Coach" class="team-member-image">
                                <div class="team-member-info">
                                    <h3 class="team-member-name">Sarah Coach</h3>
                                    <p class="team-member-role">HIIT & Cardio</p>
                                    <p class="team-member-description">Energetic trainer specializing in high-intensity workouts</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="team-member">
                                <img src="../img/Maria_Silva.png" alt="Maria Silva" class="team-member-image">
                                <div class="team-member-info">
                                    <h3 class="team-member-name">Maria Silva</h3>
                                    <p class="team-member-role">Yoga & Flexibility</p>
                                    <p class="team-member-description">Certified yoga instructor bringing balance and mindfulness</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="team-member">
                                <img src="../img/man training.jpg" alt="Marcus Strong" class="team-member-image">
                                <div class="team-member-info">
                                    <h3 class="team-member-name">Marcus Strong</h3>
                                    <p class="team-member-role">Powerlifting</p>
                                    <p class="team-member-description">Former athlete now guiding next generation of lifters</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-nav-btn next" onclick="scrollCarousel('trainers-carousel', 1)">❯</button>
                </div>
                
                <a href="#" class="button" style="margin-top: 2em;">BOOK A SESSION</a>
            </div>
        </section>

        <!-- NUTRITION SECTION WITH CAROUSEL -->
        <section id="nutrition" class="teaser-section bg-nutrition">
            <div class="container">
                <h2 class="text-white">SPOT-ON NUTRITION PLANS</h2>
                <p class="subtitle text-white">Tailored meal plans for muscle gain or weight loss</p>
                
                <div class="carousel-wrapper" style="margin-top: 3em;">
                    <button class="carousel-nav-btn prev" onclick="scrollCarousel('nutrition-carousel', -1)">❮</button>
                    <div class="carousel-container" id="nutrition-carousel">
                        <div class="carousel-item">
                            <div class="nutrition-card">
                                <img src="../img/protein bowl.jpg" alt="Protein Power Bowl" class="nutrition-image">
                                <span class="nutrition-badge">420 kcal</span>
                                <h3 class="nutrition-name">Protein Power Bowl</h3>
                                <p class="nutrition-description">Grilled chicken, quinoa, roasted vegetables with olive oil dressing. Perfect for muscle building.</p>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="nutrition-card">
                                <img src="../img/green salad.jpg" alt="Green Energy Salad" class="nutrition-image">
                                <span class="nutrition-badge">380 kcal</span>
                                <h3 class="nutrition-name">Green Energy Salad</h3>
                                <p class="nutrition-description">Mixed greens, turkey, avocado, nuts. High protein, low carb. Ideal for weight loss.</p>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="nutrition-card">
                                <img src="../img/stew.jpg" alt="Recovery Stew" class="nutrition-image">
                                <span class="nutrition-badge">450 kcal</span>
                                <h3 class="nutrition-name">Recovery Stew</h3>
                                <p class="nutrition-description">Lean beef, sweet potato, broccoli in nutrient-rich broth. Post-workout nutrition.</p>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="nutrition-card">
                                <img src="../img/omega3.jpg" alt="Omega-3 Delight" class="nutrition-image">
                                <span class="nutrition-badge">350 kcal</span>
                                <h3 class="nutrition-name">🐟 Omega-3 Delight</h3>
                                <p class="nutrition-description">Salmon, wild rice, asparagus. Heart-healthy and muscle-supporting.</p>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="nutrition-card">
                                <img src="../img/breakfast.png" alt="Morning Fuel" class="nutrition-image">
                                <span class="nutrition-badge">400 kcal</span>
                                <h3 class="nutrition-name">🥚 Morning Fuel</h3>
                                <p class="nutrition-description">Egg white omelet with mushrooms, oatmeal, berries. Energy for your day.</p>
                            </div>
                        </div>
                        
                        <div class="carousel-item">
                            <div class="nutrition-card">
                                <img src="../img/protein smoothie.jpg" alt="Recovery Smoothie" class="nutrition-image">
                                <span class="nutrition-badge">300 kcal</span>
                                <h3 class="nutrition-name">🍓 Recovery Smoothie</h3>
                                <p class="nutrition-description">Protein powder, banana, berries, Greek yogurt. Quick post-workout recovery.</p>
                            </div>
                        </div>
                    </div>
                    <button class="carousel-nav-btn next" onclick="scrollCarousel('nutrition-carousel', 1)">❯</button>
                </div>
                
                <a href="#" class="button" style="margin-top: 2em;">GET CUSTOM PLAN</a>
            </div>
        </section>

        <!-- FEEDBACK SECTION -->
        <section id="feedback" class="teaser-section bg-news">
            <div class="container">
                <h2>WHAT OUR MEMBERS SAY</h2>
                <p class="subtitle large-subtitle">Real testimonials from people just like you who transformed with Ladybug's Gym</p>
                
                <div class="cards-grid cards-grid-3">
                    <div class="feedback-card">
                        <p class="feedback-quote">"The trainers here are absolutely incredible! I lost 25 pounds in 3 months and feel amazing!"</p>
                        <p class="feedback-author">- Jessica M. ⭐⭐⭐⭐⭐</p>
                    </div>
                    
                    <div class="feedback-card">
                        <p class="feedback-quote">"Best gym I've ever been to. The facilities are top-notch and the community is so supportive."</p>
                        <p class="feedback-author">- Tom Richardson ⭐⭐⭐⭐⭐</p>
                    </div>
                    
                    <div class="feedback-card">
                        <p class="feedback-quote">"Affordable pricing with premium quality? I couldn't believe it until I tried it myself!"</p>
                        <p class="feedback-author">- Alex Chen ⭐⭐⭐⭐⭐</p>
                    </div>
                    
                    <div class="feedback-card">
                        <p class="feedback-quote">"The nutrition plans actually made sense and tasted good. I'm hooked!"</p>
                        <p class="feedback-author">- Maria Santos ⭐⭐⭐⭐⭐</p>
                    </div>
                    
                    <div class="feedback-card">
                        <p class="feedback-quote">"Personal training sessions have changed my life. Can't recommend enough!"</p>
                        <p class="feedback-author">- David Smith ⭐⭐⭐⭐⭐</p>
                    </div>
                    
                    <div class="feedback-card">
                        <p class="feedback-quote">"The yoga classes helped me recover from an injury. Amazing experience!"</p>
                        <p class="feedback-author">- Sophie Laurent ⭐⭐⭐⭐⭐</p>
                    </div>
                </div>
                
                <a href="#" class="button" style="margin-top: 2em;">LEAVE YOUR FEEDBACK</a>
            </div>
        </section>

        <!-- Q&A SECTION -->
        <section id="qa" class="teaser-section bg-hero">
            <div class="container">
                <h2>FREQUENTLY ASKED QUESTIONS</h2>
                <p class="subtitle large-subtitle">Got questions? We've got answers!</p>
                
                <div class="cards-grid cards-grid-2">
                    <div class="qa-card-item">
                        <p class="qa-question">Q: Do I need a membership to use the gym?</p>
                        <p class="qa-answer">A: Yes! We offer flexible membership plans starting from just €9/month. Choose what works best for you.</p>
                    </div>
                    
                    <div class="qa-card-item">
                        <p class="qa-question">Q: Can I try the gym before committing?</p>
                        <p class="qa-answer">A: Absolutely! We offer a free 3-day trial pass. Come experience the Ladybug's Gym difference!</p>
                    </div>
                    
                    <div class="qa-card-item">
                        <p class="qa-question">Q: Are personal trainers included in the membership?</p>
                        <p class="qa-answer">A: Personal trainers are available with our Pro and Elite memberships. Our Elite plan includes unlimited sessions!</p>
                    </div>
                    
                    <div class="qa-card-item">
                        <p class="qa-question">Q: What are your opening hours?</p>
                        <p class="qa-answer">A: We're open 24/7 for members! Access your gym whenever it fits your schedule.</p>
                    </div>
                    
                    <div class="qa-card-item">
                        <p class="qa-question">Q: Do you offer nutrition coaching?</p>
                        <p class="qa-answer">A: Yes! Our nutritionists create personalized meal plans included in Pro and Elite memberships.</p>
                    </div>
                    
                    <div class="qa-card-item">
                        <p class="qa-question">Q: Can I bring a guest?</p>
                        <p class="qa-answer">A: Pro and Elite members get guest passes. Pro members get 2 per month, Elite gets unlimited!</p>
                    </div>
                </div>
                
                <a href="#" class="button" style="margin-top: 2em;">CONTACT US FOR MORE</a>
            </div>
        </section>

    </main>

    <script>
        function scrollCarousel(carouselId, direction) {
            const carousel = document.getElementById(carouselId);
            const itemWidth = carousel.querySelector('.carousel-item').offsetWidth + 32; // item width + gap
            
            if (direction === 1) {
                carousel.scrollLeft += itemWidth;
            } else {
                carousel.scrollLeft -= itemWidth;
            }
        }
    </script>
<?php drawFooter(); ?>
</body>
</html>