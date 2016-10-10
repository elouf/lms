<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cohorte;

class LoadCohorteData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            'Mathématiques');
        $this->createItem($manager,
            'Lettres Modernes');
        $this->createItem($manager,
            'SVT');
        $this->createItem($manager,
            'Histoire-Géographie');
        $this->createItem($manager,
            'Physique-Chimie');
        $this->createItem($manager,
            'Anglais');
        $this->createItem($manager,
            'Espagnol');

        $this->createItem($manager,
            'crpe');
        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom){
        $item = new Cohorte();
        $item->setNom($nom);
        $manager->persist($item);
    }

}