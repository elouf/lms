<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EmailingController extends Controller
{

    /**
     * @Route("/emailing", name="emailing")
     */
    public function myCalendarAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $sessions = $this->getDoctrine()->getRepository('AppBundle:Session')->findAll();

        return $this->render('emailing.html.twig', ['users' => $users, 'sessions' => $sessions]);
    }
}
