-- ============================================================
-- Migration: Course Registrations + Profile Photos
-- Run this in phpMyAdmin on your buli_db database
-- ============================================================

-- 1. Add profile_photo to students table
ALTER TABLE `students`
  ADD COLUMN `profile_photo` varchar(255) DEFAULT NULL AFTER `admission_ref_id`;

-- 2. Add profile_photo to staff table
ALTER TABLE `staff`
  ADD COLUMN `profile_photo` varchar(255) DEFAULT NULL AFTER `department`;

-- 3. Create course_registrations table
CREATE TABLE IF NOT EXISTS `course_registrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `semester` varchar(30) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Registered','Dropped','Completed') DEFAULT 'Registered',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_registration` (`student_id`,`course_id`,`semester`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `cr_course_fk` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cr_student_fk` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
