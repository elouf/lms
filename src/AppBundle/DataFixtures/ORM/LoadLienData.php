<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Lien;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadLienData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'Lien Analyse',
            'description du lien 1',
            $this->getReference('cours_alg'),
            'http://www.google.fr',
            $this->getReference('typelien_http'));
        $this->addReference('ress_cours_alg_1', $ress);

        $ress = $this->createItem($manager,
            'Lien Alg2',
            'description du lien 2',
            $this->getReference('cours_alg'),
            'http://www.google.fr',
            $this->getReference('typelien_pdf'));
        $this->addReference('ress_cours_alg_2', $ress);

        $ress = $this->createItem($manager,
            'Lien Alg3',
            'description du lien 3',
            $this->getReference('cours_alg'),
            'http://www.google.fr',
            $this->getReference('typelien_webmedia'));
        $this->addReference('ress_cours_alg_3', $ress);

        $ress = $this->createItem($manager,
            'Lien Alg4',
            'description du lien 4',
            $this->getReference('cours_alg'),
            'http://www.google.fr',
            $this->getReference('typelien_opale'));
        $this->addReference('ress_cours_alg_4', $ress);

        $ress = $this->createItem($manager,
            'Lien Analyse1',
            'description du lien 1',
            $this->getReference('cours_analyse'),
            'http://www.google.fr',
            $this->getReference('typelien_opale'));
        $this->addReference('ress_cours_ana_1', $ress);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $description, $cours, $url, $typeLien){
        $item = new Lien();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setDescription($description);
        $item->setUrl($url);
        $item->setTypeLien($typeLien);
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
        return 12;
    }
}