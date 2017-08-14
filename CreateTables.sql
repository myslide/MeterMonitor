/* 
 * Copyright (C) 2017 mysli
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
Create the used Database structure and then grant access to the database tables with SELECT,INSERT,UPDATE,DELETE privileges.
map these user and password into parameters.yml  'database_user:' and 'database_password:'.
 * Author:  mysli
 * Created: 14.08.2017
 */

CREATE DATABASE `consumption`;
CREATE TABLE IF NOT EXISTS `epower` (
  `captureDate` date NOT NULL,
  `submitDate` datetime NOT NULL,
  `value` int(11) NOT NULL,
  `AbsoluteValue` int(11) NOT NULL,
  `note` varchar(255) COLLATE utf8_german2_ci DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8453C9F7342E0551` (`submitDate`),
  UNIQUE KEY `UNIQ_8453C9F75B39DB79` (`AbsoluteValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

CREATE TABLE IF NOT EXISTS `gas` (
  `captureDate` date NOT NULL,
  `submitDate` datetime NOT NULL,
  `value` int(11) NOT NULL,
  `AbsoluteValue` int(11) NOT NULL,
  `note` varchar(255) COLLATE utf8_german2_ci DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8453C9F7342E0551` (`submitDate`),
  UNIQUE KEY `UNIQ_8453C9F75B39DB79` (`AbsoluteValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;

CREATE TABLE IF NOT EXISTS `water` (
  `captureDate` date NOT NULL,
  `submitDate` datetime NOT NULL,
  `value` int(11) NOT NULL,
  `AbsoluteValue` int(11) NOT NULL,
  `note` varchar(255) COLLATE utf8_german2_ci DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8453C9F7342E0551` (`submitDate`),
  UNIQUE KEY `UNIQ_8453C9F75B39DB79` (`AbsoluteValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_german2_ci;
