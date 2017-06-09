<?php

namespace AppBundle\DataFixtures\FromChamilo;

use AppBundle\Entity\Discipline;
use AppBundle\Entity\Lien;
use AppBundle\Entity\Section;
use AppBundle\Entity\ZoneRessource;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Cours;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class LoadRessourcesData extends LoadChamiloConnect implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $donneesXMLfile = 'http://www.e-educmaster.com/chamilo/stdi/xml/donnees.xml';
        $file_headers = @get_headers($donneesXMLfile);
        $output = new ConsoleOutput();
        $progress = new ProgressBar($output, 50);
        $progress->start();
        if($file_headers[0] == 'HTTP/1.0 404 Not Found'){
            echo "The file $donneesXMLfile does not exist\n";
        } else if ($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found'){
            echo "The file $donneesXMLfile does not exist, and I got redirected to a custom 404 page..\n";
        } else {
            $xml = simplexml_load_file($donneesXMLfile);
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
                            $courseTitle = $course->title;

                            if($course->studitNewName != ''){
                                $courseTitle = $course->studitNewName;
                            }

                            $cours = $this->createCours($manager,
                                $courseTitle,
                                '',
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
                                    $numSection = 0;
                                    foreach ($xmlC->children() as $rub) {
                                        $section = $this->createSection($manager, $rub['nom'], $cours, $numSection);
                                        $numSection++;
                                        $numRess = 0;

                                        foreach ($rub->children() as $col) {

                                            foreach ($col->children() as $elem) {
                                                $progress->advance();
                                                if ($elem->type == 'devoirs') {

                                                } elseif ($elem->type == 'Lien') {
                                                    $queryLink = "SELECT * FROM c_link WHERE c_id='" . $course->id . "' AND id='" . $elem->id . "'";

                                                    if ($resultLink = $this->getMysqli()->query($queryLink)) {
                                                        $link = $resultLink->fetch_object();
                                                        if($link->enabled){
                                                            $lien = $this->createLien($manager, $link->title, "", $cours, $link->url, $this->getReference('typelien_http'));
                                                            $zone = $this->createZone($manager, $section, $lien, $link->studitVisible == 1, "", $numRess);

                                                            $numRess++;
                                                        }
                                                        $resultLink->close();
                                                    }

                                                } /*elseif ($elem->type == 'parcours') {
                                                    $queryParc = "SELECT * FROM c_lp WHERE c_id='" . $course->id . "' AND id='" . $elem->id . "'  AND enabled='1'";
                                                    if ($resultParc = $this->getMysqli()->query($queryParc)) {
                                                        $parc = $resultParc->fetch_object();
                                                        // $parc->name   $parc->studitVisible

                                                        $queryParcItem = "SELECT * FROM c_lp_item WHERE c_id='" . $course->id . "' AND lp_id='" . $parc->id . "'";

                                                        if ($resultParcItem = $this->getMysqli()->query($queryParcItem)) {
                                                            while ($pItem = $resultParcItem->fetch_object()) {
                                                                $queryLink = "SELECT * FROM c_link WHERE id='" . $pItem->path . "' AND c_id='" . $course->id . "'";

                                                                if ($resultLink = $this->getMysqli()->query($queryLink)) {
                                                                    $link = $resultLink->fetch_object();
                                                                    // $link->url    $link->title
                                                                } else {
                                                                    // $pItem->title
                                                                }
                                                            }
                                                            echo '</ul>';
                                                            $resultParcItem->close();
                                                        }
                                                        $resultParc->close();
                                                    }
                                                    $numRess++;
                                                }*/
                                            }
                                        }
                                    }

                                }
                            }


                        }
                    }

                }

                $resultDisc->close();
            }
        }
        $progress->finish();
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

    public function createSection(ObjectManager $manager, $nom, $cours, $position){
        $item = new Section();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setIsVisible(true);
        $item->setPictoFilePath('fa-pencil');
        $item->setPosition($position);
        $manager->persist($item);
        return $item;
    }

    public function createLien(ObjectManager $manager, $nom, $description, $cours, $url, $typeLien){
        $item = new Lien();
        $item->setNom($nom);
        $item->setCours($cours);
        $item->setDescription($description);
        $item->setUrl($url);
        $item->setTypeLien($typeLien);
        $manager->persist($item);
        return $item;
    }

    public function createZone(ObjectManager $manager, $section, $ressource, $isVisible, $description, $position){
        $item = new ZoneRessource();
        $item->setSection($section);
        if($ressource != null){
            $item->setRessource($ressource);
        }
        $item->setIsVisible($isVisible);
        $item->setPosition($position);
        $item->setDescription($description);
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