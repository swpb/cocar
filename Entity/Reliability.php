<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reliability
 *
 * @ORM\Table(name="tb_reliability")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\ReliabilityRepository")
 */
class Reliability
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
     * @var string
     *
     * @ORM\Column(name="code_interface", type="string", length=255)
     */
    private $codeInterface;

    /**
     * @var integer
     *
     * @ORM\Column(name="date", type="integer")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="rly", type="smallint")
     */
    private $rly;


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
     * Set codeInterface
     *
     * @param string $codeInterface
     * @return Reliability
     */
    public function setCodeInterface($codeInterface)
    {
        $this->codeInterface = $codeInterface;
    
        return $this;
    }

    /**
     * Get codeInterface
     *
     * @return string 
     */
    public function getCodeInterface()
    {
        return $this->codeInterface;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Reliability
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
     * Set rly
     *
     * @param integer $rly
     * @return Reliability
     */
    public function setRly($rly)
    {
        $this->rly = $rly;
    
        return $this;
    }

    /**
     * Get rly
     *
     * @return integer 
     */
    public function getRly()
    {
        return $this->rly;
    }
}
