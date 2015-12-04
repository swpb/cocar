<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * PrinterCounter
 *
 * @ORM\Table("tb_ping_computador",uniqueConstraints={@UniqueConstraint(name="ping_computador_date_unique_idx", columns={"computador_id", "date"})})
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\PingComputadorRepository")
 */
class PingComputador
{
    public function __construct()
    {

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
     * @ORM\ManyToOne(targetEntity="Computador", inversedBy="pingComputador")
     * @ORM\JoinColumn(name="computador_id", referencedColumnName="id")
     */
    protected $computador;

    /**
     * @var integer
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param Computador|null $computador
     * @return $this
     */
    public function setComputador(\Swpb\Bundle\CocarBundle\Entity\Computador $computador = null)
    {
        $this->computador = $computador;
    
        return $this;
    }

    /**
     * @return mixed
     */
    public function getComputador()
    {
        return $this->computador;
    }
}
