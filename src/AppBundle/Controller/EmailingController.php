<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    /**
     * @Route("/emailingSendMail_ajax", name="emailingSendMail_ajax")
     */
    public function emailingSendMailAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $objet = $request->request->get('objet');
            $message = $request->request->get('message');
            $users = $request->request->get('users');

            for($i=0; $i<count($users); $i++){
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('id' => $users[$i]));
                $message = \Swift_Message::newInstance()
                    ->setSubject($objet)
                    ->setFrom('contact.afadec@gmail.com')
                    ->setCC($user->getEmail())
                    ->setBody(
                        $message,
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($message);
            }
            $em->flush();

            return new JsonResponse(array('action' =>'Send mail'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
