<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Copie;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCopieData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateR = new \DateTime();
        $dateR->setDate(2017, 1, 2);
        $ress = $this->createItem($manager,
            $this->getReference('user_etudiant_1'),
            $this->getReference('dev_cours_alg_2'),
            $dateR
            );
        $this->addReference('copie_dev_cours_alg_2', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $user, $devoir, $dateCrea){
        $item = new Copie();
        $item->setAuteur($user);
        $item->setDevoir($devoir);
        $item->setDateCreation($dateCrea);
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
        return 15;
    }
}