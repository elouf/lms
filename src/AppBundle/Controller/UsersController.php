<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Inscription;
use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_d;
use AppBundle\Entity\Cohorte;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Inscription_sess;
use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Repository\CohorteRepository;
use DateTime;
use AppBundle\Entity\Inscription_coh;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
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

        $cohRepo = $this->getDoctrine()->getRepository('AppBundle:Cohorte');
        $cohortes = $cohRepo->findAll();
        $myUsers = [];
        /* @var $user User */
        foreach($users as $user){
            if($user->isEnabled()){
                $myCohortes = [];
                if($cohortes){
                    /* @var $cohorte Cohorte */
                    foreach ($cohortes as $cohorte) {
                        if($cohRepo->userHasAccessOrIsInscrit($user->getId(), $cohorte->getId())){
                            array_push($myCohortes, $cohorte);
                        }
                    }
                }
                array_push($myUsers, ['user' => $user, 'cohortes' => $myCohortes]);
            }else{
                array_push($myUsers, ['user' => $user, 'cohortes' => []]);
            }

        }


        return $this->render('user/userFrontEnd.html.twig', [
            'myUsers' => $myUsers
        ]);
    }

    /**
     * @Route("/reactiveAll", name="reactiveAll")
     */
    public function usersReactivationAction (Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $em->getRepository('AppBundle:User')->findBy(array('enabled' => false));

        $arrayUsers = [];

        if($users) {
            foreach ($users as $user) {
                $user->setEnabled(true);
                array_push($arrayUsers, $user);
            }
        }

        $em->flush();

        return $this->render('user/desactivation.html.twig', [
            'users' => $arrayUsers
        ]);
    }

    /**
     * @Route("/usersDesactivation/{year}/{month}/{day}", name="usersDesactivation")
     */
    public function usersDesactivationAction (Request $request, $year, $month, $day)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $em->getRepository('AppBundle:User')->findBy(array('enabled' => true));

        $arrayUsers = [];

        if($users) {
            $d1 = new DateTime($year.'-'.$month.'-'.$day.' 00:00:00');
            /* @var $user User */
            foreach ($users as $user) {
                $isEns = false;

                $inscrs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));
                if ($inscrs){
                    /* @var $inscr Inscription_coh */
                    foreach ($inscrs as $inscr) {
                        if($inscr->getRole() == 'Enseignant'){
                            $isEns = true;
                            break;
                        }
                    }
                }
                $inscrs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('user' => $user));
                if ($inscrs){
                    /* @var $inscr Inscription_d */
                    foreach ($inscrs as $inscr) {
                        if($inscr->getRole() == 'Enseignant'){
                            $isEns = true;
                            break;
                        }
                    }
                }
                $inscrs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('user' => $user));
                if ($inscrs){
                    /* @var $inscr Inscription_c */
                    foreach ($inscrs as $inscr) {
                        if($inscr->getRole() == 'Enseignant'){
                            $isEns = true;
                            break;
                        }
                    }
                }

                if(!$isEns && $this->getUser()->getId() != $user->getId() && $user->getCreatedAt()<$d1){
                    $user->setEnabled(false);
                    array_push($arrayUsers, $user);
                }
            }
        }

        $em->flush();

        return $this->render('user/desactivation.html.twig', [
            'users' => $arrayUsers
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
        $copie_repo = $this->getDoctrine()->getRepository('AppBundle:Copie');

        $cohortes = $coh_repo->findBy(array(), array('nom' => 'ASC'));
        $discs = $disc_repo->findBy(array(), array('nom' => 'ASC'));
        $cours = $cours_repo->findBy(array(), array('nom' => 'ASC'));
        $copies = $copie_repo->findBy(array('auteur' => $user), array('id' => 'ASC'));

        $myCopies = array();
        if($copies){
            foreach($copies as $copie){
                $fichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier')->findOneBy(array('copie' => $copie));
                if($fichier){
                    array_push($myCopies, ['copie' => $copie, 'fichier' => $fichier]);
                }
            }
        }

        $allcourses = $cours_repo->findAll();
        $sessions_tab = array();
        $sessions_tabTest = array();
        foreach($allcourses as $coursFiltre){
            if($coursFiltre->getSession() != null && $cours_repo->userHasAccess($user->getId(), $coursFiltre->getId())){
                $sess = $coursFiltre->getSession();
                $cours_tabTest = array();
                if(!in_array($sess, $sessions_tabTest)) {
                    foreach($allcourses as $coursCheckDisc) {
                        if($coursCheckDisc->getSession() == $sess && $cours_repo->userHasAccess($user->getId(), $coursCheckDisc->getId())){
                            if (!in_array($coursCheckDisc, $cours_tabTest)) {
                                array_push($cours_tabTest, $coursCheckDisc);
                            }
                        }

                    }

                    array_push($sessions_tabTest, $sess);
                    $isInscrit = $this->getDoctrine()->getRepository('AppBundle:Session')->userIsInscrit($user->getId(), $sess->getId());
                    array_push($sessions_tab, array(
                        'session' => $sess,
                        'isInscrit' => $isInscrit,
                        'sessionsForUser' => $cours_tabTest
                    ));
                }
            }
        }

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
            ->add('created_at', 'date', array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'format' => 'dd/MM/yyyy',
                'label' => 'Date d\'inscription',
                'label_attr' => array('class' => 'col-sm-4'),
                'attr' => array(
                    'class' => 'col-sm-8',
                    'readonly' => true,
                )
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
            'form' => $form->createView(),
            'sessions' => $sessions_tab,
            'copies' => $myCopies
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
        }else if($type == "session"){
            $entityName = "Session";
        }

        $cohRepo = $this->getDoctrine()->getRepository('AppBundle:Cohorte');

        $itemRepo = $this->getDoctrine()->getRepository('AppBundle:'.$entityName);
        $item = $this->getDoctrine()->getRepository('AppBundle:'.$entityName)->findOneBy(array('id' => $id));

        $userRepo = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $userRepo->findBy(array('enabled' => true));

        $usersNoAccessTab = array();
        $usersAccessTab = array();

        foreach($users as $user){
            if($itemRepo->userHasAccessOrIsInscrit($user->getId(), $id)){
                array_push($usersAccessTab, [
                    "user" => $user,
                    "isInscrit" => $itemRepo->userIsInscrit($user->getId(), $id),
                    "myCohs" => $cohRepo->allForUser($user->getId()),
                    "role" => $itemRepo->getRole($user->getId(), $id)
                ]);
            }else{
                array_push($usersNoAccessTab, [
                    'user' => $user,
                    "myCohs" => $cohRepo->allForUser($user->getId())
                ]);
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
        }else if($type == "session"){
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

    /**
     * @Route("/inscrSessUser_ajax", name="inscrSessUser_ajax")
     */
    public function inscrSessUserAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');
            $userId = $request->request->get('idUser');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));

            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));

            $inscr = new Inscription_sess();
            $inscr->setSession($session);
            $inscr->setUser($user);
            $inscr->setDateInscription(new DateTime());
            if($role){
                $inscr->setRole($role);
            }

            $em->persist($inscr);
            $em->flush();

            return new JsonResponse(array('action' =>'Inscription user session'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/desinscrSessUser_ajax", name="desinscrSessUser_ajax")
     */
    public function desinscrSessUserAjaxAction (Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');
            $userId = $request->request->get('idUser');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));

            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            $inscr = $em->getRepository('AppBundle:Inscription_sess')->findOneBy(array('session' => $session, 'user' => $user));

            $em->remove($inscr);

            $em->flush();

            return new JsonResponse(array('action' =>'Inscription user session'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    public function allSessionsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $item = $em->getRepository('AppBundle:Session')->findAll();

        return $this->render(
            'user/itemsList.html.twig',
            array(
                'items' => $item,
                'type' => 'session',
                )
        );
    }
    public function allCohortesAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $item = $em->getRepository('AppBundle:Cohorte')->findAll();

        return $this->render(
            'user/itemsList.html.twig',
            array(
                'items' => $item,
                'type' => 'cohorte',
            )
        );
    }


}
