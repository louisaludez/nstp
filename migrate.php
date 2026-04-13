<?php
require 'config/db.php';

try {
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS `activity_plans` (
      `id` int NOT NULL AUTO_INCREMENT,
      `instructor_id` int NOT NULL,
      `section_id` int DEFAULT NULL,
      `title` varchar(255) NOT NULL,
      `description` text,
      `location` varchar(255) DEFAULT NULL,
      `scheduled_date` date DEFAULT NULL,
      `objectives` text,
      `files_attached` int DEFAULT 0,
      `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
      `submitted_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE IF NOT EXISTS `accomplishment_reports` (
      `id` int NOT NULL AUTO_INCREMENT,
      `instructor_id` int NOT NULL,
      `section_id` int DEFAULT NULL,
      `title` varchar(255) NOT NULL,
      `location` varchar(255) DEFAULT NULL,
      `completed_date` date DEFAULT NULL,
      `participants_count` int DEFAULT 0,
      `accomplishments` text,
      `files_attached` int DEFAULT 0,
      `status` enum('Pending','Reviewed') DEFAULT 'Pending',
      `submitted_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE IF NOT EXISTS `notifications` (
      `id` int NOT NULL AUTO_INCREMENT,
      `user_id` int DEFAULT NULL,
      `role` varchar(50) DEFAULT NULL,
      `message` text NOT NULL,
      `link` varchar(255) DEFAULT NULL,
      `is_read` tinyint(1) DEFAULT 0,
      `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    );

    CREATE TABLE IF NOT EXISTS `attendance` (
      `id` int NOT NULL AUTO_INCREMENT,
      `student_id` varchar(50) NOT NULL,
      `section_id` int NOT NULL,
      `date` date NOT NULL,
      `status` enum('Present','Absent','Late') NOT NULL,
      PRIMARY KEY (`id`)
    );
    ");
    echo "Done";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
