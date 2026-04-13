-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 10:36 AM
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
-- Database: `cafeteriareg`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `AdminID` int(11) NOT NULL,
  `First_name` varchar(50) NOT NULL,
  `Last_name` varchar(50) NOT NULL,
  `Gender` enum('M','F') NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`AdminID`, `First_name`, `Last_name`, `Gender`, `Password`) VALUES
(1, 'Maneno', 'Noah', 'M', 'Ambassador');

-- --------------------------------------------------------

--
-- Table structure for table `chef`
--

CREATE TABLE `chef` (
  `ChefID` int(11) NOT NULL,
  `First_name` varchar(50) NOT NULL,
  `Last_name` varchar(50) NOT NULL,
  `Gender` enum('M','F') NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chef`
--

INSERT INTO `chef` (`ChefID`, `First_name`, `Last_name`, `Gender`, `Password`) VALUES
(1, 'Gordon', 'Ramsey', 'M', 'Hellskitchen');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `FeedbackID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Type` varchar(50) NOT NULL,
  `Message` text NOT NULL,
  `GoesTo` enum('Admin','Chef') NOT NULL,
  `Status` enum('Unread','Read') NOT NULL DEFAULT 'Unread',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`FeedbackID`, `UserID`, `Type`, `Message`, `GoesTo`, `Status`, `CreatedAt`) VALUES
(1, 1, 'Bug Report', 'Can I get a place I can see my ID', 'Admin', 'Read', '2026-03-30 16:55:19'),
(2, 1, 'Food Complaint', 'The Rice was bad', 'Chef', 'Read', '2026-04-02 09:07:32'),
(3, 2, 'Order Cancellation', 'Order #006 cancelled. Reason: Changed my mind', 'Chef', 'Read', '2026-04-08 12:11:33');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `ItemID` int(11) NOT NULL,
  `Item` varchar(50) NOT NULL,
  `Category` enum('Lunch','Drinks','Pastry') NOT NULL,
  `Price` decimal(8,2) NOT NULL,
  `Quantity` int(11) NOT NULL DEFAULT 0,
  `Status` enum('In Stock','Out of Stock') NOT NULL DEFAULT 'In Stock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`ItemID`, `Item`, `Category`, `Price`, `Quantity`, `Status`) VALUES
(1, 'Pilau', 'Lunch', 160.00, 99, 'In Stock'),
(2, 'Chips', 'Lunch', 100.00, 3, 'In Stock'),
(3, 'Githeri', 'Lunch', 120.00, 10, 'In Stock'),
(4, 'Rice beef', 'Lunch', 150.00, 15, 'In Stock'),
(5, 'Choco Milk', 'Drinks', 150.00, 2, 'In Stock'),
(6, 'Fresh Juice', 'Drinks', 120.00, 1, 'In Stock'),
(7, 'Milk', 'Drinks', 100.00, 2, 'In Stock'),
(8, 'Cake', 'Pastry', 180.00, 0, 'Out of Stock'),
(9, 'Muffin', 'Pastry', 90.00, 2, 'In Stock'),
(10, 'Donut', 'Pastry', 80.00, 6, 'In Stock');

-- --------------------------------------------------------

--
-- Table structure for table `menu_option`
--

CREATE TABLE `menu_option` (
  `OptionID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ItemID` int(11) NOT NULL,
  `Quantity` enum('Half','Full') NOT NULL,
  `Price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_option`
--

INSERT INTO `menu_option` (`OptionID`, `OrderID`, `ItemID`, `Quantity`, `Price`) VALUES
(1, 1, 2, 'Full', 150),
(4, 3, 8, 'Full', 230),
(5, 4, 1, 'Half', 160),
(6, 4, 1, 'Half', 160),
(7, 4, 5, 'Full', 200),
(8, 5, 1, 'Half', 160),
(9, 5, 6, 'Full', 170),
(10, 6, 10, 'Full', 130),
(11, 6, 10, 'Full', 130),
(12, 6, 10, 'Full', 130),
(13, 6, 5, 'Full', 200),
(14, 6, 7, 'Full', 150),
(15, 6, 1, 'Full', 210),
(16, 6, 1, 'Full', 210),
(17, 8, 1, 'Full', 150),
(18, 8, 4, 'Full', 80),
(19, 9, 3, 'Full', 130),
(20, 9, 6, 'Full', 70),
(21, 9, 9, 'Full', 80),
(22, 10, 2, 'Half', 100),
(23, 10, 5, 'Full', 50),
(24, 10, 7, 'Full', 80),
(25, 11, 4, 'Full', 120),
(26, 11, 4, 'Full', 80),
(27, 11, 8, 'Full', 80),
(28, 12, 2, 'Full', 150),
(29, 12, 1, 'Half', 100),
(30, 12, 5, 'Full', 50);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ChefID` int(11) NOT NULL,
  `Status` enum('Pending','Paid','Preparing','Ready','Picked Up') NOT NULL DEFAULT 'Pending',
  `Total` int(11) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`OrderID`, `UserID`, `ChefID`, `Status`, `Total`, `Time`) VALUES
