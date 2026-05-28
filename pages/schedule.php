<?php
declare(strict_types=1);
require_once(__DIR__ . '/../templates/common.tpl.php');
require_once(__DIR__ . '/../templates/schedule.tpl.php');
require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/class_schedule.class.php');

date_default_timezone_set('UTC');
Session::start();

$isLoggedIn = Session::isLoggedIn();
$userId     = Session::getUserId();
$weekStart  = $_GET['week'] ?? date('Y-m-d', strtotime('monday this week'));
$classes    = ClassSchedule::getWeekClasses($weekStart);
$enrolledIds = $isLoggedIn ? ClassSchedule::getUserEnrollmentIds($userId) : [];

// Group classes by day
$byDay = [];
foreach ($classes as $c) {
    $day = date('Y-m-d', strtotime($c['scheduled_at']));
    $byDay[$day][] = $c;
}

drawHead("Class Schedule | Ladybug's Gym");
drawHeader();
drawPageHeader('Class Schedule', 'Find and book your next workout session.');
drawScheduleCalendar($weekStart, $byDay, $enrolledIds, $isLoggedIn);
drawScheduleModals();
drawScheduleBootstrap($weekStart, $isLoggedIn, $enrolledIds);
drawFooter();