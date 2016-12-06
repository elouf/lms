<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CoursController extends Controller
{
    /**
     * @Route("/courses", name="courses")
     */
    public function coursesAction (Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cours');
        $cours = $repository->findAll();

        return $this->render('cours/list.html.twig', ['courses' => $cours]);
    }

    /**
     * @Route("/courses/disc/{id}", name="coursesForDisc")
     */
    public function coursesByDiscAction (Request $request, $id)
    {
        $repositoryD = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disc = $repositoryD->find($id);

        $repositoryC = $this->getDoctrine()->getRepository('AppBundle:Cours');
        $courses = $repositoryC->findBy(array('discipline' => $disc));

        return $this->render('cours/one.html.twig', ['courses' => $courses]);
    }

    /**
     *
     * @Route("/cours/{id}/mode/{mode}", name="oneCours")
     */
    public function oneCoursAction (Request $request, $id, $mode)
    {
        $repositoryL = $this->getDoctrine()->getRepository('AppBundle:Lien');
        $repositoryD = $this->getDoctrine()->getRepository('AppBundle:Devoir');
        $repositoryCop = $this->getDoctrine()->getRepository('AppBundle:Copie');
        $repositoryCor = $this->getDoctrine()->getRepository('AppBundle:Corrige');
        $repositoryZ = $this->getDoctrine()->getRepository('AppBundle:ZoneRessource');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cours');
        $cours = $repository->find($id);

        $sections = $this->getDoctrine()->getRepository('AppBundle:Section')->findBy(array('cours' => $cours));

        $datas = array();
        for($i=0; $i<count($sections); $i++){
            $datas[$i]["section"] = $sections[$i];

            $zones = $repositoryZ->findBy(array('section' => $sections[$i]));
            $datas[$i]["zones"]["containers"] = $zones;
            $datas[$i]["zones"]["content"] = array();
            $datas[$i]["zones"]["type"] = array();
            for($j=0; $j<count($zones); $j++){
                $zone = $datas[$i]["zones"]["containers"][$j];

                $ressource = "une ressource non typée";

                if($repositoryL->findBy(array('id' => $zone->getRessource()->getId()))){
                    // la ressource est un lien
                    $ressource = $repositoryL->findOneBy(array('id' => $zone->getRessource()->getId()));
                    $datas[$i]["zones"]["type"][$j] = "lien";

                    $datas[$i]["zones"]["content"][$j] = $ressource;
                }elseif($repositoryD->findBy(array('id' => $zone->getRessource()->getId()))){
                    // la ressource est un devoir
                    $ressource = $repositoryD->findOneBy(array('id' => $zone->getRessource()->getId()));
                    $datas[$i]["zones"]["type"][$j] = "devoir";

                    $copie = $repositoryCop->findOneBy(array('auteur' => $this->getUser(), 'devoir' => $ressource));
                    $datas[$i]["zones"]["copie"][$j] = $copie;
                    $corrige = $repositoryCor->findOneBy(array('copie' => $copie));
                    $datas[$i]["zones"]["corrige"][$j] = $corrige;

                    $datas[$i]["zones"]["content"][$j] = array();
                    $datas[$i]["zones"]["content"][$j]['devoir'] = $ressource;
                    $datas[$i]["zones"]["content"][$j]['copie'] = $copie;
                    $datas[$i]["zones"]["content"][$j]['corrige'] = $corrige;
                    dump($copie);
                    dump($corrige);
                }else{
                    // on ne trouve pas le type de la ressource
                }


            }
        }
        if($mode == "etu"){
            return $this->render('cours/one.html.twig', ['cours' => $cours, 'zonesSections' => $datas]);
        }elseif($mode == "admin"){
            return $this->render('cours/oneAdmin.html.twig', ['cours' => $cours, 'zonesSections' => $datas]);
        }
    }
}
