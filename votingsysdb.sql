-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2025 at 10:06 AM
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
-- Database: `votingsysdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_db`
--

CREATE TABLE `admin_db` (
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_username` varchar(11) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_security` int(11) NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_db`
--

INSERT INTO `admin_db` (`admin_id`, `admin_email`, `admin_username`, `admin_password`, `admin_security`, `date_created`) VALUES
(2, 'admin@votesys.com', 'Admin', '$2y$10$6ZI/ikWFOrZQmyox31AMMehJ3dZelw3YnaXz7RDS1JqEj2WEAipaS', 1234, '2025-07-17');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id_num` int(11) NOT NULL,
  `candidate_name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `platform` text NOT NULL,
  `photo` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `vote_count` int(11) NOT NULL DEFAULT 0,
  `partylist` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id_num`, `candidate_name`, `position`, `platform`, `photo`, `date_created`, `vote_count`, `partylist`) VALUES
(1, 'Andrei Lazaro', 'President', 'Platform here', '6879e776590d3_Andrei Lazaro.jpg', '2025-07-18 06:19:34', 0, '2'),
(2, 'Bea Alcantara', 'President', 'Platform here', '6879e788481a5_Bea Alcantara.png', '2025-07-18 06:19:52', 0, '1'),
(3, 'Carmela Tolentino', 'Vice President', 'Platform here', '6879e79fe820f_Carmela Tolentino.jpg', '2025-07-18 06:20:15', 0, '2'),
(4, 'Dexter Ramos', 'Vice President', 'Platform here', '6879e7b94eb13_Dexter Ramos.jpg', '2025-07-18 06:20:41', 0, '1'),
(5, 'Erik Santiago', 'Secretary', 'Platform here', '6879e7f799729_Erika Santiago.jpg', '2025-07-18 06:21:43', 0, '2'),
(6, 'Francis Dela Cruz', 'Secretary', 'Platform here', '6879e8198bd07_Francis Dela Cruz.jpg', '2025-07-18 06:22:17', 0, '1'),
(7, 'Gwen Soriano', 'Treasurer', 'Platform here', '6879e85d93dee_Gwen Soriano.jpg', '2025-07-18 06:23:25', 0, '2'),
(8, 'Harold Yambao', 'Treasurer', 'Platform here', '6879e8797ccd9_Harold Yambao.jpg', '2025-07-18 06:23:53', 0, '1'),
(9, 'Ivy Cordero', 'Auditor', 'Platform here', '6879e8a33d751_Ivy Cordero.jpg', '2025-07-18 06:24:35', 0, '2'),
(10, 'Julius Magpantay', 'Auditor', 'Platform here', '6879e8d0c7fa1_Julius Magpantay.jpg', '2025-07-18 06:25:20', 0, '1'),
(11, 'Kristine Abad', 'PRO', 'Platform here', '6879e8f01e682_Kristine Abad.jpg', '2025-07-18 06:25:52', 0, '2'),
(12, 'Leo Salcedo', 'PRO', 'Platform here', '6879e91b16c85_Leo Salcedo.jpg', '2025-07-18 06:26:35', 0, '1');

-- --------------------------------------------------------

--
-- Table structure for table `party_list`
--

CREATE TABLE `party_list` (
  `partylist_id` int(11) NOT NULL,
  `partylist_name` varchar(255) NOT NULL,
  `partylist_image` varchar(255) DEFAULT NULL,
  `dateCreated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `party_list`
--

INSERT INTO `party_list` (`partylist_id`, `partylist_name`, `partylist_image`, `dateCreated`) VALUES
(1, 'Bayan Muna Partylist', 'Bayan Muna Partylist.png', '2025-07-18'),
(2, '4Ps Partylist', '4Ps Partylist.png', '2025-07-18');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `position_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `position_name`) VALUES
(1, 'President'),
(2, 'Vice President'),
(3, 'Secretary'),
(4, 'Treasurer'),
(5, 'Auditor'),
(6, 'PRO');

