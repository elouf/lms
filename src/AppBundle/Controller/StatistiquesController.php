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

        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte');

        return $this->render('stats.html.twig', [
            'userId' => $this->getUser()->getId(),
            'cohortes' => $cohortes
        ]);
    }

}
