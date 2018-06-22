<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmailingController extends Controller
{

    /**
     * @Route("/emailing", name="emailing")
     */
    public function emailingAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findBy(array(), array('lastname' => 'ASC'));
        $sessions = $this->getDoctrine()->getRepository('AppBundle:Session')->findBy(array(), array('nom' => 'ASC'));
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findBy(array(), array('nom' => 'ASC'));
        $courses = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array(), array('nom' => 'ASC'));

        $coursesArray = array();
        for($i=0; $i<count($courses); $i++){
            array_push($coursesArray, [$courses[$i], $courses[$i]->getDiscipline()]);
        }
        return $this->render('emailing.html.twig', ['users' => $users, 'sessions' => $sessions, 'cohortes' => $cohortes, 'courses' => $coursesArray]);
    }

    /**
     * @Route("/applyFiltersEmailing_ajax", name="applyFiltersEmailing_ajax")
     */
    public function applyFiltersEmailingAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine();
            $coursSubscribed = $request->request->get('coursSubscribed');
            $sessionsSubscribed = $request->request->get('sessionsSubscribed');
            $sessionsUnSubscribed = $request->request->get('sessionsUnSubscribed');
            $cohSubscribed = $request->request->get('cohSubscribed');
            $cohUnSubscribed = $request->request->get('cohUnSubscribed');

            $users = array();
            $notUsers = array();

            for($i=0; $i<count($coursSubscribed); $i++){
                $inscritIds = $em->getRepository('AppBundle:Cours')->findInscrits($coursSubscribed[$i]);
                if($inscritIds){
                    foreach($inscritIds as $inscr){
                        array_push($users, $inscr->getId());
                    }
                }
            }

            for($i=0; $i<count($sessionsSubscribed); $i++){
                $session = $this->getDoctrine()->getRepository('AppBundle:Session')->findOneBy(array('id' => $sessionsSubscribed[$i]));
                if($session){
                    $inscrs = $this->getDoctrine()->getRepository('AppBundle:Inscription_sess')->findBy(array('session' => $session));
                    if($inscrs){
                        foreach($inscrs as $inscr){
                            array_push($users, $inscr->getUser()->getId());
                        }
                    }
                }
            }
            for($i=0; $i<count($sessionsUnSubscribed); $i++){
                $session = $this->getDoctrine()->getRepository('AppBundle:Session')->findOneBy(array('id' => $sessionsUnSubscribed[$i]));
                if($session){
                    $inscrs = $this->getDoctrine()->getRepository('AppBundle:Inscription_sess')->findBy(array('session' => $session));
                    if($inscrs){
                        foreach($inscrs as $inscr){
                            array_push($notUsers, $inscr->getUser()->getId());
                        }
                    }
                }
            }

            for($i=0; $i<count($cohSubscribed); $i++){
                $cohorte = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $cohSubscribed[$i]));
                if($cohorte){
                    $inscrs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                    if($inscrs){
                        foreach($inscrs as $inscr){
                            array_push($users, $inscr->getUser()->getId());
                        }
                    }
                }
            }

            for($i=0; $i<count($cohUnSubscribed); $i++){
                $cohorte = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $cohUnSubscribed[$i]));
                if($cohorte){
                    $inscrs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                    if($inscrs){
                        foreach($inscrs as $inscr){
                            array_push($notUsers, $inscr->getUser()->getId());
                        }
                    }
                }
            }

            return new JsonResponse(array('action' =>'Send mail', 'users' => $users, 'notUsers' => $notUsers));
        }

        return new JsonResponse('This is not ajax!', 400);
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
            $headers = null;
            for($i=0; $i<count($users); $i++){
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('id' => $users[$i]));
                $emailContent = \Swift_Message::newInstance()
                    ->setSubject($objet)
                    ->setFrom('erwannig@studit.fr')
                    ->setReplyTo('noreply.afadec@gmail.fr')
                    ->setCC($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'user/email.html.twig',
                            array(
                                'prenom' => $this->getUser()->getFirstname(),
                                'nom' => $this->getUser()->getLastname(),
                                'message' => $message
                            )
                        ),
                        'text/html'
                    )
                    ->addPart(
                        $this->renderView(
                            'user/email.html.twig',
                            array(
                                'prenom' => $this->getUser()->getFirstname(),
                                'nom' => $this->getUser()->getLastname(),
                                'message' => $message
                            )
                        ),
                        'text/plain'
                    )
                ;
                $headers = $emailContent->getHeaders();
                $this->get('mailer')->send($emailContent);
            }
            $em->flush();

            return new JsonResponse(array(
                'action' =>'Send mail',
                'headers' => $headers));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
    /*
     *
     * public function emailingSendMailAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $objet = $request->request->get('objet');
            $message = $request->request->get('message');
            $users = $request->request->get('users');

            $mailSendLimit = $this->getParameter('grouped_mails_limit');
            $nbArray = intval(ceil(count($users)/$mailSendLimit));

            $users_chunk = array_chunk($users, $nbArray);

            for($i=0; $i<count($users_chunk); $i++){
                for($j=0; $j<count($users_chunk[$i]); $j++) {
                }
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('id' => $users_chunk[$i][$j]));
                $emailContent = \Swift_Message::newInstance()
                    ->setSubject($objet.$i)
                    ->setFrom('contact.afadec@gmail.com')
                    ->setCC($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'user/email.html.twig',
                            array(
                                'prenom' => $this->getUser()->getFirstname(),
                                'nom' => $this->getUser()->getLastname(),
                                'message' => $message
                            )
                        ),
                        'text/html'
                    )
                ;
                $this->get('mailer')->send($emailContent);

            }
            $em->flush();

            return new JsonResponse(array('action' =>'Send mail'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
     */

    /**
     * @Route("/emailingSendAdminMail_ajax", name="emailingSendAdminMail_ajax")
     */
    public function emailingSendAdminMailAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $objet = $request->request->get('sujet');
            $message = $request->request->get('message');
            $email = $request->request->get('email');
            $nom = $request->request->get('nom');

            $emailContent = \Swift_Message::newInstance()
                ->setSubject("Message Plateforme : ".$objet)
                ->setFrom($email)
                ->setCC('contact.afadec@gmail.com')
                ->setBody(
                    "De : ".$nom." (".$email.")<br>".
                    $message,
                    'text/html'
                )
            ;
            $this->get('mailer')->send($emailContent);

            $em->flush();

            return new JsonResponse(array('action' =>'Send mail'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/emailingGenerateCsv_ajax", name="emailingGenerateCsv_ajax")
     */
    public function emailingGenerateCsvAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $users = $request->request->get('users');

            $data = array(['id','email','firstname','lastname']);
            for($i=0; $i<count($users); $i++){
                $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('id' => $users[$i]));
                array_push($data, [$user->getId(), $user->getEmail(), $user->getFirstname(), $user->getLastname()]);
            }

            $em->flush();

            return new JsonResponse(array('action' =>'get csv users', 'data' => $data));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
