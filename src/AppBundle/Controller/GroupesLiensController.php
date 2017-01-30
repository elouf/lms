<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GroupeLiens;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GroupesLiensController extends Controller
{
    /**
     * @Route("/changeContentGroupe_ajax", name="changeContentGroupe_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentGroupeAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');

            $groupe = $em->getRepository('AppBundle:GroupeLiens')->findOneBy(array('id' => $id));
            $groupe->setNom($nom);
            $groupe->setDescription($description);

            $em->persist($groupe);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Section Name',
                'id' => $groupe->getId(),
                'nom' => $groupe->getNom())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
