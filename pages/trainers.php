<?php
declare(strict_types=1);

require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/trainers.tpl.php');
require_once(__DIR__ . '/../database/trainer.class.php');

$trainers = Trainer::getAllTrainers();

drawHead("Our Trainers | Ladybug's Gym");
drawHeader();
drawPageHeader('MEET OUR TRAINERS', 'Certified professionals dedicated to your success.');
drawTrainersPage($trainers);
drawFooter();
?>