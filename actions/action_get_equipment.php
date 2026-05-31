<?php
declare(strict_types=1);

require_once(__DIR__ . '/../database/class_schedule.class.php');

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['query'] ?? '');
$status = trim($_GET['status'] ?? '');

$data = ClassSchedule::getFilteredEquipment($query, $status !== '' ? $status : null);

echo json_encode($data);
exit;
?>
