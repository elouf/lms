<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class LienController extends Controller
{
    /**
     * @Route("/changeContentLien_ajax", name="changeContentLien_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentLienAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $url = $request->request->get('url');
            $description = $request->request->get('description');

            $lien = $em->getRepository('AppBundle:Lien')->findOneBy(array('id' => $id));
            $lien->setNom($nom);
            $lien->setUrl($url);
            $lien->setDescription($description);

            $em->persist($lien);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Section Name',
                'id' => $lien->getId(),
                'nom' => $lien->getNom())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
