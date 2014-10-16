<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="level_a")
 * @ORM\Entity
 */
class LevelA
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
     * @ORM\OneToMany(targetEntity="LevelB", mappedBy="parent")
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
     * @return LevelA
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
     * Add children
     *
     * @param \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB $children
     * @return LevelA
     */
    public function addChildren(\SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB $children)
    {
        $this->children[] = $children;
    
        return $this;
    }

    /**
     * Remove children
     *
     * @param \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB $children
     */
    public function removeChildren(\SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB $children)
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