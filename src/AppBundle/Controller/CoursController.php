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
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->find($id);
        $discipline = $cours->getDiscipline();

        // on corrige le statut du user. Si c'est un enseignant, il ne doit pas être en etu. Si ce n'est pas un admin, il ne doit pas être admin
        if(!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $role = "";
            $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
            if($cohortes){
                foreach($cohortes as $cohorte){
                    if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                        $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $this->getUser(), 'cohorte' => $cohorte));
                        if($inscrCoh){
                            $role = $inscrCoh->getRole()->getNom();
                            break;
                        }
                    }
                }
            }
            if($role == ""){
                $inscrDis = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $this->getUser(), 'discipline' => $discipline));
                if($inscrDis) {
                    $role = $inscrDis->getRole()->getNom();
                }
            }
            if($role == ""){
                $inscrC = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findOneBy(array('user' => $this->getUser(), 'cours' => $cours));
                if($inscrC) {
                    $role = $inscrC->getRole()->getNom();
                }
            }
            if($role == "Enseignant"){
                $mode = 'ens';
            }else{
                $mode = 'etu';
            }
        }





        $repositoryTypeLiens = $this->getDoctrine()->getRepository('AppBundle:TypeLien')->findAll();
        $repositoryCategorieLiens = $this->getDoctrine()->getRepository('AppBundle:CategorieLien')->findAll();

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
                    $ressource = $this->getDoctrine()->getRepository('AppBundle:Ressource')->findOneBy(array('id' => $zone->getRessource()->getId()));
                    $ressType = $ressource->getType();
                    $datas[$i]["zones"]["type"][$j] = $ressType;

                    if($ressType == "lien"){
                        $datas[$i]["zones"]["type"][$j] = "lien";
                        $datas[$i]["zones"]["content"][$j] = $ressource;

                    }elseif($ressType == "devoir"){
                        $datas[$i]["zones"]["type"][$j] = "devoir";

                        $repositorySujet = $this->getDoctrine()
                            ->getRepository('AppBundle:DevoirSujet')
                            ->findBy(array('devoir' => $ressource));

                        $repositoryCorrigeType = $this->getDoctrine()
                            ->getRepository('AppBundle:DevoirCorrigeType')
                            ->findBy(array('devoir' => $ressource));

                        $datas[$i]["zones"]["content"][$j] = $ressource;
                        $datas[$i]["zones"]["sujet"][$j] = $repositorySujet;
                        $datas[$i]["zones"]["corrigeType"][$j] = "undefined";

                        if($repositoryCorrigeType) {
                            $datas[$i]["zones"]["corrigeType"][$j] = $repositoryCorrigeType;
                        }

                        // on a pas besoin des copies du user si on est en mode admin, par contre en etu, oui
                        if($mode == "admin"){

                        }elseif($mode == 'ens'){
                            // on compte le nombre de copies non corrigées
                            $datas[$i]["zones"]["copiesDeposes"][$j] = 0;
                            $datas[$i]["zones"]["corrigesDeposes"][$j] = 0;

                            $copies = $this->getDoctrine()->getRepository('AppBundle:Copie')->findBy(array('devoir' => $ressource));
                            for($u=0; $u<count($copies); $u++){
                                $copieFichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier')->findOneBy(array('copie' => $copies[$u]));
                                if($copieFichier){
                                    $datas[$i]["zones"]["copiesDeposes"][$j]++;
                                    $corrigeFichier = $this->getDoctrine()->getRepository('AppBundle:Corrige')->findOneBy(array('copie' => $copies[$u]));
                                    if($corrigeFichier){
                                        $datas[$i]["zones"]["corrigesDeposes"][$j]++;
                                    }
                                }
                            }
                        }else{
                            $datas[$i]["zones"]["copie"][$j] = "undefined";
                            $datas[$i]["zones"]["corrige"][$j] = "undefined";
                            $datas[$i]["zones"]["copieFichier"][$j] = "undefined";
                            $datas[$i]["zones"]["corrigeFichier"][$j] = "undefined";

                            $copie = $this->getDoctrine()->getRepository('AppBundle:Copie')->findOneBy(array('devoir' => $ressource, 'auteur' => $this->getUser()));
                            if($copie){
                                $datas[$i]["zones"]["copie"][$j] = $copie;

                                $copieFichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier')->findOneBy(array('copie' => $copie));
                                if($copieFichier){
                                    $datas[$i]["zones"]["copieFichier"][$j] = $copieFichier;
                                }

                                $corrige = $this->getDoctrine()->getRepository('AppBundle:Corrige')->findOneBy(array('copie' => $copie));
                                if($corrige){
                                    $datas[$i]["zones"]["corrige"][$j] = $corrige;
                                    $corrigeFichier = $this->getDoctrine()->getRepository('AppBundle:CorrigeFichier')->findOneBy(array('corrige' => $corrige));
                                    $datas[$i]["zones"]["corrigeFichier"][$j] = $corrigeFichier;
                                }
                            }
                        }

                    }elseif($ressType == "groupe") {
                        $repositoryGaL = $this->getDoctrine()
                            ->getRepository('AppBundle:AssocGroupeLiens')
                            ->findBy(array('groupe' => $ressource), array('position' => 'ASC'));
                        $datas[$i]["zones"]["groupe"][$j] = $ressource;
                        $datas[$i]["zones"]["content"][$j] = $repositoryGaL;
                    }elseif($ressType == "libre"){
                            $datas[$i]["zones"]["content"][$j] = $ressource;
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
        $cLiens = $this->getDoctrine()->getRepository('AppBundle:Lien')->findBy(array('cours' => $cours));
        $cLibres = $this->getDoctrine()->getRepository('AppBundle:RessourceLibre')->findBy(array('cours' => $cours));

        $cGroupesEntity = $this->getDoctrine()->getRepository('AppBundle:GroupeLiens')->findBy(array('cours' => $cours));
        $cGroupes = array();
        for($i=0; $i<count($cGroupesEntity); $i++){
            $repositoryGaL = $this->getDoctrine()
                ->getRepository('AppBundle:AssocGroupeLiens')
                ->findBy(array('groupe' => $cGroupesEntity[$i]))
            ;
            $cGroupes[$i]['groupe'] = $cGroupesEntity[$i];
            $cGroupes[$i]['content'] = $repositoryGaL;
        }

        $cDevoirsEntity = $this->getDoctrine()->getRepository('AppBundle:Devoir')->findBy(array('cours' => $cours));
        $cDevoirs = array();
        for($i=0; $i<count($cDevoirsEntity); $i++){
            $repositorySujet = $this->getDoctrine()
                ->getRepository('AppBundle:DevoirSujet')
                ->findBy(array('devoir' => $cDevoirsEntity[$i]));
            $repositoryCorrigeType = $this->getDoctrine()
                ->getRepository('AppBundle:DevoirCorrigeType')
                ->findBy(array('devoir' => $cDevoirsEntity[$i]));

            $cDevoirs[$i]['content'] = $cDevoirsEntity[$i];
            $cDevoirs[$i]['sujets'] = $repositorySujet;
            $cDevoirs[$i]['corrigesType'] = $repositoryCorrigeType;
        }

        //Comme un accès aux documents du cours existe, on doit afficher l'info-bulle si certains n'ont pas été visités
        $docController = new DocumentController();
        /*       $docs = $docController->getDocsByCours($cours);

               $documents = $docs[0];
               $documentsImportants = $docs[1];
               dump($docs);*/

        if($mode == "admin"){
            return $this->render('cours/oneAdmin.html.twig',
                [
                    'cours' => $cours,
                    'zonesSections' => $datas,
                    'liens' => $cLiens,
                    'devoirs' => $cDevoirs,
                    'groupes' => $cGroupes,
                    'libres' => $cLibres,
                    'typeLiens' => $repositoryTypeLiens,
                    'categorieLiens' => $repositoryCategorieLiens,
                    'folderUpload' => $this->getParameter('upload_directory'),
                    'uploadSteps' => $this->getParameter('upload_steps'),
                    'uploadSrcSteps' => $this->getParameter('upload_srcSteps'),
                    'uploadCourse' => $this->getParameter('upload_course')
                ]);
        }elseif($mode == "ens") {
            return $this->render('cours/one.html.twig', [
                'cours' => $cours,
                'zonesSections' => $datas,
                'mode' => 'ens',
                'folderUpload' => $this->getParameter('upload_directory'),
                'uploadSteps' => $this->getParameter('upload_steps'),
                'uploadSrcSteps' => $this->getParameter('upload_srcSteps'),
                'uploadCourse' => $this->getParameter('upload_course')
            ]);
        }else{
            return $this->render('cours/one.html.twig', [
                'cours' => $cours,
                'zonesSections' => $datas,
                'mode' => 'etu',
                'folderUpload' => $this->getParameter('upload_directory'),
                'uploadSteps' => $this->getParameter('upload_steps'),
                'uploadSrcSteps' => $this->getParameter('upload_srcSteps'),
                'uploadCourse' => $this->getParameter('upload_course')
            ]);
        }
    }

    /**
     * @Route("/getNbZoneRess_ajax", name="getNbZoneRess_ajax")
     * @Method({"GET", "POST"})
     */
    public function getNbZoneRessAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $id = $request->request->get('idItem');

            $ressource = $em->getRepository('AppBundle:Ressource')->findOneBy(array('id' => $id));
            $zones = $em->getRepository('AppBundle:ZoneRessource')->findBy(array('ressource' => $ressource));

            return new JsonResponse(array('action' =>'get nb zone avec cette ressource', 'nb' => count($zones)));
        }

        return new JsonResponse('This is not ajax!', 400);
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
                $em->remove($ressource);
                $em->flush();
                return new JsonResponse(array('action' =>'delete Zone', 'id' => $ressource->getId()));
            }
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/checkDirForUploadFile_ajax", name="checkDirForUploadFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function checkDirForUploadFileAjax (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $url = $request->request->get('url');

            if(!is_dir($url)) {
                if (!mkdir($url, 0777, true)) {
                    return new JsonResponse(array('error' =>'Echec de la création du dossier', 'url' => $url));
                }else{
                    return new JsonResponse(array('action' =>'Dossier créé', 'url' => $url));
                }
            }else{
                return new JsonResponse(array('action' =>'Dossier existe déjà', 'url' => $url));
            }

        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/changeActivationDocsCours_ajax", name="changeActivationDocsCours_ajax")
     */
    public function changeActivationDocsCoursAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));
            $cours->setDocsActivated($isVisible == "false");

            $em->persist($cours);
            $em->flush();

            return new JsonResponse(array('action' =>'change Visibility of documents', 'id' => $cours->getId(), 'isVisible' => $cours->getDocsActivated()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
