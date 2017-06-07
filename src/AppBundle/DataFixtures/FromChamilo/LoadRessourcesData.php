<?php

namespace AppBundle\DataFixtures\FromChamilo;

use AppBundle\Entity\Discipline;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;

class LoadRessourcesData extends LoadChamiloConnect implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $queryDisc = "SELECT * FROM course_category WHERE keepForStudit='1' ORDER by ID";
        if ($resultDisc = $this->getMysqli()->query($queryDisc)) {
            while ($disc = $resultDisc->fetch_object()) {
                $oneDisc = $this->createDisc($manager,
                    explode('_', $disc->name)[0],
                    'La discipline '.explode('_', $disc->name)[0],
                    'disciplines/'.explode('_', $disc->name)[1].'.png');

                $queryCours = "SELECT * FROM course WHERE category_code='".$disc->code."' AND keepForStudit='1'";

                if ($resultCourse = $this->getMysqli()->query($queryCours)) {
                    while ($course = $resultCourse->fetch_object()) {
                        $cours = $this->createCours($manager,
                            $course->title,
                            'Cours de '.$course->title,
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
                            'cours/'.$course->imgFilePath,
                            $oneDisc,
                            0);

                    }
                }

            }

            $resultDisc->close();
        }

        $this->getMysqli()->close();

        $manager->flush();
    }

    public function createCours(ObjectManager $manager, $nom, $descr, $accueil, $imgFilePath, Discipline $disc, $cout){
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

    public function createDisc(ObjectManager $manager, $nom, $descr, $imgFilePath){
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
        return 2;
    }
}