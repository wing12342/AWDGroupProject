-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 09, 2024 at 03:03 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ElectricVehicleChargers`
--

-- --------------------------------------------------------

--
-- Table structure for table `ElectricVehicleChargers`
--

CREATE TABLE `ElectricVehicleChargers` (
  `NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN` varchar(50) NOT NULL,
  `LOCATION_EN` varchar(200) NOT NULL,
  `ADDRESS_EN` varchar(200) NOT NULL,
  `NAME_OF_DISTRICT_COUNCIL_DISTRICT_TC` varchar(20) NOT NULL,
  `LOCATION_TC` varchar(50) NOT NULL,
  `ADDRESS_TC` varchar(50) NOT NULL,
  `NAME_OF_DISTRICT_COUNCIL_DISTRICT_SC` varchar(20) NOT NULL,
  `LOCATION_SC` varchar(50) NOT NULL,
  `ADDRESS_SC` varchar(50) NOT NULL,
  `STANDARD_BS1363_no` varchar(50) NOT NULL,
  `MEDIUM_IEC62196_no` varchar(5) NOT NULL,
  `MEDIUM_SAEJ1772_no` varchar(5) NOT NULL,
  `MEDIUM_OTHERS_no` varchar(5) NOT NULL,
  `QUICK_CHAdeMO_no` varchar(5) NOT NULL,
  `QUICK_CCS_DC_COMBO_no` varchar(5) NOT NULL,
  `QUICK_IEC62196_no` varchar(5) NOT NULL,
  `QUICK_GB_T20234_3_DC__no` varchar(5) NOT NULL,
  `QUICK_OTHERS_no` varchar(5) NOT NULL,
  `REMARK_FOR__OTHERS_` varchar(50) DEFAULT NULL,
  `DATA_PATH` varchar(200) DEFAULT NULL,
  `GeometryLongitude` double(40,30) NOT NULL,
  `GeometryLatitude` double(40,30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ElectricVehicleChargers`
--
ALTER TABLE `ElectricVehicleChargers`
  ADD PRIMARY KEY (`ADDRESS_EN`,`LOCATION_EN`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
