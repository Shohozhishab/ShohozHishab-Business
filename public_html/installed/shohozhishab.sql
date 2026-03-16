-- phpMyAdmin SQL Dump
-- version 5.1.1deb3+bionic1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 25, 2023 at 05:03 AM
-- Server version: 5.7.41-0ubuntu0.18.04.1
-- PHP Version: 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shohozhishab_ci4`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `user_id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(155) NOT NULL,
  `name` varchar(40) NOT NULL,
  `mobile` int(11) NOT NULL,
  `address` text,
  `pic` varchar(100) DEFAULT NULL,
  `country` varchar(155) DEFAULT NULL,
  `ComName` varchar(155) DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`user_id`, `email`, `password`, `name`, `mobile`, `address`, `pic`, `country`, `ComName`, `role_id`, `status`, `createdBy`, `createdDtm`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 'imranertaza12@gmail.com', '7c222fb2927d828af22f592134e8932480637c0d', 'Syed Imran Ertaza', 1924329315, 'Noapara, Abhaynagar, Jessore', 'profile_1664976903_39bd3bf1ddc2da4682f0.jpg', NULL, NULL, 1, '1', 1, '2018-11-21 18:05:40', 1, '2022-10-05 19:35:03', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `bank_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `account_no` bigint(20) NOT NULL,
  `balance` double UNSIGNED NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_deposit`
--

CREATE TABLE `bank_deposit` (
  `dep_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `amount` double UNSIGNED NOT NULL,
  `commont` text,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bank_withdraw`
--

CREATE TABLE `bank_withdraw` (
  `wthd_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `amount` double UNSIGNED NOT NULL,
  `commont` text,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(155) DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `chaque`
--

CREATE TABLE `chaque` (
  `chaque_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `chaque_number` int(22) NOT NULL,
  `to_name` varchar(155) DEFAULT NULL,
  `to` int(11) DEFAULT NULL,
  `from_name` varchar(155) DEFAULT NULL,
  `from` int(11) DEFAULT NULL,
  `from_loan_provider` int(11) DEFAULT NULL,
  `amount` double UNSIGNED NOT NULL,
  `issue_date` date NOT NULL,
  `account_number` int(30) DEFAULT NULL,
  `status` enum('Pending','Bounce','Approved') NOT NULL DEFAULT 'Pending',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) NOT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_name` varchar(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `father_name` varchar(55) DEFAULT NULL,
  `mother_name` varchar(55) DEFAULT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `present_address` text,
  `age` int(11) UNSIGNED DEFAULT NULL,
  `mobile` bigint(11) DEFAULT NULL,
  `pic` varchar(55) DEFAULT NULL,
  `nid` varchar(155) DEFAULT NULL,
  `cus_type_id` int(11) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `balance` double NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `customer_type`
--

CREATE TABLE `customer_type` (
  `cus_type_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `type_name` varchar(155) NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `salary` int(11) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `balance` double UNSIGNED DEFAULT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gen_settings`
--

CREATE TABLE `gen_settings` (
  `settings_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `label` varchar(155) NOT NULL,
  `value` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gen_settings_super`
--

CREATE TABLE `gen_settings_super` (
  `settings_id_sup` int(11) NOT NULL,
  `label` varchar(155) NOT NULL,
  `value` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `gen_settings_super`
--

INSERT INTO `gen_settings_super` (`settings_id_sup`, `label`, `value`, `createdDtm`, `createdBy`, `updatedBy`, `updatedDtm`, `deleted`, `deletedRole`) VALUES
(1, 'loading_message', 'Please wait until it is processing...', '2022-09-27 10:55:34', 0, NULL, '2023-03-20 19:54:41', NULL, NULL),
(2, 'site_title', 'Shohoz Hishab | Accounting management system', '2022-09-27 10:55:53', 0, NULL, '2022-09-27 10:55:53', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `invoice`
--

CREATE TABLE `invoice` (
  `invoice_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double UNSIGNED NOT NULL,
  `entire_sale_discount` int(11) DEFAULT NULL,
  `vat` int(11) DEFAULT NULL,
  `final_amount` double UNSIGNED NOT NULL,
  `profit` double NOT NULL COMMENT 'profit on the sale',
  `nagad_paid` double UNSIGNED DEFAULT NULL,
  `bank_paid` double UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `chaque_paid` double UNSIGNED DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `due` double UNSIGNED DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext COLLATE utf8_unicode_ci,
  `payment_method` longtext COLLATE utf8_unicode_ci,
  `payment_details` longtext COLLATE utf8_unicode_ci,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `year` longtext COLLATE utf8_unicode_ci,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_item`
--

CREATE TABLE `invoice_item` (
  `inv_item` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `title` varchar(155) DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` double UNSIGNED NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL,
  `total_price` double UNSIGNED NOT NULL,
  `discount` int(11) DEFAULT NULL,
  `final_price` double UNSIGNED NOT NULL,
  `profit` double NOT NULL COMMENT 'profit on individual product',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) UNSIGNED DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `lc`
