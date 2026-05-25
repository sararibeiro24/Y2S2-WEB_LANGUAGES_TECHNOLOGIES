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
        <script src="../javascript/register_validation.js" defer></script>
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
        // $user = User::getById($userId); 
    }
    ?>
    <header class="site-header">
        <!-- Added ID here for javascript -->
        <div class="container header-container" id="headerContainer">
            
            <!-- Left Side: Title -->
            <h1>Ladybug's Gym</h1>

            <!-- Right Side: Ladybug Menu Toggle -->
            <div class="logo-container" id="ladybugToggle">
                <div class="ladybug-icon">🐞</div>
            </div>

            <!-- The Navigation (The "Wings" Dropdown) -->
            <nav class="wings-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#plans">Plans</a></li>
                    <li><a href="index.php#news">News</a></li>
                    <li><a href="index.php#philosophy">Our Philosophy</a></li>
                    <li><a href="index.php#equipment">Equipment</a></li>
                    <li><a href="schedule.php">Classes</a></li>
                    <li><a href="index.php#trainers">Trainers</a></li>
                    <li><a href="index.php#nutrition">Nutrition</a></li>
                    <li><a href="index.php#feedback">Feedback</a></li>
                    <li><a href="index.php#qa">Q&A</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li><a href="profile.php">My Profile</a></li>
                        <li><a href="../actions/action_logout.php" class="button button-small">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="button button-small">Login</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- JavaScript to toggle the menu when clicking the bug -->
    <script>
        document.getElementById('ladybugToggle').addEventListener('click', function() {
            document.getElementById('headerContainer').classList.toggle('nav-open');
        });
    </script>
    <?php
}

function drawPageHeader($title,$subtitle){
?>
     <div class="page-header">
            <h2><?php echo htmlspecialchars($title); ?></h2>
            <p><?php echo htmlspecialchars($subtitle); ?></p>
        </div>


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
    <link rel="stylesheet" href="../css/shared.css">
    <link rel="stylesheet" href="../css/cards.css">
    <link rel="stylesheet" href="../css/carousel.css">
    <link rel="stylesheet" href="../css/home.css">
    <link rel="stylesheet" href="../css/schedule.css">
    <link rel="stylesheet" href="../css/profile.css">
    <link rel="stylesheet" href="../css/login.css">
    <?php
}
function drawMessages() {

    $messages = Session::getMessages(); 
    
    if (empty($messages)) return;

    echo '<section id="messages">';
    foreach ($messages as $message) {
        $type = htmlspecialchars($message['type']);
        $text = htmlspecialchars($message['text']);
        echo "<div class=\"alert alert-$type\">$text</div>";
    }
    echo '</section>';
}
