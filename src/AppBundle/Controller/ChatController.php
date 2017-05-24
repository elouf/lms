<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Forum;
use AppBundle\Entity\ForumPost;
use AppBundle\Entity\ForumSujet;
use AppBundle\Entity\Lien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ZipArchive;

class ChatController extends Controller
{

    /**
     * @Route("/chat/{id}", name="chat")
     */
    public function ChatAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $chat = $this->getDoctrine()->getRepository('AppBundle:Chat')->findOneBy(array('id' => $id));

        return $this->render('chat/chat.html.twig', [
            'chat' => $chat
        ]);
    }

    /**
     * @Route("/changeContentChat_ajax", name="changeContentChat_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentChatAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');

            $chat = $em->getRepository('AppBundle:Chat')->findOneBy(array('id' => $id));
            $chat->setNom($nom);
            $chat->setDescription($description);

            $em->persist($chat);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Chat content',
                'chat' => $chat)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
