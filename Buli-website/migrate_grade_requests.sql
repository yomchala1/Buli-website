-- ============================================================
-- Migration: Grade Edit Requests
-- Run this in phpMyAdmin on buli_db
-- ============================================================

CREATE TABLE IF NOT EXISTS `grade_edit_requests` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `grade_id`      int(11)      NOT NULL,
  `staff_id`      int(11)      NOT NULL,
  `student_id`    int(11)      NOT NULL,
  `course_id`     int(11)      NOT NULL,
  `semester`      varchar(30)  NOT NULL,
  `old_grade`     varchar(5)   NOT NULL,
  `new_grade`     varchar(5)   NOT NULL,
  `old_gp`        decimal(3,2) NOT NULL DEFAULT 0.00,
  `new_gp`        decimal(3,2) NOT NULL DEFAULT 0.00,
  `reason`        text         NOT NULL,
  `status`        enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `admin_note`    varchar(255) DEFAULT NULL,
  `requested_at`  timestamp    NOT NULL DEFAULT current_timestamp(),
  `reviewed_at`   timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grade_id`   (`grade_id`),
  KEY `staff_id`   (`staff_id`),
  KEY `student_id` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
