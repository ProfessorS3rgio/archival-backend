CREATE TABLE `resolution_draft_files` (
  `file_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `resolution_draft_id` bigint NOT NULL,
  `uploaded_by` bigint DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `uploaded_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null
);

CREATE TABLE `project_lists` (
  `project_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `code_no` int DEFAULT null,
  `district` varchar(255) DEFAULT null,
  `location` varchar(255) DEFAULT null,
  `project_name` varchar(255) DEFAULT null,
  `source_fund` varchar(255) DEFAULT null,
  `source_type` varchar(255) DEFAULT null,
  `amount` decimal(12,2) DEFAULT null,
  `extracted_text` longtext,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `committee_categories` (
  `committee_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `committee_name` varchar(255) DEFAULT null,
  `description` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null
);

CREATE TABLE `ordinance_draft_files` (
  `file_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ordinance_draft_id` bigint NOT NULL,
  `uploaded_by` bigint DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `uploaded_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null
);

CREATE TABLE `ordinance_draft` (
  `ordinance_draft_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `year` int DEFAULT null,
  `author` varchar(255) DEFAULT null,
  `session` varchar(255) DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `date_drafted` timestamp DEFAULT null,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `status` varchar(255) DEFAULT null,
  `updated_at` timestamp DEFAULT null
);

CREATE TABLE `sangguniang_panlalawigan_terms` (
  `term_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `term_no` varchar(255) DEFAULT null,
  `start_year` date DEFAULT null,
  `end_year` date DEFAULT null,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `resolution_draft` (
  `resolution_draft_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `year` int DEFAULT null,
  `author` varchar(255) DEFAULT null,
  `session` varchar(255) DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `date_drafted` timestamp DEFAULT null,
  `status` varchar(255) DEFAULT null,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `migrations` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
);

