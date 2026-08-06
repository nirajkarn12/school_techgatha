-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 06, 2026 at 07:03 AM
-- Server version: 9.1.0
-- PHP Version: 8.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resinnep_ecommerceweb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_achiever`
--

DROP TABLE IF EXISTS `tbl_achiever`;
CREATE TABLE IF NOT EXISTS `tbl_achiever` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `photo` varchar(255) NOT NULL DEFAULT '',
  `achievement` varchar(255) NOT NULL DEFAULT '',
  `year` varchar(20) NOT NULL DEFAULT '',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_achiever`
--

INSERT INTO `tbl_achiever` (`id`, `name`, `photo`, `achievement`, `year`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Amita Nepal', 'achiever-1-1784889520-c4232b.jpg', 'SEE   perfect (4 CGPA)', '2026', 0, 'Active', '2026-07-23 14:58:29'),
(2, 'smiriti shrestha', 'achiever-2-1784889512-29c4f0.jpg', 'SEE   perfect (4 CGPA)', '2029', 0, 'Active', '2026-07-23 15:38:00'),
(3, 'Raghav kumar karna', 'achiever-3-1784889500-327ce9.jpg', 'SEE -2082  perfect (4 CGPA)', '2082', 0, 'Active', '2026-07-23 15:38:23'),
(4, 'bibasha karki', 'achiever-4-1784889582-bce056.png', '', '2029', 0, 'Active', '2026-07-23 15:43:42');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admission`
--

DROP TABLE IF EXISTS `tbl_admission`;
CREATE TABLE IF NOT EXISTS `tbl_admission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_name` varchar(150) NOT NULL,
  `dob` date DEFAULT NULL,
  `gender` varchar(20) NOT NULL DEFAULT '',
  `class_applied` varchar(80) NOT NULL DEFAULT '',
  `parent_name` varchar(150) NOT NULL DEFAULT '',
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `address` text,
  `previous_school` varchar(255) NOT NULL DEFAULT '',
  `message` text,
  `status` varchar(30) NOT NULL DEFAULT 'New',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_birthday_student`
--

DROP TABLE IF EXISTS `tbl_birthday_student`;
CREATE TABLE IF NOT EXISTS `tbl_birthday_student` (
  `id` int NOT NULL AUTO_INCREMENT,
  `template_id` int NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `class_name` varchar(100) NOT NULL DEFAULT '',
  `birthday_date` varchar(50) NOT NULL DEFAULT '',
  `details` text,
  `student_image` varchar(255) NOT NULL DEFAULT '',
  `generated_image` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_birthday_student`
--

INSERT INTO `tbl_birthday_student` (`id`, `template_id`, `name`, `class_name`, `birthday_date`, `details`, `student_image`, `generated_image`, `status`, `sort_order`, `created_at`) VALUES
(1, 1, 'NIRAJ KARNA', 'Grade 8', '', '', 'birthday-student-1-1785839465-0aa2c4.jpg', 'birthday-1-1785911357.jpg', 'Active', 0, '2026-08-04 15:33:32'),
(2, 3, 'hGdlbg', 'sf] z\'esfdgf', '', 'ghhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 'birthday-student-2-1785913693-effdef.png', 'birthday-2-1785925904.jpg', 'Active', 0, '2026-08-05 12:21:05');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_birthday_template`
--

DROP TABLE IF EXISTS `tbl_birthday_template`;
CREATE TABLE IF NOT EXISTS `tbl_birthday_template` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `template_image` varchar(255) NOT NULL DEFAULT '',
  `output_x` int NOT NULL DEFAULT '0',
  `output_y` int NOT NULL DEFAULT '0',
  `output_width` int NOT NULL DEFAULT '0',
  `output_height` int NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_birthday_template`
--

INSERT INTO `tbl_birthday_template` (`id`, `title`, `template_image`, `output_x`, `output_y`, `output_width`, `output_height`, `status`, `sort_order`, `created_at`) VALUES
(1, 'Default Birthday Card', 'birthday-template-1-1785836947-801f56.png', 130, 220, 480, 520, 'Active', 0, '2026-08-04 15:33:04'),
(2, 'new_one', 'birthday-template-1785911740-a8033e.png', 285, 205, 510, 590, 'Active', 0, '2026-08-05 12:20:40'),
(3, 'tenplate_day', 'birthday-template-3-1785913617-ff5f3c.jpg', 285, 205, 510, 590, 'Active', 0, '2026-08-05 12:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_booking_assignment`
--

DROP TABLE IF EXISTS `tbl_booking_assignment`;
CREATE TABLE IF NOT EXISTS `tbl_booking_assignment` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `payment_id` varchar(255) NOT NULL,
  `payment_row_id` int NOT NULL DEFAULT '0',
  `staff_id` int NOT NULL,
  `assigned_by` int NOT NULL DEFAULT '0',
  `assigned_at` datetime DEFAULT NULL,
  `job_status` varchar(30) NOT NULL DEFAULT 'Assigned',
  `service_address` text,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(30) DEFAULT NULL,
  `client_name` varchar(255) NOT NULL DEFAULT '',
  `client_phone` varchar(50) NOT NULL DEFAULT '',
  `client_email` varchar(255) NOT NULL DEFAULT '',
  `service_name` varchar(255) NOT NULL DEFAULT '',
  `service_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission_type` varchar(20) NOT NULL DEFAULT 'percent',
  `commission_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `commission_status` varchar(20) NOT NULL DEFAULT 'pending',
  `staff_notes` text,
  `admin_notes` text,
  `completed_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `arrived_at` datetime DEFAULT NULL,
  `checkin_lat` decimal(10,7) DEFAULT NULL,
  `checkin_lng` decimal(10,7) DEFAULT NULL,
  `commission_share_percent` decimal(5,2) NOT NULL DEFAULT '100.00',
  `service_lat` decimal(10,7) DEFAULT NULL,
  `service_lng` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`assignment_id`),
  KEY `idx_payment_id` (`payment_id`),
  KEY `idx_staff_id` (`staff_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_booking_assignment`
--

INSERT INTO `tbl_booking_assignment` (`assignment_id`, `payment_id`, `payment_row_id`, `staff_id`, `assigned_by`, `assigned_at`, `job_status`, `service_address`, `preferred_date`, `preferred_time`, `client_name`, `client_phone`, `client_email`, `service_name`, `service_amount`, `commission_type`, `commission_value`, `commission_amount`, `commission_status`, `staff_notes`, `admin_notes`, `completed_at`, `approved_at`, `paid_at`, `arrived_at`, `checkin_lat`, `checkin_lng`, `commission_share_percent`, `service_lat`, `service_lng`) VALUES
(1, 'ORD-20260703172557', 90, 1, 1, '2026-07-17 15:05:45', 'Completed', 'nnbnbn', '2026-07-22', '10', 'ram', '9999999999', 'admin@mail.com', 'Dress Mould ', 0.00, 'fixed', 100.00, 100.00, 'paid', '', 'fgfgfgfgfgf', '2026-07-17 15:12:44', '2026-07-17 15:12:44', '2026-07-21 14:28:41', '2026-07-17 15:06:27', NULL, NULL, 100.00, NULL, NULL),
(2, 'ORD-20260717165001', 91, 1, 1, '2026-07-17 16:50:19', 'Assigned', 'Manamaiju\r\nkathmandu', NULL, '', 'hala', '9999999999', 'pandey@gmail.com', 'Dress Mould ', 0.00, 'percent', 35.00, 0.00, 'pending', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 100.00, NULL, NULL),
(3, 'ORD-20260721121404', 92, 1, 1, '2026-07-21 12:15:11', 'Assigned', 'New Millenium School, Barahi 1st Cross, Kupondole, Lalitpur-10, Lalitpur, Lalitpur Metropolitan City, Lalitpur, Bagamati Province, 00779, Nepal', '2026-07-25', '23:46', 'ram', '9999999999', 'pandey@gmail.com', 'Rose container wuth lid ', 0.00, 'percent', 35.00, 0.00, 'pending', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 100.00, 27.6855000, 85.3229000),
(4, 'ORD-20260721142432', 94, 1, 1, '2026-07-21 14:25:34', 'Assigned', 'Manamaiju, Bhagwan Bahal Marg, Thamel, Kathmandu-26, Kathmandu Metropolitan City, Kathmandu, Bagamati Province, 25511, Nepal', '2026-07-24', '02:24', 'ram', '9999999999', 'pandey@gmail.com', 'Baby Angel Mould ', 525.00, 'percent', 35.00, 183.75, 'pending', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 100.00, 27.7162368, 85.3127519),
(5, 'ORD-20260721142432', 94, 2, 1, '2026-07-21 14:26:03', 'Completed', 'Manamaiju, Bhagwan Bahal Marg, Thamel, Kathmandu-26, Kathmandu Metropolitan City, Kathmandu, Bagamati Province, 25511, Nepal', '2026-07-24', '02:24', 'ram', '9999999999', 'pandey@gmail.com', 'Baby Angel Mould ', 525.00, 'fixed', 5000.00, 2500.00, 'paid', '', '', '2026-07-21 14:27:07', '2026-07-21 14:27:07', '2026-07-21 14:28:37', '2026-07-21 14:26:57', NULL, NULL, 50.00, 27.7162368, 85.3127519),
(6, 'ORD-20260721144500', 95, 2, 1, '2026-07-21 14:45:55', 'Assigned', 'Belbari-08, Belbari, Morang, Koshi Province, 56600, Nepal', '2026-07-22', '02:45', 'Niraj Karna', '9801076273', 'nirajk_mi@yonefu.info', 'niraj', 360.00, 'percent', 20.00, 7.20, 'pending', NULL, 'ghghhggh', NULL, NULL, NULL, NULL, NULL, NULL, 10.00, 26.6105832, 87.4728292),
(7, 'ORD-20260721144500', 95, 1, 1, '2026-07-21 14:49:05', 'Assigned', 'Belbari-08, Belbari, Morang, Koshi Province, 56600, Nepal', '2026-07-22', '02:45', 'Niraj Karna', '9801076273', 'nirajk_mi@yonefu.info', 'niraj', 360.00, 'percent', 35.00, 126.00, 'pending', NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, 100.00, 26.6105832, 87.4728292);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_brochure`
--

DROP TABLE IF EXISTS `tbl_brochure`;
CREATE TABLE IF NOT EXISTS `tbl_brochure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `year` varchar(20) NOT NULL DEFAULT '',
  `image` varchar(255) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_brochure`
--

INSERT INTO `tbl_brochure` (`id`, `title`, `year`, `image`, `file`, `sort_order`, `status`, `created_at`) VALUES
(1, 'school propectus', '2026', 'brochure-1-1784800253.jpg', 'brochure-file-1.docx', 0, 'Active', '2026-07-23 15:12:12'),
(2, 'primary Brochure', '2025', 'brochure-2-1785394999-75ecd4.jpg', '', 0, 'Active', '2026-07-23 15:28:11'),
(3, 'Brochure-secondary annual', '2027', 'brochure-3-1784800155.png', '', 0, 'Active', '2026-07-23 15:28:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_calendar_event`
--

DROP TABLE IF EXISTS `tbl_calendar_event`;
CREATE TABLE IF NOT EXISTS `tbl_calendar_event` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `event_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `event_time` varchar(50) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_date_idx` (`event_date`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_calendar_event`
--

INSERT INTO `tbl_calendar_event` (`id`, `title`, `description`, `event_date`, `end_date`, `event_time`, `location`, `status`, `created_at`) VALUES
(1, 'debate', '', '2026-07-23', '2026-07-23', '10:10 AM', '', 'Active', '2026-07-23 10:57:21');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_career_application`
--

DROP TABLE IF EXISTS `tbl_career_application`;
CREATE TABLE IF NOT EXISTS `tbl_career_application` (
  `id` int NOT NULL AUTO_INCREMENT,
  `vacancy_id` int NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `phone` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `resume_note` text,
  `cover_letter` text,
  `status` varchar(30) NOT NULL DEFAULT 'New',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vacancy_id_idx` (`vacancy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_career_application`
--

INSERT INTO `tbl_career_application` (`id`, `vacancy_id`, `full_name`, `phone`, `email`, `resume_note`, `cover_letter`, `status`, `created_at`) VALUES
(1, 1, 'ram', '9999999999', 'pandey@gmail.com', 'hjhhjhj', 'hjhjhjhjhj', 'Shortlisted', '2026-07-23 18:22:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_client`
--

DROP TABLE IF EXISTS `tbl_client`;
CREATE TABLE IF NOT EXISTS `tbl_client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL,
  `website_url` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_client`
--

INSERT INTO `tbl_client` (`id`, `name`, `logo`, `website_url`, `status`, `sort_order`, `created_at`) VALUES
(1, 'bnnbn', 'client-1.png', '', 'Inactive', 0, '2026-07-17 16:18:24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_color`
--

DROP TABLE IF EXISTS `tbl_color`;
CREATE TABLE IF NOT EXISTS `tbl_color` (
  `color_id` int NOT NULL AUTO_INCREMENT,
  `color_name` varchar(255) NOT NULL,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_color`
--

INSERT INTO `tbl_color` (`color_id`, `color_name`) VALUES
(1, 'Red'),
(2, 'Black'),
(3, 'Blue'),
(4, 'Yellow'),
(5, 'Green'),
(6, 'White'),
(7, 'Orange'),
(8, 'Brown'),
(9, 'Tan'),
(10, 'Pink'),
(11, 'Mixed'),
(12, 'Lightblue'),
(13, 'Violet'),
(14, 'Light Purple'),
(15, 'Salmon'),
(16, 'Gold'),
(17, 'Gray'),
(18, 'Ash'),
(19, 'Maroon'),
(20, 'Silver'),
(21, 'Dark Clay'),
(22, 'Cognac'),
(23, 'Coffee'),
(24, 'Charcoal'),
(25, 'Navy'),
(26, 'Fuchsia'),
(27, 'Olive'),
(28, 'Burgundy'),
(29, 'Midnight Blue'),
(30, 'Rose Gold'),
(31, 'Golden ');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_country`
--

DROP TABLE IF EXISTS `tbl_country`;
CREATE TABLE IF NOT EXISTS `tbl_country` (
  `country_id` int NOT NULL AUTO_INCREMENT,
  `country_name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=251 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `tbl_country`
--

INSERT INTO `tbl_country` (`country_id`, `country_name`) VALUES
(250, 'kathmandu');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer`
--

DROP TABLE IF EXISTS `tbl_customer`;
CREATE TABLE IF NOT EXISTS `tbl_customer` (
  `cust_id` int NOT NULL AUTO_INCREMENT,
  `cust_name` varchar(100) NOT NULL,
  `cust_cname` varchar(100) NOT NULL,
  `cust_email` varchar(100) NOT NULL,
  `cust_google_id` varchar(64) NOT NULL DEFAULT '',
  `cust_phone` varchar(50) NOT NULL,
  `cust_country` int NOT NULL,
  `cust_address` text NOT NULL,
  `cust_city` varchar(100) NOT NULL,
  `cust_state` varchar(100) NOT NULL,
  `cust_zip` varchar(30) NOT NULL,
  `cust_b_name` varchar(100) NOT NULL,
  `cust_b_cname` varchar(100) NOT NULL,
  `cust_b_phone` varchar(50) NOT NULL,
  `cust_b_country` int NOT NULL,
  `cust_b_address` text NOT NULL,
  `cust_b_city` varchar(100) NOT NULL,
  `cust_b_state` varchar(100) NOT NULL,
  `cust_b_zip` varchar(30) NOT NULL,
  `cust_s_name` varchar(100) NOT NULL,
  `cust_s_cname` varchar(100) NOT NULL,
  `cust_s_phone` varchar(50) NOT NULL,
  `cust_s_country` int NOT NULL,
  `cust_s_address` text NOT NULL,
  `cust_s_city` varchar(100) NOT NULL,
  `cust_s_state` varchar(100) NOT NULL,
  `cust_s_zip` varchar(30) NOT NULL,
  `cust_password` varchar(100) NOT NULL,
  `cust_token` varchar(255) NOT NULL,
  `cust_datetime` varchar(100) NOT NULL,
  `cust_timestamp` varchar(100) NOT NULL,
  `cust_status` int NOT NULL,
  PRIMARY KEY (`cust_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_customer`
--

INSERT INTO `tbl_customer` (`cust_id`, `cust_name`, `cust_cname`, `cust_email`, `cust_google_id`, `cust_phone`, `cust_country`, `cust_address`, `cust_city`, `cust_state`, `cust_zip`, `cust_b_name`, `cust_b_cname`, `cust_b_phone`, `cust_b_country`, `cust_b_address`, `cust_b_city`, `cust_b_state`, `cust_b_zip`, `cust_s_name`, `cust_s_cname`, `cust_s_phone`, `cust_s_country`, `cust_s_address`, `cust_s_city`, `cust_s_state`, `cust_s_zip`, `cust_password`, `cust_token`, `cust_datetime`, `cust_timestamp`, `cust_status`) VALUES
(32, 'uday kumar pandit', '', 'sastikatrading@gmail.com', '', '9801076273', 250, '', 'Biratnagar', 'bagmati', '', '', '', '', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', '', 1),
(33, 'uday kumar pandit', '', 'nirajkarna66@gmail.com', '114860328817943707086', '9801076273', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '$2y$10$abgFL9/9eEHwljuFgILvbuyastlecoCU.oYc.cxJoJ35FBC69N0C6', '', '2026-07-02 11:18:40', '1782970420', 1),
(34, 'salman', '', 'pandey@gmail.com', '', '9999999999', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '$2y$10$8bzjz1NNJyrNDctXcpA6Iugfzt292LOLJfA0sf4CTP4ewTZArjLgi', '', '2026-07-02 16:32:29', '1782989249', 1),
(35, 'up', '', 'na66@gmail.com', '', '9999999999', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '$2y$10$Vyp6TWdyXJ3tK2rMS5L.5eTZEmuPxUkZTLQtf5T1AslfYX4H/RH5.', '', '2026-07-02 16:53:43', '1782990523', 1),
(36, 'ram', '', 'rajan@gmail.com', '', '9999999999', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '$2y$10$BzKdig1DDnaWo2RZ2dSPi.FxEE6e.MMT62XkEBZ3XSqb8H2X80ozG', '', '2026-07-02 18:08:42', '1782995022', 1),
(37, 'ram', '', 'admin@mail.com', '', '9999999999', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '', '', '', 0, '', '', '', '', '$2y$10$jsujCUIDDNBHluBPitnYWehP2KijZQkXNnJic8cdV8B0t/qt2Y2zW', '', '2026-07-03 12:23:26', '1783060706', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_message`
--

DROP TABLE IF EXISTS `tbl_customer_message`;
CREATE TABLE IF NOT EXISTS `tbl_customer_message` (
  `customer_message_id` int NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `order_detail` text NOT NULL,
  `cust_id` int NOT NULL,
  PRIMARY KEY (`customer_message_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_customer_message`
--

INSERT INTO `tbl_customer_message` (`customer_message_id`, `subject`, `message`, `order_detail`, `cust_id`) VALUES
(9, 'hehfkfnf', 'youhfde bd ebcdd ', '\r\nCustomer Name: Ramesh Khatri<br>\r\nCustomer Email: rameshkhatri6789@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2025-09-08 15:47:48<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 1538<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: 1757339268<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Amazfit GTS 3 Smart Watch for Android iPhone<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 8<br>\r\nUnit Price: 179<br>\r\n            \r\n<br><b><u>Product Item 2</u></b><br>\r\nProduct Name: Loose-fit One-Shoulder Cutout Rib Knit Maxi Dress<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 39<br>\r\n            \r\n<br><b><u>Product Item 3</u></b><br>\r\nProduct Name: Women\'s Tea Length Dress with Rosette Detail (Petite & Regular)<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 67<br>\r\n            ', 20),
(10, 'ghgh', 'ghghghghgh', '\r\nCustomer Name: niraj karna<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: cod<br>\r\nPayment Date: 2025-10-08 06:15:09<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 300<br>\r\nPayment Status: Pending<br>\r\nShipping Status: Pending<br>\r\nPayment Id: 1759896909<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Women\'s Plus-Size Shirt Dress with Gold Hardware<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 2<br>\r\nUnit Price: 100<br>\r\n            ', 23),
(11, 'gnnbnnnn', 'bnbnnbnbnb', '\r\nCustomer Name: niraj karna<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: cod<br>\r\nPayment Date: 2025-10-08 16:18:55<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 220<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: 1759933135<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: New full cover<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 100<br>\r\n            ', 24),
(12, 'gnnbnnnn', 'bnbnnbnbnb', '\r\nCustomer Name: niraj karna<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: cod<br>\r\nPayment Date: 2025-10-08 16:18:55<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 220<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: 1759933135<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: New full cover<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 100<br>\r\n            ', 24),
(13, 'about order 25', 'thank you payment received .', '\r\nCustomer Name: sabita tamang <br>\r\nCustomer Email: grg.cwang@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2025-11-01 14:27:19<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 1650<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Pending<br>\r\nPayment Id: 1762007239<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Peony mold <br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 3<br>\r\nUnit Price: 550<br>\r\n            ', 25),
(14, 'delivery status ', 'delivery has been completed .thank you for choosing us .', '\r\nCustomer Name: sabita tamang <br>\r\nCustomer Email: grg.cwang@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2025-11-01 14:27:19<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 1650<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: 1762007239<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Peony mold <br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 3<br>\r\nUnit Price: 550<br>\r\n            ', 25),
(15, 'nm', 'nmnmnmnm', '\r\nCustomer Name: niraj<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2026-06-30 16:16:02<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 1234000<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Pending<br>\r\nPayment Id: ORD-1782815462<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: 3 in 1 Floral Topping mold<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 3<br>\r\nUnit Price: 380<br>\r\n            ', 0),
(16, 'df', 'dfg', '\r\nCustomer Name: niraj<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2026-06-30 16:16:02<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 1234000<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: ORD-1782815462<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: 3 in 1 Floral Topping mold<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 3<br>\r\nUnit Price: 380<br>\r\n            ', 0),
(17, 'df', 'hgjhnnbnb', '\r\nCustomer Name: uday kumar pandit<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2026-07-02 18:11:20<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 350<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: ORD-20260702181120<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Panda Mould<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 360<br>\r\n            ', 33),
(18, 'df', 'hgjhnnbnb', '\r\nCustomer Name: uday kumar pandit<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2026-07-02 18:11:20<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 350<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: ORD-20260702181120<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Panda Mould<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 360<br>\r\n            ', 33),
(19, 'df', 'hgjhnnbnb', '\r\nCustomer Name: uday kumar pandit<br>\r\nCustomer Email: nirajkarna66@gmail.com<br>\r\nPayment Method: esewa<br>\r\nPayment Date: 2026-07-02 18:11:20<br>\r\nPayment Details: <br><br>\r\nPaid Amount: 350<br>\r\nPayment Status: Completed<br>\r\nShipping Status: Completed<br>\r\nPayment Id: ORD-20260702181120<br>\r\n            \r\n<br><b><u>Product Item 1</u></b><br>\r\nProduct Name: Panda Mould<br>\r\nSize: <br>\r\nColor: <br>\r\nQuantity: 1<br>\r\nUnit Price: 360<br>\r\n            ', 33);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_elections`
--

DROP TABLE IF EXISTS `tbl_elections`;
CREATE TABLE IF NOT EXISTS `tbl_elections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `candidate_name` varchar(255) NOT NULL,
  `election_post` varchar(255) NOT NULL,
  `candidate_image` varchar(255) NOT NULL,
  `vote_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_elections`
--

INSERT INTO `tbl_elections` (`id`, `candidate_name`, `election_post`, `candidate_image`, `vote_count`, `is_active`, `created_at`) VALUES
(1, 'सागर कुमार श्रेष्ठ', 'विद्यालय कप्तान', 'candidate-1-1785302490-0eab68.png', 18, 1, '2026-07-29 04:38:22'),
(2, 'सुस्मिता कुमारी शर्मा', 'विद्यालय कप्तान', 'candidate-2-1785302502-c4270c.png', 5, 1, '2026-07-29 04:38:22'),
(3, 'रोशन प्रसाद अधिकारी', 'उप-कप्तान', 'candidate-3-1785302543-8f10b8.png', 5, 1, '2026-07-29 04:38:22'),
(4, 'अञ्जली देवी गौतम', 'उप-कप्तान', 'candidate-4-1785302510-3107ea.png', 4, 1, '2026-07-29 04:38:22'),
(5, 'niraj karna', 'उप-कप्तान', 'candidate-1785304652-254ee6.png', 11, 1, '2026-07-29 05:57:32'),
(6, 'xcxcxc', 'उप-कप्तान', 'candidate-1785304692-2f14f2.png', 2, 1, '2026-07-29 05:58:12'),
(7, 'Sita Karki', 'कप्तान', 'candidate-1785305884-260f78.png', 7, 1, '2026-07-29 06:18:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_end_category`
--

DROP TABLE IF EXISTS `tbl_end_category`;
CREATE TABLE IF NOT EXISTS `tbl_end_category` (
  `ecat_id` int NOT NULL AUTO_INCREMENT,
  `ecat_name` varchar(255) NOT NULL,
  `mcat_id` int NOT NULL,
  PRIMARY KEY (`ecat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_end_category`
--

INSERT INTO `tbl_end_category` (`ecat_id`, `ecat_name`, `mcat_id`) VALUES
(80, 'Round', 18),
(81, 'Hook', 18),
(82, 'Floral Topping Mold', 19),
(83, ' Flower Mold ', 19),
(84, 'Leaf Mold ', 19),
(85, 'All Moulds', 19),
(86, 'Heart Moulds ', 19),
(87, 'Wick Trimmer ', 22),
(88, 'Teddy Mould ', 19);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_faq`
--

DROP TABLE IF EXISTS `tbl_faq`;
CREATE TABLE IF NOT EXISTS `tbl_faq` (
  `faq_id` int NOT NULL AUTO_INCREMENT,
  `faq_title` varchar(255) NOT NULL,
  `faq_content` text NOT NULL,
  PRIMARY KEY (`faq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_faq`
--

INSERT INTO `tbl_faq` (`faq_id`, `faq_title`, `faq_content`) VALUES
(1, 'How to find an item?', '<h3 class=\"checkout-complete-box font-bold txt16\" style=\"box-sizing: inherit; text-rendering: optimizeLegibility; margin: 0.2rem 0px 0.5rem; padding: 0px; line-height: 1.4; background-color: rgb(250, 250, 250);\"><font color=\"#222222\" face=\"opensans, Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif\"><span style=\"font-size: 15.7143px;\"><b>We have a wide range of fabulous products to choose from.</b></span></font></h3><h3 class=\"checkout-complete-box font-bold txt16\" style=\"box-sizing: inherit; text-rendering: optimizeLegibility; margin: 0.2rem 0px 0.5rem; padding: 0px; line-height: 1.4; background-color: rgb(250, 250, 250);\"><span style=\"font-size: 15.7143px; color: rgb(34, 34, 34); font-family: opensans, \" helvetica=\"\" neue\",=\"\" helvetica,=\"\" arial,=\"\" sans-serif;\"=\"\">Tip 1: If you\'re looking for a specific product, use the keyword search box located at the top of the site. Simply type what you are looking for, and prepare to be amazed!</span></h3><h3 class=\"checkout-complete-box font-bold txt16\" style=\"box-sizing: inherit; text-rendering: optimizeLegibility; margin: 0.2rem 0px 0.5rem; padding: 0px; line-height: 1.4; background-color: rgb(250, 250, 250);\"><font color=\"#222222\" face=\"opensans, Helvetica Neue, Helvetica, Helvetica, Arial, sans-serif\"><span style=\"font-size: 15.7143px;\">Tip 2: If you want to explore a category of products, use the Shop Categories in the upper menu, and navigate through your favorite categories where we\'ll feature the best products in each.</span></font><br><br></h3>\r\n'),
(2, 'What is your return policy?', '<p><span style=\"color: rgb(10, 10, 10); font-family: opensans, &quot;Helvetica Neue&quot;, Helvetica, Helvetica, Arial, sans-serif; font-size: 14px; text-align: center;\">You have 15 days to make a refund request after your order has been delivered.</span><br></p>\r\n'),
(3, ' I received a defective/damaged item, can I get a refund?', '<p>In case the item you received is damaged or defective, you could return an item in the same condition as you received it with the original box and/or packaging intact. Once we receive the returned item, we will inspect it and if the item is found to be defective or damaged, we will process the refund along with any shipping fees incurred.<br></p>\r\n'),
(4, 'When are ‘Returns’ not possible?', '<p class=\"a  \" style=\"box-sizing: inherit; text-rendering: optimizeLegibility; line-height: 1.6; margin-bottom: 0.714286rem; padding: 0px; font-size: 14px; color: rgb(10, 10, 10); font-family: opensans, &quot;Helvetica Neue&quot;, Helvetica, Helvetica, Arial, sans-serif; background-color: rgb(250, 250, 250);\">There are a few certain scenarios where it is difficult for us to support returns:</p><ol style=\"box-sizing: inherit; line-height: 1.6; margin-right: 0px; margin-bottom: 0px; margin-left: 1.25rem; padding: 0px; list-style-position: outside; color: rgb(10, 10, 10); font-family: opensans, &quot;Helvetica Neue&quot;, Helvetica, Helvetica, Arial, sans-serif; font-size: 14px; background-color: rgb(250, 250, 250);\"><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Return request is made outside the specified time frame, of 15 days from delivery.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Product is used, damaged, or is not in the same condition as you received it.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Specific categories like innerwear, lingerie, socks and clothing freebies etc.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Defective products which are covered under the manufacturer\'s warranty.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Any consumable item which has been used or installed.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Products with tampered or missing serial numbers.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Anything missing from the package you\'ve received including price tags, labels, original packing, freebies and accessories.</li><li style=\"box-sizing: inherit; margin: 0px; padding: 0px; font-size: inherit;\">Fragile items, hygiene related items.</li></ol>\r\n'),
(8, 'How to place order from website ?', '<p>contact us&nbsp;</p>');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallery`
--

DROP TABLE IF EXISTS `tbl_gallery`;
CREATE TABLE IF NOT EXISTS `tbl_gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `album_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL DEFAULT '',
  `content` text,
  `photo` varchar(255) NOT NULL DEFAULT '',
  `mcat_id` int NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `album_id_idx` (`album_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_gallery`
--

INSERT INTO `tbl_gallery` (`id`, `album_id`, `title`, `content`, `photo`, `mcat_id`, `status`, `sort_order`, `created_at`) VALUES
(36, NULL, 'science labs', '', 'gallery-1784888055-fc1ee2.jpg', 0, 'Active', 3, '2026-07-24 15:59:15'),
(40, NULL, 'Bsc.Nursing', '', 'gallery-1784888055-26c7df.jpg', 0, 'Active', 4, '2026-07-24 15:59:15'),
(41, 4, 'sports', '', 'gallery-album-4-1784888277-1.png', 0, 'Active', 1, '2026-07-24 16:02:57'),
(43, 4, 'library', '', 'gallery-album-4-1784888277-3.png', 0, 'Active', 3, '2026-07-24 16:02:57'),
(44, 4, 'Activity based', '', 'gallery-album-4-1784888277-4.png', 0, 'Active', 4, '2026-07-24 16:02:57'),
(46, 4, '34 acres land area', '', 'gallery-album-4-1784888277-6.png', 0, 'Active', 6, '2026-07-24 16:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallery_album`
--

DROP TABLE IF EXISTS `tbl_gallery_album`;
CREATE TABLE IF NOT EXISTS `tbl_gallery_album` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `cover_photo` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status_sort_idx` (`status`,`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_gallery_album`
--

INSERT INTO `tbl_gallery_album` (`id`, `title`, `description`, `cover_photo`, `status`, `sort_order`, `created_at`) VALUES
(4, 'okk', 'tghgh', 'gallery-album-4-1784888277-1.png', 'Active', 0, '2026-07-24 16:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_language`
--

DROP TABLE IF EXISTS `tbl_language`;
CREATE TABLE IF NOT EXISTS `tbl_language` (
  `lang_id` int NOT NULL AUTO_INCREMENT,
  `lang_name` varchar(255) NOT NULL,
  `lang_value` text NOT NULL,
  PRIMARY KEY (`lang_id`)
) ENGINE=MyISAM AUTO_INCREMENT=164 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_language`
--

INSERT INTO `tbl_language` (`lang_id`, `lang_name`, `lang_value`) VALUES
(1, 'Currency', '$'),
(2, 'Search Product', 'Search Product'),
(3, 'Search', 'Search'),
(4, 'Submit', 'Submit'),
(5, 'Update', 'Update'),
(6, 'Read More', 'Read More'),
(7, 'Serial', 'Serial'),
(8, 'Photo', 'Photo'),
(9, 'Login', 'Login'),
(10, 'Customer Login', 'Customer Login'),
(11, 'Click here to login', 'Click here to login'),
(12, 'Back to Login Page', 'Back to Login Page'),
(13, 'Logged in as', 'Logged in as'),
(14, 'Logout', 'Logout'),
(15, 'Register', 'Register'),
(16, 'Customer Registration', 'Customer Registration'),
(17, 'Registration Successful', 'Registration Successful'),
(18, 'Cart', 'Cart'),
(19, 'View Cart', 'View Cart'),
(20, 'Update Cart', 'Update Cart'),
(21, 'Back to Cart', 'Back to Cart'),
(22, 'Checkout', 'Checkout'),
(23, 'Proceed to Checkout', 'Proceed to Checkout'),
(24, 'Orders', 'Orders'),
(25, 'Order History', 'Order History'),
(26, 'Order Details', 'Order Details'),
(27, 'Payment Date and Time', 'Payment Date and Time'),
(28, 'Transaction ID', 'Transaction ID'),
(29, 'Paid Amount', 'Paid Amount'),
(30, 'Payment Status', 'Payment Status'),
(31, 'Payment Method', 'Payment Method'),
(32, 'Payment ID', 'Payment ID'),
(33, 'Payment Section', 'Payment Section'),
(34, 'Select Payment Method', 'Select Payment Method'),
(35, 'Select a Method', 'Select a Method'),
(36, 'PayPal', 'PayPal'),
(37, 'Stripe', 'Stripe'),
(38, 'Bank Deposit', 'Bank Deposit'),
(39, 'Card Number', 'Card Number'),
(40, 'CVV', 'CVV'),
(41, 'Month', 'Month'),
(42, 'Year', 'Year'),
(43, 'Send to this Details', 'Send to this Details'),
(44, 'Transaction Information', 'Transaction Information'),
(45, 'Include transaction id and other information correctly', 'Include transaction id and other information correctly'),
(46, 'Pay Now', 'Pay Now'),
(47, 'Product Name', 'Product Name'),
(48, 'Product Details', 'Product Details'),
(49, 'Categories', 'Categories'),
(50, 'Category:', 'Category:'),
(51, 'All Products Under', 'All Products Under'),
(52, 'Select Size', 'Select Size'),
(53, 'Select Color', 'Select Color'),
(54, 'Product Price', 'Product Price'),
(55, 'Quantity', 'Quantity'),
(56, 'Out of Stock', 'Out of Stock'),
(57, 'Share This', 'Share This'),
(58, 'Share This Product', 'Share This Product'),
(59, 'Product Description', 'Product Description'),
(60, 'Features', 'Features'),
(61, 'Conditions', 'Conditions'),
(62, 'Return Policy', 'Return Policy'),
(63, 'Reviews', 'Reviews'),
(64, 'Review', 'Review'),
(65, 'Give a Review', 'Give a Review'),
(66, 'Write your comment (Optional)', 'Write your comment (Optional)'),
(67, 'Submit Review', 'Submit Review'),
(68, 'You already have given a rating!', 'You already have given a rating!'),
(69, 'You must have to login to give a review', 'You must have to login to give a review'),
(70, 'No description found', 'No description found'),
(71, 'No feature found', 'No feature found'),
(72, 'No condition found', 'No condition found'),
(73, 'No return policy found', 'No return policy found'),
(74, 'Review not found', 'Review not found'),
(75, 'Customer Name', 'Customer Name'),
(76, 'Comment', 'Comment'),
(77, 'Comments', 'Comments'),
(78, 'Rating', 'Rating'),
(79, 'Previous', 'Previous'),
(80, 'Next', 'Next'),
(81, 'Sub Total', 'Sub Total'),
(82, 'Total', 'Total'),
(83, 'Action', 'Action'),
(84, 'Shipping Cost', 'Shipping Cost'),
(85, 'Continue Shopping', 'Continue Shopping'),
(86, 'Update Billing Address', 'Update Billing Address'),
(87, 'Update Shipping Address', 'Update Shipping Address'),
(88, 'Update Billing and Shipping Info', 'Update Billing and Shipping Info'),
(89, 'Dashboard', 'Dashboard'),
(90, 'Welcome to the Dashboard', 'Welcome to the Dashboard'),
(91, 'Back to Dashboard', 'Back to Dashboard'),
(92, 'Subscribe', 'Subscribe'),
(93, 'Subscribe To Our Newsletter', 'Subscribe To Our Newsletter'),
(94, 'Email Address', 'Email Address'),
(95, 'Enter Your Email Address', 'Enter Your Email Address'),
(96, 'Password', 'Password'),
(97, 'Forget Password', 'Forget Password'),
(98, 'Retype Password', 'Retype Password'),
(99, 'Update Password', 'Update Password'),
(100, 'New Password', 'New Password'),
(101, 'Retype New Password', 'Retype New Password'),
(102, 'Full Name', 'Full Name'),
(103, 'Company Name', 'Company Name'),
(104, 'Phone Number', 'Phone Number'),
(105, 'Address', 'Address'),
(106, 'Country', 'Country'),
(107, 'City', 'City'),
(108, 'State', 'State'),
(109, 'Zip Code', 'Zip Code'),
(110, 'About Us', 'About Us'),
(111, 'Featured Posts', 'Featured Posts'),
(112, 'Popular Posts', 'Popular Posts'),
(113, 'Recent Posts', 'Recent Posts'),
(114, 'Contact Information', 'Contact Information'),
(115, 'Contact Form', 'Contact Form'),
(116, 'Our Office', 'Our Office'),
(117, 'Update Profile', 'Update Profile'),
(118, 'Send Message', 'Send Message'),
(119, 'Message', 'Message'),
(120, 'Find Us On Map', 'Find Us On Map'),
(121, 'Congratulation! Payment is successful.', 'Congratulation! Payment is successful.'),
(122, 'Billing and Shipping Information is updated successfully.', 'Billing and Shipping Information is updated successfully.'),
(123, 'Customer Name can not be empty.', 'Customer Name can not be empty.'),
(124, 'Phone Number can not be empty.', 'Phone Number can not be empty.'),
(125, 'Address can not be empty.', 'Address can not be empty.'),
(126, 'You must have to select a country.', 'You must have to select a country.'),
(127, 'City can not be empty.', 'City can not be empty.'),
(128, 'State can not be empty.', 'State can not be empty.'),
(129, 'Zip Code can not be empty.', 'Zip Code can not be empty.'),
(130, 'Profile Information is updated successfully.', 'Profile Information is updated successfully.'),
(131, 'Email Address can not be empty', 'Email Address can not be empty'),
(132, 'Email and/or Password can not be empty.', 'Email and/or Password can not be empty.'),
(133, 'Email Address does not match.', 'Email Address does not match.'),
(134, 'Email address must be valid.', 'Email address must be valid.'),
(135, 'You email address is not found in our system.', 'You email address is not found in our system.'),
(136, 'Please check your email and confirm your subscription.', 'Please check your email and confirm your subscription.'),
(137, 'Your email is verified successfully. You can now login to our website.', 'Your email is verified successfully. You can now login to our website.'),
(138, 'Password can not be empty.', 'Password can not be empty.'),
(139, 'Passwords do not match.', 'Passwords do not match.'),
(140, 'Please enter new and retype passwords.', 'Please enter new and retype passwords.'),
(141, 'Password is updated successfully.', 'Password is updated successfully.'),
(142, 'To reset your password, please click on the link below.', 'To reset your password, please click on the link below.'),
(143, 'PASSWORD RESET REQUEST - YOUR WEBSITE.COM', 'PASSWORD RESET REQUEST - YOUR WEBSITE.COM'),
(144, 'The password reset email time (24 hours) has expired. Please again try to reset your password.', 'The password reset email time (24 hours) has expired. Please again try to reset your password.'),
(145, 'A confirmation link is sent to your email address. You will get the password reset information in there.', 'A confirmation link is sent to your email address. You will get the password reset information in there.'),
(146, 'Password is reset successfully. You can now login.', 'Password is reset successfully. You can now login.'),
(147, 'Email Address Already Exists', 'Email Address Already Exists.'),
(148, 'Sorry! Your account is inactive. Please contact to the administrator.', 'Sorry! Your account is inactive. Please contact to the administrator.'),
(149, 'Change Password', 'Change Password'),
(150, 'Registration Email Confirmation for YOUR WEBSITE', 'Registration Email Confirmation for YOUR WEBSITE.'),
(151, 'Thank you for your registration! Your account has been created. To active your account click on the link below:', 'Thank you for your registration! Your account has been created. To active your account click on the link below:'),
(152, 'Your registration is completed. Please check your email address to follow the process to confirm your registration.', 'Your registration is completed. Please check your email address to follow the process to confirm your registration.'),
(153, 'No Product Found', 'No Product Found'),
(154, 'Add to Cart', 'Add to Cart'),
(155, 'Related Products', 'Related Products'),
(156, 'See all related products from below', 'See all the related products from below'),
(157, 'Size', 'Size'),
(158, 'Color', 'Color'),
(159, 'Price', 'Price'),
(160, 'Please login as customer to checkout', 'Please login as customer to checkout'),
(161, 'Billing Address', 'Billing Address'),
(162, 'Shipping Address', 'Shipping Address'),
(163, 'Rating is Submitted Successfully!', 'Rating is Submitted Successfully!');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_mid_category`
--

DROP TABLE IF EXISTS `tbl_mid_category`;
CREATE TABLE IF NOT EXISTS `tbl_mid_category` (
  `mcat_id` int NOT NULL AUTO_INCREMENT,
  `mcat_name` varchar(255) NOT NULL,
  `tcat_id` int NOT NULL,
  PRIMARY KEY (`mcat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_mid_category`
--

INSERT INTO `tbl_mid_category` (`mcat_id`, `mcat_name`, `tcat_id`) VALUES
(18, 'Diwali Special', 6),
(19, 'Silicone molds', 6),
(20, 'Glass Jar', 6),
(21, 'Beginner Kit', 6),
(22, 'Essential Tools', 6),
(23, 'Fragrance', 6),
(24, 'Wax Melter', 6),
(25, 'Silicone molds', 8),
(26, 'Deep Casting Molds', 7),
(27, 'Jewelry Molds', 7),
(28, 'Color', 7),
(29, 'Resin', 7),
(30, 'Essential Tools', 7),
(31, 'Resin Beginner Tools', 7),
(32, 'Candle Box', 8),
(33, 'Sticker', 8),
(34, 'Ribbon', 8),
(35, 'Wrapping Paper', 8),
(36, 'Fragile Sticker', 8);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order`
--

DROP TABLE IF EXISTS `tbl_order`;
CREATE TABLE IF NOT EXISTS `tbl_order` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(100) NOT NULL,
  `color` varchar(100) NOT NULL,
  `quantity` varchar(50) NOT NULL,
  `unit_price` varchar(50) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `line_total` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_order`
--

INSERT INTO `tbl_order` (`id`, `product_id`, `product_name`, `size`, `color`, `quantity`, `unit_price`, `payment_id`, `line_total`) VALUES
(34, 108, 'Daisy Floral Mold', '', '', '2', '300', '1', 0.00),
(131, 164, 'Panda Mould ', '', '', '1', '360', 'ORD-20260721145221', 360.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_page`
--

DROP TABLE IF EXISTS `tbl_page`;
CREATE TABLE IF NOT EXISTS `tbl_page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `about_title` varchar(255) NOT NULL,
  `about_content` text NOT NULL,
  `about_banner` varchar(255) NOT NULL,
  `about_meta_title` varchar(255) NOT NULL,
  `about_meta_keyword` text NOT NULL,
  `about_meta_description` text NOT NULL,
  `faq_title` varchar(255) NOT NULL,
  `faq_banner` varchar(255) NOT NULL,
  `faq_meta_title` varchar(255) NOT NULL,
  `faq_meta_keyword` text NOT NULL,
  `faq_meta_description` text NOT NULL,
  `blog_title` varchar(255) NOT NULL,
  `blog_banner` varchar(255) NOT NULL,
  `blog_meta_title` varchar(255) NOT NULL,
  `blog_meta_keyword` text NOT NULL,
  `blog_meta_description` text NOT NULL,
  `contact_title` varchar(255) NOT NULL,
  `contact_banner` varchar(255) NOT NULL,
  `contact_meta_title` varchar(255) NOT NULL,
  `contact_meta_keyword` text NOT NULL,
  `contact_meta_description` text NOT NULL,
  `pgallery_title` varchar(255) NOT NULL,
  `pgallery_banner` varchar(255) NOT NULL,
  `pgallery_meta_title` varchar(255) NOT NULL,
  `pgallery_meta_keyword` text NOT NULL,
  `pgallery_meta_description` text NOT NULL,
  `vgallery_title` varchar(255) NOT NULL,
  `vgallery_banner` varchar(255) NOT NULL,
  `vgallery_meta_title` varchar(255) NOT NULL,
  `vgallery_meta_keyword` text NOT NULL,
  `vgallery_meta_description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_page`
--

INSERT INTO `tbl_page` (`id`, `about_title`, `about_content`, `about_banner`, `about_meta_title`, `about_meta_keyword`, `about_meta_description`, `faq_title`, `faq_banner`, `faq_meta_title`, `faq_meta_keyword`, `faq_meta_description`, `blog_title`, `blog_banner`, `blog_meta_title`, `blog_meta_keyword`, `blog_meta_description`, `contact_title`, `contact_banner`, `contact_meta_title`, `contact_meta_keyword`, `contact_meta_description`, `pgallery_title`, `pgallery_banner`, `pgallery_meta_title`, `pgallery_meta_keyword`, `pgallery_meta_description`, `vgallery_title`, `vgallery_banner`, `vgallery_meta_title`, `vgallery_meta_keyword`, `vgallery_meta_description`) VALUES
(1, 'About Techgatha School', '<p><strong>Welcome to Techgatha School.</strong></p>\r\n<p>We are committed to nurturing every student with quality education, strong values, and holistic development.</p>\r\n<p>Our school focuses on academic excellence, discipline, and a caring learning environment where students grow with confidence.</p>\r\n<p>From classroom learning to co-curricular activities, Techgatha School prepares students for a bright future.</p>', 'about-banner-1784887013.jpg', 'About Techgatha School', 'Techgatha School, about school, education Nepal', 'Learn about Techgatha School — quality education, qualified teachers, and a caring campus community.', 'FAQ', 'faq-banner.jpg', 'FAQ | 8848 Cleaning Service', 'cleaning FAQ, booking questions, 8848 cleaning service help', 'Frequently asked questions about booking, pricing, and cleaning services with 8848 Cleaning Service.', 'News & Events', 'blog-banner.jpg', 'News & Events | Techgatha School', '', 'School news, notices, and event updates from Techgatha School.', 'Contact Us', 'contact-banner-1784788646.jpg', 'Contact Techgatha School', 'contact 8848 cleaning, cleaning service phone Kathmandu, book cleaning Nepal', 'Contact Techgatha School for admission and general information.', 'School Gallery', 'pgallery-banner.jpg', 'Gallery | Techgatha School', '', 'Photos from classrooms, activities, and school life at Techgatha School.', 'Video Gallery', 'vgallery-banner.jpg', 'Ecommerce - Video Gallery', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_payment`
--

DROP TABLE IF EXISTS `tbl_payment`;
CREATE TABLE IF NOT EXISTS `tbl_payment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `payment_date` varchar(50) NOT NULL,
  `txnid` varchar(255) NOT NULL,
  `paid_amount` int NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `card_cvv` varchar(10) NOT NULL,
  `card_month` varchar(10) NOT NULL,
  `card_year` varchar(10) NOT NULL,
  `bank_transaction_info` text NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `payment_QR` varchar(255) DEFAULT NULL,
  `payment_status` varchar(25) NOT NULL,
  `shipping_status` varchar(20) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `discount_type` varchar(20) DEFAULT 'percent',
  `discount_value` decimal(10,2) DEFAULT '0.00',
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `vat_percent` decimal(10,2) DEFAULT '0.00',
  `vat_amount` decimal(10,2) DEFAULT '0.00',
  `grand_total` decimal(10,2) DEFAULT '0.00',
  `due_amount` decimal(10,2) DEFAULT '0.00',
  `notes` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `service_address` text,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` varchar(30) DEFAULT NULL,
  `booking_status` varchar(25) DEFAULT 'Pending',
  `assignment_status` varchar(25) DEFAULT 'Unassigned',
  `service_lat` decimal(10,7) DEFAULT NULL,
  `service_lng` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_payment`
--

INSERT INTO `tbl_payment` (`id`, `customer_id`, `customer_name`, `customer_email`, `payment_date`, `txnid`, `paid_amount`, `card_number`, `card_cvv`, `card_month`, `card_year`, `bank_transaction_info`, `payment_method`, `payment_QR`, `payment_status`, `shipping_status`, `payment_id`, `subtotal`, `discount_type`, `discount_value`, `discount_amount`, `vat_percent`, `vat_amount`, `grand_total`, `due_amount`, `notes`, `created_at`, `updated_at`, `customer_phone`, `service_address`, `preferred_date`, `preferred_time`, `booking_status`, `assignment_status`, `service_lat`, `service_lng`) VALUES
(96, 34, 'uday kumar pandit', 'sastikatrading@gmail.com', '2026-07-21 14:52:21', '', 0, '', '', '', '', '', 'enquiry', NULL, 'Pending', 'Pending', 'ORD-20260721145221', 360.00, 'percent', 0.00, 0.00, 0.00, 0.00, 360.00, 360.00, 'Area: gggh, ghghgh, ghghgh\nPanda Mould : cfc', '2026-07-21 14:52:21', '2026-07-21 14:52:21', '9801076273', 'Manamaiju, Bhagwan Bahal Marg, Thamel, Kathmandu-26, Kathmandu Metropolitan City, Kathmandu, Bagamati Province, 25511, Nepal', '2026-07-30', '15:33', 'Pending', 'Unassigned', 27.7162368, 85.3127519);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_photo`
--

DROP TABLE IF EXISTS `tbl_photo`;
CREATE TABLE IF NOT EXISTS `tbl_photo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caption` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_photo`
--

INSERT INTO `tbl_photo` (`id`, `caption`, `photo`) VALUES
(1, 'Photo 1', 'photo-1.jpg'),
(2, 'Photo 2', 'photo-2.jpg'),
(3, 'Photo 3', 'photo-3.jpg'),
(4, 'Photo 4', 'photo-4.jpg'),
(5, 'Photo 5', 'photo-5.jpg'),
(6, 'Photo 6', 'photo-6.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_post`
--

DROP TABLE IF EXISTS `tbl_post`;
CREATE TABLE IF NOT EXISTS `tbl_post` (
  `post_id` int NOT NULL AUTO_INCREMENT,
  `post_title` varchar(255) NOT NULL,
  `post_slug` varchar(255) NOT NULL,
  `post_content` text NOT NULL,
  `post_date` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `category_id` int NOT NULL,
  `total_view` int NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_keyword` text NOT NULL,
  `meta_description` text NOT NULL,
  PRIMARY KEY (`post_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_post`
--

INSERT INTO `tbl_post` (`post_id`, `post_title`, `post_slug`, `post_content`, `post_date`, `photo`, `category_id`, `total_view`, `meta_title`, `meta_keyword`, `meta_description`) VALUES
(9, '7th LASS Sports Meet', 'nostrum-copiosae-argumentum-has', '<p>Lorem ipsum dolor sit amet, qui case probo velit no, an postea scaevola partiendo mei. Id mea fuisset perpetua referrentur. Ut everti ceteros mei, alii discere eum no, duo id malis iuvaret. Ad sint everti accusam vel, ea viderer suscipiantur pri. Brute option minimum in cum, ignota iuvaret an pro.</p>\r\n\r\n<p>Solum atqui intellegebat mea an. Ne ius alterum aliquam. Ea nec populo aliquid mentitum, vis in meliore atomorum, sanctus consequat vituperatoribus duo ea. Ad doctus pertinacia ius, virtute fuisset id has, eum ut modo principes. Qui eu labore adversarium, oporteat delicata qui ut, an qui meliore principes. Id aliquid dolorum nam.</p>\r\n\r\n<p>Reque pericula philosophia ut mei, volumus eligendi mandamus has an. In nobis consulatu pri, has at timeam scaevola, has simul quaeque et. Te nec sale accumsan. Dolorem prodesset efficiendi sea ea.</p>\r\n\r\n<p>Et habeo modus debitis pri, vel quis fierent albucius ne. Ea animal meliore usu, nec etiam dolorum atomorum at, nam in audire mandamus omittantur. Cu ius dicam officiis molestiae, mea volumus officiis cotidieque no. Ut vel possim interpretaris, idque probatus antiopam has ad. Facilisi qualisque te sea, no dolorum mnesarchum usu.</p>\r\n\r\n<p>Eum tota graeci impetus an, eirmod invenire rationibus ne mel. Ignota habemus eum ex, vis omnesque delicata perpetua an. Sit id modo invidunt sapientem, ne eum vocibus dolores phaedrum. Case praesent appellantur eu per.</p>', '05-09-2017', 'blog-9-1784887467-5975d0.jpeg', 0, 12, 'Nostrum copiosae argumentum has', '', ''),
(10, 'Movingup Ceremony', 'an-labores-explicari-qui-eu', '<p><span style=\"color: rgb(68, 68, 68); font-family: Helvetica; font-size: 16px; background-color: rgb(248, 248, 248);\">Moving Up Ceremony for Our Grade 10 Students</span><br style=\"color: rgb(68, 68, 68); font-family: Helvetica; font-size: 16px; background-color: rgb(248, 248, 248);\"><span style=\"color: rgb(68, 68, 68); font-family: Helvetica; font-size: 16px; background-color: rgb(248, 248, 248);\">As our Grade 10 students take a significant step forward in their academic journey, we proudly celebrate their achievements at the Moving Up Ceremony. This milestone reflects their dedication, perseverance, and hard work, marking the end of one chapter and the beginning of exciting new opportunities and challenges ahead. We are incredibly proud of each student and look forward to seeing them grow, excel, and achieve even greater success in the future. Congratulations and best wishes for a bright journey ahead!</span></p>', '05-09-2017', 'blog-10-1784887428-fd32a1.jpeg', 0, 4, 'An labores explicari qui eu', '', ''),
(11, 'Investiture Ceremony', 'Investiture Ceremony', '<div style=\"color: rgb(68, 68, 68); font-family: Helvetica; font-size: 15px; background-color: rgb(248, 248, 248);\">The Investiture Ceremony is a proud and defining moment where our students are entrusted with the mantle of leadership. Through these coveted roles, they begin a journey of growth, sharpening their decision-making, communication, teamwork, and leadership skills.</div><div style=\"color: rgb(68, 68, 68); font-family: Helvetica; font-size: 15px; background-color: rgb(248, 248, 248);\">Congratulations to all our newly appointed school leaders! May your tenure be impactful and inspiring.</div>', '05-09-2017', 'blog-11-1784887343-3e96e7.jpg', 0, 18, 'Lorem ipsum dolor sit amet', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

DROP TABLE IF EXISTS `tbl_product`;
CREATE TABLE IF NOT EXISTS `tbl_product` (
  `p_id` int NOT NULL AUTO_INCREMENT,
  `p_name` varchar(255) NOT NULL,
  `p_old_price` varchar(10) NOT NULL,
  `p_current_price` varchar(10) NOT NULL,
  `p_qty` int NOT NULL,
  `p_featured_photo` varchar(255) NOT NULL,
  `p_description` text NOT NULL,
  `p_short_description` text NOT NULL,
  `p_feature` text NOT NULL,
  `p_condition` text NOT NULL,
  `p_return_policy` text NOT NULL,
  `p_total_view` int NOT NULL,
  `p_is_featured` int NOT NULL,
  `p_is_active` int NOT NULL,
  `ecat_id` int NOT NULL,
  `staff_commission_type` varchar(20) NOT NULL DEFAULT 'inherit',
  `staff_commission_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`p_id`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`p_id`, `p_name`, `p_old_price`, `p_current_price`, `p_qty`, `p_featured_photo`, `p_description`, `p_short_description`, `p_feature`, `p_condition`, `p_return_policy`, `p_total_view`, `p_is_featured`, `p_is_active`, `ecat_id`, `staff_commission_type`, `staff_commission_value`) VALUES
(172, 'Computer Labs', '0', '0', 1, 'facility-172-1785473152-12b653.jpg', '', '', '', '', '', 0, 1, 1, 80, 'inherit', 0.00),
(173, 'Science Lab', '0', '0', 1, 'facility-173-1785473143-72ee28.jpeg', '', '', '', '', '', 1, 1, 1, 80, 'inherit', 0.00),
(174, 'Auditorium', '0', '0', 1, 'facility-174-1784887643-063cd4.jpg', '', '', '', '', '', 7, 1, 1, 80, 'inherit', 0.00),
(175, 'Library', '0', '0', 1, 'facility-175-1784887609-e7b969.png', '', '', '', '', '', 5, 1, 1, 80, 'inherit', 0.00),
(176, 'sports', '0', '0', 1, 'facility-176-1784887580-628c4c.png', '', '', '', '', '', 2, 1, 1, 80, 'inherit', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_color`
--

DROP TABLE IF EXISTS `tbl_product_color`;
CREATE TABLE IF NOT EXISTS `tbl_product_color` (
  `id` int NOT NULL AUTO_INCREMENT,
  `color_id` int NOT NULL,
  `p_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=286 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_product_color`
--

INSERT INTO `tbl_product_color` (`id`, `color_id`, `p_id`) VALUES
(69, 1, 4),
(70, 4, 4),
(77, 6, 6),
(82, 2, 12),
(83, 9, 13),
(84, 3, 14),
(85, 2, 15),
(86, 6, 15),
(87, 3, 16),
(88, 3, 17),
(89, 2, 18),
(90, 3, 19),
(91, 1, 20),
(92, 8, 21),
(93, 2, 22),
(94, 2, 23),
(95, 2, 25),
(96, 5, 26),
(97, 2, 27),
(98, 4, 27),
(99, 5, 28),
(100, 7, 29),
(101, 10, 30),
(102, 11, 31),
(103, 14, 32),
(105, 2, 34),
(106, 1, 35),
(107, 3, 36),
(109, 6, 38),
(110, 2, 39),
(111, 11, 42),
(149, 3, 10),
(150, 6, 9),
(151, 3, 8),
(152, 7, 7),
(159, 2, 77),
(163, 17, 79),
(164, 2, 78),
(167, 3, 80),
(168, 2, 81),
(172, 1, 82),
(173, 2, 82),
(174, 4, 82),
(281, 2, 118),
(282, 20, 118),
(283, 30, 118),
(284, 31, 118),
(285, 2, 170);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_photo`
--

DROP TABLE IF EXISTS `tbl_product_photo`;
CREATE TABLE IF NOT EXISTS `tbl_product_photo` (
  `pp_id` int NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) NOT NULL,
  `p_id` int NOT NULL,
  PRIMARY KEY (`pp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=208 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_product_photo`
--

INSERT INTO `tbl_product_photo` (`pp_id`, `photo`, `p_id`) VALUES
(142, '142.jpg', 108),
(143, '143.jpg', 108),
(144, '144.jpg', 109),
(145, '145.jpg', 110),
(146, '146.jpg', 111),
(147, '147.jpg', 111),
(148, '148.jpg', 112),
(149, '149.jpg', 112),
(150, '150.jpg', 112),
(151, '151.jpeg', 113),
(152, '152.jpeg', 114),
(153, '153.jpeg', 114),
(155, '155.jpeg', 116),
(156, '156.jpeg', 117),
(157, '157.jpeg', 118),
(158, '158.jpeg', 118),
(159, '159.jpeg', 118),
(160, '160.jpeg', 118),
(161, '161.jpeg', 118),
(162, '162.jpeg', 120),
(163, '163.jpeg', 121),
(164, '164.jpeg', 123),
(165, '165.jpeg', 126),
(166, '166.jpeg', 128),
(167, '167.jpeg', 129),
(170, '170.jpeg', 132),
(171, '171.jpeg', 133),
(172, '172.jpeg', 134),
(173, '173.jpeg', 134),
(174, '174.jpeg', 134),
(175, '175.jpeg', 138),
(176, '176.jpeg', 139),
(177, '177.jpeg', 140),
(178, '178.jpeg', 142),
(179, '179.jpeg', 143),
(180, '180.jpeg', 143),
(181, '181.jpeg', 144),
(182, '182.jpeg', 145),
(183, '183.jpeg', 146),
(184, '184.jpeg', 147),
(185, '185.jpeg', 149),
(186, '186.jpeg', 150),
(187, '187.jpeg', 151),
(188, '188.jpeg', 153),
(189, '189.jpeg', 154),
(190, '190.jpeg', 155),
(191, '191.jpeg', 157),
(192, '192.jpeg', 157),
(193, '193.jpeg', 158),
(194, '194.jpeg', 158),
(195, '195.jpeg', 160),
(196, '196.jpeg', 161),
(197, '197.jpeg', 162),
(198, '198.jpeg', 162),
(199, '199.jpeg', 166),
(200, '200.jpg', 164),
(201, '201.png', 164),
(202, '202.jpg', 164),
(203, '1783397820_2f4a526a.jpg', 171),
(204, '1783397820_0424504c.jpg', 171),
(205, '1783397820_56b9d6aa.jpeg', 171),
(206, '206.jpg', 171),
(207, '207.jpg', 171);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product_size`
--

DROP TABLE IF EXISTS `tbl_product_size`;
CREATE TABLE IF NOT EXISTS `tbl_product_size` (
  `id` int NOT NULL AUTO_INCREMENT,
  `size_id` int NOT NULL,
  `p_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=467 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_product_size`
--

INSERT INTO `tbl_product_size` (`id`, `size_id`, `p_id`) VALUES
(44, 1, 6),
(56, 8, 12),
(57, 9, 12),
(58, 10, 12),
(59, 11, 12),
(60, 12, 12),
(61, 13, 12),
(62, 9, 13),
(63, 11, 13),
(64, 13, 13),
(65, 15, 13),
(66, 9, 14),
(67, 11, 14),
(68, 12, 14),
(69, 13, 14),
(70, 9, 15),
(71, 11, 15),
(72, 13, 15),
(73, 15, 16),
(74, 16, 16),
(75, 17, 16),
(76, 16, 17),
(77, 17, 17),
(78, 14, 18),
(79, 15, 18),
(80, 16, 18),
(81, 17, 18),
(82, 15, 19),
(83, 16, 19),
(84, 17, 19),
(85, 14, 20),
(86, 15, 20),
(87, 17, 20),
(88, 15, 21),
(89, 17, 21),
(90, 15, 22),
(91, 16, 22),
(92, 17, 22),
(93, 15, 23),
(94, 16, 23),
(95, 17, 23),
(96, 18, 25),
(97, 19, 25),
(98, 20, 25),
(99, 21, 25),
(100, 19, 26),
(101, 21, 26),
(102, 22, 26),
(103, 23, 26),
(104, 19, 27),
(105, 20, 27),
(106, 21, 27),
(107, 22, 27),
(108, 19, 28),
(109, 20, 28),
(110, 21, 28),
(111, 19, 29),
(112, 20, 29),
(113, 22, 29),
(114, 1, 30),
(115, 2, 30),
(116, 3, 30),
(117, 4, 30),
(118, 23, 31),
(119, 26, 32),
(123, 2, 34),
(124, 2, 35),
(125, 2, 36),
(126, 3, 36),
(129, 2, 38),
(130, 3, 38),
(131, 4, 38),
(132, 5, 38),
(133, 27, 39),
(134, 8, 42),
(210, 3, 10),
(211, 4, 10),
(212, 5, 10),
(213, 6, 10),
(214, 3, 9),
(215, 4, 9),
(216, 3, 8),
(217, 4, 8),
(218, 2, 7),
(219, 3, 7),
(220, 4, 7),
(249, 1, 79),
(250, 2, 79),
(251, 3, 79),
(252, 1, 78),
(253, 2, 78),
(254, 3, 78),
(255, 4, 78),
(256, 5, 78),
(259, 26, 80),
(262, 3, 82),
(263, 4, 82),
(463, 48, 119),
(464, 8, 124),
(465, 9, 125),
(466, 8, 170);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rating`
--

DROP TABLE IF EXISTS `tbl_rating`;
CREATE TABLE IF NOT EXISTS `tbl_rating` (
  `rt_id` int NOT NULL AUTO_INCREMENT,
  `p_id` int NOT NULL,
  `cust_id` int NOT NULL,
  `comment` text NOT NULL,
  `rating` int NOT NULL,
  PRIMARY KEY (`rt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_school_message`
--

DROP TABLE IF EXISTS `tbl_school_message`;
CREATE TABLE IF NOT EXISTS `tbl_school_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role` varchar(40) NOT NULL,
  `person_name` varchar(150) NOT NULL DEFAULT '',
  `designation` varchar(150) NOT NULL DEFAULT '',
  `photo` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_unique` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_school_message`
--

INSERT INTO `tbl_school_message` (`id`, `role`, `person_name`, `designation`, `photo`, `message`, `status`, `sort_order`, `updated_at`) VALUES
(1, 'principal', 'Mr.Rajesh Rayamajhi', 'Principal', 'leadership-1-1784889976.png', '<p>Dear Students, Parents, Teachers, and Well-Wishers,</p><p>Welcome to New Era Academy, where learning goes beyond the classroom and every student is encouraged to discover their true potential. Our mission is to provide quality education in a safe, caring, and inspiring environment that nurtures academic excellence, strong character, creativity, and lifelong learning.</p><p>At New Era Academy, we believe that education is a partnership between the school, parents, and the community. Through dedicated teachers, modern teaching methodologies, and a student-centered approach, we strive to prepare our learners to become responsible, confident, and compassionate global citizens.</p><p>I extend my heartfelt gratitude to our parents for their continued trust and support, to our teachers for their unwavering dedication, and to our students for inspiring us every day with their enthusiasm and curiosity.</p><p>Together, let us continue building a brighter future through knowledge, innovation, and excellence.</p><p>I warmly welcome you to the New Era Academy family and look forward to achieving many more milestones together.</p><p><strong>Best Regards,</strong></p><p><strong>Rajesh Rayamajhi</strong><br><strong>Principal</strong><br></p>', 'Active', 1, '2026-07-24 16:31:16'),
(2, 'chairman', '', 'Chairman', '', '<p>Welcome. Please update this message from the admin panel.</p>', 'Active', 2, '2026-07-23 10:18:39'),
(3, 'vice_principal', '', 'Vice Principal', '', '<p>Welcome. Please update this message from the admin panel.</p>', 'Active', 3, '2026-07-23 10:18:39');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_service`
--

DROP TABLE IF EXISTS `tbl_service`;
CREATE TABLE IF NOT EXISTS `tbl_service` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `photo` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_service`
--

INSERT INTO `tbl_service` (`id`, `title`, `content`, `photo`) VALUES
(5, 'Easy Returns', 'Return any item before 15 days!', 'service-5.png'),
(7, 'Fast Shipping', 'Items are shipped within 24 hours.', 'service-7.png'),
(9, 'Secure Checkout', 'Providing Secure Checkout Options for all', 'service-9.png'),
(11, '100% satisfaction ', 'customer\'s satisfaction ', 'service-11.png');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_settings`
--

DROP TABLE IF EXISTS `tbl_settings`;
CREATE TABLE IF NOT EXISTS `tbl_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) NOT NULL,
  `site_name` varchar(255) NOT NULL DEFAULT '8848 Cleaning Service',
  `favicon` varchar(255) NOT NULL,
  `footer_about` text NOT NULL,
  `footer_copyright` text NOT NULL,
  `contact_address` text NOT NULL,
  `contact_email` varchar(50) NOT NULL,
  `contact_phone` varchar(50) NOT NULL,
  `contact_fax` varchar(50) NOT NULL,
  `contact_map_iframe` text NOT NULL,
  `receive_email` varchar(50) NOT NULL,
  `receive_email_subject` varchar(50) NOT NULL,
  `receive_email_thank_you_message` text NOT NULL,
  `forget_password_message` text NOT NULL,
  `total_recent_post_footer` int NOT NULL,
  `total_popular_post_footer` int NOT NULL,
  `total_recent_post_sidebar` int NOT NULL,
  `total_popular_post_sidebar` int NOT NULL,
  `total_featured_product_home` int NOT NULL,
  `total_latest_product_home` int NOT NULL,
  `total_popular_product_home` int NOT NULL,
  `meta_title_home` text NOT NULL,
  `meta_keyword_home` text NOT NULL,
  `meta_description_home` text NOT NULL,
  `banner_login` varchar(100) NOT NULL,
  `banner_registration` varchar(100) NOT NULL,
  `banner_forget_password` varchar(255) NOT NULL,
  `banner_reset_password` varchar(255) NOT NULL,
  `banner_search` text NOT NULL,
  `banner_cart` text NOT NULL,
  `banner_checkout` text NOT NULL,
  `banner_product_category` text NOT NULL,
  `banner_blog` text NOT NULL,
  `cta_title` text NOT NULL,
  `cta_content` text NOT NULL,
  `cta_read_more_text` text NOT NULL,
  `cta_read_more_url` text NOT NULL,
  `cta_photo` varchar(255) NOT NULL,
  `featured_product_title` text NOT NULL,
  `featured_product_subtitle` text NOT NULL,
  `latest_product_title` text NOT NULL,
  `latest_product_subtitle` text NOT NULL,
  `popular_product_title` text NOT NULL,
  `popular_product_subtitle` text NOT NULL,
  `testimonial_title` text NOT NULL,
  `testimonial_subtitle` text NOT NULL,
  `testimonial_photo` text NOT NULL,
  `blog_title` text NOT NULL,
  `blog_subtitle` text NOT NULL,
  `newsletter_text` text NOT NULL,
  `paypal_email` text NOT NULL,
  `stripe_public_key` varchar(255) NOT NULL,
  `stripe_secret_key` varchar(255) NOT NULL,
  `bank_detail` text NOT NULL,
  `before_head` text NOT NULL,
  `after_body` text NOT NULL,
  `before_body` text NOT NULL,
  `home_service_on_off` int NOT NULL,
  `home_welcome_on_off` int NOT NULL,
  `home_featured_product_on_off` int NOT NULL,
  `home_latest_product_on_off` int NOT NULL,
  `home_popular_product_on_off` int NOT NULL,
  `home_testimonial_on_off` int NOT NULL,
  `home_blog_on_off` int NOT NULL,
  `newsletter_on_off` int NOT NULL,
  `ads_above_welcome_on_off` int NOT NULL,
  `ads_above_featured_product_on_off` int NOT NULL,
  `ads_above_latest_product_on_off` int NOT NULL,
  `ads_above_popular_product_on_off` int NOT NULL,
  `ads_above_testimonial_on_off` int NOT NULL,
  `ads_category_sidebar_on_off` int NOT NULL,
  `default_staff_commission_type` varchar(20) DEFAULT 'percent',
  `default_staff_commission_value` decimal(10,2) DEFAULT '35.00',
  `marquee_on_off` tinyint NOT NULL DEFAULT '1',
  `marquee_notices` text,
  `invoice_vat_no` varchar(100) NOT NULL DEFAULT '',
  `invoice_due_days` int NOT NULL DEFAULT '30',
  `invoice_footer_note` text,
  `google_client_id` varchar(255) NOT NULL DEFAULT '',
  `google_client_secret` varchar(255) NOT NULL DEFAULT '',
  `recaptcha_site_key` varchar(255) NOT NULL DEFAULT '',
  `recaptcha_secret_key` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_settings`
--

INSERT INTO `tbl_settings` (`id`, `logo`, `site_name`, `favicon`, `footer_about`, `footer_copyright`, `contact_address`, `contact_email`, `contact_phone`, `contact_fax`, `contact_map_iframe`, `receive_email`, `receive_email_subject`, `receive_email_thank_you_message`, `forget_password_message`, `total_recent_post_footer`, `total_popular_post_footer`, `total_recent_post_sidebar`, `total_popular_post_sidebar`, `total_featured_product_home`, `total_latest_product_home`, `total_popular_product_home`, `meta_title_home`, `meta_keyword_home`, `meta_description_home`, `banner_login`, `banner_registration`, `banner_forget_password`, `banner_reset_password`, `banner_search`, `banner_cart`, `banner_checkout`, `banner_product_category`, `banner_blog`, `cta_title`, `cta_content`, `cta_read_more_text`, `cta_read_more_url`, `cta_photo`, `featured_product_title`, `featured_product_subtitle`, `latest_product_title`, `latest_product_subtitle`, `popular_product_title`, `popular_product_subtitle`, `testimonial_title`, `testimonial_subtitle`, `testimonial_photo`, `blog_title`, `blog_subtitle`, `newsletter_text`, `paypal_email`, `stripe_public_key`, `stripe_secret_key`, `bank_detail`, `before_head`, `after_body`, `before_body`, `home_service_on_off`, `home_welcome_on_off`, `home_featured_product_on_off`, `home_latest_product_on_off`, `home_popular_product_on_off`, `home_testimonial_on_off`, `home_blog_on_off`, `newsletter_on_off`, `ads_above_welcome_on_off`, `ads_above_featured_product_on_off`, `ads_above_latest_product_on_off`, `ads_above_popular_product_on_off`, `ads_above_testimonial_on_off`, `ads_category_sidebar_on_off`, `default_staff_commission_type`, `default_staff_commission_value`, `marquee_on_off`, `marquee_notices`, `invoice_vat_no`, `invoice_due_days`, `invoice_footer_note`, `google_client_id`, `google_client_secret`, `recaptcha_site_key`, `recaptcha_secret_key`) VALUES
(1, 'logo.png', 'Techgatha School', 'favicon.png', 'Techgatha School is committed to quality education, strong values, and holistic student development.', '© {YEAR} Techgatha School. All rights reserved.', 'Manamaiju,kathmandu ', 'contact@sastikatrading.com.np', '+977-9869224134', '', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3531.197527116017!2d85.31022257581755!3d27.742052876162564!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb19005931a1db%3A0x582712fc68092e57!2sRaise%E2%80%99N%20Studio!5e0!3m2!1sen!2snp!4v1762004893741!5m2!1sen!2snp\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>', 'support@ecommercephp.com', 'Visitor Email Message from Ecommerce Site PHP', 'Thank you for sending email. We will contact you shortly.', 'A confirmation link is sent to your email address. You will get the password reset information in there.', 4, 4, 5, 5, 5, 6, 8, 'Techgatha School | Quality Education in Nepal', 'Techgatha School, school Nepal, admission, teachers, gallery, news, events', 'Techgatha School — quality education, qualified teachers, admissions, news and school events.', 'banner_login.jpg', 'banner_registration.jpg', 'banner_forget_password.jpg', 'banner_reset_password.jpg', 'banner_search.jpg', 'banner_cart.jpg', 'banner_checkout.jpg', 'banner_product_category.jpg', 'banner_blog.jpg', 'Ready to join Techgatha School?', 'Apply for admission online. Our office will guide you through the next steps.', 'Apply for admission', 'admission.php', 'cta.jpg', 'School Highlights', 'Explore life at Techgatha School', 'Latest Updates', 'News from our campus', 'Most Visited', 'Popular school pages', 'What parents say', 'Feedback from our school community', 'testimonial.jpg', 'News & Events', 'School notices, activities, and updates', 'Get school notices, events, and admission updates.', 'admin@ecom.com', '', '', '', '', '<div id=\"fb-root\"></div>\r\n<script>(function(d, s, id) {\r\n  var js, fjs = d.getElementsByTagName(s)[0];\r\n  if (d.getElementById(id)) return;\r\n  js = d.createElement(s); js.id = id;\r\n  js.src = \"//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=323620764400430\";\r\n  fjs.parentNode.insertBefore(js, fjs);\r\n}(document, \'script\', \'facebook-jssdk\'));</script>', '<!--Start of Tawk.to Script-->\r\n<script type=\"text/javascript\">\r\nvar Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();\r\n(function(){\r\nvar s1=document.createElement(\"script\"),s0=document.getElementsByTagName(\"script\")[0];\r\ns1.async=true;\r\ns1.src=\'https://embed.tawk.to/69089c9c39dbe71958a1931a/1j94q7hio\';\r\ns1.charset=\'UTF-8\';\r\ns1.setAttribute(\'crossorigin\',\'*\');\r\ns0.parentNode.insertBefore(s1,s0);\r\n})();\r\n</script>\r\n<!--End of Tawk.to Script-->', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 'percent', 50.00, 1, '', '1234567890', 30, 'Thank you for choosing our cleaning service. We appreciate your trust.', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_shipping_cost`
--

DROP TABLE IF EXISTS `tbl_shipping_cost`;
CREATE TABLE IF NOT EXISTS `tbl_shipping_cost` (
  `shipping_cost_id` int NOT NULL AUTO_INCREMENT,
  `country_id` int NOT NULL,
  `amount` varchar(20) NOT NULL,
  PRIMARY KEY (`shipping_cost_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_shipping_cost`
--

INSERT INTO `tbl_shipping_cost` (`shipping_cost_id`, `country_id`, `amount`) VALUES
(1, 228, '11'),
(2, 167, '100'),
(3, 13, '8'),
(4, 230, '0'),
(5, 2, '100'),
(6, 154, '12345'),
(7, 44, '100'),
(8, 246, '120'),
(9, 247, '234'),
(10, 249, '120'),
(11, 250, '300');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_shipping_cost_all`
--

DROP TABLE IF EXISTS `tbl_shipping_cost_all`;
CREATE TABLE IF NOT EXISTS `tbl_shipping_cost_all` (
  `sca_id` int NOT NULL AUTO_INCREMENT,
  `amount` varchar(20) NOT NULL,
  PRIMARY KEY (`sca_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_shipping_cost_all`
--

INSERT INTO `tbl_shipping_cost_all` (`sca_id`, `amount`) VALUES
(1, '300');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_size`
--

DROP TABLE IF EXISTS `tbl_size`;
CREATE TABLE IF NOT EXISTS `tbl_size` (
  `size_id` int NOT NULL AUTO_INCREMENT,
  `size_name` varchar(255) NOT NULL,
  PRIMARY KEY (`size_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_size`
--

INSERT INTO `tbl_size` (`size_id`, `size_name`) VALUES
(1, '9.5*9*4.2cm'),
(8, '7.5*4*6'),
(9, '8.5*4.5*7cm'),
(10, '33'),
(12, '35'),
(13, '36'),
(14, '37'),
(15, '38'),
(16, '39'),
(19, '42'),
(20, '43'),
(21, '44'),
(22, '45'),
(23, '46'),
(24, '47'),
(25, '48'),
(26, 'Free Size'),
(27, 'One Size for All'),
(28, '10'),
(29, '12 Months'),
(30, '2T'),
(31, '3T'),
(32, '4T'),
(33, '5T'),
(34, '6 Years'),
(35, '7 Years'),
(36, '8 Years'),
(37, '10 Years'),
(38, '12 Years'),
(39, '14 Years'),
(40, '256 GB'),
(41, '128 GB'),
(42, '14 Plus'),
(43, '16 Plus'),
(44, '18 Plus'),
(45, '20 Plus'),
(46, '22 Plus'),
(47, '24 Plus'),
(48, '8.9*4.8cm');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_slider`
--

DROP TABLE IF EXISTS `tbl_slider`;
CREATE TABLE IF NOT EXISTS `tbl_slider` (
  `id` int NOT NULL AUTO_INCREMENT,
  `photo` varchar(255) NOT NULL,
  `heading` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `button_text` varchar(255) NOT NULL,
  `button_url` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_slider`
--

INSERT INTO `tbl_slider` (`id`, `photo`, `heading`, `content`, `button_text`, `button_url`, `position`) VALUES
(13, 'slider-13-1784886947-7d0133.jpg', 'LEAD,KINDLY LIGHT', '', '', '', 'Left');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_social`
--

DROP TABLE IF EXISTS `tbl_social`;
CREATE TABLE IF NOT EXISTS `tbl_social` (
  `social_id` int NOT NULL AUTO_INCREMENT,
  `social_name` varchar(30) NOT NULL,
  `social_url` varchar(255) NOT NULL,
  `social_icon` varchar(30) NOT NULL,
  PRIMARY KEY (`social_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_social`
--

INSERT INTO `tbl_social` (`social_id`, `social_name`, `social_url`, `social_icon`) VALUES
(1, 'Facebook', 'https://www.facebook.com/Rajanmemorial/', 'fa fa-facebook'),
(2, 'Twitter', 'https://www.twitter.com/#', 'fa fa-twitter'),
(3, 'LinkedIn', '', 'fa fa-linkedin'),
(4, 'Google Plus', '', 'fa fa-google-plus'),
(5, 'Pinterest', '', 'fa fa-pinterest'),
(6, 'YouTube', 'https://www.youtube.com/123', 'fa fa-youtube'),
(7, 'Instagram', 'https://www.instagram.com/techgathanepal/', 'fa fa-instagram'),
(8, 'Tumblr', '', 'fa fa-tumblr'),
(9, 'Flickr', '', 'fa fa-flickr'),
(10, 'Reddit', '', 'fa fa-reddit'),
(11, 'Snapchat', '', 'fa fa-snapchat'),
(12, 'WhatsApp', '', 'fa fa-whatsapp'),
(13, 'Quora', '', 'fa fa-quora'),
(14, 'StumbleUpon', '', 'fa fa-stumbleupon'),
(15, 'Delicious', '', 'fa fa-delicious'),
(16, 'Digg', '', 'fa fa-digg');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff`
--

DROP TABLE IF EXISTS `tbl_staff`;
CREATE TABLE IF NOT EXISTS `tbl_staff` (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL DEFAULT 'user-1.jpg',
  `address` text,
  `default_commission_type` varchar(20) NOT NULL DEFAULT 'percent',
  `default_commission_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `designation` varchar(150) NOT NULL DEFAULT '',
  `rating` tinyint NOT NULL DEFAULT '5',
  `facebook_url` varchar(255) NOT NULL DEFAULT '',
  `instagram_url` varchar(255) NOT NULL DEFAULT '',
  `show_on_website` tinyint NOT NULL DEFAULT '1',
  `bio` text,
  `level_id` int DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_staff`
--

INSERT INTO `tbl_staff` (`staff_id`, `full_name`, `email`, `phone`, `password`, `photo`, `address`, `default_commission_type`, `default_commission_value`, `status`, `created_at`, `designation`, `rating`, `facebook_url`, `instagram_url`, `show_on_website`, `bio`, `level_id`, `sort_order`) VALUES
(1, 'Demo Staff', 'staff@gmail.com', '9800000000', '$2y$10$9ZQdweYb3dGRxZ7eHe8sZOR7tWOt5wucQb3lmYLIOu0cPi9XpwSye', 'staff-1.jpg', '', 'percent', 35.00, 'Active', '2026-07-17 14:32:52', '', 4, 'https://www.facebook.com/neweraacademyedu/', 'https://www.instagram.com/jaishreepatange/', 1, '', 1, 0),
(2, 'uday kumar pandit', 'nirajk_mi@yonefu.info', '9801076273', '$2y$10$D06XyirZ1EknrGw2s4PGr.Q7ON4P8xQTnH1DZgMD/sgizJbSipyDO', 'staff-2.jpg', 'budhiganga gaupalika -7,biratnagar,morang', 'percent', 20.00, 'Active', '2026-07-21 14:22:30', '', 5, '', '', 1, '', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff_auto_assign`
--

DROP TABLE IF EXISTS `tbl_staff_auto_assign`;
CREATE TABLE IF NOT EXISTS `tbl_staff_auto_assign` (
  `id` int NOT NULL DEFAULT '1',
  `last_staff_id` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_staff_auto_assign`
--

INSERT INTO `tbl_staff_auto_assign` (`id`, `last_staff_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_staff_availability`
--

DROP TABLE IF EXISTS `tbl_staff_availability`;
CREATE TABLE IF NOT EXISTS `tbl_staff_availability` (
  `availability_id` int NOT NULL AUTO_INCREMENT,
  `staff_id` int NOT NULL,
  `day_of_week` tinyint NOT NULL,
  `start_time` time NOT NULL DEFAULT '08:00:00',
  `end_time` time NOT NULL DEFAULT '18:00:00',
  `is_available` tinyint NOT NULL DEFAULT '1',
  PRIMARY KEY (`availability_id`),
  KEY `idx_staff_day` (`staff_id`,`day_of_week`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_staff_availability`
--

INSERT INTO `tbl_staff_availability` (`availability_id`, `staff_id`, `day_of_week`, `start_time`, `end_time`, `is_available`) VALUES
(1, 1, 1, '08:00:00', '18:00:00', 1),
(2, 1, 1, '08:00:00', '18:00:00', 1),
(3, 2, 0, '08:00:00', '18:00:00', 1),
(4, 2, 0, '08:00:00', '22:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subscriber`
--

DROP TABLE IF EXISTS `tbl_subscriber`;
CREATE TABLE IF NOT EXISTS `tbl_subscriber` (
  `subs_id` int NOT NULL AUTO_INCREMENT,
  `subs_email` varchar(255) NOT NULL,
  `subs_date` varchar(100) NOT NULL,
  `subs_date_time` varchar(100) NOT NULL,
  `subs_hash` varchar(255) NOT NULL,
  `subs_active` int NOT NULL,
  PRIMARY KEY (`subs_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_subscriber`
--

INSERT INTO `tbl_subscriber` (`subs_id`, `subs_email`, `subs_date`, `subs_date_time`, `subs_hash`, `subs_active`) VALUES
(19, 'nirajkarna66@gmail.com', '2025-10-08', '2025-10-08 04:07:56', '1791c7d104ea59c65b8daa829b1f35fa', 1),
(23, 'mendhonepal@gmail.com', '2025-11-01', '2025-11-01 14:57:28', 'eb976e48c84d9d78f7396bb203957a5a', 1),
(25, 'rajan@gmail.com', '2026-07-02', '2026-07-02 11:46:05', '58d782cd7c5118a5', 1),
(26, 'pandey@gmail.com', '2026-07-21', '2026-07-21 17:59:07', '33d93a5ec9228ca8', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_teacher_level`
--

DROP TABLE IF EXISTS `tbl_teacher_level`;
CREATE TABLE IF NOT EXISTS `tbl_teacher_level` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sort_order_idx` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_teacher_level`
--

INSERT INTO `tbl_teacher_level` (`id`, `name`, `sort_order`, `status`, `created_at`) VALUES
(1, 'Leadership', 1, 'Active', '2026-07-23 10:57:34'),
(2, 'Primary Level', 2, 'Active', '2026-07-23 10:57:34'),
(3, 'Secondary Level', 3, 'Active', '2026-07-23 10:57:34'),
(4, 'Support Staff', 4, 'Active', '2026-07-23 10:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_testimonial`
--

DROP TABLE IF EXISTS `tbl_testimonial`;
CREATE TABLE IF NOT EXISTS `tbl_testimonial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `designation` varchar(150) NOT NULL DEFAULT '',
  `company` varchar(150) NOT NULL DEFAULT '',
  `review` text NOT NULL,
  `rating` tinyint NOT NULL DEFAULT '5',
  `photo` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_testimonial`
--

INSERT INTO `tbl_testimonial` (`id`, `name`, `designation`, `company`, `review`, `rating`, `photo`, `status`, `sort_order`, `created_at`) VALUES
(1, 'Anisha Shrestha', 'Homeowner', 'Kathmandu', 'The team arrived on time and left our home spotless. Booking online was easy and communication was clear.', 5, 'testimonial-1.jpg', 'Active', 1, '2026-07-17 15:46:19'),
(2, 'Rajesh Thapa', 'Office Manager', 'Lalitpur', 'We use 8848 Cleaning Service for our office every week. Reliable staff and consistent quality.', 5, '', 'Active', 2, '2026-07-17 15:46:19'),
(3, 'Maya Gurung', 'Apartment Owner', 'Bhaktapur', 'Deep clean before moving in was excellent. Highly recommend for anyone who wants a professional finish.', 4, '', 'Active', 3, '2026-07-17 15:46:19');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_top_category`
--

DROP TABLE IF EXISTS `tbl_top_category`;
CREATE TABLE IF NOT EXISTS `tbl_top_category` (
  `tcat_id` int NOT NULL AUTO_INCREMENT,
  `tcat_name` varchar(255) NOT NULL,
  `show_on_menu` int NOT NULL,
  PRIMARY KEY (`tcat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_top_category`
--

INSERT INTO `tbl_top_category` (`tcat_id`, `tcat_name`, `show_on_menu`) VALUES
(6, 'Candle Raw Material', 1),
(7, 'Resin Raw Material', 1),
(8, 'Packaging Raw Material', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL,
  `status` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `full_name`, `email`, `phone`, `password`, `photo`, `role`, `status`) VALUES
(1, 'Administrator', 'admin@mail.com', '9813766623', 'e10adc3949ba59abbe56e057f20f883e', 'user-1.jpg', 'Super Admin', 'Active'),
(2, 'Christine', 'christine@mail.com', '4444444444', '81dc9bdb52d04dc20036dbd8313ed055', 'user-13.jpg', 'Admin', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_vacancy`
--

DROP TABLE IF EXISTS `tbl_vacancy`;
CREATE TABLE IF NOT EXISTS `tbl_vacancy` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `department` varchar(150) NOT NULL DEFAULT '',
  `description` text,
  `deadline` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_vacancy`
--

INSERT INTO `tbl_vacancy` (`id`, `title`, `department`, `description`, `deadline`, `status`, `created_at`) VALUES
(1, 'senior Sciene Teacher ', 'Science and technology ', 'Job Summary:\r\n\r\nThe Exam In-Charge is responsible for planning, organizing, coordinating, and supervising all activities related to the internal and external examinations at Global College of Technology. You will ensure that assessments, evaluations and examinations (theory as well as practical) are conducted smoothly, fairly, and in compliance with the institutional and regulatory standards.\r\n\r\nKey Responsibilities: \r\n\r\n1. Exam Planning & Coordination\r\n\r\nPrepare academic calendars for all internal and external assessments and exams.\r\nCoordinate with faculty and departments to develop and finalize exam schedules.\r\nCommunicate timetables and exam-related updates to all stakeholders.\r\n2. Administration of Exams\r\n\r\nOrganize logistics for examination sessions (e.g., seat-planning, invigilators, materials).\r\nCoordinate to ensure availability of question papers, answer sheets, attendance registers, and other resources.\r\nSupervise the secure printing, packaging, and distribution of examination materials.\r\n3. Conduct & Supervision\r\n\r\nEnsure exams are conducted in a fair, disciplined, and secure manner.\r\nSupervise invigilators and address any irregularities or emergencies during exams.\r\nMaintain strict confidentiality of exam materials and results.\r\n4. Result Processing\r\n\r\nOversee the collection and collation of answer sheets for marking.\r\nCoordinate with faculty for timely evaluation and result submission.\r\nPrepare result summaries, mark sheets, transcripts, and grade reports.\r\n5. Compliance & Reporting\r\n\r\nEnsure all activities comply with internal policies and external academic boards or universities.\r\nPrepare necessary reports and documentation for audits and quality assurance.\r\n6. Support & Liaison\r\n\r\nLiaise with academic boards, universities, and regulatory bodies regarding exam processes.\r\nHandle student queries related to assessment, re-evaluation, or make-up exams.\r\nJob Specification\r\n\r\nRequired qualifications for this job\r\n\r\nRequired Education Level\r\n:\r\nUnder Graduate (Bachelor)\r\nRequired Experience\r\n:\r\nMore than 2 years\r\nOther Specification\r\n\r\nBachelor’s degree in relevant field with minimum of 2 years’ experience in academic/examination administration.\r\nExperience in Foreign Affiliated College will be prioritized\r\nExcellent communication and interpersonal skills.\r\nStrong organizational and multitasking abilities \r\nProfessional appearance and positive attitude \r\nSalary: Remuneration package will be highly attractive and negotiable.', '2026-07-31', 'Active', '2026-07-23 18:14:02'),
(2, 'School Coordinator (Pre-Primary)', 'Science and technology ', 'We are seeking a dedicated, organized, and passionate School Coordinator (Pre-Primary) to oversee the academic and administrative operations of our pre-primary section. If you are committed to early childhood education and have strong leadership skills, we encourage you to apply.\r\n\r\nKey Responsibilities\r\n\r\nCoordinate the daily academic and administrative activities of the pre-primary section.\r\nSupervise and support pre-primary teachers to ensure high-quality teaching and learning.\r\nMonitor lesson planning, classroom management, and curriculum implementation.\r\nFoster a safe, nurturing, and engaging learning environment for young children.\r\nMaintain effective communication with parents regarding students\' progress and school activities.\r\nOrganize school events, parent meetings, and co-curricular activities.\r\nEnsure compliance with school policies, academic standards, and child safety guidelines.\r\nMonitor student development and maintain accurate academic records.\r\nCollaborate with the school management team to achieve educational goals and continuous improvement.\r\nJob Specification\r\n\r\nRequired qualifications for this job\r\n\r\nRequired Education Level\r\n:\r\nUnder Graduate (Bachelor)\r\nRequired Experience\r\n:\r\nMore than 2 years\r\nOther Specification\r\n\r\nBachelor\'s degree in Education, Early Childhood Education, or a related field.\r\nMinimum 2 years of experience in a pre-primary teaching or coordination role.\r\nStrong knowledge of early childhood education practices and child development.\r\nExcellent leadership, communication, and interpersonal skills.\r\nGood organizational and problem-solving abilities.\r\nProficiency in MS Office and basic administrative tasks.\r\nAbility to work collaboratively with teachers, parents, and school management.\r\nSkills Required\r\n\r\nRequired skills for this job\r\n\r\nTeaching\r\nSupervision\r\nMonitoring\r\nLesson Planning\r\nEarly Childhood Care\r\nSalary\r\n\r\nOffered financial and non-financial compensation for this job\r\n\r\nOffered Salary\r\n:\r\nNot Disclosed', '2026-08-01', 'Active', '2026-07-23 18:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_video`
--

DROP TABLE IF EXISTS `tbl_video`;
CREATE TABLE IF NOT EXISTS `tbl_video` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `iframe_code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_video`
--

INSERT INTO `tbl_video` (`id`, `title`, `iframe_code`) VALUES
(1, 'Video 1', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/L3XAFSMdVWU\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>'),
(2, 'Video 2', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/sinQ06YzbJI\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>'),
(4, 'Video 3', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/ViZNgU-Yt-Y\" frameborder=\"0\" allow=\"autoplay; encrypted-media\" allowfullscreen></iframe>');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_why_feature`
--

DROP TABLE IF EXISTS `tbl_why_feature`;
CREATE TABLE IF NOT EXISTS `tbl_why_feature` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `icon_class` varchar(100) NOT NULL DEFAULT 'fa-star',
  `sort_order` int NOT NULL DEFAULT '0',
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_why_feature`
--

INSERT INTO `tbl_why_feature` (`id`, `title`, `icon`, `icon_class`, `sort_order`, `status`, `created_at`) VALUES
(1, '40 years of Excellence in Education.', '', 'fa-award', 1, 'Active', '2026-07-23 14:44:55'),
(2, 'Winner of Numerous National and Regional Educational Awards.', '', 'fa-trophy', 2, 'Active', '2026-07-23 14:44:55'),
(3, 'Well-Equipped Science and Computer Laboratories.', '', 'fa-flask', 3, 'Active', '2026-07-23 14:44:55'),
(4, 'Highly trained and Experienced Teachers.', '', 'fa-chalkboard-user', 4, 'Active', '2026-07-23 14:44:55'),
(5, 'ECA Training Imparted by Full-time National-Level Coaches.', '', 'fa-person-running', 5, 'Active', '2026-07-23 14:44:55'),
(6, 'Psychosocial counsellors and Career counsellors Available.', '', 'fa-comments', 6, 'Active', '2026-07-23 14:44:55');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
