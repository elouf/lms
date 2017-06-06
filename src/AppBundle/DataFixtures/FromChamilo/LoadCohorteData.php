<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cohorte;

class LoadCohorteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $coh = $this->createItem($manager, 'Allemand');
        $coh = $this->createItem($manager, 'Anglais');
        $coh = $this->createItem($manager, 'ArtsAppl');
        $coh = $this->createItem($manager, 'ArtsPlast');
        $coh = $this->createItem($manager, 'Documentation');
        $coh = $this->createItem($manager, 'EcoGest');
        $coh = $this->createItem($manager, 'EducMusChantChoral');
        $coh = $this->createItem($manager, 'EMCC');
        $coh = $this->createItem($manager, 'Espagnol');
        $coh = $this->createItem($manager, 'HG');
        $coh = $this->createItem($manager, 'Lettres');
        $coh = $this->createItem($manager, 'Maths');
        $coh = $this->createItem($manager, 'Philosophie');
        $coh = $this->createItem($manager, 'PhyChi');
        $coh = $this->createItem($manager, 'SES');
        $coh = $this->createItem($manager, 'SIgenie');
        $coh = $this->createItem($manager, 'STMS');
        $coh = $this->createItem($manager, 'SVT');

        $coh = $this->createItem($manager, 'crpe');
        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom){
        $item = new Cohorte();
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
        return 3;
    }
}