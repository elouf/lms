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

        if (file_exists('http://www.e-educmaster.com/chamilo/stdi/xml/donnees.xml')) {
            $xml = simplexml_load_file('http://www.e-educmaster.com/chamilo/stdi/xml/donnees.xml');
            $queryDisc = "SELECT * FROM course_category WHERE keepForStudit='1' ORDER by ID";
            if ($resultDisc = $this->getMysqli()->query($queryDisc)) {
                while ($disc = $resultDisc->fetch_object()) {
                    $oneDisc = $this->createDisc($manager,
                        explode('_', $disc->name)[0],
                        'La discipline ' . explode('_', $disc->name)[0],
                        'disciplines/' . explode('_', $disc->name)[1] . '.png');

                    $queryCours = "SELECT * FROM course WHERE category_code='" . $disc->code . "' AND keepForStudit='1'";

                    if ($resultCourse = $this->getMysqli()->query($queryCours)) {
                        while ($course = $resultCourse->fetch_object()) {
                            $cours = $this->createCours($manager,
                                $course->title,
                                'Cours de ' . $course->title,
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
                                'cours/' . $course->imgFilePath,
                                $oneDisc,
                                0);

                            foreach ($xml->children() as $xmlC) {
                                if ($xmlC['id'] == $course->id) {
                                    foreach ($xmlC->children() as $rub) {
                                        //$rub['nom']
                                        foreach ($rub->children() as $col) {
                                            foreach ($col->children() as $elem) {
                                                if ($elem->type == 'devoirs') {

                                                } elseif ($elem->type == 'Lien') {
                                                    echo '<tr data-idElem="' . $elem->id . '" data-type="lien">';
                                                    echo '<td>Lien</td>';
                                                    $queryLink = "SELECT * FROM c_link WHERE c_id='" . $course->id . "'
                                                    AND id='" . $elem->id . "'";
                                                    if ($resultLink = $this->getMysqli()->query($queryLink)) {
                                                        $link = $resultLink->fetch_object();
                                                        //$link->url   $link->title

                                                        if ($link->enabled == 0) {
                                                            echo '<td><div class="btn btnActivate">Garder</div></td>';
                                                        } else {
                                                            echo '<td><div class="btn btnDelete">Supprimer</div></td>';
                                                        }
                                                        if ($link->studitVisible == 0) {
                                                            echo '<td><div class="btn btnDisplay">Afficher</div></td>';
                                                        } else {
                                                            echo '<td><div class="btn btnHide">Masquer</div></td>';
                                                        }
                                                        $resultLink->close();
                                                    }

                                                } elseif ($elem->type == 'parcours') {
                                                    echo '<tr data-idElem="' . $elem->id . '" data-type="parcours">';
                                                    echo '<td>Parcours</td>';
                                                    $queryParc = "SELECT * FROM c_lp WHERE c_id='" . $course->id . "' AND id='" . $elem->id . "'";
                                                    if ($resultParc = $this->getMysqli()->query($queryParc)) {
                                                        $parc = $resultParc->fetch_object();

                                                        echo '<td><p>' . $parc->name . '</p>';

                                                        $queryParcItem = "SELECT * FROM c_lp_item WHERE c_id='" . $course->id . "' AND lp_id='" . $parc->id . "'";

                                                        if ($resultParcItem = $this->getMysqli()->query($queryParcItem)) {
                                                            echo '<ul>';
                                                            while ($pItem = $resultParcItem->fetch_object()) {
                                                                $queryLink = "SELECT * FROM c_link WHERE id='" . $pItem->path . "' AND c_id='" . $course->id . "'";

                                                                if ($resultLink = $this->getMysqli()->query($queryLink)) {
                                                                    $link = $resultLink->fetch_object();
                                                                    echo '<li><a target="_blank" href="' . $link->url . '">' . $pItem->title . '</a></li>';
                                                                } else {
                                                                    echo '<li>' . $pItem->title . '</li>';
                                                                }
                                                            }
                                                            echo '</ul>';
                                                            $resultParcItem->close();
                                                        }
                                                        echo '</td>';

                                                        if ($parc->enabled == 0) {
                                                            echo '<td><div class="btn btnActivate">Garder</div></td>';
                                                        } else {
                                                            echo '<td><div class="btn btnDelete">Supprimer</div></td>';
                                                        }
                                                        if ($parc->studitVisible == 0) {
                                                            echo '<td><div class="btn btnDisplay">Afficher</div></td>';
                                                        } else {
                                                            echo '<td><div class="btn btnHide">Masquer</div></td>';
                                                        }
                                                        $resultParc->close();
                                                    }
                                                }
                                                echo '</tr>';
                                            }
                                        }

                                        echo '</table></div></div>';
                                    }

                                }
                            }


                        }
                    }

                }

                $resultDisc->close();
            }
        } else {

        }

        $this->getMysqli()->close();

        $manager->flush();
    }

    public function createDisc(ObjectManager $manager, $nom, $descr, $imgFilePath)
    {
        $item = new Discipline();
        $item->setNom($nom);
        $item->setDescription($descr);
        $item->setImgFilePath($imgFilePath);
        $manager->persist($item);
        return $item;
    }

    public function createCours(ObjectManager $manager, $nom, $descr, $accueil, $imgFilePath, Discipline $disc, $cout)
    {
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