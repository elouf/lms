<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssocDocDisc;
use AppBundle\Entity\AssocDocInscr;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentController extends Controller
{

    /**
     * @Route("/documents/{id}", name="documents")
     */
    public function documentsByDisciplineAction (Request $request, $id)
    {
        $discipline = $this->getDoctrine()->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));

        $assocsDisc = $this->getDoctrine()->getRepository('AppBundle:AssocDocDisc')->findBy(array('discipline' => $discipline));

        // on récupère tous les documents liés à la discipline
        $documents = array();
        // documents directement associés à la discipline
        for($i=0; $i<count($assocsDisc); $i++) {
            if(!in_array($assocsDisc[$i]->getDocument(), $documents)){
                array_push($documents, $assocsDisc[$i]->getDocument());
            }
        }
        // documents associés à une inscription à une cohorte (à laquelle le user est inscrite) inscrite à la discipline
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        foreach($cohortes as $cohorte){
            if($cohorte->getDisciplines()->contains($discipline)){
                $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $this->getUser(), 'cohorte' => $cohorte));
                if($inscrCoh){
                    $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrCoh, 'cours' => null));
                    for($i=0; $i<count($assocsInscr); $i++) {
                        if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                            array_push($documents, $assocsInscr[$i]->getDocument());
                        }
                    }
                }
            }
        }
        // documents associés à une inscription à la discipline (à laquelle le user est inscrite)
        $inscrDis = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $this->getUser(), 'discipline' => $discipline));
        if($inscrDis){
            $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrDis, 'cours' => null));
            for($i=0; $i<count($assocsInscr); $i++) {
                if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                    array_push($documents, $assocsInscr[$i]->getDocument());
                }
            }
        }

        // puis tous les users (ça permet d'afficher la combo-box des users destinataires des documents)
        $users = array();
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        foreach($cohortes as $cohorte){
            if($cohorte->getDisciplines()->contains($discipline)){
                $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                foreach($inscrCohs as $inscrCoh){
                    if(!in_array($inscrCoh->getUser(), $users)) {
                        array_push($users, $inscrCoh->getUser());
                    }
                }
            }
        }

        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        foreach($inscrDs as $inscrD){
            if(!in_array($inscrD->getUser(), $users)) {
                array_push($users, $inscrD->getUser());
            }
        }

        return $this->render('documents/oneByDisc.html.twig', ['discipline' => $discipline, 'documents' => $documents, 'users' => $users]);
    }

    /**
     * @Route("/cours/{id}/documents", name="documentsCours")
     */
    public function documentsByCoursAction (Request $request, $id)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));
        $discipline = $cours->getDiscipline();

        $assocsCours = $this->getDoctrine()->getRepository('AppBundle:AssocDocCours')->findBy(array('cours' => $cours));

        // on récupère tous les documents liés au cours
        $documents = array();
        // documents directement associés au cours
        for($i=0; $i<count($assocsCours); $i++) {
            if(!in_array($assocsCours[$i]->getDocument(), $documents)){
                array_push($documents, $assocsCours[$i]->getDocument());
            }
        }
        // documents associés à une inscription à une cohorte (à laquelle le user est inscrit) inscrite au cours ou à la discipline qui la contient
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        foreach($cohortes as $cohorte){
            if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $this->getUser(), 'cohorte' => $cohorte));
                if($inscrCoh){
                    $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrCoh));
                    for($i=0; $i<count($assocsInscr); $i++) {
                        if(!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null){
                            array_push($documents, $assocsInscr[$i]->getDocument());
                        }
                    }
                }
            }
        }
        // documents associés à une inscription à la discipline contenant le cours (à laquelle le user est inscrite)
        $inscrDis = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $this->getUser(), 'discipline' => $discipline));
        if($inscrDis){
            $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrDis));
            for($i=0; $i<count($assocsInscr); $i++) {
                if(!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null){
                    array_push($documents, $assocsInscr[$i]->getDocument());
                }
            }
        }

        // puis tous les users (ça permet d'afficher la combo-box des users destinataires des documents)
        $users = array();
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        foreach($cohortes as $cohorte){
            if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                foreach($inscrCohs as $inscrCoh){
                    if(!in_array($inscrCoh->getUser(), $users)) {
                        array_push($users, $inscrCoh->getUser());
                    }
                }
            }
        }

        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        foreach($inscrDs as $inscrD){
            if(!in_array($inscrD->getUser(), $users)) {
                array_push($users, $inscrD->getUser());
            }
        }

        $inscrCs = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        foreach($inscrCs as $inscrC){
            if(!in_array($inscrC->getUser(), $users)) {
                array_push($users, $inscrC->getUser());
            }
        }

        return $this->render('documents/oneByCours.html.twig', ['cours' => $cours, 'documents' => $documents, 'users' => $users]);
    }

    /**
     * @Route("/uploadDocFile_ajax", name="uploadDocFile_ajax")
     */
    public function uploadDocFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $discId = $request->request->get('discId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');
            $userId = $request->request->get('userId');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $users = $request->request->get('users');


            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('/var', $urlDest);

            $discipline = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $discId));
            $proprietaire = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            $doc = new Document();
            $doc->setProprietaire($proprietaire);
            $doc->setNom($nom);
            $doc->setDescription($description);
            $doc->setDateCrea(new DateTime());

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $rand = rand(1, 999999);
            rename($url, $urlDest.'file'.$rand.'.'.$ext);

            $doc->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file'.$rand.'.'.$ext);
            $em->persist($doc);

            // on créée les associations avec les users. Si ils sont tous concernés, c'est qu'on est dans le cas d'une assocDisc
            //sinon c'est une assoc_doc
            if(in_array("0", $users)){
                $assocDisc = new AssocDocDisc();
                $assocDisc->setDiscipline($discipline);
                $assocDisc->setDocument($doc);
                $em->persist($assocDisc);
            }else{
                for($i=0; $i<count($users); $i++){
                    $assocInscr = new AssocDocInscr();
                    $assocInscr->setDocument($doc);

                    $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $users[$i]));

                    // on commence par voir si le user est inscrit à une cohorte qui est inscrite à cette discipline
                    $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
                    $estAssocie = false;
                    foreach($cohortes as $cohorte){
                        if($cohorte->getDisciplines()->contains($discipline)){
                            $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                            if($inscrCoh){
                                $assocInscr->setInscription($inscrCoh);
                                $estAssocie = true;
                                break 1;
                            }
                        }
                    }

                    // pas de cohorte, on cherche une inscription directe à la discipline
                    if(!$estAssocie){
                        $inscrD = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $user, 'discipline' => $discipline));
                        if($inscrD){
                            $assocInscr->setInscription($inscrD);
                            $estAssocie = true;
                        }
                    }
                    if($estAssocie){
                        $em->persist($assocInscr);
                    }


                }
            }

            $em->flush();
            return new JsonResponse(array('action' =>'upload Document for Discipline', 'id' => $discId, 'ext' => $ext));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deleteDocument_ajax", name="deleteDocument_ajax")
     */
    public function deleteDocumentAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $docId = $request->request->get('id');

            $document = $em->getRepository('AppBundle:Document')->findOneBy(array('id' => $docId));

            $urlTab = explode('/var', $document->getUrl());

            $em->remove($document);

            $em->flush();

            unlink('../var'.$urlTab[1]);

            $em->flush();

            return new JsonResponse(array('action' =>'deleteDocument', 'id' => $docId));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
