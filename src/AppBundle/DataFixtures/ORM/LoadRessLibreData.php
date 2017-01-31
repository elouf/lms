<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\RessourceLibre;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadRessourceLibreData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'Ressource Libre 1',
            'description de la ressource libre 1',
            $this->getReference('cours_alg'));
        $this->addReference('ressLib_cours_alg_1', $ress);

        $ress = $this->createItem($manager,
            'Ressource Libre 2',
            'description de la ressource libre 2',
            $this->getReference('cours_alg'));
        $this->addReference('ressLib_cours_alg_2', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours){
        $item = new RessourceLibre();
        $item->setNom($nom);
        $item->setCours($cours);
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
        return 12;
    }
}