<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ZoneRessource;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadZoneRessourceData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('ress_cours_alg_1'),
            0);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('ress_cours_alg_2'),
            1);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('ress_cours_alg_3'),
            2);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('ress_cours_alg_1'),
            0);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('ress_cours_alg_2'),
            1);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $section, $ressource, $position){
        $item = new ZoneRessource();
        $item->setSection($section);
        $item->setRessource($ressource);
        $item->setPosition($position);
        $manager->persist($item);
        return $item;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 13;
    }
}