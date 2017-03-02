<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Evt_user;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEvtUData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateD = new \DateTime();
        $dateD->setDate(2017, 3, 9);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 3, 10);
        $evt = $this->createItem($manager,
            'évènement utilisateur 1',
            "description de l'évènement 1",
            $dateD,
            $dateF,
            $this->getReference('user_etudiant_1')
        );

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $dateD, $dateF, $user){
        $item = new Evt_user();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setDateDebut($dateD);
        $item->setDateFin($dateF);
        $item->setUSer($user);
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
        return 32;
    }
}