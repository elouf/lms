<?php

namespace AppBundle\Service;

use AppBundle\Entity\AssocDocCours;
use AppBundle\Entity\AssocDocDisc;
use AppBundle\Entity\AssocDocInscr;
use AppBundle\Entity\Cohorte;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Devoir;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use AppBundle\Entity\Evt_cours;
use AppBundle\Entity\Evt_discipline;
use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_coh;
use AppBundle\Entity\Inscription_d;
use AppBundle\Entity\Log;
use AppBundle\Entity\Session;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class CalendarServ
{
    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getMyCalendarDatas($user){
        try {
            $this->em->flush();
        } catch (ORMException $e) {
        }

        $repositoryCours = $this->em->getRepository('AppBundle:Cours');

        $repositoryCoh = $this->em->getRepository('AppBundle:Cohorte');

        $repositoryDisc = $this->em->getRepository('AppBundle:Discipline');
        $disciplines = $repositoryDisc->findAll();

        $myEvents = array();

        // Par défaut, admin : toutes les disciplines
        $disciplinesArray2Consider = $disciplines;

        $coursesIndiv = array();

        // si ce n'est pas l'admin, on fait le tri
        if (!$user->hasRole('ROLE_SUPER_ADMIN')){
            // on créé un tableau qui contient les disciplines auxquelles l' user est inscrit par cohorte
            $repositoryInscrCoh = $this->em->getRepository('AppBundle:Inscription_coh');
            $inscrsCoh = $repositoryInscrCoh->findBy(array('user'=> $user));
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
            $repositoryInscrD = $this->em->getRepository('AppBundle:Inscription_d');
            $inscrsD = $repositoryInscrD->findBy(array('user'=> $user));
            foreach($inscrsD as $inscrD){
                if(!in_array($inscrD->getDiscipline(), $disciplinesArray2Consider)){
                    array_push($disciplinesArray2Consider, $inscrD->getDiscipline());
                }
            }
            $myDiscs = $disciplinesArray2Consider;

            // enfin, on ajoute les cours auxquels l'utilisateur est inscrit individuellement (du coup une portion de discipline)
            $repositoryInscrC = $this->em->getRepository('AppBundle:Inscription_c');
            $inscrsC = $repositoryInscrC->findBy(array('user'=> $user));
            /* @var Inscription_c $inscrC */
            foreach($inscrsC as $inscrC){
                if(!in_array($inscrC->getCours()->getDiscipline(), $disciplinesArray2Consider)){
                    array_push($coursesIndiv, $inscrC->getCours());
                    array_push($myDiscs, $inscrC->getCours()->getDiscipline());
                }
            }
        }else{
            $myDiscs = $disciplines;
        }

        // on ajoute les evt de discipline
        for($j=0; $j<count($disciplinesArray2Consider); $j++){
            $repositoryEvtD = $this->em->getRepository('AppBundle:Evt_discipline')->findBy(array('discipline'=> $disciplinesArray2Consider[$j]));
            /* @var $evtD Evt_discipline */
            foreach($repositoryEvtD as $evtD){
                array_push($myEvents, array('evt' => $evtD, 'type' => 'discEvt', 'disc' => $evtD->getDiscipline()->getId()));
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
            $repositoryEvtC = $this->em->getRepository('AppBundle:Evt_cours')->findBy(array('cours'=> $courses[$j]["courses"]));
            /* @var $evtC Evt_cours */
            foreach($repositoryEvtC as $evtC){
                array_push($myEvents, array('evt' => $evtC, 'type' => 'coursEvt', 'disc' => $evtC->getCours()->getDiscipline()->getId()));
            }
            $repositoryDevoir = $this->em->getRepository('AppBundle:Devoir')->findBy(array('cours'=> $courses[$j]["courses"]));
            /* @var $evtDevoir Devoir */
            foreach($repositoryDevoir as $evtDevoir){
                array_push($myEvents, array('evt' => $evtDevoir, 'type' => 'devoirEvt', 'disc' => $evtDevoir->getCours()->getDiscipline()->getId()));
            }
        }

        // on ajoute les evt de user
        $repositoryEvtU = $this->em->getRepository('AppBundle:Evt_user')->findBy(array('user'=> $user));
        if($repositoryEvtU){
            foreach($repositoryEvtU as $evtU){
                array_push($myEvents, array('evt' => $evtU, 'type' => 'userEvt', 'disc' => ''));
            }
        }

        // on ajoute les sessions
        $sessions = $this->em->getRepository('AppBundle:Session')->findAll();
        if($sessions){
            /* @var $session Session */
            foreach($sessions as $session){
                array_push($myEvents, array('evt' => $session, 'type' => 'sessionEvt', 'disc' => ''));
            }
        }

        return array('events' => $myEvents, 'myDiscs' => $myDiscs);
    }
}