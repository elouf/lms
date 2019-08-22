<?php

namespace AppBundle\Repository;

/**
 * CoursRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CoursRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByDisc($disc)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.discipline = :disc')
            ->setParameter('disc', $disc);

        return $qb->getQuery()->getResult();
    }

    public function findRole($user, $cours)
    {
        $em = $this->getEntityManager();
        $discipline = $cours->getDiscipline();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours, 'user' => $user));
        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline, 'user' => $user));
        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));

        $role = "Etudiant";

        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline) || $coh->getCours()->contains($cours)){
                    $role = $inscrCoh->getRole()->getNom();
                }

            }
        }
        if($role == "Etudiant"){
            if($inscrDs){
                $role = $inscrDs->getRole()->getNom();
            }
        }
        if($role == "Etudiant"){
            if($inscrCs){
                $role = $inscrCs->getRole()->getNom();
            }
        }
        return $role;
    }

    public function findInscrits($cours)
    {
        $em = $this->getEntityManager();
        $session = $cours->getSession();
        $discipline = $cours->getDiscipline();

        $users = array();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        if($inscrCs){
            foreach($inscrCs as $inscrC){
                $inscrUser = $inscrC->getUser();
                if(!in_array($inscrUser, $users) && $inscrUser->isEnabled()){
                    array_push($users, $inscrUser);
                }
            }
        }

        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                $inscrUser = $inscrD->getUser();
                if(!in_array($inscrUser, $users) && $inscrUser->isEnabled()){
                    array_push($users, $inscrUser);
                }
            }
        }

        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findAll();
        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline) || $coh->getCours()->contains($cours)){
                    $cohUser = $inscrCoh->getUser();
                    if(!in_array($cohUser, $users) && $cohUser->isEnabled()){
                        array_push($users, $cohUser);
                    }
                }

            }
        }

        $userToSend = array();
        $repositoryInscrSess = $em->getRepository('AppBundle:Inscription_sess');
        if($session){
            foreach($users as $user){
                $inscSess = $repositoryInscrSess->findBy(array('session' => $session, 'user' => $user));
                if($inscSess){
                    array_push($userToSend, $user);
                }
            }
        }else{
            $userToSend = $users;
        }

        return $userToSend;
    }

    public function userIsInscrit($user, $cours)
    {
        return $this->getUserInscr($user, $cours)!=null;
    }

    public function getUserInscr($user, $cours){
        $em = $this->getEntityManager();

        $insc = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours, 'user' => $user));
        return $insc;
    }

    public function userHasAccess($user, $cours)
    {
        $em = $this->getEntityManager();
        $repositoryDiscipline = $em->getRepository('AppBundle:Discipline');
        $disc = $cours->getDiscipline();

        if($repositoryDiscipline->userHasAccess($user, $disc) ||
            $repositoryDiscipline->userIsInscrit($user, $disc)
        ){
            return true;
        }else{
            return false;
        }
    }

    public function userHasAccessOrIsInscrit($user, $cours)
    {
        return $this->userHasAccess($user, $cours) || $this->userIsInscrit($user, $cours);
    }

    public function getRole($user, $item)
    {
        $em = $this->getEntityManager();
        $discipline = $item->getDiscipline();

        $inscr = $em->getRepository('AppBundle:Inscription_c')->findOneBy(array('cours' => $item, 'user' => $user));
        if($inscr){
            return $inscr->getRole();
        }else{
            $inscrD = $em->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $discipline, 'user' => $user));
            if($inscrD){
                return $inscrD->getRole();
            }else{
                $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));
                if($inscrCohs){
                    foreach($inscrCohs as $inscrCoh){
                        $coh = $inscrCoh->getCohorte();
                        if($coh->getDisciplines()->contains($discipline) || $coh->getCours()->contains($item)){
                            return $inscrCoh->getRole();
                        }
                    }
                }
            }
        }
        return null;
    }
}
