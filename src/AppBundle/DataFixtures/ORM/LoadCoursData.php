<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Discipline;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadCoursData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $this->createItem($manager,
            'Mathématiques',
            'La discipline de Mathématiques du second degré', '
            images/disc_maths.png',
            $this->getReference('disc_maths'),
            0);
        $this->createItem($manager,
            'Lettres Modernes',
            'La discipline de Lettres Modernes du second degré',
            'images/disc_lettres.png',
            $this->getReference('disc_lettres'),
            0);
        $this->createItem($manager,
            'SVT',
            'La discipline de SVT du second degré',
            'images/disc_svt.png',
            $this->getReference('disc_svt'),
            0);
        $this->createItem($manager,
            'Histoire-Géographie',
            'La discipline d\'Histoire-Géographie du second degré',
            'images/disc_hist.png',
            $this->getReference('disc_hist'),
            0);
        $this->createItem($manager,
            'Physique-Chimie',
            'La discipline de Physique-Chimie du second degré',
            'images/disc_phy.png',
            $this->getReference('disc_phy'),
            0);
        $this->createItem($manager,
            'Anglais',
            'La discipline d\'Anglais du second degré',
            'images/disc_anglais.png',
            $this->getReference('disc_angl'),
            0);
        $this->createItem($manager,
            'Espagnol',
            'La discipline d\'Espagnol du second degré',
            'images/disc_esp.png',
            $this->getReference('disc_esp'),
            0);

        $this->createItem($manager,
            'Mathématiques',
            'La discipline de Mathématiques du premier degré',
            'images/disc_crpe_maths.png',
            $this->getReference('disc_crpe_maths'),
            0);
        $this->createItem($manager,
            'Français',
            'La discipline de Français du premier degré',
            'images/disc_crpe_fra.png',
            $this->getReference('disc_crpe_fra'),
            0);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $imgFilePath, Discipline $disc, $cout){
        $item = new Cours();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setImgFilePath($imgFilePath);
        $item->setCout($cout);
        $item->setDiscipline($disc);
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
        return 2;
    }
}