<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CategorieLien;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategorieLienData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'Intitule');
        $this->addReference('categorielien_intitule', $ress);
        $ress = $this->createItem($manager,
            'Aide');
        $this->addReference('categorielien_aide', $ress);
        $ress = $this->createItem($manager,
            'Corrige');
        $this->addReference('categorielien_corrige', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom){
        $item = new CategorieLien();
        $item->setNom($nom);
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
        return 1;
    }
}