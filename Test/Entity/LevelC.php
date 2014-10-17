<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="level_c")
 * @ORM\Entity
 */
class LevelC
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
     * @ORM\ManyToOne(targetEntity="LevelB", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parent;
    
    public function getGroup()
    {
        return $this->id < 5 ? 'Group1' : 'Group2' ;
    }
    
    public function getExtraData()
    {
        return array('subtitle' => 'Subtitle for '.$this->name);
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
     * @return LevelC
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
     * @param \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB $parent
     * @return LevelC
     */
    public function setParent(\SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB $parent = null)
    {
        $this->parent = $parent;
    
        return $this;
    }

    /**
     * Get parent
     *
     * @return \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB 
     */
    public function getParent()
    {
        return $this->parent;
    }
}