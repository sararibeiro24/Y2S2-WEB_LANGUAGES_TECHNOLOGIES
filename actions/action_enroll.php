<?php

require_once(__DIR__ . '/../database/database.db.php');
require_once(__DIR__ . '/../utils/session.php');

$session = new Session();

if (!$session->isLoggedIn()) {
  die('Access denied');
}

$db = getDatabaseConnection();

$userId = $session->getUserid();

$scheduleId = (int) $_POST['schedule_id'];


// CHECK CAPACITY

$stmt = $db->prepare('
  SELECT
    classes.capacity,
    COUNT(enrollments.id) as enrolled

  FROM class_schedule

  JOIN classes
    ON classes.id = class_schedule.class_id

  LEFT JOIN enrollments
    ON enrollments.schedule_id = class_schedule.id

  WHERE class_schedule.id = ?
');

$stmt->execute([$scheduleId]);

$class = $stmt->fetch();

if ($class['enrolled'] >= $class['capacity']) {
  die('Class is full');
}


// PREVENT DUPLICATES

$stmt = $db->prepare('
  SELECT *
  FROM enrollments
  WHERE user_id = ?
  AND schedule_id = ?
');

$stmt->execute([$userId, $scheduleId]);

if ($stmt->fetch()) {
  die('Already enrolled');
}


// INSERT

$stmt = $db->prepare('
  INSERT INTO enrollments(user_id, schedule_id)
  VALUES (?, ?)
');

$stmt->execute([$userId, $scheduleId]);

header('Location: ../pages/schedule.php');