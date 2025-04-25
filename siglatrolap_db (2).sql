-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 25, 2025 at 09:39 AM
-- Server version: 5.7.34
-- PHP Version: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `siglatrolap_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee') NOT NULL DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `username` varchar(100) DEFAULT NULL,
  `user_id` int(100) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `check_in_time` time DEFAULT NULL,
  `check_out_time` time DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'On Time',
  `leave_type` varchar(50) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `reason` text,
  `stat` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `created_at`, `username`, `user_id`, `date`, `check_in_time`, `check_out_time`, `status`, `leave_type`, `start_date`, `end_date`, `reason`, `stat`) VALUES
(39, 'admin', 'dmins@gmail.com', '$2y$10$DuZhv03BTmNiT5Hnukjko.bgkalBsjF8ems9qR5kBC57KdOjyk1Mu', 'admin', '2025-03-25 17:00:00', 'adminer', 0, NULL, NULL, NULL, 'On Time', '', '0000-00-00', '0000-00-00', '', 'rejected'),
(40, 'admin2', 'admirers@gmail.com', '$2y$10$qfSB9nAIGsYmeTSaBKzuxOAw1JBLklTbCaI3LWZGzxx4ubbyp1jgu', 'admin', '2025-03-25 17:01:41', 'adminer2', 0, NULL, NULL, NULL, 'On Time', '', '0000-00-00', '0000-00-00', '', 'rejected'),
(44, 'joshua sierra', 'joshuasierra725@gmail.com', '$2y$10$/u1U5g6G31qFya2votoYOuyaSuVXMIiXQyzhRtM31/gWIG5RmKrdu', 'employee', '2025-04-25 02:48:47', 'Josh', NULL, NULL, NULL, NULL, 'On Time', NULL, NULL, NULL, NULL, NULL),
(45, 'joshua sierra', NULL, '', 'employee', '2025-04-25 02:49:13', '', 44, '2025-04-25', '04:49:13', NULL, 'Under Time', NULL, NULL, NULL, NULL, 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
