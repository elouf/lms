<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Inscription_coh;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInscription_cohData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_1'),
            $this->getReference('coh_maths'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_1'),
            $this->getReference('coh_phy'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_6'),
            $this->getReference('coh_crpe'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_6'),
            $this->getReference('coh_esp'));



        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $role, $user, $cohorte){
        $item = new Inscription_coh();
        $item->setUser($user);
        $item->setCohorte($cohorte);
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
        return 10;
    }
}