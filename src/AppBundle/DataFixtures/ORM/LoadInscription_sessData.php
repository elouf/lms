<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Inscription_sess;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInscription_sessData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_1'),
            $this->getReference('sess_est_maths'));

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $role, $user, $session){
        $item = new Inscription_sess();
        $item->setUser($user);
        $item->setSession($session);
        $item->setDateInscription(new DateTime());
        $item->setRole($role);
        $manager->persist($item);
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
        return 9;
    }
}