<?php
declare(strict_types=1);

function drawTrainerCard(Trainer $trainer): void {
    $photo = $trainer->getProfilePhoto();
    $hasPhoto = $photo !== '' && file_exists(__DIR__ . '/../pages/../img/' . $photo);
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
?>
