<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Performance
 *
 * @ORM\Table(name="tb_daily_performance")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\DailyPerformanceRepository")
 */
class DailyPerformance
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
     * @var integer
     *
     * @ORM\Column(name="code_interface", type="integer")
     */
    private $codeInterface;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="day", type="datetime")
     */
    private $day;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_in", type="integer")
     */
    private $cirIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_out", type="integer")
     */
    private $cirOut = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_in_rec", type="integer")
     */
    private $cirInRec = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="cir_out_rec", type="integer")
     */
    private $cirOutRec = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="volume_in", type="bigint")
     */
    private $volumeIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="volume_out", type="bigint")
     */
    private $volumeOut = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="delay_120_160", type="float")
     */
    private $delay120160 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="delay_m_160", type="float")
     */
    private $delayM160 = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="loss_in_hour", type="time")
     */
    private $lossInHour;

    /**
     * @var float
     *
     * @ORM\Column(name="loss_in_peak", type="float")
     */
    private $lossInPeak = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="loss_out_hour", type="time")
     */
    private $lossOutHour;

    /**
     * @var float
     *
     * @ORM\Column(name="loss_out_peak", type="float")
     */
    private $lossOutPeak = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="loss_out_3_6", type="float")
     */
    private $lossOut36 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="loss_in_3_6", type="float")
     */
    private $lossInt36 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="loss_out_m_6", type="float")
     */
    private $lossOutM6 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="loss_in_m_6", type="float")
     */
    private $lossInM6 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="congest_in_10_30", type="float")
     */
    private $congestIn1030 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="congest_in_m_30", type="float")
     */
    private $congestInM30 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="congest_out_10_30", type="float")
     */
    private $congestOut1030 = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="congest_out_m_30", type="float")
     */
    private $congestOutM30 = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hmm_hour_in", type="time")
     */
    private $hmmHourIn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hmm_hour_out", type="time")
     */
    private $hmmHourOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_peak_in", type="float")
     */
    private $hmmPeakIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_peak_out", type="float")
     */
    private $hmmPeakOut = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_media_in", type="float")
     */
    private $hmmMediaIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_media_out", type="float")
     */
    private $hmmMediaOut = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="hmm_criticality_in", type="float")
     */
    private $hmmCriticalityIn = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="hmm_criticality_out", type="float")
     */
    private $hmmCriticalityOut = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_hour_in_per", type="time")
     */
    private $hmmHourInPer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="hmm_hour_out_per", type="time")
     */
    private $hmmHourOutPer;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_peak_in_per", type="float")
     */
    private $hmmPeakInPer = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_peak_out_per", type="float")
     */
    private $hmmPeakOutPer = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_media_in_per", type="float")
     */
    private $hmmMediaInPer = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hmm_media_out_per", type="float")
     */
    private $hmmMediaOutPer = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="hmm_criticality_in_per", type="float")
     */
    private $hmmCriticalityInPer = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="hmm_criticality_out_per", type="float")
     */
    private $hmmCriticalityOutPer = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="_7_19_peak_in", type="float")
     */
    private $_719PeakIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="_7_19_peak_out", type="float")
     */
    private $_719PeakOut = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="_7_19_media_in", type="float")
     */
    private $_719MediaIn = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="_7_19_media_out", type="float")
     */
    private $_719MediaOut = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="_7_19_criticality_in", type="float")
     */
    private $_719CriticalityIn = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="_7_19_criticality_out", type="float")
     */
    private $_719CriticalityOut = 0;


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
     * @param integer $codeInterface
     * @return Performance
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
     * Set day
     *
     * @param \DateTime $day
     * @return Performance
     */
    public function setDay($day)
    {
        $this->day = $day;
    
        return $this;
    }

    /**
     * Get day
     *
     * @return \DateTime 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set cirIn
     *
     * @param integer $cirIn
     * @return Performance
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
     * @return Performance
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
     * @return Performance
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
     * @return Performance
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

    /**
     * Set volumeIn
     *
     * @param integer $volumeIn
     * @return Performance
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
     * @return Performance
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
     * Set delay120160
     *
     * @param float $delay120160
     * @return Performance
     */
    public function setDelay120160($delay120160)
    {
        $this->delay120160 = $delay120160;
    
        return $this;
    }

    /**
     * Get delay120160
     *
     * @return float 
     */
    public function getDelay120160()
    {
        return $this->delay120160;
    }

    /**
     * Set delayM160
     *
     * @param float $delayM160
     * @return Performance
     */
    public function setDelayM160($delayM160)
    {
        $this->delayM160 = $delayM160;
    
        return $this;
    }

    /**
     * Get delayM160
     *
     * @return float 
     */
    public function getDelayM160()
    {
        return $this->delayM160;
    }

    /**
     * Set lossInHour
     *
     * @param \DateTime $lossInHour
     * @return Performance
     */
    public function setLossInHour($lossInHour)
    {
        $this->lossInHour = $lossInHour;
    
        return $this;
    }

    /**
     * Get lossInHour
     *
     * @return \DateTime 
     */
    public function getLossInHour()
    {
        return $this->lossInHour;
    }

    /**
     * Set lossInPeak
     *
     * @param float $lossInPeak
     * @return Performance
     */
    public function setLossInPeak($lossInPeak)
    {
        $this->lossInPeak = $lossInPeak;
    
        return $this;
    }

    /**
     * Get lossInPeak
     *
     * @return float 
     */
    public function getLossInPeak()
    {
        return $this->lossInPeak;
    }

    /**
     * Set lossOutHour
     *
     * @param \DateTime $lossOutHour
     * @return Performance
     */
    public function setLossOutHour($lossOutHour)
    {
        $this->lossOutHour = $lossOutHour;
    
        return $this;
    }

    /**
     * Get lossOutHour
     *
     * @return \DateTime 
     */
    public function getLossOutHour()
    {
        return $this->lossOutHour;
    }

    /**
     * Set lossOutPeak
     *
     * @param float $lossOutPeak
     * @return Performance
     */
    public function setLossOutPeak($lossOutPeak)
    {
        $this->lossOutPeak = $lossOutPeak;
    
        return $this;
    }

    /**
     * Get lossOutPeak
     *
     * @return float 
     */
    public function getLossOutPeak()
    {
        return $this->lossOutPeak;
    }

    /**
     * Set lossOut36
     *
     * @param float $lossOut36
     * @return Performance
     */
    public function setLossOut36($lossOut36)
    {
        $this->lossOut36 = $lossOut36;
    
        return $this;
    }

    /**
     * Get lossOut36
     *
     * @return float 
     */
    public function getLossOut36()
    {
        return $this->lossOut36;
    }

    /**
     * Set lossInt36
     *
     * @param float $lossInt36
     * @return Performance
     */
    public function setLossInt36($lossInt36)
    {
        $this->lossInt36 = $lossInt36;
    
        return $this;
    }

    /**
     * Get lossInt36
     *
     * @return float 
     */
    public function getLossInt36()
    {
        return $this->lossInt36;
    }

    /**
     * Set lossOutM6
     *
     * @param float $lossOutM6
     * @return Performance
     */
    public function setLossOutM6($lossOutM6)
    {
        $this->lossOutM6 = $lossOutM6;
    
        return $this;
    }

    /**
     * Get lossOutM6
     *
     * @return float 
     */
    public function getLossOutM6()
    {
        return $this->lossOutM6;
    }

    /**
     * Set lossInM6
     *
     * @param float $lossInM6
     * @return Performance
     */
    public function setLossInM6($lossInM6)
    {
        $this->lossInM6 = $lossInM6;
    
        return $this;
    }

    /**
     * Get lossInM6
     *
     * @return float 
     */
    public function getLossInM6()
    {
        return $this->lossInM6;
    }

    /**
     * Set congestIn1030
     *
     * @param float $congestIn1030
     * @return Performance
     */
    public function setCongestIn1030($congestIn1030)
    {
        $this->congestIn1030 = $congestIn1030;
    
        return $this;
    }

    /**
     * Get congestIn1030
     *
     * @return float 
     */
    public function getCongestIn1030()
    {
        return $this->congestIn1030;
    }

    /**
     * Set congestInM30
     *
     * @param float $congestInM30
     * @return Performance
     */
    public function setCongestInM30($congestInM30)
    {
        $this->congestInM30 = $congestInM30;
    
        return $this;
    }

    /**
     * Get congestInM30
     *
     * @return float 
     */
    public function getCongestInM30()
    {
        return $this->congestInM30;
    }

    /**
     * Set congestOut1030
     *
     * @param float $congestOut1030
     * @return Performance
     */
    public function setCongestOut1030($congestOut1030)
    {
        $this->congestOut1030 = $congestOut1030;
    
        return $this;
    }

    /**
     * Get congestOut1030
     *
     * @return float 
     */
    public function getCongestOut1030()
    {
        return $this->congestOut1030;
    }

    /**
     * Set congestOutM30
     *
     * @param float $congestOutM30
     * @return Performance
     */
    public function setCongestOutM30($congestOutM30)
    {
        $this->congestOutM30 = $congestOutM30;
    
        return $this;
    }

    /**
     * Get congestOutM30
     *
     * @return float 
     */
    public function getCongestOutM30()
    {
        return $this->congestOutM30;
    }

    /**
     * Set hmmHourIn
     *
     * @param \DateTime $hmmHourIn
     * @return Performance
     */
    public function setHmmHourIn($hmmHourIn)
    {
        $this->hmmHourIn = $hmmHourIn;
    
        return $this;
    }

    /**
     * Get hmmHourIn
     *
     * @return \DateTime 
     */
    public function getHmmHourIn()
    {
        return $this->hmmHourIn;
    }

    /**
     * Set hmmHourOut
     *
     * @param \DateTime $hmmHourOut
     * @return Performance
     */
    public function setHmmHourOut($hmmHourOut)
    {
        $this->hmmHourOut = $hmmHourOut;
    
        return $this;
    }

    /**
     * Get hmmHourOut
     *
     * @return \DateTime 
     */
    public function getHmmHourOut()
    {
        return $this->hmmHourOut;
    }

    /**
     * Set hmmPeakIn
     *
     * @param integer $hmmPeakIn
     * @return Performance
     */
    public function setHmmPeakIn($hmmPeakIn)
    {
        $this->hmmPeakIn = $hmmPeakIn;
    
        return $this;
    }

    /**
     * Get hmmPeakIn
     *
     * @return integer 
     */
    public function getHmmPeakIn()
    {
        return $this->hmmPeakIn;
    }

    /**
     * Set hmmPeakOut
     *
     * @param integer $hmmPeakOut
     * @return Performance
     */
    public function setHmmPeakOut($hmmPeakOut)
    {
        $this->hmmPeakOut = $hmmPeakOut;
    
        return $this;
    }

    /**
     * Get hmmPeakOut
     *
     * @return integer 
     */
    public function getHmmPeakOut()
    {
        return $this->hmmPeakOut;
    }

    /**
     * Set hmmMediaIn
     *
     * @param integer $hmmMediaIn
     * @return Performance
     */
    public function setHmmMediaIn($hmmMediaIn)
    {
        $this->hmmMediaIn = $hmmMediaIn;
    
        return $this;
    }

    /**
     * Get hmmMediaIn
     *
     * @return integer 
     */
    public function getHmmMediaIn()
    {
        return $this->hmmMediaIn;
    }

    /**
     * Set hmmMediaOut
     *
     * @param integer $hmmMediaOut
     * @return Performance
     */
    public function setHmmMediaOut($hmmMediaOut)
    {
        $this->hmmMediaOut = $hmmMediaOut;
    
        return $this;
    }

    /**
     * Get hmmMediaOut
     *
     * @return integer 
     */
    public function getHmmMediaOut()
    {
        return $this->hmmMediaOut;
    }

    /**
     * Set hmmCriticalityIn
     *
     * @param float $hmmCriticalityIn
     * @return Performance
     */
    public function setHmmCriticalityIn($hmmCriticalityIn)
    {
        $this->hmmCriticalityIn = $hmmCriticalityIn;
    
        return $this;
    }

    /**
     * Get hmmCriticalityIn
     *
     * @return float 
     */
    public function getHmmCriticalityIn()
    {
        return $this->hmmCriticalityIn;
    }

    /**
     * Set hmmCriticalityOut
     *
     * @param float $hmmCriticalityOut
     * @return Performance
     */
    public function setHmmCriticalityOut($hmmCriticalityOut)
    {
        $this->hmmCriticalityOut = $hmmCriticalityOut;
    
        return $this;
    }

    /**
     * Get hmmCriticalityOut
     *
     * @return float 
     */
    public function getHmmCriticalityOut()
    {
        return $this->hmmCriticalityOut;
    }

    /**
     * Set hmmHourInPer
     *
     * @param integer $hmmHourInPer
     * @return Performance
     */
    public function setHmmHourInPer($hmmHourInPer)
    {
        $this->hmmHourInPer = $hmmHourInPer;
    
        return $this;
    }

    /**
     * Get hmmHourInPer
     *
     * @return integer 
     */
    public function getHmmHourInPer()
    {
        return $this->hmmHourInPer;
    }

    /**
     * Set hmmHourOutPer
     *
     * @param \DateTime $hmmHourOutPer
     * @return Performance
     */
    public function setHmmHourOutPer($hmmHourOutPer)
    {
        $this->hmmHourOutPer = $hmmHourOutPer;
    
        return $this;
    }

    /**
     * Get hmmHourOutPer
     *
     * @return \DateTime 
     */
    public function getHmmHourOutPer()
    {
        return $this->hmmHourOutPer;
    }

    /**
     * Set hmmPeakInPer
     *
     * @param integer $hmmPeakInPer
     * @return Performance
     */
    public function setHmmPeakInPer($hmmPeakInPer)
    {
        $this->hmmPeakInPer = $hmmPeakInPer;
    
        return $this;
    }

    /**
     * Get hmmPeakInPer
     *
     * @return integer 
     */
    public function getHmmPeakInPer()
    {
        return $this->hmmPeakInPer;
    }

    /**
     * Set hmmPeakOutPer
     *
     * @param integer $hmmPeakOutPer
     * @return Performance
     */
    public function setHmmPeakOutPer($hmmPeakOutPer)
    {
        $this->hmmPeakOutPer = $hmmPeakOutPer;
    
        return $this;
    }

    /**
     * Get hmmPeakOutPer
     *
     * @return integer 
     */
    public function getHmmPeakOutPer()
    {
        return $this->hmmPeakOutPer;
    }

    /**
     * Set hmmMediaInPer
     *
     * @param integer $hmmMediaInPer
     * @return Performance
     */
    public function setHmmMediaInPer($hmmMediaInPer)
    {
        $this->hmmMediaInPer = $hmmMediaInPer;
    
        return $this;
    }

    /**
     * Get hmmMediaInPer
     *
     * @return integer 
     */
    public function getHmmMediaInPer()
    {
        return $this->hmmMediaInPer;
    }

    /**
     * Set hmmMediaOutPer
     *
     * @param integer $hmmMediaOutPer
     * @return Performance
     */
    public function setHmmMediaOutPer($hmmMediaOutPer)
    {
        $this->hmmMediaOutPer = $hmmMediaOutPer;
    
        return $this;
    }

    /**
     * Get hmmMediaOutPer
     *
     * @return integer 
     */
    public function getHmmMediaOutPer()
    {
        return $this->hmmMediaOutPer;
    }

    /**
     * Set hmmCriticalityInPer
     *
     * @param float $hmmCriticalityInPer
     * @return Performance
     */
    public function setHmmCriticalityInPer($hmmCriticalityInPer)
    {
        $this->hmmCriticalityInPer = $hmmCriticalityInPer;
    
        return $this;
    }

    /**
     * Get hmmCriticalityInPer
     *
     * @return float 
     */
    public function getHmmCriticalityInPer()
    {
        return $this->hmmCriticalityInPer;
    }

    /**
     * Set hmmCriticalityOutPer
     *
     * @param float $hmmCriticalityOutPer
     * @return Performance
     */
    public function setHmmCriticalityOutPer($hmmCriticalityOutPer)
    {
        $this->hmmCriticalityOutPer = $hmmCriticalityOutPer;
    
        return $this;
    }

    /**
     * Get hmmCriticalityOutPer
     *
     * @return float 
     */
    public function getHmmCriticalityOutPer()
    {
        return $this->hmmCriticalityOutPer;
    }

    /**
     * Set 719PeakInt
     *
     * @param integer $719PeakInt
     * @return Performance
     */
    public function set719PeakIn($_719PeakIn)
    {
        $this->_719PeakIn = $_719PeakIn;
    
        return $this;
    }

    /**
     * Get 719PeakInt
     *
     * @return integer 
     */
    public function get719PeakIn()
    {
        return $this->_719PeakIn;
    }

    /**
     * Set 719PeakOut
     *
     * @param integer $719PeakOut
     * @return Performance
     */
    public function set719PeakOut($_719PeakOut)
    {
        $this->_719PeakOut = $_719PeakOut;
    
        return $this;
    }

    /**
     * Get 719PeakOut
     *
     * @return integer 
     */
    public function get719PeakOut()
    {
        return $this->_719PeakOut;
    }

    /**
     * Set 719MediaIn
     *
     * @param integer $719MediaIn
     * @return Performance
     */
    public function set719MediaIn($_719MediaIn)
    {
        $this->_719MediaIn = $_719MediaIn;
    
        return $this;
    }

    /**
     * Get 719MediaIn
     *
     * @return integer 
     */
    public function get719MediaIn()
    {
        return $this->_719MediaIn;
    }

    /**
     * Set 719MediaOut
     *
     * @param integer $719MediaOut
     * @return Performance
     */
    public function set719MediaOut($_719MediaOut)
    {
        $this->_719MediaOut = $_719MediaOut;
    
        return $this;
    }

    /**
     * Get 719MediaOut
     *
     * @return integer 
     */
    public function get719MediaOut()
    {
        return $this->_719MediaOut;
    }

    /**
     * Set 719CriticalityIn
     *
     * @param float $719CriticalityIn
     * @return Performance
     */
    public function set719CriticalityIn($_719CriticalityIn)
    {
        $this->_719CriticalityIn = $_719CriticalityIn;
    
        return $this;
    }

    /**
     * Get 719CriticalityIn
     *
     * @return float 
     */
    public function get719CriticalityIn()
    {
        return $this->_719CriticalityIn;
    }

    /**
     * Set 719CriticalityOut
     *
     * @param float $719CriticalityOut
     * @return Performance
     */
    public function set719CriticalityOut($_719CriticalityOut)
    {
        $this->_719CriticalityOut = $_719CriticalityOut;
    
        return $this;
    }

    /**
     * Get 719CriticalityOut
     *
     * @return float 
     */
    public function get719CriticalityOut()
    {
        return $this->_719CriticalityOut;
    }
}
