<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Discipline;
use AppBundle\Entity\Session;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadSessionData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $dateD = new \DateTime();
        $dateD->setDate(2017, 5, 10);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 6, 5);
        $dateDAl = new \DateTime();
        $dateDAl->setDate(2017, 5, 5);
        $dateFAl = new \DateTime();
        $dateFAl->setDate(2017, 5, 15);
        $session = $this->createItem($manager,
            'Session estivale Mathématique 2017',
            'Session estivale de mathématique du second degré, accessible entre juillet et Septembre 2017',
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
            0,
            $dateD,
            $dateF,
            $dateDAl,
            $dateFAl,
            'Une session estivale de mathématique commencera bientôt, soyez prêts');
        $this->addReference('sess_est_maths', $session);

        $dateD = new \DateTime();
        $dateD->setDate(2017, 9, 10);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 11, 5);
        $dateDAl = new \DateTime();
        $dateDAl->setDate(2017, 9, 5);
        $dateFAl = new \DateTime();
        $dateFAl->setDate(2017, 9, 15);
        $session = $this->createItem($manager,
            'Session automnale Mathématique 2017',
            'Session automnale de mathématique du second degré, accessible entre octobre et Décembre 2017',
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
            0,
            $dateD,
            $dateF,
            $dateDAl,
            $dateFAl,
            'Une session estivale de mathématique commencera bientôt, soyez prêts');
        $this->addReference('sess_aut_maths', $session);

        $dateD = new \DateTime();
        $dateD->setDate(2017, 5, 10);
        $dateF = new \DateTime();
        $dateF->setDate(2017, 6, 5);
        $dateDAl = new \DateTime();
        $dateDAl->setDate(2017, 5, 5);
        $dateFAl = new \DateTime();
        $dateFAl->setDate(2017, 5, 15);
        $session = $this->createItem($manager,
            'Session estivale Physique 2017',
            'Session estivale de Physique du second degré, accessible entre juillet et Septembre 2017',
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
            $this->getReference('disc_phy'),
            0,
            $dateD,
            $dateF,
            $dateDAl,
            $dateFAl,
            'Une session estivale de mathématique commencera bientôt, soyez prêts');
        $this->addReference('sess_est_phy', $session);

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $nom, $descr, $accueil, $imgFilePath,
                               $disc, $cout, $dateD, $dateF, $dateDAl, $dateFAl, $messageAl){
        $item = new Session();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setAccueil($accueil);
        $item->setImgFilePath($imgFilePath);
        $item->setCout($cout);
        $item->setDiscipline($disc);
        $item->setDateDebut($dateD);
        $item->setDateFin($dateF);
        $item->setDateDebutAlerte($dateDAl);
        $item->setDateFinAlerte($dateFAl);
        $item->setMessageAlerte($messageAl);
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