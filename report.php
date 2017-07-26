<?php

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
include_once 'config.php';
define("VALUE", "0");
define("DATE", "1");

class ReportVO {

    private $delta = 0;
    private $consumptionDaily = 0;
    private $consumptionPeriod = 0;

    public function getDelta() {
        return $this->delta;
    }
    public function setDelta(int $diff) {
        return $this->delta=$diff;;
    }

}

/**
 * Description of Report
 *
 * @author mysli
 */
class Report {

    private $SQL_RECENT_2;
    private $dbh = NULL;
    private $vo;
    
    public function getReportVO():ReportVO{
        return $this->vo;
    }

    function openDB() {
        $this->dbh = new PDO('mysql:host=' . HOSTNAME . ';dbname=' . DATABASE, MYSQL_USER, MYSQL_PASS);
        return isset($this->dbh);
    }

    function init() {
        try {
            
        } catch (PDOException $e) {
            print "Error connecting Database!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    function createReport() {
        $recent2 = $this->dbh->query($this->SQL_RECENT_2);
        $c = $recent2->rowCount();
        $dif = 0;
        if ($recent2->rowCount() == 2) {
            foreach ($recent2 as $value) {
                $dif = $value[VALUE] - $dif;
            }
            $dif = -1 * $dif;
            error_log("$dif");
        }
        $this->vo->setDelta($dif);
    }

    public function __construct($dbh, $table) {
        $this->SQL_RECENT_2 = 'SELECT AbsoluteValue FROM consumption.' . $table . ' ORDER BY AbsoluteValue DESC  LIMIT 2';
        $this->vo = new ReportVO();
        if (isset($dbh)) {
            $this->dbh = $dbh;
        } else {
            openDB();
        }
    }

}
