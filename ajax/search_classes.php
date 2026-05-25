<?php

require_once(__DIR__ . '/../database/database.db.php');

$db = getDatabaseConnection();

$query = '%' . $_GET['q'] . '%';

$stmt = $db->prepare('
  SELECT
    classes.name,
    users.name as trainer

  FROM class_schedule

  JOIN classes
    ON classes.id = class_schedule.class_id

  JOIN users
    ON users.id = class_schedule.trainer_id

  WHERE classes.name LIKE ?
');

$stmt->execute([$query]);

$classes = $stmt->fetchAll();

foreach ($classes as $class) {

  echo '<article class="card">';

  echo '<h3>' .
    htmlspecialchars($class['name']) .
    '</h3>';

  echo '<p>' .
    htmlspecialchars($class['trainer']) .
    '</p>';

  echo '</article>';
}