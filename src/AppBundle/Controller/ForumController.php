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

class ForumController extends Controller
{

    /**
     * @Route("/forum/{id}", name="forum")
     */
    public function ForumAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->findOneBy(array('id' => $id));

        $sujets = array();
        $sujetsEntity = $this->getDoctrine()->getRepository('AppBundle:ForumSujet')->findBy(array('forum' => $forum));

        for($i=0; $i<count($sujetsEntity); $i++){
            $sujets[$i]["sujet"] = $sujetsEntity[$i];
            $sujets[$i]["posts"] = $this->getDoctrine()->getRepository('AppBundle:ForumPost')->findBy(array('sujet' => $sujetsEntity[$i]));
        }

        return $this->render('forum/forum.html.twig', [
            'forum' => $forum,
            'sujets' => $sujets
        ]);
    }

    /**
     * @Route("forum/sujet/{id}", name="forumSujet")
     */
    public function SujetAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $sujet = $this->getDoctrine()->getRepository('AppBundle:ForumSujet')->findOneBy(array('id' => $id));

        $posts = $this->getDoctrine()->getRepository('AppBundle:ForumPost')->findBy(array('sujet' => $sujet), array('createdAt' => 'ASC'));

        return $this->render('forum/sujet.html.twig', [
            'sujet' => $sujet,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/forum/{id}/newSujet", name="newForumSujet")
     */
    public function newSujetAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $forum = $this->getDoctrine()->getRepository('AppBundle:Forum')->findOneBy(array('id' => $id));

        return $this->render('forum/addSujet.html.twig', [
            'forum' => $forum
        ]);
    }

    /**
     * @Route("/forum/sujet/{id}/newPost", name="newForumPost")
     */
    public function newPostAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $sujet = $this->getDoctrine()->getRepository('AppBundle:ForumSujet')->findOneBy(array('id' => $id));

        return $this->render('forum/addPost.html.twig', [
            'forum' => $sujet->getForum(),
            'sujet' => $sujet
        ]);
    }

    /**
     * @Route("/forum/post/{id}/editPost", name="editForumPost")
     */
    public function editPostAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $post = $this->getDoctrine()->getRepository('AppBundle:ForumPost')->findOneBy(array('id' => $id));

        return $this->render('forum/editPost.html.twig', [
            'forum' => $post->getSujet()->getForum(),
            'sujet' => $post->getSujet(),
            'post' => $post
        ]);
    }

    /**
     * @Route("/forumNewSujet_ajax", name="forumNewSujet_ajax")
     * @Method({"GET", "POST"})
     */
    public function forumNewSujetAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $titre = $request->request->get('titre');
            $texte = $request->request->get('texte');

            $forum = $em->getRepository('AppBundle:Forum')->findOneBy(array('id' => $id));

            $sujet = new ForumSujet();
            $sujet->setCreateur($this->getUser());
            $sujet->setTitre($titre);
            $sujet->setForum($forum);

            $em->persist($sujet);

            $post = new ForumPost();
            $post->setAuteur($this->getUser());
            $post->setTexte($texte);
            $post->setSujet($sujet);

            $em->persist($post);

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'create forum sujet',
                    'sujet' => $sujet->getId())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/forumNewPost_ajax", name="forumNewPost_ajax")
     * @Method({"GET", "POST"})
     */
    public function forumNewPostAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $texte = $request->request->get('texte');

            $sujet = $em->getRepository('AppBundle:ForumSujet')->findOneBy(array('id' => $id));

            $post = new ForumPost();
            $post->setAuteur($this->getUser());
            $post->setTexte($texte);
            $post->setSujet($sujet);

            $em->persist($post);

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'create forum post',
                    'post' => $post->getId())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/forumEditPost_ajax", name="forumEditPost_ajax")
     * @Method({"GET", "POST"})
     */
    public function forumEditPostAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $texte = $request->request->get('texte');

            $post = $em->getRepository('AppBundle:ForumPost')->findOneBy(array('id' => $id));

            $post->setTexte($texte);

            $em->persist($post);

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'create forum post',
                    'post' => $post->getId())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

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
     * @Route("/deleteSujet_ajax", name="deleteSujet_ajax")
     */
    public function deleteSujetAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $sujetId = $request->request->get('id');

            $sujet = $em->getRepository('AppBundle:ForumSujet')->findOneBy(array('id' => $sujetId));

            $em->remove($sujet);
            $em->flush();

            return new JsonResponse(array('action' =>'deleteSujet', 'id' => $sujetId));
        }
        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deletePost_ajax", name="deletePost_ajax")
     */
    public function deletePostAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $postId = $request->request->get('id');

            $post = $em->getRepository('AppBundle:ForumPost')->findOneBy(array('id' => $postId));

            $em->remove($post);
            $em->flush();

            return new JsonResponse(array('action' =>'deletePost', 'id' => $post));
        }
        return new JsonResponse('This is not ajax!', 400);
    }
}
