-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Feb 11. 11:10
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `flavorwave`
--
CREATE DATABASE IF NOT EXISTS `flavorwave` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `flavorwave`;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `etel`
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
-- A tábla adatainak kiíratása `etel`
--

INSERT INTO `etel` (`id`, `nev`, `egyseg_ar`, `leiras`, `kategoria_id`, `kep_url`, `kaloria`, `osszetevok`, `allergenek`) VALUES
(8, 'Margaréta pizza', 2000, 'Klasszikus margaréta pizza paradicsomszósszal és sajttal', 4, 'margherita.jpg', 800, 'Paradicsomszósz, sajt, pizzatészta', 'Glutén, tej'),
(9, 'Sonkás pizza', 2300, 'Sonkával és sajttal gazdagított pizza', 4, 'sonkaspizza.jpg', 900, 'Paradicsomszósz, sajt, sonka, pizzatészta', 'Glutén, tej'),
(10, 'Szalámis pizza', 2400, 'Pikáns szalámis pizza', 4, 'szalamispizza.jpg', 950, 'Paradicsomszósz, sajt, szalámi, pizzatészta', 'Glutén, tej'),
(11, 'Baconos pizza', 2500, 'Baconos és sajtos pizza', 4, 'baconospizza.jpg', 1000, 'Paradicsomszósz, sajt, bacon, pizzatészta', 'Glutén, tej'),
(12, 'Hawaii pizza', 2600, 'Ananászos-sonkás pizza', 4, 'hawaiipizza.jpg', 850, 'Paradicsomszósz, sajt, sonka, ananász, pizzatészta', 'Glutén, tej'),
(13, 'Gombás pizza', 2200, 'Friss gombával és sajttal készített pizza', 4, 'gombaspizza.jpg', 900, 'Paradicsomszósz, sajt, gomba, pizzatészta', 'Glutén, tej'),
(14, 'Coca-Cola', 500, 'Klasszikus kóla üdítőital', 9, '', 140, 'Szénsavas üdítőital', ''),
(15, 'Fanta', 500, 'Narancs ízű üdítőital', 9, '', 150, 'Szénsavas üdítőital', ''),
(16, 'Sprite', 500, 'Citrom-lime ízű üdítőital', 9, '', 130, 'Szénsavas üdítőital', ''),
(17, 'Cappy', 600, 'Gyümölcslevek különböző ízekben', 9, '', 100, 'Gyümölcslé', ''),
(18, 'Kinley', 500, 'Tonic üdítő', 9, '', 80, 'Szénsavas üdítőital', ''),
(19, '7UP', 500, 'Citrom-lime ízű üdítőital', 9, '', 130, 'Szénsavas üdítőital', ''),
(20, 'Nestea', 600, 'Jeges tea különböző ízekben', 9, '', 70, 'Jeges tea', ''),
(21, 'Kávék', 800, 'Különféle kávék', 9, '', 50, 'Kávé', ''),
(22, 'Csokis shake', 900, 'Csokoládés ízű shake', 8, 'csokisshake.jpeg', 300, 'Tej, csokoládé, cukor', 'Tej'),
(23, 'Vaníliás shake', 900, 'Vanília ízű shake', 8, 'vaniliasshake.jpeg', 290, 'Tej, vanília, cukor', 'Tej'),
(24, 'Epres shake', 900, 'Eper ízű shake', 8, 'epresshake.jpeg', 280, 'Tej, eper, cukor', 'Tej'),
(25, 'Áfonyás shake', 900, 'Áfonya ízű shake', 8, 'afonyasshake.jpeg', 280, 'Tej, áfonya, cukor', 'Tej'),
(26, 'Karamellás shake', 950, 'Karamellás ízű shake', 8, 'karamellasshake.jpeg', 320, 'Tej, karamell, cukor', 'Tej'),
(27, 'Hasáb burgonya', 600, 'Ropogós sült burgonya', 6, '', 300, 'Burgonya, olaj, só', ''),
(28, 'Steak burgonya', 700, 'Szeletelt burgonya steakfűszerezéssel', 6, '', 350, 'Burgonya, olaj, fűszerek', ''),
(29, 'Crinkle-cut burgonya', 650, 'Hullámosra vágott sült burgonya', 6, '', 320, 'Burgonya, olaj, só', ''),
(30, 'Waffle fries burgonya', 750, 'Rácsosra vágott sült burgonya', 6, '', 340, 'Burgonya, olaj, só', ''),
(31, 'Sima hotdog', 800, 'Egyszerű hotdog mustárral és ketchuppal', 5, '', 400, 'Virslis kifli, mustár, ketchup', 'Glutén'),
(32, 'Fűszeres hotdog', 900, 'Fűszeres hotdog csípős szósszal', 5, '', 450, 'Virslis kifli, csípős szósz, hagyma', 'Glutén'),
(33, 'Chilis hotdog', 950, 'Hotdog chili szósszal és jalapeñóval', 5, '', 500, 'Virslis kifli, chili szósz, jalapeño', 'Glutén'),
(34, 'Fánk', 500, 'Friss, porcukros fánk', 7, '', 300, 'Liszt, cukor, olaj', 'Glutén'),
(35, 'Brownie', 700, 'Csokoládés brownie', 7, 'brownie.jpeg', 350, 'Csokoládé, liszt, cukor', 'Glutén, tej'),
(36, 'Almáspite', 600, 'Házi almáspite', 7, 'almaspite.jpeg', 320, 'Liszt, alma, cukor', 'Glutén'),
(37, 'Isler', 650, 'Csokoládéval bevont omlós sütemény', 7, '', 340, 'Liszt, csokoládé, cukor', 'Glutén, tej'),
(38, 'Sajtburger', 1500, 'Klasszikus sajtburger sajttal és zöldségekkel', 3, '', 500, 'Zsemle, marhahús, sajt, saláta, paradicsom, szószok', 'Glutén, tej'),
(39, 'Dupla sajtburger', 2000, 'Dupla húspogácsás sajtburger', 3, '', 750, 'Zsemle, marhahús, sajt, saláta, paradicsom, szószok', 'Glutén, tej'),
(40, 'Dupla húsos burger', 2200, 'Extra húsos burger dupla húspogácsával', 3, '', 800, 'Zsemle, marhahús, saláta, paradicsom, szószok', 'Glutén'),
(41, 'Csirke burger', 1800, 'Ropogós csirkés burger', 3, '', 600, 'Zsemle, csirkehús, saláta, paradicsom, szószok', 'Glutén'),
(42, 'Baconos burger', 2000, 'Baconos és sajtos burger', 3, '', 700, 'Zsemle, marhahús, bacon, sajt, saláta, paradicsom, szószok', 'Glutén, tej'),
(43, 'Standard burger', 1400, 'Egyszerű burger húspogácsával és zöldségekkel', 3, '', 450, 'Zsemle, marhahús, saláta, paradicsom, szószok', 'Glutén'),
(69, '1', 21, '0', 7, 'freepik__candid-image-photography-natural-textures-highly-r__7922.jpeg', 30, 'asd', 'asd');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `extra_feltet`
--

