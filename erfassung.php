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

/**
 * Description of erfassung
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

}

class Validator {

    private $NumberPattern = '.';

    public function __construct($NumberPattern) {
        $this->NumberPattern = $NumberPattern;
    }

    public function validate($toCheck) {
        return (isset($toCheck) && (preg_match($this->NumberPattern, $toCheck, $wert) == 1) && $toCheck > 0);
    }

}

class Erfassung {

//The individual Regex pattern to check the Digits.
    const EPOWER_PATTERN = '/(^\d{1,7}$)/'; //numbers only, max.7
    const GAS_PATTERN = '/(^\d{1,5}$)/';
    const WATER_PATTERN = '/(^\d{1,7}$)/';
    const NOTE_PATTERN = '/([\w ?!ÄÖÜäöü,]{0,20})/'; //max 20chars and Space,?.!
//    const USER = ;//'erfasser';
    const PASS = 'rolli2017';
    const SQL_INSERT_EPOWER = 'INSERT INTO epower (CaptureDate,Value,AbsoluteValue,Note) VALUES(?,?,?,?)';
    const SQL_INSERT_GAS = 'INSERT INTO gas (CaptureDate,Value,AbsoluteValue,Note) VALUES(?,?,?,?)';
    const SQL_INSERT_WATER = 'INSERT INTO water (CaptureDate,Value,AbsoluteValue,Note) VALUES(?,?,?,?)';
    const SQL_INIT_EPOWER = 'SELECT Value FROM epower WHERE AbsoluteValue= ?';
    const SQL_INIT_GAS = 'SELECT Value FROM gas WHERE AbsoluteValue= ?';
    const SQL_INIT_WATER = 'SELECT Value FROM water WHERE AbsoluteValue= ?';
    const SQL_INIT_EPOWERABS = 'SELECT MAX(AbsoluteValue) FROM epower';
    const SQL_INIT_GASABS = 'SELECT MAX(AbsoluteValue) FROM gas';
    const SQL_INIT_WATERABS = 'SELECT MAX(AbsoluteValue) FROM water';

    private $EPowerValidator;
    private $GasValidator;
    private $WaterValidator;
    private $NoteValidator;
    public $dbh = NULL;
    public $sthepower = NULL;
    public $sthepowerinit = NULL;
//public $epower = '0';
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

    function openDB() {
        $this->dbh = new PDO('mysql:host=localhost;dbname=verbrauchsdaten', MYSQL_USER, MYSQL_PASS);
        return isset($this->dbh);
    }

    function init() {
        try {
            if ($this->openDB()) {
                $this->sthepower = $this->dbh->prepare(Erfassung::SQL_INSERT_EPOWER, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $this->sthgas = $this->dbh->prepare(Erfassung::SQL_INSERT_GAS, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                $this->sthwater = $this->dbh->prepare(Erfassung::SQL_INSERT_WATER, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

                $this->sthepowerinit = $this->dbh->prepare(Erfassung::SQL_INIT_EPOWER);
                $this->sthgasinit = $this->dbh->prepare(Erfassung::SQL_INIT_GAS);
                $this->sthwaterinit = $this->dbh->prepare(Erfassung::SQL_INIT_WATER);

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
            $this->EPowerRecord->absoluteValue = ($this->dbh->query(Erfassung::SQL_INIT_EPOWERABS)->fetch()[0]);
            $this->GasRecord->absoluteValue = ($this->dbh->query(Erfassung::SQL_INIT_GASABS)->fetch()[0]);
            $this->WaterRecord->absoluteValue = ($this->dbh->query(Erfassung::SQL_INIT_WATERABS)->fetch()[0]);
            //($this->sthepowerinit->execute(array($this->EPowerRecord->absoluteValue)));
            //var_dump($this->EPowerRecord->absoluteValue);

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



            //  $this->EPowerRecord->value = ($this->dbh->query(Erfassung::SQL_INIT_EPOWER)->fetch()[0]);
            //  $this->GasRecord->value = ($this->dbh->query(Erfassung::SQL_INIT_GAS)->fetch()[0]);
            //  $this->WaterRecord->value = ($this->dbh->query(Erfassung::SQL_INIT_WATER)->fetch()[0]);
        } catch (Exception $e) {
            print "Error connecting Database!: " . $e->getMessage() . "<br/>";
        }
    }

    function readAbleseDatum($fieldName) {
        $datumwert = filter_input(INPUT_POST, $fieldName);
        $datzeit = $datumwert . ' ' . "23:59:00";
        $datzeit = date('Y-m-d H:i:s', strtotime($datzeit));
        return $datzeit;
    }

    function getDelta($prevReading, $reading) {
        return $reading - $prevReading;
    }

    function validateNote($note) {
        if (isset($note)) {
            return TRUE;
        }
        return FALSE;
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
        error_log("$reading",0);
        $isvalid = $validator->validate($reading);
        error_log("isvalid?"."$isvalid",0);
        if ($isvalid && isset($captureDate)) {
            $record->delta = $this->getDelta($record->value, $reading);
            error_log("delta="."$record->delta",0);
            if ($record->delta > 0) {
                $record->value = $reading;
                $record->absoluteValue = $record->absoluteValue + $record->delta;
                $sth->execute(array($captureDate, $record->value, $record->absoluteValue, $this->trimNote($note)));
                 error_log("$captureDate"." recordvalue="."$record->value"." $record->absoluteValue",0);
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

    public function getEPowerRecord() {
        return $this->EPowerRecord->value;
    }

    public function getGasRecord() {
        return $this->GasRecord->value;
    }

    public function getWaterRecord() {
        return $this->WaterRecord->value;
    }

    function printInput() {
        try {
            $this->openDB();
            $this->writeEPower();
            $this->writeGas();
           $this->writeWater();
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
        $this->EPowerValidator = new Validator(self::EPOWER_PATTERN);
        $this->GasValidator = new Validator(self::GAS_PATTERN);
        $this->WaterValidator = new Validator(self::WATER_PATTERN);
        $this->NoteValidator = new Validator(self::NOTE_PATTERN);
        $this->init();
    }

}

?>