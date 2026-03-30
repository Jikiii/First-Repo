-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 04:11 PM
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
-- Database: `dreamhome_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisement`
--

CREATE TABLE `advertisement` (
  `advert_id` int(11) NOT NULL,
  `property_no` varchar(5) DEFAULT NULL,
  `newspaper_name` varchar(50) DEFAULT NULL,
  `date_placed` date DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advertisement`
--

INSERT INTO `advertisement` (`advert_id`, `property_no`, `newspaper_name`, `date_placed`, `cost`) VALUES
(1, 'PG21', 'Glasgow Times', '2024-01-15', 150.00),
(2, 'PG21', 'Daily Record', '2024-02-01', 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `branch`
--

CREATE TABLE `branch` (
  `branch_no` varchar(5) NOT NULL,
  `street` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `fax` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch`
--

INSERT INTO `branch` (`branch_no`, `street`, `area`, `city`, `postcode`, `telephone`, `fax`) VALUES
('B3', '163 Main Street', 'Patrick', 'Glasgow', 'G11 9QX', '0141-123-4567', '0141-123-4568'),
('B85', '19 Taylor Street', 'Cranford', 'London', 'SW1A 1AA', '0171-884-5112', '0171-884-5113');

-- --------------------------------------------------------

--
-- Table structure for table `leaseagreement`
--

CREATE TABLE `leaseagreement` (
  `lease_no` varchar(5) NOT NULL,
  `property_no` varchar(5) DEFAULT NULL,
  `renter_no` varchar(5) DEFAULT NULL,
  `arranged_by_staff_no` varchar(5) DEFAULT NULL,
  `monthly_rent` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `deposit_amount` decimal(10,2) DEFAULT NULL,
  `deposit_paid` tinyint(1) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leaseagreement`
--

INSERT INTO `leaseagreement` (`lease_no`, `property_no`, `renter_no`, `arranged_by_staff_no`, `monthly_rent`, `payment_method`, `deposit_amount`, `deposit_paid`, `start_date`, `end_date`, `duration`) VALUES
('L001', 'PG21', 'CR74', 'SA12', 600.00, 'Bank Transfer', 1200.00, 1, '2024-01-01', '2024-12-31', 12);

-- --------------------------------------------------------

--
-- Table structure for table `nextofkin`
--

CREATE TABLE `nextofkin` (
  `staff_no` varchar(5) NOT NULL,
  `full_name` varchar(60) DEFAULT NULL,
  `relationship` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nextofkin`
--

INSERT INTO `nextofkin` (`staff_no`, `full_name`, `relationship`, `address`, `telephone`) VALUES
('SG14', 'Robert Brand', 'Husband', '45 Queen Street, Glasgow', '0141-234-5679'),
('SL21', 'Mary White', 'Wife', '19 Taylor Street, Cranford, London', '0171-884-5112');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `property_no` varchar(5) NOT NULL,
  `street` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `monthly_rent` decimal(10,2) DEFAULT NULL,
  `staff_no` varchar(5) DEFAULT NULL,
  `branch_no` varchar(5) DEFAULT NULL,
  `date_withdrawn` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`property_no`, `street`, `area`, `city`, `postcode`, `type`, `rooms`, `monthly_rent`, `staff_no`, `branch_no`, `date_withdrawn`, `status`) VALUES
('PG16', 'Novar', 'Hyndland', 'Glasgow', 'G12 9AX', 'Flat', 4, 450.00, 'ST456', 'B3', NULL, 'available'),
('PG21', 'Dale Road', 'Hyndland', 'Glasgow', 'G12', 'House', 5, 600.00, 'ST456', 'B3', NULL, 'rented'),
('PG36', 'Manor Road', NULL, 'Glasgow', 'G32 4QX', 'Flat', 3, 375.00, 'ST456', 'B3', NULL, 'available'),
('PG46', 'Lawrence St.', 'Patrick', 'Glasgow', 'G11 9QX', 'Flat', 3, 350.00, 'ST456', 'B3', NULL, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `propertyinspection`
--

CREATE TABLE `propertyinspection` (
  `inspection_id` int(11) NOT NULL,
  `property_no` varchar(5) DEFAULT NULL,
  `staff_no` varchar(5) DEFAULT NULL,
  `inspection_date` date DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `propertyinspection`
--

INSERT INTO `propertyinspection` (`inspection_id`, `property_no`, `staff_no`, `inspection_date`, `comments`) VALUES
(1, 'PG21', 'SG14', '2024-03-15', 'No problems found'),
(2, 'PG21', 'SG14', '2024-09-30', 'Cracked ceiling in living room. Requires urgent repair.'),
(3, 'PG21', 'SA12', '2024-07-01', 'Crockery needs to be replaced.');

-- --------------------------------------------------------

--
-- Table structure for table `renter`
--

CREATE TABLE `renter` (
  `renter_no` varchar(5) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `preferred_property_type` varchar(20) DEFAULT NULL,
  `max_monthly_rent` decimal(10,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `date_registered` date DEFAULT NULL,
  `seen_by_staff_no` varchar(5) DEFAULT NULL,
  `branch_no` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `renter`
--

INSERT INTO `renter` (`renter_no`, `first_name`, `last_name`, `address`, `telephone`, `preferred_property_type`, `max_monthly_rent`, `comments`, `date_registered`, `seen_by_staff_no`, `branch_no`) VALUES
('CR74', 'Mike', 'Ritchie', '18 Tain Street, Gourock', 'PAIG IYQ', 'House', 750.00, 'Currently living at home with parents. Getting married in August.', '1995-03-24', 'SA12', 'B3');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_no` varchar(5) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(30) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `sex` char(1) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `national_insurance_no` varchar(15) DEFAULT NULL,
  `job_title` varchar(30) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `date_joined` date DEFAULT NULL,
  `branch_no` varchar(5) DEFAULT NULL,
  `supervisor_no` varchar(5) DEFAULT NULL,
  `typing_speed` int(11) DEFAULT NULL,
  `car_allowance` decimal(10,2) DEFAULT NULL,
  `monthly_bonus` decimal(10,2) DEFAULT NULL,
  `manager_start_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_no`, `first_name`, `last_name`, `address`, `telephone`, `sex`, `date_of_birth`, `national_insurance_no`, `job_title`, `salary`, `date_joined`, `branch_no`, `supervisor_no`, `typing_speed`, `car_allowance`, `monthly_bonus`, `manager_start_date`) VALUES
('SA12', 'Ann', 'Beech', '12 Park Avenue, Glasgow', '0141-345-6789', 'F', '1980-11-30', 'EF345678F', 'Supervisor', 48000.00, '2012-06-15', 'B3', NULL, NULL, NULL, NULL, NULL),
('SG14', 'Susan', 'Brand', '45 Queen Street, Glasgow', '0141-234-5678', 'F', '1975-07-20', 'CD789012E', 'Supervisor', 45000.00, '2010-03-01', 'B3', NULL, NULL, NULL, NULL, NULL),
('SL21', 'John', 'White', '19 Taylor Street, Cranford, London', '0171-884-5112', 'M', '1965-03-15', 'AB123456C', 'Manager', 75000.00, '1988-10-24', 'B85', NULL, NULL, 5000.00, 1000.00, '1988-10-24'),
('ST456', 'Mike', 'Johnson', '8 Station Road, Glasgow', '0141-456-7890', 'M', '1988-02-25', 'GH456789G', 'Staff', 32000.00, '2015-01-10', 'B3', 'SG14', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisement`
--
ALTER TABLE `advertisement`
  ADD PRIMARY KEY (`advert_id`),
  ADD KEY `property_no` (`property_no`);

--
-- Indexes for table `branch`
--
ALTER TABLE `branch`
  ADD PRIMARY KEY (`branch_no`);

--
-- Indexes for table `leaseagreement`
--
ALTER TABLE `leaseagreement`
  ADD PRIMARY KEY (`lease_no`),
  ADD KEY `property_no` (`property_no`),
  ADD KEY `renter_no` (`renter_no`),
  ADD KEY `arranged_by_staff_no` (`arranged_by_staff_no`);

--
-- Indexes for table `nextofkin`
--
ALTER TABLE `nextofkin`
  ADD PRIMARY KEY (`staff_no`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`property_no`),
  ADD KEY `staff_no` (`staff_no`),
  ADD KEY `branch_no` (`branch_no`);

--
-- Indexes for table `propertyinspection`
--
ALTER TABLE `propertyinspection`
  ADD PRIMARY KEY (`inspection_id`),
  ADD KEY `property_no` (`property_no`),
  ADD KEY `staff_no` (`staff_no`);

--
-- Indexes for table `renter`
--
ALTER TABLE `renter`
  ADD PRIMARY KEY (`renter_no`),
  ADD KEY `seen_by_staff_no` (`seen_by_staff_no`),
  ADD KEY `branch_no` (`branch_no`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_no`),
  ADD UNIQUE KEY `national_insurance_no` (`national_insurance_no`),
  ADD KEY `branch_no` (`branch_no`),
  ADD KEY `supervisor_no` (`supervisor_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisement`
--
ALTER TABLE `advertisement`
  MODIFY `advert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `propertyinspection`
--
ALTER TABLE `propertyinspection`
  MODIFY `inspection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advertisement`
--
ALTER TABLE `advertisement`
  ADD CONSTRAINT `advertisement_ibfk_1` FOREIGN KEY (`property_no`) REFERENCES `property` (`property_no`);

--
-- Constraints for table `leaseagreement`
--
ALTER TABLE `leaseagreement`
  ADD CONSTRAINT `leaseagreement_ibfk_1` FOREIGN KEY (`property_no`) REFERENCES `property` (`property_no`),
  ADD CONSTRAINT `leaseagreement_ibfk_2` FOREIGN KEY (`renter_no`) REFERENCES `renter` (`renter_no`),
  ADD CONSTRAINT `leaseagreement_ibfk_3` FOREIGN KEY (`arranged_by_staff_no`) REFERENCES `staff` (`staff_no`);

--
-- Constraints for table `nextofkin`
--
ALTER TABLE `nextofkin`
  ADD CONSTRAINT `nextofkin_ibfk_1` FOREIGN KEY (`staff_no`) REFERENCES `staff` (`staff_no`) ON DELETE CASCADE;

--
-- Constraints for table `property`
--
ALTER TABLE `property`
  ADD CONSTRAINT `property_ibfk_1` FOREIGN KEY (`staff_no`) REFERENCES `staff` (`staff_no`),
  ADD CONSTRAINT `property_ibfk_2` FOREIGN KEY (`branch_no`) REFERENCES `branch` (`branch_no`);

--
-- Constraints for table `propertyinspection`
--
ALTER TABLE `propertyinspection`
  ADD CONSTRAINT `propertyinspection_ibfk_1` FOREIGN KEY (`property_no`) REFERENCES `property` (`property_no`),
  ADD CONSTRAINT `propertyinspection_ibfk_2` FOREIGN KEY (`staff_no`) REFERENCES `staff` (`staff_no`);

--
-- Constraints for table `renter`
--
ALTER TABLE `renter`
  ADD CONSTRAINT `renter_ibfk_1` FOREIGN KEY (`seen_by_staff_no`) REFERENCES `staff` (`staff_no`),
  ADD CONSTRAINT `renter_ibfk_2` FOREIGN KEY (`branch_no`) REFERENCES `branch` (`branch_no`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`branch_no`) REFERENCES `branch` (`branch_no`),
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`supervisor_no`) REFERENCES `staff` (`staff_no`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
