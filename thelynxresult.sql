-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2026 at 10:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thelynxresult`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-arsalansareed@gmail.com|127.0.0.1', 'i:1;', 1770365856),
('laravel-cache-arsalansareed@gmail.com|127.0.0.1:timer', 'i:1770365856;', 1770365856),
('laravel-cache-hasnainjaoon425@gmail.com|127.0.0.1', 'i:4;', 1770352986),
('laravel-cache-hasnainjaoon425@gmail.com|127.0.0.1:timer', 'i:1770352986;', 1770352986),
('laravel-cache-husnian12@gmail.com|127.0.0.1', 'i:1;', 1770613456),
('laravel-cache-husnian12@gmail.com|127.0.0.1:timer', 'i:1770613456;', 1770613456),
('laravel-cache-spatie.permission.cache', 'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:15:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:14:\"view dashboard\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:16:\"approve teachers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:15:\"revoke teachers\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:1;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:13:\"create result\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:11:\"edit result\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:13:\"delete result\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:11:\"view result\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:13:\"view sessions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:15:\"create sessions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:13:\"edit sessions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:15:\"delete sessions\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:15:\"create subjects\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:13:\"edit subjects\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:13:\"view subjects\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:15:\"delete subjects\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:7:\"Teacher\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:7:\"Student\";s:1:\"c\";s:3:\"web\";}}}', 1770699864);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_21_060551_create_the_lynx_results_table', 1),
(5, '2026_01_21_061220_create_subject_wise_marks_table', 1),
(6, '2026_01_21_061305_create_student_results_table', 1),
(7, '2026_01_21_061324_create_student_marks_table', 1),
(9, '2026_01_21_060441_create_the_lyn_x_results_table', 2),
(10, '2026_01_24_112255_create_schoolsession_table', 3),
(12, '2026_01_24_142233_add_session_id_to_student_results_table', 4),
(13, '2026_01_24_160545_add_session_id_to_student_results_table', 4),
(14, '2026_01_24_160553_add_session_id_to_student_results_table', 4),
(15, '2026_01_25_181828_add_session_id_column_to_student_results', 5),
(19, '2026_01_25_192316_add_term_working_days_to_student_results_table', 6),
(20, '2026_01_30_054708_add_grand_terms_to_student_results_table', 7),
(22, '2026_01_30_185658_add_promoted_class_to_student_results_table', 7),
(23, '2026_02_02_060316_add_profile_and_branch_fields_to_users_table', 8),
(26, '2026_02_03_044931_create_permission_tables', 9),
(27, '2026_02_06_070819_add_role_id_to_users_table', 10),
(28, '2026_02_06_085607_remove_role_id_from_users_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 7),
(2, 'App\\Models\\User', 8),
(2, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 11),
(2, 'App\\Models\\User', 12),
(2, 'App\\Models\\User', 13),
(2, 'App\\Models\\User', 14),
(2, 'App\\Models\\User', 15);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('nain888@gmail.com', '$2y$12$RmQ5eFatjssanvtpr4td1e.7fCVKOiKf/NaeO4z2kmurNbY2nQHj6', '2026-02-06 01:07:18');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view dashboard', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(2, 'approve teachers', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(3, 'revoke teachers', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(4, 'create result', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(5, 'edit result', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(6, 'delete result', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(7, 'view result', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(8, 'view sessions', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(9, 'create sessions', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(10, 'edit sessions', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(11, 'delete sessions', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(12, 'create subjects', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(13, 'edit subjects', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(14, 'view subjects', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(15, 'delete subjects', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(2, 'Teacher', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(3, 'Student', 'web', '2026-02-03 00:35:15', '2026-02-03 00:35:15'),
(4, 'User', 'web', '2026-02-03 01:29:09', '2026-02-03 01:29:09');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(3, 1),
(4, 1),
(4, 2),
(5, 1),
(5, 2),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(7, 3),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(11, 1),
(11, 2),
(12, 1),
(12, 2),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(15, 1),
(15, 2);

-- --------------------------------------------------------

--
-- Table structure for table `schoolsessions`
--

CREATE TABLE `schoolsessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `t1_working_days` varchar(255) NOT NULL,
  `t2_working_days` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schoolsessions`
--

INSERT INTO `schoolsessions` (`id`, `title`, `t1_working_days`, `t2_working_days`, `created_at`, `updated_at`) VALUES
(8, '2026', '99', '98', '2026-01-30 00:39:30', '2026-01-30 00:39:30'),
(9, '2024', '110', '90', '2026-01-30 00:39:40', '2026-01-30 00:39:40'),
(11, '2027', '121', '123', '2026-01-31 07:15:33', '2026-01-31 07:15:33'),
(12, '2028', '100', '110', '2026-02-02 06:11:48', '2026-02-02 06:11:48'),
(13, '2023', '100', '110', '2026-02-03 04:16:33', '2026-02-03 04:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7C8NW1s2k5sOYub4fuMwlgLCvJCWeQNIAdPvmJsk', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMU1tcjBzSVpQTTluM0U3RjRtV3RKanUwNHNScU5jc2c1eldabWtCayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXN1bHRzLzkyIjtzOjU6InJvdXRlIjtzOjEyOiJyZXN1bHRzLnNob3ciO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1770621866),
('9XzaZGxv91WpP1CJdFE1edQiT2KxDs5xQ9o0M5Vv', 7, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTnlaZTRKZGIxYWVteEZMS282UExtUEROQmJqYU9DWEJyTGJ5UzByQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9yZXN1bHRzIjtzOjU6InJvdXRlIjtzOjE1OiJzdHVkZW50cy5yZXN1bHQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo3O30=', 1770621716);

-- --------------------------------------------------------

--
-- Table structure for table `student_marks`
--

CREATE TABLE `student_marks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `result_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `term_one_mark` decimal(5,2) DEFAULT NULL,
  `term_one_grade` varchar(255) DEFAULT NULL,
  `term_one_percent` decimal(5,2) DEFAULT NULL,
  `term_one_total` decimal(5,2) DEFAULT NULL,
  `term_two_mark` decimal(5,2) DEFAULT NULL,
  `term_two_grade` varchar(255) DEFAULT NULL,
  `term_two_percent` decimal(5,2) DEFAULT NULL,
  `term_two_total` decimal(5,2) DEFAULT NULL,
  `grand_term_one` decimal(5,2) DEFAULT NULL,
  `grand_term_two` decimal(5,2) DEFAULT NULL,
  `w_days_term_one` int(11) DEFAULT NULL,
  `w_days_term_two` int(11) DEFAULT NULL,
  `w_days_total` int(11) DEFAULT NULL,
  `remarks` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_marks`
