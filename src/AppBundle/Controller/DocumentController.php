<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssocDocCours;
use AppBundle\Entity\AssocDocDisc;
use AppBundle\Entity\AssocDocInscr;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use AppBundle\Entity\StatsUsersDocs;
use AppBundle\Entity\User;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentController extends Controller
{
    public function pushArrayDocsAndNew($docs){
        $destDocs = array();
        $repoStatsUsersDocs = $this->getDoctrine()->getRepository('AppBundle:StatsUsersDocs');
        foreach($docs as $doc){
            $stat = $repoStatsUsersDocs->findBy(array('user' => $this->getUser(), 'document' => $doc));
            $isNew = 0;
            if(!$stat){
                $isNew = 1;
            }
            array_push($destDocs, [$doc, $isNew]);
        }
        return $destDocs;
    }

    /**
     * @Route("/documents/{id}", name="documents")
     */
    public function documentsByDisciplineAction (Request $request, $id)
    {
        $discipline = $this->getDoctrine()->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();

        $docs = $this->getDoctrine()->getRepository('AppBundle:Document')->findByDisc($discipline, $this->getUser());

        $documents = $this->pushArrayDocsAndNew($docs[0]);
        $documentsImportants = $this->pushArrayDocsAndNew($docs[1]);

        $role = $docs[2];
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $role = "admin";
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
            $repoInscription_coh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh');
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline)){
                    $inscrCohs = $repoInscription_coh->findBy(array('cohorte' => $cohorte));
                    foreach($inscrCohs as $inscrCoh){
                        if(!in_array($inscrCoh->getUser(), $users)  && $inscrCoh->getUser()->isEnabled()) {
                            array_push($users, [$inscrCoh->getUser(), $inscrCoh->getRole()->getNom() ]);
                        }
                    }
                }
            }
        }

        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users)  && $inscrD->getUser()->isEnabled()) {
                    array_push($users, [$inscrD->getUser(), $inscrD->getRole()->getNom()]);
                }
            }
        }

        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $role = "admin";
        }

        return $this->render('documents/byDisc.html.twig', [
            'discipline' => $discipline,
            'documentsImportants' => $documentsImportants,
            'documents' => $documents,
            'users' => $users,
            'role' => $role,
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

        $docs = $this->getDoctrine()->getRepository('AppBundle:Document')->findByCours($cours, $this->getUser());

        $documents = $this->pushArrayDocsAndNew($docs[0]);
        $documentsImportants = $this->pushArrayDocsAndNew($docs[1]);
        $role = $docs[2];
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
            $role = "admin";
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
            $repoInscription_coh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh');
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                    $inscrCohs = $repoInscription_coh->findBy(array('cohorte' => $cohorte));
                    foreach($inscrCohs as $inscrCoh){
                        if(!in_array($inscrCoh->getUser(), $users)  && $inscrCoh->getUser()->isEnabled()) {
                            array_push($users, [$inscrCoh->getUser(), $inscrCoh->getRole()->getNom() ]);
                        }
                    }
                }
            }
        }

        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users)  && $inscrD->getUser()->isEnabled()) {
                    array_push($users, [$inscrD->getUser(), $inscrD->getRole()->getNom()]);
                }
            }
        }

        $inscrCs = $this->getDoctrine()->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        if($inscrCs){
            foreach($inscrCs as $inscrC){
                if(!in_array($inscrC->getUser(), $users)  && $inscrC->getUser()->isEnabled()) {
                    array_push($users, [$inscrC->getUser(), $inscrC->getRole()->getNom()]);
                }
            }
        }

        return $this->render('documents/byCours.html.twig', [
            'cours' => $cours,
            'documentsImportants' => $documentsImportants,
            'documents' => $documents,
            'users' => $users,
            'role' => $role,
            'folderUpload' => $this->getParameter('upload_directory'),
            'uploadSteps' => $this->getParameter('upload_steps'),
            'uploadSrcSteps' => $this->getParameter('upload_srcSteps')
        ]);
    }

    /**
     * @Route("/uploadDocFile_ajax", name="uploadDocFile_ajax")
     */
    public function uploadDocFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

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
            $urlDestTab = explode('var', $urlDest);

            $repoDiscipline = $em->getRepository('AppBundle:Discipline');
            $repoUser = $em->getRepository('AppBundle:User');
            $discipline = $repoDiscipline->findOneBy(array('id' => $discId));
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

                $inscrits = $repoDiscipline->findInscrits($discipline);
                if($inscrits){
                    foreach($inscrits as $inscrit){
                        //$this->sendMailDoc($inscrit, $proprietaire, 'la discipline '.$discipline->getNom());
                    }
                }
            }else{
                $repoCohorte = $em->getRepository('AppBundle:Cohorte');
                $repoInscription_coh = $em->getRepository('AppBundle:Inscription_coh');
                $repoInscription_d = $em->getRepository('AppBundle:Inscription_d');
                for($i=0; $i<count($users); $i++){
                    $assocInscr = new AssocDocInscr();
                    $assocInscr->setDocument($doc);
                    $assocInscr->setIsImportant($isImportant);

                    $user = $repoUser->findOneBy(array('id' => $users[$i]));

                    // on commence par voir si le user est inscrit à une cohorte qui est inscrite à cette discipline
                    $cohortes = $repoCohorte->findAll();
                    $estAssocie = false;
                    foreach($cohortes as $cohorte){
                        if($cohorte->getDisciplines()->contains($discipline)){
                            $inscrCoh = $repoInscription_coh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                            if($inscrCoh){
                                $assocInscr->setInscription($inscrCoh);
                                $assocInscr->setTypeInscr('coh');
                                $estAssocie = true;
                                break 1;
                            }
                        }
                    }

                    // pas de cohorte, on cherche une inscription directe à la discipline
                    if(!$estAssocie){
                        $inscrD = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $discipline));
                        if($inscrD){
                            $assocInscr->setInscription($inscrD);
                            $assocInscr->setTypeInscr('dis');
                            $estAssocie = true;
                        }
                    }
                    if($estAssocie){
                        //$this->sendMailDoc($user, $proprietaire, 'la discipline '.$discipline->getNom());
                        $em->persist($assocInscr);
                    }
                }
            }

            $em->flush();
            return new JsonResponse(array('action' =>'upload Document for Discipline', 'id' => $discId, 'ext' => $ext));
        }
        return new JsonResponse('This is not ajax!', 400);
    }

    public function sendMailDoc(User $user, User $sender, $conteneurName){
        $message = \Swift_Message::newInstance()
            ->setSubject('[AFADEC] Document déposé')
            ->setFrom('noreply@afadec.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'documents/depotMail.html.twig',
                    array(
                        'prenom' => $user->getFirstname(),
                        'nom' => $user->getLastname(),
                        'id' => $user->getId(),
                        'senderPrenom' => $sender->getFirstname(),
                        'senderNom' => $sender->getLastname(),
                        'conteneurName' => $conteneurName
                    )
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
    }

    /**
     * @Route("/uploadDocCoursFile_ajax", name="uploadDocCoursFile_ajax")
     */
    public function uploadDocCoursFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

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
            $urlDestTab = explode('var', $urlDest);

            $repoCours = $em->getRepository('AppBundle:Cours');
            $repoUser = $em->getRepository('AppBundle:User');
            $cours = $repoCours->findOneBy(array('id' => $coursId));
            $proprietaire = $repoUser->findOneBy(array('id' => $userId));

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
                $em->flush();


                $inscrits = $repoCours->findInscrits($cours);
                if($inscrits){
                    foreach($inscrits as $inscrit){
                        //$this->sendMailDoc($inscrit, $proprietaire, 'le cours '.$cours->getNom());
                    }
                }

                return new JsonResponse(array('action' =>'upload Document for Cours : All', 'id' => $coursId, 'ext' => $ext));
            }else{
                $repoCohorte = $em->getRepository('AppBundle:Cohorte');
                $repoInscription_coh = $em->getRepository('AppBundle:Inscription_coh');
                $repoInscription_d = $em->getRepository('AppBundle:Inscription_d');
                $repoInscription_c = $em->getRepository('AppBundle:Inscription_c');
                for($i=0; $i<count($users); $i++){
                    $assocInscr = new AssocDocInscr();
                    $assocInscr->setDocument($doc);
                    $assocInscr->setIsImportant($isImportant);
                    $assocInscr->setCours($cours);

                    $user = $repoUser->findOneBy(array('id' => $users[$i]));

                    // on commence par voir si le user est inscrit à une cohorte qui est inscrite à ce cours
                    $cohortes = $repoCohorte->findAll();
                    $estAssocie = false;
                    foreach($cohortes as $cohorte){
                        if($cohorte->getDisciplines()->contains($cours->getDiscipline()) || $cohorte->getCours()->contains($cours)){
                            $inscrCoh = $repoInscription_coh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                            if($inscrCoh){
                                $assocInscr->setInscription($inscrCoh);
                                $assocInscr->setTypeInscr('coh');
                                $estAssocie = true;
                                break 1;
                            }
                        }
                    }

                    // pas de cohorte, on cherche une inscription directe au cours
                    if(!$estAssocie){
                        $inscrD = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $cours->getDiscipline()));
                        if($inscrD){
                            $assocInscr->setInscription($inscrD);
                            $assocInscr->setTypeInscr('dis');
                            $estAssocie = true;
                        }
                    }
                    if(!$estAssocie){
                        $inscrC = $repoInscription_c->findOneBy(array('user' => $user, 'cours' => $cours));
                        if($inscrC){
                            $assocInscr->setInscription($inscrC);
                            $assocInscr->setTypeInscr('cours');
                            $estAssocie = true;
                        }
                    }
                    if($estAssocie){
                        //$this->sendMailDoc($user, $proprietaire, 'le cours '.$cours->getNom());
                        $em->persist($assocInscr);
                    }
                }
                $em->flush();
                return new JsonResponse(array('action' =>'upload Document for Cours : not All', 'id' => $coursId, 'ext' => $ext, 'users'=> $users));
            }
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

            date_default_timezone_set('Europe/Paris');

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