CREATE TABLE `extra_feltet` (
  `id` int(11) NOT NULL,
  `feltet_nev` varchar(20) NOT NULL,
  `feltet_ar` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `felhasznalo`
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
-- A tábla adatainak kiíratása `felhasznalo`
--

INSERT INTO `felhasznalo` (`id`, `felhasznalo_nev`, `email_cim`, `jelszo`, `tel_szam`, `lakcim`, `jog_szint`) VALUES
(4, 'main', '13c-borondi@ipari.vein.hu', '$2y$10$61bO5xSX5AjCzX7o1CIEFeaR3MD7gDpI2E.WqB/IB6wKZlAKVfbC.', '+36203353526', '', 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feltet_opcio`
--

CREATE TABLE `feltet_opcio` (
  `id` int(11) NOT NULL,
  `feltet_id` int(11) NOT NULL,
  `tetel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kategoria`
--

CREATE TABLE `kategoria` (
  `id` int(11) NOT NULL,
  `kategoria_nev` varchar(20) NOT NULL,
  `kep_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `kategoria`
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
-- Tábla szerkezet ehhez a táblához `megrendeles`
--

CREATE TABLE `megrendeles` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `leadas_megjegyzes` varchar(250) NOT NULL,
  `kezbesites` varchar(50) NOT NULL,
  `leadas_allapota` tinyint(4) NOT NULL,
  `leadasdatuma` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `rendeles_tetel`
--

CREATE TABLE `rendeles_tetel` (
  `id` int(11) NOT NULL,
  `rendeles_id` int(11) NOT NULL,
  `termek_id` int(11) NOT NULL,
  `mennyiseg` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `tetelek`
--

CREATE TABLE `tetelek` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `etel_id` int(11) NOT NULL,
  `darab` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `valaszthato_feltet`
--

CREATE TABLE `valaszthato_feltet` (
  `id` int(11) NOT NULL,
  `feltet_id` int(11) NOT NULL,
  `etel_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `velemenyek`
--

CREATE TABLE `velemenyek` (
  `id` int(11) NOT NULL,
  `felhasznalo_id` int(11) NOT NULL,
  `velemeny_szoveg` varchar(250) NOT NULL,
  `ertekeles` int(5) NOT NULL,
  `email_cim` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- A tábla adatainak kiíratása `velemenyek`
--

INSERT INTO `velemenyek` (`id`, `felhasznalo_id`, `velemeny_szoveg`, `ertekeles`, `email_cim`) VALUES
(31, 4, 'asdff', 4, '13c-borondi@ipari.vein.hu');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `etel`
--
ALTER TABLE `etel`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nev` (`nev`),
  ADD KEY `kategoria_id` (`kategoria_id`);

--
-- A tábla indexei `extra_feltet`
--
ALTER TABLE `extra_feltet`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `felhasznalo`
--
ALTER TABLE `felhasznalo`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `feltet_opcio`
--
ALTER TABLE `feltet_opcio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feltet_id` (`feltet_id`,`tetel_id`),
  ADD KEY `tetel_id` (`tetel_id`);

--
-- A tábla indexei `kategoria`
--
ALTER TABLE `kategoria`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `megrendeles`
--
ALTER TABLE `megrendeles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- A tábla indexei `rendeles_tetel`
--
ALTER TABLE `rendeles_tetel`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendeles_id` (`rendeles_id`,`termek_id`),
  ADD KEY `termek_id` (`termek_id`);

--
-- A tábla indexei `tetelek`
--
ALTER TABLE `tetelek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rendeles_id` (`etel_id`),
  ADD KEY `etel_id` (`etel_id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- A tábla indexei `valaszthato_feltet`
--
ALTER TABLE `valaszthato_feltet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feltet_id` (`feltet_id`,`etel_id`),
  ADD KEY `etel_id` (`etel_id`);

--
-- A tábla indexei `velemenyek`
--
ALTER TABLE `velemenyek`
  ADD PRIMARY KEY (`id`),
  ADD KEY `felhasznalo_id` (`felhasznalo_id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `etel`
--
ALTER TABLE `etel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT a táblához `extra_feltet`
--
ALTER TABLE `extra_feltet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `felhasznalo`
--
ALTER TABLE `felhasznalo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `feltet_opcio`
--
ALTER TABLE `feltet_opcio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `kategoria`
--
ALTER TABLE `kategoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT a táblához `megrendeles`
--
ALTER TABLE `megrendeles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `rendeles_tetel`
--
ALTER TABLE `rendeles_tetel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `tetelek`
--
ALTER TABLE `tetelek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `valaszthato_feltet`
--
ALTER TABLE `valaszthato_feltet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `velemenyek`
--
ALTER TABLE `velemenyek`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `etel`
--
ALTER TABLE `etel`
  ADD CONSTRAINT `etel_ibfk_1` FOREIGN KEY (`kategoria_id`) REFERENCES `kategoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `feltet_opcio`
--
ALTER TABLE `feltet_opcio`
  ADD CONSTRAINT `feltet_opcio_ibfk_1` FOREIGN KEY (`feltet_id`) REFERENCES `extra_feltet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feltet_opcio_ibfk_2` FOREIGN KEY (`tetel_id`) REFERENCES `tetelek` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `megrendeles`
--
ALTER TABLE `megrendeles`
  ADD CONSTRAINT `megrendeles_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `rendeles_tetel`
--
ALTER TABLE `rendeles_tetel`
  ADD CONSTRAINT `rendeles_tetel_ibfk_1` FOREIGN KEY (`rendeles_id`) REFERENCES `megrendeles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rendeles_tetel_ibfk_2` FOREIGN KEY (`termek_id`) REFERENCES `etel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `tetelek`
--
ALTER TABLE `tetelek`
  ADD CONSTRAINT `tetelek_ibfk_1` FOREIGN KEY (`etel_id`) REFERENCES `etel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `valaszthato_feltet`
--
ALTER TABLE `valaszthato_feltet`
  ADD CONSTRAINT `valaszthato_feltet_ibfk_1` FOREIGN KEY (`etel_id`) REFERENCES `etel` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `valaszthato_feltet_ibfk_2` FOREIGN KEY (`feltet_id`) REFERENCES `extra_feltet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Megkötések a táblához `velemenyek`
--
ALTER TABLE `velemenyek`
  ADD CONSTRAINT `velemenyek_ibfk_1` FOREIGN KEY (`felhasznalo_id`) REFERENCES `felhasznalo` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
