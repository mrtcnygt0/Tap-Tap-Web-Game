-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 09 Haz 2024, 15:05:19
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `oyun_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin`
--

CREATE TABLE `admin` (
  `id` int(10) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'mert', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT '0',
  `score` int(11) DEFAULT 0,
  `user_ref` text DEFAULT NULL,
  `ref_sayisi` int(11) DEFAULT NULL,
  `where_ref` text NOT NULL DEFAULT '0',
  `bot_level` int(11) NOT NULL DEFAULT 0,
  `last_login` timestamp NOT NULL DEFAULT current_timestamp(),
  `task_davet` int(11) NOT NULL DEFAULT 0,
  `task_insta` int(11) NOT NULL DEFAULT 0,
  `task_twitter` int(11) NOT NULL DEFAULT 0,
  `task_spotify` int(11) NOT NULL DEFAULT 0,
  `energy` int(11) NOT NULL DEFAULT 5000,
  `energy_level` int(11) NOT NULL DEFAULT 0,
  `game` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `score`, `user_ref`, `ref_sayisi`, `where_ref`, `bot_level`, `last_login`, `task_davet`, `task_insta`, `task_twitter`, `task_spotify`, `energy`, `energy_level`, `game`) VALUES
(6, 'nisa5811@outlook.com', 'nisa1203', '8', 9372, 'NSAnsa2002', 0, 'YCvY3lZCNJ', 1, '2024-06-03 01:30:08', 0, 1, 1, 1, 100000, 0, 10),
(19, 'yusuf', '123', '7', 10540, 'HBWYqYvTRE', 0, 'YCvY3lZCNJ', 2, '2024-05-30 16:16:45', 0, 0, 0, 0, 100000, 0, 10),
(20, 'berk', '123', '7', 29243, 'B8B3WW908x', 0, 'YCvY3lZCNJ', 2, '2024-06-02 19:54:58', 0, 1, 0, 0, 100000, 0, 10),
(22, 'Fatihkrks54', '1234567890qQ.', '3', 206, 'NLPyL9rRj5', 0, 'YCvY3lZCNJ', 0, '2024-05-31 03:31:55', 0, 0, 0, 0, 100000, 0, 10),
(23, 'unmaruf', '123456', '7', 68262, 'JRpZpZacbs', 0, 'YCvY3lZCNJ', 2, '2024-06-01 17:51:06', 0, 1, 1, 1, 100000, 0, 10),
(25, 'beko', '123', '1', 3630, 'lVVbPyzW0r', 0, 'YCvY3lZCNJ', 0, '2024-06-02 15:52:55', 0, 1, 1, 1, 100000, 0, 10),
(29, 'raj1234', 'Tony@12345&', '0', 0, 'DlfVkTW0yl', 0, 'YCvY3lZCNJ', 0, '2024-06-02 04:05:31', 0, 0, 0, 0, 100000, 0, 10),
(30, 'halil', 'halil12399@@', '3', 2841, 't1zifQtp2V', 0, 'YCvY3lZCNJ', 0, '2024-06-02 14:40:07', 0, 1, 1, 1, 100000, 0, 10),
(31, 'mrtcnygt2', 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', '6', 37427, 'YCvY3lZCNJ', 8, '', 2, '2024-06-09 12:36:44', 1, 1, 1, 1, 1000, 0, 42),
(32, 'abc@def.com', '123456', '8', 35680, 'pzbkULTy45', 0, '', 1, '2024-06-02 21:39:36', 0, 0, 0, 0, 100000, 0, 10),
(33, 'deneme', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '0', 9, 'F3v2ke3hn6', 0, '', 0, '2024-06-03 21:50:57', 0, 0, 0, 0, 4991, 0, 10);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
