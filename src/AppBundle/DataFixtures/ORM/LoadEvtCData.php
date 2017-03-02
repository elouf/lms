<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Evt_cours;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadEvtCData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateD = new \DateTime();
        $dateD->setDate(2017, 3, 1);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 3, 10);
        $evt = $this->createItem($manager,
            'évènement de cours 1',
            "description de l'évènement 1",
            $dateD,
            $dateF,
            $this->getReference('cours_alg')
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $dateD, $dateF, $cours){
        $item = new Evt_cours();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setDateDebut($dateD);
        $item->setDateFin($dateF);
        $item->setCours($cours);
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
        return 30;
    }
}