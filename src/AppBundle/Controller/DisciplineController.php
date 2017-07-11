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
     * @Route("/myCourses/{id}", defaults={"id" = 0}, name="myCourses")
     */
    public function myCoursesAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repositoryCours = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $repositoryCoh = $this->getDoctrine()->getRepository('AppBundle:Cohorte');

        $disciplines = $this->getDoctrine()->getRepository('AppBundle:Discipline')->findBy(array(), array('nom' => 'ASC'));

        // Par défaut, admin : toutes les disciplines
        $disciplinesArray2Consider = $disciplines;

        $coursesIndiv = array();

        // si ce n'est pas l'admin, on fait le tri
        if (!$this->getUser()->hasRole('ROLE_SUPER_ADMIN')){
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

        $courses = array();
        // on construit le tableau des disciplines/cours complètes
        for($i=0; $i<count($disciplinesArray2Consider); $i++){
            $courses[$i]["courses"] = array();
            $courses[$i]["sessions"] = array();
            $courses[$i]["sessionsAlerte"] = array();
            $courses[$i]["sessionsAlerteIsInscrit"] = array();
            $courses[$i]["discipline"] = $disciplinesArray2Consider[$i];
            $coursesT = $repositoryCours->findBy(array('discipline' =>$disciplinesArray2Consider[$i]), array('position' => 'ASC'));
            for($j=0; $j<count($coursesT); $j++){
                if(!$coursesT[$j]->getSession()) {
                    array_push($courses[$i]["courses"], $coursesT[$j]);
                }else{
                    $session = $coursesT[$j]->getSession();
                    $currentDate = new DateTime();
                    $inscrSess = $this->getDoctrine()
                        ->getRepository('AppBundle:Inscription_sess')
                        ->findOneBy(array('user' => $this->getUser(), 'session' => $session));
                    // on est inscrit et les dates sont bonnes (ou on est admin ou enseignant)
                    $isEns = false;
                    if($inscrSess){
                        if($inscrSess->getRole() == "Enseignant"){
                            $isEns = true;
                        }
                    }

                    if(($currentDate >= $session->getDateDebut() &&
                        $currentDate <= $session->getDateFin() &&
                        $inscrSess) ||
                        $this->getUser()->hasRole('ROLE_SUPER_ADMIN') ||
                        $isEns
                    ){
                        array_push($courses[$i]["sessions"], $coursesT[$j]);
                    }elseif($currentDate < $session->getDateDebut() && $currentDate >= $session->getDateDebutAlerte() && $currentDate < $session->getDateFinAlerte()){
                        // la date de début n'est pas encore commencée, mais la date d'alerte oui : on doit permettre de s'inscrire si ce n'est pas fait
                        array_push($courses[$i]["sessionsAlerte"], $coursesT[$j]);
                        array_push($courses[$i]["sessionsAlerteIsInscrit"], $inscrSess != null);
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
        ////Comme un accès aux documents de la discipline existe, on doit afficher l'info-bulle si certains n'ont pas été visités
        for($j=0; $j<count($courses); $j++){
            $docs = $this->getDoctrine()->getRepository('AppBundle:Document')->findByDisc($courses[$j]["discipline"], $this->getUser());
            $documents = array_merge($docs[0], $docs[1]);

            $nbNewDocs = 0;
            foreach($documents as $doc){
                $stat = $this->getDoctrine()->getRepository('AppBundle:StatsUsersDocs')->findBy(array('user' => $this->getUser(), 'document' => $doc));
                if(!$stat){
                    $nbNewDocs++;
                }
            }
            $courses[$j]["nbNewDocs"] = $nbNewDocs;
        }

        return $this->render('discipline/myCourses.html.twig', ['courses' => $courses, 'default' => $id]);
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

            $id = $request->request->get('id');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));

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
