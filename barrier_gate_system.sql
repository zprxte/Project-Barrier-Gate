-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 04:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barrier_gate_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `password` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `password`) VALUES
(660673, 11111111),
(660689, 22222222),
(660765, 33333333),
(661283, 44444444),
(661517, 55555555);

-- --------------------------------------------------------

--
-- Table structure for table `car_info`
--

CREATE TABLE `car_info` (
  `car_id` varchar(50) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `car_type` varchar(50) NOT NULL,
  `status_payment` varchar(50) NOT NULL,
  `privilege_parking` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_info`
--

INSERT INTO `car_info` (`car_id`, `user_id`, `car_type`, `status_payment`, `privilege_parking`) VALUES
('3กร3885', '650206', 'รถยนต์', '1', 0),
('9กถ6765', '650206', 'รถยนต์', '1', 0),
('ILOVEU', '4651', 'มอเตอร์ไซค์', 'Pending', 0),
('ULOVEME', '4651', 'รถยนต์', '1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `executive`
--

CREATE TABLE `executive` (
  `exec_id` int(6) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `executive`
--

INSERT INTO `executive` (`exec_id`, `password`) VALUES
(123456, '123');

-- --------------------------------------------------------

--
-- Table structure for table `parking_sessions`
--

CREATE TABLE `parking_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` varchar(20) NOT NULL,
  `time_in` datetime NOT NULL,
  `time_out` datetime DEFAULT NULL,
  `status` enum('PARKED','COMPLETED') DEFAULT 'PARKED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_sessions`
--

INSERT INTO `parking_sessions` (`id`, `user_id`, `car_id`, `time_in`, `time_out`, `status`) VALUES
(1, 650206, '3กร3885', '2025-11-27 21:43:26', '2025-11-27 21:43:50', 'COMPLETED'),
(2, 4651, 'ULOVEME', '2025-11-27 21:50:07', '2025-11-27 21:50:11', 'COMPLETED'),
(3, 650206, '9กถ6765', '2025-11-27 21:50:13', '2025-11-27 21:50:16', 'COMPLETED'),
(4, 4651, 'ILOVEU', '2025-11-27 21:50:21', '2025-11-27 21:50:23', 'COMPLETED'),
(5, 650206, '3กร3885', '2025-11-27 21:53:32', NULL, 'PARKED');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(6) NOT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_type`, `password`, `reg_date`) VALUES
(4651, 'บุคลากร', '$2y$10$WMsJ5coKT5w83MRi3bzWCOsz2W3kKSXwIxPOXPm9NxFBIDEkJ.0rW', '2025-11-27 14:09:39'),
(650206, 'นักศึกษา', '$2y$10$hLc1WJpQK3hz2uKJvhTIAONfV4aFyr4K33DAMT6LD.LqECBoqs7j2', '2025-11-27 14:12:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `car_info`
--
ALTER TABLE `car_info`
  ADD PRIMARY KEY (`car_id`);

--
-- Indexes for table `executive`
--
ALTER TABLE `executive`
  ADD PRIMARY KEY (`exec_id`);

--
-- Indexes for table `parking_sessions`
--
ALTER TABLE `parking_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parking_sessions`
--
ALTER TABLE `parking_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
