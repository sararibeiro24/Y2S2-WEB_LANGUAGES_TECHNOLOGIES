<?php
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../database/plan.class.php');
require_once(__DIR__ . '/../database/feedback.class.php');
require_once(__DIR__ . '/../database/user.class.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../templates/plans.tpl.php');
require_once(__DIR__ . '/../templates/nutrition.tpl.php');
require_once(__DIR__ . '/../templates/equipment.tpl.php');
require_once( __DIR__ . '/../templates/home/feedback.tpl.php');

Session::start();
$isLoggedIn = Session::isLoggedIn();
$plans = Plan::getAllPlans();
$feedbacks = [];
$feedbackName = '';
try {
    $feedbacks = Feedback::getApprovedFeedback();
} catch (PDOException $e) {
    error_log('Feedback error: ' . $e->getMessage());
}

if (Session::isLoggedIn()) {
    $user = User::getById(Session::getUserId());
    $feedbackName = $user ? $user->getName() : '';
}
drawHead("Ladybug's Gym | Welcome");
drawHeader();
?>
    <main class="scrollable-home">
        <?php require __DIR__ . '/../templates/home/hero.tpl.php'; ?>
        <?php drawPlansTeaser($plans); ?>
        <?php require __DIR__ . '/../templates/home/cta.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/news.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/philosophy.tpl.php'; ?>
        <?php drawEquipmentHomepage(); ?>
        <?php require __DIR__ . '/../templates/home/classes.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/trainers.tpl.php'; ?>
        <?php drawNutritionHomepage(); ?>
        <?php drawFeedback($feedbacks,$feedbackName);?>
        <?php require __DIR__ . '/../templates/home/qa.tpl.php'; ?>

    </main>

    <script>
        function scrollCarousel(carouselId, direction) {
            const carousel = document.getElementById(carouselId);
            const itemWidth = carousel.querySelector('.carousel-item').offsetWidth + 32;

            if (direction === 1) {
                carousel.scrollLeft += itemWidth;
            } else {
                carousel.scrollLeft -= itemWidth;
            }
        }
    </script>
<?php drawFooter(); ?>