-- --------------------------------------------------------

--
-- Table structure for table `user_information`
--

CREATE TABLE `user_information` (
  `id_number` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `date_created` date NOT NULL DEFAULT current_timestamp(),
  `hasVoted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_information`
--

INSERT INTO `user_information` (`id_number`, `first_name`, `last_name`, `middle_name`, `gender`, `email`, `username`, `password`, `role`, `date_created`, `hasVoted`) VALUES
(2, 'Rafhielle Allen', 'Alcabaza', 'Madrid', 'Male', '202310828@fit.edu.ph', 'Allen', '$2y$10$5cNcunlwGEVlusoOjV61wOiyL2OXLCPjVwFOqw7KJQ4hYbSH97Uru', '', '2025-07-18', 0),
(3, 'Rovic Christopher', 'Sarthou', 'Maniego', 'Male', '202312219@fit.edu.ph', 'Sarts', '$2y$10$BVO9uY0z.uQ8LtYDotO5IuEWTX/TzMiq/EI.haz2oKdquEWUCOGLu', '', '2025-07-18', 0),
(4, 'Breindelle Vincent', 'Ayuso', 'M', 'Male', '202312345@fit.edu.ph', 'Breindelle', '$2y$10$iZPdS/UEEBCDyhSSaUaZhum2LWQtdFSt69E4IwClXyEuZSaoZBlSO', '', '2025-07-18', 0),
(5, 'Marie Antoinette', 'Arenas', 'A', 'Female', '202357395@fit.edu.ph', 'Toni', '$2y$10$ultmztT80ZQSO4t/FY/Dj.FNx8HWJDzNLLH6vMcbj94CC0LvnoVqO', '', '2025-07-18', 0),
(6, 'Alyana Julia', 'Coronel', 'Middle', 'Female', '202386945@fit.edu.ph', 'Julia', '$2y$10$tuopJSVeh3RM/VsRxR9vHO9jumrze4bqmVpa.oeU9SYXN9oYB9pTu', '', '2025-07-18', 0),
(7, 'Angelo Effiel Xuncti', 'Esguerra', 'Middle', 'Male', '202376954@fit.edu.ph', 'Axel', '$2y$10$g4mB99LBx/pzo/OBnAjjIuH0GYETRx6.A..OWgYZGbmJ7VzSdHx6C', '', '2025-07-18', 0),
(8, 'Ryan Claver', 'Del Rosario', 'Middle', 'Male', '202368596@fit.edu.ph', 'Ryan', '$2y$10$MuKoRXpg6TbwVvasHQU7Hu8Xdr24S7vaeF81wIgR1O1DxkiY5eVYC', '', '2025-07-18', 0),
(9, 'Sean', 'Nieves', 'Naelgas', 'Male', '202311382@fit.edu.ph', 'Sean', '$2y$10$v7QsXtoXkOsshO4fMlrvzO.dCpgCLuIiWbuvGLOgXT5Ocy/N7zsL2', '', '2025-07-18', 0),
(10, 'Paul', 'Naelgas', 'Nieves', 'Male', '2023113382@fit.edu.ph', 'Paul', '$2y$10$aiFfRBz9fvGbjYryTh3jeezEIgnnPZ.Tl7WTA92fsYuaInM9bIBqG', '', '2025-07-18', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_db`
--
ALTER TABLE `admin_db`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id_num`);

--
-- Indexes for table `party_list`
--
ALTER TABLE `party_list`
  ADD PRIMARY KEY (`partylist_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `user_information`
--
ALTER TABLE `user_information`
  ADD PRIMARY KEY (`id_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_db`
--
ALTER TABLE `admin_db`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id_num` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `party_list`
--
ALTER TABLE `party_list`
  MODIFY `partylist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_information`
--
ALTER TABLE `user_information`
  MODIFY `id_number` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
