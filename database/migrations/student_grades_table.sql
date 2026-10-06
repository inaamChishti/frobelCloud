-- Create student_grades table
CREATE TABLE IF NOT EXISTS `student_grades` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `studentid` varchar(255) NOT NULL,
  `family_id` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `month` varchar(255) NOT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `branch_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_grades_studentid_month_index` (`studentid`, `month`),
  KEY `student_grades_family_id_month_index` (`family_id`, `month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

