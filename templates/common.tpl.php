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
    ?>
    <header class="site-header">
        <div class="container header-container">
            <h1>Ladybug's Gym</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="schedule.php">Classes</a></li>
                    <li><a href="#">Trainers</a></li>
                    <li><a href="login.php" class="button button-small">Login</a></li>
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
