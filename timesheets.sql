-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 21, 2021 at 07:17 AM
-- Server version: 10.2.41-MariaDB-cll-lve
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deanwint_timesheets`
--

-- --------------------------------------------------------

--
-- Table structure for table `days`
--

CREATE TABLE `days` (
  `id` int(11) NOT NULL,
  `timesheet_id` int(11) NOT NULL,
  `dof` int(2) DEFAULT NULL,
  `datex` date NOT NULL,
  `daystart` time DEFAULT NULL,
  `daystop` time DEFAULT NULL,
  `break1start` time DEFAULT NULL,
  `break1stop` time DEFAULT NULL,
  `break2start` time DEFAULT NULL,
  `break2stop` time DEFAULT NULL,
  `break3start` time DEFAULT NULL,
  `break3stop` time DEFAULT NULL,
  `oncall` tinyint(1) DEFAULT NULL,
  `lvannual` time DEFAULT NULL,
  `lvsick` time DEFAULT NULL,
  `lvtoil` time DEFAULT NULL,
  `lvphcon` time DEFAULT NULL,
  `lvflex` time DEFAULT NULL,
  `oc1start` time DEFAULT NULL,
  `oc1stop` time DEFAULT NULL,
  `oc2start` time DEFAULT NULL,
  `oc2stop` time DEFAULT NULL,
  `oc3start` time DEFAULT NULL,
  `oc3stop` time DEFAULT NULL,
  `ot1start` time DEFAULT NULL,
  `ot1stop` time DEFAULT NULL,
  `ot2start` time DEFAULT NULL,
  `ot2stop` time DEFAULT NULL,
  `ot3start` time DEFAULT NULL,
  `ot3stop` time DEFAULT NULL,
  `toil1start` time DEFAULT NULL,
  `toil1stop` time DEFAULT NULL,
  `toil2start` time DEFAULT NULL,
  `toil2stop` time DEFAULT NULL,
  `toil3start` time DEFAULT NULL,
  `toil3stop` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `first` varchar(255) NOT NULL,
  `last` varchar(255) NOT NULL,
  `email` text NOT NULL,
  `flexcf` varchar(9) DEFAULT NULL,
  `toilcf` varchar(9) DEFAULT NULL,
  `novellname` varchar(255) DEFAULT NULL,
  `default_daystart` time DEFAULT NULL,
  `default_daystop` time DEFAULT NULL,
  `default_break1start` time DEFAULT NULL,
  `default_break1stop` time DEFAULT NULL,
  `default_break2start` time DEFAULT NULL,
  `default_break2stop` time DEFAULT NULL,
  `default_break3start` time DEFAULT NULL,
  `default_break3stop` time DEFAULT NULL,
  `supervisor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

CREATE TABLE `timesheets` (
  `id` int(11) NOT NULL,
  `employee` int(11) NOT NULL,
  `fne_date` date NOT NULL,
  `flexcf` varchar(9) DEFAULT NULL,
  `toilcf` varchar(9) DEFAULT NULL,
  `submitted` timestamp NULL DEFAULT NULL,
  `approvedby` int(11) DEFAULT NULL,
  `approvedtime` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `days`
--
ALTER TABLE `days`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `days`
--
ALTER TABLE `days`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `timesheets`
--
ALTER TABLE `timesheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
