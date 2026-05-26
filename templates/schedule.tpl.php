<?php

function drawClassCard(
    $scheduleId,
    $classTime,
    $status,
    $title,
    $trainer,
    $duration,
    $spots,
    $capacity,
    $imageIcon = 'relogio.png',
    $usersIcon = 'follower.png',
    $buttonText = 'Enroll Now'
) {

    $badgeClass = 'badge-green';
    $isDisabled = false;

    if ($status === 'Full') {
        $badgeClass = 'badge-red';
        $isDisabled = true;
    }
    elseif ($status === 'Few Spots') {
        $badgeClass = 'badge-yellow';
    }

?>

<article class="card class-card">

    <div class="class-time">
        <?php echo htmlspecialchars($classTime); ?>
    </div>

    <span class="badge <?php echo $badgeClass; ?>">
        <?php echo htmlspecialchars($status); ?>
    </span>

    <h3 class="class-title">
        <?php echo htmlspecialchars($title); ?>
    </h3>

    <p class="class-trainer">
        with <?php echo htmlspecialchars($trainer); ?>
    </p>

    <div class="class-meta">

        <span>
            <img
                src="../img/<?php echo htmlspecialchars($imageIcon); ?>"
                alt="Clock icon"
            >

            <?php echo htmlspecialchars($duration); ?> min
        </span>

        <span>
            <img
                src="../img/<?php echo htmlspecialchars($usersIcon); ?>"
                alt="Users icon"
            >

            <?php echo htmlspecialchars($spots); ?>
            /
            <?php echo htmlspecialchars($capacity); ?>

            spots
        </span>

    </div>

    <?php if ($isDisabled): ?>

        <button class="button button-small" disabled>
            Full
        </button>

    <?php else: ?>

        <form action="../actions/action_enroll.php" method="post">

            <input
                type="hidden"
                name="schedule_id"
                value="<?php echo $scheduleId; ?>"
            >

            <button class="button button-small button-outline">
                <?php echo htmlspecialchars($buttonText); ?>
            </button>

        </form>

    <?php endif; ?>

</article>

<?php
}
?>

<!--<?php
/*function drawClassCard($classTime, $status, $title, $trainer, $duration, $spots, $imageIcon = 'relogio.png', $usersIcon = 'follower.png', $buttonText = 'Enroll Now') {
    $badgeClass = 'badge-green';
    $isDisabled = false;
    
    if ($status === 'Full') {
        $badgeClass = 'badge-red';
        $isDisabled = true;
    } elseif ($status === 'Few Spots') {
        $badgeClass = 'badge-yellow';
    }
    ?>
    <article class="card class-card">
        <div class="class-time"><?php echo htmlspecialchars($classTime); ?></div>
        <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($status); ?></span>
        <h3 class="class-title"><?php echo htmlspecialchars($title); ?></h3>
        <p class="class-trainer">with <?php echo htmlspecialchars($trainer); ?></p>
        <div class="class-meta">
            <span><img src="../img/<?php echo htmlspecialchars($imageIcon); ?>" alt="Clock icon" loading="lazy"><?php echo htmlspecialchars($duration); ?> min</span>
            <span><img src="../img/<?php echo htmlspecialchars($usersIcon); ?>" alt="Users icon" loading="lazy"><?php echo htmlspecialchars($spots); ?> spots</span>
        </div>
        <?php if ($isDisabled): ?>
            <button class="button button-small" disabled><?php echo htmlspecialchars($buttonText); ?></button>
        <?php else: ?>
            <a href="#" class="button button-small button-outline"><?php echo htmlspecialchars($buttonText); ?></a>
        <?php endif; ?>
    </article>
    <?php
} -->*/