<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StatistiquesController extends Controller
{

    /**
     * @Route("/stats", name="stats")
     */
    public function statsAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $repoCohorte = $this->getDoctrine()->getRepository('AppBundle:Cohorte');
        $cohortes = $repoCohorte->findAll();
        $cohortesArr = array();

        for($i=0; $i<count($cohortes); $i++) {
            $cohortesArr[$i]["cohorte"] = $cohortes[$i];
            $inscrits = $repoCohorte->findInscrits($cohortes[$i]);
            $cohortesArr[$i]["inscrits"] = $inscrits;
        }

        return $this->render('stats.html.twig', [
            'userId' => $this->getUser()->getId(),
            'cohortes' => $cohortesArr
        ]);
    }

    /**
     * @Route("/stats/usersDocs/disc/{discId}", name="statsDocsByUsersDisc")
     */
    public function statsDocsByUsersDiscAction (Request $request, $discId)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $disc = $this->getDoctrine()->getRepository('AppBundle:Discipline')->find($discId);

        $assocs = $this->getDoctrine()->getRepository('AppBundle:AssocDocCours')->findAll();

        $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $userRepo->findAll();

        $myAssocs = array();

        if($assocs) {
            foreach ($assocs as $assoc) {
                if($assoc->getCours()->getDiscipline()->getId() == $discId){
                    $user = $assoc->getDocument()->getProprietaire();

                    $keyUser = array_search($user, array_column($myAssocs, 'user'));

                    if($keyUser !== false){
                        $keyCours = array_search($assoc->getCours(), array_column($myAssocs[$keyUser]['depots'], 'cours'));

                        if($keyCours !== false){
                            $myAssocs[$keyUser]['depots'][$keyCours]['nombre']++;
                        }else{
                            $depot = array(
                                'cours' => $assoc->getCours(),
                                'nombre' => 1
                            );
                            array_push($myAssocs[$keyUser]['depots'], $depot);
                        }
                    }else{
                        $depot = array(array(
                            'cours' => $assoc->getCours(),
                            'nombre' => 1
                        ));
                        $oneUser = array(
                            'user' => $user,
                            'depots' => $depot
                        );
                        array_push($myAssocs, $oneUser);
                    }
                }
            }
        }

        return $this->render('stats/statsDocsByUserDisc.html.twig', [
            'discipline' => $disc,
            'assocs' => $myAssocs,
            'users' => $users
        ]);
    }

}
