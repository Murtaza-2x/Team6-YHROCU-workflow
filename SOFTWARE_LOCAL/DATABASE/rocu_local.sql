-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2025 at 07:20 PM
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
-- Database: `rocu_local`
--

-- --------------------------------------------------------

--
-- Table structure for table `archive`
--

CREATE TABLE `archive` (
  `archive_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `project_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `priority` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `edited_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archive`
--

INSERT INTO `archive` (`archive_id`, `task_id`, `subject`, `project_id`, `status`, `priority`, `description`, `created_at`, `archived_at`, `edited_by`) VALUES
(1, 1, 'First', 1, 'Complete', 'Urgent', 'First task description\r\n\r\nperiod perrrioddddd gag the tea clock the gag gagggy boots dowwwn yass', '0000-00-00 00:00:00', '2025-03-26 14:06:43', 1),
(3, 1, 'First', 1, 'Complete', 'Urgent', 'K\'s FRP Factory', '2025-03-26 14:10:17', '2025-03-26 14:11:46', 1),
(4, 1, 'First', 1, 'Complete', 'Urgent', 'K\'s FRP Factory', '2025-03-26 14:10:17', '2025-03-26 15:00:12', 1),
(5, 1, 'First', 1, 'In Progress', 'Urgent', 'Goals:\r\n- Finish Login Security\r\n- Write 300 words about Something\r\n- Buy a Chuki 180sx\r\n- Eat 3 bananas', '2025-03-26 14:10:17', '2025-03-26 15:00:40', 1),
(6, 1, 'First', 1, 'In Progress', 'Urgent', 'Goals:\r\n- Finish Login Security\r\n- Write 300 words about Something\r\n- Buy a Chuki 180sx\r\n- Eat 3 bananas\r\n- Listen to 5 songs', '2025-03-26 14:10:17', '2025-03-26 15:01:13', 1),
(7, 1, 'First', 1, 'In Progress', 'Urgent', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2025-03-26 14:10:17', '2025-03-26 15:01:17', 1),
(8, 1, 'First', 1, 'In Progress', 'Urgent', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Yea', '2025-03-26 14:10:17', '2025-03-27 19:35:31', 1),
(9, 1, 'First', 1, 'In Progress', 'Urgent', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Yeaesfsdfyhfrhdrfhdefesf', '2025-03-26 14:10:17', '2025-03-27 20:27:24', 1),
(10, 29, 'Yess', 2, 'Complete', 'Urgent', 'drgirshogserpoihgseg', '2025-03-27 20:32:43', '2025-03-27 20:33:13', 1),
(11, 29, 'Yessssss', 2, 'Complete', 'Urgent', 'drgirshogserpoihgseg', '2025-03-27 20:32:43', '2025-03-27 20:33:50', 1),
(12, 29, 'Yessssss', 2, 'Complete', 'Urgent', 'drgirshogserpoihgseg', '2025-03-27 20:32:43', '2025-03-27 20:33:53', 1),
(13, 29, 'Yessssss', 2, 'Complete', 'Urgent', 'drgirshogserpoihgseg', '2025-03-27 20:32:43', '2025-03-27 20:34:00', 1),
(14, 1, 'First', 1, 'In Progress', 'Urgent', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Yeaesfsdfyhfrhdrfhdefesf', '2025-03-26 14:10:17', '2025-03-28 13:37:50', 1),
(15, 25, 'Third', 3, 'New', 'Moderate', 'Aquamarine Dive into Me', '2025-03-26 14:10:17', '2025-03-28 13:46:22', 1),
(16, 30, 'ORIGIN PLAT LINE 02', 4, 'In Progress', 'Urgent', 'Crazy Gloss style', '2025-03-28 12:53:59', '2025-03-28 13:57:11', 1),
(17, 30, 'ORIGIN PLAT LINE 02', 4, 'In Progress', 'Urgent', 'LISTEN TO ARCA ON SPOTIFY', '2025-03-28 12:53:59', '2025-03-28 14:26:42', 1),
(18, 30, 'ORIGIN PLAT LINE 02', 4, 'In Progress', 'Urgent', 'LISTEN TO ARCA ON SPOTIFY T', '2025-03-28 12:53:59', '2025-03-28 14:30:31', 1),
(19, 30, 'ORIGIN PLAT LINE 02', 4, 'In Progress', 'Urgent', 'LISTEN TO ARCA ON SPOTIFY TT', '2025-03-28 12:53:59', '2025-04-01 17:18:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `task_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 1, 1, 'First comment ever...', '2025-03-26 12:07:25'),
(2, 1, 1, 'i can cure your DISEASE', '2025-03-26 12:10:00'),
(3, 1, 1, 'yass', '2025-03-26 12:16:07'),
(4, 1, 1, 'Heyy :) ', '2025-03-26 21:12:15'),
(5, 1, 3, 'Yooo :D', '2025-03-26 23:05:59'),
(6, 1, 1, 'tfygfhjgghgihu', '2025-03-27 19:35:18'),
(7, 1, 1, 'yes', '2025-03-28 13:32:06'),
(8, 1, 1, 't', '2025-03-28 13:47:16'),
(9, 1, 1, 'well yes', '2025-04-01 17:17:26'),
(10, 1, 1, 'well yes', '2025-04-01 17:17:32'),
(11, 1, 1, 'well yes', '2025-04-01 17:18:17'),
(12, 24, 1, 'yes', '2025-04-01 17:18:22'),
(13, 30, 1, 'come to my party', '2025-04-01 17:18:31');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('New','In Progress','Complete') DEFAULT 'New',
  `priority` enum('Low','Moderate','Urgent') DEFAULT 'Low'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `description`, `status`, `priority`) VALUES
(1, 'Project One', 'Test123', 'New', 'Low'),
(2, 'Project Two', 'I DONT NEED YOUR DRUGS, I\'D RATHER GET, RATHER GET...HIGH FASHION\r\n\r\nI DON\'T NEED YOU', 'In Progress', 'Moderate'),
(3, 'Project Three', 'I\'VE GOT NOTHING ON BUT THE RADIO', 'New', 'Moderate'),
(4, 'Project Four', 'listen to amore on spotify\r\nlisten to amore on spotify\r\nlisten to amore on spotify\r\nlisten to amore on spotify\r\nlisten to amore on spotify\r\nlisten to amore on spotify\r\nlisten to amore on spotify\r\nlisten to amore on spotify', 'New', 'Low'),
(5, 'Project Five', 'its Charli baby', 'New', 'Moderate'),
(6, 'Project Six', 'AR1', 'New', 'Moderate');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `status` set('New','In Progress','Complete','') DEFAULT NULL,
  `priority` set('Low','Moderate','Urgent') DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `subject`, `status`, `priority`, `created_by`, `description`, `project_id`, `created_at`, `updated_at`) VALUES
(1, 'First', 'In Progress', 'Urgent', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Yeaesfsdfyhfrhdrfhdefesf sefsrgsrgh', 1, '2025-03-26 14:10:17', '2025-03-28 13:37:50'),
(24, 'Second', 'New', 'Moderate', 1, 'Threat racing corps japan Threat racing corps japan Threat racing corps japan Threat racing corps japan Threat racing corps japan Threat racing corps japan Threat racing corps japan ', 2, '2025-03-26 14:10:17', '2025-03-26 14:10:17'),
(25, 'Third', 'New', 'Moderate', 1, 'Aquamarine Dive into Me IM A RAY OF LIGHT', 3, '2025-03-26 14:10:17', '2025-03-28 13:46:22'),
(28, 'The boy is mine', 'New', 'Low', 1, 'rgrsgsegsegf', 1, '2025-03-27 20:31:45', '2025-03-27 20:31:45'),
(29, 'Yessssss', 'Complete', 'Urgent', 1, 'drgirshogserpoihgseg', 2, '2025-03-27 20:32:43', '2025-03-27 20:33:13'),
(30, 'ORIGIN PLAT LINE 02', 'In Progress', 'Urgent', 1, 'LISTEN TO ARCA ON SPOTIFY TT', 4, '2025-03-28 12:53:59', '2025-03-28 14:30:31'),
(31, 'A & W', 'In Progress', 'Moderate', 1, 'XCX WORLD', 2, '2025-03-28 13:34:57', '2025-03-28 13:34:57');

-- --------------------------------------------------------

--
-- Table structure for table `task_assigned_users`
--

CREATE TABLE `task_assigned_users` (
  `task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_assigned_users`
--

INSERT INTO `task_assigned_users` (`task_id`, `user_id`) VALUES
(1, 3),
(24, 2),
(24, 3),
(25, 3),
(28, 2),
(29, 2),
(29, 3),
(30, 3),
(31, 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(25) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `clearance` enum('User','Manager','Admin','') DEFAULT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `clearance`, `status`) VALUES
(1, 'johnAdmin', 'johnAdmin@gmail.com', '$2y$10$QqXm9FwYOaMMo.WFjanCwOUmwvwqhzDCSZ1BJC9TRX2eJ6wLzGL6.', 'Admin', 'Active'),
(2, 'joeManager', 'joeManager@gmail.com', '$2y$10$SVr/8MjS73HhyzoNKSImWeVofySdGwxBK4P10Skk3ATMqELaPqo5e', 'Manager', 'Active'),
(3, 'jimUser', 'jimUser@gmail.com', '$2y$10$e.7FdoNpAgrytnLq2kduwOdlXx8RmcqldFa6HZXjH9/k7GhSVfe26', 'User', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`archive_id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_name` (`project_name`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_created_by` (`created_by`),
  ADD KEY `project_id` (`project_id`);

--
-- Indexes for table `task_assigned_users`
--
ALTER TABLE `task_assigned_users`
  ADD PRIMARY KEY (`task_id`,`user_id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archive`
--
ALTER TABLE `archive`
  MODIFY `archive_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `archive`
--
ALTER TABLE `archive`
  ADD CONSTRAINT `archive_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `task_assigned_users`
--
ALTER TABLE `task_assigned_users`
  ADD CONSTRAINT `fk_task` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
