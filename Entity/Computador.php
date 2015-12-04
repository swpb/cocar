<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * Printer
 *
 * @ORM\Table("tb_computador", uniqueConstraints={@UniqueConstraint(name="mac_address_idx", columns={"mac_address"})})
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\ComputadorRepository")
 */
class Computador
{
    /**
     * Construct
     */
    public function __construct()
    {
        $this->pingComputador = new ArrayCollection();
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="PingComputador", mappedBy="computador")
     */
    protected $pingComputador;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="local", type="text", nullable=true)
     */
    private $local;


    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=255, unique=true)
     */
    private $host;

    /**
     * @var string
     *
     * @ORM\Column(name="mac_address", type="text", nullable=true)
     */
    private $mac_address;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="netmask", type="text", nullable=true)
     */
    private $netmask;

    /**
     * @var string
     *
     * @ORM\Column(name="so_name", type="text", nullable=true)
     */
    private $so_name;


    /**
     * @var string
     *
     * @ORM\Column(name="so_version", type="text", nullable=true)
     */
    private $so_version;

    /**
     * @var string
     *
     * @ORM\Column(name="accuracy", type="text", nullable=true)
     */
    private $accuracy;

    /**
     * @var string
     *
     * @ORM\Column(name="so_vendor", type="text", nullable=true)
     */
    private $so_vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="so_os_family", type="text", nullable=true)
     */
    private $so_os_family;

    /**
     * @var string
     *
     * @ORM\Column(name="so_type", type="text", nullable=true)
     */
    private $so_type;

    /**
     * @var string
     *
     * @ORM\Column(name="so_cpe", type="text", nullable=true)
     */
    private $so_cpe;

    /**
     * @var integer
     *
     * @ORM\Column(name="cacic_id", type="integer", nullable=true)
     */
    private $cacic_id;


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
     * Set local
     *
     * @param string $local
     * @return Printer
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local
     *
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Add printerCounter
     *
     * @param PrinterCounter $printerCounter
     * @return Entity
     */
    public function addPingComputador(PingComputador $pingComputador)
    {
        $pingComputador->setComputador($this);
        $this->pingComputador[] = $pingComputador;
    
        return $this;
    }

    /**
     * Remove printerCounter
     *
     * @param PrinterCounter $printerCounter
     */
    public function removePingComputador(PingComputador $pingComputador)
    {
        $this->pingComputador->removeElement($pingComputador);
    }

    /**
     * Get printerCounter
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPingComputador()
    {
        return $this->pingComputador;
    }

    /**
     * Active set
     *
     * @param $active
     * @return $this
     */
    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    /**
     * Active get
     *
     * @return bool
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * Netmask set
     *
     * @param $netmask
     * @return $this
     */
    public function setNetmask($netmask) {
        $this->netmask = $netmask;

        return $this;
    }

    /**
     * Netmask get
     *
     * @return string
     */
    public function getNetmask() {
        return $this->netmask;
    }


    /**
     * @param $mac_address
     * @return $this
     */
    public function setMacAddress($mac_address) {
        $this->mac_address = $mac_address;

        return $this;
    }

    /**
     * @return string
     */
    public function getMacAddress() {
        return $this->mac_address;
    }

    /**
     * @param $so_name
     * @return $this
     */
    public function setSoName($so_name) {
        $this->so_name = $so_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoName() {
        return $this->so_name;
    }

    /**
     * @param $so_version
     * @return $this
     */
    public function setSoVersion($so_version) {
        $this->so_version = $so_version;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoVersion() {
        return $this->so_version;
    }

    /**
     * @param $accuracy
     * @return $this
     */
    public function setAccuracy($accuracy) {
        $this->accuracy = $accuracy;

        return $this;
    }

    /**
     * @return string
     */
    public function getAccuracy() {
        return $this->accuracy;
    }

    /**
     * @param $so_vendor
     * @return $this
     */
    public function setSoVendor($so_vendor) {
        $this->so_vendor = $so_vendor;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoVendor() {
        return $this->so_vendor;
    }

    /**
     * @param $so_os_family
     * @return $this
     */
    public function setSoOsFamily($so_os_family) {
        $this->so_os_family = $so_os_family;

        return $this;
    }

    /**
     * @param $so_type
     * @return $this
     */
    public function setSoType($so_type) {
        $this->so_type = $so_type;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoType() {
        return $this->so_type;
    }

    /**
     * @param $so_cpe
     * @return $this
     */
    public function setSoCpe($so_cpe) {
        $this->so_cpe = $so_cpe;

        return $this;
    }

    /**
     * @return string
     */
    public function getSoCpe() {
        return $this->so_cpe;
    }

    /**
     * @param $cacic_id
     * @return $this
     */
    public function setCacicId($cacic_id) {
        $this->cacic_id = $cacic_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getCacicId() {
        return $this->cacic_id;
    }

}
