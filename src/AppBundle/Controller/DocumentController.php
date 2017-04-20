<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssocDocCours;
use AppBundle\Entity\AssocDocDisc;
use AppBundle\Entity\AssocDocInscr;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use AppBundle\Entity\StatsUsersDocs;
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
        $documentsImportants = array();
        // documents directement associés à la discipline
        for($i=0; $i<count($assocsDisc); $i++) {
            if($assocsDisc[$i]->getIsImportant()){
                if(!in_array($assocsDisc[$i]->getDocument(), $documentsImportants)){
                    array_push($documentsImportants, $assocsDisc[$i]->getDocument());
                }
            }else{
                if(!in_array($assocsDisc[$i]->getDocument(), $documents)){
                    array_push($documents, $assocsDisc[$i]->getDocument());
                }
            }
        }
        // documents associés à une inscription à une cohorte (à laquelle le user est inscrite) inscrite à la discipline
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        if($cohortes){
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline)){
                    if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
                        $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                        if($inscrCohs){
                            foreach($inscrCohs as $inscrCoh){
                                $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrCoh, 'cours' => null));
                                for($i=0; $i<count($assocsInscr); $i++) {
                                    if($assocsInscr[$i]->getIsImportant()){
                                        if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                        }
                                    }else{
                                        if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                                            array_push($documents, $assocsInscr[$i]->getDocument());
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $this->getUser(), 'cohorte' => $cohorte));
                        if($inscrCoh){
                            $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrCoh, 'cours' => null));
                            for($i=0; $i<count($assocsInscr); $i++) {
                                if($assocsInscr[$i]->getIsImportant()){
                                    if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                                        array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                    }
                                }else{
                                    if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                                        array_push($documents, $assocsInscr[$i]->getDocument());
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }

        // documents associés à une inscription à la discipline (à laquelle le user est inscrite)
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $inscrDiss = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
            if($inscrDiss){
                foreach($inscrDiss as $inscrDis){
                    $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrDis, 'cours' => null));
                    for($i=0; $i<count($assocsInscr); $i++) {
                        if($assocsInscr[$i]->getIsImportant()){
                            if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                                array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                            }
                        }else{
                            if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                                array_push($documents, $assocsInscr[$i]->getDocument());
                            }
                        }
                    }
                }
            }
        }else{
            $inscrDis = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $this->getUser(), 'discipline' => $discipline));
            if($inscrDis){
                $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrDis, 'cours' => null));
                for($i=0; $i<count($assocsInscr); $i++) {
                    if($assocsInscr[$i]->getIsImportant()){
                        if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                        }
                    }else{
                        if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                            array_push($documents, $assocsInscr[$i]->getDocument());
                        }
                    }
                }
            }
        }


        // puis tous les users (ça permet d'afficher la combo-box des users destinataires des documents)
        $users = array();
        $admins = $this->getDoctrine()->getRepository('AppBundle:User')->findByRole('ROLE_SUPER_ADMIN');
        foreach($admins as $admin){
            if(!in_array($admin, $users)) {
                array_push($users, [$admin, 'admin']);
            }
        }
        if($cohortes){
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline)){
                    $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                    foreach($inscrCohs as $inscrCoh){
                        if(!in_array($inscrCoh->getUser(), $users)) {

                            array_push($users, [$inscrCoh->getUser(), $inscrCoh->getRole()->getNom() ]);
                        }
                    }
                }
            }
        }

        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users)) {
                    array_push($users, [$inscrD->getUser(), $inscrD->getRole()->getNom()]);
                }
            }
        }

        return $this->render('documents/byDisc.html.twig', [
            'discipline' => $discipline,
            'documentsImportants' => $documentsImportants,
            'documents' => $documents,
            'users' => $users,
            'folderUpload' => $this->getParameter('upload_directory'),
            'uploadSteps' => $this->getParameter('upload_steps'),
            'uploadSrcSteps' => $this->getParameter('upload_srcSteps')
        ]);
    }

    /**
     * @Route("/courseDocs/{id}", name="courseDocs")
     */
    public function documentsByCoursAction (Request $request, $id)
    {
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));
        $discipline = $cours->getDiscipline();
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();

        $docs = $this->getDocsByCours($cours);

        $documents = $docs[0];
        $documentsImportants = $docs[1];

        // puis tous les users (ça permet d'afficher la combo-box des users destinataires des documents)
        $users = array();
        $admins = $this->getDoctrine()->getRepository('AppBundle:User')->findByRole('ROLE_SUPER_ADMIN');
        foreach($admins as $admin){
            if(!in_array($admin, $users)) {
                array_push($users, [$admin, 'admin']);
            }
        }
        if($cohortes){
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                    $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                    foreach($inscrCohs as $inscrCoh){
                        if(!in_array($inscrCoh->getUser(), $users)) {
                            array_push($users, [$inscrCoh->getUser(), $inscrCoh->getRole()->getNom() ]);
                        }
                    }
                }
            }
        }

        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users)) {
                    array_push($users, [$inscrD->getUser(), $inscrD->getRole()->getNom()]);
                }
            }
        }

        $inscrCs = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        if($inscrCs){
            foreach($inscrCs as $inscrC){
                if(!in_array($inscrC->getUser(), $users)) {
                    array_push($users, [$inscrC->getUser(), $inscrC->getRole()->getNom()]);
                }
            }
        }

        return $this->render('documents/byCours.html.twig', [
            'cours' => $cours,
            'documentsImportants' => $documentsImportants,
            'documents' => $documents,
            'users' => $users,
            'folderUpload' => $this->getParameter('upload_directory'),
            'uploadSteps' => $this->getParameter('upload_steps'),
            'uploadSrcSteps' => $this->getParameter('upload_srcSteps')
        ]);
    }

    public function getDocsByCours($cours){
        $discipline = $cours->getDiscipline();
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        $assocsCours = $this->getDoctrine()->getRepository('AppBundle:AssocDocCours')->findBy(array('cours' => $cours));

        // on récupère tous les documents liés au cours
        $documents = array();
        $documentsImportants = array();
        // documents directement associés au cours
        for($i=0; $i<count($assocsCours); $i++) {
            if($assocsCours[$i]->getIsImportant()){
                if(!in_array($assocsCours[$i]->getDocument(), $documentsImportants)){
                    array_push($documentsImportants, $assocsCours[$i]->getDocument());
                }
            }else{
                if(!in_array($assocsCours[$i]->getDocument(), $documents)){
                    array_push($documents, $assocsCours[$i]->getDocument());
                }
            }
        }

        // documents associés à une inscription à une cohorte (à laquelle le user est inscrit) inscrite au cours ou à la discipline qui la contient
        if($cohortes){
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                    if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
                        $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                        if ($inscrCohs) {
                            foreach($inscrCohs as $inscrCoh) {
                                $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrCoh));
                                for ($i = 0; $i < count($assocsInscr); $i++) {
                                    if($assocsInscr[$i]->getIsImportant()){
                                        if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                        }
                                    }else{
                                        if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                                            array_push($documents, $assocsInscr[$i]->getDocument());
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $this->getUser(), 'cohorte' => $cohorte));
                        if($inscrCoh){
                            $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrCoh));
                            for($i=0; $i<count($assocsInscr); $i++) {
                                if($assocsInscr[$i]->getIsImportant()){
                                    if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                                        array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                    }
                                }else{
                                    if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                                        array_push($documents, $assocsInscr[$i]->getDocument());
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }

        // documents associés à une inscription à la discipline contenant le cours (à laquelle le user est inscrite)
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $inscrDiss = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $discipline));
            if($inscrDiss) {
                foreach ($inscrDiss as $inscrDis) {
                    $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrDis));
                    for ($i = 0; $i < count($assocsInscr); $i++) {
                        if($assocsInscr[$i]->getIsImportant()){
                            if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                                array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                            }
                        }else{
                            if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                                array_push($documents, $assocsInscr[$i]->getDocument());
                            }
                        }
                    }
                }
            }
        }else{
            $inscrDis = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $this->getUser(), 'discipline' => $discipline));
            if($inscrDis){
                $assocsInscr = $this->getDoctrine()->getRepository('AppBundle:AssocDocInscr')->findBy(array('inscription' => $inscrDis));
                for($i=0; $i<count($assocsInscr); $i++) {
                    if($assocsInscr[$i]->getIsImportant()){
                        if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                        }
                    }else{
                        if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                            array_push($documents, $assocsInscr[$i]->getDocument());
                        }
                    }
                }
            }
        }
        return array($documents, $documentsImportants);
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
            $isImportant = $request->request->get('isImportant') == "true"? true : false;
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
                $assocDisc->setIsImportant($isImportant);
                $em->persist($assocDisc);
            }else{
                for($i=0; $i<count($users); $i++){
                    $assocInscr = new AssocDocInscr();
                    $assocInscr->setDocument($doc);
                    $assocInscr->setIsImportant($isImportant);

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
     * @Route("/uploadDocCoursFile_ajax", name="uploadDocCoursFile_ajax")
     */
    public function uploadDocCoursFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $coursId = $request->request->get('coursId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');
            $userId = $request->request->get('userId');
            $nom = $request->request->get('nom');
            $isImportant = $request->request->get('isImportant') == "true"? true : false;
            $description = $request->request->get('description');
            $users = $request->request->get('users');


            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('/var', $urlDest);

            $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $coursId));
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

            // on créée les associations avec les users. Si ils sont tous concernés, c'est qu'on est dans le cas d'une assocCours
            //sinon c'est une assoc_doc
            if(in_array("0", $users)){
                $assocCours = new AssocDocCours();
                $assocCours->setCours($cours);
                $assocCours->setDocument($doc);
                $assocCours->setIsImportant($isImportant);
                $em->persist($assocCours);
            }else{
                for($i=0; $i<count($users); $i++){
                    $assocInscr = new AssocDocInscr();
                    $assocInscr->setDocument($doc);
                    $assocInscr->setIsImportant($isImportant);

                    $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $users[$i]));

                    // on commence par voir si le user est inscrit à une cohorte qui est inscrite à ce cours
                    $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
                    $estAssocie = false;
                    foreach($cohortes as $cohorte){
                        if($cohorte->getDisciplines()->contains($cours->getDiscipline()) || $cohorte->getCours()->contains($cours)){
                            $inscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                            if($inscrCoh){
                                $assocInscr->setInscription($inscrCoh);
                                $estAssocie = true;
                                break 1;
                            }
                        }
                    }

                    // pas de cohorte, on cherche une inscription directe au cours
                    if(!$estAssocie){
                        $inscrD = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findOneBy(array('user' => $user, 'discipline' => $cours->getDiscipline()));
                        if($inscrD){
                            $assocInscr->setInscription($inscrD);
                            $estAssocie = true;
                        }
                    }
                    if(!$estAssocie){
                        $inscrC = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findOneBy(array('user' => $user, 'cours' => $cours));
                        if($inscrC){
                            $assocInscr->setInscription($inscrC);
                            $estAssocie = true;
                        }
                    }
                    if($estAssocie){
                        $em->persist($assocInscr);
                    }


                }
            }

            $em->flush();
            return new JsonResponse(array('action' =>'upload Document for Cours', 'id' => $coursId, 'ext' => $ext));
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

    /**
     * @Route("/getDoc_ajax", name="getDoc_ajax")
     */
    public function getDocAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $docId = $request->request->get('id');

            $document = $em->getRepository('AppBundle:Document')->findOneBy(array('id' => $docId));

            $em->flush();

            return new JsonResponse(array('action' =>'Get Document',
                'id' => $docId, 'url' => $document->getUrl(), 'nom' => $document->getNom(), 'description' => $document->getDescription()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/userOpenDoc_ajax", name="userOpenDoc_ajax")
     */
    public function userOpenDocAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $docId = $request->request->get('id');

            $document = $em->getRepository('AppBundle:Document')->findOneBy(array('id' => $docId));
            $user = $this->getUser();

            $statUserDoc = new StatsUsersDocs();
            $statUserDoc->setDocument($document);
            $statUserDoc->setUser($user);
            $statUserDoc->setDateAcces(new DateTime());

            $em->persist($statUserDoc);

            $em->flush();

            return new JsonResponse(array('action' =>'Set Stat USer Doc',
                'id' => $docId, 'user' => $user->getEmail(), 'nom' => $document->getNom()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
