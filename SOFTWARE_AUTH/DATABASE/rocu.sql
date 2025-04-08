-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 08, 2025 at 09:20 PM
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
(27, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, updated a thing inside this task. Needs to be checked by a Manager.', '2025-04-08 19:09:53'),
(28, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, removed a thing inside this task.', '2025-04-08 19:10:09'),
(29, 21, 'auth0|67f274cac75549971ee489a2', 'Example Comment 3, checked something inside this task, need update from another staff member.', '2025-04-08 19:12:43'),
(30, 23, 'auth0|67f274cac75549971ee489a2', 'Example Comment 5, not sure what to do next?', '2025-04-08 19:16:03'),
(31, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, testing in this task.', '2025-04-08 19:19:35');

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
(10, 'Example Project 1', 'Example Project 1\r\n\r\nEdit & Archive & Change Date\r\n\r\nTest Filter', 'New', 'Low', '2025-04-25', '2025-04-08 20:05:48', 'auth0|67f274cac75549971ee489a2'),
(11, 'Example Project 2', 'Example Project 2', 'In Progress', 'Moderate', '2025-04-13', '2025-04-08 20:06:04', 'auth0|67f274cac75549971ee489a2');

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
  `due_date` date DEFAULT NULL,
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_archive`
--

INSERT INTO `project_archive` (`id`, `project_id`, `project_name`, `status`, `priority`, `description`, `edited_by`, `archived_at`, `created_at`, `due_date`, `created_by`) VALUES
(19, 10, 'Example Project 1', 'New', 'Low', 'Example Project 1', 'auth0|67f274cac75549971ee489a2', '2025-04-08 19:13:52', '2025-04-08 19:05:48', '2025-04-10', 'auth0|67f274cac75549971ee489a2'),
(20, 10, 'Example Project 1', 'New', 'Low', 'Example Project 1\r\n\r\nEdit & Archive & Change Date', 'auth0|67f274cac75549971ee489a2', '2025-04-08 19:14:08', '2025-04-08 19:05:48', '2025-04-25', 'auth0|67f274cac75549971ee489a2');

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
(19, 'Example Task 1', 'Example Task 1\r\n\r\nEdit & Archive Test', 10, 'New', 'Low', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:06:27'),
(20, 'Example Task 2', 'Example Task 2', 11, 'Complete', 'Urgent', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:07:14'),
(21, 'Example Task 3', 'Example Task 3\r\n\r\nEdit & Archive', 10, 'In Progress', 'Moderate', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:08:16'),
(22, 'Example Task 4', 'Example Task 4', 11, 'Complete', 'Low', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:14:58'),
(23, 'Example Task 5', 'Example Task 5', 10, 'In Progress', 'Low', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:15:26'),
(24, 'Example Task 6', 'Example Task 6', 10, 'In Progress', 'Moderate', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:16:23'),
(25, 'Example Task 7', 'Example Task 7', 11, 'Complete', 'Urgent', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:16:51'),
(26, 'Example Task 8', 'Example Task 8', 10, 'Complete', 'Urgent', 'auth0|67f274cac75549971ee489a2', '0000-00-00', '2025-04-08 20:17:17');

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
(20, 21, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:08:16', '2025-04-08 20:09:04', 'Example Task 3', 'In Progress', 'Moderate', 'Example Task 3'),
(21, 19, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:06:27', '2025-04-08 20:09:53', 'Example Task 1', 'New', 'Low', 'Example Task 1'),
(22, 19, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:06:27', '2025-04-08 20:10:09', 'Example Task 1', 'New', 'Low', 'Example Task 1'),
(23, 19, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:06:27', '2025-04-08 20:10:39', 'Example Task 1', 'New', 'Low', 'Example Task 1'),
(24, 19, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:06:27', '2025-04-08 20:11:05', 'Example Task 1', 'New', 'Low', 'Example Task 1\r\n\r\nEdit & Archive'),
(25, 21, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:08:16', '2025-04-08 20:12:43', 'Example Task 3', 'In Progress', 'Moderate', 'Example Task 3'),
(26, 21, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:08:16', '2025-04-08 20:13:09', 'Example Task 3', 'In Progress', 'Moderate', 'Example Task 3'),
(27, 23, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:15:26', '2025-04-08 20:16:03', 'Example Task 5', 'In Progress', 'Low', 'Example Task 5'),
(28, 19, 'auth0|67f274cac75549971ee489a2', '2025-04-08 20:06:27', '2025-04-08 20:19:35', 'Example Task 1', 'New', 'Low', 'Example Task 1\r\n\r\nEdit & Archive Test');

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
(19, 'auth0|67f274cac75549971ee489a2'),
(19, 'auth0|67f274f7866a73130dcdc41d'),
(19, 'auth0|67f27505fc259580822d0e72'),
(20, 'auth0|67f274cac75549971ee489a2'),
(20, 'auth0|67f274f7866a73130dcdc41d'),
(20, 'auth0|67f27505fc259580822d0e72'),
(21, 'auth0|67f274cac75549971ee489a2'),
(21, 'auth0|67f274f7866a73130dcdc41d'),
(21, 'auth0|67f27505fc259580822d0e72'),
(22, 'auth0|67f27505fc259580822d0e72'),
(23, 'auth0|67f274f7866a73130dcdc41d'),
(24, 'auth0|67f274f7866a73130dcdc41d'),
(25, 'auth0|67f274cac75549971ee489a2'),
(26, 'auth0|67f274f7866a73130dcdc41d');

-- --------------------------------------------------------

--
-- Table structure for table `task_comment_archive`
--

CREATE TABLE `task_comment_archive` (
  `id` int(11) NOT NULL,
  `archive_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_comment_archive`
--

INSERT INTO `task_comment_archive` (`id`, `archive_id`, `task_id`, `user_id`, `comment`, `created_at`) VALUES
(14, 21, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, updated a thing inside this task. Needs to be checked by a Manager.', '2025-04-08 19:09:53'),
(15, 22, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, updated a thing inside this task. Needs to be checked by a Manager.', '2025-04-08 19:10:09'),
(16, 22, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, removed a thing inside this task.', '2025-04-08 19:10:09'),
(17, 25, 21, 'auth0|67f274cac75549971ee489a2', 'Example Comment 3, checked something inside this task, need update from another staff member.', '2025-04-08 19:12:43'),
(18, 27, 23, 'auth0|67f274cac75549971ee489a2', 'Example Comment 5, not sure what to do next?', '2025-04-08 19:16:03'),
(19, 28, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, updated a thing inside this task. Needs to be checked by a Manager.', '2025-04-08 19:19:35'),
(20, 28, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, removed a thing inside this task.', '2025-04-08 19:19:35'),
(21, 28, 19, 'auth0|67f274cac75549971ee489a2', 'Example Comment 1, testing in this task.', '2025-04-08 19:19:35');

-- --------------------------------------------------------

--
-- Table structure for table `test_table`
--

CREATE TABLE `test_table` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `task_comment_archive`
--
ALTER TABLE `task_comment_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test_table`
--
ALTER TABLE `test_table`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `project_archive`
--
ALTER TABLE `project_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `task_archive`
--
ALTER TABLE `task_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `task_comment_archive`
--
ALTER TABLE `task_comment_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `test_table`
--
ALTER TABLE `test_table`
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
