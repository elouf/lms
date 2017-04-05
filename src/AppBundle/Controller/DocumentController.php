<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Discipline;
use AppBundle\Entity\Document;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DocumentController extends Controller
{

    /**
     * @Route("/documents/{id}", name="documents")
     */
    public function documentsByDisciplineAction (Request $request, $id)
    {
        $discipline = $this->getDoctrine()->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));

        $assocsDisc = $this->getDoctrine()->getRepository('AppBundle:AssocDocDisc')->findBy(array('discipline' => $discipline));

        $documents = array();
        for($i=0; $i<count($assocsDisc); $i++) {
            $documents[$i]["fromDisc"] = $assocsDisc[$i]->getDocument();
        }

        $users = array();
        $cohortes = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findAll();
        foreach($cohortes as $cohorte){
            if(!$cohorte->getDisciplines()->contains($discipline)){
                $inscrCohs = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh')->findBy(array('cohorte' => $cohorte));
                foreach($inscrCohs as $inscrCoh){
                    if(!in_array($inscrCoh->getUser(), $users)) {
                        array_push($users, $inscrCoh->getUser());
                    }
                }
            }
        }
        $inscrDs = $this->getDoctrine()->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        foreach($inscrDs as $inscrD){
            if(!in_array($inscrD->getUser(), $users)) {
                array_push($users, $inscrD->getUser());
            }
        }
        dump($users);
        return $this->render('documents/oneByDisc.html.twig', ['discipline' => $discipline, 'documents' => $documents, 'users' => $users]);
    }

    /**
     * @Route("/uploadDocFile_ajax", name="uploadDocFile_ajax")
     */
    public function uploadDocFileAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $discId = $request->request->get('discId');
            $url = utf8_encode($request->request->get('url'));
            $urlDest = $request->request->get('urlDest');
            $currentUrl = $request->request->get('currentUrl');
            $userId = $request->request->get('userId');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');


            $urlTab = explode('/web', $currentUrl);
            $urlDestTab = explode('/var', $urlDest);

            $disipline = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $discId));
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            $doc = new Document();
            $doc->setProprietaire($user);
            $doc->setNom($nom);
            $doc->setDescription($description);
            $doc->setDateCrea(new DateTime());

            $ext = pathinfo($url, PATHINFO_EXTENSION);
            $rand = rand(1, 999999);
            rename($url, $urlDest.'file'.$rand.'.'.$ext);

            $doc->setUrl($urlTab[0].'/var'.$urlDestTab[1].'file'.$rand.'.'.$ext);

            $em->persist($doc);
            $em->flush();
            return new JsonResponse(array('action' =>'upload Document for Discipline', 'id' => $discId, 'ext' => $ext));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
