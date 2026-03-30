-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2026 at 05:03 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `comp1044`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password_hash`, `full_name`) VALUES
(1, 'admin1', '$2y$10$aAc8wOnV.BRdeCDC9TdhBOzJTcJR8FtErl9tZNRiVw0BOpQpMckBa', 'System Administrator');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `assessment_id` int(11) NOT NULL,
  `internship_id` int(11) NOT NULL,
  `tasks_score` decimal(5,2) NOT NULL,
  `health_safety_score` decimal(5,2) NOT NULL,
  `theory_score` decimal(5,2) NOT NULL,
  `presentation_score` decimal(5,2) NOT NULL,
  `clarity_score` decimal(5,2) NOT NULL,
  `lifelong_learning_score` decimal(5,2) NOT NULL,
  `project_management_score` decimal(5,2) NOT NULL,
  `time_management_score` decimal(5,2) NOT NULL,
  `qualitative_comments` text,
  `final_score` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessment_id`, `internship_id`, `tasks_score`, `health_safety_score`, `theory_score`, `presentation_score`, `clarity_score`, `lifelong_learning_score`, `project_management_score`, `time_management_score`, `qualitative_comments`, `final_score`) VALUES
(1, 1, '85.00', '90.00', '80.00', '88.00', '85.00', '92.00', '80.00', '85.00', 'Alice showed excellent technical skills but could improve on project tracking documentation.', '85.75');

-- --------------------------------------------------------

--
-- Table structure for table `assessors`
--

CREATE TABLE `assessors` (
  `assessor_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assessors`
--

INSERT INTO `assessors` (`assessor_id`, `username`, `password_hash`, `full_name`) VALUES
(1, 'assessor1', '$2y$10$CNK.pSZmJLJgiCAvEh7WzuZgTn.xg0DHbdUHgYbT17KDExZoCkdBS', 'Dr. Alan Smith'),
(2, 'assessor2', '$2y$10$CNK.pSZmJLJgiCAvEh7WzuZgTn.xg0DHbdUHgYbT17KDExZoCkdBS', 'Prof. Sarah Jones');

-- --------------------------------------------------------

--
-- Table structure for table `internships`
--

CREATE TABLE `internships` (
  `internship_id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `assessor_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `internships`
--

INSERT INTO `internships` (`internship_id`, `student_id`, `assessor_id`, `company_name`) VALUES
(1, 'S1001', 1, 'Tech Innovations Inc.'),
(2, 'S1002', 1, 'Data Driven Solutions'),
(3, 'S1003', 2, 'Creative Web Agency');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` varchar(20) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `programme` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `programme`) VALUES
('S1001', 'Alice Johnson', 'BSc Computer Science'),
('S1002', 'Bob Williams', 'BSc Software Engineering'),
('S1003', 'Charlie Brown', 'BSc Information Technology');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`assessment_id`),
  ADD UNIQUE KEY `internship_id` (`internship_id`);

--
-- Indexes for table `assessors`
--
ALTER TABLE `assessors`
  ADD PRIMARY KEY (`assessor_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `internships`
--
ALTER TABLE `internships`
  ADD PRIMARY KEY (`internship_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `assessor_id` (`assessor_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `assessment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assessors`
--
ALTER TABLE `assessors`
  MODIFY `assessor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `internships`
--
ALTER TABLE `internships`
  MODIFY `internship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`internship_id`) REFERENCES `internships` (`internship_id`) ON DELETE CASCADE;

--
-- Constraints for table `internships`
--
ALTER TABLE `internships`
  ADD CONSTRAINT `internships_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `internships_ibfk_2` FOREIGN KEY (`assessor_id`) REFERENCES `assessors` (`assessor_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
