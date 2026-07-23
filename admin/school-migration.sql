-- School website content tables
-- Run via admin/run-school-migration.php

CREATE TABLE IF NOT EXISTS `tbl_school_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` varchar(40) NOT NULL,
  `person_name` varchar(150) NOT NULL DEFAULT '',
  `designation` varchar(150) NOT NULL DEFAULT '',
  `photo` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_unique` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_admission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_name` varchar(150) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(20) NOT NULL DEFAULT '',
  `class_applied` varchar(80) NOT NULL DEFAULT '',
  `parent_name` varchar(150) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `address` text,
  `previous_school` varchar(255) NOT NULL DEFAULT '',
  `message` text,
  `status` varchar(30) NOT NULL DEFAULT 'New',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tbl_calendar_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `event_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `event_time` varchar(50) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_date_idx` (`event_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
