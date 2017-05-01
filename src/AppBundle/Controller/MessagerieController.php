<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MessagerieController extends Controller
{

    /**
     * @Route("/messagerie", name="messagerie")
     */
    public function myMessagesAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $assocsUserMsg = $this->getDoctrine()->getRepository('AppBundle:AssocUserMsg')->findBy(array('user' => $this->getUser()));

        $messages = array();
        foreach($assocsUserMsg as $assoc){
            $msg = $assoc->getMessage();
            if(!in_array($msg, $messages)){
                array_push($messages, $msg);
            }
        }

        return $this->render('messagerie.html.twig', ['messages' => $messages]);
    }
}
