<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Forum;
use AppBundle\Entity\Lien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ZipArchive;

class ForumController extends Controller
{
    /**
     * @Route("/changeContentForum_ajax", name="changeContentForum_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentForumAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');

            $forum = $em->getRepository('AppBundle:Forum')->findOneBy(array('id' => $id));
            $forum->setNom($nom);
            $forum->setDescription($description);

            $em->persist($forum);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Forum content',
                'forum' => $forum)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addLien_ajax", name="addLien_ajax")
     * @Method({"GET", "POST"})
     */
    public function addLienAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $idCours = $request->request->get('idCours');

            $forum = new Forum();
            $forum->setNom($nom);
            $forum->setDescription($description);
            $forum->setCours($em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $idCours)));

            $em->persist($forum);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Add Forum',
                    'lien' => $forum,
                    'id' => $forum->getId())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
