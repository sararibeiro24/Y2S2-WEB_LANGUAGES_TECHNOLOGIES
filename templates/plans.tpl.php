<?php

function drawPlansPage(array $plans, bool $isLoggedIn, ?int $currentPlanId, string $csrfToken): void {
?>
<main class="container plans-page">
    <?php drawPageHeader('Membership Plans', 'Pick the plan that matches your goals.'); ?>

    <?php drawPlansFilter(); ?>

    <section class="plans-grid" id="plansGrid">
        <h2 class="sr-only">Available Plans</h2>
        <?php if (!$plans): ?>
            <p class="empty-state">No membership plans available yet. Check back later.</p>
        <?php endif; ?>

        <?php foreach ($plans as $plan): ?>
            <?php drawPlanCard($plan, $isLoggedIn, $currentPlanId, $csrfToken); ?>
        <?php endforeach; ?>
    </section>
</main>

<script>
let allPlanHTML = [];

function initPlanFilter() {
    const grid = document.getElementById('plansGrid');
    const cards = grid.querySelectorAll('.plan-card');
    let debounceTimer;
    cards.forEach(c => allPlanHTML.push(c.outerHTML));
    document.getElementById('planSearch').addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(filterPlans, 300);
    });
    document.getElementById('billingCycle').addEventListener('change', filterPlans);
}

function filterPlans() {
    const query = document.getElementById('planSearch').value.toLowerCase().trim();
    const cycle = document.getElementById('billingCycle').value.toLowerCase();
    const grid = document.getElementById('plansGrid');
    let html = '';

    allPlanHTML.forEach(raw => {
        const div = document.createElement('div');
        div.innerHTML = raw;
        const card = div.firstElementChild;

        const name = card.querySelector('h3').textContent.toLowerCase();
        const features = [...card.querySelectorAll('.feature-list li')].map(l => l.textContent.toLowerCase()).join(' ');
        const badge = card.querySelector('.badge-plan-cycle').textContent.toLowerCase().trim();

        const matchQuery = !query || name.includes(query) || features.includes(query);
        const matchCycle = !cycle || badge === cycle;

        if (matchQuery && matchCycle) {
            html += raw;
        }
    });

    if (html) {
        grid.innerHTML = html;
    } else {
        grid.innerHTML = '<p class="plan-filter-empty">No plans match your filter.</p>';
    }
}

function clearPlanFilters() {
    document.getElementById('planSearch').value = '';
    document.getElementById('billingCycle').value = '';
    filterPlans();
}

document.addEventListener('DOMContentLoaded', initPlanFilter);
</script>
<?php
}


function drawPlansFilter(): void {
?>
<div class="schedule-filters">
    <h2 class="sr-only">Filter Plans</h2>
    <div class="filter-row" style="grid-template-columns: 1fr 1fr auto;">
        <div class="filter-group">
            <label for="planSearch">Search plans</label>
            <input id="planSearch" class="input-field" type="text"
                   placeholder="Search by name or feature">
        </div>

        <div class="filter-group">
            <label for="billingCycle">Billing cycle</label>
            <select id="billingCycle" class="input-field">
                <option value="">All cycles</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>

        <div class="filter-group filter-actions">
            <label>&nbsp;</label>
            <button type="button" class="button" onclick="clearPlanFilters()"
                    style="white-space: nowrap;">Clear</button>
        </div>
    </div>
</div>
<?php
}


function drawPlanCard( array  $plan,bool   $isLoggedIn,?int   $currentPlanId,string $csrfToken): void {
    $features  = array_filter(array_map('trim', explode(',', $plan['features'])));
    $isCurrent = $currentPlanId !== null && (int)$plan['id'] === $currentPlanId;
    $tier      = strtolower(explode(' ', $plan['name'])[0]);

    $badge = '';
    if ($tier === 'premium')  $badge = 'Most Popular';
    if ($tier === 'elite')    $badge = '⚡ Elite';
    if ($tier === 'annual')   $badge = 'Best Value';
?>
<article class="card plan-card plan-tier-<?= $tier ?> <?php echo $isCurrent ? 'current-plan' : ''; ?>">
    <?php if ($badge): ?>
        <span class="plan-badge"><?= $badge ?></span>
    <?php endif; ?>
    <div class="plan-card-header">
        <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
        <span class="badge badge-plan-cycle">
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
    $tier = strtolower(explode(' ', $plan['name'])[0]);

    $badge = '';
    if ($tier === 'premium')  $badge = 'Most Popular';
    if ($tier === 'elite')    $badge = '⚡ Elite';
    if ($tier === 'annual')   $badge = 'Best Value';
?>
<div class="carousel-item">
    <div class="plan-card plan-tier-<?= $tier ?>">
        <?php if ($badge): ?>
            <span class="plan-badge"><?= $badge ?></span>
        <?php endif; ?>
        <div class="plan-card-header">
            <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
            <span class="badge badge-plan-cycle">
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
        <a href="plans.php" class="button">GET STARTED</a>
    </div>
</div>
<?php
}
?>