<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/trainers.tpl.php');
require_once(__DIR__ . '/../database/trainer.class.php');

$trainers = Trainer::getAllTrainers();

drawHead("Our Trainers | Ladybug's Gym");
drawHeader();
drawPageHeader('MEET OUR TRAINERS', 'Certified professionals dedicated to your success.');
?>
<main class="container trainers-page">
    <div class="trainer-search">
        <input type="text" id="trainerSearch" class="input-field" placeholder="Search by name, specialization, or keyword..." style="width: 100%; max-width: 500px; display: block; margin: 0 auto 2em;">
    </div>
    <div class="team-showcase" id="trainerGrid">
        <?php foreach ($trainers as $trainer): ?>
            <div onclick="openTrainerModal(<?= $trainer->getId() ?>)">
                <?php drawTrainerCard($trainer); ?>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Trainer Detail Modal -->
<div class="modal-overlay" id="trainerModal">
    <div class="modal-content" id="trainerModalContent">
        <button class="modal-close" onclick="closeModal('trainerModal')">&times;</button>
        <div id="trainerModalBody">Loading...</div>
    </div>
</div>

<script src="../javascript/trainer_filter.js" defer></script>
<?php
drawFooter();
?>
