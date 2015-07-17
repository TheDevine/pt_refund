-- phpMyAdmin SQL Dump
-- version 3.5.8.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 30, 2014 at 12:37 AM
-- Server version: 5.5.34-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`dept_id`, `name`) VALUES
(1, 'Admin'),
(2, 'Accounting'),
(3, 'Billing');

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
  `amount` decimal(10,0) DEFAULT NULL,
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
  `modfied_by` int(11) DEFAULT NULL,
  `modified_dt` datetime DEFAULT NULL,
  `approved_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`refund_id`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `refund`
--

INSERT INTO `refund` (`NG_enc_id`, `refund_id`, `created_by`, `approved_by`, `dt_request`, `dt_required`, `amount`, `payable`, `addr_ln_1`, `addr_ln_2`, `city`, `state`, `zip`, `purpose`, `dt_approved`, `status`, `vo_po_nbr`, `GL_acct_nbr`, `comments`, `modfied_by`, `modified_dt`, `approved_dt`) VALUES
('12345', 1, 7, NULL, '2014-01-29 00:00:00', '2014-01-31 00:00:00', 1500, 'Cynthia Chapin', '101 Main Street', 'Apt 1', 'Burlington', 'VT', '05401', 'He works hard for the money', NULL, 'UPDATED', NULL, NULL, '', 7, '2014-01-29 22:22:06', NULL),
('123', 3, 7, NULL, '2014-01-29 00:00:00', '2014-01-29 00:00:00', 1, 'John Jacobjingleheimerschmit', '1 Nowhere Street', '', 'Burlingame', 'AR', '56214', 'Something', NULL, 'new', NULL, NULL, NULL, NULL, NULL, NULL),
('12345', 4, 7, NULL, '2014-01-29 21:13:04', '2014-01-31 00:00:00', 1000, 'Joy-El Barbour', '2098 Dartmouth Ave', '', 'Columbus', 'OH', '43219', 'He works hard for the money', NULL, 'UPDATED', NULL, NULL, '', 7, '2014-01-29 22:21:01', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `access_lvl`, `dept_id`, `password`, `username`, `delete_ind`) VALUES
(1, 'Jean ', 'Thibault', 'A', 3, '$6$889HEKcha.kk$Ji7zdRJCW2cj9fxjc4h0B.Xn84W6P3PSntF4xgAguTZ9yjL7fkFcalW42iyrh2me/1ixFfQTdPzS8NGVO8EpR/', 'jthibault', NULL),
(7, 'Jonathan', 'Bowley', 'S', 1, '$6$Mk4jQ5y8ONpd$x6yyL.nlrnx5vuQa/3OBk7Mb.QRXaYdjZjwzYegNODfHaPsK0DURKUvbMTwN8cdiijt2u.fcUaUvTqdNPVEe70', 'jbowley', NULL),
(8, 'David', 'Simmons', 'S', 2, '$6$QLQZYvc/OCcp$ar13ZHSlh7Pqod8vLyCFoQRywetkY4EPHJPfbz6yoLx1yIaCZlpucgMLZAguwU0RoMrRdLnMqfMi6mjLdTxVh0', 'dsimmons', NULL),
(9, 'Laura ', 'Hunter', 'U', 2, '$6$366W1Vmo1G..$j.mZ8SmihaAk8OpD4LmQrbTcTz6qF/iXj4JvM7rmnMe68MrbYQy.p3u1lDWOI97wzv0L6LxNEoaPk.fml9T.B.', 'lhunter', NULL),
(10, 'Kenderlyn', 'Phelps', 'A', 2, '$6$Lu1EHq5eHL6B$SkY67B9hhKbio0JG6xDmJFJDQiKggeRAB3EUKlsYDvliqN8DICOHzMmYoVA1p6YT6RvXYkmcywVhYG0xHIEDN.', 'kphelps', NULL),
(11, 'Clarissa', 'Marmelejo-Stitely', 'A', 2, '$6$yLtkl6.ibCe/$aMLi2VlJ6WD0VxvdIFeddZc.TqS020ENfTwd6ekL9bkg8mUV6JAjn9TdtyiMSJx3SCtCQPUz2FuMer7mA5h0l1', 'cstitely', NULL),
(12, 'Sandra', 'Silva', 'A', 3, '$6$TBypQYNP11bA$ji0islDQHJ8RYTk7k/BVAHmGYnFSo0Q87sTBcb8AWat9f4muiNISrS7aGbMQbsP4TtHUvzaDItcxPa6hDuX0I.', 'ssilva', NULL);

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
