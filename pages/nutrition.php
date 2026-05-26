<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

drawHead("Nutrition Plans | Ladybug's Gym");
drawHeader();
?>
<!DOCTYPE html>
<html lang="en">
<body>
    <main class="container nutrition-page">
        <?php drawPageHeader('Nutrition Plans', 'Browse custom meal guidance and trainer-approved plans.'); ?>

        <section class="nutrition-search card">
            <div class="form-row">
                <div class="form-group">
                    <label for="nutritionSearchInput">Search plans</label>
                    <input id="nutritionSearchInput" type="text" class="input-field" placeholder="Search by goal, member or trainer">
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
                        <option value="Miguel Ferreira">Miguel Ferreira</option>
                        <option value="Sofia Martins">Sofia Martins</option>
                    </select>
                </div>
            </div>
        </section>

        <section id="nutritionResults" class="cards-grid">
            <p class="empty-state">Loading nutrition plans...</p>
        </section>
    </main>
</body>
</html>
<?php
drawFooter();
?>
