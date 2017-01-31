<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cours');
        $cours = $repository->find($id);

        $repositoryG = $this->getDoctrine()->getRepository('AppBundle:GroupeLiens');
        $repositoryRL = $this->getDoctrine()->getRepository('AppBundle:RessourceLibre');
        $repositoryL = $this->getDoctrine()->getRepository('AppBundle:Lien');
        $repositoryD = $this->getDoctrine()->getRepository('AppBundle:Devoir');

        $repositoryCop = $this->getDoctrine()->getRepository('AppBundle:Copie');
        $repositoryCor = $this->getDoctrine()->getRepository('AppBundle:Corrige');

        $sections = $this->getDoctrine()->getRepository('AppBundle:Section')->findBy(array('cours' => $cours), array('position' => 'ASC'));

        // On commence par récupérer le contenu des sections du cours
        $datas = array();
        for($i=0; $i<count($sections); $i++){
            $datas[$i]["section"] = $sections[$i];

            $zones = $this->getDoctrine()->getRepository('AppBundle:ZoneRessource')->findBy(array('section' => $sections[$i]), array('position' => 'ASC'));
            $datas[$i]["zones"]["containers"] = $zones;
            $datas[$i]["zones"]["content"] = array();
            $datas[$i]["zones"]["type"] = array();
            for($j=0; $j<count($zones); $j++){
                $zone = $datas[$i]["zones"]["containers"][$j];

                if($zone->getRessource() != null){
                    if($ressource = $repositoryL->findOneBy(array('id' => $zone->getRessource()->getId()))){

                        // la ressource est un lien
                        $datas[$i]["zones"]["type"][$j] = "lien";
                        $datas[$i]["zones"]["content"][$j] = $ressource;
                    }elseif($ressource = $repositoryD->findOneBy(array('id' => $zone->getRessource()->getId()))){

                        // la ressource est un devoir
                        $datas[$i]["zones"]["type"][$j] = "devoir";

                        $copie = $repositoryCop->findOneBy(array('auteur' => $this->getUser(), 'devoir' => $ressource));
                        $datas[$i]["zones"]["copie"][$j] = $copie;
                        $corrige = $repositoryCor->findOneBy(array('copie' => $copie));
                        $datas[$i]["zones"]["corrige"][$j] = $corrige;

                        $datas[$i]["zones"]["content"][$j] = array();
                        $datas[$i]["zones"]["content"][$j]['devoir'] = $ressource;
                        $datas[$i]["zones"]["content"][$j]['copie'] = $copie;
                        $datas[$i]["zones"]["content"][$j]['corrige'] = $corrige;

                    }elseif($groupe = $repositoryG->findOneBy(array('id' => $zone->getRessource()->getId()))) {

                        // la ressource est un groupe de liens
                        $datas[$i]["zones"]["type"][$j] = "groupe";
                        $repositoryGaL = $this->getDoctrine()
                            ->getRepository('AppBundle:AssocGroupeLiens')
                            ->findBy(array('groupe' => $groupe));
                        $datas[$i]["zones"]["groupe"][$j] = $groupe;
                        $datas[$i]["zones"]["content"][$j] = $repositoryGaL;
                    }elseif($free = $repositoryRL->findOneBy(array('id' => $zone->getRessource()->getId()))){

                            // la ressource est une ressource libre
                            $datas[$i]["zones"]["type"][$j] = "free";
                            $datas[$i]["zones"]["content"][$j] = $free;
                    }else{

                        // on ne trouve pas le type de la ressource
                        $datas[$i]["zones"]["type"][$j] = "unknown";
                        $datas[$i]["zones"]["content"][$j] = $zone->getDescription();

                    }
                }else{

                    // Aucune ressource associée
                    $datas[$i]["zones"]["type"][$j] = "free";
                    $datas[$i]["zones"]["content"][$j] = $zone->getDescription();

                }
            }
        }

        // on récupère aussi tout le contenu du cours
        $cLiens = $repositoryL->findBy(array('cours' => $cours));
        $cDevoirs = $repositoryD->findBy(array('cours' => $cours));
        $cLibres = $repositoryRL->findBy(array('cours' => $cours));

        $cGroupesEntity = $repositoryG->findBy(array('cours' => $cours));
        $cGroupes = array();
        for($i=0; $i<count($cGroupesEntity); $i++){
            $repositoryGaL = $this->getDoctrine()
                ->getRepository('AppBundle:AssocGroupeLiens')
                ->findBy(array('groupe' => $cGroupesEntity[$i]))
            ;
            $cGroupes[$i]['groupe'] = $cGroupesEntity[$i];
            $cGroupes[$i]['content'] = $repositoryGaL;
        }

        if($mode == "etu"){
            return $this->render('cours/one.html.twig', ['cours' => $cours, 'zonesSections' => $datas]);
        }elseif($mode == "admin"){
            return $this->render('cours/oneAdmin.html.twig',
                [
                    'cours' => $cours,
                    'zonesSections' => $datas,
                    'liens' => $cLiens,
                    'devoirs' => $cDevoirs,
                    'groupes' => $cGroupes,
                    'libres' => $cLibres
                ]);
        }
    }

    /**
     * @Route("/supprItem_ajax", name="supprItem_ajax")
     * @Method({"GET", "POST"})
     */
    public function supprItemAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $id = $request->request->get('idItem');
            $type = $request->request->get('typeItem');

            $entityRessourceName = "";
            if($type == "groupe"){
                $entityRessourceName = "GroupeLiens";
            }elseif($type == "devoir"){
                $entityRessourceName = "Devoir";
            }elseif($type == "lien"){
                $entityRessourceName = "Lien";
            }elseif($type == "libre"){
                $entityRessourceName = "RessourceLibre";
            }

            if($entityRessourceName == ""){
                return new JsonResponse(array(
                        'error' => true,
                        'entityRessourceName' => "non reconnu")
                );
            }else {
                $ressource = $em->getRepository('AppBundle:' . $entityRessourceName)->findOneBy(array('id' => $id));

                // on supprime les zones qui contenait l'item
                $zones = $em->getRepository('AppBundle:ZoneRessource')->findBy(array('ressource' => $ressource));
                for($i=0; $i<count($zones); $i++){
                    $em->remove($zones[$i]);
                }

                // puis on supprime l'item
                $em->remove($ressource);
                $em->flush();
                return new JsonResponse(array('action' =>'delete Zone', 'id' => $ressource->getId()));
            }
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
