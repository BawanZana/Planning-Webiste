-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 16, 2022 at 01:46 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `planning`
--

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `boards` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `flag` int(11) DEFAULT 1,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`id`, `name`, `flag`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'updated3 board', 1, 8, '2022-08-14 20:32:57', '2022-08-16 09:16:49'),
(3, 'board test', 1, 8, '2022-08-15 21:20:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `labels`
--

CREATE TABLE `labels` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `flag` int(11) DEFAULT 1,
  `user_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `labels`
--

INSERT INTO `labels` (`id`, `name`, `flag`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'important', 1, 1, '2022-08-15 14:51:44', NULL),
(3, 'Personal', 1, 1, '2022-08-15 15:33:19', NULL),
(4, 'update', 1, 8, '2022-08-15 21:55:20', '2022-08-16 09:55:46');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` bigint(20) NOT NULL,
  `task_id` bigint(20) DEFAULT NULL,
  `task` varchar(100) DEFAULT NULL,
  `user` varchar(100) DEFAULT NULL,
  `action` varchar(300) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `task_id`, `task`, `user`, `action`, `created_at`, `updated_at`) VALUES
(1, 1, 'first task', 'bawan', 'insert', '2022-08-14 23:37:14', NULL),
(2, 2, 'second task', 'bawan', 'insert', '2022-08-14 23:39:59', NULL),
(3, 3, 'second task', 'bawan', 'insert', '2022-08-14 23:40:40', NULL),
(4, 4, 'second task', 'bawan', 'insert', '2022-08-14 23:41:48', NULL),
(5, NULL, 'second task', 'bawan', 'insert', '2022-08-14 23:42:20', NULL),
(6, 6, 'second task', 'bawan', 'insert', '2022-08-14 23:42:52', NULL),
(7, 7, 'second task', 'bawan', 'insert', '2022-08-14 23:43:43', NULL),
(8, NULL, 'second task', 'bawan', 'insert', '2022-08-14 23:44:43', NULL),
(9, NULL, 'second task', 'bawan', 'insert', '2022-08-14 23:45:12', NULL),
(10, 10, 'test task', 'bawan', 'insert', '2022-08-15 12:52:22', NULL),
(11, NULL, 'test task', 'bawan', 'insert', '2022-08-15 13:23:53', NULL),
(14, NULL, 'second task', 'bawan zana', 'delete', '2022-08-15 14:31:34', NULL),
(15, 12, 'test task2', 'bawan zana', 'insert', '2022-08-15 17:22:22', NULL),
(16, 13, 'test again', 'bawan zana', 'insert', '2022-08-15 18:42:51', NULL),
(17, NULL, 'second task', 'bawan', 'delete', '2022-08-15 19:29:11', NULL),
(18, 10, 'updated task', 'bawan zana', 'update', '2022-08-15 22:19:55', NULL),
(19, 14, 'import', 'bawan zana', 'insert', '2022-08-15 22:21:23', NULL),
(20, NULL, 'import444', 'bawan zana', 'insert', '2022-08-15 22:54:29', NULL),
(21, NULL, 'import444', 'bawan zana', 'update', '2022-08-15 22:55:27', NULL),
(22, NULL, 'updated task44', 'bawan zana', 'delete', '2022-08-15 22:56:13', NULL),
(23, 14, 'import', 'bawan', 'update', '2022-08-15 23:21:33', NULL),
(24, 2, 'second task', 'bawan', 'update', '2022-08-15 23:30:33', NULL),
(25, 3, 'second task', 'bawan', 'update', '2022-08-15 23:33:27', NULL),
(26, 3, 'task3', 'bawan', 'update', '2022-08-15 23:33:48', NULL),
(27, 3, 'task3', 'bawan', 'update', '2022-08-15 23:34:49', NULL),
(28, 3, 'task3', 'bawan', 'update', '2022-08-15 23:34:58', NULL),
(29, 4, 'second task', 'bawan', 'update', '2022-08-15 23:35:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `flag` int(11) DEFAULT 1,
  `user_id` bigint(20) DEFAULT NULL,
  `board_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `name`, `description`, `flag`, `user_id`, `board_id`, `created_at`, `updated_at`) VALUES
(1, 'to-do', 'updated2', 1, 1, 1, '2022-08-14 20:36:22', '2022-08-15 00:13:29'),
(2, 'in-progress', 'test', 1, 1, 1, '2022-08-15 12:25:00', NULL),
(3, 'dev-review', 'test', 1, 1, 1, '2022-08-15 12:25:13', NULL),
(4, 'testing', 'test', 1, 1, 1, '2022-08-15 12:25:32', NULL),
(5, 'done', 'test', 1, 1, 1, '2022-08-15 12:25:58', NULL),
(6, 'close', 'test', 1, 1, 1, '2022-08-15 12:26:04', NULL),
(8, 'testing url', 'updated3', 1, 1, 3, '2022-08-15 21:23:28', '2022-08-15 22:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `flag` int(11) DEFAULT 1,
  `due_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `status_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `name`, `description`, `flag`, `due_date`, `image`, `user_id`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 'first task', 'just test', 1, '0000-00-00', '91f9325c615696de00e46bc2dda813f5.png', 1, 3, '2022-08-14 23:37:14', NULL),
(2, 'export', 'test', 1, '2022-08-14', 'c4f1be951b741c4fafa943ea7e84d66d.jpg', 4, 4, '2022-08-14 23:39:59', '2022-08-15 23:30:33'),
(3, 'task3', 'test', 1, '2022-08-14', '206c11f08cc81b7117acce45d26cab0b.jpg', 8, 3, '2022-08-14 23:40:40', '2022-08-15 23:34:58'),
(4, 'task3', 'test', 1, '2022-08-14', '7d6746908a13a55bc40e4ddd410cafc0.jpg', 8, 3, '2022-08-14 23:41:48', '2022-08-15 23:35:26'),
(6, 'second task', 'just test', 1, '0000-00-00', 'c0f9f626bb86f65c0685a167f7b1ef39.png', 1, 1, '2022-08-14 23:42:52', NULL),
(7, 'second task', 'just test', 1, '0000-00-00', '6222531659350642ac6a3a248a240e3f.png', 1, 5, '2022-08-14 23:43:43', NULL),
(10, 'updated task', 'test', 1, '2022-08-14', 'd1d54cc1b0144cc28bc896a758370eea.jpg', 1, 1, '2022-08-15 12:52:22', '2022-08-15 22:19:55'),
(12, 'test task2', 'test', 0, '2022-08-15', '8f1abeb1afce38d345df81595867dc5e.png', 1, 1, '2022-08-15 17:22:22', NULL),
(13, 'test again', 'test', 0, '2022-08-15', '18824986bcfe434221905dd33033e505.png', 1, 1, '2022-08-15 18:42:51', NULL),
(14, 'import44', 'test', 1, '2022-08-14', 'b5e178320d1c02e9ee5d6985aaf0f189.jpg', 4, 4, '2022-08-15 22:21:23', '2022-08-15 23:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `tasks_labels`
--

CREATE TABLE `tasks_labels` (
  `id` bigint(20) NOT NULL,
  `task_id` bigint(20) DEFAULT NULL,
  `label_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tasks_labels`
--

INSERT INTO `tasks_labels` (`id`, `task_id`, `label_id`, `created_at`, `updated_at`) VALUES
(1, 10, 1, '2022-08-15 14:57:47', NULL),
(4, 1, 1, '2022-08-15 15:39:52', NULL),
(5, 3, 3, '2022-08-15 21:56:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_type` int(11) DEFAULT 1,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `user_type`, `password`, `created_at`, `updated_at`) VALUES
(1, 'bawan zana', 'bawanzana@gmail.com', 1, '$2y$10$IuBr6TqxCpNxL2IuahIcJOi4DytSRlJ8Xb8vaDD7T6JJkr7qwRsmi', '2022-08-14 19:08:53', '2022-08-14 22:31:13'),
(4, 'bawan', 'bawanzana4@gmail.com', 2, '$2y$10$TL8GDhOzm9SWpfSNCXeWP.qH0EM153D/PDsR6dHIqCLhKIj87Oso6', '2022-08-15 18:51:55', NULL),
(8, 'bawan', 'bawanzana444@gmail.com', 3, '$2y$10$5JYxRMaD1q64jiovCtbLt.J/hJyvyO4BAAtZKb.6mjCk/WIQvwyOC', '2022-08-15 19:14:10', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `labels`
--
ALTER TABLE `labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `board_id` (`board_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `tasks_labels`
--
ALTER TABLE `tasks_labels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `label_id` (`label_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boards`
--
ALTER TABLE `boards`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `labels`
--
ALTER TABLE `labels`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tasks_labels`
--
ALTER TABLE `tasks_labels`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `boards`
--
ALTER TABLE `boards`
  ADD CONSTRAINT `boards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `labels`
--
ALTER TABLE `labels`
  ADD CONSTRAINT `labels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `statuses`
--
ALTER TABLE `statuses`
  ADD CONSTRAINT `statuses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `statuses_ibfk_2` FOREIGN KEY (`board_id`) REFERENCES `boards` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tasks_labels`
--
ALTER TABLE `tasks_labels`
  ADD CONSTRAINT `tasks_labels_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_labels_ibfk_2` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
