<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Gas
 *
 * @ORM\Table(name="gas")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GasRepository")
 */
class Gas extends EntityBase
{
    const name='Gas';
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set captureDate
     *
     * @param \DateTime $captureDate
     *
     * @return Water
     */
    public function setCaptureDate($captureDate)
    {
        $this->captureDate = $captureDate;

        return $this;
    }

    /**
     * Get captureDate
     *
     * @return \DateTime
     */
    public function getCaptureDate()
    {
        return $this->captureDate;
    }

    /**
     * Set submitDate
     *
     * @param \DateTime $submitDate
     *
     * @return Gas
     */
    public function setSubmitDate($submitDate)
    {
        $this->submitDate = $submitDate;

        return $this;
    }

    /**
     * Get submitDate
     *
     * @return \DateTime
     */
    public function getSubmitDate()
    {
        return $this->submitDate;
    }

    /**
     * Set value
     *
     * @param integer $value
     *
     * @return Gas
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set absoluteValue
     *
     * @param integer $absoluteValue
     *
     * @return Gas
     */
    public function setAbsoluteValue($absoluteValue)
    {
        $this->absoluteValue = $absoluteValue;

        return $this;
    }

    /**
     * Get absoluteValue
     *
     * @return int
     */
    public function getAbsoluteValue()
    {
        return $this->absoluteValue;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Gas
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}

