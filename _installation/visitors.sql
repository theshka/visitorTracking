-- phpMyAdmin SQL Dump
-- version 4.0.10.6
-- http://www.phpmyadmin.net
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `visitors`
--

CREATE TABLE IF NOT EXISTS `visitors` (
`id` int(11) NOT NULL,
  `visitor_ip` varchar(32) DEFAULT NULL,
  `visitor_browser` varchar(255) DEFAULT NULL,
  `visitor_OS` varchar(64) NOT NULL,
  `visitor_city` varchar(64) NOT NULL,
  `visitor_state` varchar(64) NOT NULL,
  `visitor_country` varchar(64) NOT NULL,
  `visitor_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `visitor_day` varchar(2) NOT NULL,
  `visitor_month` varchar(2) NOT NULL,
  `visitor_year` varchar(4) NOT NULL,
  `visitor_hour` varchar(2) NOT NULL DEFAULT '0',
  `visitor_minute` varchar(2) NOT NULL DEFAULT '0',
  `visitor_seconds` varchar(2) NOT NULL,
  `visitor_referer` varchar(255) DEFAULT NULL,
  `visitor_page` varchar(255) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
