<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{

    /**
     * @Route("/usersManag", name="usersManag")
     */
    public function usersManagAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');



        return $this->render('user/userFrontEnd.html.twig', [
            'events' => "test"
        ]);
    }

    /**
     * @Route("/user/{id}", name="user")
     */
    public function userAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        $discs = $this->getDoctrine()->getRepository('AppBundle:Discipline')->findAll();
        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findAll();



        return $this->render('user/one.html.twig', [
            'user' => $user
        ]);
    }
}
