<?php


function drawPlansPage(array  $plans, bool   $isLoggedIn, ?int   $currentPlanId, string $csrfToken, string $queryTerm   = '',string $cycleFilter = ''): void {
?>
<main class="container plans-page">
    <?php drawPageHeader('Membership Plans', 'Pick the plan that matches your goals.'); ?>

    <?php drawPlansFilter($queryTerm, $cycleFilter); ?>

    <section class="plans-grid">
        <?php if (!$plans): ?>
            <p class="empty-state">No membership plans available yet. Check back later.</p>
        <?php endif; ?>

        <?php foreach ($plans as $plan): ?>
            <?php drawPlanCard($plan, $isLoggedIn, $currentPlanId, $csrfToken); ?>
        <?php endforeach; ?>
    </section>
</main>
<?php
}


function drawPlansFilter(string $queryTerm, string $cycleFilter): void {
?>
<section class="filter-panel card">
    <form method="GET" action="plans.php" class="plans-filter-form">
        <div class="form-row">
            <div class="form-group">
                <label for="planSearch">Search plans</label>
                <input
                    id="planSearch"
                    name="query"
                    class="input-field"
                    type="text"
                    placeholder="Search by name or feature"
                    value="<?php echo htmlspecialchars($queryTerm); ?>">
            </div>

            <div class="form-group">
                <label for="billingCycle">Billing cycle</label>
                <select id="billingCycle" name="billing_cycle" class="input-field">
                    <option value="">All cycles</option>
                    <option value="weekly"  <?php echo $cycleFilter === 'weekly'  ? 'selected' : ''; ?>>Weekly</option>
                    <option value="monthly" <?php echo $cycleFilter === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                    <option value="yearly"  <?php echo $cycleFilter === 'yearly'  ? 'selected' : ''; ?>>Yearly</option>
                </select>
            </div>

            <div class="form-group form-actions">
                <button type="submit" class="button button-full">Filter Plans</button>
            </div>
        </div>
    </form>
</section>
<?php
}


function drawPlanCard( array  $plan,bool   $isLoggedIn,?int   $currentPlanId,string $csrfToken): void {
    $features  = array_filter(array_map('trim', explode(',', $plan['features'])));
    $isCurrent = $currentPlanId !== null && (int)$plan['id'] === $currentPlanId;
?>
<article class="card plan-card <?php echo $isCurrent ? 'current-plan' : ''; ?>">
    <div class="plan-card-header">
        <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
        <span class="badge badge-green">
            <?php echo htmlspecialchars(ucfirst($plan['billing_cycle'])); ?>
        </span>
    </div>

    <p class="plan-price">
        €<?php echo number_format((float)$plan['price'], 2); ?>
        / <?php echo htmlspecialchars($plan['billing_cycle']); ?>
    </p>

    <ul class="feature-list">
        <?php foreach ($features as $feature): ?>
            <li><?php echo htmlspecialchars($feature); ?></li>
        <?php endforeach; ?>
    </ul>

    <?php if ($isLoggedIn): ?>
        <?php drawUpgradePlanForm((int)$plan['id'], $isCurrent, $csrfToken); ?>
    <?php else: ?>
        <p class="card-copy">Please <a href="login.php">log in</a> to select a plan.</p>
    <?php endif; ?>
</article>
<?php
}

function drawUpgradePlanForm(int $planId, bool $isCurrent, string $csrfToken): void {
?>
<form action="../actions/action_upgrade_plan.php" method="POST">
    <input type="hidden" name="plan_id"    value="<?php echo $planId; ?>">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
    <button type="submit" class="button button-full" <?php echo $isCurrent ? 'disabled' : ''; ?>>
        <?php echo $isCurrent ? 'Current Plan' : 'Choose this plan'; ?>
    </button>
</form>
<?php
}
function drawPlansTeaser(array $plans): void {
?>
<section id="plans" class="teaser-section bg-plans slanted-bottom">
    <div class="container">
        <h2 class="huge-text text-white">BECOME UN-BEETLE-ABLE TODAY!</h2>
        <p class="subtitle text-white">Your dream body starts here. No excuses, no hidden fees — just results.</p>

        <div class="carousel-wrapper" style="margin-top: 3em;">
            <button class="carousel-nav-btn prev" onclick="scrollCarousel('plans-carousel', -1)">❮</button>
            <div class="carousel-container" id="plans-carousel">
                <?php foreach ($plans as $plan): ?>
                    <?php drawPlanTeaserCard($plan); ?>
                <?php endforeach; ?>
            </div>
            <button class="carousel-nav-btn next" onclick="scrollCarousel('plans-carousel', 1)">❯</button>
        </div>
    </div>
</section>
<?php
}

function drawPlanTeaserCard(array $plan): void {
    $features = array_filter(array_map('trim', explode(',', $plan['features'])));
?>
<div class="carousel-item">
    <div class="plan-card">
        <h3 class="plan-name"><?php echo htmlspecialchars($plan['name']); ?></h3>
        <div class="plan-price">
            €<?php echo number_format((float)$plan['price'], 2); ?>
            <span style="font-size: 0.6em;">/ <?php echo htmlspecialchars($plan['billing_cycle']); ?></span>
        </div>
        <ul class="plan-features">
            <?php foreach ($features as $feature): ?>
                <li>✓ <?php echo htmlspecialchars($feature); ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="plans.php" class="button">GET STARTED</a>
    </div>
</div>
<?php
}
?>