<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CategorieLien;
use AppBundle\Entity\Mp3Podcast;
use AppBundle\Entity\Podcast;
use AppBundle\Entity\RessourceH5P;
use AppBundle\Entity\Section;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use DOMDocument;
use DOMXPath;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use wapmorgan\Mp3Info\Mp3Info;

class H5pController extends Controller
{
    /**
     * @Route("/changeContentH5p_ajax", name="changeContentH5p_ajax")
     * @Method({"GET", "POST"})
     */
    public function changeContentH5pAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $nom = $request->request->get('nom');
            $description = $request->request->get('description');

            /* @var $h5p RessourceH5P */
            $h5p = $em->getRepository('AppBundle:RessourceH5P')->findOneBy(array('id' => $id));
            $h5p->setNom($nom);
            $h5p->setDescription($description);

            $em->flush();
            return new JsonResponse(array(
                'action' =>'change H5p Infos',
                'h5p' => $h5p->getId()
                )
            );
        }
        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * Créer une ressources avec les modules de H5P
     *
     * @Route("/editH5Pressource/{id}", name="editH5Pressource", methods={"GET", "POST"})
     * @param Request $request
     * @param $id
     */

    public function editH5PressourceAction(request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if($this->get('session')->get( 'refererBase' ) == null){

            $request->getSession()->set('refererBase', $request->headers->get('referer'));
        }
        //on verif si l'id existe sinon on le redirige à l'accueil
        $content = null;
        if (!$id) {
            return $this->redirectToRoute( "homepage" );
        } else {
            //on verif si l'id de la section est valide
            $em = $this->getDoctrine()->getManager();
            /* @var RessourceH5P $ressource */
            $ressource = $em->getRepository( 'AppBundle:RessourceH5P' )->findOneBy( array('id' => $id) );

            //Retourne la vue pour créer les ressources
            //Appelle le service d'H5P en plus pour les ressources
            $h5pIntegration = $this->get('emmedy_h5p.integration')->getEditorIntegrationSettings($ressource->getH5p() ? $ressource->getH5p()->getId() : null);
            //création du formulaire

            //Change le nom du formulaire pour H5P
            $form = $this->get( 'form.factory' )->createNamed( 'h5p' )
                ->add( 'titre', null, [
                    "attr" => ['class' => 'form-control'],
                    'data' => $ressource->getNom()
                ] )
                ->add( 'description', CKEditorType::class, [
                    'config_name' => 'my_simple_config',
                    "attr" => ['class' => 'form-control'],
                    'data' => $ressource->getDescription(),
                    "required" => false
                ] )
                ->add( 'library', HiddenType::class, [
                    "attr" => ['id' => 'h5p_library ']
                ] );

            //verifie le contenu de h5p si il y en a on l'ajoute sinon on renvoie rien
            if ($ressource->getH5p() != null) {
                $form
                    ->add('library', HiddenType::class, [
                        "attr" => ['id' => 'h5p_library'],
                        'data' => (string)$ressource->getH5p()->getLibrary()
                    ])
                    ->add('parameters', HiddenType::class, [
                        'data' => $ressource->getH5p()->getParameters()
                    ]);
            } else {
                $form
                    ->add('library', HiddenType::class, [
                        "attr" => ['id' => 'h5p_library'],
                    ])
                    ->add('parameters', HiddenType::class);
            }

            $form->handleRequest( $request );
            //verif les infos de la requête
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                //verif si il y a du contenu H5P ou pas
                if ($data['library'] != null) {
                    //sauvegarde et renvoie l'id de la table H5P_content
                    $contentId = $this->get( 'emmedy_h5p.library_storage' )->storeLibraryData( $data['library'], $data['parameters'], $ressource->getH5p() );
                    //obliger de rajouter comme ça... pour que cela fonctionne supprimer l'entitée content qui ne sert à rien grace au one to one et supprimer les restes des entitées useless
                    /* @var \Emmedy\H5PBundle\Entity\Content $H5Pcontent */
                    $H5Pcontent = $em->getRepository( '\Emmedy\H5PBundle\Entity\Content' )->findOneBy( array('id' => $contentId) );
                }

                $ressource->setNom($data['titre']);
                $ressource->setDescription($data['description']);

                if ($data['library'] != null) {
                    //defini suivant le contenu d'H5P ne pas changer cela fonctionne
                    /** @var \Emmedy\H5PBundle\Entity\Content $H5Pcontent */
                    $ressource->setH5p( $H5Pcontent );
                }

                $em->flush();

                return $this->redirectToRoute( 'oneCours', array('id' => $ressource->getCours()->getId(), 'mode' => 'admin') );

            }

            //Renvoie la vue du formulaire
            return $this->render( 'ressources/h5p.html.twig', [
                'form' => $form->createView(),
                'h5p' => $ressource,
                'h5pIntegration' => $h5pIntegration,
                'h5pCoreTranslations' => $this->get( 'emmedy_h5p.integration' )->getTranslationFilePath()
            ] );
        }
    }
}
