<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Discipline;
use AppBundle\Entity\Session;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadSessionData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateD = new \DateTime();
        $dateD->setDate(2017, 5, 10);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 6, 5);
        $dateDAl = new \DateTime();
        $dateDAl->setDate(2017, 5, 5);
        $dateFAl = new \DateTime();
        $dateFAl->setDate(2017, 5, 15);
        $session = $this->createItem($manager,
            'Session estivale 2017',
            $dateD,
            $dateF,
            $dateDAl,
            $dateFAl,
            'Une session estivale commencera bientôt, soyez prêts');
        $this->addReference('sess_est_2017', $session);
        $this->getReference('cours_alg_esti')->setSession($session);

        $dateD = new \DateTime();
        $dateD->setDate(2017, 9, 10);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 11, 5);
        $dateDAl = new \DateTime();
        $dateDAl->setDate(2017, 5, 5);
        $dateFAl = new \DateTime();
        $dateFAl->setDate(2017, 9, 15);
        $session = $this->createItem($manager,
            'Session automnale 2017',
            $dateD,
            $dateF,
            $dateDAl,
            $dateFAl,
            'Une session automnale commencera bientôt, soyez prêts');
        $this->addReference('sess_aut_2017', $session);
        $this->getReference('cours_alg_aut')->setSession($session);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom,
                               $dateD, $dateF, $dateDAl, $dateFAl, $messageAl){
        $item = new Session();
        $item->setNom($nom);
        $item->setDateDebut($dateD);
        $item->setDateFin($dateF);
        $item->setDateDebutAlerte($dateDAl);
        $item->setDateFinAlerte($dateFAl);
        $item->setMessageAlerte($messageAl);
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