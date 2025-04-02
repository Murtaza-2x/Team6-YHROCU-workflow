-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 02:29 PM
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
-- Database: `rocu`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `task_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 'auth0|67e80674b5ac5df6f28de6cd', 'yes', '2025-04-01 09:14:35'),
(2, 1, 'auth0|67e80674b5ac5df6f28de6cd', 'i got nothing on but the radio', '2025-04-01 11:01:52'),
(3, 3, 'auth0|67e80674b5ac5df6f28de6cd', 'well yes!!', '2025-04-01 20:27:03'),
(4, 3, 'auth0|67e80674b5ac5df6f28de6cd', 'well yes!!!', '2025-04-01 20:27:33');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('New','In Progress','Complete') NOT NULL DEFAULT 'New',
  `priority` enum('Low','Moderate','Urgent') NOT NULL DEFAULT 'Moderate',
  `due_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `description`, `status`, `priority`, `due_date`, `created_at`, `created_by`) VALUES
(1, 'PinkPantheress', 'The world is my oyster and im the only...girl!!!!!', 'Complete', 'Moderate', '2025-04-22', '2025-04-02 12:50:02', ''),
(2, 'High FASHION', 'I\'D RATHER GET RATHER GET GET', 'New', 'Moderate', '2025-04-17', '2025-04-02 12:50:02', ''),
(3, 'Take my Hand', 'Well yes', 'In Progress', 'Low', '2025-04-30', '2025-04-02 12:50:02', ''),
(4, 'I want to go there', 'Yes yes yes well', 'New', 'Moderate', '2025-04-09', '2025-04-02 12:50:02', ''),
(5, '1 Thing', 'Amerie!', 'In Progress', 'Low', '2025-04-17', '2025-04-02 12:51:03', ''),
(6, 'Restless', 'EVIL NINE\r\nBORN TOO SLOW', 'New', 'Moderate', '2025-04-23', '2025-04-02 13:14:23', 'auth0|67e80674b5ac5df6f28de6cd');

-- --------------------------------------------------------

--
-- Table structure for table `project_archive`
--

CREATE TABLE `project_archive` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `status` enum('New','In Progress','Complete') NOT NULL,
  `priority` enum('Low','Moderate','Urgent') NOT NULL,
  `description` text NOT NULL,
  `edited_by` varchar(255) NOT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_archive`
--

INSERT INTO `project_archive` (`id`, `project_id`, `project_name`, `status`, `priority`, `description`, `edited_by`, `archived_at`, `created_at`, `due_date`) VALUES
(1, 1, 'Project One', 'New', 'Moderate', 'The world is my oyster and im the only...girl', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-01 21:08:45', '2025-04-01 21:08:45', NULL),
(2, 1, 'PinkPantheress', 'New', 'Moderate', 'The world is my oyster and im the only...girl', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:03:17', '2025-04-02 11:03:17', NULL),
(3, 2, 'High FASHION', 'New', 'Moderate', 'I\'D RATHER GET RATHER GET', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:25:21', '2025-04-02 11:25:21', NULL),
(4, 3, 'Take my Hand', 'In Progress', 'Low', 'Well yes', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:30:05', '2025-04-02 11:30:05', '2025-04-29'),
(5, 1, 'PinkPantheress', 'New', 'Moderate', 'The world is my oyster and im the only...girl!!!', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:34:57', '2025-04-02 11:34:57', NULL),
(6, 1, 'PinkPantheress', 'New', 'Moderate', 'The world is my oyster and im the only...girl!!!', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:35:07', '2025-04-02 11:35:07', '2025-04-25'),
(7, 2, 'High FASHION', 'New', 'Moderate', 'I\'D RATHER GET RATHER GET', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:40:04', '2025-04-02 11:40:04', '2025-04-16'),
(8, 2, 'High FASHION', 'New', 'Moderate', 'I\'D RATHER GET RATHER GET', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 11:40:47', '2025-04-02 11:40:47', '2025-04-17'),
(9, 4, 'I want to go there', 'New', 'Moderate', 'Yes yes yes', '', '2025-04-02 11:48:18', '2025-04-02 11:48:18', '2025-04-09'),
(10, 5, '1 Thing', 'In Progress', 'Low', 'Amerie', '', '2025-04-02 11:51:13', '2025-04-02 11:51:03', '2025-04-17'),
(11, 1, 'PinkPantheress', 'New', 'Moderate', 'The world is my oyster and im the only...girl!!!', '', '2025-04-02 12:01:11', '2025-04-02 11:50:02', '2025-04-22'),
(12, 1, 'PinkPantheress', 'New', 'Moderate', 'The world is my oyster and im the only...girl!!!!', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 12:03:00', '2025-04-02 11:50:02', '2025-04-22'),
(13, 1, 'PinkPantheress', 'New', 'Moderate', 'The world is my oyster and im the only...girl!!!!!', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 12:06:42', '2025-04-02 11:50:02', '2025-04-22'),
(14, 6, 'Restless', 'New', 'Moderate', 'EVIL NINE', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 12:14:58', '2025-04-02 12:14:23', '2025-04-09');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `status` enum('New','In Progress','Complete') DEFAULT 'New',
  `priority` enum('Low','Moderate','Urgent') DEFAULT 'Low',
  `created_by` varchar(255) NOT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `subject`, `description`, `project_id`, `status`, `priority`, `created_by`, `due_date`, `created_at`) VALUES
(1, 'XCX WORLD', '1. Do you wanna come to my party?\r\n2. Taxi\r\n3. I wanna be with you', 1, 'Complete', 'Moderate', '', NULL, '2025-04-02 12:50:02'),
(2, 'LG7', 'I can be your boyfriend for the evening\r\nYou can be my boyfriend for the night', 1, 'New', 'Low', '', NULL, '2025-04-02 12:50:02'),
(3, 'C.R.A.S.H', '360 featuring robyn and yung lean', 1, 'In Progress', 'Low', 'tougereddesign', NULL, '2025-04-02 12:50:02'),
(4, 'Puppy', 'Well yes', 1, 'In Progress', 'Low', 'auth0|67e80674b5ac5df6f28de6cd', NULL, '2025-04-02 12:50:02'),
(5, 'Hey tinashe wanna do this song', 'b2b b2b', 2, 'In Progress', 'Moderate', 'auth0|67e80674b5ac5df6f28de6cd', NULL, '2025-04-02 12:50:02'),
(6, 'Vogue', 'Madonna', 5, 'In Progress', 'Moderate', 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00', '2025-04-02 12:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `task_archive`
--

CREATE TABLE `task_archive` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `edited_by` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `archived_at` datetime DEFAULT current_timestamp(),
  `subject` varchar(255) NOT NULL,
  `status` enum('New','In Progress','Complete') NOT NULL,
  `priority` enum('Low','Moderate','Urgent') NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_archive`
