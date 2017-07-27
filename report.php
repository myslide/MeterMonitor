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
define("VALUE", "1");
define("DATE", "0");

class ReportVO {

    private $delta = 0;
    public $captureInterval = 0;
    public $consumptionThisYear = 0;
    public $consumptionperDay = 0.0;

    public function getDelta() {
        return $this->delta;
    }

    public function setDelta(int $diff) {
        return $this->delta = $diff;
        ;
    }

    public function getConsumptionThisYear() {
        return $this->consumptionThisYear;
    }

    public function getConsumptionPerDay(): float {
        return $this->consumptionperDay;
    }

    /**
     * The date difference btw. current and previous value.
     * @return type
     */
    public function getCaptureInterval(): int {
        return $this->captureInterval;
    }

}

/**
 * Description of Report
 *
 * @author mysli
 */
class Report {

    private $SQL_RECENT_2;
    private $SQL_YEAR;
    private $dbh = NULL;
    private $vo;

    public function getReportVO(): ReportVO {
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
        $dif = 0;
        $dateDiff = new DateInterval('P0D');
        if ($recent2->rowCount() == 2) {
            $row1 = $recent2->fetch();
            $row2 = $recent2->fetch();
            //if ($row1 != FALSE &&row2!=FALSE) {
            $dif = $row1[VALUE] - $row2[VALUE];

            $dateDiff = (new DateTime($row1[DATE]))->diff(new DateTime($row2[DATE]));
            //}
        }
        $this->getReportVO()->captureInterval = $dateDiff->days;
        $this->getReportVO()->setDelta($dif);
        $this->getReportVO()->consumptionperDay = $dif / ($dateDiff->days == 0 ? 1 : $dateDiff->days);
        //$this->getReportVO()->consumptionperDay=
    }

    public function __construct($dbh, $table) {
        $this->SQL_RECENT_2 = 'SELECT CaptureDate,AbsoluteValue FROM consumption.' . $table . ' ORDER BY CaptureDate DESC  LIMIT 2';
        $this->SQL_Year = 'SELECT CaptureDate,AbsoluteValue FROM consumption.' . $table . ' ORDER BY AbsoluteValue DESC';
        $this->vo = new ReportVO();
        if (isset($dbh)) {
            $this->dbh = $dbh;
        } else {
            openDB();
        }
    }

}
