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
        $inscr = $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_1'),
            $this->getReference('coh_maths'));
        $this->addReference('inscr_01', $inscr);

        $inscr = $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_1'),
            $this->getReference('coh_phy'));
        $this->addReference('inscr_02', $inscr);

        $inscr = $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_4'),
            $this->getReference('coh_maths'));
        $this->addReference('inscr_03', $inscr);

        $this->createItem($manager,
            $this->getReference('role_etu'),
            $this->getReference('user_etudiant_6'),
            $this->getReference('coh_esp'));

        $this->createItem($manager,
            $this->getReference('role_ens'),
            $this->getReference('user_enseignant_1'),
            $this->getReference('coh_maths'));

        for ($i = 10; $i < 30; $i++) {
            $cohNames = array('coh_phy', 'coh_maths', 'coh_esp');
            $randCoh = $this->getReference($cohNames[mt_rand(0, count($cohNames)-1)]);
            $inscr = $this->createItem($manager,
                $this->getReference('role_etu'),
                $this->getReference('user_etudiant_' . $i),
                $randCoh);
            $this->addReference('inscr_auto' . $i, $inscr);
        }


        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $role, $user, $cohorte)
    {
        $item = new Inscription_coh();
        $item->setUser($user);
        $item->setCohorte($cohorte);
        $item->setDateInscription(new DateTime());
        $item->setRole($role);
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
        return 10;
    }
}