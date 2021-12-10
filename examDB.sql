-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 01, 2021 at 08:52 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `examDB_ingimar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `user_id` int(255) DEFAULT NULL,
  `admin` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`user_id`, `admin`) VALUES
(120, 1),
(227, 1),
(228, 1);

-- --------------------------------------------------------

--
-- Table structure for table `authenticate`
--

CREATE TABLE `authenticate` (
  `email` varchar(255) DEFAULT NULL,
  `authToken` varchar(255) DEFAULT NULL,
  `tokenExpires` longtext NOT NULL,
  `emailVerified` int(11) NOT NULL DEFAULT 0,
  `selector` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `authenticate`
--

INSERT INTO `authenticate` (`email`, `authToken`, `tokenExpires`, `emailVerified`, `selector`) VALUES
('sindri@sindri.com', '$2y$10$oCmgK.MCouFYLu6N0KLyKuStGgJtFrrSJ7DW6wThP2sypiRRC9eyC', '1618852485', 0, '2f713f5ed72ba3fd'),
('ingimareys93@gmail.com', '$2y$10$8X7a2A63a/K9W68k4TInYOeLWQn.UBLLh2Fbcq5VHW6vBA4BO/K2i', '1620483214', 1, 'efe8460c249b15b0'),
('Tiago.33.abreu@gmail.com', '$2y$10$HYZuk.9EzEfNQ/d4EeMmke06a/Ybb074ytEaDLI6uw0v27Gm3aDCa', '1620578363', 1, 'e323a9699f9d9b18'),
('e.bazilyuk@gmail.com', '$2y$10$XYpm6C8/sZ6hl6SSm3DtqOOdUGsSa1gRtaZwToRzI2P6OSV/o8Zc2', '1620655015', 0, '251fd0b4db98d4f1'),
('ingimar@ingimar.dk', '$2y$10$u2AoOgHu3aF//vxaLM9pfOjwZIDNWdvzbVCjqpYSLFVdF6PCzIOui', '1622278297', 0, '6455e58de72bd687'),
('ingimar.web.dev@gmail.com', '$2y$10$8lNZ9Q36/urbIebEhqAqMesDIpHyCSZVTV8d6BFG/u23HRGEZSK7K', '1622535301', 1, '8c6865a1872db11c');

-- --------------------------------------------------------

--
-- Table structure for table `blocked`
--

CREATE TABLE `blocked` (
  `id` int(200) NOT NULL,
  `blocked_user_id` int(255) DEFAULT NULL,
  `user_blocked_by` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `blocked`
--

INSERT INTO `blocked` (`id`, `blocked_user_id`, `user_blocked_by`) VALUES
(1, 30, 120);

-- --------------------------------------------------------

--
-- Table structure for table `resetpassword`
--

CREATE TABLE `resetpassword` (
  `id` int(255) NOT NULL,
  `selector` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `expires` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `resetpassword`
--

INSERT INTO `resetpassword` (`id`, `selector`, `token`, `Email`, `expires`) VALUES
(26, '43502cc5a7feb217', '$2y$10$U8VJcjNtDJepwEDpUggj/ulfnw8fWj7MX81beWqhjDayWU7F6tmey', 'ingimareys93@gmail.com', '1621252704');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(255) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `age` int(5) NOT NULL,
  `active` int(1) NOT NULL,
  `phone` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `first_name`, `last_name`, `email`, `password`, `age`, `active`, `phone`) VALUES
(30, 'inboardparrel', 'Johanna', 'Somehting', 'johanna@johanna.dk', '$2y$10$7K2/H6poV9WCatvz1phAs.6zyXAC0CTPk2Ars2.7bmNIPD1iP/cJK', 25, 1, ''),
(83, 'irischeery', 'Sindri', 'Sindri', 'sindri@sindri.com', '$2y$10$AM2mhzEeQGThKTEdC9A4ouKk.gSHAKfvodkQuYp1I7P4coQXERJgm', 28, 0, ''),
(120, 'yellygolden', 'Ingimar', 'Eyfjord', 'ingimareys93@gmail.com', '$2y$10$ZXCQ9UF2JpLpeGHG2Jy6tuQV3PBQJMiDwUGTn.S/MaRXiM7b86wo6', 28, 1, '+4522397370'),
(121, 'infielderpager', 'Tiago', 'Abreu', 'Tiago.33.abreu@gmail.com', '$2y$10$sOEYHxIKxhd8blnePZ7lSu4jPvmqxAnZWSGbHZIiiQ0uVGqCMv8Ii', 28, 1, ''),
(223, 'Jelena123', 'Jelena', 'Baziluka', 'e.bazilyuk@gmail.com', '$2y$10$2ySj0pUc.LRlnTuVkf2Ql.sKGVx2FMwPimtIbKlKM1Pil/JndRV7i', 25, 1, ''),
(224, 'Supper_Pretty_girl', 'Johanne', 'Johanne', 'johannejpoulsen@gmail.com', '$2y$10$LK2HZNKArFx/JWOuFwR/i.BU5n6.vYwMYT6D/4.nhjrWRgpBU68XO', 25, 1, ''),
(227, 'SomeoneElse', 'Ingimar', 'Eyfjord', 'ingimar@ingimar.dk', '$2y$10$I7ukEj0BNjOITsuMycRh1OxPrBIl46q/GpOVpZ0Iyk.9IcZwGHm02', 28, 1, '+4522397370'),
(228, 'DEFADMIN', 'Default', 'Admin', 'ingimar.web.dev@gmail.com', '$2y$10$pJsZ1xxQ1fvXeI2UQikef.zr6DnI3cJdc88mMBhLH3bG6hO5VUo4q', 0, 1, '30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `authenticate`
--
ALTER TABLE `authenticate`
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `blocked`
--
ALTER TABLE `blocked`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blocked_user_id` (`blocked_user_id`),
  ADD KEY `user_blocked_by` (`user_blocked_by`);

--
-- Indexes for table `resetpassword`
--
ALTER TABLE `resetpassword`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blocked`
--
ALTER TABLE `blocked`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `resetpassword`
--
ALTER TABLE `resetpassword`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `authenticate`
--
ALTER TABLE `authenticate`
  ADD CONSTRAINT `email` FOREIGN KEY (`email`) REFERENCES `users` (`email`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `blocked`
--
ALTER TABLE `blocked`
  ADD CONSTRAINT `blocked_ibfk_1` FOREIGN KEY (`blocked_user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `blocked_ibfk_2` FOREIGN KEY (`user_blocked_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
