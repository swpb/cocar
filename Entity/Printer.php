<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Printer
 *
 * @ORM\Table("tb_printer")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\PrinterRepository")
 */
class Printer
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
     * @ORM\OneToMany(targetEntity="PrinterCounter", mappedBy="printer")
     */
    protected $printerCounter;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="communitySnmpPrinter", type="string", length=255)
     */
    private $communitySnmpPrinter;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, unique=true)
     */
    private $host;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->printerCounter = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Printer
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Printer
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set communitySnmpPrinter
     *
     * @param string $communitySnmpPrinter
     * @return Printer
     */
    public function setCommunitySnmpPrinter($communitySnmpPrinter)
    {
        $this->communitySnmpPrinter = $communitySnmpPrinter;
    
        return $this;
    }

    /**
     * Get communitySnmpPrinter
     *
     * @return string 
     */
    public function getCommunitySnmpPrinter()
    {
        return $this->communitySnmpPrinter;
    }

    /**
     * Set host
     *
     * @param string $host
     * @return Printer
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Add printerCounter
     *
     * @param \Cocar\CocarBundle\Entity\PrinterCounter $printerCounter
     * @return Entity
     */
    public function addPrinterCounter(\Swpb\Bundle\CocarBundle\Entity\PrinterCounter $printerCounter)
    {
        $this->printerCounter[] = $printerCounter;
    
        return $this;
    }

    /**
     * Remove printerCounter
     *
     * @param \Cocar\CocarBundle\Entity\PrinterCounter $printerCounter
     */
    public function removePrinterCounter(\Swpb\Bundle\CocarBundle\Entity\PrinterCounter $printerCounter)
    {
        $this->printerCounter->removeElement($printerCounter);
    }

    /**
     * Get printerCounter
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrinterCounter()
    {
        return $this->printerCounter;
    }
}
