<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class RessourceLibreController extends Controller
{
    /**
     * @Route("/changeContentLibre_ajax", name="changeContentLibre_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentLibreAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $description = $request->request->get('description');

            $lien = $em->getRepository('AppBundle:RessourceLibre')->findOneBy(array('id' => $id));
            $lien->setDescription($description);

            $em->persist($lien);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Ressource Libre content',
                'id' => $lien->getId(),
                'nom' => $lien->getDescription())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
