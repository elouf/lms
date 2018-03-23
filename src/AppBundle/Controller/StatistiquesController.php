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

        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        $cohortesArr = array();

        for($i=0; $i<count($cohortes); $i++) {
            $cohortesArr[$i]["cohorte"] = $cohortes[$i];
            $inscrits = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findInscrits($cohortes[$i]->getId());
            $cohortesArr[$i]["inscrits"] = $inscrits;
        }

        return $this->render('stats.html.twig', [
            'userId' => $this->getUser()->getId(),
            'cohortes' => $cohortesArr
        ]);
    }

}
