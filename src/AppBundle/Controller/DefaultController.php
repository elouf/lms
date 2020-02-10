<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GroupeResa;
use AppBundle\Entity\SystemeResa;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine();

        $myEvents = null;
        $myDiscs = null;
        if ($this->getUser()) {
            $myEvents = $this->get('calendarServ')->getMyCalendarDatas($this->getUser())['events'];
            $myDiscs = $this->get('calendarServ')->getMyCalendarDatas($this->getUser())['myDiscs'];
        }

        return $this->render('index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            'events' => $myEvents,
            'myDiscs' => $myDiscs,
            'total' => false
        ]);
    }

    /**
     * @Route("/faq", name="faq")
     */
    public function faqAction(Request $request)
    {
        return $this->render('pagesFixes/faq.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/concours", name="concours")
     */
    public function concoursAction(Request $request)
    {
        return $this->render('pagesFixes/concours.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/cookies", name="cookies")
     */
    public function cookiesAction(Request $request)
    {
        return $this->render('pagesFixes/cookies.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/metierEnseignant", name="metierEnseignant")
     */
    public function metierEnseignantAction(Request $request)
    {
        return $this->render('pagesFixes/metierEnseignant.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/afadec", name="afadec")
     */
    public function afadecAction(Request $request)
    {
        return $this->render('pagesFixes/afadec.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }

    /**
     * @Route("/deleteCompte", name="pageSuppressionCompte")
     */
    public function pageSuppressionCompteAction(Request $request)
    {
        return $this->render('pagesFixes/pageSuppressionCompte.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
        ]);
    }


    /**
     * @Route("/inscrGroupeResa_ajax", name="inscrGroupeResa_ajax")
     */
    public function inscrGroupeResaAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idGroup = $request->request->get('idGroupe');

            /* @var $user User */
            $user = $this->getUser();

            /* @var $group GroupeResa */
            $group = $this->getDoctrine()->getRepository('AppBundle:GroupeResa')
                ->findOneBy(array('id' => $idGroup));
            /* @var $system SystemeResa */
            $system = $group->getSystem();


            /* @var $oneGroup GroupeResa */
            foreach ($system->getGroups() as $oneGroup) {
                $oneGroup->removeUser($user);
            }
            $group->addUser($user);
            $em->flush();

            $arraySystem = [];
            $userGroupId = 0;
            foreach ($system->getGroups() as $oneGroup) {
                $places = $oneGroup->getMax() - $oneGroup->getUsers()->count();
                array_push($arraySystem, array('groupId' => $oneGroup->getId(), 'places' => $places));
                if($oneGroup->getUsers()->contains($this->getUser())){
                    $userGroupId = $oneGroup->getId();
                }
            }

            return new JsonResponse(array(
                'action' => 'get',
                'etat' => $arraySystem,
                'userGroupId' => $userGroupId
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/desinscrGroupeResa_ajax", name="desinscrGroupeResa_ajax")
     */
    public function desinscrGroupeResaAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idGroup = $request->request->get('idGroupe');

            /* @var $user User */
            $user = $this->getUser();

            /* @var $group GroupeResa */
            $group = $this->getDoctrine()->getRepository('AppBundle:GroupeResa')
                ->findOneBy(array('id' => $idGroup));
            $group->removeUser($user);

            $em->flush();

            $arraySystem = [];
            $userGroupId = 0;
            foreach ($group->getSystem()->getGroups() as $oneGroup) {
                $places = $oneGroup->getMax() - $oneGroup->getUsers()->count();
                array_push($arraySystem, array('groupId' => $oneGroup->getId(), 'places' => $places));
                if($oneGroup->getUsers()->contains($this->getUser())){
                    $userGroupId = $oneGroup->getId();
                }
            }

            return new JsonResponse(array(
                'action' => 'get',
                'etat' => $arraySystem,
                'userGroupId' => $userGroupId
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getGroupeResasNumbers_ajax", name="getGroupeResasNumbers_ajax")
     */
    public function getGroupeResasNumbersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $idSystem = $request->request->get('idSystem');

            /* @var $system SystemeResa */
            $system = $this->getDoctrine()->getRepository('AppBundle:SystemeResa')
                ->findOneBy(array('id' => $idSystem));

            $arraySystem = [];
            $userGroupId = 0;
            /* @var $oneGroup GroupeResa */
            foreach ($system->getGroups() as $oneGroup) {
                $places = $oneGroup->getMax() - $oneGroup->getUsers()->count();
                array_push($arraySystem, array('groupId' => $oneGroup->getId(), 'places' => $places));
                if($oneGroup->getUsers()->contains($this->getUser())){
                    $userGroupId = $oneGroup->getId();
                }
            }

            return new JsonResponse(array(
                'action' => 'get',
                'etat' => $arraySystem,
                'userGroupId' => $userGroupId
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
