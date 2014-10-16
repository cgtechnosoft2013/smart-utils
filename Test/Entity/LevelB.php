<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="level_b")
 * @ORM\Entity
 */
class LevelB
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="LevelA", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    /**
     * @ORM\OneToMany(targetEntity="LevelC", mappedBy="parent")
     */
    private $children;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return LevelB
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
     * Set parent
     *
     * @param \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelA $parent
     * @return LevelB
     */
    public function setParent(\SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelA $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \Workflow\HazardousSituationBundle\Entity\LevelA 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC $children
     * @return LevelB
     */
    public function addChildren(\SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \SDLab\Bundle\SmartUtilsBundle\Test\Entity\Entity\LevelC $children
     */
    public function removeChildren(\SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }
}