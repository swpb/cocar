<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MonthlyPerformance
 *
 * @ORM\Table(name="tb_monthly_perform")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\MonthlyPerformanceRepository")
 */
class MonthlyPerformance
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="code_interface", type="integer")
     */
    private $codeInterface;

    /**
     * @var float
     *
     * @ORM\Column(name="use_20_50_in", type="float")
     */
    private $use2050In;

    /**
     * @var float
     *
     * @ORM\Column(name="use_50_85_in", type="float")
     */
    private $use5085In;

    /**
     * @var float
     *
     * @ORM\Column(name="use_m_85_in", type="float")
     */
    private $useM85In;

    /**
     * @var float
     *
     * @ORM\Column(name="use_20_50_out", type="float")
     */
    private $use2050Out;

    /**
     * @var float
     *
     * @ORM\Column(name="use_50_85_out", type="float")
     */
    private $use5085Out;

    /**
     * @var float
     *
     * @ORM\Column(name="use_m_85_out", type="float")
     */
    private $useM85Out;

    /**
     * @var float
     *
     * @ORM\Column(name="volume_in", type="float")
     */
    private $volumeIn;

    /**
     * @var float
     *
     * @ORM\Column(name="volume_out", type="float")
     */
    private $volumeOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_in", type="integer")
     */
    private $cirIn;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_out", type="integer")
     */
    private $cirOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_in_rec", type="integer")
     */
    private $cirInRec;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_out_rec", type="integer")
     */
    private $cirOutRec;


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
     * Set date
     *
     * @param \DateTime $date
     * @return MonthlyPerformance
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set codeInterface
     *
     * @param integer $codeInterface
     * @return MonthlyPerformance
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
     * Set use2050In
     *
     * @param float $use2050In
     * @return MonthlyPerformance
     */
    public function setUse2050In($use2050In)
    {
        $this->use2050In = $use2050In;
    
        return $this;
    }

    /**
     * Get use2050In
     *
     * @return float 
     */
    public function getUse2050In()
    {
        return $this->use2050In;
    }

    /**
     * Set use5085In
     *
     * @param float $use5085In
     * @return MonthlyPerformance
     */
    public function setUse5085In($use5085In)
    {
        $this->use5085In = $use5085In;
    
        return $this;
    }

    /**
     * Get use5085In
     *
     * @return float 
     */
    public function getUse5085In()
    {
        return $this->use5085In;
    }

    /**
     * Set useM85In
     *
     * @param float $useM85In
     * @return MonthlyPerformance
     */
    public function setUseM85In($useM85In)
    {
        $this->useM85In = $useM85In;
    
        return $this;
    }

    /**
     * Get useM85In
     *
     * @return float 
     */
    public function getUseM85In()
    {
        return $this->useM85In;
    }

    /**
     * Set use2050Out
     *
     * @param float $use2050Out
     * @return MonthlyPerformance
     */
    public function setUse2050Out($use2050Out)
    {
        $this->use2050Out = $use2050Out;
    
        return $this;
    }

    /**
     * Get use2050Out
     *
     * @return float 
     */
    public function getUse2050Out()
    {
        return $this->use2050Out;
    }

    /**
     * Set use5085Out
     *
     * @param float $use5085Out
     * @return MonthlyPerformance
     */
    public function setUse5085Out($use5085Out)
    {
        $this->use5085Out = $use5085Out;
    
        return $this;
    }

    /**
     * Get use5085Out
     *
     * @return float 
     */
    public function getUse5085Out()
    {
        return $this->use5085Out;
    }

    /**
     * Set useM85Out
     *
     * @param float $useM85Out
     * @return MonthlyPerformance
     */
    public function setUseM85Out($useM85Out)
    {
        $this->useM85Out = $useM85Out;
    
        return $this;
    }

    /**
     * Get useM85Out
     *
     * @return float 
     */
    public function getUseM85Out()
    {
        return $this->useM85Out;
    }

    /**
     * Set volumeIn
     *
     * @param float $volumeIn
     * @return MonthlyPerformance
     */
    public function setVolumeIn($volumeIn)
    {
        $this->volumeIn = $volumeIn;
    
        return $this;
    }

    /**
     * Get volumeIn
     *
     * @return float 
     */
    public function getVolumeIn()
    {
        return $this->volumeIn;
    }

    /**
     * Set volumeOut
     *
     * @param float $volumeOut
     * @return MonthlyPerformance
     */
    public function setVolumeOut($volumeOut)
    {
        $this->volumeOut = $volumeOut;
    
        return $this;
    }

    /**
     * Get volumeOut
     *
     * @return float 
     */
    public function getVolumeOut()
    {
        return $this->volumeOut;
    }

    /**
     * Set cirIn
     *
     * @param integer $cirIn
     * @return MonthlyPerformance
     */
    public function setCirIn($cirIn)
    {
        $this->cirIn = $cirIn;
    
        return $this;
    }

    /**
     * Get cirIn
     *
     * @return integer 
     */
    public function getCirIn()
    {
        return $this->cirIn;
    }

    /**
     * Set cirOut
     *
     * @param integer $cirOut
     * @return MonthlyPerformance
     */
    public function setCirOut($cirOut)
    {
        $this->cirOut = $cirOut;
    
        return $this;
    }

    /**
     * Get cirOut
     *
     * @return integer 
     */
    public function getCirOut()
    {
        return $this->cirOut;
    }

    /**
     * Set cirInRec
     *
     * @param integer $cirInRec
     * @return MonthlyPerformance
     */
    public function setCirInRec($cirInRec)
    {
        $this->cirInRec = $cirInRec;
    
        return $this;
    }

    /**
     * Get cirInRec
     *
     * @return integer 
     */
    public function getCirInRec()
    {
        return $this->cirInRec;
    }

    /**
     * Set cirOutRec
     *
     * @param integer $cirOutRec
     * @return MonthlyPerformance
     */
    public function setCirOutRec($cirOutRec)
    {
        $this->cirOutRec = $cirOutRec;
    
        return $this;
    }

    /**
     * Get cirOutRec
     *
     * @return integer 
     */
    public function getCirOutRec()
    {
        return $this->cirOutRec;
    }
}
