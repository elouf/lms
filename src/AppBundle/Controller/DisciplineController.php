<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cours;
use AppBundle\Entity\Discipline;
use AppBundle\Entity\Inscription_sess;
use AppBundle\Entity\User;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;

class DisciplineController extends Controller
{

    /**
     * @Route("/discCoursManag", name="discCoursManag")
     */
    public function discCoursManagAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $disRepo = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $disRepo->findAll();

        $myDisc = array();
        $i = 0;
        if ($disciplines) {
            foreach ($disciplines as $discipline) {
                $cours = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $discipline));
                $myDisc[$i]['discipline'] = $discipline;

                $myDisc[$i]['cours'] = array();
                if ($cours) {
                    foreach ($cours as $cour) {
                        array_push($myDisc[$i]['cours'], $cour);
                    }
                }
                $i++;
            }
        }
        return $this->render('ressources/allDiscCours.html.twig', [
            'disciplines' => $myDisc
        ]);
    }

    /**
     * @Route("/myCourses/{id}", defaults={"id" = 0}, name="myCourses")
     */
    public function myCoursesAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        date_default_timezone_set('Europe/Paris');

        $em = $this->getDoctrine();

        $repositoryCours = $em->getRepository('AppBundle:Cours');

        $repositoryCoh = $em->getRepository('AppBundle:Cohorte');

        $repositoryDis = $em->getRepository('AppBundle:Discipline');

        $repoInscription_coh = $em->getRepository('AppBundle:Inscription_coh');

        $repoInscription_d = $em->getRepository('AppBundle:Inscription_d');

        $repositoryInscrSess = $em->getRepository('AppBundle:Inscription_sess');

        $repositoryStatsUsersDocs = $em->getRepository('AppBundle:StatsUsersDocs');

        $repositoryDocuments = $em->getRepository('AppBundle:Document');

        $disciplines = $repositoryDis->findBy(array(), array('nom' => 'ASC'));

        $cohortes = $repositoryCoh->findAll();

        // Par défaut, admin : toutes les disciplines
        $disciplinesArray2Consider = $disciplines;
        $cohLiees = array();

        $coursesIndiv = array();
        /* @var $user User */
        $user = $this->getUser();
        $statutUser = $user->getStatut();
        $userIsAdmin = $user->hasRole('ROLE_SUPER_ADMIN');

        // si ce n'est pas l'admin, on fait le tri
        if (!$userIsAdmin) {
            // on créé un tableau qui contient les disciplines auxquelles l' user est inscrit par cohorte
            $repositoryInscrCoh = $em->getRepository('AppBundle:Inscription_coh');
            $inscrsCoh = $repositoryInscrCoh->findBy(array('user' => $user));
            $discInscrCoh = array();
            foreach ($inscrsCoh as $inscrCoh) {
                $cohorte = $repositoryCoh->find($inscrCoh->getCohorte());
                foreach ($cohorte->getDisciplines() as $disc) {
                    if (!in_array($disc, $discInscrCoh)) {
                        array_push($discInscrCoh, $disc);
                    }
                }
            }

            $disciplinesArray2Consider = $discInscrCoh;

            // on ajoute les disciplines auxquelles le user est inscrit directement
            $repositoryInscrD = $em->getRepository('AppBundle:Inscription_d');
            $inscrsD = $repositoryInscrD->findBy(array('user' => $user));
            foreach ($inscrsD as $inscrD) {
                if (!in_array($inscrD->getDiscipline(), $disciplinesArray2Consider)) {
                    array_push($disciplinesArray2Consider, $inscrD->getDiscipline());
                }
            }

            // enfin, on ajoute les cours auxquels l'utilisateur est inscrit individuellement (du coup une portion de discipline)
            $repositoryInscrC = $em->getRepository('AppBundle:Inscription_c');
            $inscrsC = $repositoryInscrC->findBy(array('user' => $user));
            foreach ($inscrsC as $inscrC) {
                if (!in_array($inscrC->getCours()->getDiscipline(), $disciplinesArray2Consider)) {
                    array_push($coursesIndiv, $inscrC->getCours());
                }
            }
        }
        if ($userIsAdmin || $statutUser == 'Responsable' || $statutUser == 'Formateur') {
            // on ajoute les cohortes liées pour l'admin pour qu'il puisse accéder aux pages d'inscriptions à ces cohortes
            for ($i = 0; $i < count($disciplinesArray2Consider); $i++) {
                $cohLiees[$i] = $repositoryDis->getCohortes($disciplinesArray2Consider[$i]);
            }
        }

        $courses = array();
        // on construit le tableau des disciplines/cours complètes
        for ($i = 0; $i < count($disciplinesArray2Consider); $i++) {

            $courses[$i]["role"] = "";
            if($cohortes){
                foreach($cohortes as $cohorte){
                    if($cohorte->getDisciplines()->contains($disciplinesArray2Consider[$i])){
                        $inscrCoh = $repoInscription_coh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                        if($inscrCoh){
                            $courses[$i]["role"] = $inscrCoh->getRole()->getNom();
                            break;
                        }
                    }
                }
            }
            if($courses[$i]["role"] == ""){
                $inscrDis = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $disciplinesArray2Consider[$i]));
                if($inscrDis) {
                    $courses[$i]["role"] = $inscrDis->getRole()->getNom();
                }
            }

            $courses[$i]["courses"] = array();
            $courses[$i]["sessions"] = array();
            $courses[$i]["sessionsAdmin"] = array();
            $courses[$i]["sessionsAlerte"] = array();
            $courses[$i]["sessionsAlerteIsInscrit"] = array();
            $courses[$i]["sessionsFinSession"] = array();
            $courses[$i]["discipline"] = $disciplinesArray2Consider[$i];
            $courses[$i]["cohortesLiees"] = array();
            if ($userIsAdmin || $statutUser == 'Responsable' || $statutUser == 'Formateur') {
                $courses[$i]["cohortesLiees"] = $cohLiees[$i];
            }
            $coursesT = $repositoryCours->findBy(array('discipline' => $disciplinesArray2Consider[$i]), array('position' => 'ASC'));
            for ($j = 0; $j < count($coursesT); $j++) {
                if (!$coursesT[$j]->getSession()) {
                    array_push($courses[$i]["courses"], $coursesT[$j]);
                } else {
                    $session = $coursesT[$j]->getSession();
                    $currentDate = new DateTime();
                    $inscrSess = $repositoryInscrSess->findOneBy(array('user' => $user, 'session' => $session));
                    // on est inscrit et les dates sont bonnes (ou on est admin ou enseignant)
                    $isEns = false;
                    if ($inscrSess) {
                        if ($inscrSess->getRole() == "Enseignant") {
                            $isEns = true;
                        }
                    }

                    $isAdminOrForm = $userIsAdmin || (($statutUser == 'Responsable' || $statutUser == 'Formateur') && $user->getConfirmedByAdmin());

                    if ($currentDate >= $session->getDateDebut() &&
                        $currentDate <= $session->getDateFin() &&
                        ($inscrSess || $isAdminOrForm || $isEns)
                    ) {
                        // on peut rentrer dans la session et on est dans les dates
                        array_push($courses[$i]["sessions"], $coursesT[$j]);
                    } elseif ($currentDate >= $session->getDateDebutAlerte() && $currentDate < $session->getDateFinAlerte() && !$isAdminOrForm) {
                        // on affiche l'alerte et on permet de s'inscrire
                        array_push($courses[$i]["sessionsAlerte"], $coursesT[$j]);
                        array_push($courses[$i]["sessionsAlerteIsInscrit"], $inscrSess != null);
                    } elseif ($currentDate >= $session->getDateFinAlerte() && $currentDate < $session->getDateFin()) {
                        // on affiche le message de fin de session
                        array_push($courses[$i]["sessionsFinSession"], $coursesT[$j]);
                    } elseif ($isEns || $isAdminOrForm) {
                        // on peut rentrer dans la session hors des dates
                        array_push($courses[$i]["sessionsAdmin"], $coursesT[$j]);
                    }
                }
            }
        }
        // on lui ajoute les cours individuels (avec leurs disciplines)
        for ($j = 0; $j < count($coursesIndiv); $j++) {
            $discExists = false;
            for ($k = 0; $k < count($courses); $k++) {
                if ($courses[$k]["discipline"] == $coursesIndiv[$j]->getDiscipline()) {
                    array_push($courses[$k]["courses"], $coursesIndiv[$j]);
                    $discExists = true;
                }
            }
            if (!$discExists) {
                $idx = count($courses);
                $courses[$idx]["discipline"] = $coursesIndiv[$j]->getDiscipline();
                $courses[$idx]["courses"] = array($coursesIndiv[$j]);
                $courses[$idx]["role"] = "Etudiant";
            }
        }

        // on recherche les infos liées aux documents
        // Comme un accès aux documents de la discipline existe, on doit afficher l'info-bulle si certains n'ont pas été visités
        for ($j = 0; $j < count($courses); $j++) {
            $docs = $repositoryDocuments->findByDisc($courses[$j]["discipline"], $user);
            $documents = array_merge($docs[0], $docs[1]);

            $nbNewDocs = 0;
            foreach ($documents as $doc) {
                $stat = $repositoryStatsUsersDocs->findBy(array('user' =>$user, 'document' => $doc));
                if (!$stat) {
                    $nbNewDocs++;
                }
            }
            $courses[$j]["nbNewDocs"] = $nbNewDocs;
        }

        $disciplinesArray2ConsiderStr = [];
        for ($j = 0; $j < count($disciplinesArray2Consider); $j++) {
            $disciplinesArray2ConsiderStr[$disciplinesArray2Consider[$j]->getNom()] = $disciplinesArray2Consider[$j]->getId();
        }

        // formulaire de création de nouveau cours (pour le référent et l'admin
        $form = $this->createFormBuilder()
            ->add('nom', TextType::class, array(
                'label' => 'Nom '
            ))
            ->add('imageFile', FileType::class, [
                'label' => 'Image',
                'required' => true,
                'multiple' => false
            ])

            ->add('discipline', ChoiceType::class, [
                'placeholder' => 'Veuillez choisir une discipline',
                'label' => "Discipline",
                'choices' => $disciplinesArray2ConsiderStr,
                "disabled" => false
            ])
            ->add('description', CKEditorType::class, [
                "required" => false
            ])
            ->add('accueil', CKEditorType::class, [
                "required" => false
            ])
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider',
                'attr' => array('class' => 'button')
            ))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $cours = new Cours();
            $cours->setNom($form['nom']->getData());
            $dis = $repositoryDis->findOneBy(array('id' => $form['discipline']->getData()));
            $cours->setDiscipline($dis);
            $cours->setAccueil($form['accueil']->getData());
            $cours->setDescription($form['description']->getData());
            $cours->setAuteur($user);

            $cours->setImageFile($form['imageFile']->getData());
            $cours->upload();

            $em->getManager()->persist($cours);

            $em->getManager()->flush();
            return $this->redirectToRoute('myCourses');

        }

        return $this->render('discipline/myCourses.html.twig', [
            'courses' => $courses,
            'active' => $id,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/discs", name="disciplines")
     */
    public function disciplinesAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $disciplines = $repository->findAll();

        return $this->render('discipline/list.html.twig', ['disciplines' => $disciplines]);
    }

    /**
     * @Route("/disc/{id}", name="oneDiscipline")
     */
    public function oneDisciplineAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Discipline');
        $discipline = $repository->find($id);

        $courses = $this->getDoctrine()->getRepository('AppBundle:Cours')->findBy(array('discipline' => $discipline));

        return $this->render('discipline/one.html.twig', ['discipline' => $discipline, 'courses' => $courses]);
    }

    /**
     * @Route("/changeActivationDocsDisc_ajax", name="changeActivationDocsDisc_ajax")
     */
    public function changeActivationDocsDiscAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            $id = $request->request->get('id');
            $isVisible = $request->request->get('isVisible');

            $disc = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $id));
            $disc->setDocsActivated($isVisible == "false");

            $em->persist($disc);
            $em->flush();

            return new JsonResponse(array('action' => 'change Visibility of documents', 'id' => $disc->getId(), 'isVisible' => $disc->getDocsActivated()));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

    /**
     * @Route("/inscrSess_ajax", name="inscrSess_ajax")
     */
    public function inscrSessAjaxAction(Request $request)
    {
        if ($request->isXMLHttpRequest()) {
            $em = $this->getDoctrine()->getEntityManager();

            date_default_timezone_set('Europe/Paris');

            $id = $request->request->get('id');

            $session = $em->getRepository('AppBundle:Session')->findOneBy(array('id' => $id));

            $roleInCours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $id));

            $role = $em->getRepository('AppBundle:Role')->findOneBy(array('nom' => 'Etudiant'));

            $inscr = new Inscription_sess();
            $inscr->setSession($session);
            $inscr->setUser($this->getUser());
            $inscr->setDateInscription(new DateTime());
            if ($role) {
                $inscr->setRole($role);
            }

            $em->persist($inscr);
            $em->flush();

            return new JsonResponse(array('action' => 'Inscription user session'));
        }

        return new JsonResponse('This is not ajax!', 400);
    }

}
