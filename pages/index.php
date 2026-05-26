<?php
require_once(__DIR__ . '/../templates/common.tpl.php');

drawHead("Ladybug's Gym | Welcome");
drawHeader();
?>
    <main class="scrollable-home">
        <?php require __DIR__ . '/../templates/home/hero.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/plans.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/cta.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/news.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/philosophy.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/equipment.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/classes.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/trainers.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/nutrition.tpl.php'; ?>
        <?php require __DIR__ . '/../templates/home/feedback.tpl.php'; ?>
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
</body>
</html>
