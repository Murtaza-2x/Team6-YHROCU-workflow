-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2025 at 01:54 PM
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
(2, 1, 'auth0|67e80674b5ac5df6f28de6cd', 'i got nothing on but the radio', '2025-04-01 11:01:52');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('New','In Progress','Complete') NOT NULL DEFAULT 'New',
  `priority` enum('Low','Moderate','Urgent') NOT NULL DEFAULT 'Moderate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `description`, `status`, `priority`) VALUES
(1, 'Project One', 'The world is my oyster and im the only...girl', 'New', 'Moderate');

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
  `created_by` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `subject`, `description`, `project_id`, `status`, `priority`, `created_by`) VALUES
(1, 'XCX WORLD', 'Do you wanna come to my party?', 1, 'In Progress', 'Moderate', ''),
(2, 'LG7', 'I can be your boyfriend for the evening\r\nYou can be my boyfriend for the night', 1, 'New', 'Low', ''),
(3, 'C.R.A.S.H', 'YUCK', 1, 'In Progress', 'Low', 'tougereddesign'),
(4, 'Puppy', 'Well yes', 1, 'In Progress', 'Low', 'auth0|67e80674b5ac5df6f28de6cd'),
(5, 'Hey tinashe wanna do this song', 'b2b', 1, 'In Progress', 'Moderate', 'auth0|67e80674b5ac5df6f28de6cd');

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
(5, 'auth0|67ebc67a8b1c1b0d6ab27149');

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
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- Constraints for table `task_assigned_users`
--
ALTER TABLE `task_assigned_users`
  ADD CONSTRAINT `task_assigned_users_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
