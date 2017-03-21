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
        //Second degré
        $disc = $this->createItem($manager,
            'Mathématiques',
            'La discipline de Mathématiques du second degré',
            'images/disc_maths.png');
        $this->addReference('disc_maths', $disc);
        $disc = $this->createItem($manager,
            'Lettres Modernes',
            'La discipline de Lettres Modernes du second degré',
            'images/disc_lettres.png');
        $this->addReference('disc_lettres', $disc);
        $disc = $this->createItem($manager,
            'SVT',
            'La discipline de SVT du second degré',
            'images/disc_svt.png');
        $this->addReference('disc_svt', $disc);
        $disc = $this->createItem($manager,
            'Histoire-Géographie',
            'La discipline d\'Histoire-Géographie du second degré',
            'images/disc_hist.png');
        $this->addReference('disc_hist', $disc);
        $disc = $this->createItem($manager,
            'Physique-Chimie',
            'La discipline de Physique-Chimie du second degré',
            'images/disc_phy.png');
        $this->addReference('disc_phy', $disc);
        $disc = $this->createItem($manager,
            'Anglais',
            'La discipline d\'Anglais du second degré',
            'images/disc_anglais.png');
        $this->addReference('disc_angl', $disc);
        $disc = $this->createItem($manager,
            'Espagnol',
            'La discipline d\'Espagnol du second degré',
            'images/disc_esp.png');
        $this->addReference('disc_esp', $disc);

        //premier degré
        $disc = $this->createItem($manager,
            'Mathématiques',
            'La discipline de Mathématiques du premier degré',
            'images/disc_crpe_maths.png');
        $this->addReference('disc_crpe_maths', $disc);
        $disc = $this->createItem($manager,
            'Français',
            'La discipline de Français du premier degré',
            'images/disc_crpe_fra.png');
        $this->addReference('disc_crpe_fra', $disc);
        $disc = $this->createItem($manager,
            'Langues Tell me More',
            'La discipline des cours payants en ligne Tell Me More',
            'images/disc_anglais.png');
        $this->addReference('disc_tmm', $disc);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $imgFilePath){
        $item = new Discipline();
        $item->setNom($nom);
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