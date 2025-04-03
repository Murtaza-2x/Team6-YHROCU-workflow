-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 02, 2025 at 06:51 PM
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
(5, 7, 'auth0|67e80674b5ac5df6f28de6cd', 'Example Comment', '2025-04-02 15:07:47'),
(6, 7, 'auth0|67e80674b5ac5df6f28de6cd', 'Example Comment 2', '2025-04-02 15:08:16'),
(7, 7, 'auth0|67e80674b5ac5df6f28de6cd', 'Example Comment 3', '2025-04-02 15:08:29'),
(8, 7, 'auth0|67e80674b5ac5df6f28de6cd', 'Example Comment 4', '2025-04-02 15:08:46');

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
(7, 'Project One', 'Example Project', 'In Progress', 'Moderate', '2025-04-24', '2025-04-02 16:05:06', 'auth0|67e80674b5ac5df6f28de6cd');

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
(15, 7, 'Project One', 'New', 'Low', 'Example Project', 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 15:06:56', '2025-04-02 15:05:06', '2025-04-24', 'auth0|67e80674b5ac5df6f28de6cd');

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
(7, 'Example Task One', 'Example Task', 7, 'In Progress', 'Low', 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00', '2025-04-02 16:07:35'),
(8, 'Example Task Two', 'Example Task Two', 7, 'Complete', 'Moderate', 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00', '2025-04-02 16:37:13'),
(9, 'Example Task Three', 'Example Task Three', 7, 'New', 'Urgent', 'auth0|67e80674b5ac5df6f28de6cd', '0000-00-00', '2025-04-02 16:39:04');

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
(7, 7, 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 16:07:35', '2025-04-02 16:09:08', 'Example Task', 'New', 'Moderate', 'Example Task'),
(8, 8, 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 16:37:13', '2025-04-02 16:37:27', 'Example Task Two', 'New', 'Low', 'Example Task Two'),
(9, 8, 'auth0|67e80674b5ac5df6f28de6cd', '2025-04-02 16:37:13', '2025-04-02 16:37:51', 'Example Task Two', 'In Progress', 'Low', 'Example Task Two');

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
(7, 'auth0|67e80674b5ac5df6f28de6cd'),
(8, 'auth0|67e80674b5ac5df6f28de6cd'),
(9, 'auth0|67e80674b5ac5df6f28de6cd');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `project_archive`
--
ALTER TABLE `project_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `task_archive`
--
ALTER TABLE `task_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
