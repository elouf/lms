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
            $this->getReference('groupeLiens_cours_alg_1'),
            true,
            "",
            0);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('groupeLiens_cours_alg_2'),
            true,
            "",
            1);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('groupeLiens_cours_alg_3'),
            true,
            "",
            2);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('forum_alg_1'),
            true,
            "",
            3);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_1'),
            $this->getReference('forum_alg_2'),
            true,
            "",
            4);

        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('dev_cours_alg_1'),
            true,
            "",
            0);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('ressLib_cours_alg_1'),
            true,
            "",
            1);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('ressLib_cours_alg_2'),
            true,
            "",
            2);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('ressLib_cours_alg_1'),
            true,
            "",
            3);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_2'),
            $this->getReference('ressLib_cours_alg_2'),
            true,
            "",
            4);

        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_3'),
            $this->getReference('ress_cours_alg_1'),
            true,
            "",
            0);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_3'),
            $this->getReference('ress_cours_alg_2'),
            true,
            "",
            1);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_3'),
            $this->getReference('dev_cours_alg_2'),
            true,
            "",
            2);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_3'),
            $this->getReference('ress_cours_alg_3'),
            true,
            "",
            3);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_3'),
            $this->getReference('ress_cours_alg_1'),
            true,
            "",
            4);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_alg_3'),
            $this->getReference('ress_cours_alg_2'),
            true,
            "",
            5);

        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_analyse_1'),
            $this->getReference('ress_cours_ana_1'),
            true,
            "",
            0);
        $ress = $this->createItem($manager,
            $this->getReference('sect_cours_analyse_1'),
            $this->getReference('forum_analyse_1'),
            true,
            "",
            0);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $section, $ressource, $isVisible, $description, $position){
        $item = new ZoneRessource();
        $item->setSection($section);
        if($ressource != null){
            $item->setRessource($ressource);
        }
        $item->setIsVisible($isVisible);
        $item->setPosition($position);
        $item->setDescription($description);
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
        return 21;
    }
}