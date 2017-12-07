<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UsersController extends Controller
{

    /**
     * @Route("/usersManag", name="usersManag")
     */
    public function usersManagAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        return $this->render('user/userFrontEnd.html.twig', [
            'events' => "test"
        ]);
    }

    /**
     * @Route("/user/{id}", name="user")
     */
    public function userAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $coh_repo = $this->getDoctrine()->getRepository('AppBundle:Cohorte');
        $disc_repo = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $cours_repo = $this->getDoctrine()->getRepository('AppBundle:Cours');

        $cohortes = $coh_repo->findBy(array(), array('nom' => 'ASC'));
        $discs = $disc_repo->findBy(array(), array('nom' => 'ASC'));
        $cours = $cours_repo->findBy(array(), array('nom' => 'ASC'));

        $cohortes_inscr = array();
        if($cohortes){
            foreach($cohortes as $cohorte){
                array_push($cohortes_inscr, [
                    'cohorte' => $cohorte,
                    'isInscrit' => $coh_repo->userIsInscrit($user->getId(), $cohorte->getId())
                ]);
            }
        }

        $discs_inscr = array();
        if($discs){
            foreach($discs as $disc){
                array_push($discs_inscr, [
                    'discipline' => $disc,
                    'isInscrit' => $disc_repo->userIsInscrit($user->getId(), $disc->getId()),
                    'hasAccess' => $disc_repo->userHasAccess($user->getId(), $disc->getId()),
                    'cohortes' => $disc->getCohortes()
                ]);
            }
        }

        $cours_inscr = array();
        if($cours){
            foreach($cours as $cour){

                array_push($cours_inscr, [
                    'cours' => $cour,
                    'isInscrit' => $cours_repo->userIsInscrit($user->getId(), $cour->getId()),
                    'hasAccess' => $cours_repo->userHasAccess($user->getId(), $cour->getId()),
                    'cohortes' => $cour->getCohortes()
                ]);
            }
        }

        return $this->render('user/one.html.twig', [
            'user' => $user,
            'cohortesInsc' => $cohortes_inscr,
            'disciplinesInsc' => $discs_inscr,
            'coursInsc' => $cours_inscr,
        ]);
    }

    /**
     * @Route("/cohorteUsers/{id}", name="cohorteUsers")
     */
    public function cohorteUsersAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $cohorte = $this->getDoctrine()->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $id));

        return $this->render('entity/oneItemCoh.html.twig', [
            'cohorte' => $cohorte,
        ]);
    }

    /**
     * @Route("/disciplineUsers/{id}", name="disciplineUsers")
     */
    public function disciplineUsersAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $discipline = $this->getDoctrine()->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));

        return $this->render('entity/oneItemDis.html.twig', [
            'discipline' => $discipline,
        ]);
    }

    /**
     * @Route("/coursUsers/{id}", name="coursUsers")
     */
    public function coursUsersAction (Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));

        return $this->render('entity/oneItemCours.html.twig', [
            'cours' => $cours,
        ]);
    }

    /**
     * @Route("/inscrireUser_ajax", name="inscrireUser_ajax")
     */
    public function inscrireUserAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $itemId = $request->request->get('idItem');
            $itemType = $request->request->get('typeItem');
            $userId = $request->request->get('idUser');

            if($itemType == 'cohorte'){
                // commence par désinscrire le user des disciplines et des cours qui en découlent

            }

            //$em->persist();

            $em->flush();

            return new JsonResponse(array('action' =>'change inscription of user'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }
}
