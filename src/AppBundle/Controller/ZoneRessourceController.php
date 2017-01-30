<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ZoneRessource;
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
     * @Route("/addZone_ajax", name="addZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function addZoneAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $idItem = $request->request->get('idItem');
            $idSection = $request->request->get('idSection');
            $typeItem = $request->request->get('typeItem');

            $zone = new ZoneRessource();
            $zone->setDescription("");
            $zone->setIsVisible(0);

            $entityRessourceName = "";
            if($typeItem == "groupe"){
                $entityRessourceName = "GroupeLiens";
            }elseif($typeItem == "devoir"){
                $entityRessourceName = "Devoir";
            }elseif($typeItem == "lien"){
                $entityRessourceName = "Lien";
            }

            if($entityRessourceName == ""){
                return new JsonResponse(array(
                    'error' => true,
                    'entityRessourceName' => "non reconnu")
                );
            }else{
                $ressource = $em->getRepository('AppBundle:'.$entityRessourceName)->findOneBy(array('id' => $idItem));
                $zone->setRessource($ressource);

                $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $idSection));
                $zone->setSection($section);

                //on cherche la position
                $sectionZones = $em->getRepository('AppBundle:ZoneRessource')->findBy(array('section' => $section));
                $posMax = 0;
                for($i=0; $i<count($sectionZones); $i++){
                    if($sectionZones[$i]->getPosition() > $posMax){
                        $posMax = $sectionZones[$i]->getPosition();
                    }
                }
                $zone->setPosition($posMax+1);

                $em->persist($zone);
                $em->flush();
                return new JsonResponse(array(
                        'action' =>'add Zone',
                        'id' => $zone->getId(),
                        'coursId' => $zone->getSection()->getCours()->getId(),
                        'section nom' => $zone->getSection()->getNom())
                );
            }

        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/sortZone_ajax", name="sortZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function sortZoneAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $arrayZonesId = $request->request->get('arrayZones');

            for($i=0; $i<count($arrayZonesId); $i++){
                $zone = $em->getRepository('AppBundle:ZoneRessource')->findOneBy(array('id' => $arrayZonesId[$i]));
                $zone->setPosition($i);
            }

            $em->flush();
            return new JsonResponse(array(
                    'action' =>'sort Zones')
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
