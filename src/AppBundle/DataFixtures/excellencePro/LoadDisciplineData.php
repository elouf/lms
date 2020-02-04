<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Discipline;

class LoadDisciplineData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $disc = $this->createItem($manager,
            'ExcellencePro',
            'La discipline qui rassemble les ressources pour la plateforme Excellence Pro',
            'images/disc_maths.png',
            'EXCELLENCEPRO'
        );
        $this->addReference('disc_excellencePro', $disc);
        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $imgFilePath, $accronym){
        $item = new Discipline();
        $item->setNom($nom);
        $item->setAccronyme($accronym);
        $item->setDescription($descr);
        $item->setImgFilePath($imgFilePath);
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
        return 1;
    }
}