SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

USE `smartsoft`;

INSERT INTO `user` (`ID`, `Username`, `Password`) VALUES
(2, 'fabian.neundorf', '$2y$10$tKzT0TFEHFtYut748dunCOO10Q/0OSMSvIlj7ce4B7.3eEQJbjhHC'),
(3, 'erika.musterfrau', NULL),
(4, 'root', NULL),
(5, 'harold.finch', NULL),
(6, 'john.reese', NULL),
(7, 'sameen.shaw', NULL),
(8, 'okay.schwierig', NULL),
(9, 'jean.picard', NULL),
(10, 'wriker', NULL),
(11, 'worf', NULL),
(82, 'deanna.troi', NULL);

INSERT INTO `employee` (`ID`, `Name`, `Administrator`) VALUES
(2, 'Fabian Neundorf', 1),
(3, 'Erika Magda Musterfrau', 0),
(4, 'Samantha Groves', 1),
(5, 'Harold Finch', 1),
(6, 'John Reese', 0),
(7, 'Sameen Shaw', 0);

INSERT INTO `customer` (`ID`, `CustomerNo`, `Tariff`, `Contact`) VALUES
(8, 'A123456789OS', 1, 2),
(9, 'NCC1701DPICA', 3, 3),
(10, 'NCC1701DWILL', 2, 3),
(11, 'NCC1701DWORF', 2, 6),
(82, 'NCC1701DTROI', 2, 4);

INSERT INTO `thread` (`ID`, `Customer`, `Subject`) VALUES
(2, 8, 'Erste Nachricht in SQL'),
(3, 8, 'Zweite Nachricht in SQL'),
(4, 10, 'Frau Troi ist böse zu mir'),
(5, 9, 'Make it so');

INSERT INTO `message` (`ID`, `Thread`, `Sender`, `Time`, `Text`) VALUES
(2, 2, NULL, '2021-11-18 00:21:12', 'Dies ist die Erste Nachricht von mir über SQL. Wird bestimmt ein Fehler geben!'),
(3, 3, NULL, '2021-11-18 00:21:58', 'Dies ist die Zweite Nachricht von mir über SQL. Wird bestimmt ein Fehler geben!'),
(4, 2, 3, '2021-11-18 00:56:59', 'Hat zwar einen Fehler gegeben, aber letztendlich doch funktioniert :-)'),
(5, 3, 3, '2021-11-18 10:24:10', 'Die zweite Antwort hier'),
(6, 3, 2, '2021-11-18 12:21:30', 'Ich bin nun ihr Ansprechpartner :-)'),
(7, 2, 2, '2021-11-20 14:21:49', 'Durch die Antwort hier, sollte dieser Thread nach oben rutschen.'),
(8, 2, 2, '2021-11-20 15:01:56', 'Eine weitere Antwort'),
(9, 2, 2, '2021-11-20 15:13:06', 'Was passiert <div>damit</div>?'),
(10, 2, 2, '2021-11-20 15:27:18', 'Und jetzt nochmal ein Versuch mit &lt;div&gt;sowas&lt;/div&gt;'),
(11, 2, 2, '2021-11-20 15:30:15', 'Ein letzter <br> versuch'),
(12, 2, 2, '2021-11-21 12:40:07', 'Die erste Antwort vom Handy 🤗'),
(13, 2, NULL, '2021-11-22 18:26:25', 'Ein test vom Kunden '),
(14, 4, NULL, '2021-11-23 09:29:46', 'Diese Deanna ärgert mich immer :-('),
(15, 5, NULL, '2021-11-27 14:11:39', 'Ja das ist der Spruch, denn ich immer gerne sage :-)');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
