<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FreeAccessStats;
use AppBundle\Entity\Mp3Podcast;
use AppBundle\Entity\UserStatCours;
use AppBundle\Entity\UserStatLogin;
use AppBundle\Entity\UserStatRessource;
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
     * @throws \Exception
     */
    public function frequentationAction(Request $request)
    {
        ini_set('memory_limit','-1');

        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getEntityManager();

        $logins = [];
        $coursAccess = [];
        $ressourcesAccess = [];
        $startingDate = DateTime::createFromFormat('j-M-Y', '01-Mar-2020');

        // Get connection
        $conn = $em->getConnection();

        // Get table name
        $metaUserStatLogin = $em->getClassMetadata(UserStatLogin::class);
        $tableNameUserStatLogin = $metaUserStatLogin->getTableName();

        $sql = "SELECT dateAcces AS dateAcces FROM $tableNameUserStatLogin ORDER BY dateAcces";
        $statement = $conn->executeQuery($sql);
        $userLogins = array_map(function ($element) {
            return new DateTime($element['dateAcces']);
        }, $statement->fetchAll());

        $metaUserStatCours = $em->getClassMetadata(UserStatCours::class);
        $tableNameUserStatCours = $metaUserStatCours->getTableName();

        $sql = "SELECT dateAcces AS dateAcces FROM $tableNameUserStatCours ORDER BY dateAcces";
        $statement = $conn->executeQuery($sql);
        $userStatCours = array_map(function ($element) {
            return new DateTime($element['dateAcces']);
        }, $statement->fetchAll());

        $metaUserStatRessource = $em->getClassMetadata(UserStatRessource::class);
        $tableNameUserStatRessource = $metaUserStatRessource->getTableName();

        $sql = "SELECT dateAcces AS dateAcces FROM $tableNameUserStatRessource ORDER BY dateAcces";
        $statement = $conn->executeQuery($sql);
        $userStatRessource = array_map(function ($element) {
            return new DateTime($element['dateAcces']);
        }, $statement->fetchAll());

        if ($userLogins){
            foreach ($userLogins as $d){
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

        if ($userStatCours){
            foreach ($userStatCours as $d){
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

        if ($userStatRessource){
            foreach ($userStatRessource as $d){
                if($d >= $startingDate){
                    $date = $d->format('d/m');
                    if(!array_key_exists($date, $ressourcesAccess)){
                        $ressourcesAccess[$date] = 1;
                    }else{
                        $ressourcesAccess[$date]++;
                    }
                }
            }
        }

        return $this->render('stats/frequentationSite.html.twig', [
            'logins' => $logins,
            'coursAcces' => $coursAccess,
            'ressourceAcces' => $ressourcesAccess
        ]);
    }
}
