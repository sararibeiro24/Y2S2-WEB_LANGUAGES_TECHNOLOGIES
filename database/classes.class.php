<?php

class GymClass {

  static function getAll(PDO $db) {

    $stmt = $db->prepare('
      SELECT
        class_schedule.id as schedule_id,
        classes.name,
        classes.description,
        classes.capacity,
        class_schedule.scheduled_at,
        users.name as trainer,
        COUNT(enrollments.id) as enrolled

      FROM class_schedule

      JOIN classes
        ON classes.id = class_schedule.class_id

      JOIN users
        ON users.id = class_schedule.trainer_id

      LEFT JOIN enrollments
        ON enrollments.schedule_id = class_schedule.id

      GROUP BY class_schedule.id

      ORDER BY class_schedule.scheduled_at ASC
    ');

    $stmt->execute();

    return $stmt->fetchAll();
  }
}