--

INSERT INTO `task_archive` (`id`, `task_id`, `edited_by`, `created_at`, `archived_at`, `subject`, `status`, `priority`, `description`) VALUES
(1, 5, 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00 00:00:00', '2025-04-01 20:45:35', 'Hey tinashe wanna do this song', 'In Progress', 'Moderate', 'b2b'),
(2, 3, 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00 00:00:00', '2025-04-01 21:24:40', 'C.R.A.S.H', 'In Progress', 'Low', 'YUCK'),
(3, 5, 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00 00:00:00', '2025-04-02 12:38:24', 'Hey tinashe wanna do this song', 'In Progress', 'Moderate', 'b2b b2b'),
(4, 1, 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 12:50:02', '2025-04-02 12:54:55', 'XCX WORLD', 'In Progress', 'Moderate', 'Do you wanna come to my party?'),
(5, 1, 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 12:50:02', '2025-04-02 12:55:47', 'XCX WORLD', 'In Progress', 'Moderate', 'Do you wanna come to my party?\r\nTaxi'),
(6, 1, 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 12:50:02', '2025-04-02 13:28:17', 'XCX WORLD', 'In Progress', 'Moderate', '1. Do you wanna come to my party?\r\n2. Taxi\r\n3. I wanna be with you');

-- --------------------------------------------------------

--
-- Table structure for table `task_assigned_users`
--

CREATE TABLE `task_assigned_users` (
  `task_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_assigned_users`
--

INSERT INTO `task_assigned_users` (`task_id`, `user_id`) VALUES
(1, 'auth0|67e80674b5ac5df6f28de6cd'),
(2, 'auth0|67e80674b5ac5df6f28de6cd'),
(3, 'auth0|67ebc67a8b1c1b0d6ab27149'),
(4, 'auth0|67e80674b5ac5df6f28de6cd'),
(4, 'auth0|67ebc67a8b1c1b0d6ab27149'),
(5, 'auth0|67e80674b5ac5df6f28de6cd'),
(5, 'auth0|67ebc67a8b1c1b0d6ab27149'),
(6, 'auth0|67ebc67a8b1c1b0d6ab27149');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_archive`
--
ALTER TABLE `project_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `task_archive`
--
ALTER TABLE `task_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `task_assigned_users`
--
ALTER TABLE `task_assigned_users`
  ADD PRIMARY KEY (`task_id`,`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `project_archive`
--
ALTER TABLE `project_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `task_archive`
--
ALTER TABLE `task_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_archive`
--
ALTER TABLE `task_archive`
  ADD CONSTRAINT `task_archive_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_assigned_users`
--
ALTER TABLE `task_assigned_users`
  ADD CONSTRAINT `task_assigned_users_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
