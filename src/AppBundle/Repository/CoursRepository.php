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

    public function findInscrits($id)
    {
        $em = $this->getEntityManager();
        $cours = $this->findOneBy(array('id'=> $id));
        $discipline = $cours->getDiscipline();

        $users = array();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        if($inscrCs){
            foreach($inscrCs as $inscrC){
                if(!in_array($inscrC->getUser(), $users)){
                    array_push($users, $inscrC->getUser());
                }
            }
        }

        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users)){
                    array_push($users, $inscrD->getUser());
                }
            }
        }

        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findAll();
        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();
                if($coh->getDisciplines()->contains($discipline) || $coh->getCours()->contains($cours)){
                    if(!in_array($inscrCoh->getUser(), $users)){
                        array_push($users, $inscrCoh->getUser());
                    }
                }

            }
        }
        return $users;
    }
}
