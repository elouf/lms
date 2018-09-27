<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssocUserChatSession;
use AppBundle\Entity\ChatPost;
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

        $assocs = $this->getDoctrine()->getRepository('AppBundle:AssocUserChatSession')->findBy(array('chat' => $chat));

        $posts = $this->getDoctrine()->getRepository('AppBundle:ChatPost')->findBy(array('chat' => $chat), array('createdAt' => 'ASC'));
        return $this->render('chat/chat.html.twig', [
            'chat' => $chat,
            'assocs' => $assocs,
            'posts' => $posts
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

    /**
     * @Route("/subsrcribeChat_ajax", name="subsrcribeChat_ajax")
     * @Method({"GET", "POST"})
     */
    public function subsrcribeChatAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $session = $request->request->get('session');
            $userId = $request->request->get('userId');
            $chatId = $request->request->get('chatId');

            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $chat = $em->getRepository('AppBundle:Chat')->findOneBy(array('id' => $chatId));

            $assoc = $em->getRepository('AppBundle:AssocUserChatSession')->findOneBy(array('user' => $user, 'chat' => $chat));
            $userReturn = $user->getFirstname().' '.$user->getLastname();
            $userId = $user->getId();
            if($assoc->getSession() == "temp"){
                $assoc->setSession($session);
            }else{
                $userReturn = "otherOne";
            }

            $em->persist($assoc);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'subscribe User to Chat',
                    'user' => $userReturn,
                    'userId' => $userId)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getChatUserBySessionession_ajax", name="getChatUserBySessionession_ajax")
     * @Method({"GET", "POST"})
     */
    public function getChatUserBySessionessionAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $session = $request->request->get('session');
            $chatId = $request->request->get('chatId');

            $chat = $em->getRepository('AppBundle:Chat')->findOneBy(array('id' => $chatId));

            $assoc = $em->getRepository('AppBundle:AssocUserChatSession')->findOneBy(array('session' => $session, 'chat' => $chat));
            $user = $assoc->getUser();

            $isAdmin = "false";
            if($user->hasRole('ROLE_SUPER_ADMIN')){
                $isAdmin = "true";
            }

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'get Chat User by session',
                    'user' => $user->getFirstname().' '.$user->getLastname(),
                    'userId' =>$user->getId(),
                    'isAdmin' => $isAdmin)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/cleanAssocUserChat_ajax", name="cleanAssocUserChat_ajax")
     * @Method({"GET", "POST"})
     */
    public function cleanAssocUserChatAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $userId = $request->request->get('userId');
            $chatId = $request->request->get('chatId');

            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $chat = $em->getRepository('AppBundle:Chat')->findOneBy(array('id' => $chatId));

            $assoc = $em->getRepository('AppBundle:AssocUserChatSession')->findOneBy(array('user' => $user, 'chat' => $chat));

            $newAssoc = new AssocUserChatSession();
            $newAssoc->setChat($chat);
            $newAssoc->setUser($user);
            $newAssoc->setSession("temp");
            $em->persist($newAssoc);

            if($assoc){
                $em->remove($assoc);
            }
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'clean association of User to Chat',
                    'user' => $user->getFirstname().' '.$user->getLastname())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/publishChatPost_ajax", name="publishChatPost_ajax")
     * @Method({"GET", "POST"})
     */
    public function publishChatPostAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $session = $request->request->get('session');
            $chatId = $request->request->get('chatId');
            $message = $request->request->get('message');
            $userId = $request->request->get('userId');

            $userConnected = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            $chat = $em->getRepository('AppBundle:Chat')->findOneBy(array('id' => $chatId));

            $assoc = $em->getRepository('AppBundle:AssocUserChatSession')->findOneBy(array('session' => $session, 'chat' => $chat));
            $user = $assoc->getUser();

            if($user->getId() != $userConnected->getId()){
                $newPost = new ChatPost();
                $newPost->setAuteur($user);
                $newPost->setChat($chat);
                $newPost->setTexte($message);
                $em->persist($newPost);
            }

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'publish Chat Post',
                    'user' => $user->getFirstname().' '.$user->getLastname())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/reloadWS_ajax", name="reloadWS_ajax")
     * @Method({"GET", "POST"})
     */
    public function reloadWSAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            //exec("php bin/console gos:websocket:server");
            return new JsonResponse(array(
                    'action' =>'reload Chat WS')
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
