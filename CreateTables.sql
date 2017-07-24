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
Create the used Database structure and then grant access to the database tables with SELECT,INSERT,UPDATE privileges.
map these user and password into config.php MYSQL_USER and MYSQL_PASS.
 * Author:  mysli
 * Created: 24.07.2017
 */

CREATE DATABASE `consumption`;
CREATE TABLE `consumption`.`epower` ( `CaptureDate` DATE NULL DEFAULT NULL , `SubmitDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `Value` INT(9) NULL , `AbsoluteValue` INT(11) NOT NULL , `Note` TEXT NULL DEFAULT NULL ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_german2_ci;
CREATE TABLE `consumption`.`gas` ( `CaptureDate` DATE NULL DEFAULT NULL , `SubmitDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `Value` INT(9) NULL , `AbsoluteValue` INT(11) NOT NULL , `Note` TEXT NULL DEFAULT NULL ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_german2_ci;
CREATE TABLE `consumption`.`water` ( `CaptureDate` DATE NULL DEFAULT NULL , `SubmitDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `Value` INT(9) NULL , `AbsoluteValue` INT(11) NOT NULL , `Note` TEXT NULL DEFAULT NULL ) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_german2_ci;