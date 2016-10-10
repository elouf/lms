<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $tabInst = [$this->getReference('inst_paris'),
        $this->getReference('inst_nantes'),
        $this->getReference('inst_rennes'),
        $this->getReference('inst_angers'),
        $this->getReference('inst_lille')];

        $this->createItem($manager,
            'admin',
            'test',
            'erwannig.louf@gmail.com',
            $this->getReference('inst_paris'));

        $this->boucleTypeUser($manager, "etudiant", 20, $tabInst);
        $this->boucleTypeUser($manager, "stagiaire", 10, $tabInst);
        $this->boucleTypeUser($manager, "enseignant", 10, $tabInst);


        $manager->flush();
    }

    public function boucleTypeUser(ObjectManager $manager, $intituleUser, $nbUser, $tabInst){
        for($i=0; $i<$nbUser; $i++){
            $inst = $tabInst[mt_rand(0, count($tabInst)-1)];
            $this->createItem($manager,
                $intituleUser.$i,
                'test',
                $intituleUser.$i.'@test.com',
                $inst);
        }
    }

    public function createItem(ObjectManager $manager, $username, $password, $email, $institut){
        $item = new User();
        $item->setUsername($username);
        $item->setPassword($password);
        $item->setEmail($email);
        $item->setInstitut($institut);
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
        return 7;
    }
}