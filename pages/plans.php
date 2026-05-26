<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../utils/session.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$userId = Session::getUserId();
$currentPlanId = null;

$db = getDatabaseConnection();
$requiredPlans = [
    'Starter Weekly' => [9.99, 'weekly', 'Gym floor access, 1 class per week, Basic support'],
    'Basic Monthly' => [19.99, 'monthly', 'Access to gym floor, 1 class per week, Standard support'],
    'Premium Weekly' => [24.99, 'weekly', 'Unlimited classes, Equipment reservations, Nutrition consultation'],
    'Premium Monthly' => [39.99, 'monthly', 'Unlimited classes, Equipment reservations, Nutrition consultation'],
    'Elite Weekly' => [34.99, 'weekly', 'Personal trainer booking, Nutrition plan, Priority support'],
    'Elite Monthly' => [59.99, 'monthly', 'Personal trainer booking, Nutrition plan, Priority support'],
    'Annual Pass' => [599.99, 'yearly', 'Unlimited access, premium support, guest passes, exclusive perks'],
];

$existingPlans = $db->query('SELECT name FROM plans')->fetchAll(PDO::FETCH_COLUMN);
$missingPlans = array_diff(array_keys($requiredPlans), $existingPlans);

if (!empty($missingPlans)) {
    $db->beginTransaction();
    $stmt = $db->prepare('INSERT OR IGNORE INTO plans (name, price, billing_cycle, features) VALUES (?, ?, ?, ?)');
    foreach ($missingPlans as $name) {
        [$price, $cycle, $features] = $requiredPlans[$name];
        $stmt->execute([$name, $price, $cycle, $features]);
    }
    $db->commit();
}

if ($isLoggedIn && $userId !== null) {
    $stmt = $db->prepare('SELECT plan_id FROM users WHERE id = ?');
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    $currentPlanId = $row['plan_id'] !== null ? (int)$row['plan_id'] : null;
}

$queryTerm = trim($_GET['query'] ?? '');
$cycleFilter = trim($_GET['billing_cycle'] ?? '');

$sql = 'SELECT id, name, price, billing_cycle, features FROM plans';
$params = [];
$clauses = [];

if ($queryTerm !== '') {
    $clauses[] = '(name LIKE ? OR features LIKE ?)';
    $params[] = "%{$queryTerm}%";
    $params[] = "%{$queryTerm}%";
}
if ($cycleFilter !== '') {
    $clauses[] = 'billing_cycle = ?';
    $params[] = $cycleFilter;
}
if ($clauses) {
    $sql .= ' WHERE ' . implode(' AND ', $clauses);
}

$stmt = $db->prepare($sql);
$stmt->execute($params);
$plans = $stmt->fetchAll();

drawHead("Membership Plans | Ladybug's Gym");
drawHeader();
?>

<!DOCTYPE html>
<html lang="en">
<body>
    <main class="container plans-page">
        <?php drawPageHeader('Membership Plans', 'Pick the plan that matches your goals.'); ?>

        <section class="filter-panel card">
            <form method="GET" action="plans.php" class="plans-filter-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="planSearch">Search plans</label>
                        <input id="planSearch" name="query" class="input-field" type="text" placeholder="Search by name or feature" value="<?php echo htmlspecialchars($queryTerm); ?>">
                    </div>
                    <div class="form-group">
                        <label for="billingCycle">Billing cycle</label>
                        <select id="billingCycle" name="billing_cycle" class="input-field">
                            <option value="">All cycles</option>
                            <option value="weekly" <?php echo $cycleFilter === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                            <option value="monthly" <?php echo $cycleFilter === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                            <option value="yearly" <?php echo $cycleFilter === 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                        </select>
                    </div>
                    <div class="form-group form-actions">
                        <button type="submit" class="button button-full">Filter Plans</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="plans-grid">
            <?php if (!$plans): ?>
                <p class="empty-state">No membership plans available yet. Check back later.</p>
            <?php endif; ?>

            <?php foreach ($plans as $plan): ?>
                <?php
                    $features = array_filter(array_map('trim', explode(',', $plan['features'])));
                    $isCurrent = $currentPlanId !== null && $plan['id'] === $currentPlanId;
                ?>
                <article class="card plan-card <?php echo $isCurrent ? 'current-plan' : ''; ?>">
                    <div class="plan-card-header">
                        <h3><?php echo htmlspecialchars($plan['name']); ?></h3>
                        <span class="badge badge-green"><?php echo htmlspecialchars(ucfirst($plan['billing_cycle'])); ?></span>
                    </div>
                    <p class="plan-price">€<?php echo number_format((float)$plan['price'], 2); ?> / <?php echo htmlspecialchars($plan['billing_cycle']); ?></p>
                    <ul class="feature-list">
                        <?php foreach ($features as $feature): ?>
                            <li><?php echo htmlspecialchars($feature); ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($isLoggedIn): ?>
                        <form action="../actions/action_upgrade_plan.php" method="POST">
                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Session::getCsrfToken()); ?>">
                            <button type="submit" class="button button-full" <?php echo $isCurrent ? 'disabled' : ''; ?>>
                                <?php echo $isCurrent ? 'Current Plan' : 'Choose this plan'; ?>
                            </button>
                        </form>
                    <?php else: ?>
                        <p class="card-copy">Please <a href="login.php">log in</a> to select a plan.</p>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
</body>
</html>
<?php
drawFooter();
?>
