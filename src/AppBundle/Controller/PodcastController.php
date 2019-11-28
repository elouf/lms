<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CategorieLien;
use AppBundle\Entity\Mp3Podcast;
use AppBundle\Entity\Podcast;
use DOMDocument;
use DOMXPath;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use wapmorgan\Mp3Info\Mp3Info;

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

            /* @var $podcast Podcast */
            $podcast = $em->getRepository('AppBundle:Podcast')->findOneBy(array('id' => $id));
            $podcast->setNom($nom);
            $podcast->setDescription($description);

            $url = $this->getRssFile($podcast);

            $xml = new DOMDocument();
            $xml->load($url."/podcast.rss");
            $xpath = new DOMXPath($xml);
            $titleNode = $xpath->query("channel/title")[0];
            $titleNode->nodeValue = $nom;

            $descrNode = $xpath->query("channel/description")[0];
            $descrNode->nodeValue = $description;

            $xml->save($url."/podcast.rss");

            $em->flush();
            return new JsonResponse(array(
                'action' =>'change Podcast Infos',
                'podcast' => $podcast->getId()
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/changeContentMp3PodcastRssFile_ajax", name="changeContentMp3PodcastRssFile_ajax")
     * @Method({"GET", "POST"})
     * @throws \Exception
     */
    public function changeContentMp3PodcastRssFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');

            /* @var $podcast Podcast */
            $podcast = $em->getRepository('AppBundle:Podcast')->findOneBy(array('id' => $id));

            $url = $this->getRssFile($podcast);

            $xml = new DOMDocument();
            $xml->load($url."/podcast.rss");

            // supprime tous les items (mp3)
            $listItems = $xml->getElementsByTagName("item");
            while ($listItems->length > 0) {
                $item = $listItems->item(0);
                $item->parentNode->removeChild($item);
            }

            $xpath = new DOMXPath($xml);
            $channelNode = $xpath->query("channel")[0];

            $mp3s = $em->getRepository('AppBundle:Mp3Podcast')->findBy(array('podcast' => $podcast, 'isVisible' => true), array('position' => 'ASC'));
            if($mp3s){
                /* @var $mp3 Mp3Podcast */
                foreach($mp3s as $mp3){
                    $urlFile = $mp3->getUrl();
                    $urlPieces = explode("/var/", $urlFile);
                    $filePath = '../var/'.$urlPieces[1];

                    $itemNode = $xml->createElement("item");
                    $itemTitleNode = $xml->createElement("title");
                    $itemTitleNode->nodeValue = $mp3->getNom();
                    $itemDescriptionNode = $xml->createElement("description");
                    $itemDescriptionNode->nodeValue = $mp3->getDescription();
                    $itemPubDateNode = $xml->createElement("pubDate");
                    $pubDateToday = new \DateTime();
                    $format = 'd/m/Y H:i:s';
                    $pubDate = $pubDateToday->format($format);
                    if($mp3->getUpdatedAt() !== null){
                        $pubDate = $mp3->getUpdatedAt()->format($format);
                    }
                    $itemPubDateNode->nodeValue = $pubDate;
                    $itemGuidNode = $xml->createElement("guid");
                    $itemGuidNode->nodeValue = "afadec".$mp3->getPosition();
                    $itemGuidNode->setAttribute('isPermaLink', 'false');
                    $itemDurationNode = $xml->createElement("itunes:duration");
                    $audio = new Mp3Info($filePath);
                    $itemDurationNode->nodeValue = floor($audio->duration / 60).':'.floor($audio->duration % 60);
                    $itemEnclosureNode = $xml->createElement("enclosure");
                    $itemEnclosureNode->setAttribute('url', $mp3->getUrl());
                    $itemEnclosureNode->setAttribute('type', 'audio/mpeg');
                    $itemEnclosureNode->setAttribute('length', $audio->audioSize);

                    $itemNode->appendChild($itemTitleNode);
                    $itemNode->appendChild($itemDescriptionNode);
                    $itemNode->appendChild($itemPubDateNode);
                    $itemNode->appendChild($itemGuidNode);
                    $itemNode->appendChild($itemDurationNode);
                    $itemNode->appendChild($itemEnclosureNode);

                    $channelNode->appendChild($itemNode);
                }
            }

            $xml->save($url."/podcast.rss");

            $em->flush();
            return new JsonResponse(array(
                'action' =>'change Podcast Infos',
                'podcast' => $podcast->getId()
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    public function getRssFile(Podcast $podcast){
        $folderUpload = $this->getParameter('upload_directory');
        $uploadSteps = $this->getParameter('upload_steps');
        $url = $uploadSteps.$folderUpload.$podcast->getCours()->getId().'/podcasts/'.$podcast->getId();
        return $url;
    }

    /**
     * @Route("/addMp3_ajax", name="addMp3_ajax")
     * @Method({"GET", "POST"})
     */
    public function addMp3AjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idMp3 = $request->request->get('idMp3');
            $nom = $request->request->get('nom');
            $url = $request->request->get('url');
            $description = $request->request->get('description');
            $idPodcast = $request->request->get('podcastId');

            /* @var $podcast Podcast */
            $podcast = $em->getRepository('AppBundle:Podcast')->findOneBy(array('id' => $idPodcast));

            $mp3 = null;

            if($idMp3 == '0'){
                $nbMp3 = $podcast->getMp3s()->count();

                $mp3 = new Mp3Podcast();
                $mp3->setPodcast($podcast);
                $mp3->setPosition($nbMp3);
                $em->persist($mp3);
            }else{
                /* @var $mp3 Mp3Podcast */
                $mp3 = $em->getRepository('AppBundle:Mp3Podcast')->findOneBy(array('id' => $idMp3));
            }
            $mp3->setNom($nom);
            $mp3->setUrl($url);
            $mp3->setDescription($description);

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
            /* @var $mp3 Mp3Podcast */
            $mp3 = $em->getRepository('AppBundle:Mp3Podcast')->findOneBy(array('id' => $mp3Id));

            $urlTab = eplode('/', $mp3->getUrl());
            $folderUpload = $this->getParameter('upload_directory');
            $uploadSteps = $this->getParameter('upload_steps');
            $url = $uploadSteps.$folderUpload.$mp3->getPodcast()->getCours()->getId().'/podcasts/'.$mp3->getPodcast()->getId().'/'.$urlTab[count($urlTab)-1];
            unlink($url);

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
            $date = new \DateTime();
            $newName = 'podcast_afadec_'.$date->format('YmdHis');
            rename($url, $urlDest.$newName.'.'.$ext);

            // lms/var...
            $newurl = $urlTab[0].'/var'.$urlDestTab[1].$newName.'.'.$ext;

            $webUrl1 = $request->getUriForPath('');
            $webUrl2 = str_replace('/app_dev.php', '', $webUrl1);
            $webUrl = str_replace('/web', '', $webUrl2);

            $url_withVar_tab = explode('var/', $newurl);

            $urlNew = $webUrl.'/var/'.$url_withVar_tab[1];

            $em->flush();
            return new JsonResponse(array('action' =>'upload Mp3Podcast', 'type' => $type, 'ext' => $ext, 'newLien' => $urlNew));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getMp3Podcast_ajax", name="getMp3Podcast_ajax")
     * @Method({"GET", "POST"})
     */
    public function getMp3PodcastAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idMp3 = $request->request->get('idMp3');
            /* @var $mp3 Mp3Podcast */
            $mp3 = $em->getRepository('AppBundle:Mp3Podcast')->findOneBy(array('id' => $idMp3));

            return new JsonResponse(array(
                    'action' =>'Get Mp3 from Podcast',
                    'nomMp3' => $mp3->getNom(),
                    'descrMp3' => $mp3->getDescription(),
                    'urlMp3' => $mp3->getUrl()
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/test_ajax", name="test_ajax")
     * @Method({"GET", "POST"})
     */
    public function testAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            return new JsonResponse(array(
                    'action' =>'test'
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
