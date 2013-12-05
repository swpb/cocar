<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rrd
 *
 * @ORM\Table(name="tb_rrdlog")
 * @ORM\Entity
 */
class Rrd
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
     * @var datetime
     *
     * @ORM\Column(name="datetime", type="datetime")
     */
    private $datetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="code_interface", type="integer")
     */
    private $codeInterface;

    /**
     * @var integer
     *
     * @ORM\Column(name="volume_in", type="float")
     */
    private $volumeIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="volume_out", type="float")
     */
    private $volumeOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="dellay", type="smallint", nullable=true)
     */
    private $dellay = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="st_delay", type="smallint", nullable=true)
     */
    private $stDelay = 0;


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
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return Rrd
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    
        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set codeInterface
     *
     * @param integer $codeInterface
     * @return Rrd
     */
    public function setCodeInterface($codeInterface)
    {
        $this->codeInterface = $codeInterface;
    
        return $this;
    }

    /**
     * Get codeInterface
     *
     * @return integer 
     */
    public function getCodeInterface()
    {
        return $this->codeInterface;
    }

    /**
     * Set volumeIn
     *
     * @param integer $volumeIn
     * @return Rrd
     */
    public function setVolumeIn($volumeIn)
    {
        $this->volumeIn = $volumeIn;
    
        return $this;
    }

    /**
     * Get volumeIn
     *
     * @return integer 
     */
    public function getVolumeIn()
    {
        return $this->volumeIn;
    }

    /**
     * Set volumeOut
     *
     * @param integer $volumeOut
     * @return Rrd
     */
    public function setVolumeOut($volumeOut)
    {
        $this->volumeOut = $volumeOut;
    
        return $this;
    }

    /**
     * Get volumeOut
     *
     * @return integer 
     */
    public function getVolumeOut()
    {
        return $this->volumeOut;
    }

    /**
     * Set dellay
     *
     * @param integer $dellay
     * @return Rrd
     */
    public function setDellay($dellay)
    {
        $this->dellay = $dellay;
    
        return $this;
    }

    /**
     * Get dellay
     *
     * @return integer 
     */
    public function getDellay()
    {
        return $this->dellay;
    }

    /**
     * Set stDelay
     *
     * @param integer $stDelay
     * @return Rrd
     */
    public function setStDelay($stDelay)
    {
        $this->stDelay = $stDelay;
    
        return $this;
    }

    /**
     * Get stDelay
     *
     * @return integer 
     */
    public function getStDelay()
    {
        return $this->stDelay;
    }
}
