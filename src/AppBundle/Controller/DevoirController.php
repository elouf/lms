<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Copie;
use AppBundle\Entity\CopieFichier;
use AppBundle\Entity\Corrige;
use AppBundle\Entity\CorrigeFichier;
use AppBundle\Entity\Devoir;
use AppBundle\Entity\DevoirCorrigeType;
use AppBundle\Entity\DevoirSujet;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use ZipArchive;

class DevoirController extends Controller
{

    /**
     *
     * @Route("/devoirens/{id}", name="oneDevoirEns")
     */
    public function getDevoirsEnsAction (Request $request, $id)
    {
        $em = $this->getDoctrine();

        $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $id));
        $cours = $devoir->getCours();

        $datas = array();

        $copies = $this->getDoctrine()->getRepository('AppBundle:Copie')->findBy(array('devoir' => $devoir));
        for($u=0; $u<count($copies); $u++){
            $copieFichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier')->findOneBy(array('copie' => $copies[$u]));
            $datas[$u]["copie"] = $copies[$u];
            $datas[$u]["copieFichier"] = $copieFichier;
            $datas[$u]["corrigeFichier"] = "undefined";
            $datas[$u]["corrige"] = "undefined";
            if($copieFichier){
                $corrige = $this->getDoctrine()->getRepository('AppBundle:Corrige')->findOneBy(array('copie' => $copies[$u]));
                if($corrige){
                    $corrigeFichier = $this->getDoctrine()->getRepository('AppBundle:CorrigeFichier')->findOneBy(array('corrige' => $corrige));
                    $datas[$u]["corrigeFichier"] = $corrigeFichier;
                    $datas[$u]["corrige"] = $corrige;
                }
            }
        }

        $corrigesType = array();
        $corrigesTypeEntities = $this->getDoctrine()->getRepository('AppBundle:DevoirCorrigeType')->findBy(array('devoir' => $devoir));
        for($u=0; $u<count($corrigesTypeEntities); $u++){
            $corrigesType[$u] = $corrigesTypeEntities[$u];
        }

        $sujets = array();
        $sujetsEntities = $this->getDoctrine()->getRepository('AppBundle:DevoirSujet')->findBy(array('devoir' => $devoir));
        for($u=0; $u<count($sujetsEntities); $u++){
            $sujets[$u] = $sujetsEntities[$u];
        }
        return $this->render('ressources/oneDevoirEns.html.twig',
            [
                'cours' => $cours,
                'devoir' => $devoir,
                'devoirsEns' => $datas,
                'sujets' => $sujets,
                'corrigesType' => $corrigesType,
                'folderUpload' => $this->getParameter('upload_directory'),
                'uploadSteps' => $this->getParameter('upload_steps'),
                'uploadSrcSteps' => $this->getParameter('upload_srcSteps')
            ]);
    }

    /**
     * @Route("/getDevoir_ajax", name="getDevoir_ajax")
     * @Method({"GET", "POST"})
     */
    public function getDevoirAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine();

            $id = $request->request->get('id');
            $userId = $request->request->get('userId');
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $id));

            $repositorySujet = $this->getDoctrine()
                ->getRepository('AppBundle:DevoirSujet')
                ->findOneBy(array('devoir' => $devoir));

            $copieStart = null;
            $copieFichier = "";
            $corrige = "";
            $corrigeFichier = "";
            $copie = $em->getRepository('AppBundle:Copie')->findOneBy(array('devoir' => $devoir, 'auteur' => $user));
            if($copie){
                $copieStart = $copie->getDateCreation();

                $copieFichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier')->findOneBy(array('copie' => $copie));

                $corrige = $this->getDoctrine()->getRepository('AppBundle:Corrige')->findOneBy(array('copie' => $copie));
                if($corrige){
                    $corrigeFichier = $this->getDoctrine()->getRepository('AppBundle:CorrigeFichier')->findOneBy(array('corrige' => $corrige));
                }

            }

            return new JsonResponse(array(
                    'action' =>'change Devoir content',
                    'duree' => $devoir->getDuree(),
                    'dateDebut' => $devoir->getDateDebut(),
                    'dateFin' => $devoir->getDateFin(),
                    'nom' => $devoir->getNom(),
                    'description' => $devoir->getDescription(),
                    'sujet' => $repositorySujet->getUrl(),
                    'copieStart' => $copieStart,
                    'copieFichier' => $copieFichier,
                    'corrige' => $corrige,
                    'corrigeFichier' => $corrigeFichier
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/startDevoir_ajax", name="startDevoir_ajax")
     * @Method({"GET", "POST"})
     */
    public function startDevoirAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');
            $userId = $request->request->get('userId');
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $id));

            $copie = new Copie();
            $copie->setDateCreation(new DateTime());
            $copie->setAuteur($user);
            $copie->setDevoir($devoir);

            $em->persist($copie);
            $em->flush();


            return new JsonResponse(array(
                    'action' =>'start Devoir',
                    'copieId' => $copie->getId()
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/changeContentDevoir_ajax", name="changeContentDevoir_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentDevoirAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');
            $dureeH = $request->request->get('dureeH');
            $dureeM = $request->request->get('dureeM');
            $dateDebut = date_create_from_format('j/m/Y H:i', $request->request->get('dateDebut'));
            $dateFin = date_create_from_format('j/m/Y H:i', $request->request->get('dateFin'));

            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $id));
            $devoir->setNom($nom);
            $devoir->setDescription($description);
            $devoir->setDuree($dureeH * 3600 + $dureeM * 60);
            $devoir->setDateDebut($dateDebut);
            $devoir->setDateFin($dateFin);

            $em->persist($devoir);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Devoir content',
                'devoir' => $devoir
                )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/uploadDevoirFile_ajax", name="uploadDevoirFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function uploadDevoirFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $typeItem = $request->request->get('typeItem');
            $itemId = $request->request->get('itemId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');
            $nom = $request->request->get('nom');
            $unzipIfZip = $request->request->get('unzipIfZip') == 'true';
            $type = $request->request->get('type');

            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('var', $urlDest);
            $dir = $urlTab[0].'/var'.$urlDestTab[1];

            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $itemId));

            if($typeItem == "sujets"){
                $devoirF = new DevoirSujet();
            }elseif($typeItem == "corrigeTypes"){
                $devoirF = new DevoirCorrigeType();
            }else{
                return new JsonResponse(array(
                        'error' => true,
                        'typeItem' => "non reconnu")
                );
            }

            $devoirF->setDevoir($devoir);
            $devoirF->setNom($nom);

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $rand = rand(1, 999999);
            rename($url, $urlDest.'file'.$rand.'.'.$ext);


            if($unzipIfZip && ($type == 'application/zip' || $type == 'application/octet-stream' || $type == 'application/x-zip-compressed' || $type == 'application/zip-compressed' || $type == 'application/x-zip') ){
                $zip = new ZipArchive;
                $res = $zip->open($urlDest.'file'.$rand.'.'.$ext);
                if ($res === TRUE) {
                    $zip->extractTo($urlDest);
                    $zip->close();

                    $indexfounded = false;
                    if(file_exists($urlDest.'index.html')){
                        $indexfounded = true;
                        $devoirF->setUrl($dir.'index.html');
                    }else{
                        $filesInZip = scandir($urlDest);
                        foreach ($filesInZip as $key => $value) {
                            if (is_dir($urlDest . $value)) {
                                if(file_exists($urlDest. $value.'/index.html')){
                                    $indexfounded = true;
                                    $devoirF->setUrl($dir. $value.'/index.html');
                                    break;
                                }
                            }
                        }
                        if(!$indexfounded){
                            $devoirF->setUrl($dir.'file.'.$ext);
                        }
                    }
                    if($indexfounded) {
                        unlink($urlDest . 'file.' . $ext);
                    }
                } else {
                    return new JsonResponse(array(
                            'error' => true,
                            'unzipping' => "absence du fichier zip")
                    );
                }
            }else{
                $devoirF->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file'.$rand.'.'.$ext);
            }

            $em->persist($devoirF);
            $em->flush();
            return new JsonResponse(array('action' =>'upload File', 'id' => $itemId, 'ext' => $ext, 'nouveau '.$typeItem => $devoirF->getUrl()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/uploadCopieFile_ajax", name="uploadCopieFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function uploadCopieFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $itemId = $request->request->get('itemId');
            $userId = $request->request->get('userId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');

            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $itemId));
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $copie = $em->getRepository('AppBundle:Copie')->findOneBy(array('auteur' => $user, 'devoir' => $devoir));

            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('var', $urlDest);

            $copieFichier = new CopieFichier();
            $copieFichier->setCopie($copie);
            $copieFichier->setDateRendu(new DateTime());
            $copieFichier->setNom("[Devoir ".$devoir->getNom()."] Copie de ".$user->getFirstName()." ".$user->getLastName());

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $rand = rand(1, 999999);
            rename($url, $urlDest.'file'.$rand.'.'.$ext);

            $copieFichier->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file'.$rand.'.'.$ext);

            $em->persist($copieFichier);
            $em->flush();

            return new JsonResponse(array('action' =>'upload File', 'id' => $itemId, 'ext' => $ext, 'nouvelle copie ' => $copieFichier->getUrl()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/uploadCorrigeFile_ajax", name="uploadCorrigeFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function uploadCorrigeFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $idDevoir = $request->request->get('idDevoir');
            $userId = $request->request->get('userId');
            $etuId = $request->request->get('etuId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');

            $devoir = $em->getRepository('AppBundle:Devoir')->findOneBy(array('id' => $idDevoir));
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $etu = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $etuId));

            $copie = $em->getRepository('AppBundle:Copie')->findOneBy(array('auteur' => $etu, 'devoir' => $devoir));

            $checkCorrige = $em->getRepository('AppBundle:Corrige')->findOneBy(array('copie' => $copie));
            if($checkCorrige){
                $corrigeF = $em->getRepository('AppBundle:CorrigeFichier')->findOneBy(array('corrige' => $checkCorrige));

                $urlTab = explode('/var', $corrigeF->getUrl());

                $em->remove($corrigeF);
                $em->flush();
                $em->remove($checkCorrige);
                $em->flush();

                unlink('../var'.$urlTab[1]);
            }

            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('var', $urlDest);

            $corrige = new Corrige();
            $corrige->setDateRendu(new DateTime());
            $corrige->setCopie($copie);
            $corrige->setAuteur($user);

            $em->persist($corrige);

            $corrigeFichier = new CorrigeFichier();
            $corrigeFichier->setCorrige($corrige);
            $corrigeFichier->setNom("[Devoir ".$devoir->getNom()."] Corrige de ".$etu->getFirstName()." ".$etu->getLastName());

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $rand = rand(1, 999999);
            rename($url, $urlDest.'file'.$rand.'.'.$ext);

            $corrigeFichier->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file'.$rand.'.'.$ext);

            $em->persist($corrigeFichier);
            $em->flush();

            return new JsonResponse(array('action' =>'upload File', 'id' => $idDevoir, 'ext' => $ext, 'nouvelle copie ' => $corrigeFichier->getUrl()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/removeDevoirFile_ajax", name="removeDevoirFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function removeDevoirFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $typeItem = $request->request->get('typeItem');
            $itemId = $request->request->get('itemId');

            $entity = "";

            if($typeItem == "sujet"){
                $entity = "DevoirSujet";
            }elseif($typeItem == "corrigeType"){
                $entity = "DevoirCorrigeType";
            }else{
                return new JsonResponse(array(
                        'error' => true,
                        'typeItem' => "non reconnu")
                );
            }

            $devoirF = $em->getRepository('AppBundle:'.$entity)->findOneBy(array('id' => $itemId));

            $urlTab = explode('/var', $devoirF->getUrl());

            $em->remove($devoirF);
            $em->flush();

            unlink('../var'.$urlTab[1]);
            return new JsonResponse(array('action' =>'delete File', 'id' => $itemId, 'type' => $typeItem));
        }

        return new JsonResponse('This is not ajax!', 400);
    }


    /**
     * @Route("/sortDevoirFile_ajax", name="sortDevoirFile_ajax")
     * @Method({"GET", "POST"})
     */
    public function sortDevoirFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $arrayFileId = $request->request->get('arrayFile');
            $elementCible = $request->request->get('elementCible');

            for($i=0; $i<count($arrayFileId); $i++){
                $file = $em->getRepository('AppBundle:'.$elementCible)->findOneBy(array('id' => $arrayFileId[$i]));
                $file->setPosition($i);
                $em->persist($file);
            }

            $em->flush();
            return new JsonResponse(array(
                    'action' =>'sort File in Devoir')
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
