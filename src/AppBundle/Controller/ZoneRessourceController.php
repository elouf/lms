<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ZoneRessourceController extends Controller
{
    /**
     * @Route("/activateZone_ajax", name="activateZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function activateZoneAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $zone = $em->getRepository('AppBundle:ZoneRessource')->findOneBy(array('id' => $id));
            $zone->setIsVisible($isVisible == "false");

            $em->persist($zone);

            $em->flush();

            return new JsonResponse(array('action' =>'change Zone Visibility', 'id' => $zone->getId(), 'isVisible' => $zone->getIsVisible()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deleteZone_ajax", name="deleteZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function deleteZoneAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');

            $zone = $em->getRepository('AppBundle:ZoneRessource')->findOneBy(array('id' => $id));

            $em->remove($zone);

            $em->flush();

            return new JsonResponse(array('action' =>'delete Zone', 'id' => $zone->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/reorderZones_ajax", name="reorderZones_ajax")
     * @Method({"GET", "POST"})
     */
    public function reorderZonesAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            $em = $this->getDoctrine()->getEntityManager();


            return new JsonResponse(array('action' =>'reorder Zones'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
