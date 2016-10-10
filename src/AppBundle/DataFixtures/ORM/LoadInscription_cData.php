<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Inscription_c;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadInscription_cData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /*$this->createItem($manager,
            'admin',
            'test',
            'erwannig.louf@gmail.com',
            $this->getReference('inst_paris'));
        $this->getReference('disc_angl');
        $manager->flush();*/
    }

    public function createItem(ObjectManager $manager, $username, $password, $email, $institut){
      /*  $item = new Inscription_c();
        $item->setUser();
        $item->setCours();
        $item->setDateInscription();
        $item->setRole();
        $manager->persist($item);*/
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
        return 7;
    }
}