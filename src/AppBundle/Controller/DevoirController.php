<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Devoir;
use AppBundle\Entity\DevoirCorrigeType;
use AppBundle\Entity\DevoirSujet;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ZipArchive;

class DevoirController extends Controller
{
    /**
     * @Route("/getDevoir_ajax", name="getDevoir_ajax")
     * @Method({"GET", "POST"})
     */
    public function getDevoirAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $id));

            return new JsonResponse(array(
                    'action' =>'change Devoir content',
                    'duree' => $devoir->getDuree(),
                    'dateDebut' => $devoir->getDateDebut(),
                    'dateFin' => $devoir->getDateFin(),
                    'nom' => $devoir->getNom(),
                    'description' => $devoir->getDescription()
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/changeContentDevoir_ajax", name="changeContentDevoir_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentDevoirAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $dureeH = $request->request->get('dureeH');
            $dureeM = $request->request->get('dureeM');
            $dateDebut = date_create_from_format('j/m/Y H:i', $request->request->get('dateDebut'));
            $dateFin = date_create_from_format('j/m/Y H:i', $request->request->get('dateFin'));

            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $id));
            $devoir->setNom($nom);
            $devoir->setDescription($description);
            $devoir->setDuree($dureeH * 3600 + $dureeM * 60);
            $devoir->setDateDebut($dateDebut);
            $devoir->setDateFin($dateFin);

            $em->persist($devoir);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Devoir content',
                'devoir' => $devoir
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/uploadDevoirFile_ajax", name="uploadDevoirFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function uploadDevoirFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $typeItem = $request->request->get('typeItem');
            $itemId = $request->request->get('itemId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');
            $nom = $request->request->get('nom');

            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('/var', $urlDest);

            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $itemId));

            if($typeItem == "sujets"){
                $devoirF = new DevoirSujet();
            }elseif($typeItem == "corrigeTypes"){
                $devoirF = new DevoirCorrigeType();
            }else{
                return new JsonResponse(array(
                        'error' => true,
                        'typeItem' => "non reconnu")
                );
            }

            $devoirF->setDevoir($devoir);
            $devoirF->setNom($nom);

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $rand = rand(1, 999999);
            rename($url, $urlDest.'file'.$rand.'.'.$ext);

            $devoirF->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file'.$rand.'.'.$ext);

            $em->persist($devoirF);
            $em->flush();
            return new JsonResponse(array('action' =>'upload File', 'id' => $itemId, 'ext' => $ext, 'nouveau '.$typeItem => $devoirF->getUrl()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/removeDevoirFile_ajax", name="removeDevoirFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function removeDevoirFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $typeItem = $request->request->get('typeItem');
            $itemId = $request->request->get('itemId');

            $entity = "";

            if($typeItem == "sujet"){
                $entity = "DevoirSujet";
            }elseif($typeItem == "corrigeType"){
                $entity = "DevoirCorrigeType";
            }else{
                return new JsonResponse(array(
                        'error' => true,
                        'typeItem' => "non reconnu")
                );
            }

            $devoirF = $em->getRepository('AppBundle:'.$entity)->findOneBy(array('id' => $itemId));

            $urlTab = explode('/var', $devoirF->getUrl());

            $em->remove($devoirF);
            $em->flush();

            unlink('../var'.$urlTab[1]);
            return new JsonResponse(array('action' =>'delete File', 'id' => $itemId, 'type' => $typeItem));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
