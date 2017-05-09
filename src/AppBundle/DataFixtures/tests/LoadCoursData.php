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
            $this->getReference('disc_maths'),
            0);
        $this->addReference('cours_alg', $cours);
        $cours = $this->createItem($manager,
            'Analyse',
            'Cours d\'analyse du second degré',
            'accueil',
            'disciplines/disc_maths.png',
            $this->getReference('disc_maths'),
            0);
        $this->addReference('cours_analyse', $cours);
        $cours = $this->createItem($manager,
            'Littérature',
            'Cours de Littérature du second degré',
            'accueil',
            'disciplines/disc_lettres.png',
            $this->getReference('disc_lettres'),
            0);
        $this->addReference('cours_litt', $cours);
        $cours = $this->createItem($manager,
            'Langue Française',
            'Cours de Langue Française du second degré',
            'accueil',
            'disciplines/disc_lettres.png',
            $this->getReference('disc_lettres'),
            0);
        $this->addReference('cours_lFra', $cours);
        $cours = $this->createItem($manager,
            'Biologie',
            'Cours de Biologie du second degré',
            'accueil',
            'disciplines/disc_svt.png',
            $this->getReference('disc_svt'),
            0);
        $this->addReference('cours_bio', $cours);
        $cours = $this->createItem($manager,
            'Géologie',
            'Cours de Géologie du second degré',
            'accueil',
            'disciplines/disc_geol.png',
            $this->getReference('disc_svt'),
            0);
        $this->addReference('cours_geol', $cours);
        $cours = $this->createItem($manager,
            'Union Indienne',
            'Cours Union indienne du second degré',
            'accueil',
            'disciplines/disc_hist.png',
            $this->getReference('disc_hist'),
            0);
        $this->addReference('cours_unInd', $cours);
        $cours = $this->createItem($manager,
            'La France des marges',
            'Cours La France des marges du second degré',
            'accueil',
            'disciplines/disc_hist.png',
            $this->getReference('disc_hist'),
            0);
        $this->addReference('cours_frMarges', $cours);
        $cours = $this->createItem($manager,
            'Physique',
            'Cours de Physique du second degré',
            'accueil',
            'disciplines/disc_phy.png',
            $this->getReference('disc_phy'),
            0);
        $this->addReference('cours_phy', $cours);
        $cours = $this->createItem($manager,
            'Chimie',
            'Cours de Chimie du second degré',
            'accueil',
            'disciplines/disc_chi.png',
            $this->getReference('disc_phy'),
            0);
        $this->addReference('cours_chi', $cours);
        $cours = $this->createItem($manager,
            'Composition',
            'Cours d\'Anglais Composition du second degré',
            'accueil',
            'disciplines/disc_anglais.png',
            $this->getReference('disc_angl'),
            0);
        $this->addReference('cours_anglCompo', $cours);
        $cours = $this->createItem($manager,
            'Traduction',
            'Cours d\'Anglais Traduction du second degré',
            'accueil',
            'disciplines/disc_anglais.png',
            $this->getReference('disc_angl'),
            0);
        $this->addReference('cours_anglTrad', $cours);
        $cours = $this->createItem($manager,
            'Traduction',
            'Cours d\'Espagnol Traduction du second degré',
            'accueil',
            'disciplines/disc_esp.png',
            $this->getReference('disc_esp'),
            0);
        $this->addReference('cours_espTrad', $cours);
        $cours = $this->createItem($manager,
            'Composition',
            'Cours d\'Espagnol Composition du second degré',
            'accueil',
            'disciplines/disc_esp.png',
            $this->getReference('disc_esp'),
            0);
        $this->addReference('cours_espCompo', $cours);

        $cours = $this->createItem($manager,
            'Prépa concours',
            'Cours de Maths Prépa concours du premier degré',
            'accueil',
            'disciplines/disc_crpe_maths.png',
            $this->getReference('disc_crpe_maths'),
            0);
        $this->addReference('cours_crpe_maths_prepa', $cours);
        $cours = $this->createItem($manager,
            'Remédiation',
            'Cours de Maths Remédiation du premier degré',
            'accueil',
            'disciplines/disc_crpe_maths.png',
            $this->getReference('disc_crpe_maths'),
            0);
        $this->addReference('cours_crpe_maths_remed', $cours);
        $cours = $this->createItem($manager,
            'Prépa concours',
            'Cours de Français Prépa concours du premier degré',
            'accueil',
            'disciplines/disc_crpe_fra.png',
            $this->getReference('disc_crpe_fra'),
            0);
        $this->addReference('cours_crpe_fra_prepa', $cours);
        $cours = $this->createItem($manager,
            'Remédiation',
            'Cours de Français Remédiation du premier degré',
            'accueil',
            'disciplines/disc_crpe_fra.png',
            $this->getReference('disc_crpe_fra'),
            0);
        $this->addReference('cours_crpe_fra_remed', $cours);

        $cours = $this->createItem($manager,
            'Anglais B2',
            "Cours d'Anglais B2 (accès gratuit)",
            'accueil',
            'disciplines/disc_anglais.png',
            $this->getReference('disc_angl'),
            0);
        $this->addReference('cours_anglais_b2', $cours);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $accueil, $imgFilePath, $disc, $cout){
        $item = new Cours();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setAccueil($accueil);
        $item->setImgFilePath($imgFilePath);
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