<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Discipline;
use AppBundle\Entity\Inscription_sess;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class DisciplineController extends Controller
{

    /**
     * @Route("/discCoursManag", name="discCoursManag")
     */
    public function discCoursManagAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $disRepo = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $disRepo->findAll();

        $myDisc = array();
        $i = 0;
        if($disciplines){
            foreach($disciplines as $discipline){
                $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $discipline));
                $myDisc[$i]['discipline'] = $discipline;

                $myDisc[$i]['cours'] = array();
                if($cours) {
                    foreach ($cours as $cour) {
                        array_push($myDisc[$i]['cours'], $cour);
                    }
                }
                $i++;
            }
        }
        return $this->render('ressources/allDiscCours.html.twig', [
            'disciplines' => $myDisc
        ]);
    }

    /**
     * @Route("/myCourses/{id}", defaults={"id" = 0}, name="myCourses")
     */
    public function myCoursesAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        date_default_timezone_set('Europe/Paris');

        $repositoryCours = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $repositoryCoh = $this->getDoctrine()->getRepository('AppBundle:Cohorte');

        $repositoryDis = $this->getDoctrine()->getRepository('AppBundle:Discipline');

        $repositoryInscrSess = $this->getDoctrine()->getRepository('AppBundle:Inscription_sess');

        $repositoryStatsUsersDocs = $this->getDoctrine()->getRepository('AppBundle:StatsUsersDocs');

        $repositoryDocuments = $this->getDoctrine()->getRepository('AppBundle:Document');

        $disciplines = $repositoryDis->findBy(array(), array('nom' => 'ASC'));

        // Par défaut, admin : toutes les disciplines
        $disciplinesArray2Consider = $disciplines;
        $cohLiees = array();

        $coursesIndiv = array();

        // si ce n'est pas l'admin, on fait le tri
        if (!($this->getUser()->hasRole('ROLE_SUPER_ADMIN'))){
            // on créé un tableau qui contient les disciplines auxquelles l' user est inscrit par cohorte
            $repositoryInscrCoh = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh');
            $inscrsCoh = $repositoryInscrCoh->findBy(array('user'=> $this->getUser()));
            $discInscrCoh = array();
            foreach($inscrsCoh as $inscrCoh){
                $cohorte = $repositoryCoh->find($inscrCoh->getCohorte());
                foreach($cohorte->getDisciplines() as $disc){
                    if(!in_array($disc, $discInscrCoh)){
                        array_push($discInscrCoh, $disc);
                    }
                }
            }

            $disciplinesArray2Consider = $discInscrCoh;

            // on ajoute les disciplines auxquelles le user est inscrit directement
            $repositoryInscrD = $this->getDoctrine()->getRepository('AppBundle:Inscription_d');
            $inscrsD = $repositoryInscrD->findBy(array('user'=> $this->getUser()));
            foreach($inscrsD as $inscrD){
                if(!in_array($inscrD->getDiscipline(), $disciplinesArray2Consider)){
                    array_push($disciplinesArray2Consider, $inscrD->getDiscipline());
                }
            }

            // enfin, on ajoute les cours auxquels l'utilisateur est inscrit individuellement (du coup une portion de discipline)
            $repositoryInscrC = $this->getDoctrine()->getRepository('AppBundle:Inscription_c');
            $inscrsC = $repositoryInscrC->findBy(array('user'=> $this->getUser()));
            foreach($inscrsC as $inscrC){
                if(!in_array($inscrC->getCours()->getDiscipline(), $disciplinesArray2Consider)){
                    array_push($coursesIndiv, $inscrC->getCours());
                }
            }
        }
        if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN') || $this->getUser()->getStatut() == 'Responsable' || $this->getUser()->getStatut() == 'Formateur'){
            // on ajoute les cohortes liées pour l'admin pour qu'il puisse accéder aux pages d'inscriptions à ces cohortes
            for($i=0; $i<count($disciplinesArray2Consider); $i++){
                $cohLiees[$i] = $repositoryDis->getCohortes($disciplinesArray2Consider[$i]->getId());
            }
        }

        $courses = array();
        // on construit le tableau des disciplines/cours complètes
        for($i=0; $i<count($disciplinesArray2Consider); $i++){
            $courses[$i]["courses"] = array();
            $courses[$i]["sessions"] = array();
            $courses[$i]["sessionsAdmin"] = array();
            $courses[$i]["sessionsAlerte"] = array();
            $courses[$i]["sessionsAlerteIsInscrit"] = array();
            $courses[$i]["sessionsFinSession"] = array();
            $courses[$i]["discipline"] = $disciplinesArray2Consider[$i];
            $courses[$i]["cohortesLiees"] = array();
            if ($this->getUser()->hasRole('ROLE_SUPER_ADMIN') || $this->getUser()->getStatut() == 'Responsable' || $this->getUser()->getStatut() == 'Formateur'){
                $courses[$i]["cohortesLiees"] = $cohLiees[$i];
            }
            $coursesT = $repositoryCours->findBy(array('discipline' =>$disciplinesArray2Consider[$i]), array('position' => 'ASC'));
            for($j=0; $j<count($coursesT); $j++){
                if(!$coursesT[$j]->getSession()) {
                    array_push($courses[$i]["courses"], $coursesT[$j]);
                }else{
                    $session = $coursesT[$j]->getSession();
                    $currentDate = new DateTime();
                    $inscrSess = $repositoryInscrSess->findOneBy(array('user' => $this->getUser(), 'session' => $session));
                    // on est inscrit et les dates sont bonnes (ou on est admin ou enseignant)
                    $isEns = false;
                    if($inscrSess){
                        if($inscrSess->getRole() == "Enseignant"){
                            $isEns = true;
                        }
                    }

                    if($currentDate >= $session->getDateDebut() &&
                        $currentDate <= $session->getDateFin() &&
                        ($inscrSess || $this->getUser()->hasRole('ROLE_SUPER_ADMIN') || (($this->getUser()->getStatut() == 'Responsable' || $this->getUser()->getStatut() == 'Formateur') && $this->getUser()->getConfirmedByAdmin()) || $isEns)
                        )
                    {
                        // on peut rentrer dans la session et on est dans les dates
                        array_push($courses[$i]["sessions"], $coursesT[$j]);
                    }elseif($currentDate >= $session->getDateDebutAlerte() && $currentDate < $session->getDateFinAlerte()){
                        // on affiche l'alerte et on permet de s'inscrire
                        array_push($courses[$i]["sessionsAlerte"], $coursesT[$j]);
                        array_push($courses[$i]["sessionsAlerteIsInscrit"], $inscrSess != null);
                    }elseif($currentDate >= $session->getDateFinAlerte() && $currentDate < $session->getDateFin()){
                        // on affiche le message de fin de session
                        array_push($courses[$i]["sessionsFinSession"], $coursesT[$j]);
                    }elseif($this->getUser()->hasRole('ROLE_SUPER_ADMIN') || $isEns  || (($this->getUser()->getStatut() == 'Responsable' || $this->getUser()->getStatut() == 'Formateur') && $this->getUser()->getConfirmedByAdmin())){
                        // on peut rentrer dans la session hors des dates
                        array_push($courses[$i]["sessionsAdmin"], $coursesT[$j]);
                    }
                }
            }
        }
        // on lui ajoute les cours individuels (avec leurs disciplines)
        for($j=0; $j<count($coursesIndiv); $j++){
            $discExists = false;
            for($k=0; $k<count($courses); $k++){
                if($courses[$k]["discipline"] == $coursesIndiv[$j]->getDiscipline()){
                    array_push($courses[$k]["courses"], $coursesIndiv[$j]);
                    $discExists = true;
                }
            }
            if(!$discExists){
                $idx = count($courses);
                $courses[$idx]["discipline"] = $coursesIndiv[$j]->getDiscipline();
                $courses[$idx]["courses"] = array($coursesIndiv[$j]);
            }
        }

        // on recherche les infos liées aux documents
        // Comme un accès aux documents de la discipline existe, on doit afficher l'info-bulle si certains n'ont pas été visités
        for($j=0; $j<count($courses); $j++){
            $docs = $repositoryDocuments->findByDisc($courses[$j]["discipline"], $this->getUser());
            $documents = array_merge($docs[0], $docs[1]);

            $nbNewDocs = 0;
            foreach($documents as $doc){
                $stat = $repositoryStatsUsersDocs->findBy(array('user' => $this->getUser(), 'document' => $doc));
                if(!$stat){
                    $nbNewDocs++;
                }
            }
            $courses[$j]["nbNewDocs"] = $nbNewDocs;
        }
        return $this->render('discipline/myCourses.html.twig', ['courses' => $courses, 'active' => $id]);
    }

    /**
     * @Route("/discs", name="disciplines")
     */
    public function disciplinesAction (Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $repository->findAll();

        return $this->render('discipline/list.html.twig', ['disciplines' => $disciplines]);
    }

    /**
     * @Route("/disc/{id}", name="oneDiscipline")
     */
    public function oneDisciplineAction (Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $discipline = $repository->find($id);

        $courses = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $discipline));

        return $this->render('discipline/one.html.twig', ['discipline' => $discipline, 'courses' => $courses]);
    }

    /**
     * @Route("/changeActivationDocsDisc_ajax", name="changeActivationDocsDisc_ajax")
     */
    public function changeActivationDocsDiscAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $disc = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));
            $disc->setDocsActivated($isVisible == "false");

            $em->persist($disc);
            $em->flush();

            return new JsonResponse(array('action' =>'change Visibility of documents', 'id' => $disc->getId(), 'isVisible' => $disc->getDocsActivated()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/inscrSess_ajax", name="inscrSess_ajax")
     */
    public function inscrSessAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));

            $roleInCours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));

            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));

            $inscr = new Inscription_sess();
            $inscr->setSession($session);
            $inscr->setUser($this->getUser());
            $inscr->setDateInscription(new DateTime());
            if($role){
                $inscr->setRole($role);
            }

            $em->persist($inscr);
            $em->flush();

            return new JsonResponse(array('action' =>'Inscription user session'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
