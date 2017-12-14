<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_d;
use AppBundle\Entity\Cohorte;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Discipline;
use DateTime;
use AppBundle\Entity\Inscription_coh;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsersController extends Controller
{

    /**
     * @Route("/usersManag", name="usersManag")
     */
    public function usersManagAction (Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $userRepo->findAll();

        return $this->render('user/userFrontEnd.html.twig', [
            'users' => $users
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

        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class, array(
                'label' => 'Nom',
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('firstname', TextType::class, array(
                'label' => 'Prénom',
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('email', EmailType::class, array(
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('phone', TextType::class, array(
                'label' => 'Numéro de téléphone',
                'required' => false,
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array('class' => 'col-sm-8')
            ))
            ->add('institut', EntityType::class, array(
                'class' => 'AppBundle:Institut',
                'choice_label' => 'nom',
                'multiple' => false,
                'label_attr' => array('class' => 'col-sm-4')
            ))
            ->add('enabled', CheckboxType::class, array(
                'label' => 'Activé'
            ))
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }


        return $this->render('user/one.html.twig', [
            'user' => $user,
            'cohortesInsc' => $cohortes_inscr,
            'disciplinesInsc' => $discs_inscr,
            'coursInsc' => $cours_inscr,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/itemUsers/{id}/type/{type}", name="itemUsers")
     */
    public function itemUsersAction (Request $request, $id, $type)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $entityName = "";
        if($type == "cohorte"){
            $entityName = "Cohorte";
        }else if($type == "discipline"){
            $entityName = "Discipline";
        }else if($type == "cours"){
            $entityName = "Cours";
        }

        $itemRepo = $this->getDoctrine()->getRepository('AppBundle:'.$entityName);
        $item = $this->getDoctrine()->getRepository('AppBundle:'.$entityName)->findOneBy(array('id' => $id));

        $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $userRepo->findAll();

        $usersNoAccessTab = array();
        $usersAccessTab = array();

        foreach($users as $user){
            if($itemRepo->userHasAccessOrIsInscrit($user->getId(), $id)){
                array_push($usersAccessTab, ["user" => $user, "isInscrit" => $itemRepo->userIsInscrit($user->getId(), $id)]);
            }else{
                array_push($usersNoAccessTab, $user);
            }
        }

        if($type == "cohorte"){
            $form = $this->createFormBuilder($item)
                ->add('nom', TextType::class, array(
                    'label' => 'Nom',
                    'label_attr' => array('class' => 'col-sm-4'),
                    'attr' => array('class' => 'col-sm-8')
                ))
                ->add('description', CKEditorType::class, array(
                    'label' => 'Description'
                ))
                ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
                ->getForm();
        }else if($type == "discipline"){
            $form = $this->createFormBuilder($item)
                ->add('nom', TextType::class, array(
                    'label' => 'Nom',
                    'label_attr' => array('class' => 'col-sm-4'),
                    'attr' => array('class' => 'col-sm-8')
                ))
                ->add('description', CKEditorType::class, array(
                    'label' => 'Description'
                ))
                ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
                ->getForm();
        }else if($type == "cours"){
            $form = $this->createFormBuilder($item)
                ->add('nom', TextType::class, array(
                    'label' => 'Nom',
                    'label_attr' => array('class' => 'col-sm-4')
                ))
                ->add('position', TextType::class, array(
                    'label' => 'Position',
                    'label_attr' => array('class' => 'col-sm-4')
                ))
                ->add('discipline', EntityType::class, array(
                    'class' => 'AppBundle:Discipline',
                    'choice_label' => 'Nom',
                    'multiple' => false,
                    'label_attr' => array('class' => 'col-sm-4')
                ))
                ->add('description', CKEditorType::class, array(
                    'label' => 'Description'
                ))
                ->add('accueil', CKEditorType::class, array(
                    'label' => 'Accueil'
                ))

                ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
                ->getForm();
        }


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('user/itemUsers.html.twig', [
            'item' => $item,
            'entityName' => $entityName,
            'usersNoHavingAccess' => $usersNoAccessTab,
            'usersHavingAccess' => $usersAccessTab,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/inscrireUser_ajax", name="inscrireUser_ajax")
     */
    public function inscrireUserAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $test = "";

            $itemId = $request->request->get('idItem');
            $itemType = $request->request->get('typeItem');
            $userId = $request->request->get('idUser');
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));

            if($itemType == 'cohorte'){
                // commence par désinscrire le user des disciplines et des cours qui en découlent
                $cohorte = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $itemId));
                foreach ($cohorte->getDisciplines() as $disc) {

                    // d'abords les disciplines associées à la cohorte
                    $inscriptionsDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array(
                        'discipline' => $disc,
                        'user' => $user
                    ));
                    if($inscriptionsDs){
                        foreach($inscriptionsDs as $inscr){
                            $em->remove($inscr);
                        }
                    }
                    // puis les cours dont la discipline est associée
                }

                // puis on supprime les inscriptions au cours associés à la cohorte
                foreach ($cohorte->getCours() as $co) {
                    $inscriptionsCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array(
                        'cours' => $co,
                        'user' => $user
                    ));
                    if($inscriptionsCs){

                        foreach($inscriptionsCs as $inscr){
                            $em->remove($inscr);
                        }
                    }
                }

                // puis on supprime les inscriptions au cours dont la discipline est associée à la cohorte
                $inscriptionsCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array(
                    'user' => $user
                ));
                if($inscriptionsCs){
                    foreach($inscriptionsCs as $inscr){
                        if($cohorte->getDisciplines()->contains($inscr->getCours()->getDiscipline())){
                            $em->remove($inscr);
                        }
                    }
                }

                // puis on créé l'inscription
                $new_inscr = new Inscription_coh();
                $new_inscr->setUser($user);
                $new_inscr->setCohorte($cohorte);
                $new_inscr->setDateInscription(new DateTime());
                $new_inscr->setRole($role);
                $em->persist($new_inscr);

            }else if($itemType == 'discipline'){
                $discipline = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $itemId));

                // on supprime les inscriptions au cours dont c'est la discipline
                $cours = $em->getRepository('AppBundle:Cours')->findBy(array(
                    'discipline' => $discipline
                ));
                if($cours){
                    foreach ($cours as $co) {
                        $inscriptionsC = $em->getRepository('AppBundle:Inscription_c')->findBy(array(
                            'cours' => $co,
                            'user' => $user
                        ));
                        if($inscriptionsC){
                            foreach($inscriptionsC as $inscr){
                                $em->remove($inscr);
                            }
                        }
                    }
                }

                // puis on créé l'inscription
                $new_inscr = new Inscription_d();
                $new_inscr->setUser($user);
                $new_inscr->setDiscipline($discipline);
                $new_inscr->setDateInscription(new DateTime());
                $new_inscr->setRole($role);
                $em->persist($new_inscr);
            }else if($itemType == 'cours'){
                $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $itemId));

                $new_inscr = new Inscription_c();
                $new_inscr->setUser($user);
                $new_inscr->setCours($cours);
                $new_inscr->setDateInscription(new DateTime());
                $new_inscr->setRole($role);
                $em->persist($new_inscr);
            }

            $em->flush();

            return new JsonResponse(array(
                'action' =>'change inscription of user',
                'test' => $test
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/desInscrireUser_ajax", name="desInscrireUser_ajax")
     */
    public function desInscrireUserAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $test = "";

            $itemId = $request->request->get('idItem');
            $itemType = $request->request->get('typeItem');
            $userId = $request->request->get('idUser');
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            $item = null;
            $inscrEntityName = "";

            if($itemType == 'cohorte'){
                $item = $em->getRepository('AppBundle:Cohorte')->findOneBy(array('id' => $itemId));
                $inscrEntityName = "Inscription_coh";

            }else if($itemType == 'discipline'){
                $item = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $itemId));
                $inscrEntityName = "Inscription_d";
            }else if($itemType == 'cours'){
                $item = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $itemId));
                $inscrEntityName = "Inscription_c";
            }
            $inscriptions = $em->getRepository('AppBundle:'.$inscrEntityName)->findBy(array(
                $itemType => $item,
                'user' => $user
            ));
            if($inscriptions) {
                foreach ($inscriptions as $inscr) {
                    $em->remove($inscr);
                }
            }

            $em->flush();

            return new JsonResponse(array(
                'action' =>'change inscription of user',
                'test' => $test
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }


}
