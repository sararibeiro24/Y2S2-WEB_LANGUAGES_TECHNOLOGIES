<?php
declare(strict_types=1);

function drawNutritionPage(array $trainers): void {
?>
<main class="container nutrition-page">
    <?php drawPageHeader('Nutrition Plans', 'Browse custom meal guidance and trainer-approved plans.'); ?>

    <section class="nutrition-search card">
        <div class="form-row">
            <div class="form-group">
                <label for="nutritionSearchInput">Search plans</label>
                <input id="nutritionSearchInput" type="text" class="input-field"
                       placeholder="Search by goal, member or trainer">
            </div>
            <div class="form-group">
                <label for="nutritionGoalFilter">Goal</label>
                <select id="nutritionGoalFilter" class="input-field">
                    <option value="">All goals</option>
                    <option value="Weight Loss">Weight Loss</option>
                    <option value="Muscle Gain">Muscle Gain</option>
                    <option value="Maintenance">Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label for="nutritionTrainerFilter">Trainer</label>
                <select id="nutritionTrainerFilter" class="input-field">
                    <option value="">All trainers</option>
                    <?php foreach ($trainers as $trainer): ?>
                        <option value="<?php echo htmlspecialchars((string)$trainer['id']); ?>">
                            <?php echo htmlspecialchars($trainer['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group action-group">
                <label class="hidden-label">&nbsp;</label>
                <button type="button" id="openRequestModalBtn" class="button">
                    Get Custom Plan
                </button>
            </div>
        </div>
    </section>

    <div id="requestPlanModal" class="modal-overlay">
        <div class="modal-content card">
            <span id="closeModalBtn" class="modal-close" >&times;</span>
            <h2>Request Custom Plan</h2>
            <p>Select your objective, and a trainer will create a tailored meal plan for you.</p>
            
            <form id="requestPlanForm" method="POST" action="../actions/action_request_nutrition.php">
                <div class="form-group">
                    <label for="modalGoalSelect">What is your goal?</label>
                    <select id="modalGoalSelect" name="goal" class="input-field" required>
                        <option value="" disabled selected>Choose a fitness goal...</option>
                        <option value="Weight Loss">Weight Loss</option>
                        <option value="Muscle Gain">Muscle Gain</option>
                        <option value="Maintenance">Maintenance</option>
                    </select>
                </div>
                <button type="submit" class="button">
                    Submit Request
                </button>
            </form>
        </div>
    </div>

    <section id="nutritionResults" class="cards-grid">
        <p class="empty-state">Loading nutrition plans...</p>
    </section>
</main>
<?php
}

function drawNutritionHomepage(): void{
?>
<section id="nutrition" class="teaser-section bg-nutrition">
    <div class="container">
        <h2 class="text-white">SPOT-ON NUTRITION PLANS</h2>
        <p class="subtitle text-white">Eat smart. Perform better. Fuel that matches your fire.</p>

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
                        <h3 class="nutrition-name">Omega-3 Delight</h3>
                        <p class="nutrition-description">Salmon, wild rice, asparagus. Heart-healthy and muscle-supporting fuel.</p>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="nutrition-card">
                        <img src="../img/breakfast.png" alt="Morning Fuel" class="nutrition-image">
                        <span class="nutrition-badge">400 kcal</span>
                        <h3 class="nutrition-name">Morning Fuel</h3>
                        <p class="nutrition-description">Egg white omelet with mushrooms, oatmeal, berries. Energy for your day.</p>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="nutrition-card">
                        <img src="../img/protein smoothie.jpg" alt="Recovery Smoothie" class="nutrition-image">
                        <span class="nutrition-badge">300 kcal</span>
                        <h3 class="nutrition-name">Recovery Smoothie</h3>
                        <p class="nutrition-description">Protein powder, banana, berries, Greek yogurt. Quick post-workout recovery.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-nav-btn next" onclick="scrollCarousel('nutrition-carousel', 1)">❯</button>
        </div>

        <a href="nutrition.php" class="button nutrition">DISCOVER MORE</a>
    </div>
</section>
<?php
}
?>