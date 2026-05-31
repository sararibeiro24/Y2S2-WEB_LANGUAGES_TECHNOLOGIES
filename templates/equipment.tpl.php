<?php
function drawEquipmentHomepage(){
?>
    <section id="equipment" class="teaser-section bg-equipment slanted-top slanted-bottom">
    <div class="container">
        <h2 class="text-white">PERFECT EQUIPMENT TO SPOT</h2>
        <p class="subtitle text-white">Everything you need. Nothing you don't. Train like a champion.</p>

        <div class="carousel-wrapper" style="margin-top: 2em;">
            <button class="carousel-nav-btn prev" onclick="scrollCarousel('equipment-carousel', -1)">❮</button>
            <div class="carousel-container" id="equipment-carousel">
                <div class="carousel-item">
                    <div class="equipment-card">
                        <img src="../img/leg_press.jpg" alt="Leg Press" class="equipment-image">
                        <h3>Leg Press Machine</h3>
                        <p>Heavy-duty leg press for maximum strength building.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="equipment-card">
                        <img src="../img/cardio.jpg" alt="Cardio" class="equipment-image">
                        <h3>Cardio Treadmills</h3>
                        <p>State-of-the-art treadmills with built-in programs.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="equipment-card">
                        <img src="../img/dumbells.jpg" alt="Dumbbells" class="equipment-image">
                        <h3>Dumbbell Set</h3>
                        <p>Complete range from 5kg to 50kg dumbbells.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="equipment-card">
                        <img src="../img/dumbells2.jpg" alt="Dumbbells 2" class="equipment-image">
                        <h3>Adjustable Dumbbells</h3>
                        <p>Space-saving adjustable dumbbell system.</p>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="equipment-card">
                        <img src="../img/heavy_weights.jpg" alt="Heavy Weights" class="equipment-image">
                        <h3>Barbell Rack</h3>
                        <p>Professional barbell rack with safety bars.</p>
                    </div>
                </div>
            </div>
            <button class="carousel-nav-btn next" onclick="scrollCarousel('equipment-carousel', 1)">❯</button>
        </div>

        <a href="equipment.php" class="button" style="margin-top: 2em;">EXPLORE ALL MACHINES</a>
    </div>
</section>
<?php
}

function drawEquipmentPage(){
?>
 <main class="container equipment-page">
        <?php drawPageHeader('Equipment Availability', 'Search and review what is free in the main gym area.'); ?>

        <div class="schedule-filters">
            <h3 class="sr-only">Filter &amp; Search</h3>
            <div class="filter-row" style="grid-template-columns: 1fr 1fr;">
                <div class="filter-group">
                    <label for="equipmentSearchInput">Search equipment</label>
                    <input id="equipmentSearchInput" type="text" class="input-field" placeholder="e.g. Treadmill, Dumbbells">
                </div>
                <div class="filter-group">
                    <label for="equipmentStatusFilter">Availability</label>
                    <select id="equipmentStatusFilter" class="input-field">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>
        </div>

        <section id="equipmentResults" class="cards-grid">
            <h3 id="resultsTitle" class="visually-hidden">Available Equipment</h3>
            <p class="empty-state">Loading equipment availability...</p>
        </section>
    </main>

<?php
}
