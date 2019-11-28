<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Chat;
use AppBundle\Entity\Devoir;
use AppBundle\Entity\Forum;
use AppBundle\Entity\GroupeLiens;
use AppBundle\Entity\Lien;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Podcast;
use AppBundle\Entity\RessourceH5P;
use AppBundle\Entity\RessourceLibre;
use AppBundle\Entity\ZoneRessource;
use DOMDocument;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class ZoneRessourceController extends Controller
{
    /**
     * @Route("/activateZone_ajax", name="activateZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function activateZoneAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $zone = $em->getRepository('AppBundle:ZoneRessource')->findOneBy(array('id' => $id));
            $zone->setIsVisible($isVisible == "false");

            $em->persist($zone);
            $em->flush();

            return new JsonResponse(array('action' => 'change Zone Visibility', 'id' => $zone->getId(), 'isVisible' => $zone->getIsVisible()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/deleteZone_ajax", name="deleteZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function deleteZoneAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $id = $request->request->get('id');
            $zone = $em->getRepository('AppBundle:ZoneRessource')->findOneBy(array('id' => $id));
            $em->remove($zone);
            $em->flush();
            return new JsonResponse(array('action' => 'delete Zone', 'id' => $zone->getId()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addZone_ajax", name="addZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function addZoneAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $idItem = $request->request->get('idItem');
            $idSection = $request->request->get('idSection');
            $typeItem = $request->request->get('typeItem');

            $zone = new ZoneRessource();
            $zone->setDescription("");
            $zone->setIsVisible(0);

            $entityRessourceName = "";
            if ($typeItem == "groupe") {
                $entityRessourceName = "GroupeLiens";
            } elseif ($typeItem == "devoir") {
                $entityRessourceName = "Devoir";
            } elseif ($typeItem == "lien") {
                $entityRessourceName = "Lien";
            } elseif ($typeItem == "libre") {
                $entityRessourceName = "RessourceLibre";
            } elseif ($typeItem == "forum") {
                $entityRessourceName = "Forum";
            } elseif ($typeItem == "chat") {
                $entityRessourceName = "Chat";
            } elseif ($typeItem == "h5p") {
                $entityRessourceName = "RessourceH5P";
            } elseif ($typeItem == "podcast") {
                $entityRessourceName = "Podcast";
            }

            if ($entityRessourceName == "") {
                return new JsonResponse(array(
                        'error' => true,
                        'entityRessourceName' => "non reconnu")
                );
            } else {
                $ressource = null;
                $sortie = "";
                if ($idItem != 0) {
                    // c'est une ressource existante
                    $ressource = $em->getRepository('AppBundle:' . $entityRessourceName)->findOneBy(array('id' => $idItem));
                } else {
                    // création d'une nouvelle ressource
                    $idCours = $request->request->get('idCours');
                    /* @var $cours Cours */
                    $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $idCours));
                    if ($typeItem == "groupe") {
                        $ressource = new GroupeLiens();
                    } elseif ($typeItem == "devoir") {
                        $ressource = new Devoir();
                        $ressource->setDateDebut(new \DateTime("now"));
                        $ressource->setDateFin(new \DateTime("now"));
                        $ressource->setDuree(0);
                    } elseif ($typeItem == "lien") {
                        $ressource = new Lien();
                        $ressource->setUrl("");
                    } elseif ($typeItem == "libre") {
                        $ressource = new RessourceLibre();
                    } elseif ($typeItem == "forum") {
                        $ressource = new Forum();
                    } elseif ($typeItem == "chat") {
                        $ressource = new Chat();
                    } elseif ($typeItem == "h5p") {
                        $ressource = new RessourceH5P();
                    } elseif ($typeItem == "podcast") {
                        $ressource = new Podcast();
                    }
                    $ressource->setDescription("");
                    $ressource->setNom("");
                    $ressource->setCours($cours);
                    $em->persist($ressource);
                    $em->flush();

                    if ($typeItem == "podcast") {
                        $folderOK = true;
                        $folderUpload = $this->getParameter('upload_directory');
                        $uploadSteps = $this->getParameter('upload_steps');
                        $url = $uploadSteps.$folderUpload.$idCours.'/podcasts/'.$ressource->getId();

                        if (!is_dir($url)) {
                            if (!mkdir($url, 0777, true)) {
                                $folderOK = false;
                                $sortie = 'error : Echec de la création du dossier. url : '.$url;
                            } else {
                                $sortie = 'Dossier créé. url : '.$url;
                            }
                        } else {
                            $sortie = 'Dossier existe déjà. url : '.$url;
                        }
                        if($folderOK){
                            $manager = $this->get('assets.packages');

                            $xml = new DOMDocument();
                            $xml_rss = $xml->createElement("rss");

                            $xml_rss->setAttribute("version", "2.0");
                            $xml_rss->setAttribute("xmlns:googleplay", "http://www.google.com/schemas/play-podcasts/1.0");
                            $xml_rss->setAttribute("xmlns:itunes", "http://www.itunes.com/dtds/podcast-1.0.dtd");
                            $xml_rss->setAttribute("xmlns:atom", "http://www.w3.org/2005/Atom");
                            $xml_rss->setAttribute("xmlns:media", "http://search.yahoo.com/mrss/");
                            $xml_rss->setAttribute("xmlns:rtl", "http://www.rtl.fr/rss");
                            $xml_rss->setAttribute("xmlns:dcterms", "http://purl.org/dc/terms/");

                            $xml_channel = $xml->createElement("channel");

                            $xml_channel_title = $xml->createElement("title");
                            $xml_channel_gpAuthor = $xml->createElement("googleplay:author");
                            $xml_channel_gpImage = $xml->createElement("googleplay:image");
                            $xml_channel_gpImage->setAttribute("href", $this->get('kernel')->getRootDir().'/'.$url.'/'.$cours->getDiscipline()->getPodcastImgFilename());
                            $xml_channel_gpAuthor->nodeValue = 'AFADEC';
                            $xml_channel_descr = $xml->createElement("description");
                            $xml_channel_img = $xml->createElement("image");
                            $xml_channel_gpCat = $xml->createElement("googleplay:category");
                            $xml_channel_gpCat->setAttribute('text', 'Education');
                            $xml_channel_ituneCat = $xml->createElement("googleplay:category");
                            $xml_channel_ituneCat->setAttribute('text', 'Education');
                            $xml_channel_lang = $xml->createElement("language");
                            $xml_channel_lang->nodeValue = 'fr';
                            $xml_channel_link = $xml->createElement("link");
                            $xml_channel_link->nodeValue = $url."/podcast.rss";

                            $xml_channel_img_link = $xml->createElement("link");
                            $xml_channel_img_link->nodeValue = 'http://www.afadec.fr';
                            $xml_channel_img_title = $xml->createElement("title");
                            $xml_channel_img_title->nodeValue = 'AFADEC logo';
                            $xml_channel_img_url = $xml->createElement("url");

                            $xml_channel_img->appendChild( $xml_channel_img_link);
                            $xml_channel_img->appendChild( $xml_channel_img_title);
                            $xml_channel_img->appendChild( $xml_channel_img_url);
                            $xml_channel->appendChild( $xml_channel_img);
                            $xml_channel->appendChild( $xml_channel_title);
                            $xml_channel->appendChild( $xml_channel_gpImage);
                            $xml_channel->appendChild( $xml_channel_gpAuthor);
                            $xml_channel->appendChild( $xml_channel_gpCat);
                            $xml_channel->appendChild( $xml_channel_ituneCat);
                            $xml_channel->appendChild( $xml_channel_descr);
                            $xml_channel->appendChild( $xml_channel_lang);
                            $xml_rss->appendChild( $xml_channel);
                            $xml->appendChild( $xml_rss );

                            copy($this->get('kernel')->getRootDir().'/../..'.$manager->getUrl('images/podcast/'.$cours->getDiscipline()->getPodcastImgFilename()), $url.'/'.$cours->getDiscipline()->getPodcastImgFilename());

                            $xml->save($url."/podcast.rss");
                            $ressource->setRss($url."/podcast.rss");
                        }

                    }

                }

                $zone->setRessource($ressource);

                $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $idSection));
                $zone->setSection($section);

                //on cherche la position
                $sectionZones = $em->getRepository('AppBundle:ZoneRessource')->findBy(array('section' => $section));
                for ($i = 0; $i < count($sectionZones); $i++) {
                    $sectionZones[$i]->setPosition($sectionZones[$i]->getPosition() + 1);
                }
                $zone->setPosition(0);

                $em->persist($zone);
                $em->flush();
                return new JsonResponse(array(
                        'action' => 'add Zone',
                        'id' => $zone->getId(),
                        'ressource' => $ressource,
                        'coursId' => $zone->getSection()->getCours()->getId(),
                        'section nom' => $zone->getSection()->getNom(),
                        'sortie' => $sortie
                    )
                );

            }

        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/sortZone_ajax", name="sortZone_ajax")
     * @Method({"GET", "POST"})
     */
    public function sortZoneAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $arrayZonesId = $request->request->get('arrayZones');

            $repoZoneRessource = $em->getRepository('AppBundle:ZoneRessource');
            for ($i = 0; $i < count($arrayZonesId); $i++) {
                $zone = $repoZoneRessource->findOneBy(array('id' => $arrayZonesId[$i]));
                $zone->setPosition($i);
            }

            $em->flush();
            return new JsonResponse(array(
                    'action' => 'sort Zones')
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
