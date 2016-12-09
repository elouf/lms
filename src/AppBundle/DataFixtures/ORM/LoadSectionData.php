<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Section;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadSectionData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $section = $this->createItem($manager,
            'Exercices de mise en route',
            $this->getReference('cours_alg'),
            0);
        $this->addReference('sect_cours_alg_1', $section);
        $section = $this->createItem($manager,
            'Corrigés des exercices de mise en route',
            $this->getReference('cours_alg'),
            1);
        $this->addReference('sect_cours_alg_2', $section);
        $section = $this->createItem($manager,
            'Exercices niveau concours',
            $this->getReference('cours_alg'),
            2);
        $this->addReference('sect_cours_alg_3', $section);
        $section = $this->createItem($manager,
            'Corrigés des exercices niveau concours',
            $this->getReference('cours_alg'),
            3);
        $this->addReference('sect_cours_alg_4', $section);
        $section = $this->createItem($manager,
            'Sujets format concours',
            $this->getReference('cours_alg'),
            4);
        $this->addReference('sect_cours_alg_5', $section);
        $section = $this->createItem($manager,
            'Corrigés des sujets format concours',
            $this->getReference('cours_alg'),
            5);
        $this->addReference('sect_cours_alg_6', $section);
        $section = $this->createItem($manager,
            'Annales',
            $this->getReference('cours_alg'),
            6);
        $this->addReference('sect_cours_alg_7', $section);
        $section = $this->createItem($manager,
            'Bibliographie',
            $this->getReference('cours_alg'),
            7);
        $this->addReference('sect_cours_alg_8', $section);
        $section = $this->createItem($manager,
            'Rapport de jury',
            $this->getReference('cours_alg'),
            8);
        $this->addReference('sect_cours_alg_9', $section);

        $section = $this->createItem($manager,
            'Section1',
            $this->getReference('cours_analyse'),
            0);
        $this->addReference('sect_cours_analyse_1', $section);
        $section = $this->createItem($manager,
            'Section2',
            $this->getReference('cours_analyse'),
            1);
        $this->addReference('sect_cours_analyse_2', $section);
        $section = $this->createItem($manager,
            'Section3',
            $this->getReference('cours_analyse'),
            2);
        $this->addReference('sect_cours_analyse_3', $section);
        $section = $this->createItem($manager,
            'Section4',
            $this->getReference('cours_analyse'),
            3);
        $this->addReference('sect_cours_analyse_4', $section);
        $section = $this->createItem($manager,
            'Section5',
            $this->getReference('cours_analyse'),
            4);
        $this->addReference('sect_cours_analyse_5', $section);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $cours, $position){
        $item = new Section();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setIsVisible(true);
        $item->setPictoFilePath('/toto/la/frite.png');
        $item->setPosition($position);
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
        return 11;
    }
}