<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class EntityBase {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="captureDate", type="date")
     */
    private $captureDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submitDate", type="datetime", unique=true)
     */
    private $submitDate;

    /**
     * @var int
     *
     * @ORM\Column(name="value", type="integer")
     */
    private $value;

    /**
     * @var int
     *
     * @ORM\Column(name="absoluteValue", type="integer", unique=true)
     */
    private $absoluteValue;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    private $note;

    /**
     * not persisted!
     */
    private $diff;
    private $dailyAvg;

    function setDiff($diff) {
        $this->diff = $diff;
    }

    public function getDiff() {
        return $this->diff;
    }

    public function setDailyAvg($dailyAvg) {
        return $this->dailyAvg = $dailyAvg;
    }

    public function getDailyAvg() {
        return $this->dailyAvg;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set captureDate
     *
     * @param \DateTime $captureDate
     *
     * @return Epower
     */
    public function setCaptureDate($captureDate) {
        $this->captureDate = $captureDate;

        return $this;
    }

    /**
     * Get captureDate
     *
     * @return \DateTime
     */
    public function getCaptureDate() {
        return $this->captureDate;
    }

    /**
     * Set submitDate
     *
     * @param \DateTime $submitDate
     *
     * @return Epower
     */
    public function setSubmitDate($submitDate) {
        $this->submitDate = $submitDate;

        return $this;
    }

    /**
     * Get submitDate
     *
     * @return \DateTime
     */
    public function getSubmitDate() {
        return $this->submitDate;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Epower
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set absoluteValue
     *
     * @param integer $absoluteValue
     *
     * @return Epower
     */
    public function setAbsoluteValue($absoluteValue) {
        $this->absoluteValue = $absoluteValue;

        return $this;
    }

    /**
     * Get absoluteValue
     *
     * @return int
     */
    public function getAbsoluteValue() {
        return $this->absoluteValue;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Epower
     */
    public function setNote($note) {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote() {
        return $this->note;
    }

}
