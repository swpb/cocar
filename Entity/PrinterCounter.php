<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PrinterCounter
 *
 * @ORM\Table("tb_printer_counter")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\PrinterCounterRepository")
 */
class PrinterCounter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Printer", inversedBy="printerCounter")
     * @ORM\JoinColumn(name="printer_id", referencedColumnName="id")
     */
    protected $printer;

    /**
     * @var integer
     *
     * @ORM\Column(name="blackInk", type="integer")
     */
    private $blackInk;

    /**
     * @var integer
     *
     * @ORM\Column(name="coloredInk", type="integer")
     */
    private $coloredInk;

    /**
     * @var integer
     *
     * @ORM\Column(name="prints", type="integer")
     */
    private $prints;

    /**
     * @var integer
     *
     * @ORM\Column(name="date", type="integer")
     */
    private $date;

    public function __construct()
    {
        $this->printer    = 0;
        $this->coloredInk = 0;
        $this->blackInk   = 0;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set blackInk
     *
     * @param integer $blackInk
     * @return PrinterCounter
     */
    public function setBlackInk($blackInk)
    {
        $this->blackInk = $blackInk;
    
        return $this;
    }

    /**
     * Get blackInk
     *
     * @return integer 
     */
    public function getBlackInk()
    {
        return $this->blackInk;
    }

    /**
     * Set coloredInk
     *
     * @param integer $coloredInk
     * @return PrinterCounter
     */
    public function setColoredInk($coloredInk)
    {
        $this->coloredInk = $coloredInk;
    
        return $this;
    }

    /**
     * Get coloredInk
     *
     * @return integer 
     */
    public function getColoredInk()
    {
        return $this->coloredInk;
    }

    /**
     * Set prints
     *
     * @param integer $prints
     * @return PrinterCounter
     */
    public function setPrints($prints)
    {
        $this->prints = $prints;
    
        return $this;
    }

    /**
     * Get prints
     *
     * @return integer 
     */
    public function getPrints()
    {
        return $this->prints;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return PrinterCounter
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set printer
     *
     * @param \Cocar\CocarBundle\Entity\Printer $printer
     * @return Circuits
     */
    public function setPrinter(\Swpb\Bundle\CocarBundle\Entity\Printer $printer = null)
    {
        $this->printer = $printer;
    
        return $this;
    }

    /**
     * Get printer
     *
     * @return \Cocar\CocarBundle\Entity\Printer 
     */
    public function getPrinter()
    {
        return $this->printer;
    }
}
