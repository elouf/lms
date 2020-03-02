<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\ForumPost;
use AppBundle\Entity\ForumSujet;
use AppBundle\Entity\GroupeResa;
use AppBundle\Entity\Inscription;
use AppBundle\Entity\Inscription_c;
use AppBundle\Entity\Inscription_d;
use AppBundle\Entity\Cohorte;
use AppBundle\Entity\Cours;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Inscription_sess;
use AppBundle\Entity\Role;
use AppBundle\Entity\Section;
use AppBundle\Entity\SystemeResa;
use AppBundle\Entity\User;
use AppBundle\Repository\CohorteRepository;
use AppBundle\Repository\CopieRepository;
use AppBundle\Repository\CoursRepository;
use AppBundle\Repository\DisciplineRepository;
use AppBundle\Repository\InstitutRepository;
use AppBundle\Repository\UserStatCoursRepository;
use AppBundle\Repository\UserStatLoginRepository;
use AppBundle\Repository\UserStatRessourceRepository;
use DateTime;
use AppBundle\Entity\Inscription_coh;
use Doctrine\Common\Collections\ArrayCollection;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use JMS\Serializer\SerializationContext;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
     * @Route("/myprofile", name="myprofile")
     */
    public function myProfileAction(Request $request, $fromCommandeCode = null)
    {
        $em = $this->getDoctrine()->getManager();

        $instituts = $this->getDoctrine()->getRepository('AppBundle:Institut')->findAll();

        /* @var $user User */
        $user = $this->getUser();
        $template = $this->getParameter('template');
        if ($template === 'afadec') {
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, array(
                    'label' => 'Nom '
                ))
                ->add('firstname', TextType::class, array(
                    'label' => 'Prénom '
                ))
                ->add('email', EmailType::class, array(
                    'label' => 'Email '
                ))
                ->add('plainPassword', RepeatedType::class, array(
                    'label' => 'Mot de passe ',
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'error_bubbling' => true,
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => false,
                    'first_options' => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Répétez le mot de passe'),
                ))
                ->add('receiveAutoNotifs', CheckboxType::class, array(
                    'label' => 'Recevoir les notifications ',
                    'required' => false
                ))
                ->add('institut', EntityType::class, array(
                    'class' => 'AppBundle:Institut',
                    'choice_label' => 'nom',
                    'multiple' => false
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'Mettre à jour',
                    'attr' => array('class' => 'button btn btnAdmin btnSaveInputChange')
                ))
                ->getForm();
        }elseif ($template === 'excellencePro') {
            $form = $this->createFormBuilder($user)
                ->add('lastname', TextType::class, array(
                    'label' => 'Nom '
                ))
                ->add('firstname', TextType::class, array(
                    'label' => 'Prénom '
                ))
                ->add('email', EmailType::class, array(
                    'label' => 'Email '
                ))
                ->add('plainPassword', RepeatedType::class, array(
                    'label' => 'Mot de passe ',
                    'type' => PasswordType::class,
                    'invalid_message' => 'Les mots de passe doivent être identiques.',
                    'error_bubbling' => true,
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => false,
                    'first_options' => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Répétez le mot de passe'),
                ))
                ->add('typeUser', ChoiceType::class, array(
                    'choices'  => [
                        'Enseignant avec prise en charge des frais annexes' => 0,
                        'Enseignant sans prise en charge des frais annexes' => 1,
                        'Parent d’élève' => 2,
                        'Autre' => 3
                    ],
                    'label' => 'Type d‘utilisateur '
                ))
                ->add('uai', TextType::class, array(
                    'label' => 'UAI '
                ))
                ->add('numec', TextType::class, array(
                    'label' => 'NUMEC '
                ))
                ->add('submit', SubmitType::class, array(
                    'label' => 'Mettre à jour',
                    'attr' => array('class' => 'button btn btnAdmin btnSaveInputChange')
                ))
                ->getForm();
        }

        $form->handleRequest($request);


        if ($form->isValid()) {
            $user = $form->getData();
            $pass = $form['plainPassword']->getData();
            if ($pass) {
                $user->setPassword($this->container->get('security.encoder_factory')->getEncoder($user)->encodePassword($pass, $user->getSalt()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('myprofile', array('user' => $user));
        }

        /* @var UserStatRessourceRepository $userStatRessRepo */
        $userStatRessRepo = $this->getDoctrine()->getRepository('AppBundle:UserStatRessource');

        /* @var UserStatCoursRepository $userStatCoursRepo */
        $userStatCoursRepo = $this->getDoctrine()->getRepository('AppBundle:UserStatCours');

        /* @var ArrayCollection $userStatLogin */
        $userStatLogin = $this->getDoctrine()->getRepository('AppBundle:UserStatLogin')->findBy(array('user' => $user));

        /* @var ArrayCollection $copies */
        $copies = $this->getDoctrine()->getRepository('AppBundle:Copie')->findBy(array('auteur' => $user));

        /* @var ArrayCollection $documents */
        $documents = $this->getDoctrine()->getRepository('AppBundle:Document')->findBy(array('proprietaire' => $user));

        /* @var ArrayCollection $sujets */
        $sujets = $this->getDoctrine()->getRepository('AppBundle:ForumSujet')->findBy(array('createur' => $user));

        /* @var ArrayCollection $posts */
        $posts = $this->getDoctrine()->getRepository('AppBundle:ForumPost')->findBy(array('auteur' => $user));

        return $this->render('user/myProfile.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir') . '/..'),
            'user' => $user,
            'form' => $form->createView(),
            'userStatRess' => $userStatRessRepo->findbyUser($user),
            'userStatCours' => $userStatCoursRepo->findbyUser($user),
            'userStatLogin' => $userStatLogin,
            'copies' => $copies,
            'documents' => $documents,
            'sujets' => $sujets,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/usersManag", name="usersManag")
     */
    public function usersManagAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $stats = $this->getDoctrine()->getRepository('AppBundle:UserStatLogin')->findAll();

        return $this->render('user/userFrontEnd.html.twig', [
            'myUsers' => $users,
            'stats' => $stats
        ]);
    }

    /**
     * @Route("/updateUsersTab_ajax", name="updateUsersTab_ajax")
     */
    public function updateUsersTabAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $userIds = $request->request->get('userIds');
            $repoUser = $em->getRepository('AppBundle:User');
            if ($userIds) {
                $users = [];
                /* @var $cohRepo CohorteRepository */
                $cohRepo = $this->getDoctrine()->getRepository('AppBundle:Cohorte');
                $Inscription_cohRepo = $this->getDoctrine()->getRepository('AppBundle:Inscription_coh');
                $cohortes = $cohRepo->findAll();
                foreach ($userIds as $userId) {
                    $userTab['userid'] = $userId;
                    $userTab['cohortes'] = [];
                    /* @var User $user */
                    $user = $repoUser->findOneBy(array('id' => $userId));
                    /* @var $cohorte Cohorte */
                    foreach ($cohortes as $cohorte) {
                        $coh = $cohRepo->findOneBy(array('id'=> $cohorte->getId()));
                        $inscrs = $Inscription_cohRepo->findBy(array('cohorte' => $coh, 'user' => $user));

                        if ($inscrs) {
                            /* @var Inscription_coh $inscr */
                            foreach ($inscrs as $inscr) {
                                array_push($userTab['cohortes'], array('nom' => $coh->getNom(), 'id' => $coh->getId(), 'role' => $inscr->getRole()->getNom()));
                            }
                        }
                    }
                    array_push($users, $userTab);
                }
            }

            $em->flush();

            return new JsonResponse(array('action' => 'ajoute les cohortes au tableau des users', 'users' => $users));
        }

        return new JsonResponse('This is not ajax!', 400);
    }


    /**
     * @Route("/notifsManag", name="notifsManag")
     */
    public function notifsManagAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $dateLimit = new \DateTime();
        $dateLimit->setTimestamp($this->getParameter('dateLimiNotifs'));

        $documents = $this->getDoctrine()->getRepository('AppBundle:Document')->findBy(array('preuveEnvoiNotif' => null));
        $docs = [];
        if ($documents) {
            /* @var $doc Document */
            foreach ($documents as $doc) {
                if ($doc->getDateCrea() > $dateLimit) {
                    array_push($docs, $doc);
                }
            }
        }

        return $this->render('user/notifsFrontEnd.html.twig', [
            'documents' => $docs
        ]);
    }

    /**
     * @Route("/reactiveAll", name="reactiveAll")
     */
    public function usersReactivationAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $em->getRepository('AppBundle:User')->findBy(array('enabled' => false));

        $arrayUsers = [];

        if ($users) {
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
     * @Route("/activateSpecialStatus", name="activateSpecialStatus")
     */
    public function activateSpecialStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $cohRepo = $this->getDoctrine()->getRepository('AppBundle:Cohorte');
        $cohortes = $cohRepo->findAll();
        $myUsers = [];
        /* @var $user User */
        foreach ($this->getUsersToActivate() as $user) {
            if ($user->isEnabled()) {
                $myCohortes = [];
                if ($cohortes) {
                    /* @var $cohorte Cohorte */
                    foreach ($cohortes as $cohorte) {
                        if ($cohRepo->userHasAccessOrIsInscrit($user, $cohorte)) {
                            array_push($myCohortes, $cohorte);
                        }
                    }
                }
                array_push($myUsers, ['user' => $user, 'cohortes' => $myCohortes]);
            } else {
                array_push($myUsers, ['user' => $user, 'cohortes' => []]);
            }

        }

        return $this->render('user/activateSpecialStatus.html.twig', [
            'myUsers' => $myUsers
        ]);
    }

    /**
     * @Route("/sendNotifs_ajax", name="sendNotifs_ajax")
     */
    public function sendNotifsAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $this->get('notifsSender')->submit();

            $em->flush();

            return new JsonResponse(array('action' => 'Send notifications'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/usersDesactivation/{year}/{month}/{day}", name="usersDesactivation")
     */
    public function usersDesactivationAction(Request $request, $year, $month, $day)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $em->getRepository('AppBundle:User')->findBy(array('enabled' => true));

        $arrayUsers = [];

        if ($users) {
            $repoInscription_coh = $em->getRepository('AppBundle:Inscription_coh');
            $repoInscription_d = $em->getRepository('AppBundle:Inscription_d');
            $repoInscription_c = $em->getRepository('AppBundle:Inscription_c');

            $d1 = new DateTime($year . '-' . $month . '-' . $day . ' 00:00:00');
            /* @var $user User */
            foreach ($users as $user) {
                $isEns = false;

                $inscrs = $repoInscription_coh->findBy(array('user' => $user));
                if ($inscrs) {
                    /* @var $inscr Inscription_coh */
                    foreach ($inscrs as $inscr) {
                        if ($inscr->getRole() == 'Enseignant') {
                            $isEns = true;
                            break;
                        }
                    }
                }
                $inscrs = $repoInscription_d->findBy(array('user' => $user));
                if ($inscrs) {
                    /* @var $inscr Inscription_d */
                    foreach ($inscrs as $inscr) {
                        if ($inscr->getRole() == 'Enseignant') {
                            $isEns = true;
                            break;
                        }
                    }
                }
                $inscrs = $repoInscription_c->findBy(array('user' => $user));
                if ($inscrs) {
                    /* @var $inscr Inscription_c */
                    foreach ($inscrs as $inscr) {
                        if ($inscr->getRole() == 'Enseignant') {
                            $isEns = true;
                            break;
                        }
                    }
                }

                if (!$isEns && $this->getUser()->getId() != $user->getId() && $user->getCreatedAt() < $d1) {
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
    public function userAction(Request $request, $id)
    {
        /* @var $user User */
        $user = $this->getUser();
        if ((($user->getStatut() !== 'Responsable' && $user->getStatut() !== 'Formateur') || !$user->getConfirmedByAdmin()) && !$this->getUser()->hasRole('ROLE_SUPER_ADMIN')) {
            return $this->redirectToRoute('homepage');
        }

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);
        $roles = $this->getDoctrine()->getRepository('AppBundle:Role')->findAll();

        /* @var $coh_repo CohorteRepository */
        $coh_repo = $this->getDoctrine()->getRepository('AppBundle:Cohorte');
        /* @var $disc_repo DisciplineRepository */
        $disc_repo = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        /* @var $cours_repo CoursRepository */
        $cours_repo = $this->getDoctrine()->getRepository('AppBundle:Cours');
        /* @var $copie_repo CopieRepository */
        $copie_repo = $this->getDoctrine()->getRepository('AppBundle:Copie');

        $cohortes = $coh_repo->findBy(array(), array('nom' => 'ASC'));
        $discs = $disc_repo->findBy(array(), array('nom' => 'ASC'));
        $cours = $cours_repo->findBy(array(), array('nom' => 'ASC'));
        $copies = $copie_repo->findBy(array('auteur' => $user), array('id' => 'ASC'));

        $myCopies = array();
        if ($copies) {
            $repoCopieFichier = $this->getDoctrine()->getRepository('AppBundle:CopieFichier');

            foreach ($copies as $copie) {
                $fichier = $repoCopieFichier->findOneBy(array('copie' => $copie));
                if ($fichier) {
                    array_push($myCopies, ['copie' => $copie, 'fichier' => $fichier]);
                }
            }
        }
        $allcourses = $cours_repo->findAll();
        $sessions_tab = array();
        $sessions_tabTest = array();
        $repoSession = $this->getDoctrine()->getRepository('AppBundle:Session');
        foreach ($allcourses as $coursFiltre) {
            if ($coursFiltre->getSession() != null && $cours_repo->userHasAccess($user, $coursFiltre)) {
                $sess = $coursFiltre->getSession();
                $cours_tabTest = array();
                if (!in_array($sess, $sessions_tabTest)) {
                    foreach ($allcourses as $coursCheckDisc) {
                        if ($coursCheckDisc->getSession() == $sess && $cours_repo->userHasAccess($user, $coursCheckDisc)) {
                            if (!in_array($coursCheckDisc, $cours_tabTest)) {
                                array_push($cours_tabTest, $coursCheckDisc);
                            }
                        }

                    }

                    array_push($sessions_tabTest, $sess);
                    $isInscrit = $repoSession->userIsInscrit($user, $sess);
                    array_push($sessions_tab, array(
                        'session' => $sess,
                        'isInscrit' => $isInscrit,
                        'sessionsForUser' => $cours_tabTest
                    ));
                }
            }
        }

        $cohortes_inscr = array();
        if ($cohortes) {
            foreach ($cohortes as $cohorte) {
                array_push($cohortes_inscr, [
                    'cohorte' => $cohorte,
                    'isInscrit' => $coh_repo->userIsInscrit($user, $cohorte),
                    'inscription' => $coh_repo->getUserInscr($user, $cohorte)
                ]);
            }
        }
        $discs_inscr = array();
        if ($discs) {
            foreach ($discs as $disc) {
                array_push($discs_inscr, [
                    'discipline' => $disc,
                    'isInscrit' => $disc_repo->userIsInscrit($user, $disc),
                    'hasAccess' => $disc_repo->userHasAccess($user, $disc),
                    'cohortes' => $disc->getCohortes(),
                    'inscription' => $disc_repo->getUserInscr($user, $cohorte)
                ]);
            }
        }

        $cours_inscr = array();
        if ($cours) {
            foreach ($cours as $cour) {

                array_push($cours_inscr, [
                    'cours' => $cour,
                    'isInscrit' => $cours_repo->userIsInscrit($user, $cour),
                    'hasAccess' => $cours_repo->userHasAccess($user, $cour),
                    'cohortes' => $cour->getCohortes(),
                    'inscription' => $cours_repo->getUserInscr($user, $cohorte)
                ]);
            }
        }

        $form = $this->createFormBuilder($user)
            ->add('lastname', TextType::class, array(
                'label' => 'Nom'
            ))
            ->add('firstname', TextType::class, array(
                'label' => 'Prénom'
            ))
            ->add('email', EmailType::class, array())
            ->add('receiveAutoNotifs', CheckboxType::class, array(
                'label' => 'Recevoir les notifications ',
                'required' => false
            ))
            ->add('institut', EntityType::class, array(
                'class' => 'AppBundle:Institut',
                'choice_label' => 'nom',
                'multiple' => false
            ))
            ->add('enabled', CheckboxType::class, array(
                'label' => 'Activé'
            ))
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }
        /* @var UserStatRessourceRepository $userStatRessRepo */
        $userStatRessRepo = $this->getDoctrine()->getRepository('AppBundle:UserStatRessource');

        /* @var UserStatCoursRepository $userStatCoursRepo */
        $userStatCoursRepo = $this->getDoctrine()->getRepository('AppBundle:UserStatCours');

        /* @var ArrayCollection $userStatLogin */
        $userStatLogin = $this->getDoctrine()->getRepository('AppBundle:UserStatLogin')->findBy(array('user' => $user));

        /* @var ArrayCollection $documents */
        $documents = $this->getDoctrine()->getRepository('AppBundle:Document')->findBy(array('proprietaire' => $user));

        /* @var ArrayCollection $sujets */
        $sujets = $this->getDoctrine()->getRepository('AppBundle:ForumSujet')->findBy(array('createur' => $user));

        /* @var ArrayCollection $posts */
        $posts = $this->getDoctrine()->getRepository('AppBundle:ForumPost')->findBy(array('auteur' => $user));

        return $this->render('user/one.html.twig', [
            'user' => $user,
            'roles' => $roles,
            'cohortesInsc' => $cohortes_inscr,
            'disciplinesInsc' => $discs_inscr,
            'coursInsc' => $cours_inscr,
            'form' => $form->createView(),
            'sessions' => $sessions_tab,
            'copies' => $myCopies,
            'userStatRess' => $userStatRessRepo->findbyUser($user),
            'userStatCours' => $userStatCoursRepo->findbyUser($user),
            'userStatLogin' => $userStatLogin,
            'documents' => $documents,
            'sujets' => $sujets,
            'posts' => $posts

        ]);
    }

    /**
     * @Route("/itemUsers/{id}/type/{type}", name="itemUsers")
     */
    public function itemUsersAction(Request $request, $id, $type)
    {
        /* @var $currentUser User */
        $currentUser = $this->getUser();
        $statut = $currentUser->getStatut();

        $doctrine = $this->getDoctrine();

        $roles = $doctrine->getRepository('AppBundle:Role')->findAll();

        $entityName = "";
        if ($type == "cohorte") {
            $entityName = "Cohorte";
        } else if ($type == "discipline") {
            $entityName = "Discipline";
        } else if ($type == "cours") {
            $entityName = "Cours";
        } else if ($type == "session") {
            $entityName = "Session";
        }


        $inscrCohRepo = $doctrine->getRepository('AppBundle:Inscription_coh');

        $itemRepo = $doctrine->getRepository('AppBundle:' . $entityName);
        $item = $doctrine->getRepository('AppBundle:' . $entityName)->findOneBy(array('id' => $id));

        $roleUser = 'admin';
        if(!$currentUser->hasRole('ROLE_SUPER_ADMIN')){
            $roleUser = $itemRepo->getRole($currentUser, $item)->getNom();
        }

        $userRepo = $doctrine->getRepository('AppBundle:User');
        $users = $userRepo->findBy(array('enabled' => true));
        $inscritsItem = [];
        $havingAccessItem =[];
        if ($type == "cours") {
           $havingAccessItem = $itemRepo->findInscrits($item);
        }
        if ($type == "cours" || $type == 'discipline') {
            $inscritsItem = $itemRepo->getUsersInscr($item);
        }elseif ($type == "cohorte"){
            $inscritsItem = $itemRepo->getUsersInscr($item);
        }elseif ($type == "session"){
            $inscritsItem = $itemRepo->getUsersInscr($item);
        }


        $usersNoAccessTab = array();
        $usersAccessTab = array();
        $usersNoAccessTab_tampon = array();
        $usersAccessTab_tampon = array();
        $limitNbTwigRows2Load = 0;

        if ((($statut !== 'Responsable' && $statut !== 'Formateur') || !$currentUser->getConfirmedByAdmin()) && !$currentUser->hasRole('ROLE_SUPER_ADMIN') && $roleUser!='Referent') {
            return $this->redirectToRoute('homepage');
        }

        foreach ($users as $user) {
            $myCohs = $inscrCohRepo->findBy(array('user' => $user));
            //$myCohs = [];
            if($type == "cohorte"){
                if(in_array($user, $inscritsItem)){
                    $role = $itemRepo->getRole($user, $item);
                    //$role = null;
                    if(count($usersAccessTab)<$limitNbTwigRows2Load) {
                        array_push($usersAccessTab, [
                            "user" => $user,
                            "isInscrit" => true,
                            "myCohs" => $myCohs,
                            "role" => $role
                        ]);
                    }else{
                        array_push($usersAccessTab_tampon, [
                            "user" => $user,
                            "isInscrit" => true,
                            "myCohs" => $myCohs,
                            "role" => $role
                        ]);
                    }
                } else {
                    if(count($usersNoAccessTab)<$limitNbTwigRows2Load) {
                        array_push($usersNoAccessTab, [
                            'user' => $user,
                            "myCohs" => $myCohs
                        ]);
                    }else{
                        array_push($usersNoAccessTab_tampon, [
                            'user' => $user,
                            "myCohs" => $myCohs
                        ]);
                    }
                }
            }else if ($type == "discipline") {
                $checkAccess = false;
                $inscr = null;
                $inscrD = null;
                if($myCohs){
                    foreach($myCohs as $inscrCoh){
                        $coh = $inscrCoh->getCohorte();
                        if($coh->getDisciplines()->contains($item)){
                            $checkAccess = true;
                            $inscr = $inscrCoh;
                            break;
                        }
                    }
                }
                if(!$checkAccess){
                    if(in_array($user, $inscritsItem)){
                        $checkAccess = true;
                        $inscrD = true;
                    }
                }
                if ($checkAccess) {
                    $role = null;
                    if($inscr){
                        $role = $inscr->getRole();
                    }elseif($inscrD) {
                        $role = $itemRepo->getRole($user, $item);
                    }else{
                        if($myCohs){
                            foreach($myCohs as $inscrCoh){
                                $coh = $inscrCoh->getCohorte();
                                if($coh->getDisciplines()->contains($item)){
                                    $role = $inscrCoh->getRole();
                                }
                            }
                        }
                    }
                    if(count($usersAccessTab)<$limitNbTwigRows2Load) {
                        array_push($usersAccessTab, [
                            "user" => $user,
                            "isInscrit" => $inscrD != null,
                            "myCohs" => $myCohs,
                            "role" => $role
                        ]);
                    }else{
                        array_push($usersAccessTab_tampon, [
                            "user" => $user,
                            "isInscrit" => $inscrD != null,
                            "myCohs" => $myCohs,
                            "role" => $role
                        ]);
                    }
                } else {
                    if(count($usersNoAccessTab)<$limitNbTwigRows2Load) {
                        array_push($usersNoAccessTab, [
                            'user' => $user,
                            "myCohs" => $myCohs
                        ]);
                    }else{
                        array_push($usersNoAccessTab_tampon, [
                            'user' => $user,
                            "myCohs" => $myCohs
                        ]);
                    }
                }
            }else {
                $isInscrit = true;
                $hasAccessItem = true;
                if(!in_array($user, $inscritsItem)) {
                    $isInscrit = false;
                    if(!in_array($user, $havingAccessItem)) {
                        $hasAccessItem = false;
                    }
                }
                $dateInscr = null;
                if($type == "session"){
                    $dateInscr = $itemRepo->getDateInscr($user, $item);
                }

                if ($isInscrit) {
                    $role = $itemRepo->getRole($user, $item);
                    if(count($usersAccessTab)<$limitNbTwigRows2Load){
                        array_push($usersAccessTab, [
                            "user" => $user,
                            "isInscrit" => $isInscrit,
                            "myCohs" => $myCohs,
                            "role" => $role,
                            "dateInscr" => $dateInscr
                        ]);
                    }else{
                        array_push($usersAccessTab_tampon, [
                            "user" => $user,
                            "isInscrit" => $isInscrit,
                            "myCohs" => $myCohs,
                            "role" => $role,
                            "dateInscr" => $dateInscr
                        ]);
                    }

                } else {
                    if ($hasAccessItem) {
                        $role = $itemRepo->getRoleNoInscr($user, $item);
                        if(count($usersAccessTab)<$limitNbTwigRows2Load) {
                            array_push($usersAccessTab, [
                                "user" => $user,
                                "isInscrit" => $isInscrit,
                                "myCohs" => $myCohs,
                                "role" => $role,
                                "dateInscr" => $dateInscr
                            ]);
                        }else{
                            array_push($usersAccessTab_tampon, [
                                "user" => $user,
                                "isInscrit" => $isInscrit,
                                "myCohs" => $myCohs,
                                "role" => $role,
                                "dateInscr" => $dateInscr
                            ]);
                        }
                    } else {
                        if(count($usersNoAccessTab)<$limitNbTwigRows2Load) {
                            array_push($usersNoAccessTab, [
                                'user' => $user,
                                "myCohs" => $myCohs
                            ]);
                        }else{
                            array_push($usersNoAccessTab_tampon, [
                                'user' => $user,
                                "myCohs" => $myCohs
                            ]);
                        }
                    }
                }
                $isInscrit = null;
                unset($isInscrit);
                $hasAccessItem = null;
                unset($hasAccessItem);
            }
        }
        $repoUserStatRessource = $doctrine->getRepository('AppBundle:UserStatRessource');
        $ressources = new ArrayCollection();
        $linkedCourses = new ArrayCollection();
        if ($type == "cohorte") {
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

            /* @var $cohs CohorteRepository */
            $linkedCourses = $itemRepo->getLinkedCourses($item);

        } else if ($type == "discipline") {
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

            $linkedCourses_arr = $doctrine->getRepository('AppBundle:Cours')->findBy(array('discipline' => $item));
            if ($linkedCourses_arr) {
                foreach ($linkedCourses_arr as $linkedCourses_arrOne) {
                    $linkedCourses->add($linkedCourses_arrOne);
                }
            }
        } else if ($type == "cours") {
            $form = $this->createFormBuilder($item)
                ->add('nom', TextType::class, array(
                    'label' => 'Nom'
                ))
                ->add('imageFile', FileType::class, [
                    'label' => 'Image',
                    'attr' => ['accept' => 'image/*'],
                    'required' => false,
                    'multiple' => false,
                ])
                ->add('position', TextType::class, array(
                    'label' => 'Position'
                ))
                ->add('enabled', CheckboxType::class, array(
                    'label' => 'Visible',
                    'required' => false
                ))
                ->add('discipline', EntityType::class, array(
                    'class' => 'AppBundle:Discipline',
                    'choice_label' => 'Nom',
                    'multiple' => false,
                ))
                ->add('description', CKEditorType::class, array(
                    'label' => 'Description'
                ))
                ->add('accueil', CKEditorType::class, array(
                    'label' => 'Accueil'
                ))
                ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
                ->getForm();

            $c_ressources = $doctrine->getRepository('AppBundle:Ressource')->findBy(array('cours' => $item));
            if ($c_ressources) {
                foreach ($c_ressources as $c_ressource) {
                    $ressStats = $repoUserStatRessource->findBy(array('ressource' => $c_ressource));

                    $ressources->add(["ressource" => $c_ressource, "stats" => $ressStats]);
                }
            }
        } else if ($type == "session") {
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
            $c_ressources = $doctrine->getRepository('AppBundle:Ressource')->findBy(array('cours' => $item));
            if ($c_ressources) {
                foreach ($c_ressources as $c_ressource) {
                    $ressStats = $repoUserStatRessource->findBy(array('ressource' => $c_ressource));

                    $ressources->add(["ressource" => $c_ressource, "stats" => $ressStats]);
                }
            }
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $itemForm = $form->getData();
            $em = $doctrine->getManager();
            if ($type == "cours") {
                if ($form['imageFile']->getData()) {
                    $itemForm->setImageFile($form['imageFile']->getData());
                    $itemForm->upload();
                }
            }else{

            }
            $em->persist($itemForm);
            $em->flush();


        }
        return $this->render('user/itemUsers.html.twig', [
            'item' => $item,
            'ressources' => $ressources,
            'linkedCourses' => $linkedCourses,
            'roles' => $roles,
            'entityName' => $entityName,
            'usersNoHavingAccess' => $usersNoAccessTab,
            'usersHavingAccess' => $usersAccessTab,
            'usersNoHavingAccessTampon' => $usersNoAccessTab_tampon,
            'usersHavingAccessTampon' => $usersAccessTab_tampon,
            'form' => $form->createView(),
            'roleUser' => $roleUser
        ]);
    }

    /**
     * @Route("/updateSectionAccesCondition_ajax", name="updateSectionAccesCondition_ajax")
     */
    public function updateSectionAccesConditionAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isAccesConditionne = $request->request->get('isAccesConditionne') == "true";
            /* @var Section $section */
            $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $id));
            $section->setIsAccesConditionne($isAccesConditionne);

            $em->flush();

            return new JsonResponse(array(
                'action' => 'change isAccesConditionne of section'
            ));
        }
    }

    /**
     * @Route("/sectionAutorizeUsers_ajax", name="sectionAutorizeUsers_ajax")
     */
    public function sectionAutorizeUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $userIds = $request->request->get('users');
            /* @var Section $section */
            $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $id));
            if(count($userIds) > 0){
                foreach ($userIds as $userId) {
                    /* @var User $user */
                    $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
                    $section->addautorizedUser($user);
                }
            }


            $em->flush();

            return new JsonResponse(array(
                'action' => 'change isAccesConditionne of section'
            ));
        }
    }

    /**
     * @Route("/sectionUnAutorizeUsers_ajax", name="sectionUnAutorizeUsers_ajax")
     */
    public function sectionUnAutorizeUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $userIds = $request->request->get('users');
            /* @var Section $section */
            $section = $em->getRepository('AppBundle:Section')->findOneBy(array('id' => $id));
            if(count($userIds) > 0){
                foreach ($userIds as $userId) {
                    /* @var User $user */
                    $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
                    $section->removeAutorizedUser($user);
                }
            }

            $em->flush();

            return new JsonResponse(array(
                'action' => 'change isAccesConditionne of section'
            ));
        }
    }

    /**
     * @Route("/changeRoleUsers_ajax", name="changeRoleUsers_ajax")
     */
    public function changeRoleUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $itemId = $request->request->get('idItem');
            $itemType = $request->request->get('typeItem');
            $userIds = $request->request->get('idUsers');
            $roleId = $request->request->get('idRole');
            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('id' => $roleId));
            $EntityName = '';
            $EntityInscrName = '';

            $repoUser = $em->getRepository('AppBundle:User');

            foreach ($userIds as $userId) {
                $user = $repoUser->findOneBy(array('id' => $userId));

                if ($itemType == 'cohorte') {
                    $EntityName = 'Cohorte';
                    $EntityInscrName = 'Inscription_coh';
                } else if ($itemType == 'discipline') {
                    $EntityName = 'Discipline';
                    $EntityInscrName = 'Inscription_d';
                } else if ($itemType == 'cours') {
                    $EntityName = 'Cours';
                    $EntityInscrName = 'Inscription_c';
                }
                $item = $em->getRepository('AppBundle:' . $EntityName)->findOneBy(array('id' => $itemId));
                /* @var $inscr Inscription_coh */
                $inscr = $em->getRepository('AppBundle:' . $EntityInscrName)->findOneBy(array($itemType => $item, 'user' => $user));
                $inscr->setRole($role);
            }
            $em->flush();

            return new JsonResponse(array(
                'action' => 'change inscription of users'
            ));
        }
    }

    /**
     * @Route("/inscrireUsers_ajax", name="inscrireUsers_ajax")
     */
    public function inscrireUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $itemId = $request->request->get('idItem');
            $itemType = $request->request->get('typeItem');
            $userIds = $request->request->get('idUsers');
            $roleId = $request->request->get('idRole');
            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('id' => $roleId));

            $repoUser = $em->getRepository('AppBundle:User');
            $repoCohorte = $em->getRepository('AppBundle:Cohorte');
            $repoInscription_d = $em->getRepository('AppBundle:Inscription_d');
            $repoInscription_c = $em->getRepository('AppBundle:Inscription_c');
            $repoDiscipline = $em->getRepository('AppBundle:Discipline');
            $repoCours = $em->getRepository('AppBundle:Cours');
            foreach ($userIds as $userId) {
                $user = $repoUser->findOneBy(array('id' => $userId));

                if ($itemType == 'cohorte') {
                    // commence par désinscrire le user des disciplines et des cours qui en découlent
                    $cohorte = $repoCohorte->findOneBy(array('id' => $itemId));
                    foreach ($cohorte->getDisciplines() as $disc) {

                        // d'abords les disciplines associées à la cohorte
                        $inscriptionsDs = $repoInscription_d->findBy(array(
                            'discipline' => $disc,
                            'user' => $user
                        ));
                        if ($inscriptionsDs) {
                            foreach ($inscriptionsDs as $inscr) {
                                $em->remove($inscr);
                            }
                        }
                        // puis les cours dont la discipline est associée
                    }

                    // puis on supprime les inscriptions au cours associés à la cohorte
                    foreach ($cohorte->getCours() as $co) {
                        $inscriptionsCs = $repoInscription_c->findBy(array(
                            'cours' => $co,
                            'user' => $user
                        ));
                        if ($inscriptionsCs) {

                            foreach ($inscriptionsCs as $inscr) {
                                $em->remove($inscr);
                            }
                        }
                    }

                    // puis on supprime les inscriptions au cours dont la discipline est associée à la cohorte
                    $inscriptionsCs = $repoInscription_c->findBy(array(
                        'user' => $user
                    ));
                    if ($inscriptionsCs) {
                        foreach ($inscriptionsCs as $inscr) {
                            if ($cohorte->getDisciplines()->contains($inscr->getCours()->getDiscipline())) {
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

                } else if ($itemType == 'discipline') {
                    $discipline = $repoDiscipline->findOneBy(array('id' => $itemId));

                    // on supprime les inscriptions au cours dont c'est la discipline
                    $cours = $repoCours->findBy(array(
                        'discipline' => $discipline
                    ));
                    if ($cours) {
                        foreach ($cours as $co) {
                            $inscriptionsC = $repoInscription_c->findBy(array(
                                'cours' => $co,
                                'user' => $user
                            ));
                            if ($inscriptionsC) {
                                foreach ($inscriptionsC as $inscr) {
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
                } else if ($itemType == 'cours') {
                    $cours = $repoCours->findOneBy(array('id' => $itemId));

                    $new_inscr = new Inscription_c();
                    $new_inscr->setUser($user);
                    $new_inscr->setCours($cours);
                    $new_inscr->setDateInscription(new DateTime());
                    $new_inscr->setRole($role);
                    $em->persist($new_inscr);
                }
            }
            $em->flush();

            return new JsonResponse(array(
                'action' => 'change inscription of users'
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/desInscrireUsers_ajax", name="desInscrireUsers_ajax")
     */
    public function desInscrireUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $itemId = $request->request->get('idItem');
            $itemType = $request->request->get('typeItem');
            $userIds = $request->request->get('idUsers');

            $repoUser = $em->getRepository('AppBundle:User');
            $repoCohorte = $em->getRepository('AppBundle:Cohorte');
            $repoDiscipline = $em->getRepository('AppBundle:Discipline');
            $repoCours = $em->getRepository('AppBundle:Cours');

            foreach ($userIds as $userId) {
                $user = $repoUser->findOneBy(array('id' => $userId));

                $item = null;
                $inscrEntityName = "";

                if ($itemType == 'cohorte') {
                    $item = $repoCohorte->findOneBy(array('id' => $itemId));
                    $inscrEntityName = "Inscription_coh";

                } else if ($itemType == 'discipline') {
                    $item = $repoDiscipline->findOneBy(array('id' => $itemId));
                    $inscrEntityName = "Inscription_d";
                } else if ($itemType == 'cours') {
                    $item = $repoCours->findOneBy(array('id' => $itemId));
                    $inscrEntityName = "Inscription_c";
                }
                $inscriptions = $em->getRepository('AppBundle:' . $inscrEntityName)->findBy(array(
                    $itemType => $item,
                    'user' => $user
                ));
                if ($inscriptions) {
                    foreach ($inscriptions as $inscr) {
                        $em->remove($inscr);
                    }
                }
            }

            $em->flush();

            return new JsonResponse(array(
                'action' => 'change inscription of users'
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/inscrSessUsers_ajax", name="inscrSessUsers_ajax")
     */
    public function inscrSessUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');
            $roleId = $request->request->get('idRole');
            $userIds = $request->request->get('idUsers');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));
            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('id' => $roleId));
            $repoUser = $em->getRepository('AppBundle:User');
            foreach ($userIds as $userId) {
                $user = $repoUser->findOneBy(array('id' => $userId));

                $inscr = new Inscription_sess();
                $inscr->setSession($session);
                $inscr->setUser($user);
                $inscr->setDateInscription(new DateTime());
                if ($role) {
                    $inscr->setRole($role);
                }

                $em->persist($inscr);
            }

            $em->flush();

            return new JsonResponse(array('action' => 'Inscription user session'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/desinscrSessUsers_ajax", name="desinscrSessUsers_ajax")
     */
    public function desinscrSessUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');
            $userIds = $request->request->get('idUsers');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));
            $repoUser = $em->getRepository('AppBundle:User');
            $repoInscription_sess = $em->getRepository('AppBundle:Inscription_sess');
            foreach ($userIds as $userId) {
                $user = $repoUser->findOneBy(array('id' => $userId));
                $inscr = $repoInscription_sess->findOneBy(array('session' => $session, 'user' => $user));
                $em->remove($inscr);
            }

            $em->flush();

            return new JsonResponse(array('action' => 'Inscription user session'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/getUsersToActivate_ajax", name="getUsersToActivate_ajax")
     */
    public function getUsersToActivateAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {

            return new JsonResponse(array(
                'action' => 'Inscription user session',
                'users' => $this->getUsersToActivate()
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/confirmUser_ajax", name="confirmUser_ajax")
     */
    public function confirmUserAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();
            $isConfirmed = $request->request->get('isConfirmed') == "true";
            $userId = $request->request->get('idUser');

            /* @var User $user */
            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
            $this->sendMailConfirmStatut($user, $isConfirmed);
            if (!$isConfirmed) {
                $user->setStatut('Etudiant');
            }
            $user->setConfirmedByAdmin(true);
            $em->flush();
            return new JsonResponse(array(
                'action' => 'Confirmation de statuts utilisateurs',
                'isConfirmed' => $isConfirmed
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/confirmAllUsers_ajax", name="confirmAllUsers_ajax")
     */
    public function confirmAllUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $users = $this->getUsersToActivate();
            foreach ($users as $user) {
                $user->setConfirmedByAdmin(true);
                $this->sendMailConfirmStatut($user, true);
            }

            $em->flush();
            return new JsonResponse(array(
                'action' => 'Confirmation de tous les statuts utilisateurs'
            ));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    public function sendMailConfirmStatut(User $user, $isConfirm)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('[AFADEC] Confirmation de votre statut')
            ->setFrom('noreply@afadec.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'user/confirmStatutMail.html.twig',
                    array(
                        'user' => $user,
                        'isConfirm' => $isConfirm
                    )
                ),
                'text/html'
            )
            ->addPart(
                $this->renderView(
                    'user/confirmStatutMail.html.twig',
                    array(
                        'user' => $user,
                        'isConfirm' => $isConfirm
                    )
                ),
                'text/html'
            );
        $this->get('mailer')->send($message);
    }

    public function getUsersToActivate()
    {
        $em = $this->getDoctrine()->getEntityManager();

        /* @var ArrayCollection $users */
        $users = $em->getRepository('AppBundle:User')->findBy(array('confirmedByAdmin' => false));
        $dateLimit = new \DateTime();
        $dateLimit->setTimestamp($this->getParameter('dateLimiNotifs'));
        $usersToSend = [];
        /* @var User $user */
        foreach ($users as $user) {
            $statut = $user->getStatut();
            if ($user->getCreatedAt() > $dateLimit && ($statut == 'Responsable' || $statut == 'Formateur')) {
                array_push($usersToSend, $user);
            }
        }
        return $usersToSend;
    }

    /**
     * @Route("/deleteUserDatas_ajax", name="deleteUserDatas_ajax")
     */
    public function deleteUserDatasAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $typeDatas = $request->request->get('typeDatas');
            $userId = $request->request->get('idUser');

            $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

            if ($typeDatas == 'Cours' || $typeDatas == 'Ressource' || $typeDatas == 'Login') {
                $stats = $em->getRepository('AppBundle:UserStat' . $typeDatas)->findBy(array('user' => $user));
                foreach ($stats as $stat) {
                    $em->remove($stat);
                }
            } elseif ($typeDatas == 'Document') {
                $docs = $em->getRepository('AppBundle:Document')->findBy(array('proprietaire' => $user));
                /* @var Document $doc */
                foreach ($docs as $doc) {
                    $doc->setProprietaire(null);
                }
            } elseif ($typeDatas == 'ForumSujet') {
                $sujets = $em->getRepository('AppBundle:ForumSujet')->findBy(array('createur' => $user));
                /* @var ForumSujet $sujet */
                foreach ($sujets as $sujet) {
                    $sujet->setCreateur(null);
                }
            } elseif ($typeDatas == 'ForumPost') {
                $posts = $em->getRepository('AppBundle:ForumPost')->findBy(array('auteur' => $user));
                /* @var ForumPost $post */
                foreach ($posts as $post) {
                    $post->setAuteur(null);
                }
            } elseif ($typeDatas == 'ALL') {
                $stats = $em->getRepository('AppBundle:UserStatLogin')->findBy(array('user' => $user));
                foreach ($stats as $stat) {
                    $em->remove($stat);
                }

                $stats = $em->getRepository('AppBundle:UserStatRessource')->findBy(array('user' => $user));
                foreach ($stats as $stat) {
                    $em->remove($stat);
                }

                $stats = $em->getRepository('AppBundle:UserStatCours')->findBy(array('user' => $user));
                foreach ($stats as $stat) {
                    $em->remove($stat);
                }

                $docs = $em->getRepository('AppBundle:Document')->findBy(array('proprietaire' => $user));
                /* @var Document $doc */
                foreach ($docs as $doc) {
                    $doc->setProprietaire(null);
                }

                $sujets = $em->getRepository('AppBundle:ForumSujet')->findBy(array('createur' => $user));
                /* @var ForumSujet $sujet */
                foreach ($sujets as $sujet) {
                    $sujet->setCreateur(null);
                }
            }

            $em->flush();

            return new JsonResponse(array('action' => 'Delete RGPD datas'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/actionLotsUsers_ajax", name="actionLotsUsers_ajax")
     */
    public function actionLotsUsersAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
            $em = $this->getDoctrine()->getEntityManager();

            $userIds = $request->request->get('userIds');
            $mode = $request->request->get('mode');
            $repoUser = $em->getRepository('AppBundle:User');
            if ($userIds) {
                foreach ($userIds as $userId) {
                    /* @var User $user */
                    $user = $repoUser->findOneBy(array('id' => $userId));
                    if ($mode == 'delete') {
                        $em->remove($user);
                    } elseif ($mode == "desact") {
                        $user->setEnabled(false);
                    } elseif ($mode == "react") {
                        $user->setEnabled(true);
                    }
                }
            }

            $em->flush();

            return new JsonResponse(array('action' => 'desactive List of Users'));
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

    /**
     * @Route("/userTreatments", name="userTreatments")
     */
    public function userTreatmentsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var $roleFormateur Role */
        //$roleFormateur = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Formateur'));

        // passe tous les roles Enseignants en Formateur
        /*$this->convertRoles("Enseignant", "Inscription_c", $roleFormateur);
        $this->convertRoles("Enseignant", "Inscription_coh", $roleFormateur);
        $this->convertRoles("Enseignant", "Inscription_d", $roleFormateur);
        $this->convertRoles("Enseignant", "Inscription_sess", $roleFormateur);*/

        /*$users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();
        $this->giveStatut2Role1($users, "Formateur", "Formateur");
        $this->giveStatut2Role1($users, "Stagiaire", "Prof_stagiaire");*/

        return $this->render('index.html.twig');
    }

    public function convertRoles($roleName, $entityInscrName, Role $roleDest)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $inscrs = $this->getDoctrine()->getRepository('AppBundle:' . $entityInscrName)->findAll();
        $myInscrs = array();
        /* @var $inscr Inscription */
        foreach ($inscrs as $inscr) {
            if ($inscr->getRole()->getNom() == $roleName) {
                $inscr->setRole($roleDest);
                array_push($myInscrs, ['user' => $inscr->getUser()->getEmail(), 'role' => $inscr->getRole(), 'id' => $inscr->getId()]);
            }
        }
        $em->flush();
    }

    public function giveStatut2Role1($users, $roleName, $statutDestName)
    {
        /* @var $user User */
        foreach ($users as $key => $user) {
            if ($key >= 2100) {
                $hasChanged = $this->giveStatut2Role2($roleName, $user, $statutDestName, "Inscription_c");
                if (!$hasChanged) {
                    $hasChanged = $this->giveStatut2Role2($roleName, $user, $statutDestName, "Inscription_d");
                }
                if (!$hasChanged) {
                    $hasChanged = $this->giveStatut2Role2($roleName, $user, $statutDestName, "Inscription_coh");
                }
                if (!$hasChanged) {
                    $hasChanged = $this->giveStatut2Role2($roleName, $user, $statutDestName, "Inscription_sess");
                }
            }
        }
    }

    public function giveStatut2Role2($roleName, User $user, $statutDestName, $entityInscrName)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $hasChanged = false;

        /* @var $role Role */
        $role = $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy(array('nom' => $roleName));

        $inscrs = $this->getDoctrine()->getRepository('AppBundle:' . $entityInscrName)->findBy(array('user' => $user, 'role' => $role));

        /* @var $inscr Inscription */
        if ($inscrs) {
            $user->setStatut($statutDestName);
            $hasChanged = true;
        }
        $em->flush();

        return $hasChanged;
    }

    /**
     * @Route("/groupesResas/{id}", name="groupesResas")
     */
    public function groupesResasAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        /* @var $system SystemeResa */
        $system = $this->getDoctrine()->getRepository('AppBundle:SystemeResa')
            ->findOneBy(array('id' => $id));

        return $this->render('user/userGroupesResas.html.twig', [
            'system' => $system
        ]);
    }
}
