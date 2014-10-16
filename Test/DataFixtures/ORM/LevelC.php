<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LevelC extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $class = new \ReflectionClass('SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC');
        $propertyId = $class->getProperty('id');
        $propertyId->setAccessible(true);

        $metadata = $manager->getClassMetaData("SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC");
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new \Doctrine\ORM\Id\AssignedGenerator());
        
        $levelC1 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC();
        $propertyId->setValue($levelC1, 1);
        $levelC1->setName('Test AAA');
        $levelC1->setParent($this->getReference('LevelB-1'));
        $manager->persist($levelC1);
        
        $levelC2 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC();
        $propertyId->setValue($levelC2, 2);
        $levelC2->setName('Test AAB');
        $levelC2->setParent($this->getReference('LevelB-1'));
        $manager->persist($levelC2);
        
        $levelC3 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC();
        $propertyId->setValue($levelC3, 3);
        $levelC3->setName('Test ABA');
        $levelC3->setParent($this->getReference('LevelB-2'));
        $manager->persist($levelC3);
        
        $levelC4 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC();
        $propertyId->setValue($levelC4, 4);
        $levelC4->setName('Test BAA');
        $levelC4->setParent($this->getReference('LevelB-3'));
        $manager->persist($levelC4);
        
        $levelC5 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC();
        $propertyId->setValue($levelC5, 5);
        $levelC5->setName('Test BAB');
        $levelC5->setParent($this->getReference('LevelB-3'));
        $manager->persist($levelC5);
        
        $levelC6 = new \SDLab\Bundle\SmartUtilsBundle\Test\Entity\LevelC();
        $propertyId->setValue($levelC6, 6);
        $levelC6->setName('Test BAC');
        $levelC6->setParent($this->getReference('LevelB-3'));
        $manager->persist($levelC6);
        
        $manager->flush();
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
    
}