<?php

namespace AppBundle\DataFixtures\Common;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $role = $this->createItem($manager, 'Admin');
        $this->addReference('role_admin', $role);

        $role = $this->createItem($manager, 'Enseignant');
        $this->addReference('role_ens', $role);

        $role = $this->createItem($manager, 'Tuteur');
        $this->addReference('role_tut', $role);

        $role = $this->createItem($manager, 'Etudiant');
        $this->addReference('role_etu', $role);

        $role = $this->createItem($manager, 'Stagiaire');
        $this->addReference('role_stag', $role);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom){
        $item = new Role();
        $item->setNom($nom);
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
        return 8;
    }
}