CREATE TABLE `resolution_files` (
  `file_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `resolution_id` bigint NOT NULL,
  `uploaded_by` bigint DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `updated_at` timestamp DEFAULT null,
  `uploaded_at` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `ordinance_lists` (
  `ordinance_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ordinance_draft_id` bigint DEFAULT null,
  `added_by` bigint NOT NULL,
  `ordinance_no` varchar(255) DEFAULT null,
  `author` varchar(255) DEFAULT null,
  `session` varchar(255) DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `date_approved` date DEFAULT null,
  `is_municipal` tinyint(1) NOT NULL DEFAULT '0',
  `municipality` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `sp_profile_pictures` (
  `sp_profile_pictures_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `sp_member_id` bigint NOT NULL,
  `added_by` bigint NOT NULL,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `date_uploaded` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `sangguniang_panlalawigan_members` (
  `member_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `term_id` bigint NOT NULL,
  `district` ENUM ('1st District', '2nd District', 'Ex-Officio') DEFAULT null,
  `full_name` varchar(255) DEFAULT null,
  `extension` ENUM ('Hon', 'Atty', 'Doc') DEFAULT null,
  `position` varchar(255) DEFAULT null,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `committee_reports` (
  `report_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `committee_id` bigint NOT NULL,
  `committee_name` varchar(255) DEFAULT null,
  `committee_report_no` varchar(255) DEFAULT null,
  `municipality_office` varchar(255) DEFAULT null,
  `title` text,
  `year` int DEFAULT null,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `provincial_offices_list` (
  `provincial_offices_list_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `year` int DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `Date_received` date DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `date_uploaded` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `extracted_text` longtext,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `ordinance_files` (
  `file_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `ordinance_id` bigint NOT NULL,
  `uploaded_by` bigint DEFAULT null,
  `year` int DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `uploaded_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null
);

CREATE TABLE `moa_lists` (
  `moa_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `resolution_no` varchar(255) DEFAULT null,
  `date_received` date DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `year` int DEFAULT null,
  `extracted_text` longtext,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `system_logs` (
  `log_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_id` bigint DEFAULT null,
  `action` varchar(100) NOT NULL,
  `target_table` varchar(255) DEFAULT null,
  `target_id` bigint DEFAULT null,
  `description` text,
  `old_values` text COMMENT 'JSON encoded old values for potential rollback',
  `new_values` text COMMENT 'JSON encoded new values',
  `ip_address` varchar(100) DEFAULT null,
  `user_agent` text,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `users` (
  `id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `role` ENUM ('admin', 'staff', 'user') NOT NULL DEFAULT 'user',
  `password` varchar(255) NOT NULL,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `accomplish_lists` (
  `accomplish_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `uploaded_by` bigint NOT NULL,
  `title` varchar(255) DEFAULT null,
  `accomplishment_year` date DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `date_uploaded` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `municipalities_communication_lists` (
  `municipalitites_communication_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `year` int DEFAULT null,
  `date_received` date DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `date_uploaded` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `extracted_text` longtext,
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `national_offices_list` (
  `national_offices_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `added_by` bigint NOT NULL,
  `provincial_resolution_no` varchar(255) DEFAULT null,
  `year` int DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `date_received` date DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `file_name` varchar(255) DEFAULT null,
  `file_url` varchar(255) DEFAULT null,
  `file_size` bigint DEFAULT null,
  `file_type` varchar(255) DEFAULT null,
  `date_uploaded` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `extracted_text` longtext,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `resolution_lists` (
  `resolution_id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `resolution_draft_id` bigint DEFAULT null,
  `resolution_no` varchar(255) DEFAULT null,
  `added_by` bigint NOT NULL,
  `author` varchar(255) DEFAULT null,
  `session` varchar(255) DEFAULT null,
  `title` varchar(255) DEFAULT null,
  `subject` varchar(255) DEFAULT null,
  `date_approved` date DEFAULT null,
  `is_municipal` tinyint(1) NOT NULL DEFAULT '0',
  `municipality` varchar(255) DEFAULT null,
  `extracted_text` longtext,
  `created_at` timestamp DEFAULT (CURRENT_TIMESTAMP),
  `updated_at` timestamp DEFAULT null,
  `is_visible` tinyint(1) NOT NULL DEFAULT '1',
  `last_modified_by` bigint DEFAULT null,
  `last_modified_at` timestamp DEFAULT null
);

CREATE TABLE `settings` (
  `id` bigint PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `auto_fill` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp DEFAULT null,
  `updated_at` timestamp DEFAULT null
);

CREATE INDEX `resolution_draft_files_resolution_draft_id_foreign` ON `resolution_draft_files` (`resolution_draft_id`);

CREATE INDEX `resolution_draft_files_uploaded_by_foreign` ON `resolution_draft_files` (`uploaded_by`);

CREATE INDEX `project_lists_added_by_foreign` ON `project_lists` (`added_by`);

CREATE INDEX `project_lists_last_modified_by_foreign` ON `project_lists` (`last_modified_by`);

CREATE INDEX `committee_categories_added_by_foreign` ON `committee_categories` (`added_by`);

CREATE INDEX `ordinance_draft_files_ordinance_draft_id_foreign` ON `ordinance_draft_files` (`ordinance_draft_id`);

CREATE INDEX `ordinance_draft_files_uploaded_by_foreign` ON `ordinance_draft_files` (`uploaded_by`);

CREATE INDEX `ordinance_draft_added_by_foreign` ON `ordinance_draft` (`added_by`);

CREATE INDEX `resolution_draft_added_by_foreign` ON `resolution_draft` (`added_by`);

CREATE INDEX `resolution_draft_last_modified_by_foreign` ON `resolution_draft` (`last_modified_by`);

CREATE INDEX `resolution_files_resolution_id_foreign` ON `resolution_files` (`resolution_id`);

CREATE INDEX `resolution_files_uploaded_by_foreign` ON `resolution_files` (`uploaded_by`);

CREATE INDEX `ordinance_lists_added_by_foreign` ON `ordinance_lists` (`added_by`);

CREATE INDEX `ordinance_lists_last_modified_by_foreign` ON `ordinance_lists` (`last_modified_by`);

CREATE INDEX `sp_profile_pictures_sp_member_id_foreign` ON `sp_profile_pictures` (`sp_member_id`);

CREATE INDEX `sp_profile_pictures_added_by_foreign` ON `sp_profile_pictures` (`added_by`);

CREATE INDEX `sangguniang_panlalawigan_members_term_id_foreign` ON `sangguniang_panlalawigan_members` (`term_id`);

CREATE INDEX `committee_reports_added_by_foreign` ON `committee_reports` (`added_by`);

CREATE INDEX `committee_reports_committee_id_foreign` ON `committee_reports` (`committee_id`);

CREATE INDEX `committee_reports_last_modified_by_foreign` ON `committee_reports` (`last_modified_by`);

CREATE INDEX `provincial_offices_list_added_by_foreign` ON `provincial_offices_list` (`added_by`);

CREATE INDEX `provincial_offices_list_last_modified_by_foreign` ON `provincial_offices_list` (`last_modified_by`);

CREATE INDEX `ordinance_files_ordinance_id_foreign` ON `ordinance_files` (`ordinance_id`);

CREATE INDEX `ordinance_files_uploaded_by_foreign` ON `ordinance_files` (`uploaded_by`);

CREATE INDEX `moa_lists_added_by_foreign` ON `moa_lists` (`added_by`);

CREATE INDEX `moa_lists_last_modified_by_foreign` ON `moa_lists` (`last_modified_by`);

CREATE INDEX `system_logs_user_id_foreign` ON `system_logs` (`user_id`);

CREATE INDEX `system_logs_action_target_table_target_id_index` ON `system_logs` (`action`, `target_table`, `target_id`);

CREATE UNIQUE INDEX `users_username_unique` ON `users` (`username`);

CREATE INDEX `accomplish_lists_uploaded_by_foreign` ON `accomplish_lists` (`uploaded_by`);

CREATE INDEX `accomplish_lists_last_modified_by_foreign` ON `accomplish_lists` (`last_modified_by`);

CREATE INDEX `municipalities_communication_lists_added_by_foreign` ON `municipalities_communication_lists` (`added_by`);

CREATE INDEX `municipalities_communication_lists_last_modified_by_foreign` ON `municipalities_communication_lists` (`last_modified_by`);

CREATE INDEX `national_offices_list_added_by_foreign` ON `national_offices_list` (`added_by`);

CREATE INDEX `national_offices_list_last_modified_by_foreign` ON `national_offices_list` (`last_modified_by`);

CREATE INDEX `resolution_lists_added_by_foreign` ON `resolution_lists` (`added_by`);

CREATE INDEX `resolution_lists_last_modified_by_foreign` ON `resolution_lists` (`last_modified_by`);

ALTER TABLE `resolution_draft_files` ADD CONSTRAINT `resolution_draft_files_resolution_draft_id_foreign` FOREIGN KEY (`resolution_draft_id`) REFERENCES `resolution_draft` (`resolution_draft_id`) ON DELETE CASCADE;

ALTER TABLE `resolution_draft_files` ADD CONSTRAINT `resolution_draft_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `project_lists` ADD CONSTRAINT `project_lists_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `project_lists` ADD CONSTRAINT `project_lists_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `committee_categories` ADD CONSTRAINT `committee_categories_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `ordinance_draft_files` ADD CONSTRAINT `ordinance_draft_files_ordinance_draft_id_foreign` FOREIGN KEY (`ordinance_draft_id`) REFERENCES `ordinance_draft` (`ordinance_draft_id`) ON DELETE CASCADE;

ALTER TABLE `ordinance_draft_files` ADD CONSTRAINT `ordinance_draft_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `ordinance_draft` ADD CONSTRAINT `ordinance_draft_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `resolution_draft` ADD CONSTRAINT `resolution_draft_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `resolution_draft` ADD CONSTRAINT `resolution_draft_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `resolution_files` ADD CONSTRAINT `resolution_files_resolution_id_foreign` FOREIGN KEY (`resolution_id`) REFERENCES `resolution_lists` (`resolution_id`) ON DELETE CASCADE;

ALTER TABLE `resolution_files` ADD CONSTRAINT `resolution_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `ordinance_lists` ADD CONSTRAINT `ordinance_lists_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `ordinance_lists` ADD CONSTRAINT `ordinance_lists_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `sp_profile_pictures` ADD CONSTRAINT `sp_profile_pictures_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `sp_profile_pictures` ADD CONSTRAINT `sp_profile_pictures_sp_member_id_foreign` FOREIGN KEY (`sp_member_id`) REFERENCES `sangguniang_panlalawigan_members` (`member_id`) ON DELETE CASCADE;

ALTER TABLE `sangguniang_panlalawigan_members` ADD CONSTRAINT `sangguniang_panlalawigan_members_term_id_foreign` FOREIGN KEY (`term_id`) REFERENCES `sangguniang_panlalawigan_terms` (`term_id`) ON DELETE CASCADE;

ALTER TABLE `committee_reports` ADD CONSTRAINT `committee_reports_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `committee_reports` ADD CONSTRAINT `committee_reports_committee_id_foreign` FOREIGN KEY (`committee_id`) REFERENCES `committee_categories` (`committee_id`) ON DELETE CASCADE;

ALTER TABLE `committee_reports` ADD CONSTRAINT `committee_reports_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `provincial_offices_list` ADD CONSTRAINT `provincial_offices_list_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `provincial_offices_list` ADD CONSTRAINT `provincial_offices_list_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `ordinance_files` ADD CONSTRAINT `ordinance_files_ordinance_id_foreign` FOREIGN KEY (`ordinance_id`) REFERENCES `ordinance_lists` (`ordinance_id`) ON DELETE CASCADE;

ALTER TABLE `ordinance_files` ADD CONSTRAINT `ordinance_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `moa_lists` ADD CONSTRAINT `moa_lists_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `moa_lists` ADD CONSTRAINT `moa_lists_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `system_logs` ADD CONSTRAINT `system_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `accomplish_lists` ADD CONSTRAINT `accomplish_lists_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `accomplish_lists` ADD CONSTRAINT `accomplish_lists_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `municipalities_communication_lists` ADD CONSTRAINT `municipalities_communication_lists_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `municipalities_communication_lists` ADD CONSTRAINT `municipalities_communication_lists_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `national_offices_list` ADD CONSTRAINT `national_offices_list_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `national_offices_list` ADD CONSTRAINT `national_offices_list_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `resolution_lists` ADD CONSTRAINT `resolution_lists_added_by_foreign` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `resolution_lists` ADD CONSTRAINT `resolution_lists_last_modified_by_foreign` FOREIGN KEY (`last_modified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
