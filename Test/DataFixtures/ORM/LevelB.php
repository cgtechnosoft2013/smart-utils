<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LevelB extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $class = new \ReflectionClass('SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB');
        $propertyId = $class->getProperty('id');
        $propertyId->setAccessible(true);

        $metadata = $manager->getClassMetaData("SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB");
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        
        $levelB1 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB();
        $propertyId->setValue($levelB1, 1);
        $levelB1->setName('Test AA');
        $levelB1->setParent($this->getReference('LevelA-1'));
        $manager->persist($levelB1);
        
        $levelB2 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB();
        $propertyId->setValue($levelB2, 2);
        $levelB2->setName('Test AB');
        $levelB2->setParent($this->getReference('LevelA-1'));
        $manager->persist($levelB2);

        $levelB3 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelB();
        $propertyId->setValue($levelB3, 3);
        $levelB3->setName('Test BA');
        $levelB3->setParent($this->getReference('LevelA-2'));
        $manager->persist($levelB3);
        
        $manager->flush();
        
        $this->addReference('LevelB-1', $levelB1);
        $this->addReference('LevelB-2', $levelB2);
        $this->addReference('LevelB-3', $levelB3);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 2;
    }
    
}