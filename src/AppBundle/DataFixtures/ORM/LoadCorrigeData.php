<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Copie;
use AppBundle\Entity\Corrige;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCorrigeData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateR = new \DateTime();
        $dateR->setDate(2017, 1, 5);
        $ress = $this->createItem($manager,
            $this->getReference('user_enseignant_1'),
            $this->getReference('copie_dev_cours_alg_1'),
            $dateR
            );
        $this->addReference('corrige_copie_dev_cours_alg_1', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $user, $copie, $dateRendu){
        $item = new Corrige();
        $item->setAuteur($user);
        $item->setCopie($copie);
        $item->setDateRendu($dateRendu);
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
        return 16;
    }
}