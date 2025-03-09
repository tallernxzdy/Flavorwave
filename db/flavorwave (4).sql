-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2025 at 09:50 AM
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
-- Database: `flavorwave`
--
CREATE DATABASE IF NOT EXISTS `flavorwave` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `flavorwave`;

-- --------------------------------------------------------

--
-- Table structure for table `etel`
--

CREATE TABLE `etel` (
  `id` int(11) NOT NULL,
  `nev` varchar(50) NOT NULL,
  `egyseg_ar` int(10) NOT NULL,
  `leiras` varchar(250) NOT NULL,
  `kategoria_id` int(11) NOT NULL,
  `kep_url` varchar(255) NOT NULL,
  `kaloria` int(4) NOT NULL,
  `osszetevok` varchar(255) NOT NULL,
  `allergenek` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `etel`
--

INSERT INTO `etel` (`id`, `nev`, `egyseg_ar`, `leiras`, `kategoria_id`, `kep_url`, `kaloria`, `osszetevok`, `allergenek`) VALUES
(8, 'Margaréta pizza', 2000, 'Klasszikus margaréta pizza paradicsomszósszal és sajttal', 4, 'margherita.jpg', 800, 'Paradicsomszósz, sajt, pizzatészta', 'Glutén, tej'),
(9, 'Sonkás pizza', 2300, 'Sonkával és sajttal gazdagított pizza', 4, 'sonkaspizza.jpg', 900, 'Paradicsomszósz, sajt, sonka, pizzatészta', 'Glutén, tej'),
(10, 'Szalámis pizza', 2400, 'Pikáns szalámis pizza', 4, 'szalamispizza.jpg', 950, 'Paradicsomszósz, sajt, szalámi, pizzatészta', 'Glutén, tej'),
(11, 'Baconos pizza', 2500, 'Baconos és sajtos pizza', 4, 'baconospizza.jpg', 1000, 'Paradicsomszósz, sajt, bacon, pizzatészta', 'Glutén, tej'),
(12, 'Hawaii pizza', 2600, 'Ananászos-sonkás pizza', 4, 'hawaiipizza.jpg', 850, 'Paradicsomszósz, sajt, sonka, ananász, pizzatészta', 'Glutén, tej'),
(13, 'Gombás pizza', 2200, 'Friss gombával és sajttal készített pizza', 4, 'gombaspizza.jpg', 900, 'Paradicsomszósz, sajt, gomba, pizzatészta', 'Glutén, tej'),
(14, 'Coca-Cola', 500, 'Klasszikus kóla üdítőital', 9, 'cola.jpg', 140, 'Szénsavas üdítőital', ''),
(15, 'Fanta', 500, 'Narancs ízű üdítőital', 9, 'fanta.jpg', 150, 'Szénsavas üdítőital', ''),
(16, 'Sprite', 500, 'Citrom-lime ízű üdítőital', 9, 'sprite.jpg', 130, 'Szénsavas üdítőital', ''),
(17, 'Cappy', 600, 'Gyümölcslevek különböző ízekben', 9, 'cappy.jpg', 100, 'Gyümölcslé', ''),
(18, 'Kinley', 500, 'Tonic üdítő', 9, 'kinley.jpg', 80, 'Szénsavas üdítőital', ''),
(19, '7UP', 500, 'Citrom-lime ízű üdítőital', 9, 'sevenup.jpg', 130, 'Szénsavas üdítőital', ''),
(20, 'Nestea', 600, 'Jeges tea különböző ízekben', 9, 'nestea.jpg', 70, 'Jeges tea', ''),
(21, 'Kávék', 800, 'Különféle kávék', 9, '', 50, 'Kávé', ''),
(22, 'Csokis shake', 900, 'Csokoládés ízű shake', 8, 'csokisshake.jpg', 300, 'Tej, csokoládé, cukor', 'Tej'),
(23, 'Vaníliás shake', 900, 'Vanília ízű shake', 8, 'vaniliasshake.jpg', 290, 'Tej, vanília, cukor', 'Tej'),
(24, 'Epres shake', 900, 'Eper ízű shake', 8, 'epresshake.jpg', 280, 'Tej, eper, cukor', 'Tej'),
(25, 'Áfonyás shake', 900, 'Áfonya ízű shake', 8, 'afonyasshake.jpg', 280, 'Tej, áfonya, cukor', 'Tej'),
(26, 'Karamellás shake', 950, 'Karamellás ízű shake', 8, 'karamellasshake.jpg', 320, 'Tej, karamell, cukor', 'Tej'),
(27, 'Hasáb burgonya', 600, 'Ropogós sült burgonya', 6, 'hasabburgonya.jpg', 300, 'Burgonya, olaj, só', ''),
(28, 'Steak burgonya', 700, 'Szeletelt burgonya steakfűszerezéssel', 6, 'steakburgonya.jpg', 350, 'Burgonya, olaj, fűszerek', ''),
(29, 'Crinkle-cut burgonya', 650, 'Hullámosra vágott sült burgonya', 6, 'crinklecutfries.jpg', 320, 'Burgonya, olaj, só', ''),
(30, 'Waffle fries burgonya', 750, 'Rácsosra vágott sült burgonya', 6, 'wafflefries.jpg', 340, 'Burgonya, olaj, só', ''),
(31, 'Sima hotdog', 800, 'Egyszerű hotdog mustárral és ketchuppal', 5, 'simahotdog.jpg', 400, 'Virslis kifli, mustár, ketchup', 'Glutén'),
(32, 'Fűszeres hotdog', 900, 'Fűszeres hotdog csípős szósszal', 5, 'fuszereshotdog.jpg', 450, 'Virslis kifli, csípős szósz, hagyma', 'Glutén'),
(33, 'Chilis hotdog', 950, 'Hotdog chili szósszal és jalapeñóval', 5, 'chilishotdog.jpg', 500, 'Virslis kifli, chili szósz, jalapeño', 'Glutén'),
(34, 'Fánk', 500, 'Friss, csokoládés fánk', 7, 'fank.jpeg', 300, 'Liszt, cukor, olaj', 'Glutén'),
(35, 'Brownie', 700, 'Csokoládés brownie', 7, 'brownie.jpeg', 350, 'Csokoládé, liszt, cukor', 'Glutén, tej'),
(36, 'Almáspite', 600, 'Házi almáspite', 7, 'almaspite.jpeg', 320, 'Liszt, alma, cukor', 'Glutén'),
(37, 'csokis keksz', 650, 'csokoládé darabos vaníliás keksz', 7, 'csokiskeksz.jpg', 340, 'Liszt, csokoládé, cukor', 'Glutén, tej'),
(38, 'Sajtburger', 1500, 'Klasszikus sajtburger sajttal és zöldségekkel', 3, 'sajtburger.jpg', 500, 'Zsemle, marhahús, sajt, saláta, paradicsom, szószok', 'Glutén, tej'),
(39, 'Dupla sajtburger', 2000, 'Dupla húspogácsás sajtburger', 3, 'duplasajtburger.jpg', 750, 'Zsemle, marhahús, sajt, saláta, paradicsom, szószok', 'Glutén, tej'),
(40, 'Dupla húsos burger', 2200, 'Extra húsos burger dupla húspogácsával', 3, 'duplahusosburger.jpg', 800, 'Zsemle, marhahús, saláta, paradicsom, szószok', 'Glutén'),
(41, 'Csirke burger', 1800, 'Ropogós csirkés burger', 3, 'csirkeburger.jpg', 600, 'Zsemle, csirkehús, saláta, paradicsom, szószok', 'Glutén'),
(42, 'Baconos burger', 2000, 'Baconos és sajtos burger', 3, 'baconburger.jpg', 700, 'Zsemle, marhahús, bacon, sajt, saláta, paradicsom, szószok', 'Glutén, tej'),
(43, 'Standard burger', 1400, 'Egyszerű burger húspogácsával és zöldségekkel', 3, 'standardburger.jpg', 450, 'Zsemle, marhahús, saláta, paradicsom, szószok', 'Glutén'),
(72, '1asde', 30, '0', 7, 'afonyasshake.jpeg', 300, 'aer', 'bbb'),
(73, 'hosszukave', 450, 'A kávé hosszan tartó élménye! Enyhén lágyabb, mégis telt ízvilág, amely tökéletes választás, ha egy hosszabb kávészünetre vágysz.\nÖsszetevők: Espresso, forró víz', 9, 'hosszukave.jpg', 200, 'Espresso, forró víz', 'Nincs ismert allergén'),
(74, 'Espresso', 400, 'Intenzív, karakteres és igazán olasz! Egyetlen korty, amely felébreszti az érzékeket, és energiával tölti fel a nap bármely szakában.', 9, 'espresso.jpg', 100, '100% arabica kávé', 'Nincs ismert allergén'),
(75, 'Tejeskávé', 500, 'A selymesen lágy tejeskávé tökéletes társ a lassú reggelekhez vagy egy kellemes délutáni beszélgetéshez. Lágy tejhab és gazdag kávé egyensúlya minden kortyban.\r\nÖsszetevők: Espresso, gőzölt tej, tejhab', 9, 'tejeskave.jpg', 150, 'Espresso, gőzölt tej, tejhab', 'Tej (laktóz)');

-- --------------------------------------------------------

--
-- Table structure for table `extra_feltet`
--

CREATE TABLE `extra_feltet` (
  `id` int(11) NOT NULL,
  `feltet_nev` varchar(20) NOT NULL,
  `feltet_ar` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `felhasznalo`
--

CREATE TABLE `felhasznalo` (
  `id` int(11) NOT NULL,
  `felhasznalo_nev` varchar(30) NOT NULL,
  `email_cim` varchar(100) NOT NULL,
  `jelszo` varchar(255) NOT NULL,
  `tel_szam` varchar(12) NOT NULL,
  `lakcim` varchar(50) NOT NULL,
  `jog_szint` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `felhasznalo`
--

INSERT INTO `felhasznalo` (`id`, `felhasznalo_nev`, `email_cim`, `jelszo`, `tel_szam`, `lakcim`, `jog_szint`) VALUES
(4, 'main', '13c-borondi@ipari.vein.hu', '$2y$10$61bO5xSX5AjCzX7o1CIEFeaR3MD7gDpI2E.WqB/IB6wKZlAKVfbC.', '06201234568', 'Herend utca 20', 1);

-- --------------------------------------------------------

--
-- Table structure for table `feltet_opcio`
--

CREATE TABLE `feltet_opcio` (
  `id` int(11) NOT NULL,
  `feltet_id` int(11) NOT NULL,
  `tetel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategoria`
--

CREATE TABLE `kategoria` (
  `id` int(11) NOT NULL,
  `kategoria_nev` varchar(20) NOT NULL,
  `kep_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategoria`
--

INSERT INTO `kategoria` (`id`, `kategoria_nev`, `kep_url`) VALUES
(3, 'Hamburgerek', ''),
(4, 'Pizzak', ''),
(5, 'Hot-dogok', ''),
(6, 'Köretek', ''),
(7, 'Desszertek', ''),
(8, 'Shakek', ''),
(9, 'Italok', '');

-- --------------------------------------------------------

--
-- Table structure for table `megrendeles`
--

CREATE TABLE `megrendeles` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `leadas_megjegyzes` varchar(250) NOT NULL,
  `kezbesites` varchar(50) NOT NULL,
  `leadas_allapota` tinyint(4) NOT NULL,
  `leadasdatuma` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `megrendeles`
--

INSERT INTO `megrendeles` (`id`, `felhasznalo_id`, `leadas_megjegyzes`, `kezbesites`, `leadas_allapota`, `leadasdatuma`) VALUES
(10, 4, 'bbb', 'házhozszállítás', 2, '2025-02-25');

-- --------------------------------------------------------

--
-- Table structure for table `rendeles_tetel`
--

CREATE TABLE `rendeles_tetel` (
  `id` int(11) NOT NULL,
  `rendeles_id` int(11) NOT NULL,
  `termek_id` int(11) NOT NULL,
  `mennyiseg` int(11) NOT NULL,
  `statusz` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tetelek`
--

CREATE TABLE `tetelek` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `etel_id` int(11) NOT NULL,
  `darab` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `valaszthato_feltet`
--

CREATE TABLE `valaszthato_feltet` (
  `id` int(11) NOT NULL,
  `feltet_id` int(11) NOT NULL,
  `etel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `velemenyek`
--

CREATE TABLE `velemenyek` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `velemeny_szoveg` varchar(250) NOT NULL,
  `ertekeles` int(5) NOT NULL,
  `email_cim` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `velemenyek`
--

INSERT INTO `velemenyek` (`id`, `felhasznalo_id`, `velemeny_szoveg`, `ertekeles`, `email_cim`) VALUES
(31, 4, 'asdff', 4, '13c-borondi@ipari.vein.hu');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `etel`
--
ALTER TABLE `etel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nev` (`nev`),
  ADD KEY `kategoria_id` (`kategoria_id`);

--
-- Indexes for table `extra_feltet`
--
ALTER TABLE `extra_feltet`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `felhasznalo`
--
ALTER TABLE `felhasznalo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feltet_opcio`
--
ALTER TABLE `feltet_opcio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feltet_id` (`feltet_id`,`tetel_id`),
  ADD KEY `tetel_id` (`tetel_id`);

--
-- Indexes for table `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `megrendeles`
--
ALTER TABLE `megrendeles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- Indexes for table `rendeles_tetel`
--
ALTER TABLE `rendeles_tetel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendeles_id` (`rendeles_id`,`termek_id`),
  ADD KEY `termek_id` (`termek_id`);

--
-- Indexes for table `tetelek`
--
ALTER TABLE `tetelek`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`felhasznalo_id`,`etel_id`),
  ADD KEY `rendeles_id` (`etel_id`),
  ADD KEY `etel_id` (`etel_id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- Indexes for table `valaszthato_feltet`
--
ALTER TABLE `valaszthato_feltet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feltet_id` (`feltet_id`,`etel_id`),
  ADD KEY `etel_id` (`etel_id`);

--
-- Indexes for table `velemenyek`
--
ALTER TABLE `velemenyek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `etel`
--
ALTER TABLE `etel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `extra_feltet`
--
ALTER TABLE `extra_feltet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `felhasznalo`
--
ALTER TABLE `felhasznalo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feltet_opcio`
--
ALTER TABLE `feltet_opcio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `megrendeles`
--
ALTER TABLE `megrendeles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rendeles_tetel`
--
ALTER TABLE `rendeles_tetel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tetelek`
--
ALTER TABLE `tetelek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `valaszthato_feltet`
--
ALTER TABLE `valaszthato_feltet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `velemenyek`
--
ALTER TABLE `velemenyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `etel`
--
ALTER TABLE `etel`
  ADD CONSTRAINT `etel_ibfk_1` FOREIGN KEY (`kategoria_id`) REFERENCES `kategoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feltet_opcio`
--
ALTER TABLE `feltet_opcio`
  ADD CONSTRAINT `feltet_opcio_ibfk_1` FOREIGN KEY (`feltet_id`) REFERENCES `extra_feltet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feltet_opcio_ibfk_2` FOREIGN KEY (`tetel_id`) REFERENCES `tetelek` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `megrendeles`
--
ALTER TABLE `megrendeles`
  ADD CONSTRAINT `megrendeles_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rendeles_tetel`
--
ALTER TABLE `rendeles_tetel`
  ADD CONSTRAINT `rendeles_tetel_ibfk_1` FOREIGN KEY (`rendeles_id`) REFERENCES `megrendeles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rendeles_tetel_ibfk_2` FOREIGN KEY (`termek_id`) REFERENCES `etel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tetelek`
--
ALTER TABLE `tetelek`
  ADD CONSTRAINT `tetelek_ibfk_1` FOREIGN KEY (`etel_id`) REFERENCES `etel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `valaszthato_feltet`
--
ALTER TABLE `valaszthato_feltet`
  ADD CONSTRAINT `valaszthato_feltet_ibfk_1` FOREIGN KEY (`etel_id`) REFERENCES `etel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `valaszthato_feltet_ibfk_2` FOREIGN KEY (`feltet_id`) REFERENCES `extra_feltet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `velemenyek`
--
ALTER TABLE `velemenyek`
  ADD CONSTRAINT `velemenyek_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
