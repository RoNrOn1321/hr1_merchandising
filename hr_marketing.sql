-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 06:12 AM
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
-- Database: `hr_marketing`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `status` varchar(100) DEFAULT 'Under Review',
  `image_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `name`, `position`, `status`, `image_url`, `created_at`) VALUES
(2, 'earth', 'soil', 'Shortlisted', '/hr1_merchandising/uploads/applicants/app_1757388285_960d2186.png', '2025-09-09 03:24:45');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `feedback_text` text NOT NULL,
  `evaluator` varchar(255) DEFAULT 'Admin',
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `employee_name`, `department`, `rating`, `feedback_text`, `evaluator`, `date`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'Engineering', 1, 'Great work on the latest project!', 'Admin', '2025-09-09', '2025-09-09 03:50:17', '2025-09-09 04:02:56'),
(2, 'John Doe2', 'qeqw', 5, 'sadsa', 'Admin', '2025-09-09', '2025-09-09 04:02:45', '2025-09-09 04:02:45');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `candidate_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `interview_date` date NOT NULL,
  `interview_time` time NOT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`id`, `candidate_name`, `position`, `interview_date`, `interview_time`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Michael Johnson', 'hitmen', '2025-09-18', '12:26:00', 'Pending', '2025-09-09 03:25:15', '2025-09-09 03:33:24'),
(3, 'jojo', 'UX Designer', '2025-10-02', '13:35:00', 'Completed', '2025-09-09 03:33:42', '2025-09-09 03:46:11');

-- --------------------------------------------------------

--
-- Table structure for table `job_offers`
--

CREATE TABLE `job_offers` (
  `id` int(11) NOT NULL,
  `candidate_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `offer_date` date NOT NULL,
  `status` enum('Pending','Accepted','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_offers`
--

INSERT INTO `job_offers` (`id`, `candidate_name`, `position`, `salary`, `offer_date`, `status`, `created_at`, `updated_at`) VALUES
(2, 'asdasd', 'hitmen', 12321312.00, '2025-09-25', 'Pending', '2025-09-09 03:38:26', '2025-09-09 04:11:51');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `location` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Temporary') NOT NULL,
  `salary_range` varchar(50) DEFAULT NULL,
  `status` enum('Open','Closed','Draft') DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`id`, `title`, `description`, `requirements`, `location`, `department`, `employment_type`, `salary_range`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Frontend Developer ', 'Develop and maintain web applications using React.js.', '3+ years experience with React, HTML, CSS, JavaScript.', 'New York, NY', 'Engineering', 'Full-time', '$80,000 - $100,000', 'Closed', '2025-09-09 03:19:29', '2025-09-09 04:05:49'),
(4, 'Mobile App Developer', 'Develop and maintain mobile applications for Android and iOS.', 'Experience with React Native or Flutter.', 'Los Angeles, CA', 'Engineering', 'Full-time', '$85,000 - $110,000', 'Draft', '2025-09-09 03:20:24', '2025-09-09 04:05:54'),
(5, 'Backend Developer', 'Design and implement APIs using PHP and Laravel.', '2+ years experience with PHP and MySQL.', 'San Francisco, CA', 'Engineering', 'Full-time', '$90,000 - $110,000', 'Open', '2025-09-09 03:21:21', '2025-09-09 03:21:21'),
(6, 'UI/UX Designer', 'Create user-centered designs for web and mobile applications.', 'Experience with Figma and Adobe XD.', 'Remote', 'Design', 'Contract', '$50/hr', 'Open', '2025-09-09 03:21:38', '2025-09-09 03:21:38'),
(7, 'Data Analyst', 'Analyze business data to support decision-making.', 'Experience with SQL, Python, and Excel.', 'New York, NY', 'Data', 'Full-time', '$80,000 - $100,000', 'Open', '2025-09-09 03:22:47', '2025-09-09 03:22:47'),
(8, 'Customer Support Specialist', 'Provide support to customers via email, chat, and calls.', 'Excellent communication skills and problem-solving ability.', 'Remote', 'Support', 'Full-time', '$40,000 - $50,000', 'Open', '2025-09-09 03:23:08', '2025-09-09 03:23:08');

-- --------------------------------------------------------

--
-- Table structure for table `learning_modules`
--

CREATE TABLE `learning_modules` (
  `id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in hours',
  `start_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_modules`
--

INSERT INTO `learning_modules` (`id`, `module_name`, `category`, `duration`, `start_date`, `status`, `created_at`, `updated_at`) VALUES
(2, 'asdas', 'dasdas', 5, '2025-10-08', 'Active', '2025-09-09 03:42:39', '2025-09-09 04:11:59');

-- --------------------------------------------------------

--
-- Table structure for table `performance_records`
--

CREATE TABLE `performance_records` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `score` int(11) NOT NULL CHECK (`score` between 0 and 100),
  `performance_level` enum('Excellent','Good','Average','Needs Improvement') NOT NULL DEFAULT 'Good',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_records`
--

INSERT INTO `performance_records` (`id`, `employee_name`, `position`, `department`, `score`, `performance_level`, `created_at`, `updated_at`) VALUES
(1, 'weqwed333', 'qweqw', 'qeqw', 15, 'Good', '2025-09-09 04:03:16', '2025-09-09 04:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `shortlists`
--

CREATE TABLE `shortlists` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `position` varchar(255) NOT NULL,
  `screening_score` int(11) DEFAULT 0,
  `shortlist_status` enum('Shortlisted','Pending Interview','Under Review','Rejected') DEFAULT 'Under Review',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shortlists`
--

INSERT INTO `shortlists` (`id`, `applicant_id`, `position`, `screening_score`, `shortlist_status`, `notes`, `created_at`, `updated_at`) VALUES
(2, 2, 'soil', 0, 'Shortlisted', NULL, '2025-09-09 03:24:45', '2025-09-09 03:24:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_applicants_position` (`position`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_offers`
--
ALTER TABLE `job_offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_modules`
--
ALTER TABLE `learning_modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance_records`
--
ALTER TABLE `performance_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shortlists`
--
ALTER TABLE `shortlists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shortlists_applicant_id` (`applicant_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_offers`
--
ALTER TABLE `job_offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `learning_modules`
--
ALTER TABLE `learning_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `performance_records`
--
ALTER TABLE `performance_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shortlists`
--
ALTER TABLE `shortlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `shortlists`
--
ALTER TABLE `shortlists`
  ADD CONSTRAINT `fk_shortlists_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
