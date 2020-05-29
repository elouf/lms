<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FreeAccessStats;
use AppBundle\Entity\Mp3Podcast;
use AppBundle\Entity\UserStatCours;
use AppBundle\Entity\UserStatLogin;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

    /**
     * @Route("/frequentationSite", name="frequentationSite")
     */
    public function frequentationAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getEntityManager();

        $logins = [];
        $coursAccess = [];
        $startingDate = DateTime::createFromFormat('j-M-Y', '01-Mar-2020');

        $userLogins = $em->getRepository('AppBundle:UserStatLogin')->findBy([], array('dateAcces' => 'ASC'));
        if ($userLogins){
            /* @var $userLogin UserStatLogin */
            foreach ($userLogins as $userLogin){
                $d = $userLogin->getDateAcces();
                if($d >= $startingDate){
                    $date = $d->format('d/m');
                    if(!array_key_exists($date, $logins)){
                        $logins[$date] = 1;
                    }else{
                        $logins[$date]++;
                    }
                }

            }
        }
        $userCours = $em->getRepository('AppBundle:UserStatCours')->findBy([], array('dateAcces' => 'ASC'));
        if ($userCours){
            /* @var $userCour UserStatCours */
            foreach ($userCours as $userCour){
                $d = $userCour->getDateAcces();
                if($d >= $startingDate){
                    $date = $d->format('d/m');
                    if(!array_key_exists($date, $coursAccess)){
                        $coursAccess[$date] = 1;
                    }else{
                        $coursAccess[$date]++;
                    }
                }

            }
        }

        return $this->render('stats/frequentationSite.html.twig', [
            'logins' => $logins,
            'coursAcces' => $coursAccess
        ]);
    }
}
