<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Lien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ZipArchive;

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
            $typeLienId = $request->request->get('typeLien');

            $lien = $em->getRepository('AppBundle:Lien')->findOneBy(array('id' => $id));
            $lien->setNom($nom);
            $lien->setUrl($url);
            $lien->setDescription($description);
            if($typeLienId == 0){
                $lien->setTypeLien(null);
            }else{
                $lien->setTypeLien($em->getRepository('AppBundle:TypeLien')->findOneBy(array('id' => $typeLienId)));
            }

            $em->persist($lien);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Lien content',
                'lien' => $lien)
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
            $url = $request->request->get('url');
            $description = $request->request->get('description');
            $typeLienId = $request->request->get('typeLien');
            $idCours = $request->request->get('idCours');

            $lien = new Lien();
            $lien->setNom($nom);
            $lien->setUrl($url);
            $lien->setDescription($description);
            $lien->setTypeLien($em->getRepository('AppBundle:TypeLien')->findOneBy(array('id' => $typeLienId)));
            $lien->setCours($em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $idCours)));

            $em->persist($lien);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Add Lien',
                    'lien' => $lien,
                    'id' => $lien->getId())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }


    /**
     * @Route("/uploadLien_ajax", name="uploadLien_ajax")
     * @Method({"GET", "POST"})
     */
    public function uploadLienAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $itemId = $request->request->get('itemId');
            $type = $request->request->get('type');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');
            $unzipIfZip = $request->request->get('unzipIfZip') == 'true';

            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('/var', $urlDest);

            $lien = $em->getRepository('AppBundle:Lien')->findOneBy(array('id' => $itemId));

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            rename($url, $urlDest.'file.'.$ext);

            if($unzipIfZip && $type == 'application/zip'){
                $zip = new ZipArchive;
                $res = $zip->open($urlDest.'file.'.$ext);
                if ($res === TRUE) {
                    $zip->extractTo($urlDest);
                    $zip->close();
                    $lien->setUrl($urlTab[0].'/var'.$urlDestTab[1].'index.html');
                    unlink($urlDest.'file.'.$ext);
                } else {
                    return new JsonResponse(array(
                            'error' => true,
                            'unzipping' => "absence du fichier zip")
                    );
                }
            }else{
                $lien->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file.'.$ext);
            }

            $em->flush();
            return new JsonResponse(array('action' =>'upload File', 'id' => $itemId, 'ext' => $ext, 'newLien' => $lien->getUrl()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