(1, 1, 1, 'Picked Up', 320, '2026-03-30 14:59:38'),
(3, 1, 1, 'Picked Up', 230, '2026-03-31 10:59:19'),
(4, 2, 1, 'Picked Up', 520, '2026-04-01 10:43:24'),
(5, 1, 1, 'Ready', 330, '2026-04-07 09:37:02'),
(6, 2, 1, '', 1160, '2026-04-07 20:28:04'),
(8, 4, 0, 'Paid', 230, '2026-04-11 13:02:09'),
(9, 5, 0, 'Ready', 280, '2026-04-11 13:02:09'),
(10, 6, 0, 'Preparing', 230, '2026-04-11 13:02:09'),
(11, 8, 0, 'Paid', 280, '2026-04-11 13:02:09'),
(12, 12, 0, 'Preparing', 250, '2026-04-11 13:02:09');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `UserID` int(11) NOT NULL,
  `First_name` varchar(50) NOT NULL,
  `Last_name` varchar(50) NOT NULL,
  `Gender` enum('M','F') NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`UserID`, `First_name`, `Last_name`, `Gender`, `Password`) VALUES
(1, 'Sean', 'Metili', 'M', 'excalibur'),
(2, 'Vanny', 'Sadiki', 'M', 'Callofduty'),
(4, 'Klein', 'Moretti', 'M', 'password123'),
(5, 'Derrick', 'Berg', 'M', 'pass1234'),
(6, 'Dun', 'Smith', 'M', 'test123'),
(7, 'Daly', 'Simone', 'F', 'user123'),
(8, 'Audrey', 'Hall', 'F', 'welcome1'),
(9, 'Alger', 'Wilson', 'M', 'login123'),
(10, 'Emlyn', 'White', 'M', 'access1'),
(11, 'Fors', 'Wall', 'M', 'demo123'),
(12, 'Cattleya', 'Williams', 'F', 'simple1'),
(13, 'Leonard', 'Mitchell', 'M', 'easy123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`AdminID`);

--
-- Indexes for table `chef`
--
ALTER TABLE `chef`
  ADD PRIMARY KEY (`ChefID`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`FeedbackID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`ItemID`);

--
-- Indexes for table `menu_option`
--
ALTER TABLE `menu_option`
  ADD PRIMARY KEY (`OptionID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ItemID` (`ItemID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ChefID` (`ChefID`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `AdminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chef`
--
ALTER TABLE `chef`
  MODIFY `ChefID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `FeedbackID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `ItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menu_option`
--
ALTER TABLE `menu_option`
  MODIFY `OptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `registration` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu_option`
--
ALTER TABLE `menu_option`
  ADD CONSTRAINT `menu_option_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `order` (`OrderID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_option_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `menu` (`ItemID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `registration` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_ibfk_2` FOREIGN KEY (`ChefID`) REFERENCES `chef` (`ChefID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