--

INSERT INTO `student_marks` (`id`, `result_id`, `subject_id`, `term_one_mark`, `term_one_grade`, `term_one_percent`, `term_one_total`, `term_two_mark`, `term_two_grade`, `term_two_percent`, `term_two_total`, `grand_term_one`, `grand_term_two`, `w_days_term_one`, `w_days_term_two`, `w_days_total`, `remarks`, `created_at`, `updated_at`) VALUES
(74, 66, 17, 29.00, 'B', 72.50, 40.00, 35.00, 'A', 87.50, 40.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-01-30 03:59:01', '2026-01-30 03:59:01'),
(75, 66, 18, 31.00, 'B', 77.50, 40.00, 54.00, 'A+', 90.00, 60.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-01-30 03:59:01', '2026-01-30 03:59:01'),
(76, 66, 19, 27.00, 'A+', 90.00, 30.00, 41.00, 'A', 82.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-01-30 03:59:01', '2026-01-30 03:59:01'),
(80, 67, 19, 24.00, 'A', 80.00, 30.00, 34.00, 'C', 68.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-01-30 14:26:43', '2026-01-30 14:26:43'),
(81, 67, 18, 36.00, 'A+', 90.00, 40.00, 54.00, 'A+', 90.00, 60.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-01-30 14:26:43', '2026-01-30 14:26:43'),
(82, 68, 17, 40.00, 'A+', 100.00, 40.00, 21.00, 'D', 52.50, 40.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-01-30 14:27:50', '2026-01-30 14:27:50'),
(84, 69, 18, 31.00, 'B', 77.50, 40.00, 41.00, 'C', 68.33, 60.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-01-30 14:41:55', '2026-01-30 14:41:55'),
(139, 80, 23, 91.00, 'A+', 91.00, 100.00, 21.00, 'F', 21.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Satisfactory', '2026-02-01 23:32:59', '2026-02-01 23:32:59'),
(161, 81, 17, 35.00, 'A', 87.50, 40.00, 36.00, 'A+', 90.00, 40.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-02-02 00:41:19', '2026-02-02 00:41:19'),
(162, 81, 26, 89.00, 'A', 89.00, 100.00, 76.00, 'B', 76.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-02-02 00:41:19', '2026-02-02 00:41:19'),
(166, 82, 28, 45.00, 'A+', 90.00, 50.00, 65.00, 'A', 86.67, 75.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-02-02 02:20:32', '2026-02-02 02:20:32'),
(167, 82, 29, 26.00, 'D', 52.00, 50.00, 31.00, 'C', 62.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Satisfactory', '2026-02-02 02:20:32', '2026-02-02 02:20:32'),
(169, 83, 30, 42.00, 'A', 84.00, 50.00, 39.00, 'A+', 97.50, 40.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 04:53:08', '2026-02-02 04:53:08'),
(170, 83, 31, 45.00, 'B', 75.00, 60.00, 24.00, 'A', 80.00, 30.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-02 04:53:08', '2026-02-02 04:53:08'),
(171, 84, 28, 50.00, 'A+', 100.00, 50.00, 23.00, 'F', 30.67, 75.00, NULL, NULL, NULL, NULL, NULL, 'Satisfactory', '2026-02-02 05:11:16', '2026-02-02 05:11:16'),
(172, 84, 29, 45.00, 'A+', 90.00, 50.00, 49.00, 'A+', 98.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 05:11:16', '2026-02-02 05:11:16'),
(173, 85, 29, 18.00, 'F', 36.00, 50.00, 96.00, 'A+', 192.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 07:07:58', '2026-02-02 07:07:58'),
(175, 86, 17, 39.00, 'A+', 97.50, 40.00, 36.00, 'A+', 90.00, 40.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 07:32:25', '2026-02-02 07:32:25'),
(176, 86, 27, 50.00, 'D', 50.00, 100.00, 78.00, 'B', 78.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Good', '2026-02-02 07:32:25', '2026-02-02 07:32:25'),
(177, 86, 23, 70.00, 'B', 70.00, 100.00, 68.00, 'C', 68.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Good', '2026-02-02 07:32:25', '2026-02-02 07:32:25'),
(178, 86, 24, 45.00, 'F', 45.00, 100.00, 96.00, 'A+', 96.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-02 07:32:25', '2026-02-02 07:32:25'),
(179, 87, 17, 39.00, 'A+', 97.50, 40.00, 39.00, 'A+', 97.50, 40.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 08:05:33', '2026-02-02 08:05:33'),
(180, 87, 18, 39.00, 'A+', 97.50, 40.00, 57.00, 'A+', 95.00, 60.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 08:05:33', '2026-02-02 08:05:33'),
(181, 87, 19, 28.00, 'A+', 93.33, 30.00, 49.00, 'A+', 98.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-02 08:05:33', '2026-02-02 08:05:33'),
(185, 89, 34, 72.00, 'B', 72.00, 100.00, 79.00, 'B', 79.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-03 05:09:27', '2026-02-03 05:09:27'),
(186, 89, 35, 66.00, 'C', 66.00, 100.00, 74.00, 'B', 74.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-03 05:09:27', '2026-02-03 05:09:27'),
(187, 90, 34, 63.00, 'C', 63.00, 100.00, 59.00, 'D', 59.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Good', '2026-02-03 05:10:37', '2026-02-03 05:10:37'),
(188, 90, 35, 68.00, 'C', 68.00, 100.00, 51.00, 'D', 51.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Satisfactory', '2026-02-03 05:10:37', '2026-02-03 05:10:37'),
(191, 91, 34, 75.00, 'B', 75.00, 100.00, 72.00, 'B', 72.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-03 05:13:26', '2026-02-03 05:13:26'),
(192, 91, 35, 78.00, 'B', 78.00, 100.00, 72.00, 'B', 72.00, 100.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-03 05:13:26', '2026-02-03 05:13:26'),
(199, 79, 17, 36.00, 'A+', 90.00, 40.00, 56.00, 'A+', 93.33, 60.00, NULL, NULL, NULL, NULL, NULL, 'Outstanding', '2026-02-04 00:32:58', '2026-02-04 00:32:58'),
(200, 79, 18, 36.00, 'A+', 90.00, 40.00, 49.00, 'A', 81.67, 60.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-02-04 00:32:58', '2026-02-04 00:32:58'),
(201, 79, 19, 36.00, 'A+', 120.00, 30.00, 35.00, 'B', 70.00, 50.00, NULL, NULL, NULL, NULL, NULL, 'Excellent', '2026-02-04 00:32:58', '2026-02-04 00:32:58'),
(202, 88, 33, 26.00, 'C', 65.00, 40.00, 33.00, 'A', 82.50, 40.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-04 00:43:08', '2026-02-04 00:43:08'),
(203, 92, 17, 36.00, 'A+', 90.00, 40.00, 39.00, 'C', 65.00, 60.00, NULL, NULL, NULL, NULL, NULL, 'Very Good', '2026-02-06 01:01:34', '2026-02-06 01:01:34');

-- --------------------------------------------------------

--
-- Table structure for table `student_results`
--

CREATE TABLE `student_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `section` varchar(255) NOT NULL,
  `rollno` int(11) NOT NULL,
  `session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `attendance` varchar(255) NOT NULL,
  `t1_working_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `t2_working_days` decimal(5,2) NOT NULL DEFAULT 0.00,
  `grand_term_one` int(11) NOT NULL DEFAULT 0,
  `grand_term_two` int(11) NOT NULL DEFAULT 0,
  `grand_total` int(11) NOT NULL DEFAULT 0,
  `overall_grade` varchar(255) DEFAULT NULL,
  `overall_percentage` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `promoted_class` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_results`
--

INSERT INTO `student_results` (`id`, `name`, `class`, `section`, `rollno`, `session_id`, `attendance`, `t1_working_days`, `t2_working_days`, `grand_term_one`, `grand_term_two`, `grand_total`, `overall_grade`, `overall_percentage`, `remarks`, `created_by`, `created_at`, `updated_at`, `promoted_class`) VALUES
(79, 'salar', 'prep', 'first', 199, 8, '163', 65.00, 98.00, 108, 140, 248, 'A', '88.58', 'perfect', 2, '2026-02-01 08:06:27', '2026-02-04 00:32:58', 'nursery'),
(80, 'alar', 'Omnis incididunt eos', 'Sit quo accusantium', 45, 8, '133', 44.00, 89.00, 91, 21, 112, 'D', '56', 'Alias et at aut labo', 2, '2026-02-01 08:14:18', '2026-02-01 23:32:59', NULL),
(81, 'hanan', 'abc', 'z', 1321, 11, '212', 100.00, 112.00, 124, 112, 236, 'A', '85.63', 'FIALED', 2, '2026-02-01 23:35:11', '2026-02-02 00:41:19', 'xyz'),
(82, 'Husnain', 'first', 'a', 1221, 8, '167', 89.00, 78.00, 71, 96, 0, 'B', '72.5', 'fialed', 3, '2026-02-02 02:20:32', '2026-02-02 02:20:32', 'second'),
(83, 'Abdul Ahad', 'Prep Green', 'Green', 1231, 8, '167', 88.00, 79.00, 87, 63, 150, 'A', '83.33', 'Doing good, but still needs improvements in the subject of Maths.', 4, '2026-02-02 04:52:38', '2026-02-02 04:53:08', 'Nursery Green'),
(84, 'Lavinia Curry', 'Voluptas aut ipsum i', 'Cum aut do blanditii', 321, 11, '212', 102.00, 110.00, 95, 72, 0, 'B', '76.2', 'Dolor laborum eligen', 3, '2026-02-02 05:11:16', '2026-02-02 05:11:16', 'Alias iure magni qui'),
(85, 'Kelsey Atkinson', 'Sed commodi est fug', 'Perferendis aliquam', 1212, 9, '178', 100.00, 78.00, 18, 96, 0, 'A+', '114', 'Cillum illo in saepe', 3, '2026-02-02 07:07:58', '2026-02-02 07:07:58', 'Dolore vel nostrum o'),
(86, 'hasnain khan', 'software', 'B', 788, 12, '143', 98.00, 45.00, 204, 278, 482, 'B', '74.31', 'goood till', 2, '2026-02-02 07:31:35', '2026-02-02 07:32:25', 'artificial intelligence'),
(87, 'Abdullah', 'BS BS', 'D', 103, 9, '184', 102.00, 82.00, 106, 145, 0, 'A+', '96.58', 'Perfect', 2, '2026-02-02 08:05:33', '2026-02-02 08:05:33', 'SE SE'),
(88, 'Jahanzaib', 'Software', 'A', 321, 13, '180', 84.00, 96.00, 26, 33, 59, 'B', '73.75', 'Perfect', 7, '2026-02-03 04:19:03', '2026-02-04 00:43:08', 'Laravel'),
(89, 'Usama', 'Junior', 'Green', 432, 13, '160', 86.00, 74.00, 138, 153, 291, 'B', '72.75', 'Looking Good', 9, '2026-02-03 05:07:35', '2026-02-03 05:09:27', 'Senior'),
(90, 'Arsalan', 'Junior', 'Blue', 125, 13, '119', 56.00, 63.00, 131, 110, 0, 'C', '60.25', 'alright', 9, '2026-02-03 05:10:37', '2026-02-03 05:10:37', 'Senior'),
(91, 'Mohsin', 'junior', 'REd', 421, 13, '187', 89.00, 98.00, 153, 144, 297, 'B', '74.25', NULL, 9, '2026-02-03 05:12:56', '2026-02-03 05:13:26', 'senior'),
(92, 'hanan ahmed', 'hana', 'b', 1020, 11, '224', 112.00, 112.00, 36, 39, 0, 'B', '75', 'testing', 2, '2026-02-06 01:01:34', '2026-02-06 01:01:34', 'ahmed');

-- --------------------------------------------------------

--
-- Table structure for table `subject_wise_marks`
--

CREATE TABLE `subject_wise_marks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `term_one_marks` int(11) NOT NULL,
  `term_two_marks` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subject_wise_marks`
--

INSERT INTO `subject_wise_marks` (`id`, `subject_name`, `term_one_marks`, `term_two_marks`, `created_by`, `created_at`, `updated_at`) VALUES
(17, 'Artificial Intelligence', 40, 60, 2, '2026-01-25 23:16:40', '2026-02-03 23:24:39'),
(18, 'Data Base', 40, 60, 2, '2026-01-30 00:38:42', '2026-01-30 00:38:42'),
(19, 'Linear Algebra', 30, 50, 2, '2026-01-30 00:39:03', '2026-01-30 00:39:03'),
(20, 'SQL', 40, 60, 2, '2026-01-30 15:06:05', '2026-01-30 15:06:05'),
(21, 'PRe nursery', 100, 100, 2, '2026-01-31 01:39:47', '2026-01-31 01:39:47'),
(22, 'Prep', 100, 100, 2, '2026-01-31 01:39:55', '2026-01-31 01:39:55'),
(23, 'first', 100, 100, 2, '2026-01-31 01:40:05', '2026-01-31 01:40:05'),
(24, 'second', 100, 100, 2, '2026-01-31 01:40:17', '2026-01-31 01:40:17'),
(25, 'third', 100, 100, 2, '2026-01-31 01:40:27', '2026-01-31 01:40:27'),
(26, 'fourth', 100, 100, 2, '2026-01-31 01:40:36', '2026-01-31 01:40:36'),
(27, '5th', 100, 100, 2, '2026-01-31 01:40:46', '2026-01-31 01:40:46'),
(28, 'Human Sciences', 50, 75, 3, '2026-02-02 02:17:49', '2026-02-02 02:17:49'),
(29, 'IslamiyaT', 50, 50, 3, '2026-02-02 02:18:07', '2026-02-02 02:18:07'),
(30, 'Pre English', 50, 40, 4, '2026-02-02 04:50:13', '2026-02-02 04:50:13'),
(31, 'Maths', 60, 30, 4, '2026-02-02 04:50:29', '2026-02-02 04:50:29'),
(32, 'Englsih Grammer', 60, 40, 3, '2026-02-02 06:05:28', '2026-02-02 06:05:28'),
(33, 'LARAVEL', 40, 40, 7, '2026-02-03 04:18:08', '2026-02-03 04:18:08'),
(34, 'Professional Practices', 100, 100, 9, '2026-02-03 05:06:16', '2026-02-03 05:06:16'),
(35, 'Reverse Engineering', 100, 100, 9, '2026-02-03 05:08:56', '2026-02-03 05:08:56'),
(36, 'basics', 121, 121, 2, '2026-02-09 02:05:27', '2026-02-09 02:05:27');

-- --------------------------------------------------------

--
-- Table structure for table `the_lynx_results`
--

CREATE TABLE `the_lynx_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `the_lyn_x_results`
--

CREATE TABLE `the_lyn_x_results` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `branch_name` varchar(255) DEFAULT NULL,
  `branch_email` varchar(255) DEFAULT NULL,
  `branch_phone` varchar(255) DEFAULT NULL,
  `branch_address` text DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `profile_picture`, `branch_name`, `branch_email`, `branch_phone`, `branch_address`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'husnainjadoon', 'husnainjadoon@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-01-21 01:35:52', '2026-02-06 02:38:20'),
(2, 'Hanan Ahmed', 'hanan121@gmail.com', 'profile_pictures/Tsn8ZWJfgs1GGyzKZlmYWgjhOTOq91ZDCyfkhUJ9.jpg', 'I-8 Junior senior', 'branch@gmial.com', '045123456', 'House No 1572, Street 87, Sector I-10 1', NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-01-21 04:21:56', '2026-02-06 02:12:24'),
(3, 'hanan khan jadoon', 'hanan1221@gmail.com', 'profile_pictures/6K0nEBPsf22uEutgSCsxobDrmBHYngfdaQNW72dH.png', 'I-8 Junior to senior', 'branch@gmial.com', '045123456', 'st-91-1234', NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-02 02:15:14', '2026-02-06 02:38:20'),
(4, 'Hasnain Khan Jadoon', 'hasnainjadoon@gmail.com', 'profile_pictures/W5ZpWYxELjMHAX9OAOsHKDatJCPHtYtCsrUuKRpO.png', 'Rawalpinidi Junior', 'rwpjunior@tlschool.com', '+9212345678', 'Rawalpindi Satellite Town Junior', NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-02 04:46:47', '2026-02-06 02:38:20'),
(7, 'umer', 'teacher@gmail.com', 'profile_pictures/dS2yQDe01GhxWrDWPDmhBS8YceuqdJKua6pgo25L.png', 'I8 Senior to junior', 'SeniorI8@thelynx.com', '+921234567', 'Street 91/ house no 123 / I-8 4', NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', 'bSTLm35KujUG98LYZSxgMiO19GNkis6QJFhNQPmvh0EeW9rDYtHFvMAhZ5F9', '2026-02-03 01:30:09', '2026-02-06 02:38:20'),
(8, 'User 1123', 'user1@gmail.com', NULL, 'husnain', 'husnian444@gmail.com', '102103103013', 'line 102103', NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-03 02:19:58', '2026-02-06 02:38:20'),
(9, 'Usama', 'usamaahmed@gmail.com', 'profile_pictures/r0aA98GYy06RWeJ2zaohRBdSg6KGm1KDrVCtsHW3.png', 'Senior I8-8', 'I8seniorbranch@thelynx.com', '852136497', 'st 45 line 85 i8-4', NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-03 05:03:55', '2026-02-06 02:38:20'),
(10, 'Teacher test', 'teachertest@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-04 08:53:43', '2026-02-06 02:38:20'),
(11, 'Warren Sheppard', 'jiky@mailinator.com', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-04 08:54:40', '2026-02-06 02:38:20'),
(12, 'my teacher', 'myteacher@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$hbVVddTC39lBX2hialr.FeQboSA3AlE2VwFw9zzj.vGgUF4Z0sdhS', NULL, '2026-02-04 08:56:55', '2026-02-06 02:38:20'),
(13, 'test2', 'test2@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$2e3c08BEd/EIMuNizKHD/O4TUkBu1MC.7hOlb.sFJvAhHEyYAnk4G', NULL, '2026-02-05 23:57:13', '2026-02-06 02:38:20'),
(14, 'nain 444', 'nain888@gmail.com', 'profile_pictures/V48h8RAjRFVBn8R6UusUIooWnCNy1y3eOi9uHb4O.png', 'head office', 'headoffice@thelynx.com', '12345678', 'St 91/Hn 81/I-8 4', NULL, '$2y$12$/rcMIpNhs5GcnQ7aZHz22eCgLGc4OB75ckdA382A6haNFl6IcUbWG', NULL, '2026-02-06 00:10:14', '2026-02-06 02:38:20'),
(15, 'Arsalan Saeed khan', 'arsalansaeed@gmail.com', NULL, 'Head office IT', 'headoffice@thelynx.com', '12345678', NULL, NULL, '$2y$12$tfhyGUHvyXs5UYgk4pInZ.TcQubs90.rCtnz8u6x6lNAdab674Snm', NULL, '2026-02-06 02:40:38', '2026-02-06 02:51:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `schoolsessions`
--
ALTER TABLE `schoolsessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `student_marks`
--
ALTER TABLE `student_marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_results`
--
ALTER TABLE `student_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subject_wise_marks`
--
ALTER TABLE `subject_wise_marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `the_lynx_results`
--
ALTER TABLE `the_lynx_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `the_lyn_x_results`
--
ALTER TABLE `the_lyn_x_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schoolsessions`
--
ALTER TABLE `schoolsessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_marks`
--
ALTER TABLE `student_marks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `student_results`
--
ALTER TABLE `student_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `subject_wise_marks`
--
ALTER TABLE `subject_wise_marks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `the_lynx_results`
--
ALTER TABLE `the_lynx_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `the_lyn_x_results`
--
ALTER TABLE `the_lyn_x_results`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
