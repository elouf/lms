<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Inscription_c;
use DateTime;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInscription_cData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_1'),
            $this->getReference('cours_espTrad'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_2'),
            $this->getReference('cours_phy'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_2'),
            $this->getReference('cours_bio'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_3'),
            $this->getReference('cours_espTrad'));
        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_3'),
            $this->getReference('cours_anglTrad'));

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $role, $user, $cours){
        $item = new Inscription_c();
        $item->setUser($user);
        $item->setCours($cours);
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