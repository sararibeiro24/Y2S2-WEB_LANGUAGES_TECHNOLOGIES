<?php
declare(strict_types=1);

function drawTrainersPage(array $trainers): void {
    ?>
    <main class="container trainers-page">
        <div class="schedule-filters">
            <div class="filter-row" style="grid-template-columns: 1fr;">
                <div class="filter-group">
                    <label for="trainerSearch">Search trainers</label>
                    <input type="text" id="trainerSearch" class="input-field" placeholder="Search by name, specialization, or keyword...">
                </div>
            </div>
        </div>
        
        <div class="team-showcase" id="trainerGrid">
            <?php foreach ($trainers as $trainer): ?>
                <div onclick="openTrainerModal(<?= (int)$trainer->getId() ?>)">
                    <?php drawTrainerCard($trainer); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div class="modal-overlay" id="trainerModal">
        <div class="modal-content" id="trainerModalContent">
            <button class="modal-close" onclick="closeModal('trainerModal')">&times;</button>
            <div id="trainerModalBody">Loading...</div>
        </div>
    </div>
    <?php
}

function drawTrainerCard(Trainer $trainer): void {
    $photo = $trainer->getProfilePhoto();
    $hasPhoto = $photo !== '' && file_exists(__DIR__ . '/../img/' . $photo);
    $initials = '';
    
    if (!$hasPhoto) {
        $parts = explode(' ', $trainer->getName());
        $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
    }
    
    $specializations = $trainer->getSpecializationsList();
    $firstSpec = !empty($specializations) ? $specializations[0] : 'Fitness';
    ?>
    <div class="team-member">
        <?php if ($hasPhoto): ?>
            <img src="../img/<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($trainer->getName()) ?>" class="team-member-image">
        <?php else: ?>
            <div class="team-member-image trainer-placeholder"><?= htmlspecialchars($initials) ?></div>
        <?php endif; ?>
        
        <div class="team-member-info">
            <h3 class="team-member-name"><?= htmlspecialchars($trainer->getName()) ?></h3>
            <p class="team-member-role"><?= htmlspecialchars($firstSpec) ?></p>
            
            <?php if ($trainer->getBio() !== null): ?>
                <p class="team-member-description"><?= htmlspecialchars($trainer->getBio()) ?></p>
            <?php endif; ?>
            
            <?php if (count($specializations) > 1): ?>
                <div class="trainer-specs">
                    <?php foreach ($specializations as $spec): ?>
                        <span class="trainer-spec-tag"><?= htmlspecialchars($spec) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}