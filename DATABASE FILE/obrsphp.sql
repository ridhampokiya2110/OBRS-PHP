-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2025 at 06:50 AM
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
-- Database: `obrsphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `obrs_admin`
--

CREATE TABLE `obrs_admin` (
  `admin_id` int(20) NOT NULL,
  `admin_fname` varchar(200) NOT NULL,
  `admin_lname` varchar(200) NOT NULL,
  `admin_email` varchar(200) NOT NULL,
  `admin_uname` varchar(200) NOT NULL,
  `admin_pwd` varchar(200) NOT NULL,
  `admin_dpic` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `obrs_admin`
--

INSERT INTO `obrs_admin` (`admin_id`, `admin_fname`, `admin_lname`, `admin_email`, `admin_uname`, `admin_pwd`, `admin_dpic`) VALUES
(1, 'System ', 'Admin', 'login@admin.com', 'Administrator', 'e6e36285c763ea386f305a8c4416e8282cc91808', 'admin-icn.png');

-- --------------------------------------------------------

--
-- Table structure for table `obrs_booking_history`
--

CREATE TABLE `obrs_booking_history` (
  `id` int(11) NOT NULL,
  `pass_id` int(20) NOT NULL,
  `bus_number` varchar(200) NOT NULL,
  `bus_name` varchar(200) NOT NULL,
  `dep_station` varchar(200) NOT NULL,
  `dep_time` varchar(200) NOT NULL,
  `arr_station` varchar(200) NOT NULL,
  `bus_fare` varchar(200) NOT NULL,
  `seats` int(11) NOT NULL,
  `selected_seats` text DEFAULT NULL,
  `seat_type` varchar(50) DEFAULT NULL,
  `total_cost` varchar(200) NOT NULL,
  `status` varchar(50) NOT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) NOT NULL DEFAULT 'pending',
  `payment_id` varchar(50) NOT NULL,
  `booking_date` datetime NOT NULL,
  `cancel_date` datetime DEFAULT NULL,
  `booking_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `obrs_booking_history`
--

INSERT INTO `obrs_booking_history` (`id`, `pass_id`, `bus_number`, `bus_name`, `dep_station`, `dep_time`, `arr_station`, `bus_fare`, `seats`, `selected_seats`, `seat_type`, `total_cost`, `status`, `payment_status`, `payment_method`, `payment_id`, `booking_date`, `cancel_date`, `booking_id`) VALUES
(1, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 3, '19,25,26', 'Single,Double,Double', '333', 'Active', 'Paid', 'credit', '', '2025-02-14 21:40:23', NULL, 'TKT00250214L57F'),
(2, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 2, '35,36', 'Double', '222', 'Active', 'Paid', 'paypal', '', '2025-03-09 13:53:25', NULL, 'TKT00250309BFPR'),
(3, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 2, '31,32', 'Double', '222', 'Expired', 'Pending', 'pending', '', '2025-03-09 13:54:21', NULL, 'TKT0025030903ED'),
(4, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '29', 'Double', '111', 'Active', 'Paid', 'debit', '', '2025-03-09 13:56:39', NULL, 'TKT00250309TI0C'),
(5, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '30', 'Single', '111', 'Expired', 'Pending', 'pending', '', '2025-03-09 13:57:50', NULL, 'TKT00250309PM7X'),
(6, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 3, '8,2,10', 'Single,Single,Single', '333', 'Cancelled', 'Paid', 'debit', '', '2025-03-09 13:58:51', '2025-03-09 13:59:43', 'TKT00250309M0XU'),
(7, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 5, '1,7,8,2,10', 'Single,Single,Single,Single,Single', '555', 'Active', 'Paid', 'paypal', '', '2025-03-09 14:00:01', NULL, 'TKT00250309IZUA'),
(8, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '28', 'Single', '111', 'Expired', 'Pending', 'pending', '', '2025-03-10 19:48:09', NULL, 'TKT00250310R4G9'),
(9, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 2, '28,30', 'Single,Single', '222', 'Active', 'Paid', 'pending', '', '2025-03-10 19:57:48', NULL, 'TKT00250310B8EV'),
(10, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '21', 'Single', '111', 'Active', 'Paid', 'pending', '', '2025-03-10 19:59:19', NULL, 'TKT00250310W569'),
(11, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '27', 'Single', '111', 'Active', 'Paid', 'credit', '', '2025-03-10 20:00:59', NULL, 'TKT0025031059HX'),
(12, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '32', 'Single', '111', 'Active', 'Paid', 'debit', '', '2025-03-10 20:01:56', NULL, 'TKT00250310N34A'),
(13, 21, '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', 1, '3', 'Single', '111', 'Active', 'Paid', 'paypal', '', '2025-03-10 20:02:45', NULL, 'TKT00250310R1D6');

-- --------------------------------------------------------

--
-- Table structure for table `obrs_bus`
--

