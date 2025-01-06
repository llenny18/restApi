-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2024 at 07:13 AM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cpa`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `ID` int(11) NOT NULL,
  `Account_ID` int(25) NOT NULL,
  `password` varchar(255) NOT NULL,
  `attempts` int(50) NOT NULL,
  `Date_Started` varchar(50) NOT NULL,
  `Categories` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`ID`, `Account_ID`, `password`, `attempts`, `Date_Started`, `Categories`) VALUES
(36, 125125125, 'sdfasdasd', 0, '', 'teacher'),
(38, 124125, 'sdfasbasd', 0, '', 'student'),
(39, 1, '2', 0, '', 'teacher'),
(40, 2, '2', 0, '', 'teacher'),
(41, 3, '3', 0, '', 'student');

-- --------------------------------------------------------

--
-- Table structure for table `adv`
--

CREATE TABLE `adv` (
  `adv_id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(100) NOT NULL,
  `opt2` varchar(100) NOT NULL,
  `opt3` varchar(100) NOT NULL,
  `opt4` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `scenario` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `adv`
--

INSERT INTO `adv` (`adv_id`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `topic`, `image`, `scenario`) VALUES
(6, 'how', 'sdkfks', 'skdfksdf', 'sdfskdfk', 'sdfksdkf', 'A', '', '', ''),
(7, 'sdfsdfsdf', 'gs', 'sg', 'sg', 'sg', 'B', 'Home Office', 'uploads/6442cbe8-c57f-433b-be55-a14ec70bb420.jpg', ''),
(8, 'asasf', 'asfa', 'sfas', 'fasf', 'asfaf', 'A', 'Part', '', 'sfsafasf');

-- --------------------------------------------------------

--
-- Table structure for table `aud`
--

