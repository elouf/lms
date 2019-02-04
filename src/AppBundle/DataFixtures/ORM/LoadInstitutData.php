<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Institut;

class LoadInstitutData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $disc = $this->createItem($manager,
            'ISFEC Lille',
            'Lille');
        $this->addReference('inst_6', $disc);
        $disc = $this->createItem($manager,
            'ISFEC Paris',
            'Paris');
        $this->addReference('inst_7', $disc);
        $disc = $this->createItem($manager,
            'ISFEC Nantes',
            'Nantes');
        $this->addReference('inst_8', $disc);
        $disc = $this->createItem($manager,
            'ISFEC Rennes',
            'Rennes');
        $this->addReference('inst_9', $disc);
        $disc = $this->createItem($manager,
            'ISFEC Angers',
            'Angers');
        $this->addReference('inst_10', $disc);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $ville){
        $item = new Institut();
        $item->setNom($nom);
        $item->setVille($ville);
        $item->setActif(true);
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
        return 3;
    }
}