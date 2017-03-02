<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Devoir;
use AppBundle\Entity\DevoirCorrigeType;
use AppBundle\Entity\DevoirSujet;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDevoirCorrigeTypeData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'www.google.fr',
            'corrigeType Fichier 1',
            $this->getReference('dev_cours_alg_1')
        );
        $this->addReference('devCorr_cours_alg_1', $ress);

        $ress = $this->createItem($manager,
            'www.google.fr',
            'corrigeType Fichier 2',
            $this->getReference('dev_cours_alg_2')
        );
        $this->addReference('devCorr_cours_alg_2', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $url, $nom, $devoir){
        $item = new DevoirCorrigeType();
        $item->setUrl($url);
        $item->setNom($nom);
        $item->setDevoir($devoir);
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
        return 14;
    }
}