<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CalendarController extends Controller
{

    /**
     * @Route("/calendrier", name="calendrier")
     */
    public function myCalendarAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repositoryCours = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $repositoryCoh = $this->getDoctrine()->getRepository('AppBundle:Cohorte');

        $repositoryDisc = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $repositoryDisc->findAll();

        $myEvents = array();

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

        // on ajoute les evt de discipline
        for($j=0; $j<count($disciplinesArray2Consider); $j++){
            $repositoryEvtD = $this->getDoctrine()->getRepository('AppBundle:Evt_discipline')->findBy(array('discipline'=> $disciplinesArray2Consider[$j]));
            foreach($repositoryEvtD as $evtD){
                array_push($myEvents, array('evt' => $evtD, 'type' => 'discEvt'));
            }
        }

        $courses = array();

        // on construit le tableau des disciplines/cours complètes
        for($i=0; $i<count($disciplinesArray2Consider); $i++){
            $courses[$i]["discipline"] = $disciplinesArray2Consider[$i];
            $courses[$i]["courses"] = $repositoryCours->findBy(array('discipline' => $disciplinesArray2Consider[$i]));
        }
        // on lui ajoute les cours individuels (avec leurs disciplines
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

        // on ajoute les evt de cours et les ressources éventuelles
        for($j=0; $j<count($courses); $j++){
            $repositoryEvtC = $this->getDoctrine()->getRepository('AppBundle:Evt_cours')->findBy(array('cours'=> $courses[$j]["courses"]));
            foreach($repositoryEvtC as $evtC){
                array_push($myEvents, array('evt' => $evtC, 'type' => 'coursEvt'));
            }
            $repositoryDevoir = $this->getDoctrine()->getRepository('AppBundle:Devoir')->findBy(array('cours'=> $courses[$j]["courses"]));
            foreach($repositoryDevoir as $evtDevoir){
                array_push($myEvents, array('evt' => $evtDevoir, 'type' => 'devoirEvt'));
            }
        }

        // on ajoute les evt de user
        $repositoryEvtU = $this->getDoctrine()->getRepository('AppBundle:Evt_user')->findBy(array('user'=> $this->getUser()));
        if($repositoryEvtU){
            foreach($repositoryEvtU as $evtU){
                array_push($myEvents, array('evt' => $evtU, 'type' => 'userEvt'));
            }
        }

        // on ajoute les sessions
        $sessions = $this->getDoctrine()->getRepository('AppBundle:Session')->findAll();
        if($sessions){
            foreach($sessions as $session){
                array_push($myEvents, array('evt' => $session, 'type' => 'sessionEvt'));
            }
        }

        return $this->render('calendrier.html.twig', ['events' => $myEvents]);
    }
}
