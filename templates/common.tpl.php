<?php

/**
 * Common template functions for header, footer, and shared elements
 */

/**
 * Draw the site header with navigation
 */
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

/**
 * Draw the site footer
 */
function drawFooter() {
    ?>
    <footer class="site-footer">
        <div class="container">
            <p>&copy; 2026 Ladybug's Gym</p>
        </div>
    </footer>
    <?php
}

/**
 * Draw CSS links
 */
function drawCSSLinks() {
    ?>
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/components.css">
    <link rel="stylesheet" href="../css/layout.css">
    <link rel="stylesheet" href="../css/pages.css">
    <?php
}

/**
 * Draw page header with title
 */
function drawPageHeader($title, $subtitle = '') {
    ?>
    <div class="page-header">
        <h2><?php echo htmlspecialchars($title); ?></h2>
        <?php if ($subtitle): ?>
            <p><?php echo htmlspecialchars($subtitle); ?></p>
        <?php endif; ?>
    </div>
    <?php
}
