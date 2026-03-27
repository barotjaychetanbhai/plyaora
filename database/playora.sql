-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2026 at 12:25 PM
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
-- Database: `playora`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Super Admin', 'admin@playora.com', '$2y$10$brTkQKWCSjfmcFDv/P6qjugHqYhPKUN/yMpDaFoj8krVhTkV6OcNu', '2026-03-07 08:19:16');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `ticket_token` varchar(64) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `turf_id` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `payment_method` enum('cash','online') DEFAULT 'online',
  `ticket_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_id`, `ticket_token`, `user_id`, `turf_id`, `booking_date`, `time_slot`, `amount`, `commission`, `status`, `payment_method`, `ticket_used`, `created_at`) VALUES
(1, 'PLY000001', NULL, 1, 1, '2026-03-07', '18:00 - 19:00', 1500.00, 150.00, 'completed', 'online', 0, '2026-03-05 08:25:11'),
(2, 'PLY000002', NULL, 2, 2, '2026-03-07', '19:00 - 20:00', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-06 08:25:11'),
(3, 'PLY000003', NULL, 1, 2, '2026-03-08', '20:00 - 21:00', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-07 08:25:11'),
(4, 'PLY000004', NULL, 2, 1, '2026-03-09', '17:00 - 18:00', 1500.00, 150.00, 'cancelled', 'online', 0, '2026-03-04 08:25:11'),
(5, 'PLY000005', NULL, 1, 1, '2026-03-07', '18:00 - 19:00', 1500.00, 150.00, 'completed', 'online', 0, '2026-03-05 10:08:37'),
(6, 'PLY000006', NULL, 2, 2, '2026-03-07', '19:00 - 20:00', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-06 10:08:37'),
(7, 'PLY000007', NULL, 1, 2, '2026-03-08', '20:00 - 21:00', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-07 10:08:37'),
(8, 'PLY000008', NULL, 2, 1, '2026-03-09', '17:00 - 18:00', 1500.00, 150.00, 'cancelled', 'online', 0, '2026-03-04 10:08:37'),
(9, 'PLY000009', NULL, 1, 2, '2026-03-12', '18:00 - 19:00', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-09 07:16:28'),
(10, 'PLY000010', NULL, 1, 3, '2026-03-15', '19:00 - 20:00', 1500.00, 150.00, '', 'online', 0, '2026-03-09 07:16:28'),
(11, 'PLY000011', NULL, 2, 1, '2026-03-10', '20:00 - 21:00', 1400.00, 140.00, 'completed', 'online', 0, '2026-03-09 07:16:28'),
(12, 'PLY000012', NULL, 3, 2, '2026-03-11', '17:00 - 18:00', 1200.00, 120.00, '', 'online', 0, '2026-03-09 07:16:28'),
(13, 'PLY000013', NULL, 4, 3, '2026-03-13', '18:00 - 19:00', 1500.00, 150.00, 'completed', 'online', 0, '2026-03-09 07:16:28'),
(14, 'PLY000014', NULL, 5, 4, '2026-03-14', '19:00 - 20:00', 1500.00, 150.00, 'completed', 'online', 0, '2026-03-09 07:16:28'),
(15, 'PLY000015', NULL, 6, 5, '2026-03-15', '20:00 - 21:00', 1300.00, 130.00, '', 'online', 0, '2026-03-09 07:16:28'),
(16, 'PLY000016', NULL, 7, 6, '2026-03-16', '21:00 - 22:00', 2000.00, 200.00, 'completed', 'online', 0, '2026-03-09 07:16:28'),
(17, 'PLY000017', NULL, 8, 2, '2026-03-17', '18:00 - 19:00', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-09 07:16:28'),
(18, 'PLY000018', NULL, 11, 11, '2026-03-09', '04:00 PM - 05:00 PM', 1600.00, 160.00, 'cancelled', 'online', 0, '2026-03-09 07:56:41'),
(19, 'PLY000019', NULL, 11, 15, '2026-03-09', '06:00 PM - 07:00 PM', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-09 08:10:51'),
(20, 'PLY000020', NULL, 11, 15, '2026-03-09', '06:00 PM - 07:00 PM', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-09 08:21:08'),
(21, 'PLY000021', NULL, 11, 15, '2026-03-09', '06:00 PM - 07:00 PM', 1200.00, 120.00, 'cancelled', 'online', 0, '2026-03-09 08:28:21'),
(22, 'PLY000022', NULL, 11, 12, '2026-03-12', '10:00 AM - 11:00 AM, 01:00 PM - 02:00 PM, 09:00 PM', 4500.00, 450.00, 'pending', 'cash', 0, '2026-03-12 08:09:01'),
(23, 'PLY000023', NULL, 11, 12, '2026-03-12', '03:00 PM - 04:00 PM', 1500.00, 150.00, 'pending', 'cash', 0, '2026-03-12 08:15:17'),
(24, NULL, NULL, 11, 15, '2026-03-13', '06:00 PM - 07:00 PM', 1200.00, 120.00, 'pending', 'cash', 0, '2026-03-13 07:16:37'),
(25, NULL, NULL, 11, 7, '2026-03-13', '09:00 AM - 10:00 AM', 1800.00, 180.00, 'pending', 'cash', 0, '2026-03-13 07:31:06'),
(26, NULL, NULL, 11, 7, '2026-03-13', '10:00 PM - 11:00 PM', 1800.00, 180.00, 'completed', 'online', 0, '2026-03-13 07:32:16'),
(27, NULL, NULL, 1, 1, '2026-03-14', '10:00 - 11:00', 1500.00, 150.00, 'pending', 'cash', 0, '2026-03-13 07:45:41'),
(28, 'PLY000028', '389b78f5f8f1cb2bd97c50826f4339e5', 11, 14, '2026-03-13', '10:00 AM - 11:00 AM', 2000.00, 200.00, 'pending', 'cash', 0, '2026-03-13 08:09:58'),
(29, 'PLY000029', '1a410bf82fc20f8bd0ea74c65c2b8e77', 11, 10, '2026-03-13', '07:00 PM - 08:00 PM, 08:00 PM - 09:00 PM', 2400.00, 240.00, 'completed', 'online', 0, '2026-03-13 09:48:34'),
(30, 'PLY000030', '13b76b32f949e82815a4cfdbc2f034df', 20, 6, '2026-03-13', '12:00 PM - 01:00 PM', 1200.00, 120.00, 'completed', 'online', 0, '2026-03-13 10:51:22'),
(31, 'PLY000031', '508e23bd69cfca3c00261c5e34fb2535', 20, 6, '2026-03-13', '10:00 PM - 11:00 PM', 1200.00, 120.00, 'completed', 'online', 1, '2026-03-13 11:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `booking_requests`
--

CREATE TABLE `booking_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `turf_id` int(11) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `time_slot` varchar(50) DEFAULT NULL,
  `status` enum('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_slots`
--

CREATE TABLE `booking_slots` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `slot_time` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking_slots`
--

INSERT INTO `booking_slots` (`id`, `booking_id`, `slot_time`) VALUES
(1, 22, '10:00 AM - 11:00 AM'),
(2, 22, '01:00 PM - 02:00 PM'),
(3, 22, '09:00 PM - 10:00 PM'),
(4, 23, '03:00 PM - 04:00 PM'),
(5, 28, '10:00 AM - 11:00 AM'),
(6, 29, '07:00 PM - 08:00 PM'),
(7, 29, '08:00 PM - 09:00 PM'),
(8, 30, '12:00 PM - 01:00 PM'),
(9, 31, '10:00 PM - 11:00 PM');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `state`, `country`, `status`) VALUES
(1, 'Mumbai', 'Maharashtra', 'India', 'active'),
(2, 'Delhi', 'Delhi', 'India', 'active'),
(3, 'Bangalore', 'Karnataka', 'India', 'active'),
(4, 'Chennai', 'Tamil Nadu', 'India', 'active'),
(5, 'Pune', 'Maharashtra', 'India', 'active'),
(6, 'Mumbai', 'Maharashtra', 'India', 'active'),
(7, 'Delhi', 'Delhi', 'India', 'active'),
(8, 'Bangalore', 'Karnataka', 'India', 'active'),
(9, 'Chennai', 'Tamil Nadu', 'India', 'active'),
(10, 'Pune', 'Maharashtra', 'India', 'active'),
(11, 'Ahmedabad', 'Gujarat', 'India', 'active'),
(12, 'Surat', 'Gujarat', 'India', 'active'),
(13, 'Vadodara', 'Gujarat', 'India', 'active'),
(14, 'Jaipur', 'Rajasthan', 'India', 'active'),
(15, 'Hyderabad', 'Telangana', 'India', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_turfs`
--

CREATE TABLE `favorite_turfs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `turf_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorite_turfs`
--

INSERT INTO `favorite_turfs` (`id`, `user_id`, `turf_id`) VALUES
(1, 1, 2),
(2, 1, 3),
(3, 2, 1),
(4, 3, 2),
(5, 4, 4),
(6, 5, 3),
(8, 11, 15),
(9, 11, 7);

-- --------------------------------------------------------

--
-- Table structure for table `owners`
--

CREATE TABLE `owners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','suspended') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `owners`
--

INSERT INTO `owners` (`id`, `name`, `email`, `phone`, `city`, `password`, `status`, `created_at`, `reset_token`) VALUES
(1, 'Vikram Singh', 'vikram@example.com', '9988776655', NULL, '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'approved', '2026-02-20 08:25:11', NULL),
(2, 'Pooja Sharma', 'pooja@example.com', '9988776644', NULL, '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'approved', '2026-02-23 08:25:11', NULL),
(3, 'Anil Kapoor', 'anil@example.com', '9988776633', NULL, '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'pending', '2026-03-05 08:25:11', NULL),
(4, 'Vikram Singh', 'vikram@example.com', '9988776655', NULL, '$2y$10$BRf/VBHyZDJhCkd87EbSx.OEwJi5GWnJzom/pwPSngZU9j0d9.QDW', 'approved', '2026-02-20 10:08:37', NULL),
(6, 'Anil Kapoor', 'anil@example.com', '9988776633', NULL, '$2y$10$BRf/VBHyZDJhCkd87EbSx.OEwJi5GWnJzom/pwPSngZU9j0d9.QDW', 'pending', '2026-03-05 10:08:37', NULL),
(7, 'Jay', 'owner@playora.com', '9988776655', 'Ahmedabad', '$2y$10$nlvl9ZflD583CLOQKdo9GOoE2RY4kWN8DzArkXrre0TvU10KrgE4O', 'approved', '2026-03-09 06:00:35', NULL),
(8, 'Green Arena Pvt Ltd', 'greenarena@playora.com', '9988771111', NULL, '$2y$10$ownerpass', 'approved', '2026-03-09 07:02:55', NULL),
(9, 'Champion Sports Club', 'champion@playora.com', '9988772222', NULL, '$2y$10$ownerpass', 'approved', '2026-03-09 07:02:55', NULL),
(10, 'PlayZone Arena', 'playzone@playora.com', '9988773333', NULL, '$2y$10$ownerpass', 'approved', '2026-03-09 07:02:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `owner_requests`
--

CREATE TABLE `owner_requests` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `request_type` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `commission` decimal(10,2) DEFAULT NULL,
  `owner_amount` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('success','failed','pending') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `commission`, `owner_amount`, `payment_status`, `created_at`) VALUES
(1, 1, 1500.00, 150.00, 1350.00, 'success', '2026-03-05 08:25:11'),
(2, 2, 1200.00, 120.00, 1080.00, 'success', '2026-03-06 08:25:11'),
(3, 3, 1200.00, 120.00, 1080.00, 'pending', '2026-03-07 08:25:11'),
(4, 1, 1500.00, 150.00, 1350.00, 'success', '2026-03-05 10:08:37'),
(5, 2, 1200.00, 120.00, 1080.00, 'success', '2026-03-06 10:08:37'),
(6, 3, 1200.00, 120.00, 1080.00, 'pending', '2026-03-07 10:08:37'),
(7, 9, 1200.00, 120.00, 1080.00, 'success', '2026-03-09 07:16:28'),
(8, 10, 1500.00, 150.00, 1350.00, 'success', '2026-03-09 07:16:28'),
(9, 11, 1400.00, 140.00, 1260.00, 'success', '2026-03-09 07:16:28'),
(10, 12, 1200.00, 120.00, 1080.00, 'success', '2026-03-09 07:16:28'),
(11, 13, 1500.00, 150.00, 1350.00, 'success', '2026-03-09 07:16:28'),
(12, 14, 1500.00, 150.00, 1350.00, 'success', '2026-03-09 07:16:28'),
(13, 29, 2400.00, 240.00, 2160.00, 'success', '2026-03-13 09:48:34'),
(14, 30, 1200.00, 120.00, 1080.00, 'success', '2026-03-13 10:51:23'),
(15, 31, 1200.00, 120.00, 1080.00, 'success', '2026-03-13 11:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `turf_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `status` enum('visible','hidden') DEFAULT 'visible',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `turf_id`, `rating`, `review`, `status`, `created_at`) VALUES
(1, 1, 1, 5, 'Amazing turf, great lighting!', 'visible', '2026-03-06 08:25:11'),
(2, 2, 2, 4, 'Good pitch but parking is an issue.', 'visible', '2026-03-07 08:25:11'),
(3, 1, 1, 5, 'Amazing turf, great lighting!', 'visible', '2026-03-06 10:08:37'),
(4, 2, 2, 4, 'Good pitch but parking is an issue.', 'visible', '2026-03-07 10:08:37'),
(5, 1, 2, 5, 'Excellent turf and lighting', 'visible', '2026-03-09 07:16:28'),
(6, 2, 1, 4, 'Good ground but parking limited', 'visible', '2026-03-09 07:16:28'),
(7, 3, 2, 5, 'Perfect cricket box', 'visible', '2026-03-09 07:16:28'),
(8, 4, 3, 4, 'Nice football turf', 'visible', '2026-03-09 07:16:28'),
(9, 5, 4, 5, 'Great atmosphere to play', 'visible', '2026-03-09 07:16:28'),
(10, 6, 5, 4, 'Well maintained turf', 'visible', '2026-03-09 07:16:28'),
(11, 11, 15, 4, 'I have good experience in this turf so must go there place', 'visible', '2026-03-09 08:20:44');

-- --------------------------------------------------------

--
-- Table structure for table `sports`
--

CREATE TABLE `sports` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sports`
--

INSERT INTO `sports` (`id`, `name`, `icon`, `status`) VALUES
(1, 'Football', 'goal', 'active'),
(2, 'Cricket', 'activity', 'active'),
(4, 'Tennis', 'activity', 'active'),
(8, 'Badminton', 'activity', 'active'),
(10, 'Basketball', 'dribbble', 'active'),
(11, 'Futsal', 'goal', 'active'),
(12, 'Volleyball', 'activity', 'active'),
(13, 'Hockey', 'activity', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `turfs`
--

CREATE TABLE `turfs` (
  `id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `sport_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `images` text DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `turfs`
--

INSERT INTO `turfs` (`id`, `name`, `city_id`, `address`, `sport_id`, `owner_id`, `price`, `description`, `images`, `amenities`, `rating`, `status`, `created_at`) VALUES
(1, 'Urban Sports Arena', 1, NULL, 1, 1, 1500.00, NULL, NULL, NULL, 4.8, 'inactive', '2026-02-21 08:25:11'),
(2, 'Delhi Metro Turf', 2, NULL, 2, 2, 1200.00, NULL, NULL, NULL, 4.5, 'active', '2026-02-25 08:25:11'),
(3, 'Skyline Box Cricket', 1, NULL, 2, 1, 1800.00, NULL, NULL, NULL, 4.9, 'active', '2026-03-02 08:25:11'),
(4, 'Elite Football Ground', 2, NULL, 1, 2, 2000.00, NULL, NULL, NULL, 4.2, 'active', '2026-03-04 08:25:11'),
(5, 'Urban Sports Arena', 1, NULL, 1, 1, 1500.00, NULL, NULL, NULL, 4.8, 'active', '2026-02-21 10:08:37'),
(6, 'Delhi Metro Turf', 2, NULL, 2, 2, 1200.00, NULL, NULL, NULL, 4.5, 'active', '2026-02-25 10:08:37'),
(7, 'Skyline Box Cricket', 1, NULL, 2, 1, 1800.00, NULL, NULL, NULL, 4.9, 'active', '2026-03-02 10:08:37'),
(8, 'Elite Football Ground', 2, NULL, 1, 2, 2000.00, NULL, NULL, NULL, 4.2, 'active', '2026-03-04 10:08:37'),
(9, 'Green Field Arena', 1, NULL, 1, 1, 1400.00, NULL, NULL, NULL, 4.7, 'active', '2026-03-09 07:03:05'),
(10, 'Champion Box Cricket', 2, NULL, 2, 2, 1200.00, NULL, NULL, NULL, 4.5, 'active', '2026-03-09 07:03:05'),
(11, 'PlayZone Football Ground', 3, NULL, 1, 3, 1600.00, NULL, NULL, NULL, 4.6, 'active', '2026-03-09 07:03:05'),
(12, 'Sky Turf Arena', 1, NULL, 1, 1, 1500.00, NULL, NULL, NULL, 4.4, 'active', '2026-03-09 07:03:05'),
(13, 'Night Riders Cricket Box', 2, NULL, 2, 2, 1300.00, NULL, NULL, NULL, 4.3, 'active', '2026-03-09 07:03:05'),
(14, 'Elite Sports Complex', 3, NULL, 1, 3, 2000.00, NULL, NULL, NULL, 4.8, 'active', '2026-03-09 07:03:05'),
(15, 'My turf', 11, 'lfsdjhajksdfbjkac', 2, 7, 1200.00, 'jasdvjkasdv', NULL, 'Parking', 4.0, 'active', '2026-03-09 07:58:04');

-- --------------------------------------------------------

--
-- Table structure for table `turf_slots`
--

CREATE TABLE `turf_slots` (
  `id` int(11) NOT NULL,
  `turf_id` int(11) DEFAULT NULL,
  `slot_time` varchar(50) DEFAULT NULL,
  `status` enum('available','blocked','booked') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `turf_slots`
--

INSERT INTO `turf_slots` (`id`, `turf_id`, `slot_time`, `status`) VALUES
(1, 1, '06:00 AM - 07:00 AM', 'available'),
(2, 1, '07:00 AM - 08:00 AM', 'available'),
(3, 1, '08:00 AM - 09:00 AM', 'available'),
(4, 1, '09:00 AM - 10:00 AM', 'available'),
(5, 1, '10:00 AM - 11:00 AM', 'available'),
(6, 1, '11:00 AM - 12:00 PM', 'available'),
(7, 1, '12:00 PM - 01:00 PM', 'available'),
(8, 1, '01:00 PM - 02:00 PM', 'available'),
(9, 1, '02:00 PM - 03:00 PM', 'available'),
(10, 1, '03:00 PM - 04:00 PM', 'available'),
(11, 1, '04:00 PM - 05:00 PM', 'available'),
(12, 1, '05:00 PM - 06:00 PM', 'available'),
(13, 1, '06:00 PM - 07:00 PM', 'available'),
(14, 1, '07:00 PM - 08:00 PM', 'available'),
(15, 1, '08:00 PM - 09:00 PM', 'available'),
(16, 1, '09:00 PM - 10:00 PM', 'available'),
(17, 1, '10:00 PM - 11:00 PM', 'available'),
(18, 2, '06:00 AM - 07:00 AM', 'available'),
(19, 2, '07:00 AM - 08:00 AM', 'available'),
(20, 2, '08:00 AM - 09:00 AM', 'available'),
(21, 2, '09:00 AM - 10:00 AM', 'available'),
(22, 2, '10:00 AM - 11:00 AM', 'available'),
(23, 2, '11:00 AM - 12:00 PM', 'available'),
(24, 2, '12:00 PM - 01:00 PM', 'available'),
(25, 2, '01:00 PM - 02:00 PM', 'available'),
(26, 2, '02:00 PM - 03:00 PM', 'available'),
(27, 2, '03:00 PM - 04:00 PM', 'available'),
(28, 2, '04:00 PM - 05:00 PM', 'available'),
(29, 2, '05:00 PM - 06:00 PM', 'available'),
(30, 2, '06:00 PM - 07:00 PM', 'available'),
(31, 2, '07:00 PM - 08:00 PM', 'available'),
(32, 2, '08:00 PM - 09:00 PM', 'available'),
(33, 2, '09:00 PM - 10:00 PM', 'available'),
(34, 2, '10:00 PM - 11:00 PM', 'available'),
(35, 3, '06:00 AM - 07:00 AM', 'available'),
(36, 3, '07:00 AM - 08:00 AM', 'available'),
(37, 3, '08:00 AM - 09:00 AM', 'available'),
(38, 3, '09:00 AM - 10:00 AM', 'available'),
(39, 3, '10:00 AM - 11:00 AM', 'available'),
(40, 3, '11:00 AM - 12:00 PM', 'available'),
(41, 3, '12:00 PM - 01:00 PM', 'available'),
(42, 3, '01:00 PM - 02:00 PM', 'available'),
(43, 3, '02:00 PM - 03:00 PM', 'available'),
(44, 3, '03:00 PM - 04:00 PM', 'available'),
(45, 3, '04:00 PM - 05:00 PM', 'available'),
(46, 3, '05:00 PM - 06:00 PM', 'available'),
(47, 3, '06:00 PM - 07:00 PM', 'available'),
(48, 3, '07:00 PM - 08:00 PM', 'available'),
(49, 3, '08:00 PM - 09:00 PM', 'available'),
(50, 3, '09:00 PM - 10:00 PM', 'available'),
(51, 3, '10:00 PM - 11:00 PM', 'available'),
(52, 4, '06:00 AM - 07:00 AM', 'available'),
(53, 4, '07:00 AM - 08:00 AM', 'available'),
(54, 4, '08:00 AM - 09:00 AM', 'available'),
(55, 4, '09:00 AM - 10:00 AM', 'available'),
(56, 4, '10:00 AM - 11:00 AM', 'available'),
(57, 4, '11:00 AM - 12:00 PM', 'available'),
(58, 4, '12:00 PM - 01:00 PM', 'available'),
(59, 4, '01:00 PM - 02:00 PM', 'available'),
(60, 4, '02:00 PM - 03:00 PM', 'available'),
(61, 4, '03:00 PM - 04:00 PM', 'available'),
(62, 4, '04:00 PM - 05:00 PM', 'available'),
(63, 4, '05:00 PM - 06:00 PM', 'available'),
(64, 4, '06:00 PM - 07:00 PM', 'available'),
(65, 4, '07:00 PM - 08:00 PM', 'available'),
(66, 4, '08:00 PM - 09:00 PM', 'available'),
(67, 4, '09:00 PM - 10:00 PM', 'available'),
(68, 4, '10:00 PM - 11:00 PM', 'available'),
(69, 5, '06:00 AM - 07:00 AM', 'available'),
(70, 5, '07:00 AM - 08:00 AM', 'available'),
(71, 5, '08:00 AM - 09:00 AM', 'available'),
(72, 5, '09:00 AM - 10:00 AM', 'available'),
(73, 5, '10:00 AM - 11:00 AM', 'available'),
(74, 5, '11:00 AM - 12:00 PM', 'available'),
(75, 5, '12:00 PM - 01:00 PM', 'available'),
(76, 5, '01:00 PM - 02:00 PM', 'available'),
(77, 5, '02:00 PM - 03:00 PM', 'available'),
(78, 5, '03:00 PM - 04:00 PM', 'available'),
(79, 5, '04:00 PM - 05:00 PM', 'available'),
(80, 5, '05:00 PM - 06:00 PM', 'available'),
(81, 5, '06:00 PM - 07:00 PM', 'available'),
(82, 5, '07:00 PM - 08:00 PM', 'available'),
(83, 5, '08:00 PM - 09:00 PM', 'available'),
(84, 5, '09:00 PM - 10:00 PM', 'available'),
(85, 5, '10:00 PM - 11:00 PM', 'available'),
(86, 6, '06:00 AM - 07:00 AM', 'available'),
(87, 6, '07:00 AM - 08:00 AM', 'available'),
(88, 6, '08:00 AM - 09:00 AM', 'available'),
(89, 6, '09:00 AM - 10:00 AM', 'available'),
(90, 6, '10:00 AM - 11:00 AM', 'available'),
(91, 6, '11:00 AM - 12:00 PM', 'available'),
(92, 6, '12:00 PM - 01:00 PM', 'available'),
(93, 6, '01:00 PM - 02:00 PM', 'available'),
(94, 6, '02:00 PM - 03:00 PM', 'available'),
(95, 6, '03:00 PM - 04:00 PM', 'available'),
(96, 6, '04:00 PM - 05:00 PM', 'available'),
(97, 6, '05:00 PM - 06:00 PM', 'available'),
(98, 6, '06:00 PM - 07:00 PM', 'available'),
(99, 6, '07:00 PM - 08:00 PM', 'available'),
(100, 6, '08:00 PM - 09:00 PM', 'available'),
(101, 6, '09:00 PM - 10:00 PM', 'available'),
(102, 6, '10:00 PM - 11:00 PM', 'available'),
(103, 7, '06:00 AM - 07:00 AM', 'available'),
(104, 7, '07:00 AM - 08:00 AM', 'available'),
(105, 7, '08:00 AM - 09:00 AM', 'available'),
(106, 7, '09:00 AM - 10:00 AM', 'available'),
(107, 7, '10:00 AM - 11:00 AM', 'available'),
(108, 7, '11:00 AM - 12:00 PM', 'available'),
(109, 7, '12:00 PM - 01:00 PM', 'available'),
(110, 7, '01:00 PM - 02:00 PM', 'available'),
(111, 7, '02:00 PM - 03:00 PM', 'available'),
(112, 7, '03:00 PM - 04:00 PM', 'available'),
(113, 7, '04:00 PM - 05:00 PM', 'available'),
(114, 7, '05:00 PM - 06:00 PM', 'available'),
(115, 7, '06:00 PM - 07:00 PM', 'available'),
(116, 7, '07:00 PM - 08:00 PM', 'available'),
(117, 7, '08:00 PM - 09:00 PM', 'available'),
(118, 7, '09:00 PM - 10:00 PM', 'available'),
(119, 7, '10:00 PM - 11:00 PM', 'available'),
(120, 8, '06:00 AM - 07:00 AM', 'available'),
(121, 8, '07:00 AM - 08:00 AM', 'available'),
(122, 8, '08:00 AM - 09:00 AM', 'available'),
(123, 8, '09:00 AM - 10:00 AM', 'available'),
(124, 8, '10:00 AM - 11:00 AM', 'available'),
(125, 8, '11:00 AM - 12:00 PM', 'available'),
(126, 8, '12:00 PM - 01:00 PM', 'available'),
(127, 8, '01:00 PM - 02:00 PM', 'available'),
(128, 8, '02:00 PM - 03:00 PM', 'available'),
(129, 8, '03:00 PM - 04:00 PM', 'available'),
(130, 8, '04:00 PM - 05:00 PM', 'available'),
(131, 8, '05:00 PM - 06:00 PM', 'available'),
(132, 8, '06:00 PM - 07:00 PM', 'available'),
(133, 8, '07:00 PM - 08:00 PM', 'available'),
(134, 8, '08:00 PM - 09:00 PM', 'available'),
(135, 8, '09:00 PM - 10:00 PM', 'available'),
(136, 8, '10:00 PM - 11:00 PM', 'available'),
(137, 9, '06:00 AM - 07:00 AM', 'available'),
(138, 9, '07:00 AM - 08:00 AM', 'available'),
(139, 9, '08:00 AM - 09:00 AM', 'available'),
(140, 9, '09:00 AM - 10:00 AM', 'available'),
(141, 9, '10:00 AM - 11:00 AM', 'available'),
(142, 9, '11:00 AM - 12:00 PM', 'available'),
(143, 9, '12:00 PM - 01:00 PM', 'available'),
(144, 9, '01:00 PM - 02:00 PM', 'available'),
(145, 9, '02:00 PM - 03:00 PM', 'available'),
(146, 9, '03:00 PM - 04:00 PM', 'available'),
(147, 9, '04:00 PM - 05:00 PM', 'available'),
(148, 9, '05:00 PM - 06:00 PM', 'available'),
(149, 9, '06:00 PM - 07:00 PM', 'available'),
(150, 9, '07:00 PM - 08:00 PM', 'available'),
(151, 9, '08:00 PM - 09:00 PM', 'available'),
(152, 9, '09:00 PM - 10:00 PM', 'available'),
(153, 9, '10:00 PM - 11:00 PM', 'available'),
(154, 10, '06:00 AM - 07:00 AM', 'available'),
(155, 10, '07:00 AM - 08:00 AM', 'available'),
(156, 10, '08:00 AM - 09:00 AM', 'available'),
(157, 10, '09:00 AM - 10:00 AM', 'available'),
(158, 10, '10:00 AM - 11:00 AM', 'available'),
(159, 10, '11:00 AM - 12:00 PM', 'available'),
(160, 10, '12:00 PM - 01:00 PM', 'available'),
(161, 10, '01:00 PM - 02:00 PM', 'available'),
(162, 10, '02:00 PM - 03:00 PM', 'available'),
(163, 10, '03:00 PM - 04:00 PM', 'available'),
(164, 10, '04:00 PM - 05:00 PM', 'available'),
(165, 10, '05:00 PM - 06:00 PM', 'available'),
(166, 10, '06:00 PM - 07:00 PM', 'available'),
(167, 10, '07:00 PM - 08:00 PM', 'available'),
(168, 10, '08:00 PM - 09:00 PM', 'available'),
(169, 10, '09:00 PM - 10:00 PM', 'available'),
(170, 10, '10:00 PM - 11:00 PM', 'available'),
(171, 11, '06:00 AM - 07:00 AM', 'available'),
(172, 11, '07:00 AM - 08:00 AM', 'available'),
(173, 11, '08:00 AM - 09:00 AM', 'available'),
(174, 11, '09:00 AM - 10:00 AM', 'available'),
(175, 11, '10:00 AM - 11:00 AM', 'available'),
(176, 11, '11:00 AM - 12:00 PM', 'available'),
(177, 11, '12:00 PM - 01:00 PM', 'available'),
(178, 11, '01:00 PM - 02:00 PM', 'available'),
(179, 11, '02:00 PM - 03:00 PM', 'available'),
(180, 11, '03:00 PM - 04:00 PM', 'available'),
(181, 11, '04:00 PM - 05:00 PM', 'available'),
(182, 11, '05:00 PM - 06:00 PM', 'available'),
(183, 11, '06:00 PM - 07:00 PM', 'available'),
(184, 11, '07:00 PM - 08:00 PM', 'available'),
(185, 11, '08:00 PM - 09:00 PM', 'available'),
(186, 11, '09:00 PM - 10:00 PM', 'available'),
(187, 11, '10:00 PM - 11:00 PM', 'available'),
(188, 12, '06:00 AM - 07:00 AM', 'available'),
(189, 12, '07:00 AM - 08:00 AM', 'available'),
(190, 12, '08:00 AM - 09:00 AM', 'available'),
(191, 12, '09:00 AM - 10:00 AM', 'available'),
(192, 12, '10:00 AM - 11:00 AM', 'available'),
(193, 12, '11:00 AM - 12:00 PM', 'available'),
(194, 12, '12:00 PM - 01:00 PM', 'available'),
(195, 12, '01:00 PM - 02:00 PM', 'available'),
(196, 12, '02:00 PM - 03:00 PM', 'available'),
(197, 12, '03:00 PM - 04:00 PM', 'available'),
(198, 12, '04:00 PM - 05:00 PM', 'available'),
(199, 12, '05:00 PM - 06:00 PM', 'available'),
(200, 12, '06:00 PM - 07:00 PM', 'available'),
(201, 12, '07:00 PM - 08:00 PM', 'available'),
(202, 12, '08:00 PM - 09:00 PM', 'available'),
(203, 12, '09:00 PM - 10:00 PM', 'available'),
(204, 12, '10:00 PM - 11:00 PM', 'available'),
(205, 13, '06:00 AM - 07:00 AM', 'available'),
(206, 13, '07:00 AM - 08:00 AM', 'available'),
(207, 13, '08:00 AM - 09:00 AM', 'available'),
(208, 13, '09:00 AM - 10:00 AM', 'available'),
(209, 13, '10:00 AM - 11:00 AM', 'available'),
(210, 13, '11:00 AM - 12:00 PM', 'available'),
(211, 13, '12:00 PM - 01:00 PM', 'available'),
(212, 13, '01:00 PM - 02:00 PM', 'available'),
(213, 13, '02:00 PM - 03:00 PM', 'available'),
(214, 13, '03:00 PM - 04:00 PM', 'available'),
(215, 13, '04:00 PM - 05:00 PM', 'available'),
(216, 13, '05:00 PM - 06:00 PM', 'available'),
(217, 13, '06:00 PM - 07:00 PM', 'available'),
(218, 13, '07:00 PM - 08:00 PM', 'available'),
(219, 13, '08:00 PM - 09:00 PM', 'available'),
(220, 13, '09:00 PM - 10:00 PM', 'available'),
(221, 13, '10:00 PM - 11:00 PM', 'available'),
(222, 14, '06:00 AM - 07:00 AM', 'available'),
(223, 14, '07:00 AM - 08:00 AM', 'available'),
(224, 14, '08:00 AM - 09:00 AM', 'available'),
(225, 14, '09:00 AM - 10:00 AM', 'available'),
(226, 14, '10:00 AM - 11:00 AM', 'available'),
(227, 14, '11:00 AM - 12:00 PM', 'available'),
(228, 14, '12:00 PM - 01:00 PM', 'available'),
(229, 14, '01:00 PM - 02:00 PM', 'available'),
(230, 14, '02:00 PM - 03:00 PM', 'available'),
(231, 14, '03:00 PM - 04:00 PM', 'available'),
(232, 14, '04:00 PM - 05:00 PM', 'available'),
(233, 14, '05:00 PM - 06:00 PM', 'available'),
(234, 14, '06:00 PM - 07:00 PM', 'available'),
(235, 14, '07:00 PM - 08:00 PM', 'available'),
(236, 14, '08:00 PM - 09:00 PM', 'available'),
(237, 14, '09:00 PM - 10:00 PM', 'available'),
(238, 14, '10:00 PM - 11:00 PM', 'available'),
(240, 15, '06:00 PM - 07:00 PM', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `city`, `password`, `status`, `created_at`, `reset_token`) VALUES
(1, 'John Doe', 'john@example.com', '9876543210', 'Mumbai', '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'active', '2026-02-25 08:25:11', NULL),
(2, 'Alice Smith', 'alice@example.com', '9876543211', 'Delhi', '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'active', '2026-02-27 08:25:11', NULL),
(3, 'Raj Khanna', 'raj@example.com', '9876543212', 'Bangalore', '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'active', '2026-03-02 08:25:11', NULL),
(4, 'Sarah Lee', 'sarah@example.com', '9876543213', 'Mumbai', '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'active', '2026-03-05 08:25:11', NULL),
(5, 'Tom Alter', 'tom@example.com', '9876543214', 'Chennai', '$2y$10$zyvtZ00Or8RxnSXVeD5Zj.6aanxqFAIOlkMovXe4Mge7MIQ/.08TK', 'active', '2026-03-06 08:25:11', NULL),
(7, 'Alice Smith', 'alice@example.com', '9876543211', 'Delhi', '$2y$10$BRf/VBHyZDJhCkd87EbSx.OEwJi5GWnJzom/pwPSngZU9j0d9.QDW', 'active', '2026-02-27 10:08:37', NULL),
(8, 'Raj Khanna', 'raj@example.com', '9876543212', 'Bangalore', '$2y$10$BRf/VBHyZDJhCkd87EbSx.OEwJi5GWnJzom/pwPSngZU9j0d9.QDW', 'active', '2026-03-02 10:08:37', NULL),
(9, 'Sarah Lee', 'sarah@example.com', '9876543213', 'Mumbai', '$2y$10$BRf/VBHyZDJhCkd87EbSx.OEwJi5GWnJzom/pwPSngZU9j0d9.QDW', 'active', '2026-03-05 10:08:37', NULL),
(10, 'Tom Alter', 'tom@example.com', '9876543214', 'Chennai', '$2y$10$BRf/VBHyZDJhCkd87EbSx.OEwJi5GWnJzom/pwPSngZU9j0d9.QDW', 'suspended', '2026-03-06 10:08:37', NULL),
(11, 'Jay', 'user@playora.com', '1234567890', 'Mumbai', '$2y$10$HNfpmfmQQScOTt9CevCTKefcJOdEhbZayUxNf2W1WBsUodThiAzuC', 'active', '2026-03-09 06:32:33', NULL),
(12, 'Rahul Patel', 'rahul@playora.com', '9876500001', 'Ahmedabad', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(13, 'Amit Shah', 'amit@playora.com', '9876500002', 'Ahmedabad', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(14, 'Neha Verma', 'neha@playora.com', '9876500003', 'Delhi', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(15, 'Karan Mehta', 'karan@playora.com', '9876500004', 'Mumbai', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(16, 'Priya Nair', 'priya@playora.com', '9876500005', 'Bangalore', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(17, 'Rohit Sharma', 'rohit@playora.com', '9876500006', 'Mumbai', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(18, 'Sanjay Patel', 'sanjay@playora.com', '9876500007', 'Ahmedabad', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(19, 'Mehul Desai', 'mehul@playora.com', '9876500008', 'Surat', '$2y$10$testpassword', 'active', '2026-03-09 07:02:48', NULL),
(20, 'Jay', 'barot8084@gmail.com', '7984429604', 'Ahmedabad', '$2y$10$LzT2iHbEBG7Z2n3GYH.YyuV.8opysPy9icxr2PRTTq6FZphKT7DRq', 'active', '2026-03-13 10:50:07', '632aff546beaa27cb4784cb00c90904fcca5354266694dc645df8fa8944f6c6d');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_requests`
--
ALTER TABLE `booking_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_slots`
--
ALTER TABLE `booking_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `favorite_turfs`
--
ALTER TABLE `favorite_turfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owners`
--
ALTER TABLE `owners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `owner_requests`
--
ALTER TABLE `owner_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sports`
--
ALTER TABLE `sports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `turfs`
--
ALTER TABLE `turfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `turf_slots`
--
ALTER TABLE `turf_slots`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `booking_requests`
--
ALTER TABLE `booking_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_slots`
--
ALTER TABLE `booking_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `favorite_turfs`
--
ALTER TABLE `favorite_turfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `owners`
--
ALTER TABLE `owners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `owner_requests`
--
ALTER TABLE `owner_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sports`
--
ALTER TABLE `sports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `turfs`
--
ALTER TABLE `turfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `turf_slots`
--
ALTER TABLE `turf_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
