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
require_once ( 'report.php' );

/**
 * Description of Capturing
 *
 * @author mysli
 * @version 20170724
 */
abstract class Request {

    public static function get($name, $filter = FILTER_DEFAULT) {
        return filter_input(INPUT_GET, $name, $filter);
    }

    public static function getInt($name) {
        return self::get($name, FILTER_VALIDATE_INT);
    }

    public static function getFloat($name) {
        return self::get($name, FILTER_VALIDATE_FLOAT);
    }

    public static function post($name, $filter = FILTER_DEFAULT) {
        return filter_input(INPUT_POST, $name, $filter);
    }

    public static function postInt($name) {
        return self::post($name, FILTER_VALIDATE_INT);
    }

    public static function postFloat($name) {
        return self::post($name, FILTER_VALIDATE_FLOAT);
    }

}

class Record {

    public $sth = NULL;
    public $value = 0;
    public $delta = 0;
    public $absoluteValue = 0;
    public $captureDate;
    public $report;

}

class NumberValidator {

    private $NumberPattern = '.';

    public function __construct($NumberPattern) {
        $this->NumberPattern = $NumberPattern;
    }

//return 1 if toCheck is not NULLL, greater then 0 and matches the given Pattern.
    public function validate($toCheck) {
        return (isset($toCheck) && (preg_match($this->NumberPattern, $toCheck, $wert) == 1) && $toCheck > 0);
    }

}

class TextValidator {

    private $Pattern = '.';

    public function __construct($Pattern) {
        $this->Pattern = $Pattern;
    }

//return 1 if toCheck is not NULLL and matches the given Pattern.
    public function validate($toCheck) {
        return (isset($toCheck) && (preg_match($this->Pattern, $toCheck, $wert) == 1));
    }

}

class DateValidator{
    private $today;
    /**
     * check, if given date is equals or lower the current date.
     * @param type $toCheck
     * @return type
     */
    public function validate($toCheck) {
        $datediff=(new DateTime($this->today))->diff((new DateTime($toCheck)),false);
        error_log($datediff->days,0);
        return (isset($toCheck)&&($datediff->days<=0));
    }
       public function __construct($today) {
        $this->today = $today;
    }
    
}

class Capturing {

//The individual Regex pattern to check the Digits.
    const EPOWER_PATTERN = '/(^\d{1,7}$)/'; //numbers only, max.7
    const GAS_PATTERN = '/(^\d{1,5}$)/';
    const WATER_PATTERN = '/(^\d{1,7}$)/';
    const NOTE_PATTERN = '/([\w ?!ÄÖÜäöü,]{0,20})/'; //max 20chars and Space,?.!
    const SQL_INSERT_EPOWER = 'INSERT INTO consumption.epower (CaptureDate,Value,AbsoluteValue,Note) VALUES(?,?,?,?)';
    const SQL_INSERT_GAS = 'INSERT INTO consumption.gas (CaptureDate,Value,AbsoluteValue,Note) VALUES(?,?,?,?)';
    const SQL_INSERT_WATER = 'INSERT INTO consumption.water (CaptureDate,Value,AbsoluteValue,Note) VALUES(?,?,?,?)';
    const SQL_INIT_EPOWER = 'SELECT Value FROM consumption.epower WHERE AbsoluteValue= ?';
    const SQL_INIT_GAS = 'SELECT Value FROM consumption.gas WHERE AbsoluteValue= ?';
    const SQL_INIT_WATER = 'SELECT Value FROM consumption.water WHERE AbsoluteValue= ?';
    const SQL_INIT_EPOWERABS = 'SELECT MAX(AbsoluteValue) FROM consumption.epower';
    const SQL_INIT_GASABS = 'SELECT MAX(AbsoluteValue) FROM consumption.gas';
    const SQL_INIT_WATERABS = 'SELECT MAX(AbsoluteValue) FROM consumption.water';

    private $EPowerValidator;
    private $GasValidator;
    private $WaterValidator;
    private $NoteValidator;
    public $dbh = NULL;
    public $sthepower = NULL;
    public $sthepowerinit = NULL;
    public $epower = '0';
    public $sthgas = NULL;
    public $sthgasinit = NULL;
    public $gas = '0';
    public $sthwater = NULL;
    public $sthwaterinit = NULL;
    public $water = '0';
    public $today = "0";
    public $EPowerRecord = NULL;
    public $GasRecord = NULL;
    public $WaterRecord = NULL;
    public $EPowerReport;
    public $GasReport;
    public $WaterReport;
    private $dateValidator;

    function openDB() {
        $this->dbh = new PDO('mysql:host=' . HOSTNAME . ';dbname=' . DATABASE, MYSQL_USER, MYSQL_PASS);
        return isset($this->dbh);
    }

