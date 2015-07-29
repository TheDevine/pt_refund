-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2015 at 06:15 PM
-- Server version: 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pt_refund`
--

-- --------------------------------------------------------

--
-- Table structure for table `attachments`
--

CREATE TABLE IF NOT EXISTS `attachments` (
  `attach_id` int(11) NOT NULL AUTO_INCREMENT,
  `description` varchar(255) DEFAULT NULL,
  `filepath` varchar(255) DEFAULT NULL,
  `refund_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`attach_id`),
  KEY `refund_id` (`refund_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE IF NOT EXISTS `departments` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `name`) VALUES
(1, 'Admin'),
(2, 'Accounting'),
(3, 'PAR2'),
(4, 'PAR1'),
(5, 'Billing');

-- --------------------------------------------------------

--
-- Table structure for table `departments_orig`
--

CREATE TABLE IF NOT EXISTS `departments_orig` (
  `dept_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `departments_orig`
--

INSERT INTO `departments_orig` (`dept_id`, `name`) VALUES
(1, 'Admin'),
(2, 'Accounting'),
(3, 'Billing');

-- --------------------------------------------------------

--
-- Table structure for table `email_recipients`
--

CREATE TABLE IF NOT EXISTS `email_recipients` (
  `recipient_id` int(1) NOT NULL,
  `step` varchar(15) NOT NULL,
  `recipient_1` int(2) DEFAULT NULL,
  `recipient_2` int(11) DEFAULT NULL,
  `recipient_3` int(11) DEFAULT NULL,
  PRIMARY KEY (`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_recipients`
--

INSERT INTO `email_recipients` (`recipient_id`, `step`, `recipient_1`, `recipient_2`, `recipient_3`) VALUES
(1, 'accounting', 1, 7, 13),
(2, 'par2', 1, 10, 13),
(3, 'par1', 1, 10, 13),
(4, 'overdue_15_days', 1, 10, 13),
(5, 'overdue_30_days', 1, 10, 13),
(6, 'completed', 1, 10, 13);

-- --------------------------------------------------------

--
-- Table structure for table `email_settings`
--

CREATE TABLE IF NOT EXISTS `email_settings` (
  `email_ID` int(11) NOT NULL AUTO_INCREMENT,
  `initialBilling_warnTimePeriod` int(11) NOT NULL DEFAULT '15',
  `finalBilling_warnTimePeriod` int(11) NOT NULL DEFAULT '30',
  `accountWarn_initialTimePeriod` int(11) NOT NULL DEFAULT '15',
  `accountWarn_finalTimePeriod` int(11) NOT NULL DEFAULT '30',
  `emailAtEachStep_ifUrgent` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`email_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `email_settings`
--

INSERT INTO `email_settings` (`email_ID`, `initialBilling_warnTimePeriod`, `finalBilling_warnTimePeriod`, `accountWarn_initialTimePeriod`, `accountWarn_finalTimePeriod`, `emailAtEachStep_ifUrgent`) VALUES
(1, 15, 30, 15, 30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `email_warnings`
--

CREATE TABLE IF NOT EXISTS `email_warnings` (
  `email_warn_id` int(11) NOT NULL,
  `warning_15` int(3) NOT NULL,
  `warning_30` int(3) NOT NULL,
  PRIMARY KEY (`email_warn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_warnings`
--

INSERT INTO `email_warnings` (`email_warn_id`, `warning_15`, `warning_30`) VALUES
(1, 14, 14);

-- --------------------------------------------------------

--
-- Table structure for table `refund`
--

CREATE TABLE IF NOT EXISTS `refund` (
  `NG_enc_id` varchar(10) DEFAULT NULL,
  `refund_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `dt_request` datetime DEFAULT NULL,
  `dt_required` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `check_nbr` varchar(18) NOT NULL,
  `check_date` datetime NOT NULL,
  `payable` varchar(255) DEFAULT NULL,
  `addr_ln_1` varchar(255) DEFAULT NULL,
  `addr_ln_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `purpose` varchar(500) DEFAULT NULL,
  `dt_approved` datetime DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `vo_po_nbr` varchar(50) DEFAULT NULL,
  `GL_acct_nbr` varchar(50) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_dt` datetime DEFAULT NULL,
  `approved_dt` datetime DEFAULT NULL,
  `urgent` int(11) DEFAULT NULL,
  `voided` tinyint(1) NOT NULL DEFAULT '0',
  `accounting_approval` tinyint(1) NOT NULL DEFAULT '0',
  `billing_initial_approval` tinyint(1) NOT NULL,
  `billing_final_approval` int(1) NOT NULL DEFAULT '0',
  `rejected` int(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `account_nbr` varchar(18) NOT NULL,
  `refund_type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`refund_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=101 ;

--
-- Dumping data for table `refund`
--

INSERT INTO `refund` (`NG_enc_id`, `refund_id`, `created_by`, `approved_by`, `dt_request`, `dt_required`, `amount`, `check_nbr`, `check_date`, `payable`, `addr_ln_1`, `addr_ln_2`, `city`, `state`, `zip`, `purpose`, `dt_approved`, `status`, `vo_po_nbr`, `GL_acct_nbr`, `comments`, `modified_by`, `modified_dt`, `approved_dt`, `urgent`, `voided`, `accounting_approval`, `billing_initial_approval`, `billing_final_approval`, `rejected`, `assigned_to`, `account_nbr`, `refund_type`) VALUES
('12345', 1, 7, NULL, '2014-01-29 00:00:00', '2014-01-31 00:00:00', '2000.00', '', '0000-00-00 00:00:00', 'Cynthia Chapin', '101 Main Street', 'Apt 1', 'Burlington', 'VT', '05401', 'He works hard for the money', NULL, 'UPDATED', NULL, NULL, '', 7, '2015-06-08 21:59:37', NULL, NULL, 0, 0, 1, 0, 0, 14, '', NULL),
('123', 3, 7, NULL, '2014-01-29 00:00:00', '2014-01-29 00:00:00', '265111.00', '', '0000-00-00 00:00:00', 'Winnie the Pooh', '1 Nowhere Street', '', 'Burlingame', 'AR', '56214', 'Something', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, '', 7, '2015-06-02 18:08:22', NULL, NULL, 0, 0, 1, 0, 0, 1, '', NULL),
('12345', 4, 7, NULL, '2014-01-29 21:13:04', '2014-01-31 00:00:00', '2000.00', '', '0000-00-00 00:00:00', 'Jean Thibault', '2098 Dartmouth Ave', '', 'Columbus', 'OH', '43219', 'Because she is worth it', NULL, 'VOIDED', NULL, NULL, '', 7, '2015-06-02 20:14:39', NULL, NULL, 1, 0, 0, 0, 0, 11, '', NULL),
('333', 5, 7, NULL, '2015-06-02 20:42:21', NULL, '333.00', '', '0000-00-00 00:00:00', 'Derek M Devine', '16 Park Street Apt 3', '', 'Burlington', 'VT', '05401', 'elbow pain', NULL, 'COMPLETED', NULL, NULL, 'this could be serious.', 7, '2015-06-04 16:24:30', NULL, 0, 0, 1, 1, 1, 0, 13, '', NULL),
('1234', 6, 7, NULL, '2015-06-04 16:26:38', NULL, '555.00', '', '0000-00-00 00:00:00', 'Derek Devine', '16', '', 'Btown', 'VT', '05401', 'hand pain', NULL, 'COMPLETED', NULL, NULL, '', 7, '2015-06-04 21:42:46', NULL, 0, 0, 1, 1, 1, 0, 14, '', NULL),
('9876', 7, 7, 12, '2015-06-04 21:44:30', '0000-00-00 00:00:00', '777.00', '', '0000-00-00 00:00:00', 'DMD', '16 P', '', 'Burlington', 'VT', '05401', 'nausea', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'I feel queasy.', 7, '2015-06-09 22:42:30', NULL, 1, 0, 0, 1, 0, 0, 14, '', NULL),
('8765', 8, 14, NULL, '2015-06-05 16:53:39', NULL, '999.00', '123456', '2015-07-01 00:00:00', 'nother test', '5432', '', 'Burlington', 'VT', '21233', 'because', NULL, 'ACCOUNTING APPROVED', NULL, NULL, 'I can.', 72, '2015-07-27 18:11:15', NULL, 0, 0, 0, 1, 0, 0, 7, '', NULL),
('1235', 9, 7, NULL, '2015-06-08 18:31:31', NULL, '888.00', '', '0000-00-00 00:00:00', 'me', '123 k', '', 'sadsf', 'vt', '12342', 'dsf', NULL, 'COMPLETED', NULL, NULL, 'sdf', 75, '2015-07-27 14:50:54', NULL, 0, 0, 1, 1, 0, 0, 14, '', NULL),
('2113213', 10, 7, NULL, '2015-06-08 20:41:10', NULL, '7777.00', '', '0000-00-00 00:00:00', 'me', '12', '', 'sdf', 'sd', '123213', 'dsf', NULL, 'REJECTED', NULL, NULL, 'elbow pain', 7, '2015-06-08 22:39:57', NULL, 0, 1, 0, 0, 0, 1, 7, '', NULL),
('13221', 11, 7, NULL, '2015-06-09 16:36:31', NULL, '123.00', '', '0000-00-00 00:00:00', 'Derek Michael Devine', '16 P', '', 'Burlington', 'VT', '12321', 'test', NULL, 'NEW', NULL, NULL, 'out', NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 7, '', NULL),
('98765', 12, 7, NULL, '2015-06-09 17:22:55', NULL, '333.00', '', '0000-00-00 00:00:00', 'Derek Devine', '16 Park', '', 'Burlington', 'VT', '05401', 'sdf', NULL, 'COMPLETED', NULL, NULL, 'sdfsd', 75, '2015-07-27 14:44:18', NULL, 0, 0, 1, 1, 0, 0, 7, '', NULL),
('7685', 13, 7, NULL, '2015-06-09 20:42:19', NULL, '333.33', '', '0000-00-00 00:00:00', 'Test Cents', '16 P', '', 'Burlington', 'VT', '05401', 'sdf', NULL, 'NEW', NULL, NULL, 'fds', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 7, '', NULL),
('12312', 14, 14, NULL, '2015-06-18 20:03:49', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'sdf', 'sd', '12312', 'dgf', NULL, 'REJECTED', NULL, NULL, 'dfs', 14, '2015-07-01 19:10:19', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('123', 15, 14, NULL, '2015-06-18 20:12:04', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'fsdf', 'sd', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-01 19:10:58', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('1231', 16, 14, NULL, '2015-06-18 20:16:54', NULL, '1231.00', '', '0000-00-00 00:00:00', 'dfsf', 'dsf', '', 'dsf', 'df', '12312', 'dfs', NULL, 'VOIDED', NULL, NULL, 'sdf', 14, '2015-07-01 16:28:00', NULL, 0, 1, 0, 0, 0, 0, 14, '', NULL),
('12312', 17, 14, NULL, '2015-06-18 20:25:40', NULL, '12321.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'dsf', 'df', '12321', 'dsf', NULL, 'VOIDED', NULL, NULL, 'sdf', 14, '2015-07-01 16:19:30', NULL, 0, 1, 0, 0, 0, 0, 8, '', NULL),
('123', 18, 14, NULL, '2015-06-18 20:28:13', '2015-07-24 00:00:00', '213.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'sdf', 'sf', '12321', 'sdvf', NULL, 'REJECTED', NULL, NULL, 'sdffs', 14, '2015-07-01 17:03:40', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('12321', 19, 14, NULL, '2015-06-18 20:30:38', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'dsf', 'sd', '12321', 'sdf', NULL, 'VOIDED', NULL, NULL, 'dsf', 14, '2015-06-30 22:29:10', NULL, 0, 1, 0, 0, 0, 0, 18, '', NULL),
('123', 20, 14, NULL, '2015-06-18 20:35:02', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'dsfds', 'sd', '123213', 'sdf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('12321', 21, 14, NULL, '2015-06-18 20:36:49', NULL, '1231.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'dsf', 'sd', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('9876', 22, 14, NULL, '2015-07-01 19:43:30', NULL, '555.00', '', '0000-00-00 00:00:00', 'me', '12 n', '', 'Burlington', 'VT', '05401', 'to test drop down', NULL, 'REJECTED', NULL, NULL, 'blue', 14, '2015-07-01 20:11:15', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('123', 23, 14, NULL, '2015-07-01 19:55:08', NULL, '6667.00', '', '0000-00-00 00:00:00', 'k k', '12 h', '', 'Burlington', 'vt', '12321', 'lkj', NULL, 'NEW', NULL, NULL, '', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('123', 24, 14, NULL, '2015-07-01 20:06:26', NULL, '777.00', '', '0000-00-00 00:00:00', 'jump', 'street', '', 'Burlington', 'vt', '05401', 'proclaim', NULL, 'REJECTED', NULL, NULL, 'what is wrong.', 14, '2015-07-01 20:10:33', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('123', 25, 14, NULL, '2015-07-01 20:13:14', NULL, '999.00', '', '0000-00-00 00:00:00', 'you', '123', '', 'Burlington', 'VT', '63452', 'sdfdsfds', NULL, 'REJECTED', NULL, NULL, 'sdfds', 14, '2015-07-01 20:14:18', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('9876', 26, 14, NULL, '2015-07-02 15:33:30', NULL, '543.00', '', '0000-00-00 00:00:00', 'fsd', 'fds', '', 'sdf', 'DC', '12321', 'dsf', NULL, 'NEW', NULL, NULL, '', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('12321321', 37, 14, NULL, '2015-07-16 05:35:02', NULL, '12321.00', '12345', '2015-07-01 00:00:00', 'sdf', 'sdf', 'sdf', 'sdf', 'VA', '213213', 'sdfds', NULL, 'ACCOUNTING APPROVED', NULL, NULL, 'sdfs', 72, '2015-07-27 16:54:30', NULL, 0, 0, 0, 1, 0, 0, 14, '', NULL),
('864343', 38, 14, NULL, '2015-07-16 15:07:19', NULL, '765.00', '', '0000-00-00 00:00:00', 'me', '12', '', 'Burlington', 'VA', '12321', 'sdff', NULL, 'NEW', NULL, NULL, 'sdfds', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 72, '', NULL),
('12321321', 39, 14, NULL, '2015-07-16 15:45:00', NULL, '232.00', '76543', '2015-07-01 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VA', '12313', 'dsfs', NULL, 'ACCOUNTING APPROVED', NULL, NULL, 'dfsds', 72, '2015-07-27 16:44:35', NULL, 0, 0, 0, 1, 0, 0, 72, '', NULL),
('767868', 40, 14, NULL, '2015-07-16 16:22:42', NULL, '243.67', '123456', '2015-07-01 00:00:00', 'DD', '17 PK', '', 'Burlington', 'VA', '05401', 'sdf', NULL, 'COMPLETED', NULL, NULL, 'sdf', 75, '2015-07-27 17:17:22', NULL, 0, 0, 0, 1, 0, 0, 14, '', NULL),
('2132132', 41, 14, NULL, '2015-07-20 22:06:05', NULL, '1231.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'dsf', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('1231', 42, 14, NULL, '2015-07-20 22:07:23', NULL, '21321.00', '', '0000-00-00 00:00:00', 'dfs', 'sdf', '', 'dsf', 'GA', '12312', 'dsf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('21321312', 43, 14, NULL, '2015-07-20 22:09:31', NULL, '12321.00', '', '0000-00-00 00:00:00', 'dsfsd', 'dsf', '', 'bsdf', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('123213', 44, 14, NULL, '2015-07-20 22:14:19', NULL, '1231.00', '', '0000-00-00 00:00:00', 'sdf', 'sdfds', '', 'Burlington', 'VA', '12321', 'sdfs', NULL, 'NEW', NULL, NULL, 'dsfds', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('12321321', 45, 14, NULL, '2015-07-20 22:20:20', NULL, '12321.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'sdfdsf', 'VA', '12321', 'dsfds', NULL, 'Billing Initial Approval', NULL, NULL, 'dfsdsf', 14, '2015-07-22 21:52:15', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('4534345', 46, 14, NULL, '2015-07-20 22:22:53', NULL, '75757.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'Burlington', 'VA', '45423', 'sdf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-22 21:13:26', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('123213', 47, 14, NULL, '2015-07-20 22:25:06', NULL, '3454534.00', '', '0000-00-00 00:00:00', 'dfs', 'sdf', '', 'Burlington', 'VA', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-22 21:05:29', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('1232132', 48, 14, NULL, '2015-07-21 16:03:21', NULL, '3243.00', '', '0000-00-00 00:00:00', 'dsf', 'sdf', 'sdf', 'Burlington', 'VA', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-22 20:59:46', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('12321321', 49, 14, NULL, '2015-07-21 16:09:00', NULL, '12321321.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'bsdf', 'MA', '12321', 'dsf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-22 20:52:30', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('111', 50, 14, NULL, '2015-07-22 18:01:49', NULL, '777777.00', '', '0000-00-00 00:00:00', 'me', '123', '', 'Burlington', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('', 51, 14, NULL, '2015-07-22 20:04:48', NULL, '33333.00', '', '0000-00-00 00:00:00', '123', 'sdf', 'dsf', 'Burlington', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('987654321', 52, 14, NULL, '2015-07-22 20:11:37', NULL, '13131313.00', '', '0000-00-00 00:00:00', 'me', 'sdf', '', 'Burlington', 'VA', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'sdf', 14, '2015-07-22 20:50:56', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('', 53, 14, NULL, '2015-07-22 20:27:38', NULL, '9090909.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VA', '12321', 'sdf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('08642', 54, 14, NULL, '2015-07-22 20:28:36', NULL, '21321321.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VA', '12321', 'dsf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-22 20:47:55', NULL, 0, 0, 0, 0, 0, 1, 14, '', NULL),
('87654', 55, 14, NULL, '2015-07-23 17:08:33', NULL, '499.00', '', '0000-00-00 00:00:00', 'test', 'dsf', '', 'dfs', 'VA', '12321', 'sdf', NULL, 'Billing Initial Approval', NULL, NULL, 'dsf', 73, '2015-07-23 17:37:39', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('21313', 69, 14, NULL, '2015-07-23 22:35:49', NULL, '501.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'sdf', 'VA', '12321', 'sdf', NULL, 'COMPLETED', NULL, NULL, 'sdf', 75, '2015-07-23 22:57:17', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('', 70, 14, NULL, '2015-07-24 17:30:24', NULL, '909090.00', '', '0000-00-00 00:00:00', 'me', 'sdf', 'dfs', 'Burlington', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('12321', 71, 14, NULL, '2015-07-24 18:00:01', NULL, '555.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'B', 'NY', '12321', 'sdf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('1232132', 72, 14, NULL, '2015-07-24 18:19:59', NULL, '3333.00', '', '0000-00-00 00:00:00', 'sdfs', 'sdfds', '', 'Burlington', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('123123213', 73, 14, NULL, '2015-07-24 18:24:45', NULL, '34343.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'Burlington', 'LA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('66666', 74, 14, NULL, '2015-07-24 18:25:38', NULL, '322342.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'dsf', 'AR', '12312', 'sdf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('332333', 75, 14, NULL, '2015-07-24 18:26:16', NULL, '32423.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'dfs', 'VA', '12321', 'sdf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('7585934', 76, 14, NULL, '2015-07-24 18:27:06', NULL, '4543.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'Burlington', 'VA', '12312', 'dsf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('55345', 77, 14, NULL, '2015-07-24 18:28:10', NULL, '213242.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'sdfs', 'VA', '12321', 'sdvf', NULL, 'PAR2 Initial', NULL, NULL, 'sdf', 74, '2015-07-27 20:32:24', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('1234', 78, 14, NULL, '2015-07-27 13:49:51', NULL, '9876.00', '', '0000-00-00 00:00:00', 'dsf', 'dfs', '', 'Burlington', 'VA', '21342', 'dsf', NULL, 'PAR2 Initial', NULL, NULL, 'dsf', 74, '2015-07-27 13:54:42', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('12345', 79, 14, NULL, '2015-07-27 13:58:09', NULL, '9876.00', '', '0000-00-00 00:00:00', 'dsf', 'dsf', '', 'Burlington', 'VA', '12321', 'dsfdsdsf', NULL, 'PAR2 Initial', NULL, NULL, 'dsf', 74, '2015-07-27 13:59:14', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('43433', 80, 14, NULL, '2015-07-27 14:01:18', NULL, '12321.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'B', 'VA', '12321', 'dsf', NULL, 'COMPLETED', NULL, NULL, 'dsf', 75, '2015-07-27 14:43:21', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('456446', 82, 14, NULL, '2015-07-27 17:22:27', NULL, '3423.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'Burlington', 'VA', '12312', 'sdf', NULL, 'PAR2 Initial', NULL, NULL, 'dsf', 74, '2015-07-27 20:30:12', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('65433', 83, 74, NULL, '2015-07-27 17:50:32', NULL, '499.00', '123456', '2015-07-01 00:00:00', 'under five hundred', '123', '', 'Burlington', 'VA', '12321', 'dsf', NULL, 'COMPLETED', NULL, NULL, 'dsf', 75, '2015-07-27 18:12:36', NULL, 0, 0, 0, 0, 0, 0, 74, '', NULL),
('335577', 84, 14, NULL, '2015-07-27 19:45:32', NULL, '88888.00', '', '0000-00-00 00:00:00', 'dsf', 'sdf', '', 'Burlington', 'VT', '12321', 'dsf', NULL, 'PAR2 Initial', NULL, NULL, 'dsf', 74, '2015-07-27 20:28:16', NULL, 0, 0, 0, 0, 0, 0, 14, '', NULL),
('84756', 85, 14, NULL, '2015-07-27 20:20:21', NULL, '55555.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VT', '12321', 'sdsf', NULL, 'PAR2 Initial', NULL, NULL, 'dsf', 74, '2015-07-27 20:24:33', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('7531', 86, 14, NULL, '2015-07-27 21:33:19', NULL, '9876.00', '123456', '2015-07-01 00:00:00', 'me', 'sdf', '', 'Burlington', 'VT', '05401', 'sdf', NULL, 'COMPLETED', NULL, NULL, 'dsf', 75, '2015-07-27 21:42:14', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('4567', 87, 14, NULL, '2015-07-29 14:02:35', NULL, '333333.00', '', '0000-00-00 00:00:00', 'me', 'sdf', '', 'Burlington', 'VT', '05401', 'dsf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('45236', 88, 14, NULL, '2015-07-29 14:11:32', NULL, '3434343.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'Burlington', 'VT', '12321', 'sdf', NULL, 'NEW', NULL, NULL, 'dsf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('12345', 89, 14, NULL, '2015-07-29 14:18:11', NULL, '12321.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VT', '12312', 'sfs', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('23423', 90, 14, NULL, '2015-07-29 14:22:12', NULL, '3434343.00', '', '0000-00-00 00:00:00', 'sdf', 'dfs', '', 'Burlington', 'VT', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('34533', 91, 14, NULL, '2015-07-29 14:26:33', NULL, '343434.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VT', '12332', 'dsf', NULL, 'NEW', NULL, NULL, 'dsfds', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('32342', 92, 14, NULL, '2015-07-29 14:40:00', NULL, '342343.00', '', '0000-00-00 00:00:00', 'me', 'sdf', '', 'Burlington', 'VT', '12312', 'sdf', NULL, 'PAR2 Initial', NULL, NULL, 'fsfds', 74, '2015-07-29 15:49:42', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('234235566', 93, 14, NULL, '2015-07-29 14:43:53', NULL, '32454.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'Burlington', 'VT', '12321', 'dsf', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'sdfs', 74, '2015-07-29 15:48:36', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('234233333', 94, 14, NULL, '2015-07-29 14:47:16', NULL, '345435.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VT', '12321', 'sdf', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'sdf', 73, '2015-07-29 15:39:09', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('1232223232', 95, 14, NULL, '2015-07-29 14:51:20', NULL, '23423.00', '1232133232', '2015-07-29 00:00:00', 'sdf', 'sdf', '', 'Burlington', 'VT', '12321', 'dsf', NULL, 'COMPLETED', NULL, NULL, 'dsf', 75, '2015-07-29 15:55:44', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('212321', 96, 14, NULL, '2015-07-29 16:06:20', NULL, '333333.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'Burlington', 'VT', '12321', 'dsf', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'dsf', 73, '2015-07-29 16:08:25', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('1232132', 97, 14, NULL, '2015-07-29 16:12:23', NULL, '99999999.99', '', '0000-00-00 00:00:00', 'SDF', 'sdfds', '', 'Burlington', 'VT', '12321', 'sdf', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'dsfds', 73, '2015-07-29 16:15:14', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('1231231231', 98, 14, NULL, '2015-07-29 16:16:25', NULL, '13131313.00', '12321321', '2015-07-29 00:00:00', 'dsf', 'dsf', '', 'Burlington', 'VT', '12312', 'dsf', NULL, 'COMPLETED', NULL, NULL, 'sdf', 75, '2015-07-29 16:22:31', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('12312321', 99, 14, NULL, '2015-07-29 16:32:12', NULL, '99999999.99', '112321123', '2015-07-01 00:00:00', 'Derek', 'sdf', '', 'Burlington', 'VT', '12321', 'test', NULL, 'COMPLETED', NULL, NULL, 'ing', 75, '2015-07-29 16:40:32', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial'),
('1237654', 100, 14, NULL, '2015-07-29 17:15:21', NULL, '99999999.99', '1232132122', '2015-07-29 00:00:00', 'me', 'sdf', '', 'Burlington', 'VT', '12321', 'sdf', NULL, 'COMPLETED', NULL, NULL, 'sdf', 75, '2015-07-29 17:27:23', NULL, 0, 0, 0, 0, 0, 0, 14, '', 'Commercial');

-- --------------------------------------------------------

--
-- Table structure for table `refund_bak`
--

CREATE TABLE IF NOT EXISTS `refund_bak` (
  `NG_enc_id` varchar(10) DEFAULT NULL,
  `refund_id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `dt_request` datetime DEFAULT NULL,
  `dt_required` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `check_nbr` varchar(18) NOT NULL,
  `check_date` datetime NOT NULL,
  `payable` varchar(255) DEFAULT NULL,
  `addr_ln_1` varchar(255) DEFAULT NULL,
  `addr_ln_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` char(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `purpose` varchar(500) DEFAULT NULL,
  `dt_approved` datetime DEFAULT NULL,
  `status` varchar(25) DEFAULT NULL,
  `vo_po_nbr` varchar(50) DEFAULT NULL,
  `GL_acct_nbr` varchar(50) DEFAULT NULL,
  `comments` varchar(1000) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_dt` datetime DEFAULT NULL,
  `approved_dt` datetime DEFAULT NULL,
  `urgent` int(11) DEFAULT NULL,
  `voided` tinyint(1) NOT NULL DEFAULT '0',
  `accounting_approval` tinyint(1) NOT NULL DEFAULT '0',
  `billing_initial_approval` tinyint(1) NOT NULL,
  `billing_final_approval` int(1) NOT NULL DEFAULT '0',
  `rejected` int(1) NOT NULL DEFAULT '0',
  `assigned_to` int(11) NOT NULL,
  `account_nbr` varchar(18) NOT NULL,
  PRIMARY KEY (`refund_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Dumping data for table `refund_bak`
--

INSERT INTO `refund_bak` (`NG_enc_id`, `refund_id`, `created_by`, `approved_by`, `dt_request`, `dt_required`, `amount`, `check_nbr`, `check_date`, `payable`, `addr_ln_1`, `addr_ln_2`, `city`, `state`, `zip`, `purpose`, `dt_approved`, `status`, `vo_po_nbr`, `GL_acct_nbr`, `comments`, `modified_by`, `modified_dt`, `approved_dt`, `urgent`, `voided`, `accounting_approval`, `billing_initial_approval`, `billing_final_approval`, `rejected`, `assigned_to`, `account_nbr`) VALUES
('12345', 1, 7, NULL, '2014-01-29 00:00:00', '2014-01-31 00:00:00', '2000.00', '', '0000-00-00 00:00:00', 'Cynthia Chapin', '101 Main Street', 'Apt 1', 'Burlington', 'VT', '05401', 'He works hard for the money', NULL, 'UPDATED', NULL, NULL, '', 7, '2015-06-08 21:59:37', NULL, NULL, 0, 0, 1, 0, 0, 14, ''),
('123', 3, 7, NULL, '2014-01-29 00:00:00', '2014-01-29 00:00:00', '265111.00', '', '0000-00-00 00:00:00', 'Winnie the Pooh', '1 Nowhere Street', '', 'Burlingame', 'AR', '56214', 'Something', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, '', 7, '2015-06-02 18:08:22', NULL, NULL, 0, 0, 1, 0, 0, 1, ''),
('12345', 4, 7, NULL, '2014-01-29 21:13:04', '2014-01-31 00:00:00', '2000.00', '', '0000-00-00 00:00:00', 'Jean Thibault', '2098 Dartmouth Ave', '', 'Columbus', 'OH', '43219', 'Because she is worth it', NULL, 'VOIDED', NULL, NULL, '', 7, '2015-06-02 20:14:39', NULL, NULL, 1, 0, 0, 0, 0, 11, ''),
('333', 5, 7, NULL, '2015-06-02 20:42:21', NULL, '333.00', '', '0000-00-00 00:00:00', 'Derek M Devine', '16 Park Street Apt 3', '', 'Burlington', 'VT', '05401', 'elbow pain', NULL, 'COMPLETED', NULL, NULL, 'this could be serious.', 7, '2015-06-04 16:24:30', NULL, 0, 0, 1, 1, 1, 0, 13, ''),
('1234', 6, 7, NULL, '2015-06-04 16:26:38', NULL, '555.00', '', '0000-00-00 00:00:00', 'Derek Devine', '16', '', 'Btown', 'VT', '05401', 'hand pain', NULL, 'COMPLETED', NULL, NULL, '', 7, '2015-06-04 21:42:46', NULL, 0, 0, 1, 1, 1, 0, 14, ''),
('9876', 7, 7, 12, '2015-06-04 21:44:30', '0000-00-00 00:00:00', '777.00', '', '0000-00-00 00:00:00', 'DMD', '16 P', '', 'Burlington', 'VT', '05401', 'nausea', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'I feel queasy.', 7, '2015-06-09 22:42:30', NULL, 1, 0, 0, 1, 0, 0, 14, ''),
('8765', 8, 14, NULL, '2015-06-05 16:53:39', NULL, '999.00', '', '0000-00-00 00:00:00', 'nother test', '5432', '', 'Burlington', 'VT', '21233', 'because', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'I can.', 7, '2015-06-08 20:53:47', NULL, 0, 0, 0, 1, 0, 0, 7, ''),
('1235', 9, 7, NULL, '2015-06-08 18:31:31', NULL, '888.00', '', '0000-00-00 00:00:00', 'me', '123 k', '', 'sadsf', 'vt', '12342', 'dsf', NULL, 'ACCOUNTING APPROVED', NULL, NULL, 'sdf', 7, '2015-06-08 20:34:25', NULL, 0, 0, 1, 1, 0, 0, 14, ''),
('2113213', 10, 7, NULL, '2015-06-08 20:41:10', NULL, '7777.00', '', '0000-00-00 00:00:00', 'me', '12', '', 'sdf', 'sd', '123213', 'dsf', NULL, 'REJECTED', NULL, NULL, 'elbow pain', 7, '2015-06-08 22:39:57', NULL, 0, 1, 0, 0, 0, 1, 7, ''),
('13221', 11, 7, NULL, '2015-06-09 16:36:31', NULL, '123.00', '', '0000-00-00 00:00:00', 'Derek Michael Devine', '16 P', '', 'Burlington', 'VT', '12321', 'test', NULL, 'NEW', NULL, NULL, 'out', NULL, NULL, NULL, 1, 0, 0, 0, 0, 0, 7, ''),
('98765', 12, 7, NULL, '2015-06-09 17:22:55', NULL, '333.00', '', '0000-00-00 00:00:00', 'Derek Devine', '16 Park', '', 'Burlington', 'VT', '05401', 'sdf', NULL, 'ACCOUNTING APPROVED', NULL, NULL, 'sdfsd', 14, '2015-06-09 20:48:13', NULL, 0, 0, 1, 1, 0, 0, 7, ''),
('7685', 13, 7, NULL, '2015-06-09 20:42:19', NULL, '333.33', '', '0000-00-00 00:00:00', 'Test Cents', '16 P', '', 'Burlington', 'VT', '05401', 'sdf', NULL, 'NEW', NULL, NULL, 'fds', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 7, ''),
('12312', 14, 14, NULL, '2015-06-18 20:03:49', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'sdf', 'sd', '12312', 'dgf', NULL, 'REJECTED', NULL, NULL, 'dfs', 14, '2015-07-01 19:10:19', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('123', 15, 14, NULL, '2015-06-18 20:12:04', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'fsdf', 'sd', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-01 19:10:58', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('1231', 16, 14, NULL, '2015-06-18 20:16:54', NULL, '1231.00', '', '0000-00-00 00:00:00', 'dfsf', 'dsf', '', 'dsf', 'df', '12312', 'dfs', NULL, 'VOIDED', NULL, NULL, 'sdf', 14, '2015-07-01 16:28:00', NULL, 0, 1, 0, 0, 0, 0, 14, ''),
('12312', 17, 14, NULL, '2015-06-18 20:25:40', NULL, '12321.00', '', '0000-00-00 00:00:00', 'sdf', 'dsf', '', 'dsf', 'df', '12321', 'dsf', NULL, 'VOIDED', NULL, NULL, 'sdf', 14, '2015-07-01 16:19:30', NULL, 0, 1, 0, 0, 0, 0, 8, ''),
('123', 18, 14, NULL, '2015-06-18 20:28:13', '2015-07-24 00:00:00', '213.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'sdf', 'sf', '12321', 'sdvf', NULL, 'REJECTED', NULL, NULL, 'sdffs', 14, '2015-07-01 17:03:40', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('12321', 19, 14, NULL, '2015-06-18 20:30:38', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'dsf', 'sd', '12321', 'sdf', NULL, 'VOIDED', NULL, NULL, 'dsf', 14, '2015-06-30 22:29:10', NULL, 0, 1, 0, 0, 0, 0, 18, ''),
('123', 20, 14, NULL, '2015-06-18 20:35:02', NULL, '123.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'dsfds', 'sd', '123213', 'sdf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, ''),
('12321', 21, 14, NULL, '2015-06-18 20:36:49', NULL, '1231.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', '', 'dsf', 'sd', '12321', 'sdf', NULL, 'REJECTED', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('9876', 22, 14, NULL, '2015-07-01 19:43:30', NULL, '555.00', '', '0000-00-00 00:00:00', 'me', '12 n', '', 'Burlington', 'VT', '05401', 'to test drop down', NULL, 'REJECTED', NULL, NULL, 'blue', 14, '2015-07-01 20:11:15', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('123', 23, 14, NULL, '2015-07-01 19:55:08', NULL, '6667.00', '', '0000-00-00 00:00:00', 'k k', '12 h', '', 'Burlington', 'vt', '12321', 'lkj', NULL, 'NEW', NULL, NULL, '', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, ''),
('123', 24, 14, NULL, '2015-07-01 20:06:26', NULL, '777.00', '', '0000-00-00 00:00:00', 'jump', 'street', '', 'Burlington', 'vt', '05401', 'proclaim', NULL, 'REJECTED', NULL, NULL, 'what is wrong.', 14, '2015-07-01 20:10:33', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('123', 25, 14, NULL, '2015-07-01 20:13:14', NULL, '999.00', '', '0000-00-00 00:00:00', 'you', '123', '', 'Burlington', 'VT', '63452', 'sdfdsfds', NULL, 'REJECTED', NULL, NULL, 'sdfds', 14, '2015-07-01 20:14:18', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('9876', 26, 14, NULL, '2015-07-02 15:33:30', NULL, '543.00', '', '0000-00-00 00:00:00', 'fsd', 'fds', '', 'sdf', 'DC', '12321', 'dsf', NULL, 'NEW', NULL, NULL, '', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, ''),
('6543', 27, 14, NULL, '2015-07-02 17:40:12', NULL, '1231.00', '', '0000-00-00 00:00:00', 'DD', '12 ', '', 'Burlington', 'VA', '12321', 'dsf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 14, ''),
('12332', 28, 14, NULL, '2015-07-02 20:54:36', NULL, '534.00', '', '0000-00-00 00:00:00', 'sf', 'sfd', '', 'Burlington', 'VA', '23423', 'sdf', NULL, 'NEW', NULL, NULL, 'sdf', NULL, '2015-07-16 15:41:34', NULL, 0, 0, 0, 0, 0, 0, 72, ''),
('1232131', 36, 14, NULL, '2015-07-16 05:07:32', NULL, '12321.00', '', '0000-00-00 00:00:00', 'sdfs', 'sdf', '', 'sdf', 'VA', '12321', 'sdfe', NULL, 'REJECTED', NULL, NULL, 'dsf', 14, '2015-07-16 05:30:09', NULL, 0, 0, 0, 0, 0, 1, 14, ''),
('12321321', 37, 14, NULL, '2015-07-16 05:35:02', NULL, '12321.00', '', '0000-00-00 00:00:00', 'sdf', 'sdf', 'sdf', 'sdf', 'VA', '213213', 'sdfds', NULL, 'ACCOUNTING APPROVAL', NULL, NULL, 'sdfs', 14, '2015-07-16 06:54:03', NULL, 0, 0, 0, 1, 0, 0, 14, ''),
('864343', 38, 14, NULL, '2015-07-16 15:07:19', NULL, '765.00', '', '0000-00-00 00:00:00', 'me', '12', '', 'Burlington', 'VA', '12321', 'sdff', NULL, 'NEW', NULL, NULL, 'sdfds', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 72, '');

-- --------------------------------------------------------

--
-- Table structure for table `refund_changes`
--

CREATE TABLE IF NOT EXISTS `refund_changes` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `refund_id` int(11) NOT NULL,
  `status_before` varchar(30) NOT NULL,
  `status_after` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(30) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`change_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `refund_changes`
--

INSERT INTO `refund_changes` (`change_id`, `refund_id`, `status_before`, `status_after`, `date`, `name`, `comments`) VALUES
(1, 96, 'NEW', 'PAR2 Initial', '2015-07-01', '74', 'first approval'),
(2, 96, 'PAR2 Initial', 'ACCOUNTING APPROVAL', '2015-07-29', '73', 'initial approval'),
(3, 97, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'sdfs'),
(4, 97, 'PAR2 Initial', 'ACCOUNTING APPROVAL', '2015-07-29', '73', 'second'),
(5, 98, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'first'),
(6, 98, 'PAR2 Initial', 'ACCOUNTING APPROVAL', '2015-07-29', '73', 'secondary approval'),
(7, 98, 'ACCOUNTING APPROVAL', 'ACCOUNTING APPROVED', '2015-07-29', '72', 'attached check and completed'),
(8, 98, 'ACCOUNTING APPROVED', 'COMPLETED', '2015-07-29', '75', 'Completed Today!'),
(9, 99, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'initial approval completed'),
(10, 99, 'PAR2 Initial', 'ACCOUNTING APPROVAL', '2015-07-29', '73', 'secondary approval completed'),
(11, 99, 'ACCOUNTING APPROVAL', 'ACCOUNTING APPROVED', '2015-07-29', '72', 'attached check'),
(12, 99, 'ACCOUNTING APPROVED', 'COMPLETED', '2015-07-29', '75', 'finalized'),
(13, 100, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'billing initial approval'),
(14, 100, 'PAR2 Initial', 'ACCOUNTING APPROVAL', '2015-07-29', '73', 'billing final'),
(15, 100, 'ACCOUNTING APPROVAL', 'ACCOUNTING APPROVED', '2015-07-29', '72', 'attached check'),
(16, 100, 'ACCOUNTING APPROVED', 'COMPLETED', '2015-07-29', '75', 'Derek approved');

-- --------------------------------------------------------

--
-- Table structure for table `refund_changes_old`
--

CREATE TABLE IF NOT EXISTS `refund_changes_old` (
  `change_id` int(11) NOT NULL AUTO_INCREMENT,
  `refund_id` int(11) NOT NULL,
  `status_before` varchar(30) NOT NULL,
  `status_after` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `name` varchar(30) NOT NULL,
  `comments` text NOT NULL,
  PRIMARY KEY (`change_id`),
  UNIQUE KEY `Refund_Id` (`refund_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `refund_changes_old`
--

INSERT INTO `refund_changes_old` (`change_id`, `refund_id`, `status_before`, `status_after`, `date`, `name`, `comments`) VALUES
(1, 77, 'NEW', 'PAR2 Initial', '2015-07-27', '74', 'Does this work?'),
(2, 86, 'NEW', 'PAR2 Initial', '2015-07-27', '74', 'the initial approval'),
(3, 94, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'sdfs'),
(4, 95, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'first billing approval'),
(5, 93, 'NEW', 'PAR2 Initial', '2015-07-29', '73', 'first approval'),
(7, 92, 'NEW', 'PAR2 Initial', '2015-07-29', '74', 'first');

-- --------------------------------------------------------

--
-- Table structure for table `refund_manyencounters`
--

CREATE TABLE IF NOT EXISTS `refund_manyencounters` (
  `Encounter_ID` varchar(20) NOT NULL,
  `Refund_ID` int(10) NOT NULL,
  PRIMARY KEY (`Encounter_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `refund_manyencounters`
--

INSERT INTO `refund_manyencounters` (`Encounter_ID`, `Refund_ID`) VALUES
('111', 50),
('123', 50),
('321', 50);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `access_lvl` char(1) DEFAULT NULL,
  `dept_id` int(11) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `delete_ind` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `dept_id` (`dept_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `access_lvl`, `dept_id`, `password`, `username`, `delete_ind`) VALUES
(1, 'Jean ', 'Thibault', 'S', 3, '$6$h6uqWXPN$jVZdyiuA0zx0zVYYPP/XDXMTz33p8jIHlduN/PbpmNe1pOXmxlOvcpCVO7J8R4CHFQp4OwPr7sDMRQMLpRv9N/', 'jthibault', NULL),
(7, 'Jonathan', 'Bowley', 'S', 3, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'jbowley', NULL),
(8, 'David', 'Simmons', 'S', 2, '$6$QLQZYvc/OCcp$ar13ZHSlh7Pqod8vLyCFoQRywetkY4EPHJPfbz6yoLx1yIaCZlpucgMLZAguwU0RoMrRdLnMqfMi6mjLdTxVh0', 'dsimmons', NULL),
(10, 'Kenderlyn', 'Phelps', 'A', 2, '$6$Lu1EHq5eHL6B$SkY67B9hhKbio0JG6xDmJFJDQiKggeRAB3EUKlsYDvliqN8DICOHzMmYoVA1p6YT6RvXYkmcywVhYG0xHIEDN.', 'kphelps', NULL),
(11, 'Clarissa', 'Marmelejo-Stitely', 'A', 2, '$6$yLtkl6.ibCe/$aMLi2VlJ6WD0VxvdIFeddZc.TqS020ENfTwd6ekL9bkg8mUV6JAjn9TdtyiMSJx3SCtCQPUz2FuMer7mA5h0l1', 'cstitely', NULL),
(12, 'Sandra', 'Silva', 'A', 3, '$6$TBypQYNP11bA$ji0islDQHJ8RYTk7k/BVAHmGYnFSo0Q87sTBcb8AWat9f4muiNISrS7aGbMQbsP4TtHUvzaDItcxPa6hDuX0I.', 'ssilva', 1),
(13, 'Erika', 'Brown', 'S', 3, '$6$aTcnaPld$jqTkLqONWxyjUkMMcYC6KO555uGbVye70o4fh0nnw.9Y36ZbQ2aTzw1JOvoa2ktARm//W7QECX/XzLVZw1htb0', 'ebrown', NULL),
(14, 'Derek', 'Devine', 'S', 3, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'ddevine', NULL),
(18, 'Deanna', 'Hurne', 'A', 2, '$1$DZ2.QX5.$Sy5v14vTasM8069cA4Bgb/', 'dhurne', NULL),
(53, 'Laura', 'Wheatley', 'A', 3, NULL, 'lwheatley', NULL),
(54, 'Kimberly', 'Fuller', 'A', 3, NULL, 'kfuller', NULL),
(72, 'Accounting', 'Tester', 'S', 2, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'accounting', NULL),
(73, 'Final ', 'Approval', 'S', 5, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'Billing_Final', NULL),
(74, 'Initial', 'Approval', 'S', 5, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'Billing_Initial', NULL),
(75, 'Par1', 'Tester', 'S', 4, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'PAR1', NULL),
(76, 'admin', 'test', 'S', 1, '$1$1E5..y5.$2.6/Ooj8IHeVysOpEOZRj0', 'testadmin', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`refund_id`) REFERENCES `refund` (`refund_id`),
  ADD CONSTRAINT `attachments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `refund`
--
ALTER TABLE `refund`
  ADD CONSTRAINT `refund_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `refund_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`dept_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
