-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 17, 2014 at 09:36 PM
-- Server version: 5.5.38
-- PHP Version: 5.4.4-14+deb7u14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `bigbrother`
--
CREATE DATABASE `bigbrother` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `bigbrother`;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE IF NOT EXISTS `requests` (
  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `latitude` varchar(25) NOT NULL,
  `longitude` varchar(25) NOT NULL,
  `accuracy` int(11) NOT NULL,
  `sid` bigint(20) unsigned NOT NULL,
  `rdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rip` varchar(25) NOT NULL,
  `battery` int(10) unsigned NOT NULL,
  `charging` tinyint(1) NOT NULL,
  `provider` varchar(25) NOT NULL,
  `bearing` int(11) NOT NULL DEFAULT '-1',
  `speed` int(11) NOT NULL DEFAULT '-1',
  `time` datetime NOT NULL,
  `deviceid` varchar(25) NOT NULL,
  `subscriberid` varchar(25) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=462 ;

-- --------------------------------------------------------

--
-- Table structure for table `secrets`
--

CREATE TABLE IF NOT EXISTS `secrets` (
  `sid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sname` varchar(25) NOT NULL,
  `type` varchar(25) NOT NULL,
  `comment` varchar(50) NOT NULL,
  `avatar` blob NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(25) NOT NULL,
  `value` varchar(25) NOT NULL,
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uname` varchar(25) NOT NULL,
  `upassword` varchar(25) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

