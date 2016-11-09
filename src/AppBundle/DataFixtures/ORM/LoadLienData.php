<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Lien;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadRessourceData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'RessourceAlg1',
            'description de la ressource 1',
            $this->getReference('cours_alg'),
            'http://www.google.fr');
        $this->addReference('ress_cours_alg_1', $ress);
        $ress = $this->createItem($manager,
            'RessourceAlg2',
            'description de la ressource 2',
            $this->getReference('cours_alg'),
            'http://www.google.fr');
        $this->addReference('ress_cours_alg_2', $ress);
        $ress = $this->createItem($manager,
            'RessourceAlg3',
            'description de la ressource 3',
            $this->getReference('cours_alg'),
            'http://www.google.fr');
        $this->addReference('ress_cours_alg_3', $ress);
        $ress = $this->createItem($manager,
            'RessourceAlg4',
            'description de la ressource 4',
            $this->getReference('cours_alg'),
            'http://www.google.fr');
        $this->addReference('ress_cours_alg_4', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours, $url){
        $item = new Lien();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setDescription($description);
        $item->setUrl($url);
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