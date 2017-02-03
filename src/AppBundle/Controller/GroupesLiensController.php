<?php

namespace AppBundle\Controller;

use AppBundle\Entity\AssocGroupeLiens;
use AppBundle\Entity\GroupeLiens;
use AppBundle\Entity\CategorieLien;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GroupesLiensController extends Controller
{
    /**
     * @Route("/changeContentGroupe_ajax", name="changeContentGroupe_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentGroupeAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');

            $groupe = $em->getRepository('AppBundle:GroupeLiens')->findOneBy(array('id' => $id));
            $groupe->setNom($nom);
            $groupe->setDescription($description);

            $em->persist($groupe);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Section Name',
                'id' => $groupe->getId(),
                'nom' => $groupe->getNom())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/addLienGroupe_ajax", name="addLienGroupe_ajax")
     * @Method({"GET", "POST"})
     */
    public function addLienGroupeAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $groupeId = $request->request->get('groupeId');
            $lienId = $request->request->get('lienId');
            $categorieId = $request->request->get('categorieId');
            $nomLien = $request->request->get('nomLien');

            $groupe = $em->getRepository('AppBundle:GroupeLiens')->findOneBy(array('id' => $groupeId));
            $lien = $em->getRepository('AppBundle:Lien')->findOneBy(array('id' => $lienId));
            $categorie = $em->getRepository('AppBundle:CategorieLien')->findOneBy(array('id' => $categorieId));

            $assocGL = new AssocGroupeLiens();
            $assocGL->setNom($nomLien);
            $assocGL->setCategorieLien($categorie);
            $assocGL->setGroupe($groupe);
            $assocGL->setLien($lien);

            //on cherche la position
            $groupeAssocs = $em->getRepository('AppBundle:AssocGroupeLiens')->findBy(array('groupe' => $groupe));
            $posMax = 0;
            for($i=0; $i<count($groupeAssocs); $i++){
                if($groupeAssocs[$i]->getPosition() > $posMax){
                    $posMax = $groupeAssocs[$i]->getPosition();
                }
            }
            $assocGL->setPosition($posMax+1);


            $em->persist($assocGL);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Add Lien in Groupe',
                    'id' => $groupe->getId(),
                    'nom' => $groupe->getNom())
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
