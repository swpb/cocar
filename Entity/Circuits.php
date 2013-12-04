<?php

namespace GerenciadorRedes\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Circuits
 *
 * @ORM\Table(name="tb_circuits")
 * @ORM\Entity(repositoryClass="GerenciadorRedes\Bundle\CocarBundle\Entity\CustomCircuitsRepository")
 */
class Circuits
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
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="circuits")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id")
     */
    protected $entity;

    /**
     * @ORM\OneToMany(targetEntity="Machine", mappedBy="gateway", cascade={"persist"})
     */
    protected $machine;

    /**
     * @var string
     *
     * @ORM\Column(name="code_interface", type="string", length=255)
     */
    private $codeInterface;

    /**
     * @var string
     *
     * @ORM\Column(name="admin_status", type="string", length=5, nullable=true)
     */
    private $adminStatus = 'UP';

    /**
     * @var string
     *
     * @ORM\Column(name="oper_status", type="string", length=5, nullable=true)
     */
    private $operStatus = 'UP';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=5, nullable=true)
     */
    private $status = 'UP';

    /**
     * @var string
     *
     * @ORM\Column(name="generate_alarm", type="boolean", length=5, nullable=true)
     */
    private $generateAlarm = true;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="manages", type="string", length=255)
     */
    private $manages;

    /**
     * @var string $reliability
     *
     * @ORM\Column(name="reliability", type="string", nullable=true)
     */
    private $reliability = '255';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_backbone", type="string", length=255)
     */
    private $ipBackbone;

    /**
     * @var string
     *
     * @ORM\Column(name="community_snmp_backbone", type="string", length=255)
     */
    private $communitySnmpBackbone;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_backbone", type="string", length=255)
     */
    private $serialBackbone;

    /**
     * @var string
     *
     * @ORM\Column(name="technology", type="string", length=255)
     */
    private $technology;

    /**
     * @var string
     *
     * @ORM\Column(name="type_interface", type="string", length=255)
     */
    private $typeInterface;

    /**
     * @var string
     *
     * @ORM\Column(name="num_snmp_interface", type="string", length=255)
     */
    private $numSnmpInterface;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_serial_interface", type="string", length=255)
     */
    private $ipSerialInterface;

    /**
     * @var string
     *
     * @ORM\Column(name="register_circuit", type="string", length=255)
     */
    private $registerCircuit;

    /**
     * @var string
     *
     * @ORM\Column(name="speed", type="string", length=255)
     */
    private $speed;

    /**
     * @var string
     *
     * @ORM\Column(name="cir_in", type="string", length=255)
     */
    private $cirIn;

    /**
     * @var string
     *
     * @ORM\Column(name="cir_out", type="string", length=255)
     */
    private $cirOut;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_router_tip", type="string", length=255)
     */
    private $serialRouterTip;

    /**
     * @var string
     *
     * @ORM\Column(name="snmp_port_tip", type="string", length=255)
     */
    private $snmpPortTip;

    /**
     * @var string
     *
     * @ORM\Column(name="community_snmp_router_tip", type="string", length=255)
     */
    private $communitySnmpRouterTip;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_serial_router_tip", type="string", length=255)
     */
    private $ipSerialRouterTip;

    /**
     * @var string
     *
     * @ORM\Column(name="history", type="string", length=255)
     */
    private $history = 'N';

    /**
     * Construct
     */
    public function __construct()
    {
        $this->machine = new ArrayCollection();
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
     * Set codeInterface
     *
     * @param string $codeInterface
     * @return Circuits
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
     * Set description
     *
     * @param string $description
     * @return Circuits
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
     * Set manages
     *
     * @param string $manages
     * @return Circuits
     */
    public function setManages($manages)
    {
        $this->manages = $manages;
    
        return $this;
    }

    /**
     * Get manages
     *
     * @return string 
     */
    public function getManages()
    {
        return $this->manages;
    }

    /**
     * Set ipBackbone
     *
     * @param string $ipBackbone
     * @return Circuits
     */
    public function setIpBackbone($ipBackbone)
    {
        $this->ipBackbone = $ipBackbone;
    
        return $this;
    }

    /**
     * Get ipBackbone
     *
     * @return string 
     */
    public function getIpBackbone()
    {
        return $this->ipBackbone;
    }

    /**
     * Set communitySnmpBackbone
     *
     * @param string $communitySnmpBackbone
     * @return Circuits
     */
    public function setCommunitySnmpBackbone($communitySnmpBackbone)
    {
        $this->communitySnmpBackbone = $communitySnmpBackbone;
    
        return $this;
    }

    /**
     * Get communitySnmpBackbone
     *
     * @return string 
     */
    public function getCommunitySnmpBackbone()
    {
        return $this->communitySnmpBackbone;
    }

    /**
     * Set serialBackbone
     *
     * @param string $serialBackbone
     * @return Circuits
     */
    public function setSerialBackbone($serialBackbone)
    {
        $this->serialBackbone = $serialBackbone;
    
        return $this;
    }

    /**
     * Get serialBackbone
     *
     * @return string 
     */
    public function getSerialBackbone()
    {
        return $this->serialBackbone;
    }

    /**
     * Set technology
     *
     * @param string $technology
     * @return Circuits
     */
    public function setTechnology($technology)
    {
        $this->technology = $technology;
    
        return $this;
    }

    /**
     * Get technology
     *
     * @return string 
     */
    public function getTechnology()
    {
        return $this->technology;
    }

    /**
     * Set typeInterface
     *
     * @param string $typeInterface
     * @return Circuits
     */
    public function setTypeInterface($typeInterface)
    {
        $this->typeInterface = $typeInterface;
    
        return $this;
    }

    /**
     * Get typeInterface
     *
     * @return string 
     */
    public function getTypeInterface()
    {
        return $this->typeInterface;
    }

    /**
     * Set numSnmpInterface
     *
     * @param string $numSnmpInterface
     * @return Circuits
     */
    public function setNumSnmpInterface($numSnmpInterface)
    {
        $this->numSnmpInterface = $numSnmpInterface;
    
        return $this;
    }

    /**
     * Get numSnmpInterface
     *
     * @return string 
     */
    public function getNumSnmpInterface()
    {
        return $this->numSnmpInterface;
    }

    /**
     * Set ipSerialInterface
     *
     * @param string $ipSerialInterface
     * @return Circuits
     */
    public function setIpSerialInterface($ipSerialInterface)
    {
        $this->ipSerialInterface = $ipSerialInterface;
    
        return $this;
    }

    /**
     * Get ipSerialInterface
     *
     * @return string 
     */
    public function getIpSerialInterface()
    {
        return $this->ipSerialInterface;
    }

    /**
     * Set registerCircuit
     *
     * @param string $registerCircuit
     * @return Circuits
     */
    public function setRegisterCircuit($registerCircuit)
    {
        $this->registerCircuit = $registerCircuit;
    
        return $this;
    }

    /**
     * Get registerCircuit
     *
     * @return string 
     */
    public function getRegisterCircuit()
    {
        return $this->registerCircuit;
    }

    /**
     * Set speed
     *
     * @param string $speed
     * @return Circuits
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;
    
        return $this;
    }

    /**
     * Get speed
     *
     * @return string 
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Set cirIn
     *
     * @param string $cirIn
     * @return Circuits
     */
    public function setCirIn($cirIn)
    {
        $this->cirIn = $cirIn;
    
        return $this;
    }

    /**
     * Get cirIn
     *
     * @return string 
     */
    public function getCirIn()
    {
        return $this->cirIn;
    }

    /**
     * Set cirOut
     *
     * @param string $cirOut
     * @return Circuits
     */
    public function setCirOut($cirOut)
    {
        $this->cirOut = $cirOut;
    
        return $this;
    }

    /**
     * Get cirOut
     *
     * @return string 
     */
    public function getCirOut()
    {
        return $this->cirOut;
    }

    /**
     * Set serialRouterTip
     *
     * @param string $serialRouterTip
     * @return Circuits
     */
    public function setSerialRouterTip($serialRouterTip)
    {
        $this->serialRouterTip = $serialRouterTip;
    
        return $this;
    }

    /**
     * Get serialRouterTip
     *
     * @return string 
     */
    public function getSerialRouterTip()
    {
        return $this->serialRouterTip;
    }

    /**
     * Set snmpPortTip
     *
     * @param string $snmpPortTip
     * @return Circuits
     */
    public function setSnmpPortTip($snmpPortTip)
    {
        $this->snmpPortTip = $snmpPortTip;
    
        return $this;
    }

    /**
     * Get snmpPortTip
     *
     * @return string 
     */
    public function getSnmpPortTip()
    {
        return $this->snmpPortTip;
    }

    /**
     * Set communitySnmpRouterTip
     *
     * @param string $communitySnmpRouterTip
     * @return Circuits
     */
    public function setCommunitySnmpRouterTip($communitySnmpRouterTip)
    {
        $this->communitySnmpRouterTip = $communitySnmpRouterTip;
    
        return $this;
    }

    /**
     * Get communitySnmpRouterTip
     *
     * @return string 
     */
    public function getCommunitySnmpRouterTip()
    {
        return $this->communitySnmpRouterTip;
    }

    /**
     * Set ipSerialRouterTip
     *
     * @param string $ipSerialRouterTip
     * @return Circuits
     */
    public function setIpSerialRouterTip($ipSerialRouterTip)
    {
        $this->ipSerialRouterTip = $ipSerialRouterTip;
    
        return $this;
    }

    /**
     * Get ipSerialRouterTip
     *
     * @return string 
     */
    public function getIpSerialRouterTip()
    {
        return $this->ipSerialRouterTip;
    }

    /**
     * Set entity
     *
     * @param \Cocar\CocarBundle\Entity\Entity $entity
     * @return Circuits
     */
    public function setEntity(\GerenciadorRedes\Bundle\CocarBundle\Entity\Entity $entity = null)
    {
        $this->entity = $entity;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return \Cocar\CocarBundle\Entity\Entity 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set adminStatus
     *
     * @param string $adminStatus
     * @return Circuits
     */
    public function setAdminStatus($adminStatus)
    {
        $this->adminStatus = $adminStatus;
    
        return $this;
    }

    /**
     * Get adminStatus
     *
     * @return string 
     */
    public function getAdminStatus()
    {
        return $this->adminStatus;
    }

    /**
     * Set operStatus
     *
     * @param string $operStatus
     * @return Circuits
     */
    public function setOperStatus($operStatus)
    {
        $this->operStatus = $operStatus;
    
        return $this;
    }

    /**
     * Get operStatus
     *
     * @return string 
     */
    public function getOperStatus()
    {
        return $this->operStatus;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Circuits
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set generateAlarm
     *
     * @param boolean $generateAlarm
     * @return Circuits
     */
    public function setGenerateAlarm($generateAlarm)
    {
        $this->generateAlarm = $generateAlarm;
    
        return $this;
    }

    /**
     * Get generateAlarm
     *
     * @return boolean 
     */
    public function getGenerateAlarm()
    {
        return $this->generateAlarm;
    }

    /**
     * Set reliability
     *
     * @param string $reliability
     * @return Circuits
     */
    public function setReliability($reliability)
    {
        $this->reliability = $reliability;
    
        return $this;
    }

    /**
     * Get reliability
     *
     * @return string 
     */
    public function getReliability()
    {
        return $this->reliability;
    }

    /**
     * Set history
     *
     * @param string $history
     * @return Circuits
     */
    public function setHistory($history)
    {
        $this->history = $history;
    
        return $this;
    }

    /**
     * Get history
     *
     * @return string 
     */
    public function getHistory()
    {
        return $this->history;
    }


    /**
     * Add circuits
     *
     * @param \Cocar\CocarBundle\Entity\Machine $machine
     * @return Entity
     */
    public function addMachine(\GerenciadorRedes\Bundle\CocarBundle\Entity\Machine $machine)
    {
        $this->machine[] = $machine;
    
        return $this;
    }

    /**
     * Remove machine
     *
     * @param \Cocar\CocarBundle\Entity\Circuits $machine
     */
    public function removeMachine(\GerenciadorRedes\Bundle\CocarBundle\Entity\Machine $machine)
    {
        $this->machine->removeElement($machine);
    }

    /**
     * Get machine
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMachine()
    {
        return $this->machine;
    }
}