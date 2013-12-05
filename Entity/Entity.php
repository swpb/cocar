<?php

namespace Swpb\Bundle\CocarBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity
 *
 * @ORM\Table(name="tb_entity")
 * @ORM\Entity(repositoryClass="Swpb\Bundle\CocarBundle\Entity\CustomEntityRepository")
 */
class Entity
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
     * @ORM\OneToMany(targetEntity="Circuits", mappedBy="entity")
     */
    protected $circuits;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->circuits = new ArrayCollection();
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
     * Set identifier
     *
     * @param string $identifier
     * @return Entity
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    
        return $this;
    }

    /**
     * Get identifier
     *
     * @return string 
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Entity
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
     * Add circuits
     *
     * @param \Cocar\CocarBundle\Entity\Circuits $circuits
     * @return Entity
     */
    public function addCircuit(\Swpb\Bundle\CocarBundle\Entity\Circuits $circuits)
    {
        $this->circuits[] = $circuits;
    
        return $this;
    }

    /**
     * Remove circuits
     *
     * @param \Cocar\CocarBundle\Entity\Circuits $circuits
     */
    public function removeCircuit(\Swpb\Bundle\CocarBundle\Entity\Circuits $circuits)
    {
        $this->circuits->removeElement($circuits);
    }

    /**
     * Get circuits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCircuits()
    {
        return $this->circuits;
    }
}
