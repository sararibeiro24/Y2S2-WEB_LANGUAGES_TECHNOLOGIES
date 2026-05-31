<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../database/trainer.class.php');

header('Content-Type: application/json');

echo json_encode(Trainer::getFiltered($_GET['search'] ?? ''));