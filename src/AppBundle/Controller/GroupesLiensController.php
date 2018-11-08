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
            $isVertical = $request->request->get('isVertical');

            /* @var $groupe GroupeLiens */
            $groupe = $em->getRepository('AppBundle:GroupeLiens')->findOneBy(array('id' => $id));
            $groupe->setNom($nom);
            $groupe->setDescription($description);
            $groupe->setIsVertical($isVertical == 'true');

            $em->persist($groupe);
            $em->flush();

            return new JsonResponse(array(
                'action' =>'change Groupe Infos',
                'groupe' => $groupe)
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
                    'groupe' => $groupe,
                    'lien' => $lien)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }


    /**
     * @Route("/removeLienGroupe_ajax", name="removeLienGroupe_ajax")
     * @Method({"GET", "POST"})
     */
    public function removeLienGroupeAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idAssoc = $request->request->get('idAssoc');
            $assoc = $em->getRepository('AppBundle:AssocGroupeLiens')->findOneBy(array('id' => $idAssoc));

            $em->remove($assoc);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Remove Lien from Groupe',
                    'assoc' => $assoc)
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/sortGroupeLiensAssocs_ajax", name="sortGroupeLiensAssocs_ajax")
     * @Method({"GET", "POST"})
     */
    public function sortGroupeLiensAssocsAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $arrayAssocsId = $request->request->get('arrayAssocs');

            for($i=0; $i<count($arrayAssocsId); $i++){
                $assoc = $em->getRepository('AppBundle:AssocGroupeLiens')->findOneBy(array('id' => $arrayAssocsId[$i]));
                $assoc->setPosition($i);
                $em->persist($assoc);
            }

            $em->flush();
            return new JsonResponse(array(
                    'action' =>'sort Liens in Groupe')
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }


    /**
     * @Route("/getLienGroupe_ajax", name="getLienGroupe_ajax")
     * @Method({"GET", "POST"})
     */
    public function getLienGroupeAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idAssoc = $request->request->get('idAssoc');
            $assoc = $em->getRepository('AppBundle:AssocGroupeLiens')->findOneBy(array('id' => $idAssoc));
            $lien = $assoc->getLien();

            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Get Lien from Groupe',
                    'nomAssoc' => $assoc->getNom(),
                    'catAssoc' => $assoc->getCategorieLien()->getId(),
                    'nomLien' => $lien->getNom(),
                    'descrLien' => $lien->getDescription(),
                    'typeLien' => $lien->getTypeLien()->getId(),
                    'urlLien' => $lien->getUrl()
                    )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/editLienGroupe_ajax", name="editLienGroupe_ajax")
     * @Method({"GET", "POST"})
     */
    public function editLienGroupeAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $idAssoc = $request->request->get('idAssoc');
            $nomAssoc = $request->request->get('nomAssoc');
            $nomLien = $request->request->get('nomLien');
            $urlLien = $request->request->get('urlLien');
            $descrLien = $request->request->get('descrLien');
            $typeLienId = $request->request->get('typeLienId');
            $categorieLienId = $request->request->get('categorieLienId');


            $assoc = $em->getRepository('AppBundle:AssocGroupeLiens')->findOneBy(array('id' => $idAssoc));
            $lien = $em->getRepository('AppBundle:Lien')->findOneBy(array('id' => $assoc->getLien()->getId()));
            $categorie = $em->getRepository('AppBundle:CategorieLien')->findOneBy(array('id' => $categorieLienId));
            $typeLien = $em->getRepository('AppBundle:TypeLien')->findOneBy(array('id' => $typeLienId));

            $assoc->setNom($nomAssoc);
            $assoc->setCategorieLien($categorie);

            $lien->setNom($nomLien);
            $lien->setUrl($urlLien);
            $lien->setDescription($descrLien);
            $lien->setTypeLien($typeLien);

            $em->persist($assoc);
            $em->persist($lien);
            $em->flush();

            return new JsonResponse(array(
                    'action' =>'Edit Lien in Groupe',
                    'nomAssoc' => $assoc->getNom(),
                    'catAssoc' => $assoc->getCategorieLien()->getId(),
                    'nomLien' => $lien->getNom(),
                    'descrLien' => $lien->getDescription(),
                    'typeLien' => $lien->getTypeLien()->getId(),
                    'urlLien' => $lien->getUrl()
                    )
            );
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
