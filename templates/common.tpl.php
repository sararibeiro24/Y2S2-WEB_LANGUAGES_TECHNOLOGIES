<?php
function drawHead($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?></title>
        <?php drawCSSLinks(); ?>
    </head>
    <body>
    <?php
}

function drawHeader() {
    require_once(__DIR__ . '/../utils/session.php');
    require_once(__DIR__ . '/../database/user.class.php');
    
    Session::start();
    $isLoggedIn = Session::isLoggedIn();
    $user = null;
    
    if ($isLoggedIn) {
        $userId = Session::getUserId();
        $user = User::getById($userId);
    }
    ?>
    <header class="site-header">
        <div class="container header-container">
            <h1>Ladybug's Gym</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Classes</a></li>
                    <li><a href="#">Trainers</a></li>
                    <?php if ($isLoggedIn && $user): ?>
                        <li><a href="profile.php"><?php echo htmlspecialchars($user->getName()); ?></a></li>
                        <li><a href="../actions/action_logout.php" class="button button-small">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="button button-small">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <?php
}


function drawFooter() {
    ?>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Ladybug's Gym</p>
        </div>
    </footer>
    <?php
}

function drawCSSLinks() {
    ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/pages.css">
    <?php
}
