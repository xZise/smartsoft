SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE `smartsoft` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `smartsoft`;

CREATE TABLE `customer` (
  `ID` int(11) NOT NULL,
  `CustomerNo` char(12) NOT NULL,
  `Tariff` int(11) NOT NULL,
  `Contact` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `employee` (
  `ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Administrator` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `message` (
  `ID` int(11) NOT NULL,
  `Thread` int(11) NOT NULL,
  `Sender` int(11) DEFAULT NULL,
  `Time` datetime NOT NULL DEFAULT current_timestamp(),
  `Text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tariff` (
  `ID` int(11) NOT NULL,
  `Name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `thread` (
  `ID` int(11) NOT NULL,
  `Customer` int(11) NOT NULL,
  `Subject` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user` (
  `ID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


ALTER TABLE `customer`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `CustomerNo` (`CustomerNo`),
  ADD KEY `Contact` (`Contact`),
  ADD KEY `Tariff` (`Tariff`);

ALTER TABLE `employee`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `message`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Sender` (`Sender`),
  ADD KEY `message_ibfk_1` (`Thread`);

ALTER TABLE `tariff`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `thread`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `thread_ibfk_1` (`Customer`);

ALTER TABLE `user`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Username` (`Username`);


ALTER TABLE `customer`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `employee`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `message`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tariff`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `thread`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `user`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`Contact`) REFERENCES `employee` (`ID`),
  ADD CONSTRAINT `customer_ibfk_2` FOREIGN KEY (`Tariff`) REFERENCES `tariff` (`ID`),
  ADD CONSTRAINT `customer_ibfk_3` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE CASCADE;

ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `user` (`ID`) ON DELETE CASCADE;

ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`Thread`) REFERENCES `thread` (`ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`Sender`) REFERENCES `employee` (`ID`);

ALTER TABLE `thread`
  ADD CONSTRAINT `thread_ibfk_1` FOREIGN KEY (`Customer`) REFERENCES `customer` (`ID`) ON DELETE CASCADE;

INSERT INTO `tariff` (`ID`, `Name`) VALUES
(1, 'Basis'),
(2, 'Medium'),
(3, 'Premium');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
