<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Devoir;
use AppBundle\Entity\Evt_cours;
use AppBundle\Entity\Evt_discipline;
use AppBundle\Entity\Evt_user;
use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class CalendarController extends Controller
{
    /**
     * @Route("/calendrier", name="calendrier")
     */
    public function myCalendarAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $myEvents = $this->get('calendarServ')->getMyCalendarDatas($this->getUser())['events'];
        $myDiscs = $this->get('calendarServ')->getMyCalendarDatas($this->getUser())['myDiscs'];

        return $this->render('calendrier/calendrier.html.twig', [
            'events' => $myEvents,
            'myDiscs' => $myDiscs,
            'total' => false
        ]);
    }

    /**
     * @Route("/createCalendarEvent_ajax", name="createCalendarEvent_ajax")
     * @Method({"GET", "POST"})
     */
    public function createCalendarEventAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $repositoryDisc = $this->getDoctrine()->getRepository('AppBundle:Discipline');

            $id = $request->request->get('id');
            $titre = $request->request->get('titre');
            $dateDebut = date_create_from_format('j/m/Y H:i', $request->request->get('dateD'));
            $dateFin = date_create_from_format('j/m/Y H:i', $request->request->get('dateF'));
            $discId = $request->request->get('discId');

            $evt = null;

            if($id === ""){
                // création d'event
                if($discId === ""){
                    // évènement utilisateur
                    $evt = new Evt_user();
                    $evt->setNom($titre);
                    $evt->setUSer($this->getUser());
                    $evt->setDateDebut($dateDebut);
                    $evt->setDateFin($dateFin);
                }else{
                    // évènement discipline
                    $disc = $repositoryDisc->findOneBy(array('id' => $discId));
                    if($disc){
                        $evt = new Evt_discipline();
                        $evt->setNom($titre);
                        $evt->setDiscipline($disc);
                        $evt->setDateDebut($dateDebut);
                        $evt->setDateFin($dateFin);
                    }
                }
                $em->persist($evt);
            }else{
                // edit d'event
                if($discId === ""){
                    // évènement utilisateur
                    $repositoryEvent = $this->getDoctrine()->getRepository('AppBundle:Evt_user');
                }else {
                    // évènement discipline
                    $repositoryEvent = $this->getDoctrine()->getRepository('AppBundle:Evt_discipline');
                }
                $evt = $repositoryEvent->findOneBy(array('id' => $id));
                $evt->setDateDebut($dateDebut);
                $evt->setDateFin($dateFin);
                $evt->setNom($titre);
            }

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'add Calendar event by ajax'
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deleteCalendarEvent_ajax", name="deleteCalendarEvent_ajax")
     * @Method({"GET", "POST"})
     */
    public function deleteCalendarEventAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $discId = $request->request->get('discId');

            if($discId === ""){
                // évènement utilisateur
                $repositoryEvent = $this->getDoctrine()->getRepository('AppBundle:Evt_user');
            }else {
                // évènement discipline
                $repositoryEvent = $this->getDoctrine()->getRepository('AppBundle:Evt_discipline');
            }
            $evt = $repositoryEvent->findOneBy(array('id' => $id));
            $em->remove($evt);

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'remove Calendar event by ajax'
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