CREATE TABLE `aud` (
  `aud_id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(100) NOT NULL,
  `opt2` varchar(100) NOT NULL,
  `opt3` varchar(100) NOT NULL,
  `opt4` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `scenario` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `aud`
--

INSERT INTO `aud` (`aud_id`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `topic`, `image`, `scenario`) VALUES
(3, 'sdfsdfsdf', 'af', 'sd', 'w', 's', 'B', 'Audit Completion', 'uploads/6442cbe8-c57f-433b-be55-a14ec70bb420.jpg', ''),
(4, 'fafafs', 'sdgsd', 'sdgd', 'sgs', 'dgsdg', 'C', 'Audit Completion', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `financial`
--

CREATE TABLE `financial` (
  `fin_id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(100) NOT NULL,
  `opt2` varchar(100) NOT NULL,
  `opt3` varchar(100) NOT NULL,
  `opt4` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `scenario` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `financial`
--

INSERT INTO `financial` (`fin_id`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `topic`, `image`, `scenario`) VALUES
(75, 'sfasf', 'asfa', 'sfas', 'fasf', 'asfaf', 'A', 'Fin Rep', '', 'asfasfa');

-- --------------------------------------------------------

--
-- Table structure for table `mng`
--

CREATE TABLE `mng` (
  `mng_id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(100) NOT NULL,
  `opt2` varchar(100) NOT NULL,
  `opt3` varchar(100) NOT NULL,
  `opt4` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `scenario` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mng`
--

INSERT INTO `mng` (`mng_id`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `topic`, `image`, `scenario`) VALUES
(3, 'dfgdfg', 'sdf', 'awd', 'dgs', 'hsbs', 'D', 'Fin Man', '', ''),
(4, 'Mng test 1', 'sdf', 'sdf', 'sdf', 'sdf', 'C', 'Man Acc', '', ''),
(5, 'Tkskdfk ', 'sdf', 'awdad', 'af', 'sdfsdfsd', 'C', 'Fin Man', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `reg`
--

CREATE TABLE `reg` (
  `reg_id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(100) NOT NULL,
  `opt2` varchar(100) NOT NULL,
  `opt3` varchar(100) NOT NULL,
  `opt4` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `scenario` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reg`
--

INSERT INTO `reg` (`reg_id`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `topic`, `image`, `scenario`) VALUES
(4, 'sdfsdfsdfsdfsdf', 'sdf', 'sdfsdf', 'sdf', 'sdfsdfsd', 'A', 'LBT', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `score_history`
--

CREATE TABLE `score_history` (
  `history_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `recorded_date` date DEFAULT curdate(),
  `exam_date` date DEFAULT NULL,
  `Financial_Score` int(100) NOT NULL,
  `Adv_Score` int(100) NOT NULL,
  `Mng_score` int(100) NOT NULL,
  `Auditing_Score` int(100) NOT NULL,
  `Taxation_Score` int(100) NOT NULL,
  `Framework_score` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `score_history`
--

INSERT INTO `score_history` (`history_id`, `student_id`, `recorded_date`, `exam_date`, `Financial_Score`, `Adv_Score`, `Mng_score`, `Auditing_Score`, `Taxation_Score`, `Framework_score`) VALUES
(2, 2314, '0000-00-00', '2024-10-08', 9, 3, 10, 7, 5, 9),
(3, 2314, '0000-00-00', '2024-10-09', 5, 3, 7, 9, 3, 2),
(4, 2314, '0000-00-00', '2024-10-10', 8, 9, 10, 2, 3, 7),
(5, 2314, '0000-00-00', '2024-10-11', 5, 2, 5, 8, 5, 4),
(6, 2314, '0000-00-00', '2024-10-12', 3, 2, 1, 1, 4, 3),
(7, 3, '2024-11-07', '2024-11-07', 0, 0, 0, 0, 0, 0),
(9, NULL, '2024-11-08', '2024-11-08', 2, 0, 0, 0, 2, 0),
(10, 3, '2024-11-08', '2024-11-08', 2, 0, 0, 0, 1, 0),
(11, 3, '2024-11-08', '2024-11-08', 1, 0, 0, 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tax`
--

CREATE TABLE `tax` (
  `tax_id` int(11) NOT NULL,
  `question` varchar(500) NOT NULL,
  `opt1` varchar(100) NOT NULL,
  `opt2` varchar(100) NOT NULL,
  `opt3` varchar(100) NOT NULL,
  `opt4` varchar(100) NOT NULL,
  `answer` varchar(100) NOT NULL,
  `topic` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `scenario` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tax`
--

INSERT INTO `tax` (`tax_id`, `question`, `opt1`, `opt2`, `opt3`, `opt4`, `answer`, `topic`, `image`, `scenario`) VALUES
(2, 'kekssdfsd fsf', 'awfasf', 'sdfsdfas fasfa', 'sdfs sdf', 'wfdsfw sdf', 'C', 'Doc Stamp', '', ''),
(3, 'gesgsdf', 'sdfs', 'sdg', 'gsd', 'sgdsdgsdg', 'D', 'Business Tax', '0', ''),
(4, 'sdfsdf', 'wefwef sdf', 'sdfsd sdf', 'fqsdf ', 'wefwfewfwfaf', 'B', 'Transfer Tax', '0', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `adv`
--
ALTER TABLE `adv`
  ADD PRIMARY KEY (`adv_id`);

--
-- Indexes for table `aud`
--
ALTER TABLE `aud`
  ADD PRIMARY KEY (`aud_id`);

--
-- Indexes for table `financial`
--
ALTER TABLE `financial`
  ADD PRIMARY KEY (`fin_id`);

--
-- Indexes for table `mng`
--
ALTER TABLE `mng`
  ADD PRIMARY KEY (`mng_id`);

--
-- Indexes for table `reg`
--
ALTER TABLE `reg`
  ADD PRIMARY KEY (`reg_id`);

--
-- Indexes for table `score_history`
--
ALTER TABLE `score_history`
  ADD PRIMARY KEY (`history_id`);

--
-- Indexes for table `tax`
--
ALTER TABLE `tax`
  ADD PRIMARY KEY (`tax_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `adv`
--
ALTER TABLE `adv`
  MODIFY `adv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `aud`
--
ALTER TABLE `aud`
  MODIFY `aud_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `financial`
--
ALTER TABLE `financial`
  MODIFY `fin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `mng`
--
ALTER TABLE `mng`
  MODIFY `mng_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reg`
--
ALTER TABLE `reg`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `score_history`
--
ALTER TABLE `score_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tax`
--
ALTER TABLE `tax`
  MODIFY `tax_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
