<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\GroupeLiens;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadGroupeLiensData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'Exercice : entiers de Gauss',
            'Avec cet exercice, testez vos connaissances sur les entiers de Gauss.',
            $this->getReference('cours_alg'));
        $this->addReference('groupeLiens_cours_alg_1', $ress);
        $ress = $this->createItem($manager,
            'Exercice : anneaux et corps',
            'Avec cet exercice, testez vos connaissances sur les anneaux et corps.',
            $this->getReference('cours_alg'));
        $this->addReference('groupeLiens_cours_alg_2', $ress);
        $ress = $this->createItem($manager,
            'Exercice : équation diophantienne',
            'Avec cet exercice, testez vos connaissances sur les équations diophantiennes.',
            $this->getReference('cours_alg'));
        $this->addReference('groupeLiens_cours_alg_3', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours){
        $item = new GroupeLiens();
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
        return 19;
    }
}