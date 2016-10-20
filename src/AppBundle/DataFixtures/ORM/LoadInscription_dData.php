<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Inscription_d;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInscription_dData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_4'),
            $this->getReference('disc_phy'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_4'),
            $this->getReference('disc_svt'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_5'),
            $this->getReference('disc_crpe_maths'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_5'),
            $this->getReference('disc_crpe_fra'));

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $role, $user, $disc){
        $item = new Inscription_d();
        $item->setUser($user);
        $item->setDiscipline($disc);
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