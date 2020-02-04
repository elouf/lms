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
        $cours = $this->createItem($manager,
            'Algèbre',
            'Cours d\'algèbre du second degré',
            '<p>Vous disposez ici de ressources d\'entraînement aux écrits du concours, sous différentes formes de
                difficulté progressive pour vous aider à travailler en autonomie :</p>
            <ul class="list-default">
                <li>Des exercices de mise en route</li>
                <li>Des exercices niveau Concours</li>
                <li>Des sujets format concours</li>
            </ul>
            <p>Tous les exercices et sujets disposent bien sûr de leur corrigé, écrit ou vidéo. Pour certains exercices
            plus difficiles, vous disposez aussi d\'indices aidant à la résolution, si besoin. Attention: Les formats
            des sujets ont changé, les annales permettent de s\'entraîner surtout à l\'épreuve 1 "Résolution de
            problème". Tenez en compte dans vos révisions. Bonne découverte !</p>',
            'disciplines/disc_maths.png',
            $this->getReference('disc_excellencePro'),
            0);
        $this->addReference('cours_alg', $cours);
        $cours = $this->createItem($manager,
            'Analyse',
            'Cours d\'analyse du second degré',
            'accueil',
            'disciplines/disc_maths.png',
            $this->getReference('disc_excellencePro'),
            0);
        $this->addReference('cours_analyse', $cours);
        $cours = $this->createItem($manager,
            'Littérature',
            'Cours de Littérature du second degré',
            'accueil',
            'disciplines/disc_lettres.png',
            $this->getReference('disc_excellencePro'),
            0);
        $this->addReference('cours_litt', $cours);
        $cours = $this->createItem($manager,
            'Langue Française',
            'Cours de Langue Française du second degré',
            'accueil',
            'disciplines/disc_lettres.png',
            $this->getReference('disc_excellencePro'),
            0);
        $this->addReference('cours_lFra', $cours);
        $cours = $this->createItem($manager,
            'Biologie',
            'Cours de Biologie du second degré',
            'accueil',
            'disciplines/disc_svt.png',
            $this->getReference('disc_excellencePro'),
            0);
        $this->addReference('cours_bio', $cours);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $accueil, $imageFilename, $disc, $cout){
        $item = new Cours();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setAccueil($accueil);
        $item->setImageFilename($imageFilename);
        $item->setCout($cout);
        $item->setDiscipline($disc);
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
        return 2;
    }
}