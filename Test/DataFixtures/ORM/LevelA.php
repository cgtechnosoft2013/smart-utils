<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LevelA extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $class = new \ReflectionClass('SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelA');
        $propertyId = $class->getProperty('id');
        $propertyId->setAccessible(true);

        $metadata = $manager->getClassMetaData("SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelA");
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        
        $levelA1 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelA();
        $propertyId->setValue($levelA1, 1);
        $levelA1->setName('Test A');
        $manager->persist($levelA1);
        
        $levelA2 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelA();
        $propertyId->setValue($levelA2, 2);
        $levelA2->setName('Test B');
        $manager->persist($levelA2);

        $manager->flush();
        
        $this->addReference('LevelA-1', $levelA1);
        $this->addReference('LevelA-2', $levelA2);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
    
}