    function init() {
        try {
            if ($this->openDB()) {
                $this->sthepower = $this->dbh->prepare(Capturing::SQL_INSERT_EPOWER, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $this->sthgas = $this->dbh->prepare(Capturing::SQL_INSERT_GAS, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $this->sthwater = $this->dbh->prepare(Capturing::SQL_INSERT_WATER, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

                $this->sthepowerinit = $this->dbh->prepare(Capturing::SQL_INIT_EPOWER);
                $this->sthgasinit = $this->dbh->prepare(Capturing::SQL_INIT_GAS);
                $this->sthwaterinit = $this->dbh->prepare(Capturing::SQL_INIT_WATER);

                $this->initRecord();
            } else {
                print "Database not connected <br/>";
            }
        } catch (PDOException $e) {
            print "Error connecting Database!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    function initRecord() {

        try {
            $this->EPowerRecord->absoluteValue = ($this->dbh->query(Capturing::SQL_INIT_EPOWERABS)->fetch()[0]);
            $this->GasRecord->absoluteValue = ($this->dbh->query(Capturing::SQL_INIT_GASABS)->fetch()[0]);
            $this->WaterRecord->absoluteValue = ($this->dbh->query(Capturing::SQL_INIT_WATERABS)->fetch()[0]);

            if ($this->sthepowerinit->execute(array($this->EPowerRecord->absoluteValue))) {
                $this->EPowerRecord->value = $this->sthepowerinit->fetch()[0];
            }
            if (is_null($this->EPowerRecord->value)) {
                $this->EPowerRecord->value = 0;
            }

            if ($this->sthgasinit->execute(array($this->GasRecord->absoluteValue))) {
                $this->GasRecord->value = $this->sthgasinit->fetch()[0];
            }
            if (is_null($this->GasRecord->value)) {
                $this->GasRecord->value = 0;
            }

            if ($this->sthwaterinit->execute(array($this->WaterRecord->absoluteValue))) {
                $this->WaterRecord->value = $this->sthwaterinit->fetch()[0];
            }

            if (is_null($this->WaterRecord->value)) {
                $this->WaterRecord->value = 0;
            }
            $this->EPowerReport = new Report($this->dbh, 'epower');
            $this->GasReport = new Report($this->dbh, 'gas');
            $this->WaterReport = new Report($this->dbh, 'water');
            $this->EPowerRecord->report = $this->EPowerReport->getReportVO();
            $this->GasRecord->report = $this->GasReport->getReportVO();
            $this->WaterRecord->report = $this->WaterReport->getReportVO();
        } catch (Exception $e) {
            print "Error connecting Database!: " . $e->getMessage() . "<br/>";
        }
    }

    function readAbleseDatum($fieldName) {
        $datumwert = filter_input(INPUT_POST, $fieldName);
        $datzeit = $datumwert . ' ' . "23:59:00";
        $datzeit = date(DATEFORMAT, strtotime($datzeit));
        return $datzeit;
    }

    function getDelta($prevReading, $reading) {
        return $reading - $prevReading;
    }

    function trimNote($note) {

        if ($this->NoteValidator->validate($note)) {
            return $note;
        }
        return NULL;
    }

    /**
     * Insert a reading into the Database, if it is a number, greater then its previous value.
     * TODO: Handling of a counter exchange!!
     * @param type $captureDate
     * @param type $reading
     * @param type $note
     * @param type $record
     * @param type $sth
     */
    function writeData($captureDate, $reading, $note, $validator, $record, $sth) {
        $isvalid = $validator->validate($reading);
        if ($isvalid && $this->dateValidator->validate($captureDate)) {
            $record->delta = $this->getDelta($record->value, $reading);
            if ($record->delta > 0) {
                $record->value = $reading;
                $record->absoluteValue = $record->absoluteValue + $record->delta;
                $sth->execute(array($captureDate, $record->value, $record->absoluteValue, $this->trimNote($note)));
            }
        } else {
//print ("Invalid Input");
        }
        //print($reading);
    }

    function writeEPower() {
        $captureDate = $this->readAbleseDatum('eablesedatum');
        $reading = filter_input(INPUT_POST, 'epower');
        $note = filter_input(INPUT_POST, 'enote');
        $this->writeData($captureDate, $reading, $note, $this->EPowerValidator, $this->EPowerRecord, $this->sthepower);
    }

    function writeGas() {
        $captureDate = $this->readAbleseDatum('gasablesedatum');
        $reading = filter_input(INPUT_POST, 'gas');
        $note = filter_input(INPUT_POST, 'gasnote');
        $this->writeData($captureDate, $reading, $note, $this->GasValidator, $this->GasRecord, $this->sthgas);
    }

    function writeWater() {
        $captureDate = $this->readAbleseDatum('waterablesedatum');
        $reading = filter_input(INPUT_POST, 'water');
        $note = filter_input(INPUT_POST, 'waternote');

        $this->writeData($captureDate, $reading, $note, $this->WaterValidator, $this->WaterRecord, $this->sthwater);
    }

    function closeDB() {
        $this->dbh = null;
    }

    function __destruct() {
        $this->closeDB();
    }

    public function getEPowerRecord():Record {
        return $this->EPowerRecord;
    }

    public function getGasRecord():Record {
        return $this->GasRecord;
    }

    public function getWaterRecord():Record {
        return $this->WaterRecord;
    }

    function printInput() {
        try {
            $this->openDB();
            $this->writeEPower();
            $this->writeGas();
            $this->writeWater();
            $this->EPowerReport->createReport();
            $this->GasReport->createReport();
            $this->WaterReport->createReport();
            $this->closeDB();
        } catch (PDOException $e) {
            print "Error connecting Database!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    function __construct() {
        $this->today = date("d.m.Y");
        $this->EPowerRecord = new Record();
        $this->GasRecord = new Record();
        $this->WaterRecord = new Record();
        $this->EPowerValidator = new NumberValidator(self::EPOWER_PATTERN);
        $this->GasValidator = new NumberValidator(self::GAS_PATTERN);
        $this->WaterValidator = new NumberValidator(self::WATER_PATTERN);
        $this->NoteValidator = new TextValidator(self::NOTE_PATTERN);
        $this->dateValidator=new DateValidator($this->today);

        $this->init();
    }

}

?>