--

CREATE TABLE `lc` (
  `lc_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `lc_name` varchar(155) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `amount` float UNSIGNED NOT NULL,
  `rest_balance` float NOT NULL COMMENT 'Rest of the balance of the LC',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) NOT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) NOT NULL,
  `deletedRole` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger`
--

CREATE TABLE `ledger` (
  `ledg_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_bank`
--

CREATE TABLE `ledger_bank` (
  `ledgBank_id` int(11) NOT NULL,
  `bank_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double UNSIGNED NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_capital`
--

CREATE TABLE `ledger_capital` (
  `capital_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_discount`
--

CREATE TABLE `ledger_discount` (
  `discount_ledg_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED DEFAULT NULL,
  `rest_balance` double DEFAULT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_employee`
--

CREATE TABLE `ledger_employee` (
  `ledg_emp_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double UNSIGNED NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_expense`
--

CREATE TABLE `ledger_expense` (
  `ledg_exp_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `memo_number` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_loan`
--

CREATE TABLE `ledger_loan` (
  `ledg_loan_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `loan_pro_id` int(11) NOT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_nagodan`
--

CREATE TABLE `ledger_nagodan` (
  `ledg_nagodan_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `loan_pro_id` int(11) DEFAULT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double UNSIGNED NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_other_sales`
--

CREATE TABLE `ledger_other_sales` (
  `ledg_oth_sales_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_profit`
--

CREATE TABLE `ledger_profit` (
  `profit_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_purchase`
--

CREATE TABLE `ledger_purchase` (
  `ledgPurch_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_sales`
--

CREATE TABLE `ledger_sales` (
  `ledgSale_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_stock`
--

CREATE TABLE `ledger_stock` (
  `stock_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `rtn_sale_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_suppliers`
--

CREATE TABLE `ledger_suppliers` (
  `ledg_sup_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `money_receipt_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `rtn_purchase_id` int(11) DEFAULT NULL,
  `chaque_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double UNSIGNED NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ledger_vat`
--

CREATE TABLE `ledger_vat` (
  `ledg_vat_id` int(11) NOT NULL,
  `vat_id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `sch_id` int(11) NOT NULL,
  `trans_id` int(11) DEFAULT NULL,
  `particulars` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL DEFAULT 'Cr.',
  `amount` double NOT NULL,
  `rest_balance` double NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `license`
--

CREATE TABLE `license` (
  `lic_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `lic_key` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `loan_provider`
--

CREATE TABLE `loan_provider` (
  `loan_pro_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `phone` bigint(11) DEFAULT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `balance` double NOT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `money_receipt`
--

CREATE TABLE `money_receipt` (
  `money_receipt_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(155) DEFAULT NULL,
  `amount` double UNSIGNED NOT NULL,
  `date` datetime NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(55) NOT NULL,
  `package_all_permission` text NOT NULL,
  `package_admin_permission` text NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `createdDtm` datetime NOT NULL,
  `createdBy` int(11) NOT NULL,
  `updatedDtm` datetime NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `payment_type`
--

CREATE TABLE `payment_type` (
  `pymnt_type_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `type_name` varchar(155) NOT NULL,
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `prod_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `name` varchar(55) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL,
  `unit` int(11) NOT NULL,
  `purchase_price` double NOT NULL,
  `selling_price` double NOT NULL,
  `purchase_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Last purchase price will be added here.',
  `supplier_id` int(11) NOT NULL,
  `size` int(11) DEFAULT NULL,
  `serial_number` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `picture` varchar(155) DEFAULT NULL,
  `warranty` varchar(55) DEFAULT NULL,
  `barcode` varchar(55) DEFAULT NULL,
  `prod_cat_id` int(11) DEFAULT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `prod_cat_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `parent_pro_cat` int(11) NOT NULL,
  `product_category` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase`
--

CREATE TABLE `purchase` (
  `purchase_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `nagad_paid` double DEFAULT NULL,
  `bank_paid` double DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `due` double DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_item`
--

CREATE TABLE `purchase_item` (
  `purchase_item_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `prod_id` int(11) NOT NULL,
  `purchase_price` double NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` double NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `return_purchase`
--

CREATE TABLE `return_purchase` (
  `rtn_purchase_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double UNSIGNED NOT NULL,
  `rtn_profit` double NOT NULL COMMENT 'profit on the sale',
  `nagad_paid` double UNSIGNED DEFAULT NULL,
  `bank_paid` double UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext COLLATE utf8_unicode_ci,
  `payment_method` longtext COLLATE utf8_unicode_ci,
  `payment_details` longtext COLLATE utf8_unicode_ci,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `year` longtext COLLATE utf8_unicode_ci,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_purchase_item`
--

CREATE TABLE `return_purchase_item` (
  `rtn_purchase_item_id` int(11) NOT NULL,
  `rtn_purchase_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `title` varchar(155) DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` double UNSIGNED NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL,
  `total_price` double UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) UNSIGNED DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `return_sale`
--

CREATE TABLE `return_sale` (
  `rtn_sale_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `pymnt_type_id` int(11) DEFAULT NULL,
  `customer_name` varchar(155) COLLATE utf8_unicode_ci DEFAULT NULL,
  `amount` double NOT NULL,
  `rtn_profit` double NOT NULL COMMENT 'profit on the sale',
  `nagad_paid` double UNSIGNED DEFAULT NULL,
  `bank_paid` double UNSIGNED DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `creation_timestamp` int(11) DEFAULT NULL,
  `payment_timestamp` longtext COLLATE utf8_unicode_ci,
  `payment_method` longtext COLLATE utf8_unicode_ci,
  `payment_details` longtext COLLATE utf8_unicode_ci,
  `status` enum('1','0') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `year` longtext COLLATE utf8_unicode_ci,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `return_sale_item`
--

CREATE TABLE `return_sale_item` (
  `rtn_sale_item_id` int(11) NOT NULL,
  `rtn_sale_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `prod_id` int(11) DEFAULT NULL,
  `title` varchar(155) DEFAULT NULL COMMENT 'It will be used, if prod_id is not inserted into the table.',
  `price` double UNSIGNED NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL,
  `total_price` double UNSIGNED NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) UNSIGNED DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `role` varchar(30) NOT NULL,
  `permission` text NOT NULL,
  `is_default` enum('1','0') NOT NULL DEFAULT '0',
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedby` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sales_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `cash` double UNSIGNED NOT NULL COMMENT 'This is the nagad cash of the shop owner.',
  `capital` double DEFAULT NULL,
  `profit` double DEFAULT NULL,
  `stockAmount` double DEFAULT NULL,
  `expense` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `purchase_balance` double NOT NULL,
  `sale_balance` double NOT NULL,
  `address` text,
  `mobile` bigint(11) DEFAULT NULL,
  `comment` text,
  `logo` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `package_id` int(11) DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `opening_status` enum('0','1') NOT NULL DEFAULT '0',
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `store_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text NOT NULL,
  `is_default` enum('1','0') NOT NULL DEFAULT '0',
  `createdDtm` datetime DEFAULT CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `balance` double NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `phone` bigint(12) DEFAULT NULL,
  `status` enum('1','0') NOT NULL DEFAULT '1',
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `trans_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text,
  `trangaction_type` enum('Dr.','Cr.') NOT NULL,
  `amount` double UNSIGNED NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `loan_pro_id` int(11) DEFAULT NULL,
  `bank_id` int(11) DEFAULT NULL,
  `lc_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `vat_id` int(11) DEFAULT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(155) NOT NULL,
  `name` varchar(40) NOT NULL,
  `mobile` bigint(11) DEFAULT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `pic` varchar(100) NOT NULL,
  `role_id` int(11) NOT NULL,
  `status` enum('1','0') NOT NULL,
  `is_default` enum('1','0') NOT NULL DEFAULT '0',
  `permission` text NOT NULL,
  `createdBy` int(11) DEFAULT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `vat_register`
--

CREATE TABLE `vat_register` (
  `vat_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `name` varchar(155) NOT NULL,
  `vat_register_no` varchar(155) DEFAULT NULL,
  `balance` double NOT NULL,
  `is_default` enum('1','0') NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `warranty_manage`
--

CREATE TABLE `warranty_manage` (
  `warranty_id` int(11) NOT NULL,
  `sch_id` int(11) NOT NULL,
  `product_name` varchar(55) NOT NULL,
  `receive_date` date NOT NULL,
  `delivery_date` date NOT NULL,
  `customer_address` varchar(55) NOT NULL,
  `customer_name` varchar(55) NOT NULL,
  `mobile` int(11) NOT NULL,
  `createdDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdBy` int(11) NOT NULL,
  `updateDtm` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedBy` int(11) DEFAULT NULL,
  `deleted` int(11) DEFAULT NULL,
  `deletedRole` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `mobile` (`mobile`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`bank_id`),
  ADD UNIQUE KEY `sch_id_2` (`sch_id`,`name`,`account_no`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `bank_deposit`
--
ALTER TABLE `bank_deposit`
  ADD PRIMARY KEY (`dep_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `bank_withdraw`
--
ALTER TABLE `bank_withdraw`
  ADD PRIMARY KEY (`wthd_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `chaque`
--
ALTER TABLE `chaque`
  ADD PRIMARY KEY (`chaque_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `sch_id_2` (`sch_id`,`mobile`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `cus_type_id` (`cus_type_id`);

--
-- Indexes for table `customer_type`
--
ALTER TABLE `customer_type`
  ADD PRIMARY KEY (`cus_type_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `gen_settings`
--
ALTER TABLE `gen_settings`
  ADD PRIMARY KEY (`settings_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `gen_settings_super`
--
ALTER TABLE `gen_settings_super`
  ADD PRIMARY KEY (`settings_id_sup`);

--
-- Indexes for table `invoice`
--
ALTER TABLE `invoice`
  ADD PRIMARY KEY (`invoice_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `student_id` (`customer_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD PRIMARY KEY (`inv_item`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `inv_exist_item_id` (`prod_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `lc`
--
ALTER TABLE `lc`
  ADD PRIMARY KEY (`lc_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `bank_id` (`bank_id`);

--
-- Indexes for table `ledger`
--
ALTER TABLE `ledger`
  ADD PRIMARY KEY (`ledg_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `inv_id` (`invoice_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_bank`
--
ALTER TABLE `ledger_bank`
  ADD PRIMARY KEY (`ledgBank_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_capital`
--
ALTER TABLE `ledger_capital`
  ADD PRIMARY KEY (`capital_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_discount`
--
ALTER TABLE `ledger_discount`
  ADD PRIMARY KEY (`discount_ledg_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `inv_id` (`invoice_id`);

--
-- Indexes for table `ledger_employee`
--
ALTER TABLE `ledger_employee`
  ADD PRIMARY KEY (`ledg_emp_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `ledger_expense`
--
ALTER TABLE `ledger_expense`
  ADD PRIMARY KEY (`ledg_exp_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `inv_id` (`memo_number`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_loan`
--
ALTER TABLE `ledger_loan`
  ADD PRIMARY KEY (`ledg_loan_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `loan_pro_id` (`loan_pro_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_nagodan`
--
ALTER TABLE `ledger_nagodan`
  ADD PRIMARY KEY (`ledg_nagodan_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `loan_pro_id` (`loan_pro_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_other_sales`
--
ALTER TABLE `ledger_other_sales`
  ADD PRIMARY KEY (`ledg_oth_sales_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`trans_id`);

--
-- Indexes for table `ledger_profit`
--
ALTER TABLE `ledger_profit`
  ADD PRIMARY KEY (`profit_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`invoice_id`);

--
-- Indexes for table `ledger_purchase`
--
ALTER TABLE `ledger_purchase`
  ADD PRIMARY KEY (`ledgPurch_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`);

--
-- Indexes for table `ledger_sales`
--
ALTER TABLE `ledger_sales`
  ADD PRIMARY KEY (`ledgSale_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `rtn_sale_id` (`rtn_sale_id`);

--
-- Indexes for table `ledger_stock`
--
ALTER TABLE `ledger_stock`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`invoice_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `ledger_suppliers`
--
ALTER TABLE `ledger_suppliers`
  ADD PRIMARY KEY (`ledg_sup_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `money_receipt_id` (`money_receipt_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `rtn_purchase_id` (`rtn_purchase_id`);

--
-- Indexes for table `ledger_vat`
--
ALTER TABLE `ledger_vat`
  ADD PRIMARY KEY (`ledg_vat_id`),
  ADD KEY `lc_id` (`invoice_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `trans_id` (`trans_id`),
  ADD KEY `vat_id` (`vat_id`);

--
-- Indexes for table `license`
--
ALTER TABLE `license`
  ADD PRIMARY KEY (`lic_id`),
  ADD UNIQUE KEY `sch_id_2` (`sch_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `loan_provider`
--
ALTER TABLE `loan_provider`
  ADD PRIMARY KEY (`loan_pro_id`),
  ADD UNIQUE KEY `sch_id_2` (`sch_id`,`phone`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `money_receipt`
--
ALTER TABLE `money_receipt`
  ADD PRIMARY KEY (`money_receipt_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `payment_type`
--
ALTER TABLE `payment_type`
  ADD PRIMARY KEY (`pymnt_type_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`prod_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `prod_cat_id` (`prod_cat_id`),
  ADD KEY `brand_id` (`brand_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`prod_cat_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `purchase`
--
ALTER TABLE `purchase`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchase_item`
--
ALTER TABLE `purchase_item`
  ADD PRIMARY KEY (`purchase_item_id`),
  ADD KEY `supplier_id` (`prod_id`),
  ADD KEY `purchase_item_id` (`purchase_item_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `return_purchase`
--
ALTER TABLE `return_purchase`
  ADD PRIMARY KEY (`rtn_purchase_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `student_id` (`supplier_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `return_purchase_item`
--
ALTER TABLE `return_purchase_item`
  ADD PRIMARY KEY (`rtn_purchase_item_id`),
  ADD KEY `invoice_id` (`rtn_purchase_id`),
  ADD KEY `inv_exist_item_id` (`prod_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `return_sale`
--
ALTER TABLE `return_sale`
  ADD PRIMARY KEY (`rtn_sale_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `student_id` (`customer_id`),
  ADD KEY `pymnt_type_id` (`pymnt_type_id`);

--
-- Indexes for table `return_sale_item`
--
ALTER TABLE `return_sale_item`
  ADD PRIMARY KEY (`rtn_sale_item_id`),
  ADD KEY `invoice_id` (`rtn_sale_id`),
  ADD KEY `inv_exist_item_id` (`prod_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sales_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `prod_id` (`invoice_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`sch_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`store_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `sch_id_2` (`sch_id`,`phone`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`trans_id`),
  ADD KEY `sch_id` (`sch_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `loan_pro_id` (`loan_pro_id`),
  ADD KEY `bank_id` (`bank_id`),
  ADD KEY `lc_id` (`lc_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `vat_id` (`vat_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `vat_register`
--
ALTER TABLE `vat_register`
  ADD PRIMARY KEY (`vat_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- Indexes for table `warranty_manage`
--
ALTER TABLE `warranty_manage`
  ADD PRIMARY KEY (`warranty_id`),
  ADD KEY `sch_id` (`sch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_deposit`
--
ALTER TABLE `bank_deposit`
  MODIFY `dep_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bank_withdraw`
--
ALTER TABLE `bank_withdraw`
  MODIFY `wthd_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chaque`
--
ALTER TABLE `chaque`
  MODIFY `chaque_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_type`
--
ALTER TABLE `customer_type`
  MODIFY `cus_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gen_settings`
--
ALTER TABLE `gen_settings`
  MODIFY `settings_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gen_settings_super`
--
ALTER TABLE `gen_settings_super`
  MODIFY `settings_id_sup` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice`
--
ALTER TABLE `invoice`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_item`
--
ALTER TABLE `invoice_item`
  MODIFY `inv_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lc`
--
ALTER TABLE `lc`
  MODIFY `lc_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger`
--
ALTER TABLE `ledger`
  MODIFY `ledg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_bank`
--
ALTER TABLE `ledger_bank`
  MODIFY `ledgBank_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_capital`
--
ALTER TABLE `ledger_capital`
  MODIFY `capital_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_discount`
--
ALTER TABLE `ledger_discount`
  MODIFY `discount_ledg_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_employee`
--
ALTER TABLE `ledger_employee`
  MODIFY `ledg_emp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_expense`
--
ALTER TABLE `ledger_expense`
  MODIFY `ledg_exp_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_loan`
--
ALTER TABLE `ledger_loan`
  MODIFY `ledg_loan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_nagodan`
--
ALTER TABLE `ledger_nagodan`
  MODIFY `ledg_nagodan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_other_sales`
--
ALTER TABLE `ledger_other_sales`
  MODIFY `ledg_oth_sales_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_profit`
--
ALTER TABLE `ledger_profit`
  MODIFY `profit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_purchase`
--
ALTER TABLE `ledger_purchase`
  MODIFY `ledgPurch_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_sales`
--
ALTER TABLE `ledger_sales`
  MODIFY `ledgSale_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_stock`
--
ALTER TABLE `ledger_stock`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_suppliers`
--
ALTER TABLE `ledger_suppliers`
  MODIFY `ledg_sup_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledger_vat`
--
ALTER TABLE `ledger_vat`
  MODIFY `ledg_vat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `license`
--
ALTER TABLE `license`
  MODIFY `lic_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loan_provider`
--
ALTER TABLE `loan_provider`
  MODIFY `loan_pro_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `money_receipt`
--
ALTER TABLE `money_receipt`
  MODIFY `money_receipt_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_type`
--
ALTER TABLE `payment_type`
  MODIFY `pymnt_type_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `prod_cat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase`
--
ALTER TABLE `purchase`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_item`
--
ALTER TABLE `purchase_item`
  MODIFY `purchase_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_purchase`
--
ALTER TABLE `return_purchase`
  MODIFY `rtn_purchase_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_purchase_item`
--
ALTER TABLE `return_purchase_item`
  MODIFY `rtn_purchase_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_sale`
--
ALTER TABLE `return_sale`
  MODIFY `rtn_sale_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `return_sale_item`
--
ALTER TABLE `return_sale_item`
  MODIFY `rtn_sale_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sales_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `sch_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `store_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `trans_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vat_register`
--
ALTER TABLE `vat_register`
  MODIFY `vat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warranty_manage`
--
ALTER TABLE `warranty_manage`
  MODIFY `warranty_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bank`
--
ALTER TABLE `bank`
  ADD CONSTRAINT `bank_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `bank_deposit`
--
ALTER TABLE `bank_deposit`
  ADD CONSTRAINT `bank_deposit_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bank_deposit_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON UPDATE CASCADE;

--
-- Constraints for table `bank_withdraw`
--
ALTER TABLE `bank_withdraw`
  ADD CONSTRAINT `bank_withdraw_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bank_withdraw_ibfk_2` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `brand`
--
ALTER TABLE `brand`
  ADD CONSTRAINT `brand_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`);

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `customers_ibfk_2` FOREIGN KEY (`cus_type_id`) REFERENCES `customer_type` (`cus_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `customer_type`
--
ALTER TABLE `customer_type`
  ADD CONSTRAINT `customer_type_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `gen_settings`
--
ALTER TABLE `gen_settings`
  ADD CONSTRAINT `gen_settings_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`pymnt_type_id`) REFERENCES `payment_type` (`pymnt_type_id`) ON UPDATE CASCADE;

--
-- Constraints for table `invoice_item`
--
ALTER TABLE `invoice_item`
  ADD CONSTRAINT `invoice_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_item_ibfk_2` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invoice_item_ibfk_3` FOREIGN KEY (`prod_id`) REFERENCES `products` (`prod_id`) ON UPDATE CASCADE;

--
-- Constraints for table `lc`
--
ALTER TABLE `lc`
  ADD CONSTRAINT `lc_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `lc_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger`
--
ALTER TABLE `ledger`
  ADD CONSTRAINT `ledger_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_ibfk_3` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_ibfk_4` FOREIGN KEY (`trans_id`) REFERENCES `transaction` (`trans_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger_bank`
--
ALTER TABLE `ledger_bank`
  ADD CONSTRAINT `ledger_bank_ibfk_1` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_bank_ibfk_2` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_bank_ibfk_3` FOREIGN KEY (`money_receipt_id`) REFERENCES `money_receipt` (`money_receipt_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_bank_ibfk_4` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_bank_ibfk_5` FOREIGN KEY (`trans_id`) REFERENCES `transaction` (`trans_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger_loan`
--
ALTER TABLE `ledger_loan`
  ADD CONSTRAINT `ledger_loan_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_loan_ibfk_2` FOREIGN KEY (`loan_pro_id`) REFERENCES `loan_provider` (`loan_pro_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_loan_ibfk_3` FOREIGN KEY (`money_receipt_id`) REFERENCES `money_receipt` (`money_receipt_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_loan_ibfk_4` FOREIGN KEY (`trans_id`) REFERENCES `transaction` (`trans_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger_nagodan`
--
ALTER TABLE `ledger_nagodan`
  ADD CONSTRAINT `ledger_nagodan_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_nagodan_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_nagodan_ibfk_3` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_nagodan_ibfk_4` FOREIGN KEY (`loan_pro_id`) REFERENCES `loan_provider` (`loan_pro_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_nagodan_ibfk_5` FOREIGN KEY (`money_receipt_id`) REFERENCES `money_receipt` (`money_receipt_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_nagodan_ibfk_6` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_nagodan_ibfk_7` FOREIGN KEY (`trans_id`) REFERENCES `transaction` (`trans_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger_suppliers`
--
ALTER TABLE `ledger_suppliers`
  ADD CONSTRAINT `ledger_suppliers_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_suppliers_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_suppliers_ibfk_3` FOREIGN KEY (`money_receipt_id`) REFERENCES `money_receipt` (`money_receipt_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_suppliers_ibfk_4` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_suppliers_ibfk_5` FOREIGN KEY (`trans_id`) REFERENCES `transaction` (`trans_id`) ON UPDATE CASCADE;

--
-- Constraints for table `ledger_vat`
--
ALTER TABLE `ledger_vat`
  ADD CONSTRAINT `ledger_vat_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_vat_ibfk_2` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ledger_vat_ibfk_3` FOREIGN KEY (`trans_id`) REFERENCES `transaction` (`trans_id`) ON UPDATE CASCADE;

--
-- Constraints for table `license`
--
ALTER TABLE `license`
  ADD CONSTRAINT `license_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `loan_provider`
--
ALTER TABLE `loan_provider`
  ADD CONSTRAINT `loan_provider_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `money_receipt`
--
ALTER TABLE `money_receipt`
  ADD CONSTRAINT `money_receipt_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `money_receipt_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payment_type`
--
ALTER TABLE `payment_type`
  ADD CONSTRAINT `payment_type_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`store_id`) REFERENCES `stores` (`store_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_4` FOREIGN KEY (`prod_cat_id`) REFERENCES `product_category` (`prod_cat_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_5` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`),
  ADD CONSTRAINT `products_ibfk_6` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`) ON UPDATE CASCADE;

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase`
--
ALTER TABLE `purchase`
  ADD CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE;

--
-- Constraints for table `purchase_item`
--
ALTER TABLE `purchase_item`
  ADD CONSTRAINT `purchase_item_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`) ON UPDATE CASCADE;

--
-- Constraints for table `return_sale_item`
--
ALTER TABLE `return_sale_item`
  ADD CONSTRAINT `return_sale_item_ibfk_1` FOREIGN KEY (`rtn_sale_id`) REFERENCES `return_sale` (`rtn_sale_id`) ON UPDATE CASCADE;

--
-- Constraints for table `roles`
--
ALTER TABLE `roles`
  ADD CONSTRAINT `roles_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON UPDATE CASCADE;

--
-- Constraints for table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shops_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `package` (`package_id`) ON UPDATE CASCADE;

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `stores_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`loan_pro_id`) REFERENCES `loan_provider` (`loan_pro_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_3` FOREIGN KEY (`bank_id`) REFERENCES `bank` (`bank_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_4` FOREIGN KEY (`lc_id`) REFERENCES `lc` (`lc_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_5` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_6` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_7` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_8` FOREIGN KEY (`vat_id`) REFERENCES `vat_register` (`vat_id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `warranty_manage`
--
ALTER TABLE `warranty_manage`
  ADD CONSTRAINT `warranty_manage_ibfk_1` FOREIGN KEY (`sch_id`) REFERENCES `shops` (`sch_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
