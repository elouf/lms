<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CategorieLien;
use AppBundle\Entity\Mp3Podcast;
use AppBundle\Entity\Podcast;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PodcastController extends Controller
{
    /**
     * @Route("/changeContentPodcast_ajax", name="changeContentPodcast_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentPodcastAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $rss = $request->request->get('rss');

            /* @var $podcast Podcast */
            $podcast = $em->getRepository('AppBundle:Podcast')->findOneBy(array('id' => $id));
            $podcast->setNom($nom);
            $podcast->setDescription($description);
            $podcast->setRss($rss);

            $em->persist($podcast);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Podcast Infos',
                'podcast' => $podcast)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addMp3_ajax", name="addMp3_ajax")
     * @Method({"GET", "POST"})
     */
    public function addMp3AjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $nom = $request->request->get('nom');
            $url = $request->request->get('url');
            $description = $request->request->get('description');
            $idPodcast = $request->request->get('podcastId');

            /* @var $podcast Podcast */
            $podcast = $em->getRepository('AppBundle:Podcast')->findOneBy(array('id' => $idPodcast));
            $nbMp3 = $podcast->getMp3s()->count();

            $mp3 = new Mp3Podcast();
            $mp3->setNom($nom);
            $mp3->setUrl($url);
            $mp3->setDescription($description);
            $mp3->setPodcast($podcast);
            $mp3->setPosition($nbMp3);

            $em->persist($mp3);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Add Mp3',
                    'mp3' => $mp3,
                    'id' => $mp3->getId())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/sortMp3InPodcast_ajax", name="sortMp3InPodcast_ajax")
     * @Method({"GET", "POST"})
     */
    public function sortMp3InPodcastAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $arrayAssocsId = $request->request->get('arrayAssocs');

            $repoMp3Podcast = $em->getRepository('AppBundle:Mp3Podcast');
            $resultat = "";

            for($i=0; $i<count($arrayAssocsId); $i++){
                /* @var $mp3 Mp3Podcast */
                $mp3 = $repoMp3Podcast->findOneBy(array('id' => $arrayAssocsId[$i]));
                $mp3->setPosition($i);
                $em->persist($mp3);
                $resultat .= '['.$mp3->getId().' : '.$mp3->getNom().' - position : '.$i.'] ';
            }

            $em->flush();
            return new JsonResponse(array(
                    'action' =>'sort Mp3s in Podcast',
                    'resultat' => $resultat
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/removeMp3Podcasts_ajax", name="removeMp3Podcasts_ajax")
     * @Method({"GET", "POST"})
     */
    public function removeMp3PodcastsAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $mp3Id = $request->request->get('mp3Id');
            $mp3 = $em->getRepository('AppBundle:Mp3Podcast')->findOneBy(array('id' => $mp3Id));

            $em->remove($mp3);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Remove Mp3 from Podcast'
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/toggleVisibilityMp3Podcast_ajax", name="toggleVisibilityMp3Podcast_ajax")
     * @Method({"GET", "POST"})
     */
    public function toggleVisibilityMp3PodcastAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $mp3Id = $request->request->get('mp3Id');
            /* @var $mp3 Mp3Podcast */
            $mp3 = $em->getRepository('AppBundle:Mp3Podcast')->findOneBy(array('id' => $mp3Id));

            $mp3->setIsVisible(!$mp3->getIsVisible());

            $em->flush();

            return new JsonResponse(array(
                    'action' => 'change visibility of podcast Mp3',
                    'isVisible' => $mp3->getIsVisible()
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/uploadMp3Podcast_ajax", name="uploadMp3Podcast_ajax")
     * @Method({"GET", "POST"})
     */
    public function uploadMp3PodcastAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $type = $request->request->get('type');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');

            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('var', $urlDest);

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            rename($url, $urlDest.'file.'.$ext);

            $newurl = $urlTab[0].'/var'.$urlDestTab[1].'file.'.$ext;

            $em->flush();
            return new JsonResponse(array('action' =>'upload Mp3Podcast', 'type' => $type, 'ext' => $ext, 'newLien' => $newurl));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
