<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Section;
use AppBundle\Entity\Cours;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SectionController extends Controller
{
    /**
     * @Route("/activateSection_ajax", name="activateSection_ajax")
     * @Method({"GET", "POST"})
     */
    public function activateSectionAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $id));
            $section->setIsVisible($isVisible == "false");

            $em->persist($section);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Section Visibility',
                'id' => $section->getId(),
                'isVisible' => $section->getIsVisible())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/changeNameSection_ajax", name="changeNameSection_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeNameSectionAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');

            $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $id));
            $section->setNom($nom);

            $em->persist($section);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Section Name',
                'id' => $section->getId(),
                'nom' => $section->getNom())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addSection_ajax", name="addSection_ajax")
     * @Method({"GET", "POST"})
     */
    public function addSectionAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $nom = $request->request->get('nom');
            $coursId = $request->request->get('coursId');

            $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $coursId));

            $section = new Section();
            $section->setIsVisible(false);
            $section->setNom($nom);
            $section->setCours($cours);
            $section->setPictoFilePath('fa-pencil');

            //on cherche la position
            $courseSections = $em->getRepository('AppBundle:Section')->findBy(array('cours' => $cours));
            $posMax = 0;
            for($i=0; $i<count($courseSections); $i++){
                if($courseSections[$i]->getPosition() > $posMax){
                    $posMax = $courseSections[$i]->getPosition();
                }
            }
            $section->setPosition($posMax+1);

            $em->persist($section);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'add Section',
                'id' => $section->getId(),
                'coursId' => $section->getCours()->getId(),
                'nom' => $section->getNom())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deleteSection_ajax", name="deleteSection_ajax")
     * @Method({"GET", "POST"})
     */
    public function deleteSectionAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $id = $request->request->get('id');
            $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $id));

            $zones = $em->getRepository('AppBundle:ZoneRessource')->findBy(array('section' => $section));
            if(count($zones) != 0){
                return new JsonResponse(array(
                    'error' => true,
                    'nbZone' => count($zones),
                    'id' => $section->getId())
                );
            }else{
                $em->remove($section);
                $em->flush();
                return new JsonResponse(array(
                    'action' =>'delete Section',
                    'id' => $section->getId())
                );
            }
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/sortSection_ajax", name="sortSection_ajax")
     * @Method({"GET", "POST"})
     */
    public function sortSectionAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $arraySectionsId = $request->request->get('arraySections');

            for($i=0; $i<count($arraySectionsId); $i++){
                $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $arraySectionsId[$i]));
                $section->setPosition($i);
            }

            $em->flush();
            return new JsonResponse(array(
                    'action' =>'sort Sections')
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