CREATE TABLE `obrs_bus` (
  `id` int(20) NOT NULL,
  `name` varchar(200) NOT NULL,
  `route` varchar(200) NOT NULL,
  `current` varchar(200) NOT NULL,
  `destination` varchar(200) NOT NULL,
  `time` varchar(200) NOT NULL,
  `passengers` varchar(200) NOT NULL,
  `number` varchar(200) NOT NULL,
  `fare` varchar(2000) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `booked_seats` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `obrs_bus`
--

INSERT INTO `obrs_bus` (`id`, `name`, `route`, `current`, `destination`, `time`, `passengers`, `number`, `fare`, `available_seats`, `booked_seats`) VALUES
(18, 'alankar', 'surat to bhavnagar', 'surat', 'bhavnagar', '2025-02-14T02:45', '36', '0013', '111', 19, '19,25,26,35,36,29,1,7,8,2,10,28,30,21,27,32,3');

-- --------------------------------------------------------

--
-- Table structure for table `obrs_employee`
--

CREATE TABLE `obrs_employee` (
  `emp_id` int(20) NOT NULL,
  `emp_fname` varchar(200) NOT NULL,
  `emp_lname` varchar(200) NOT NULL,
  `emp_nat_idno` varchar(200) NOT NULL,
  `emp_phone` varchar(200) NOT NULL,
  `emp_addr` varchar(200) NOT NULL,
  `emp_uname` varchar(200) NOT NULL,
  `emp_email` varchar(200) NOT NULL,
  `emp_pwd` varchar(200) NOT NULL,
  `emp_dpic` varchar(200) NOT NULL,
  `emp_dept` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `obrs_employee`
--

INSERT INTO `obrs_employee` (`emp_id`, `emp_fname`, `emp_lname`, `emp_nat_idno`, `emp_phone`, `emp_addr`, `emp_uname`, `emp_email`, `emp_pwd`, `emp_dpic`, `emp_dept`) VALUES
(1, 'John', 'Doe', 'EMP001', '1234567890', '123 Main St', 'johndoe', 'employee@mail.com', 'bb14de66ac0ebdcfaed504d8cd9980ad156d8aa5', 'default.jpg', 'Operations'),
(2, 'Jane', 'Smith', 'EMP002', '2345678901', '456 Oak Ave', 'janesmith', 'jane.smith@example.com', '7c222fb2927d828af22f592134e8932480637c0d', 'default.jpg', 'Customer Service'),
(3, 'Robert', 'Johnson', 'EMP003', '3456789012', '789 Pine Rd', 'rjohnson', 'robert.j@example.com', '7c222fb2927d828af22f592134e8932480637c0d', 'default.jpg', 'Operations');

-- --------------------------------------------------------

--
-- Table structure for table `obrs_passenger`
--

CREATE TABLE `obrs_passenger` (
  `pass_id` int(20) NOT NULL,
  `pass_fname` varchar(200) NOT NULL,
  `pass_lname` varchar(200) NOT NULL,
  `pass_phone` varchar(200) NOT NULL,
  `pass_addr` varchar(200) NOT NULL,
  `pass_email` varchar(200) NOT NULL,
  `pass_pwd` varchar(200) NOT NULL,
  `pass_dpic` varchar(200) NOT NULL,
  `pass_uname` varchar(200) NOT NULL,
  `pass_bus_number` varchar(200) NOT NULL,
  `pass_bus_name` varchar(200) NOT NULL,
  `pass_dep_station` varchar(200) NOT NULL,
  `pass_dep_time` varchar(200) NOT NULL,
  `pass_arr_station` varchar(200) NOT NULL,
  `pass_bus_fare` varchar(200) NOT NULL,
  `pass_fare_payment_code` varchar(200) NOT NULL,
  `seats` int(11) NOT NULL,
  `selected_seats` text DEFAULT NULL,
  `seat_type` varchar(50) DEFAULT NULL,
  `booking_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `obrs_passenger`
--

INSERT INTO `obrs_passenger` (`pass_id`, `pass_fname`, `pass_lname`, `pass_phone`, `pass_addr`, `pass_email`, `pass_pwd`, `pass_dpic`, `pass_uname`, `pass_bus_number`, `pass_bus_name`, `pass_dep_station`, `pass_dep_time`, `pass_arr_station`, `pass_bus_fare`, `pass_fare_payment_code`, `seats`, `selected_seats`, `seat_type`, `booking_id`) VALUES
(21, 'Hudson', 'Jacobi', '2819374192', '1522', 'your.emailfakedata88649@gmail.com', '09bc73a20cf2dcb3d4c0d1085eacca52601a090b', '', 'Hudson', '0013', 'alankar', 'surat', '14 Feb, 2025 at 02:45 AM', 'bhavnagar', '111', '', 3, '3', 'Single', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `obrs_passwordresets`
--

CREATE TABLE `obrs_passwordresets` (
  `pwd_id` int(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `obrs_admin`
--
ALTER TABLE `obrs_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `obrs_booking_history`
--
ALTER TABLE `obrs_booking_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `obrs_bus`
--
ALTER TABLE `obrs_bus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `obrs_employee`
--
ALTER TABLE `obrs_employee`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `obrs_passenger`
--
ALTER TABLE `obrs_passenger`
  ADD PRIMARY KEY (`pass_id`);

--
-- Indexes for table `obrs_passwordresets`
--
ALTER TABLE `obrs_passwordresets`
  ADD PRIMARY KEY (`pwd_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `obrs_admin`
--
ALTER TABLE `obrs_admin`
  MODIFY `admin_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `obrs_booking_history`
--
ALTER TABLE `obrs_booking_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `obrs_bus`
--
ALTER TABLE `obrs_bus`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `obrs_employee`
--
ALTER TABLE `obrs_employee`
  MODIFY `emp_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `obrs_passenger`
--
ALTER TABLE `obrs_passenger`
  MODIFY `pass_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `obrs_passwordresets`
--
ALTER TABLE `obrs_passwordresets`
  MODIFY `pwd_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
