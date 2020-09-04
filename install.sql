SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `AD_ADMINS`;
CREATE TABLE IF NOT EXISTS `AD_ADMINS` (
  `login` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `ip` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `AD_ADMINS` (`login`, `password`, `ip`) VALUES
('admin', 'b6bf13d17e7abb25a9cf8e4eeeebd481', '127.0.0.1');

DROP TABLE IF EXISTS `AD_BUYERS`;
CREATE TABLE IF NOT EXISTS `AD_BUYERS` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `good` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `exp_time` int(11) NOT NULL,
  `cost` int(11) NOT NULL,
  `server` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `AD_CATEGORIES`;
CREATE TABLE IF NOT EXISTS `AD_CATEGORIES` (
  `id` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AD_CUPONS`;
CREATE TABLE IF NOT EXISTS `AD_CUPONS` (
  `name` varchar(50) DEFAULT NULL,
  `summ` int(11) DEFAULT NULL,
  `count` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `AD_GOODS`;
CREATE TABLE IF NOT EXISTS `AD_GOODS` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL DEFAULT '0',
  `cost` int(11) NOT NULL DEFAULT '0',
  `img` text NOT NULL,
  `description` text NOT NULL,
  `cupon` int(11) NOT NULL DEFAULT '0',
  `count` varchar(30) NOT NULL DEFAULT '-',
  `server` varchar(3) NOT NULL DEFAULT '*',
  `commands` text NOT NULL,
  `cat` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `dobuy` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `AD_PAYMENTS`;
CREATE TABLE IF NOT EXISTS `AD_PAYMENTS` (
  `id` int(11) NOT NULL COMMENT 'Payment ID',
  `username` varchar(50) NOT NULL COMMENT 'Username',
  `data` varchar(256) NOT NULL COMMENT 'Information',
  `time` int(11) NOT NULL COMMENT 'Date (Time)',
  `status` int(11) NOT NULL COMMENT 'Status',
  `stime` int(11) DEFAULT NULL COMMENT 'Server Time',
  `server` int(11) NOT NULL,
  `log` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `AD_ADMINS`
  ADD UNIQUE KEY `login` (`login`);

ALTER TABLE `AD_BUYERS`
  ADD KEY `id` (`id`);

ALTER TABLE `AD_CATEGORIES`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `AD_CUPONS`
  ADD UNIQUE KEY `name` (`name`);

ALTER TABLE `AD_GOODS`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `1` (`id`);

ALTER TABLE `AD_PAYMENTS`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `AD_BUYERS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `AD_CATEGORIES`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `AD_GOODS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `AD_PAYMENTS`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Payment ID';
  
--
-- Structure `AD_PAGES`
--

CREATE TABLE `AD_PAGES` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `title` varchar(32) NOT NULL,
  `text` text NOT NULL,
  `adv` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Pages Dump `AD_PAGES`
--

INSERT INTO `AD_PAGES` (`id`, `name`, `title`, `text`, `adv`) VALUES
(1, 'thanks', 'Thanks for buying!', '<p style=\"text-align:center\"><span style=\"font-size:14px\"><span style=\"font-family:Tahoma,Geneva,sans-serif\">Thanks for buying my script!</span></span></p>', 0);

INSERT INTO `AD_GOODS` (`id`, `name`, `cost`, `img`, `description`, `cupon`, `count`, `server`, `commands`, `cat`, `priority`, `dobuy`) VALUES
(1, 'Test Item', 10, 'https://i.imgur.com/b3kjhxD.png', '&lt;p&gt;Change description in adminpanel&lt;/p&gt;\r\n', 0, '-', '*', '[\"addrank {user}\"]', 1, 0, 0);

INSERT INTO `AD_CATEGORIES` (`id`, `title`, `priority`) VALUES
(1, 'Category', 1);
--
-- Indexes
--

--
-- Index `AD_PAGES`
--
ALTER TABLE `AD_PAGES`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `id_3` (`id`),
  ADD KEY `id_2` (`id`);

--
-- AUTO_INCREMENT
--

--
-- AUTO_INCREMENT for `AD_PAGES`
--
ALTER TABLE `AD_PAGES`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
