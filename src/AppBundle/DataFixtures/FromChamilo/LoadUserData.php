<?php

namespace AppBundle\DataFixtures\Common;

use AppBundle\DataFixtures\FromChamilo\LoadChamiloConnect;
use AppBundle\Entity\Institut;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends LoadChamiloConnect implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            'jeSuisAfadec2017',
            'Admin',
            'AFADEC',
            'contact.afadec@gmail.com',
            null,
            true);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $password, $firstname, $lastname, $email, $institut, $isSuperAdmin){
        $item = new User();
        $item->setUsername($email);
        $item->setPlainPassword($password);
        $item->setFirstname($firstname);
        $item->setLastname($lastname);
        $item->setEmail($email);
        if($institut != null){
            $item->setInstitut($institut);
        }
        $item->setEnabled(true);
        $item->setSuperAdmin($isSuperAdmin);
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
        return 7;
    }
}