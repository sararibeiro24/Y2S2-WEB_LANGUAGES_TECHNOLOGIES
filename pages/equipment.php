<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();

drawHead("Equipment Availability | Ladybug's Gym");
drawHeader();
?>
<!DOCTYPE html>
<html lang="en">
<body>
    <main class="container equipment-page">
        <?php drawPageHeader('Equipment Availability', 'Search and review what is free in the main gym area.'); ?>

        <section class="equipment-search card">
            <div class="form-row">
                <div class="form-group">
                    <label for="equipmentSearchInput">Search equipment</label>
                    <input id="equipmentSearchInput" type="text" class="input-field" placeholder="e.g. Treadmill, Dumbbells">
                </div>
                <div class="form-group">
                    <label for="equipmentStatusFilter">Availability</label>
                    <select id="equipmentStatusFilter" class="input-field">
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>
        </section>

        <section id="equipmentResults" class="cards-grid">
            <p class="empty-state">Loading equipment availability...</p>
        </section>
    </main>
</body>
</html>
<?php
drawFooter();
?>
